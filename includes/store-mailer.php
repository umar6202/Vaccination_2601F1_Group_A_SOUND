<?php

/**
 * VEBRA Mailer
 * Uses Composer autoload and safely checks PHPMailer SMTP availability.
 */

$autoloadPath = __DIR__ . '/../vendor/autoload.php';

if (is_file($autoloadPath)) {
    require_once $autoloadPath;
}

$phpMailerPath = __DIR__ . '/../vendor/phpmailer/phpmailer/src/';

if (is_file($phpMailerPath . 'Exception.php')) {
    require_once $phpMailerPath . 'Exception.php';
}

if (is_file($phpMailerPath . 'PHPMailer.php')) {
    require_once $phpMailerPath . 'PHPMailer.php';
}

if (is_file($phpMailerPath . 'SMTP.php')) {
    require_once $phpMailerPath . 'SMTP.php';
}

/**
 * Read mail configuration.
 */
function vebra_mail_setting(string $name): string
{
    static $configLoaded = false;
    static $localConfig = [];

    if (!$configLoaded) {
        $configPath = dirname(__DIR__) . '/config.local.php';

        if (is_file($configPath)) {
            $config = require $configPath;

            if (is_array($config)) {
                $localConfig = $config;
            }
        }

        $configLoaded = true;
    }

    $environmentValue = getenv($name);

    if ($environmentValue !== false && trim((string)$environmentValue) !== '') {
        return (string)$environmentValue;
    }

    return is_string($localConfig[$name] ?? null)
        ? $localConfig[$name]
        : '';
}

/**
 * Send email through SMTP.
 */
function vebra_send_email(
    string $recipient,
    string $subject,
    string $message,
    ?string $replyTo = null,
    ?string $replyToName = null
): bool {

    if (
        !class_exists('PHPMailer\\PHPMailer\\PHPMailer') ||
        !class_exists('PHPMailer\\PHPMailer\\SMTP')
    ) {
        error_log('VEBRA Mailer: PHPMailer or SMTP.php is missing.');
        return false;
    }

    $host = trim(vebra_mail_setting('MAIL_HOST'));
    $port = filter_var(
        vebra_mail_setting('MAIL_PORT') ?: '587',
        FILTER_VALIDATE_INT
    );

    $encryption = strtolower(
        trim(vebra_mail_setting('MAIL_ENCRYPTION') ?: 'tls')
    );

    $username = vebra_mail_setting('MAIL_USERNAME');
    $password = vebra_mail_setting('MAIL_PASSWORD');
    $fromAddress = trim(vebra_mail_setting('MAIL_FROM_ADDRESS'));
    $fromName = trim(vebra_mail_setting('MAIL_FROM_NAME') ?: 'VEBRA');

    if (
        $host === '' ||
        !$port ||
        $port < 1 ||
        $port > 65535 ||
        !in_array($encryption, ['tls', 'ssl', 'none'], true) ||
        !filter_var($recipient, FILTER_VALIDATE_EMAIL) ||
        !filter_var($fromAddress, FILTER_VALIDATE_EMAIL) ||
        (($username !== '') !== ($password !== ''))
    ) {
        error_log('VEBRA Mailer: Invalid SMTP configuration.');
        return false;
    }

    try {
        $mailer = new PHPMailer\PHPMailer\PHPMailer(true);

        $mailer->isSMTP();
        $mailer->Host = $host;
        $mailer->Port = $port;
        $mailer->SMTPAuth = ($username !== '');
        $mailer->Username = $username;
        $mailer->Password = $password;
        $mailer->Timeout = 15;
        $mailer->SMTPDebug = 0;

        $mailer->CharSet = 'UTF-8';

        if ($encryption === 'tls') {
            $mailer->SMTPSecure =
                PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($encryption === 'ssl') {
            $mailer->SMTPSecure =
                PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mailer->SMTPSecure = '';
            $mailer->SMTPAutoTLS = false;
        }

        $mailer->setFrom($fromAddress, $fromName);
        $mailer->addAddress($recipient);

        if ($replyTo !== null) {
            $mailer->addReplyTo($replyTo, $replyToName ?? '');
        }

        $mailer->Subject = $subject;
        $mailer->Body = $message;
        $mailer->isHTML(false);

        return $mailer->send();

    } catch (Throwable $exception) {
        error_log('VEBRA Mailer error: ' . $exception->getMessage());
        return false;
    }
}
