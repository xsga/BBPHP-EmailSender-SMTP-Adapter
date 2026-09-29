# BBPHP Email Sender SMTP Adapter

A PHP SMTP adapter for the BBPHP Email Sender Core library, built on top of PHPMailer.

This package implements the `SendEmailService` contract from the core library and sends emails through an SMTP server using the PHPMailer transport.

## Features

- SMTP-based email sending via PHPMailer
- HTML email support
- Support for primary recipients, CC, and BCC
- Integration with the BBPHP Email Sender Core abstraction
- PSR-3 logger compatibility
- Ready for use in small services, APIs, or application infrastructure layers

## Requirements

- PHP 8.4 or higher
- Composer
- A valid SMTP server (for example Gmail, SendGrid, Mailgun, Exchange, or a local SMTP relay)
- The BBPHP Email Sender Core package

## Installation

```bash
composer require xsga/bbphp-emailsender-smtp-adapter
```

## Configuration

The adapter requires SMTP connection settings through `SMTPConnectionDataDto`.

```php
<?php

use Xsga\BBPHP\EmailSender\Adapter\Smtp\SMTPConnectionDataDto;

$smtpData = new SMTPConnectionDataDto();
$smtpData->host = 'smtp.example.com';
$smtpData->port = 465;
$smtpData->user = 'your-user@example.com';
$smtpData->password = 'your-password';
```

## Usage

```php
<?php

require 'vendor/autoload.php';

use Psr\Log\NullLogger;
use Xsga\BBPHP\EmailSender\Adapter\Smtp\SMTPConnectionDataDto;
use Xsga\BBPHP\EmailSender\Adapter\Smtp\SMTPPHPMailerEmailService;
use Xsga\BBPHP\EmailSender\Core\Application\Dto\EmailDataDto;

$smtpData = new SMTPConnectionDataDto();
$smtpData->host = 'smtp.example.com';
$smtpData->port = 465;
$smtpData->user = 'your-user@example.com';
$smtpData->password = 'your-password';

$emailData = new EmailDataDto();
$emailData->sender = 'noreply@example.com';
$emailData->senderName = 'Support';
$emailData->recipients = ['customer@example.com'];
$emailData->recipientsCC = ['copy@example.com'];
$emailData->recipientsBCC = ['hidden@example.com'];
$emailData->subject = 'Welcome to our platform';
$emailData->body = '<h1>Hello!</h1><p>Thank you for signing up.</p>';

$logger = new NullLogger();
$emailService = new SMTPPHPMailerEmailService($logger, $smtpData);
$emailService->send($emailData);
```

## Email payload

The email payload is represented by `EmailDataDto` from the core package:

```php
final class EmailDataDto
{
    public string $sender = '';
    public string $senderName = '';
    public array $recipients = [];
    public array $recipientsCC = [];
    public array $recipientsBCC = [];
    public string $subject = '';
    public string $body = '';
    public array $attachments = [];
}
```

## Notes

- The adapter uses `PHPMailer::ENCRYPTION_SMTPS` by default for secure SMTP connections.
- The implementation catches errors and wraps them into a `SendEmailException` from the core package.
- A logger compatible with PSR-3 can be injected to help monitor delivery success or failures.

## License

This project is licensed under the MIT license.

## Author

Parker

## Related packages

- BBPHP Email Sender Core: https://github.com/xsga/BBPHP-EmailSender-Core
- Package: `xsga/bbphp-emailsender-core`
- Composer package: `xsga/bbphp-emailsender-smtp-adapter`