<?php

declare(strict_types=1);

namespace Xsga\BBPHP\EmailSender\Adapter\Smtp;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Psr\Log\LoggerInterface;
use Throwable;
use Xsga\BBPHP\EmailSender\Core\Application\Dto\EmailDataDto;
use Xsga\BBPHP\EmailSender\Core\Application\Services\SendEmailService;
use Xsga\BBPHP\EmailSender\Core\Domain\Exceptions\SendEmailException;

final class SMTPPHPMailerEmailService implements SendEmailService
{
    private const int DEBUG_LEVEL = SMTP::DEBUG_OFF;

    private readonly PHPMailer $phpMailer;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SMTPConnectionDataDto $smtpData
    ) {
        $this->phpMailer = new PHPMailer(true);
    }

    public function send(EmailDataDto $emailData): void
    {
        try {
            $this->configureServer();
            $this->prepareEmail($emailData);

            $this->phpMailer->send();

            $this->logger->info('Email sent successfully using PHPMailer SMTP service', [
                'event' => 'email.phpmailer.send.success',
                'recipients' => $emailData->recipients,
                'subject' => $emailData->subject
            ]);
        } catch (Throwable $exception) {
            $errorMsg = 'Error sending email message using PHPMailer SMTP service';

            $this->logger->error($errorMsg, [
                'event' => 'email.phpmailer.send.error_sending_email',
                'sender' => $emailData->sender,
                'recipients' => $emailData->recipients,
                'subject' => $emailData->subject,
                'php_mailer_error' => $this->phpMailer->ErrorInfo,
                'exception_message' => $exception->getMessage(),
                'exception_stack_trace' => $exception->getTraceAsString(),
            ]);

            throw new SendEmailException($errorMsg);
        }
    }

    private function configureServer(): void
    {
        $this->phpMailer->isSMTP();

        $this->phpMailer->SMTPDebug  = self::DEBUG_LEVEL;
        $this->phpMailer->Host       = $this->smtpData->host;
        $this->phpMailer->SMTPAuth   = true;
        $this->phpMailer->Username   = $this->smtpData->user;
        $this->phpMailer->Password   = $this->smtpData->password;
        $this->phpMailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->phpMailer->Port       = $this->smtpData->port;
    }

    private function prepareEmail(EmailDataDto $emailData): void
    {
        $this->phpMailer->setFrom($emailData->sender, $emailData->senderName);
        $this->phpMailer->clearAllRecipients();

        $this->setRecipients($emailData);
        $this->setRecipientsCC($emailData);
        $this->setRecipientsBCC($emailData);
        $this->setContent($emailData);
    }

    private function setRecipients(EmailDataDto $emailData): void
    {
        foreach ($emailData->recipients as $recipient) {
            $this->phpMailer->addAddress($recipient);
        }
    }

    private function setRecipientsCC(EmailDataDto $emailData): void
    {
        foreach ($emailData->recipientsCC as $recipientCC) {
            $this->phpMailer->addCC($recipientCC);
        }
    }

    private function setRecipientsBCC(EmailDataDto $emailData): void
    {
        foreach ($emailData->recipientsBCC as $recipientBCC) {
            $this->phpMailer->addBCC($recipientBCC);
        }
    }

    private function setContent(EmailDataDto $emailData): void
    {
        $this->phpMailer->isHTML(true);
        $this->phpMailer->Subject = $emailData->subject;
        $this->phpMailer->Body = $emailData->body;
    }
}
