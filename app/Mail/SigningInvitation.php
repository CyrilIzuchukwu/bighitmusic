<?php

namespace App\Mail;

use App\Models\SigningDocument;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class SigningInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public string $qrPng;
    public string $accessCode;

    public function __construct(public SigningDocument $document)
    {
        $token = Crypt::decryptString($document->token_ciphertext);
        $baseUrl = rtrim(config('app.url'), '/');
        $url = $baseUrl . route('signing.show', ['token' => $token], false);
        $this->qrPng = (new PngWriter())->write(QrCode::create($url)->setSize(300)->setMargin(12))->getString();
        $this->accessCode = Crypt::decryptString($document->code_ciphertext);
    }

    public function build(): self
    {
        return $this->subject($this->document->subject)
            ->view('emails.signing-invitation');
    }
}
