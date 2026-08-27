<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * Gemeinsamer PHPMailer/SMTP-Versand für die gesamte Seite. Ursprünglich nur
 * für die Widerrufs-Bestätigung gebaut (app/widerruf.php), inzwischen auch
 * von der Kontaktformular-Pipeline (contact_submit.php) genutzt – ersetzt
 * dort den bisherigen PHP mail()-Versand.
 */

require_once __DIR__ . '/lib/phpmailer/Exception.php';
require_once __DIR__ . '/lib/phpmailer/SMTP.php';
require_once __DIR__ . '/lib/phpmailer/PHPMailer.php';

if (!function_exists('kk_mailer_load_env')) {
    function kk_mailer_load_env(): void
    {
        static $loaded = false;
        if ($loaded) return;
        $loaded = true;
        foreach ([dirname(__DIR__) . '/.env', '/etc/klas-kuechen/.env'] as $path) {
            if (!is_file($path) || !is_readable($path)) continue;
            $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (!is_array($lines)) continue;
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
                [$key, $val] = explode('=', $line, 2);
                $key = trim($key);
                $val = trim($val);
                if (preg_match('/^["\'](.*)["\']$/', $val, $m)) $val = $m[1];
                if ($key !== '' && getenv($key) === false) putenv("$key=$val");
            }
            break;
        }
    }
}

if (!function_exists('kk_smtp_configured')) {
    function kk_smtp_configured(): bool
    {
        kk_mailer_load_env();
        return trim((string)(getenv('SMTP_HOST') ?: '')) !== '';
    }
}

if (!function_exists('kk_send_mail')) {
    /**
     * Versand über PHPMailer/SMTP statt PHP mail(). Die tatsächliche
     * Absenderadresse ist immer die SMTP-authentifizierte Adresse
     * (SMTP_FROM_EMAIL) – viele SMTP-Server lehnen abweichende From-Adressen
     * ab oder werten sie als Spam-Signal. Für "Antworten geht an den
     * richtigen Empfänger" sorgt stattdessen Reply-To.
     *
     * @return array{ok: bool, error: ?string}
     */
    function kk_send_mail(
        string $toEmail,
        string $toName,
        string $subject,
        string $body,
        string $replyTo = '',
        string $replyToName = '',
        string $fromNameOverride = ''
    ): array {
        kk_mailer_load_env();

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = (string)(getenv('SMTP_HOST') ?: '');
            $mail->Port       = (int)(getenv('SMTP_PORT') ?: 587);
            $mail->SMTPAuth   = true;
            $mail->Username   = (string)(getenv('SMTP_USERNAME') ?: '');
            $mail->Password   = (string)(getenv('SMTP_PASSWORD') ?: '');
            $encryption       = strtolower((string)(getenv('SMTP_ENCRYPTION') ?: 'tls'));
            $mail->SMTPSecure = $encryption === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->CharSet    = 'UTF-8';

            $fromEmail = (string)(getenv('SMTP_FROM_EMAIL') ?: 'kontakt@kuechen-klas.de');
            $fromName  = $fromNameOverride !== '' ? $fromNameOverride : (string)(getenv('SMTP_FROM_NAME') ?: 'Klas Küchen');
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($toEmail, $toName);
            if ($replyTo !== '') $mail->addReplyTo($replyTo, $replyToName);

            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return ['ok' => true, 'error' => null];
        } catch (PHPMailerException $e) {
            return ['ok' => false, 'error' => $mail->ErrorInfo ?: $e->getMessage()];
        } catch (Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}

if (!function_exists('kk_business_letter_footer')) {
    /**
     * Pflichtangaben für geschäftliche Korrespondenz nach außen (Geschäftsbrief),
     * exakt wie im aktuellen Impressum. Nur an E-Mails anhängen, die an
     * externe Empfänger (Kunden/Leads) gehen – nicht an interne
     * Benachrichtigungen ans eigene Postfach. Bewusst als fester Block statt
     * aus config/business.php zusammengesetzt, da hier zusätzliche Angaben
     * (Fax, USt-IdNr., abweichende Anschrift für die MStV-Verantwortlichkeit,
     * Handwerkskammer, Schlichtungshinweis) enthalten sind, die dort nicht
     * gepflegt werden. Bei einer Änderung im Impressum hier mit abgleichen.
     */
    function kk_business_letter_footer(): string
    {
        return implode("\n", [
            'Daniel Klas',
            'Hauptstraße 31a',
            '55487 Sohren',
            'Deutschland',
            '',
            'Tel.: +49 (0)6763 5189970',
            'Fax: +49 (0)6763 5189971',
            'E-Mail: kontakt@kuechen-klas.de',
            '',
            'Umsatzsteuer-Identifikationsnummer: DE341907681',
            '',
            'Verantwortliche/r i.S.d. § 18 Abs. 2 MStV:',
            'Daniel Klas, Auf der Forst 9, 55481 Metzenhausen',
            '',
            'Wir sind zur Teilnahme an einem Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle weder verpflichtet noch bereit.',
            '',
            'Zuständige Handwerkskammer: Handwerkskammer Koblenz - Betriebsnummer: 109447',
            '',
            'Mitglied der Initiative "Fairness im Handel".',
            'Nähere Informationen: https://www.fairness-im-handel.de',
        ]);
    }
}
