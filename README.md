<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Protected document invitations

The admin sidebar has a separate **Protected Documents** area. An admin uploads one PDF for one signer, writes an email subject and body, and sets an unlock limit, expiry, and signature position on the last page. The admin may enter a unique six-digit access code or leave the field blank for the app to generate one. The generated code is shown on the admin detail page. After preparing the document, the admin page renders the PDF and marks the signature area. Drag the marker before sending; its new position saves automatically and is locked after the invitation is sent. **Send invitation** generates a QR image inside the system and emails it with the six-digit code. The recipient enters that code using six separate digit boxes. The PDF and raw URL are not included as email attachments or visible text.

The QR URL is built from `config('app.url')`, which reads `APP_URL` in `.env`. Set `APP_URL=https://ibighitmusic.com` on the live server. A phone scanning a QR code during local development must be able to reach the local `APP_URL` over your network; `localhost` on the phone points to the phone itself.

Before using this feature, install the added Composer packages (`composer update endroid/qr-code setasign/fpdf setasign/fpdi --with-all-dependencies`) with PHP 8.1+ and the GD extension, then run `php artisan migrate`. Keep `APP_KEY` stable because it encrypts the stored PDFs and invitation secrets. Configure the mail transport before sending. Existing mail features remain separate.

Document creation and its initial activity record are saved in one database transaction. The encrypted PDF is stored just before that transaction and removed if the transaction fails. A unique database index prevents two invitations from sharing an access code, including when two admins submit at the same time. Generated codes retry on a collision. Existing invitations made before the six-digit rule may need their codes changed in Access settings before they can be sent or unlocked.

The recipient viewer does not offer a download control, but any content displayed on a recipient's device can still be copied by screenshots or developer tools. This is a controlled viewing workflow, not a technical guarantee against copying. The signed PDF is stored privately for admin review. PDF import support should be checked against the types of PDFs you will upload; some PDF structures may require a different PDF processing library.

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
