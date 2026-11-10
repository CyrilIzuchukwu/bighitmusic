<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="x-apple-disable-message-reformatting" />
    <title>{{ $document->subject }}</title>
</head>

<body style="margin:0;padding:8px;background:#F4ECFC;font-family:Poppins,Arial,sans-serif;color:#1B1B1B;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-spacing:0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="width:100%;max-width:700px;border-spacing:0;">
                    <tr>
                        <td style="padding:6px 0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#fff;border-radius:10px;border-spacing:0;">
                                <tr>
                                    <td><img src="https://res.cloudinary.com/dpfcntrwo/image/upload/v1738447564/1_g81fm0.png"
                                            width="700" alt="Bighit Music"
                                            style="display:block;width:100%;max-width:700px;height:auto;border:0;border-radius:10px 10px 0 0;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:22px 20px 24px;">
                                        <p style="margin:0 0 12px;font-size:13px;line-height:23px;">Hello
                                            {{ $document->recipient_name }},</p>
                                        <p style="margin:0;font-size:13px;line-height:23px;white-space:pre-line;">
                                            {{ $document->body }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:6px 0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#fff;border-radius:10px;border-spacing:0;">
                                <tr>
                                    <td align="center" style="padding:25px 18px 8px;">
                                        <p
                                            style="margin:0 0 8px;color:#8C3CDF;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;">
                                            Direct document pass</p>
                                        <p style="margin:0;font-size:19px;font-weight:700;line-height:27px;">Review and
                                            sign your document</p>
                                        <p style="margin:10px 0 0;color:#60586c;font-size:13px;line-height:21px;">Scan
                                            the QR code to open your secure document page, then enter the six-digit code
                                            below.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding:10px 18px;"><img
                                            src="{{ $message->embedData($qrPng, 'document-qr.png', 'image/png') }}"
                                            width="220" height="220" alt="QR code to open your document"
                                            style="display:block;width:220px;height:220px;max-width:100%;border:0;" />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding:8px 18px 25px;">
                                        <p style="margin:0 0 7px;color:#60586c;font-size:12px;line-height:18px;">Your
                                            six-digit access code</p>
                                        <p
                                            style="margin:0;color:#592a92;font-family:Arial,sans-serif;font-size:28px;font-weight:700;letter-spacing:7px;line-height:36px;">
                                            {{ $accessCode }}</p>
                                        @if ($document->expires_at)
                                            <p style="margin:15px 0 0;color:#60586c;font-size:12px;line-height:19px;">
                                                Access expires {{ $document->expires_at->format('M j, Y g:i A T') }}.
                                            </p>
                                        @endif
                                        <p style="margin:17px 0 0;color:#706878;font-size:12px;line-height:19px;">The
                                            PDF is not attached to this email. Keep the QR code and access code private.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:6px 0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#fff;border-radius:10px;border-spacing:0;">
                                <tr>
                                    <td style="padding:20px 20px 10px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0"
                                            style="border-spacing:0;">
                                            <tr>
                                                <td style="padding-right:12px;"><a href="https://x.com/bts_bighit"><img
                                                            width="24" height="24"
                                                            src="https://res.cloudinary.com/dpfcntrwo/image/upload/v1738447921/x_ha0fdh.png"
                                                            alt="X" style="display:block;border:0;" /></a></td>
                                                <td style="padding-right:12px;"><a
                                                        href="https://www.instagram.com/bts.bighitofficial/"><img
                                                            width="24" height="24"
                                                            src="https://res.cloudinary.com/dpfcntrwo/image/upload/v1738447935/instagram_aurtgi.png"
                                                            alt="Instagram" style="display:block;border:0;" /></a></td>
                                                <td style="padding-right:12px;"><a href="https://youtube.com/@BTS"><img
                                                            width="24" height="24"
                                                            src="https://res.cloudinary.com/dpfcntrwo/image/upload/v1738447949/youtube_dttdnj.png"
                                                            alt="YouTube" style="display:block;border:0;" /></a></td>
                                                <td><a href="https://facebook.com/bangtan.official"><img width="24"
                                                            height="24"
                                                            src="https://res.cloudinary.com/dpfcntrwo/image/upload/v1738447958/facebook_q5ecag.png"
                                                            alt="Facebook" style="display:block;border:0;" /></a></td>
                                            </tr>
                                        </table>
                                        <p style="margin:16px 0 0;color:#707070;font-size:12px;line-height:20px;">&copy;
                                            {{ date('Y') }} BTS Records. All rights reserved.</p>
                                        <p style="margin:7px 0 0;color:#707070;font-size:12px;line-height:20px;">Music
                                            and entertainment services offered by Bighit Music. We are dedicated to
                                            supporting and promoting artists across various genres.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding:10px 20px 20px;"><img width="70"
                                            src="https://res.cloudinary.com/dpfcntrwo/image/upload/v1738447755/bighitlogo_ilxvbk.png"
                                            alt="Bighit Music logo"
                                            style="display:block;width:70px;height:auto;border:0;" /></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
