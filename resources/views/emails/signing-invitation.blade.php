<!doctype html>
<html><body style="font-family:Arial,sans-serif;line-height:1.6;color:#202124">
    <p>Hello {{ $document->recipient_name }},</p>
    <p>{!! nl2br(e($document->body)) !!}</p>
    <p>Scan this QR code to open your document:</p>
    <p><img src="{{ $message->embedData($qrPng, 'document-qr.png', 'image/png') }}" width="250" height="250" alt="Document access QR code"></p>
    <p><strong>Access code:</strong> {{ $accessCode }}</p>
    @if($document->expires_at)
        <p>Access expires {{ $document->expires_at->format('M j, Y g:i A T') }}.</p>
    @endif
    <p>This message has no document attachment. Keep your QR code and access code private.</p>
</body></html>
