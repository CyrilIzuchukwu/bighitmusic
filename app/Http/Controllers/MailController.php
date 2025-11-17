<?php

namespace App\Http\Controllers;

use App\Mail\FormLinkEmail;
use App\Mail\PlainTextEmail;
use App\Mail\RegisteredMailNotification;
use App\Models\FileAttachment;
use App\Models\PrivateVacation;
use App\Models\RegisteredMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MailController extends Controller
{
    //.
    public function add_mail()
    {
        return view('admin.mail.add-mail');
    }

    public function store(Request $request)
    {
        // Validation rules
        $request->validate([
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'email' => 'required|email|unique:registered_mails,email',
        ]);

        // Create the new registered mail record
        RegisteredMail::create([
            'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ]);

        // Redirect with a success message
        return redirect()->back()->with('success', 'User information registered successfully.');
    }


    public function index()
    {
        $registeredMails = RegisteredMail::all();
        return view('admin.mail.registered-mails', compact('registeredMails'));
    }



    public function edit($id)
    {
        $mail = RegisteredMail::findOrFail($id);
        return view('admin.mail.edit', compact('mail'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
        ]);

        $mail = RegisteredMail::findOrFail($id);
        $mail->update($request->all());

        return redirect()->route('registered-mails.index')->with('success', 'Registered email updated successfully');
    }

    public function destroy($id)
    {
        $mail = RegisteredMail::findOrFail($id);
        $mail->delete();

        return redirect()->route('registered-mails.index')->with('success', ' deleted successfully');
    }


    public function sendEmail($id)
    {
        $mail = RegisteredMail::findOrFail($id);
        return view('admin.mail.send_email1', compact('mail'));
    }

    public function sendEmailProcess(Request $request, $id)
    {
        try {
            $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
                'email_type' => 'required|string|in:plain,form',
                'attachment' => 'nullable|file|max:5048',
            ]);

            // Handle the file upload using Storage
            $fileName = null;
            $attachmentPath = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');

                // Generate unique filename
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

                // Define storage path
                $storagePath = 'attachments/';

                // Store file in public disk
                $attachmentPath = $file->storeAs($storagePath, $fileName, 'public');

                // Save to database
                $fileAttachment = new FileAttachment();
                $fileAttachment->file_name = $fileName;
                $fileAttachment->save();
            }

            $mail = RegisteredMail::findOrFail($id);

            $emailData = [
                'subject' => $request->subject,
                'message' => $request->message,
                'name' => $mail->firstname . ' ' . $mail->lastname,
                'email' => $mail->email,
                'attachment' => $attachmentPath ? Storage::url($attachmentPath) : null,
                'attachment_full_path' => $attachmentPath ? storage_path('app/public/' . $attachmentPath) : null,
            ];

            try {
                // Determine which email to send based on the selected type
                if ($request->email_type === 'plain') {
                    // Send plain text email
                    Mail::to($emailData['email'])->send(new PlainTextEmail($emailData));
                } elseif ($request->email_type === 'form') {
                    // Send form link email
                    Mail::to($emailData['email'])->send(new FormLinkEmail($emailData));
                }

                return redirect()->route('registered-mails.index')
                    ->with('success', 'Email sent successfully to ' . $mail->email);
            } catch (\Exception $e) {
                // Catch mail-sending errors
                return redirect()->back()
                    ->with('error', 'Failed to send email: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            // Catch validation or file upload errors
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }


    public function compose_mail()
    {
        return view('admin.mail.compose-email');
    }


    public function sendEmailProcess2(Request $request)
    {
        $request->validate([
            'email' => 'required|email|string',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'email_type' => 'required|string|in:plain,form',
            'attachment' => 'nullable|file|max:5048',
        ]);

        // Handle the file upload using Storage
        $fileName = null;
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $storagePath = 'attachments/';
            $attachmentPath = $file->storeAs($storagePath, $fileName, 'public');
        }

        if ($fileName) {
            $file = new FileAttachment();
            $file->file_name = $fileName;
            $file->save();
        }

        $emailData = [
            'subject' => $request->subject,
            'message' => $request->message,
            'name' => $request->firstname . ' ' . $request->lastname,
            'email' => $request->email,
            'attachment' => $attachmentPath ? Storage::url($attachmentPath) : null,
            'attachment_full_path' => $attachmentPath ? storage_path('app/public/' . $attachmentPath) : null,
        ];

        // Determine which email to send based on the selected type
        if ($request->email_type === 'plain') {
            // Send plain text email
            Mail::to($emailData['email'])->send(new PlainTextEmail($emailData));
        } elseif ($request->email_type === 'form') {
            // Send form link email
            Mail::to($emailData['email'])->send(new FormLinkEmail($emailData));
        }

        return redirect()->route('registered-mails.index')->with('success', 'Email sent successfully');
    }
}
