<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>BTH</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css2?family=Onest:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<style>
    .main {
        width: 100%;
        max-width: 700px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    table {
        border-spacing: 0;
    }

    td {
        padding: 0;
    }

    img {
        border: 0;
    }

    a {
        text-decoration: none;
        color: inherit;
        display: inline-block;
    }

    ul {
        list-style: none;
    }
</style>

<body style="background: #F4ECFC; padding: 5px 5px;">
    <table class="main" cellpadding="0" cellspacing="0" style="font-family: 'Poppins', sans-serif; margin: 0 auto">

        <tr>
            <td align="center" style="padding: 6px 0;">
                <table width="100%" cellpadding="0" cellspacing="0" style="background: #fff; border-radius: 10px;  overflow: hidden;">
                    <tr>
                        <td style="background-color: #fff;">
                            <span style="position: relative;  max-width: 100%; max-height: 170px !important; border-radius: 10px;">
                                <img src="https://res.cloudinary.com/dpfcntrwo/image/upload/v1738447564/1_g81fm0.png" alt="Main Image" style="position: relative; max-width: 100%; max-height: 170px; border-top-left-radius: 10px; border-top-right-radius: 10px; object-fit:cover; object-position: center;">
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 10px; padding-top: 0px !important;">
                            <br>
                            <p style="color: #1B1B1B;  font-size: 13px; font-weight: 400;  text-align: left; line-height: 23px;">
                                {!! $emailData['message'] !!}
                            </p>

                            <p style=" margin-top: 15px; color: #1B1B1B; font-size: 13px; line-height: 1.55;">
                                Please click the link below to fill out the form:
                            </p>

                            <span style="display: block; margin-top: 20px">
                                <a style="background: #8C3CDF; text-align: center; color: #fff; padding: 8px 20px; text-decoration: none; font-weight: 600;" href="{{ route('vacation.form') }}" target="_blank">Fill out the form</a>
                            </span>

                            <br>
                            <br>

                            <span style="position: relative; max-width: 100%; max-height: 260px; border-radius: 10px;">
                                <img src="https://res.cloudinary.com/dpfcntrwo/image/upload/v1738227670/ezgif.com-optimize_nn1ic5.gif" alt="" style="position: relative; max-width: 100%; max-height: 260px; border-radius: 10px; object-fit:cover;  object-position: center;">
                            </span>


                            <p style=" margin-top: 15px; color: #1B1B1B; font-size: 13px; line-height: 1.55;">
                                Thank you for your time and cooperation! If you have any questions, feel free to reach out.
                            </p>

                            <p style="margin-bottom: 20px; margin-top: 15px; color: #1B1B1B; font-size: 13px; line-height: 1.55; font-weight: 600;">
                                Best regards, <br>
                                TEAM Bighit Music
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>


        <tr>
            <td style="padding: 6px 0px;">
                <table width="100%" cellpadding="0" cellspacing="0" style="background: #fff; border-radius: 0px; overflow: hidden; padding: 10px;">
                    <tr>
                        <td>
                            <table style="width: 100%;">
                                <tr>
                                    <td style="padding: 0;">
                                        <table style="padding-top: 20px; display: inline-block; text-align: start;" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td>
                                                    <a href="https://x.com/bts_bighit" target="_blank">
                                                        <img style="width: 24px;" src="https://res.cloudinary.com/dpfcntrwo/image/upload/v1738447921/x_ha0fdh.png" alt="Twitter">
                                                    </a>
                                                </td>
                                                <td style="padding-left: 10px;">
                                                    <a href="https://www.instagram.com/bts.bighitofficial?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank">
                                                        <img style="width: 24px;" src="https://res.cloudinary.com/dpfcntrwo/image/upload/v1738447935/instagram_aurtgi.png" alt="Instagram">
                                                    </a>
                                                </td>

                                                <td style="padding-left: 10px;">
                                                    <a href="https://youtube.com/@BTS" target="_blank">
                                                        <img style="width: 24px;" src="https://res.cloudinary.com/dpfcntrwo/image/upload/v1738447949/youtube_dttdnj.png" alt="YouTube">
                                                    </a>
                                                </td>
                                                <td style="padding-left: 10px;">
                                                    <a href="https://facebook.com/bangtan.official" target="_blank">
                                                        <img style="width: 24px;" src="https://res.cloudinary.com/dpfcntrwo/image/upload/v1738447958/facebook_q5ecag.png" alt="Facebook">
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p style="font-size: 13px; font-weight: 400; line-height: 35px; text-align: left; color: #707070; margin-block: 0px; margin-top: 15px;">
                                © 2024 Bighit Music. All rights reserved.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="font-size: 13px; font-weight: 400; text-align:  left; color: #707070; margin-block: 0px; line-height: 23px;">
                                Music and entertainment services offered by Bighit Music. We are dedicated to supporting and promoting artists across various genres. All rights and content related to BTS Records and its artists are protected under copyright laws.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; text-align: center;">
                            <span style="display: block; text-align: center;">
                                <img src="https://res.cloudinary.com/dpfcntrwo/image/upload/v1738447755/bighitlogo_ilxvbk.png" style=" width: 70px" alt="Logo">
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>


    </table>
</body>

</html>