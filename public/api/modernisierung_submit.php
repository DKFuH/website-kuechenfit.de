<?php
declare(strict_types=1);

/**
 * Adapter zwischen dem KüchenFit-Formular und der generischen
 * contact_submit.php-Pipeline. Herausgeloest aus dem gemeinsamen
 * kuechen-klas.de-Projekt; dort bediente diese Datei zusaetzlich
 * kuechenfolieren.de ueber ein Marken-Array. Da dieses Projekt nur noch
 * KüchenFit ist, entfaellt die Markenpruefung.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('X-Content-Type-Options: nosniff');

function ms_respond(int $status, array $payload): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function ms_text(mixed $value, int $length = 500): string
{
    $text = trim((string)$value);
    $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $text) ?? $text;
    return function_exists('mb_substr') ? mb_substr($text, 0, $length) : substr($text, 0, $length);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') ms_respond(405, ['ok' => false, 'message' => 'Ungültige Anfrage.']);
if (strtolower((string)($_SERVER['HTTP_SEC_FETCH_SITE'] ?? '')) === 'cross-site') ms_respond(403, ['ok' => false, 'message' => 'Die Anfrage wurde abgelehnt.']);

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$csrf = ms_text($_POST['csrf_token'] ?? '', 128);
$sessionCsrf = ms_text($_SESSION['csrf_token'] ?? '', 128);
if ($csrf === '' || $sessionCsrf === '' || !hash_equals($sessionCsrf, $csrf)) {
    ms_respond(403, ['ok' => false, 'message' => 'Die Sitzung ist abgelaufen. Bitte lade die Seite neu.']);
}
if (ms_text($_POST['website'] ?? '', 200) !== '' || ms_text($_POST['hp_field'] ?? '', 200) !== '') {
    ms_respond(200, ['ok' => true, 'status' => 'accepted']);
}

$services = ['Folierung', 'Frontentausch', 'Arbeitsplatte', 'Spüle und Armatur', 'Nischenrückwand', 'Licht und Geräte', 'Stauraum und Funktion', 'Komplettmodernisierung', 'Entscheidungscheck', 'Bestandsprüfung'];
$timeframes = ['Sofort', '1–3 Monate', '3–6 Monate', 'Später', 'Noch offen'];
$service = ms_text($_POST['service'] ?? '', 80);
$timeframe = ms_text($_POST['zeitpunkt'] ?? '', 50);
$name = ms_text($_POST['name'] ?? '', 100);
$email = strtolower(ms_text($_POST['email'] ?? '', 254));
$phone = ms_text($_POST['phone'] ?? '', 30);
$plz = ms_text($_POST['plz'] ?? '', 5);
$details = ms_text($_POST['details'] ?? '', 2000);
$consent = ($_POST['consent'] ?? '') === '1';
if (!in_array($service, $services, true)
    || !in_array($timeframe, $timeframes, true)
    || mb_strlen($name) < 2
    || filter_var($email, FILTER_VALIDATE_EMAIL) === false
    || !preg_match('/^[0-9]{5}$/', $plz)
    || mb_strlen($details) < 10
    || !$consent) {
    ms_respond(422, ['ok' => false, 'message' => 'Bitte prüfe die Pflichtfelder.']);
}

$lines = [
    'Anfrage über KüchenFit',
    '',
    'Gewünschter Service: ' . $service,
    'PLZ: ' . $plz,
    'Beschreibung: ' . $details,
];

$pageUrl = ms_text($_POST['page_url'] ?? '', 500);
$referrer = ms_text($_POST['referrer'] ?? '', 500);
$trackingFields = [];
foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'] as $field) {
    $trackingFields[$field] = ms_text($_POST[$field] ?? '', 100);
}
foreach (['fbclid', 'gclid', 'wbraid', 'gbraid', 'msclkid', 'epik'] as $field) {
    $trackingFields[$field] = ms_text($_POST[$field] ?? '', 180);
}
$_POST = [
    'csrf_token' => $csrf,
    'form_rendered_at' => (string)((int)($_POST['form_rendered_at'] ?? 0)),
    'website' => '',
    'hp_field' => '',
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'topic' => 'KüchenFit Modernisierung',
    'message' => implode("\n", $lines),
    'timeframe' => $timeframe,
    'budget_range' => '',
    'consent' => true,
    'page_url' => $pageUrl,
    'referrer' => $referrer,
    ...$trackingFields,
];

require __DIR__ . '/contact_submit.php';
