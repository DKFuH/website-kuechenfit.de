<?php
declare(strict_types=1);

/**
 * API Endpoint: POST /api/contact_submit.php
 * ════════════════════════════════════════════════════════════════════════════
 *
 * Kontakt-/Anfrageformular-Handler für kuechenfit.de.
 *
 * Herausgeloest aus dem gemeinsamen kuechen-klas.de-Projekt (siehe dortiges
 * public/api/contact_submit.php fuer die Ursprungsversion, die von mehreren
 * Marken/Formularen gemeinsam genutzt wird). Diese Kopie ist bewusst
 * eigenstaendig: eigene SQLite-Datenbank, eigener Vault, keine Laufzeit-
 * Abhaengigkeit zu kuechen-klas.de oder kuechenfolieren.de.
 *
 * WICHTIG: Das ist bewusst KEIN automatischer Lead-Endpoint. Falls aus einer
 * Nachricht ein echter Lead wird, erfolgt die Weiterverarbeitung manuell aus
 * der E-Mail heraus (identisch zum Hauptprojekt).
 *
 * Backup-Strategie (mehrere unabhängige Wege, damit nichts verloren geht):
 *   1) SQLite-Datenbank außerhalb des Webroots (Quelle der Wahrheit)
 *   2) JSON-Vault-Datei außerhalb des Webroots (Rohkopie, falls DB korrupt)
 *   3) E-Mail-Versand an CONTACT_TO_EMAIL (primärer Zustellweg)
 *   4) n8n-Webhook (sekundärer/redundanter Zustellweg, optional)
 *
 * Spam-Schutz (mehrschichtig): CSRF-Token, zwei Honeypot-Felder, Time-Trap,
 * Rate-Limiting pro IP, Origin/Referer-Check, Content-Spam-Scoring.
 *
 * Environment Variables (.env, im Projektroot NICHT in /public/):
 *   CONTACT_TO_EMAIL, CONTACT_FROM_EMAIL, CONTACT_N8N_WEBHOOK_URL,
 *   CONTACT_N8N_BEARER_TOKEN, CONTACT_DISABLE_MAIL_DELIVERY,
 *   CONTACT_DISABLE_WEBHOOK_DELIVERY, CONTACT_RATE_LIMIT,
 *   CONTACT_MIN_SUBMIT_SECONDS, CONTACT_DB_PATH, DEBUG_MODE
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('X-Content-Type-Options: nosniff');

require_once dirname(__DIR__, 2) . '/app/mailer.php';

// ════════════════════════════════════════════════════════════════════════════
// HELPER FUNCTIONS
// ════════════════════════════════════════════════════════════════════════════

function respond(int $code, array $body): void {
  http_response_code($code);
  echo json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  exit;
}

function log_event(string $level, string $msg, array $context = []): void {
  $timestamp = gmdate('Y-m-d H:i:s');
  $contextStr = !empty($context) ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
  error_log("[$timestamp] [$level] contact_submit: $msg$contextStr");
}

function contactStoragePath(string|false $configured, string $default): string {
  $path = trim((string)$configured);
  if ($path === '') return $default;
  $isWindowsAbsolute = preg_match('/^[a-zA-Z]:[\\\\\/]/', $path) === 1;
  $isUnixAbsolute = str_starts_with($path, '/');
  if (DIRECTORY_SEPARATOR === '/' && $isWindowsAbsolute) {
    log_event('WARNING', 'Ignoring Windows storage path on Unix host');
    return $default;
  }
  if (DIRECTORY_SEPARATOR === '\\' && $isUnixAbsolute) {
    log_event('WARNING', 'Ignoring Unix storage path on Windows host');
    return $default;
  }
  return $path;
}

function getClientIp(): string {
  $trustProxy = strtolower((string)(getenv('TRUST_PROXY_HEADERS') ?: 'false')) === 'true';
  if ($trustProxy && !empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $candidate = trim((string)$_SERVER['HTTP_CF_CONNECTING_IP']);
    if (filter_var($candidate, FILTER_VALIDATE_IP)) return $candidate;
  }
  if ($trustProxy && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $candidate = trim(explode(',', (string)$_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
    if (filter_var($candidate, FILTER_VALIDATE_IP)) return $candidate;
  }
  $remote = trim((string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'));
  return filter_var($remote, FILTER_VALIDATE_IP) ? $remote : '0.0.0.0';
}

function safeFilename(string $s): string {
  return preg_replace('/[^a-zA-Z0-9._-]/', '_', $s) ?? 'unknown';
}

function sanitizeText(string $s, int $maxLen = 5000): string {
  $s = trim($s);
  $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $s) ?? $s;
  if (function_exists('mb_substr')) {
    $s = mb_substr($s, 0, $maxLen);
  } else {
    $s = substr($s, 0, $maxLen);
  }
  return $s;
}

function validateEmail(string $email): bool {
  $email = strtolower(trim($email));
  return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function loadEnv(string $filePath): void {
  if (!file_exists($filePath)) return;
  $lines = @file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  if (!is_array($lines)) return;
  foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    if (strpos($line, '=') === false) continue;
    [$key, $val] = explode('=', $line, 2);
    $key = trim($key);
    $val = trim($val);
    if (preg_match('/^["\'](.*)["\']$/', $val, $m)) {
      $val = $m[1];
    }
    if ($key !== '' && getenv($key) === false) {
      putenv("$key=$val");
    }
  }
}

function sanitizeHeaderValue(string $s): string {
  return trim(preg_replace('/[\r\n]+/', ' ', $s) ?? $s);
}

function computeSpamScore(string $name, string $message, string $email): int {
  $score = 0;
  $text = $name . ' ' . $message;

  $urlCount = preg_match_all('#https?://#i', $text);
  if ($urlCount >= 1) $score += 2;
  if ($urlCount >= 3) $score += 4;

  $triggers = [
    'viagra', 'cialis', 'casino', 'crypto invest', 'bitcoin invest', 'forex',
    'seo service', 'backlink', 'loan approved', 'work from home', 'bulk email',
    'приобрести', 'заработок', 'xxx', 'porn', 'escort',
  ];
  $lowerText = mb_strtolower($text);
  foreach ($triggers as $t) {
    if (mb_strpos($lowerText, $t) !== false) $score += 5;
  }

  $letters = preg_replace('/[^A-Za-zÀ-ÖØ-öø-ÿ]/u', '', $message) ?? '';
  if (mb_strlen($letters) > 20) {
    $upper = preg_replace('/[^A-ZÀ-Ö]/u', '', $letters) ?? '';
    $ratio = mb_strlen($upper) / max(1, mb_strlen($letters));
    if ($ratio > 0.6) $score += 3;
  }

  $emailDomain = strtolower((string)substr((string)strrchr($email, '@'), 1));
  if ($emailDomain !== '' && preg_match_all('#https?://([^/\s]+)#i', $text, $m)) {
    foreach ($m[1] as $host) {
      if (stripos($host, $emailDomain) !== false) { $score += 2; break; }
    }
  }

  $words = preg_split('/\s+/', trim($message)) ?: [];
  if (count($words) <= 2 && mb_strlen($message) > 60) $score += 3;

  return $score;
}

function sendContactMail(
  string $toEmail,
  string $fromEmail,
  string $name,
  string $email,
  string $phone,
  string $topic,
  string $message,
  string $timeframe,
  string $budgetRange,
  string $requestId,
  bool $suspectedSpam,
  string $subjectSuffix = ''
): array {
  $subjectPrefix = $suspectedSpam ? '[VERDACHT SPAM] ' : '';
  $subject = $subjectPrefix . 'Kontaktanfrage: ' . sanitizeHeaderValue($name)
    . ($subjectSuffix !== '' ? ' – ' . sanitizeHeaderValue($subjectSuffix) : '');

  $lines = [];
  $lines[] = 'Neue Kontaktanfrage über kuechenfit.de';
  $lines[] = str_repeat('-', 60);
  $lines[] = 'Name:        ' . $name;
  $lines[] = 'E-Mail:      ' . $email;
  if ($phone !== '')        $lines[] = 'Telefon:     ' . $phone;
  if ($topic !== '')        $lines[] = 'Thema:       ' . $topic;
  if ($timeframe !== '')    $lines[] = 'Zeitraum:    ' . $timeframe;
  if ($budgetRange !== '')  $lines[] = 'Budget:      ' . $budgetRange;
  $lines[] = '';
  $lines[] = 'Nachricht:';
  $lines[] = $message;
  $lines[] = '';
  $lines[] = str_repeat('-', 60);
  $lines[] = 'Request-ID: ' . $requestId;
  if ($suspectedSpam) {
    $lines[] = 'HINWEIS: Diese Nachricht wurde automatisch als möglicher Spam markiert.';
  }
  $body = implode("\n", $lines);

  $safeEmail = sanitizeHeaderValue($email);
  $replyTo = validateEmail($safeEmail) ? $safeEmail : '';
  $replyToName = $replyTo !== '' ? sanitizeHeaderValue($name) : '';

  return kk_send_mail($toEmail, 'KüchenFit', $subject, $body, $replyTo, $replyToName, 'Kontaktformular KüchenFit');
}

/**
 * Vorbereitungs-Stichpunkte fuer die Lead-Bestaetigungsmail. Diese Domain
 * bedient nur ein Thema (Kuechenmodernisierung), daher genuegt ein Fall.
 */
function leadPrepBullets(string $topic): array
{
  return [
    'Antworte auf diese E-Mail und häng ein paar Fotos vom aktuellen Zustand der Küche an',
    'Hilfreich: Gesamtaufnahme sowie Fotos von Fronten, Arbeitsplatte und beschädigten Stellen',
    'Ungefähre Maße der Bereiche, die sich verändern sollen',
  ];
}

function sendLeadConfirmationMail(
  string $replyToEmail,
  string $name,
  string $email,
  string $topic,
  string $timeframe,
  string $requestId
): array {
  if (!validateEmail($email)) {
    return ['ok' => false, 'error' => 'invalid_lead_email'];
  }

  $firstName = trim((string)strtok($name, ' '));
  $greetingName = $firstName !== '' ? $firstName : $name;
  $subject = 'Deine Anfrage ist bei KüchenFit angekommen';

  $lines = [];
  $lines[] = 'Hallo ' . $greetingName . ',';
  $lines[] = '';
  $lines[] = 'danke für deine Anfrage' . ($topic !== '' ? ' zu "' . $topic . '"' : '') . '. Ich melde mich persönlich bei dir' . ($timeframe !== '' ? ' – dein gewünschter Zeitrahmen (' . $timeframe . ') ist bei mir angekommen.' : '.');
  $lines[] = '';
  $lines[] = 'Am einfachsten schickst du uns deine Küchenfotos direkt als Antwort auf diese E-Mail. Damit unser erstes Gespräch schnell konkret wird, hilft:';
  foreach (leadPrepBullets($topic) as $bullet) {
    $lines[] = '- ' . $bullet;
  }
  $lines[] = '';
  $lines[] = 'Du musst dafür nichts vorbereiten oder ausrechnen – eine grobe Einschätzung reicht völlig.';
  $lines[] = '';
  $lines[] = 'Bis gleich,';
  $lines[] = 'Daniel Klas';
  $lines[] = 'KüchenFit – ein Service von Klas Küchen';
  $lines[] = '';
  $lines[] = kk_business_letter_footer();
  $body = implode("\n", $lines);

  // Reply-To bewusst auf das betreute Postfach (CONTACT_TO_EMAIL): Foto-Antworten
  // der Interessenten sollen dort landen, nicht im No-Reply-Absender.
  $safeReplyTo = sanitizeHeaderValue($replyToEmail);
  return kk_send_mail($email, $greetingName, $subject, $body, $safeReplyTo, '', 'Daniel Klas – KüchenFit');
}

function sendContactWebhook(string $webhookUrl, string $bearerToken, array $payload): array {
  if ($webhookUrl === '') {
    return ['attempted' => false, 'ok' => false, 'error' => null];
  }

  $ch = curl_init($webhookUrl);
  if ($ch === false) {
    return ['attempted' => true, 'ok' => false, 'error' => 'curl_init failed'];
  }

  $headers = [
    'Content-Type: application/json; charset=utf-8',
    'X-Request-Id: ' . (string)($payload['request_id'] ?? ''),
  ];
  if ($bearerToken !== '') {
    $headers[] = 'Authorization: Bearer ' . $bearerToken;
  }

  curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 3,
    CURLOPT_TIMEOUT => 8,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
  ]);

  $responseBody = curl_exec($ch);
  $curlErr = curl_error($ch);
  $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($responseBody === false || $httpCode >= 400) {
    return ['attempted' => true, 'ok' => false, 'error' => $curlErr ?: ('HTTP ' . $httpCode)];
  }
  return ['attempted' => true, 'ok' => true, 'error' => null];
}

function getDb(string $dbPath): PDO {
  $dir = dirname($dbPath);
  if (!is_dir($dir) && !@mkdir($dir, 0700, true)) {
    throw new RuntimeException('Cannot create storage directory: ' . $dir);
  }
  $pdo = new PDO('sqlite:' . $dbPath);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->exec('PRAGMA journal_mode = WAL;');
  $pdo->exec(<<<SQL
    CREATE TABLE IF NOT EXISTS submissions (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      request_id TEXT NOT NULL UNIQUE,
      received_at_utc TEXT NOT NULL,
      name TEXT NOT NULL,
      email TEXT NOT NULL,
      phone TEXT,
      topic TEXT,
      message TEXT NOT NULL,
      timeframe TEXT,
      budget_range TEXT,
      page_url TEXT,
      referrer TEXT,
      utm_source TEXT,
      utm_medium TEXT,
      utm_campaign TEXT,
      utm_content TEXT,
      utm_term TEXT,
      fbclid TEXT,
      gclid TEXT,
      wbraid TEXT,
      gbraid TEXT,
      msclkid TEXT,
      epik TEXT,
      client_ip TEXT,
      user_agent TEXT,
      spam_score INTEGER NOT NULL DEFAULT 0,
      is_suspected_spam INTEGER NOT NULL DEFAULT 0,
      mail_sent INTEGER NOT NULL DEFAULT 0,
      mail_error TEXT,
      webhook_attempted INTEGER NOT NULL DEFAULT 0,
      webhook_sent INTEGER NOT NULL DEFAULT 0,
      webhook_error TEXT
    );
  SQL);
  $columns = $pdo->query('PRAGMA table_info(submissions)')->fetchAll(PDO::FETCH_ASSOC);
  $columnNames = array_column($columns, 'name');
  foreach (['utm_content', 'utm_term', 'fbclid', 'gclid', 'wbraid', 'gbraid', 'msclkid', 'epik'] as $columnName) {
    if (!in_array($columnName, $columnNames, true)) {
      $pdo->exec('ALTER TABLE submissions ADD COLUMN ' . $columnName . ' TEXT');
    }
  }
  $pdo->exec('CREATE INDEX IF NOT EXISTS idx_submissions_received_at ON submissions(received_at_utc);');
  return $pdo;
}

// ════════════════════════════════════════════════════════════════════════════
// LOAD ENVIRONMENT + CONFIG
// ════════════════════════════════════════════════════════════════════════════

foreach ([
  dirname(__DIR__) . '/.env',
  dirname(__DIR__, 2) . '/.env',
  __DIR__ . '/../.env',
] as $envFile) {
  if (file_exists($envFile)) {
    loadEnv($envFile);
    break;
  }
}

$DEBUG_MODE          = strtolower((string)(getenv('DEBUG_MODE') ?: 'false')) === 'true';
$DISABLE_EXTERNAL_DELIVERY = $DEBUG_MODE
  && strtolower((string)(getenv('CONTACT_DISABLE_EXTERNAL_DELIVERY') ?: 'false')) === 'true';
$DISABLE_MAIL_DELIVERY = $DISABLE_EXTERNAL_DELIVERY
  || strtolower((string)(getenv('CONTACT_DISABLE_MAIL_DELIVERY') ?: 'false')) === 'true';
$DISABLE_WEBHOOK_DELIVERY = $DISABLE_EXTERNAL_DELIVERY
  || strtolower((string)(getenv('CONTACT_DISABLE_WEBHOOK_DELIVERY') ?: 'false')) === 'true';
$CONTACT_TO_EMAIL    = getenv('CONTACT_TO_EMAIL') ?: 'kontakt@kuechen-klas.de';
$CONTACT_FROM_EMAIL  = getenv('CONTACT_FROM_EMAIL') ?: 'kontaktformular@kuechenfit.de';
$N8N_WEBHOOK_URL     = getenv('CONTACT_N8N_WEBHOOK_URL') ?: '';
$N8N_BEARER_TOKEN    = getenv('CONTACT_N8N_BEARER_TOKEN') ?: '';
$RATE_LIMIT_PER_MIN  = max(1, (int)(getenv('CONTACT_RATE_LIMIT') ?: 5));
$MIN_SUBMIT_SECONDS  = max(1, (int)(getenv('CONTACT_MIN_SUBMIT_SECONDS') ?: 3));
$MAX_FORM_AGE_SECONDS = 3 * 3600;

$DEFAULT_STORAGE_DIR = dirname(__DIR__, 2) . '/storage';
$DB_PATH    = contactStoragePath(getenv('CONTACT_DB_PATH'), $DEFAULT_STORAGE_DIR . '/contact_submissions.db');
$VAULT_DIR  = contactStoragePath(getenv('CONTACT_VAULT_DIR'), $DEFAULT_STORAGE_DIR . '/contact_vault');

// ════════════════════════════════════════════════════════════════════════════
// REQUEST METHOD + ORIGIN CHECK
// ════════════════════════════════════════════════════════════════════════════

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  respond(405, ['ok' => false, 'error' => 'method_not_allowed']);
}

// Eigenstaendiges Projekt: Origin-Whitelist nur noch fuer diese eine Domain.
$allowedOrigins = [
  'https://kuechenfit.de',
  'https://www.kuechenfit.de',
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '' && !in_array($origin, $allowedOrigins, true) && !$DEBUG_MODE) {
  log_event('WARN', 'Blocked cross-origin request', ['origin' => $origin, 'ip' => getClientIp()]);
  respond(403, ['ok' => false, 'error' => 'origin_not_allowed']);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// ════════════════════════════════════════════════════════════════════════════
// INPUT PARSING
// ════════════════════════════════════════════════════════════════════════════

$contentType = strtolower(trim(explode(';', (string)($_SERVER['CONTENT_TYPE'] ?? ''), 2)[0]));
if (in_array($contentType, ['multipart/form-data', 'application/x-www-form-urlencoded'], true)) {
  $input = $_POST;
} else {
  $raw = file_get_contents('php://input') ?: '';
  if (strlen($raw) > 200_000) {
    respond(413, ['ok' => false, 'error' => 'payload_too_large']);
  }
  $input = json_decode($raw, true);
  if (!is_array($input)) $input = $_POST;
}

// ════════════════════════════════════════════════════════════════════════════
// SPAM-SCHUTZ SCHICHT 1: CSRF
// ════════════════════════════════════════════════════════════════════════════

$csrfToken = trim((string)($input['csrf_token'] ?? ''));
$sessionCsrf = trim((string)($_SESSION['csrf_token'] ?? ''));
if ($csrfToken === '' || $sessionCsrf === '' || !hash_equals($sessionCsrf, $csrfToken)) {
  log_event('WARN', 'CSRF validation failed', ['ip' => getClientIp()]);
  respond(403, ['ok' => false, 'error' => 'csrf_invalid']);
}

// ════════════════════════════════════════════════════════════════════════════
// SPAM-SCHUTZ SCHICHT 2: DOPPELTES HONEYPOT
// ════════════════════════════════════════════════════════════════════════════

$honeypot1 = trim((string)($input['website'] ?? ''));
$honeypot2 = trim((string)($input['hp_field'] ?? ''));
if ($honeypot1 !== '' || $honeypot2 !== '') {
  log_event('WARN', 'Honeypot triggered', ['ip' => getClientIp()]);
  respond(200, ['ok' => true, 'status' => 'accepted']);
}

// ════════════════════════════════════════════════════════════════════════════
// SPAM-SCHUTZ SCHICHT 3: TIME-TRAP
// ════════════════════════════════════════════════════════════════════════════

$formRenderedAt = (int)($input['form_rendered_at'] ?? 0);
$now = time();
if ($formRenderedAt <= 0) {
  log_event('WARN', 'Missing form_rendered_at', ['ip' => getClientIp()]);
  respond(200, ['ok' => true, 'status' => 'accepted']);
}
$elapsed = $now - $formRenderedAt;
if ($elapsed < $MIN_SUBMIT_SECONDS) {
  log_event('WARN', 'Time-trap triggered (too fast)', ['ip' => getClientIp(), 'elapsed' => $elapsed]);
  respond(200, ['ok' => true, 'status' => 'accepted']);
}
if ($elapsed > $MAX_FORM_AGE_SECONDS) {
  respond(400, ['ok' => false, 'error' => 'form_expired']);
}

// ════════════════════════════════════════════════════════════════════════════
// INPUT SANITIZATION
// ════════════════════════════════════════════════════════════════════════════

$name        = sanitizeText((string)($input['name'] ?? ''), 100);
$email       = strtolower(trim((string)($input['email'] ?? '')));
$message     = sanitizeText((string)($input['message'] ?? ''), 5000);
$phone       = sanitizeText((string)($input['phone'] ?? ''), 30);
$topic       = sanitizeText((string)($input['topic'] ?? ''), 100);
$timeframe   = sanitizeText((string)($input['timeframe'] ?? ''), 50);
$budgetRange = sanitizeText((string)($input['budget_range'] ?? ''), 50);
$consent     = (bool)($input['consent'] ?? false);

// ════════════════════════════════════════════════════════════════════════════
// VALIDATION
// ════════════════════════════════════════════════════════════════════════════

$errors = [];
if (mb_strlen($name) < 2)  $errors[] = 'name_too_short';
if (mb_strlen($name) > 100) $errors[] = 'name_too_long';
if (!validateEmail($email)) $errors[] = 'email_invalid';
if (mb_strlen($message) < 10) $errors[] = 'message_too_short';
if (mb_strlen($message) > 5000) $errors[] = 'message_too_long';
if (!$consent) $errors[] = 'consent_required';

if (!empty($errors)) {
  respond(422, ['ok' => false, 'error' => 'validation_failed', 'errors' => $errors]);
}

// ════════════════════════════════════════════════════════════════════════════
// SPAM-SCHUTZ SCHICHT 4: RATE LIMITING (pro IP, pro Minute)
// ════════════════════════════════════════════════════════════════════════════

$ip = getClientIp();
$minute = (int)floor($now / 60);
$rateDir = $VAULT_DIR . '/.ratelimit';
if (!is_dir($rateDir) && !@mkdir($rateDir, 0700, true) && !is_dir($rateDir)) {
  log_event('ERROR', 'Cannot create rate-limit directory', ['dir' => $rateDir]);
  respond(500, ['ok' => false, 'error' => 'storage_unavailable']);
}
$rateFile = $rateDir . '/' . safeFilename($ip . '_' . $minute);
$rateHandle = @fopen($rateFile, 'c+');
if ($rateHandle === false || !flock($rateHandle, LOCK_EX)) {
  if (is_resource($rateHandle)) fclose($rateHandle);
  log_event('ERROR', 'Cannot lock rate-limit file', ['file' => $rateFile]);
  respond(500, ['ok' => false, 'error' => 'storage_unavailable']);
}
$storedCount = stream_get_contents($rateHandle);
$count = (int)$storedCount + 1;
rewind($rateHandle);
ftruncate($rateHandle, 0);
fwrite($rateHandle, (string)$count);
fflush($rateHandle);
flock($rateHandle, LOCK_UN);
fclose($rateHandle);

if ($count > $RATE_LIMIT_PER_MIN) {
  log_event('WARN', 'Rate limit exceeded', ['ip' => $ip, 'count' => $count]);
  respond(429, ['ok' => false, 'error' => 'rate_limited']);
}

// ════════════════════════════════════════════════════════════════════════════
// SPAM-SCHUTZ SCHICHT 5: CONTENT-SCORING (markiert, blockt nicht hart)
// ════════════════════════════════════════════════════════════════════════════

$spamScore = computeSpamScore($name, $message, $email);
$isSuspectedSpam = $spamScore >= 5;

// ════════════════════════════════════════════════════════════════════════════
// PAYLOAD ZUSAMMENSTELLEN
// ════════════════════════════════════════════════════════════════════════════

$requestId = bin2hex(random_bytes(16));
$nowIso = gmdate('c');
$utm = [
  'source' => sanitizeText((string)($input['utm_source'] ?? ''), 100),
  'medium' => sanitizeText((string)($input['utm_medium'] ?? ''), 100),
  'campaign' => sanitizeText((string)($input['utm_campaign'] ?? ''), 100),
  'content' => sanitizeText((string)($input['utm_content'] ?? ''), 100),
  'term' => sanitizeText((string)($input['utm_term'] ?? ''), 100),
];
$clickIds = [];
foreach (['fbclid', 'gclid', 'wbraid', 'gbraid', 'msclkid', 'epik'] as $clickId) {
  $clickIds[$clickId] = sanitizeText((string)($input[$clickId] ?? ''), 180);
}

$payload = [
  'request_id' => $requestId,
  'received_at_utc' => $nowIso,
  'client_ip' => $ip,
  'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
  'page_url' => sanitizeText((string)($input['page_url'] ?? ($_SERVER['HTTP_REFERER'] ?? '')), 500),
  'referrer' => sanitizeText((string)($input['referrer'] ?? ($_SERVER['HTTP_REFERER'] ?? '')), 500),
  'utm' => $utm,
  'utm_source' => $utm['source'],
  'utm_medium' => $utm['medium'],
  'utm_campaign' => $utm['campaign'],
  'utm_content' => $utm['content'],
  'utm_term' => $utm['term'],
  'click_ids' => $clickIds,
  ...$clickIds,
  'contact' => [
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'topic' => $topic,
    'message' => $message,
    'timeframe' => $timeframe,
    'budget_range' => $budgetRange,
    'consent' => $consent,
  ],
  'spam_score' => $spamScore,
  'is_suspected_spam' => $isSuspectedSpam,
];

// ════════════════════════════════════════════════════════════════════════════
// BACKUP 1: JSON-VAULT (Rohkopie, unabhängig von DB/Mail/Webhook)
// ════════════════════════════════════════════════════════════════════════════

$vaultFile = $VAULT_DIR . '/' . gmdate('Ymd') . '/' . gmdate('His') . '_' . safeFilename($requestId) . '.json';
$vaultSubDir = dirname($vaultFile);
if (!is_dir($vaultSubDir) && !@mkdir($vaultSubDir, 0700, true) && !is_dir($vaultSubDir)) {
  log_event('ERROR', 'Cannot create vault directory', ['dir' => $vaultSubDir]);
}
$vaultJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
if (@file_put_contents($vaultFile, $vaultJson, LOCK_EX) === false) {
  log_event('ERROR', 'Vault write failed', ['file' => $vaultFile]);
}

// ════════════════════════════════════════════════════════════════════════════
// BACKUP 2 (Quelle der Wahrheit): SQLITE
// ════════════════════════════════════════════════════════════════════════════

try {
  $pdo = getDb($DB_PATH);
} catch (Throwable $e) {
  log_event('ERROR', 'Database unavailable', ['error' => $e->getMessage()]);
  respond(500, ['ok' => false, 'error' => 'storage_unavailable']);
}

try {
  $stmt = $pdo->prepare(<<<SQL
    INSERT INTO submissions (
      request_id, received_at_utc, name, email, phone, topic, message,
      timeframe, budget_range, page_url, referrer, utm_source, utm_medium, utm_campaign, utm_content, utm_term,
      fbclid, gclid, wbraid, gbraid, msclkid, epik,
      client_ip, user_agent, spam_score, is_suspected_spam,
      mail_sent, mail_error, webhook_attempted, webhook_sent, webhook_error
    ) VALUES (
      :request_id, :received_at_utc, :name, :email, :phone, :topic, :message,
      :timeframe, :budget_range, :page_url, :referrer, :utm_source, :utm_medium, :utm_campaign, :utm_content, :utm_term,
      :fbclid, :gclid, :wbraid, :gbraid, :msclkid, :epik,
      :client_ip, :user_agent, :spam_score, :is_suspected_spam,
      0, NULL, 0, 0, NULL
    )
  SQL);
  $stmt->execute([
    ':request_id' => $requestId,
    ':received_at_utc' => $nowIso,
    ':name' => $name,
    ':email' => $email,
    ':phone' => $phone,
    ':topic' => $topic,
    ':message' => $message,
    ':timeframe' => $timeframe,
    ':budget_range' => $budgetRange,
    ':page_url' => $payload['page_url'],
    ':referrer' => $payload['referrer'],
    ':utm_source' => $payload['utm']['source'],
    ':utm_medium' => $payload['utm']['medium'],
    ':utm_campaign' => $payload['utm']['campaign'],
    ':utm_content' => $payload['utm']['content'],
    ':utm_term' => $payload['utm']['term'],
    ':fbclid' => $payload['click_ids']['fbclid'],
    ':gclid' => $payload['click_ids']['gclid'],
    ':wbraid' => $payload['click_ids']['wbraid'],
    ':gbraid' => $payload['click_ids']['gbraid'],
    ':msclkid' => $payload['click_ids']['msclkid'],
    ':epik' => $payload['click_ids']['epik'],
    ':client_ip' => $ip,
    ':user_agent' => $payload['user_agent'],
    ':spam_score' => $spamScore,
    ':is_suspected_spam' => $isSuspectedSpam ? 1 : 0,
  ]);
} catch (Throwable $e) {
  log_event('ERROR', 'Database insert failed', ['request_id' => $requestId, 'error' => $e->getMessage()]);
  respond(500, ['ok' => false, 'error' => 'storage_write_failed']);
}

// ════════════════════════════════════════════════════════════════════════════
// ZUSTELLWEG 1: E-MAIL (primär)
// ════════════════════════════════════════════════════════════════════════════

$disableMailForRequest = $DISABLE_MAIL_DELIVERY;
$mailResult = $disableMailForRequest
  ? ['ok' => false, 'error' => 'disabled_by_configuration']
  : sendContactMail(
      $CONTACT_TO_EMAIL,
      $CONTACT_FROM_EMAIL,
      $name,
      $email,
      $phone,
      $topic,
      $message,
      $timeframe,
      $budgetRange,
      $requestId,
      $isSuspectedSpam
    );

if (!$mailResult['ok'] && !$disableMailForRequest) {
  log_event('ERROR', 'Mail delivery failed', ['request_id' => $requestId, 'error' => $mailResult['error']]);
}

// ════════════════════════════════════════════════════════════════════════════
// ZUSTELLWEG 1b: BESTÄTIGUNGSMAIL AN DEN LEAD
// ════════════════════════════════════════════════════════════════════════════

if (!$disableMailForRequest && !$isSuspectedSpam) {
  $leadMailResult = sendLeadConfirmationMail($CONTACT_TO_EMAIL, $name, $email, $topic, $timeframe, $requestId);
  if (!$leadMailResult['ok']) {
    log_event('ERROR', 'Lead confirmation mail failed', ['request_id' => $requestId, 'error' => $leadMailResult['error']]);
  }
}

// ════════════════════════════════════════════════════════════════════════════
// ZUSTELLWEG 2: N8N-WEBHOOK (sekundär, redundant)
// ════════════════════════════════════════════════════════════════════════════

$webhookResult = $DISABLE_WEBHOOK_DELIVERY
  ? ['attempted' => false, 'ok' => false, 'error' => 'disabled_by_configuration']
  : sendContactWebhook($N8N_WEBHOOK_URL, $N8N_BEARER_TOKEN, $payload);
if ($webhookResult['attempted'] && !$webhookResult['ok']) {
  log_event('WARN', 'Webhook delivery failed', ['request_id' => $requestId, 'error' => $webhookResult['error']]);
}

// ════════════════════════════════════════════════════════════════════════════
// ZUSTELLSTATUS IN SQLITE AKTUALISIEREN
// ════════════════════════════════════════════════════════════════════════════

try {
  $stmt = $pdo->prepare(<<<SQL
    UPDATE submissions
       SET mail_sent = :mail_sent,
           mail_error = :mail_error,
           webhook_attempted = :webhook_attempted,
           webhook_sent = :webhook_sent,
           webhook_error = :webhook_error
     WHERE request_id = :request_id
  SQL);
  $stmt->execute([
    ':request_id' => $requestId,
    ':mail_sent' => $mailResult['ok'] ? 1 : 0,
    ':mail_error' => $mailResult['error'],
    ':webhook_attempted' => $webhookResult['attempted'] ? 1 : 0,
    ':webhook_sent' => $webhookResult['ok'] ? 1 : 0,
    ':webhook_error' => $webhookResult['error'],
  ]);
} catch (Throwable $e) {
  @file_put_contents($vaultFile . '.status.json', json_encode([
    'request_id' => $requestId,
    'mail' => $mailResult,
    'webhook' => $webhookResult,
    'status_update_error' => $e->getMessage(),
    'recorded_at_utc' => gmdate('c'),
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
  log_event('ERROR', 'Delivery status update failed', ['request_id' => $requestId, 'error' => $e->getMessage()]);
}

log_event('INFO', 'Contact submission stored', [
  'request_id' => $requestId,
  'mail_sent' => $mailResult['ok'],
  'webhook_sent' => $webhookResult['ok'],
  'spam_score' => $spamScore,
]);

respond(200, [
  'ok' => true,
  'status' => 'accepted',
  'request_id' => $requestId,
  'message' => 'Vielen Dank! Wir melden uns persönlich bei dir.',
]);
