<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/consent.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
header('X-Content-Type-Options: nosniff');

function consent_error(string $code, int $status): never
{
    http_response_code($status);
    echo json_encode(['ok' => false, 'error' => $code], JSON_UNESCAPED_SLASHES);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Allow: POST');
    consent_error('method_not_allowed', 405);
}
if (strtolower((string)($_SERVER['HTTP_SEC_FETCH_SITE'] ?? '')) === 'cross-site') consent_error('origin_denied', 403);
$contentType = strtolower(trim(explode(';', (string)($_SERVER['CONTENT_TYPE'] ?? ''))[0]));
if ($contentType !== 'application/json') consent_error('invalid_content_type', 415);
$origin = rtrim(trim((string)($_SERVER['HTTP_ORIGIN'] ?? '')), '/');
if ($origin !== '') {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
    $allowed = [$scheme . '://' . $host, 'https://kuechenfit.de', 'https://www.kuechenfit.de'];
    if (!in_array(strtolower($origin), array_map('strtolower', $allowed), true)) consent_error('origin_denied', 403);
}
$length = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
if ($length < 2 || $length > 8192) consent_error('invalid_size', 413);
$raw = file_get_contents('php://input', false, null, 0, 8193);
if (!is_string($raw) || strlen($raw) > 8192) consent_error('invalid_size', 413);
try { $data = json_decode($raw, true, 32, JSON_THROW_ON_ERROR); } catch (Throwable) { consent_error('invalid_json', 400); }
if (!is_array($data)) consent_error('invalid_payload', 422);

$config = kk_consent_config();
$consentId = (string)($data['consentId'] ?? '');
$action = (string)($data['action'] ?? '');
$revision = filter_var($data['revision'] ?? null, FILTER_VALIDATE_INT);
if (!preg_match('/^[A-Za-z0-9_-]{8,128}$/', $consentId)) consent_error('invalid_consent_id', 422);
if (!in_array($action, ['created', 'changed'], true)) consent_error('invalid_action', 422);
if ($revision !== $config['revision']) consent_error('invalid_revision', 409);

$validCategories = array_keys($config['categories']);
$validProviders = [];
foreach ($config['providers'] as $id => $provider) $validProviders[$provider['category']][] = $id;
$normalizeList = static function (mixed $value, array $allowed): array {
    if (!is_array($value)) consent_error('invalid_selection', 422);
    $result = [];
    foreach ($value as $item) {
        if (!is_string($item) || !in_array($item, $allowed, true)) consent_error('invalid_selection', 422);
        $result[] = $item;
    }
    return array_values(array_unique($result));
};
$normalizeServices = static function (mixed $value) use ($validProviders, $validCategories, $normalizeList): array {
    if (!is_array($value)) consent_error('invalid_services', 422);
    $result = [];
    foreach ($value as $category => $services) {
        if (!is_string($category) || !in_array($category, $validCategories, true)) consent_error('invalid_services', 422);
        $result[$category] = $normalizeList($services, $validProviders[$category] ?? []);
    }
    return $result;
};

$acceptedCategories = $normalizeList($data['acceptedCategories'] ?? [], $validCategories);
$rejectedCategories = $normalizeList($data['rejectedCategories'] ?? [], $validCategories);
$acceptedServices = $normalizeServices($data['acceptedServices'] ?? []);
$rejectedServices = $normalizeServices($data['rejectedServices'] ?? []);

try {
    $pdo = kk_consent_db(true);
    $stmt = $pdo->prepare('INSERT INTO consent_receipts
        (consent_id, revision, material_hash, action, accepted_categories, rejected_categories, accepted_services, rejected_services, created_at_utc)
        VALUES (:consent_id, :revision, :material_hash, :action, :accepted_categories, :rejected_categories, :accepted_services, :rejected_services, :created_at_utc)');
    $stmt->execute([
        ':consent_id' => $consentId,
        ':revision' => $config['revision'],
        ':material_hash' => kk_consent_material_hash($config),
        ':action' => $action,
        ':accepted_categories' => json_encode($acceptedCategories, JSON_UNESCAPED_SLASHES),
        ':rejected_categories' => json_encode($rejectedCategories, JSON_UNESCAPED_SLASHES),
        ':accepted_services' => json_encode($acceptedServices, JSON_UNESCAPED_SLASHES),
        ':rejected_services' => json_encode($rejectedServices, JSON_UNESCAPED_SLASHES),
        ':created_at_utc' => gmdate('Y-m-d\TH:i:s\Z'),
    ]);
    http_response_code(201);
    echo json_encode(['ok' => true], JSON_UNESCAPED_SLASHES);
} catch (Throwable $error) {
    error_log('Consent receipt storage failed: ' . $error->getMessage());
    consent_error('storage_unavailable', 503);
}
