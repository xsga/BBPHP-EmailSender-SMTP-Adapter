<?php

declare(strict_types=1);

namespace Xsga\BBPHP\EmailSender\Adapter\Smtp;

final class SMTPConnectionDataDto
{
    public string $host = '';
    public int $port = 0;
    public string $user = '';
    public string $password = '';
}
