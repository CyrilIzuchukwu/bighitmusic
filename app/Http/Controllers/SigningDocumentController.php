<?php

namespace App\Http\Controllers;

use App\Mail\SigningInvitation;
use App\Models\SigningDocument;
use App\Services\SignedPdf;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SigningDocumentController extends Controller
{
    public function index()
    {
        return view('admin.signing.index', ['documents' => SigningDocument::latest()->paginate(20)]);
    }

    public function create()
    {
        return view('admin.signing.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'recipient_email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
            'document' => 'required|file|mimes:pdf|max:10240',
            'access_code' => ['nullable', 'regex:/^[0-9]{6}$/'],
            'max_opens' => 'required|integer|min:1|max:1000',
            'expires_at' => 'nullable|date|after:now',
            'signature_x' => 'required|integer|between:0,90',
            'signature_y' => 'required|integer|between:0,90',
        ]);

        $manualCode = $data['access_code'] ?? null;
        if ($manualCode !== null && SigningDocument::where('code_hash', $this->codeDigest($manualCode))->exists()) {
            throw ValidationException::withMessages(['access_code' => 'This six-digit code is already in use. Choose another.']);
        }

        $pdf = file_get_contents($request->file('document')->getRealPath());
        if (!str_starts_with($pdf, '%PDF-')) {
            return back()->withErrors(['document' => 'Please upload a valid PDF.'])->withInput();
        }

        $token = Str::random(64);
        $path = 'signing/' . Str::uuid() . '.pdf.enc';
        if (!Storage::disk('local')->put($path, Crypt::encryptString($pdf))) {
            return back()->withErrors(['document' => 'Could not store the document.'])->withInput();
        }

        try {
            $document = null;
            for ($attempt = 0; $attempt < 25; $attempt++) {
                $code = $manualCode ?? sprintf('%06d', random_int(0, 999999));
                $codeDigest = $this->codeDigest($code);
                if (SigningDocument::where('code_hash', $codeDigest)->exists()) {
                    if ($manualCode !== null) {
                        throw ValidationException::withMessages(['access_code' => 'This six-digit code is already in use. Choose another.']);
                    }
                    continue;
                }
                try {
                    $document = DB::transaction(function () use ($data, $request, $path, $token, $code, $codeDigest) {
                        $document = SigningDocument::create([
                            'title' => $data['title'],
                            'recipient_name' => $data['recipient_name'],
                            'recipient_email' => $data['recipient_email'],
                            'subject' => $data['subject'],
                            'body' => $data['body'],
                            'original_name' => $request->file('document')->getClientOriginalName(),
                            'file_path' => $path,
                            'token_hash' => hash('sha256', $token),
                            'token_ciphertext' => Crypt::encryptString($token),
                            'code_hash' => $codeDigest,
                            'code_ciphertext' => Crypt::encryptString($code),
                            'max_opens' => $data['max_opens'],
                            'expires_at' => $data['expires_at'] ?? null,
                            'signature_x' => $data['signature_x'],
                            'signature_y' => $data['signature_y'],
                        ]);
                        $this->log($document, 'created', $request);
                        return $document;
                    });
                    break;
                } catch (QueryException $e) {
                    if (!SigningDocument::where('code_hash', $codeDigest)->exists()) {
                        throw $e;
                    }
                    if ($manualCode !== null) {
                        throw ValidationException::withMessages(['access_code' => 'This six-digit code is already in use. Choose another.']);
                    }
                }
            }
            if ($document === null) {
                throw new \RuntimeException('Could not generate a unique access code. Please try again.');
            }
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }

        return redirect()->route('signing.admin.show', $document)->with('success', 'Document prepared. Review it, then send the invitation.');
    }

    public function adminShow(SigningDocument $document)
    {
        return view('admin.signing.show', [
            'document' => $document,
            'events' => $document->events()->paginate(30),
            'accessCode' => Crypt::decryptString($document->code_ciphertext),
        ]);
    }

    public function send(Request $request, SigningDocument $document)
    {
        abort_if($document->signed_at || $document->revoked_at, 403);
        if ($document->opens >= $document->max_opens) {
            return back()->with('error', 'The opening limit has been reached. Increase it before sending.');
        }
        if (!preg_match('/^[0-9]{6}$/', Crypt::decryptString($document->code_ciphertext))) {
            return back()->with('error', 'Set a unique six-digit access code before sending this invitation.');
        }
        $previousSentAt = $document->sent_at;
        $document->update(['sent_at' => now()]);
        try {
            Mail::to($document->recipient_email)->send(new SigningInvitation($document));
            $this->log($document, 'invitation_sent', $request);
        } catch (\Throwable $e) {
            $document->update(['sent_at' => $previousSentAt]);
            report($e);
            return back()->with('error', 'The invitation could not be sent. Check the application log for the cause.');
        }
        return back()->with('success', 'Invitation sent with QR code and access code.');
    }

    public function settings(Request $request, SigningDocument $document)
    {
        abort_if($document->signed_at, 403);
        $data = $request->validate([
            'access_code' => ['nullable', 'regex:/^[0-9]{6}$/'],
            'max_opens' => 'required|integer|min:1|max:1000',
            'expires_at' => 'nullable|date|after:now',
        ]);
        $changes = [
            'max_opens' => $data['max_opens'],
            'expires_at' => $data['expires_at'] ?? null,
        ];
        $codeDigest = null;
        if (!empty($data['access_code'])) {
            $codeDigest = $this->codeDigest($data['access_code']);
            if (SigningDocument::where('code_hash', $codeDigest)->where('id', '!=', $document->id)->exists()) {
                throw ValidationException::withMessages(['access_code' => 'This six-digit code is already in use. Choose another.']);
            }
            $changes['code_hash'] = $codeDigest;
            $changes['code_ciphertext'] = Crypt::encryptString($data['access_code']);
        }
        try {
            DB::transaction(function () use ($document, $changes, $codeDigest, $request) {
                $locked = SigningDocument::whereKey($document->id)->lockForUpdate()->firstOrFail();
                abort_if($locked->signed_at, 403);
                if ($codeDigest !== null && $codeDigest !== $locked->code_hash) {
                    $changes['access_version'] = $locked->access_version + 1;
                }
                $locked->update($changes);
                $this->log($locked, 'settings_changed', $request);
            });
        } catch (QueryException $e) {
            if ($codeDigest !== null && SigningDocument::where('code_hash', $codeDigest)->where('id', '!=', $document->id)->exists()) {
                throw ValidationException::withMessages(['access_code' => 'This six-digit code is already in use. Choose another.']);
            }
            throw $e;
        }
        return back()->with('success', 'Access settings updated. Send the invitation again if you changed its code.');
    }

    public function position(Request $request, SigningDocument $document)
    {
        $data = $request->validate([
            'signature_x' => 'required|integer|between:0,90',
            'signature_y' => 'required|integer|between:0,90',
        ]);

        DB::transaction(function () use ($document, $data, $request) {
            $locked = SigningDocument::whereKey($document->id)->lockForUpdate()->firstOrFail();
            abort_if($locked->sent_at || $locked->signed_at, 403);
            $locked->update($data);
            $this->log($locked, 'signature_position_changed', $request);
        });

        return response()->json(['saved' => true]);
    }

    public function revoke(Request $request, SigningDocument $document)
    {
        abort_if($document->signed_at, 403);
        $document->update(['revoked_at' => now(), 'access_version' => $document->access_version + 1]);
        $this->log($document, 'access_revoked', $request);
        return back()->with('success', 'Access disabled.');
    }

    public function restore(Request $request, SigningDocument $document)
    {
        abort_if($document->signed_at, 403);
        $document->update(['revoked_at' => null]);
        $this->log($document, 'access_restored', $request);
        return back()->with('success', 'Access restored.');
    }

    public function adminPdf(SigningDocument $document)
    {
        $path = $document->signed_file_path ?: $document->file_path;
        return $this->pdfResponse($path);
    }

    public function adminDownloadSigned(SigningDocument $document)
    {
        abort_unless($document->signed_at && $document->signed_file_path, 404);

        return $this->pdfResponse(
            $document->signed_file_path,
            'attachment; filename="signed-document-' . $document->id . '.pdf"'
        );
    }

    public function show(Request $request, string $token)
    {
        $document = $this->findToken($token);
        abort_unless($document->canOpen(), 403, 'This invitation is unavailable.');
        $granted = $this->granted($request, $document);
        return view('signing.show', compact('document', 'token', 'granted'));
    }

    public function unlock(Request $request, string $token)
    {
        $document = $this->findToken($token);
        abort_unless($document->canOpen(), 403);
        $request->validate(['access_code' => ['required', 'regex:/^[0-9]{6}$/']]);

        if (!hash_equals($document->code_hash, $this->codeDigest($request->access_code))) {
            $this->log($document, 'unlock_failed', $request);
            return back()->withErrors(['access_code' => 'Incorrect access code.']);
        }

        $opened = DB::transaction(function () use ($document, $request) {
            $locked = SigningDocument::whereKey($document->id)->lockForUpdate()->firstOrFail();
            if (!$locked->canOpen() || $locked->opens >= $locked->max_opens) {
                return false;
            }
            $locked->increment('opens');
            $this->log($locked, 'unlocked', $request);
            return true;
        });

        if (!$opened) {
            return back()->withErrors(['access_code' => 'The opening limit has been reached.']);
        }
        $request->session()->put('signing_grant_' . $document->id, [
            'until' => now()->addMinutes(30)->timestamp,
            'version' => $document->access_version,
        ]);
        $request->session()->regenerate();
        return redirect()->route('signing.show', $token);
    }

    public function pdf(Request $request, string $token)
    {
        $document = $this->findToken($token);
        abort_unless($document->canOpen() && $this->granted($request, $document), 403);
        $this->log($document, 'document_viewed', $request);
        return $this->pdfResponse($document->file_path);
    }

    public function sign(Request $request, string $token, SignedPdf $signer)
    {
        $document = $this->findToken($token);
        abort_unless($document->canOpen() && $this->granted($request, $document), 403);
        $request->validate([
            'confirmation' => 'accepted',
            'signature_data' => 'nullable|string|max:400000',
            'signature_upload' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
        ]);

        $png = null;
        if ($request->hasFile('signature_upload')) {
            $raw = file_get_contents($request->file('signature_upload')->getRealPath());
            $image = imagecreatefromstring($raw);
            if ($image) {
                ob_start();
                imagepng($image);
                $png = ob_get_clean();
                imagedestroy($image);
            }
        } elseif (preg_match('/^data:image\/png;base64,([A-Za-z0-9+\/=]+)$/', $request->input('signature_data', ''), $match)) {
            $png = base64_decode($match[1], true);
        }
        if (!$png || strlen($png) > 300000 || !getimagesizefromstring($png)) {
            return back()->withErrors(['signature_data' => 'Please draw or upload a valid signature.']);
        }

        $document->refresh();
        abort_unless($document->canOpen(), 403);
        $path = $signer->create($document, $png);
        $updated = SigningDocument::whereKey($document->id)->whereNull('signed_at')->whereNull('revoked_at')
            ->update(['signed_file_path' => $path, 'signed_at' => now()]);
        if (!$updated) {
            Storage::disk('local')->delete($path);
            abort(409, 'This document has already been signed or disabled.');
        }
        $this->log($document, 'signed', $request);
        $request->session()->forget('signing_grant_' . $document->id);
        return view('signing.complete');
    }

    private function findToken(string $token): SigningDocument
    {
        return SigningDocument::where('token_hash', hash('sha256', $token))->firstOrFail();
    }

    private function codeDigest(string $code): string
    {
        return hash_hmac('sha256', 'signing-code:' . $code, config('app.key'));
    }

    private function granted(Request $request, SigningDocument $document): bool
    {
        $grant = $request->session()->get('signing_grant_' . $document->id, []);
        return is_array($grant)
            && ($grant['until'] ?? 0) > now()->timestamp
            && ($grant['version'] ?? 0) === $document->access_version;
    }

    private function pdfResponse(string $path, string $disposition = 'inline; filename="document.pdf"')
    {
        $pdf = Crypt::decryptString(Storage::disk('local')->get($path));
        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition,
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }

    private function log(SigningDocument $document, string $event, Request $request): void
    {
        $document->events()->create([
            'event' => $event,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000),
        ]);
    }
}
