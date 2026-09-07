<?php
declare(strict_types=1);

function kk_consent_load_env(): void
{
    static $loaded = false;
    if ($loaded) return;
    $loaded = true;
    $paths = [dirname(__DIR__) . '/.env', '/etc/klas-kuechen/.env'];
    foreach ($paths as $path) {
        if (!is_file($path) || !is_readable($path)) continue;
        $values = parse_ini_file($path, false, INI_SCANNER_RAW);
        if (!is_array($values)) continue;
        foreach ($values as $key => $value) {
            if (!preg_match('/^[A-Z][A-Z0-9_]*$/', (string)$key) || getenv((string)$key) !== false) continue;
            putenv((string)$key . '=' . (string)$value);
            $_ENV[(string)$key] = (string)$value;
        }
        break;
    }
}

function kk_consent_config(): array
{
    static $config;
    if (is_array($config)) return $config;
    kk_consent_load_env();
    $loaded = require dirname(__DIR__) . '/config/consent.php';
    if (!is_array($loaded)) throw new RuntimeException('Ungültige Consent-Konfiguration.');
    $errors = kk_consent_validate_config($loaded);
    if ($errors !== []) throw new RuntimeException('Ungültige Consent-Konfiguration: ' . implode(' ', $errors));
    return $config = $loaded;
}

function kk_consent_validate_config(array $config): array
{
    $errors = [];
    if (($config['library']['version'] ?? '') !== '3.1.0') $errors[] = 'CookieConsent-Version muss 3.1.0 sein.';
    if (!is_int($config['revision'] ?? null) || $config['revision'] < 1) $errors[] = 'Revision fehlt.';
    if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}\.[0-9]+$/', (string)($config['material_version'] ?? ''))) $errors[] = 'Materialversion ist ungültig.';
    if (!preg_match('/^[A-Za-z0-9_-]+$/', (string)($config['mtm']['container'] ?? ''))) $errors[] = 'MTM-Container ist ungültig.';
    $categories = $config['categories'] ?? [];
    if (!is_array($categories) || !isset($categories['necessary']) || empty($categories['necessary']['read_only'])) $errors[] = 'Notwendige Kategorie fehlt.';
    $providers = $config['providers'] ?? [];
    if (!is_array($providers)) $errors[] = 'Providerliste ist ungültig.';
    foreach ((array)$providers as $id => $provider) {
        if (!preg_match('/^[a-z][a-z0-9_]{1,31}$/', (string)$id)) $errors[] = 'Provider-ID ist ungültig.';
        if (!is_array($provider) || !isset($categories[$provider['category'] ?? ''])) $errors[] = 'Provider-Kategorie ist ungültig.';
        if (!in_array($provider['managed_by'] ?? '', ['mtm', 'component'], true)) $errors[] = 'Provider-Verwaltung ist ungültig.';
        foreach (['name', 'purpose', 'data', 'recipient', 'privacy_url', 'third_country', 'storage', 'retention'] as $field) {
            if (trim((string)($provider[$field] ?? '')) === '') $errors[] = 'Providerfeld fehlt: ' . $field . '.';
        }
        $privacy = (string)($provider['privacy_url'] ?? '');
        if (!str_starts_with($privacy, '/') || str_starts_with($privacy, '//')) $errors[] = 'Datenschutzlink muss intern sein.';
    }
    return array_values(array_unique($errors));
}

function kk_consent_material_payload(array $config): array
{
    return [
        'revision' => $config['revision'],
        'material_version' => $config['material_version'],
        'retention_days' => $config['retention_days'],
        'cookie' => $config['cookie'],
        'categories' => $config['categories'],
        'providers' => $config['providers'],
    ];
}

function kk_consent_sort_recursive(array &$value): void
{
    if (!array_is_list($value)) ksort($value);
    foreach ($value as &$item) if (is_array($item)) kk_consent_sort_recursive($item);
}

function kk_consent_material_hash(?array $config = null): string
{
    $payload = kk_consent_material_payload($config ?? kk_consent_config());
    kk_consent_sort_recursive($payload);
    return hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
}

function kk_consent_paths(): array
{
    $storage = dirname(__DIR__) . '/storage';
    return [
        'db' => getenv('CONSENT_DB_PATH') ?: $storage . '/consent_receipts.db',
        'cleanup_status' => $storage . '/admin/consent-cleanup.json',
        'material_hash' => dirname(__DIR__) . '/config/consent-material.sha256',
        'vendor_js' => dirname(__DIR__) . '/public/assets/vendor/cookieconsent/cookieconsent.umd.js',
        'vendor_css' => dirname(__DIR__) . '/public/assets/vendor/cookieconsent/cookieconsent.css',
    ];
}

function kk_consent_mtm_container(?array $config = null): string
{
    kk_consent_load_env();
    $environment = trim((string)(getenv('MATOMO_TAG_MANAGER_CONTAINER') ?: ''));
    $container = $environment !== ''
        ? $environment
        : trim((string)(($config ?? kk_consent_config())['mtm']['container'] ?? ''));
    return preg_match('/^[A-Za-z0-9_-]+$/', $container) ? $container : '';
}

function kk_consent_public_config(): array
{
    $config = kk_consent_config();
    $providers = [];
    foreach ($config['providers'] as $id => $provider) {
        $providers[$id] = [
            'id' => $id,
            'category' => $provider['category'],
            'managedBy' => $provider['managed_by'],
            'name' => $provider['name'],
            'purpose' => $provider['purpose'],
            'data' => $provider['data'],
            'recipient' => $provider['recipient'],
            'privacyUrl' => $provider['privacy_url'],
            'thirdCountry' => $provider['third_country'],
            'storage' => $provider['storage'],
            'retention' => $provider['retention'],
            'cookies' => $provider['cookies'] ?? [],
        ];
    }
    return [
        'revision' => $config['revision'],
        'materialVersion' => $config['material_version'],
        'materialHash' => kk_consent_material_hash($config),
        'cookie' => $config['cookie'],
        'categories' => $config['categories'],
        'providers' => $providers,
        'receiptEndpoint' => '/api/consent-receipt.php',
        'mtm' => [
            'container' => kk_consent_mtm_container($config),
            'baseUrl' => 'https://analytics.tischlermeister-klas.de/',
        ],
    ];
}

function kk_consent_db(bool $create = true): PDO
{
    if (!extension_loaded('pdo_sqlite')) throw new RuntimeException('SQLite ist nicht verfügbar.');
    $path = kk_consent_paths()['db'];
    if (!$create && !is_file($path)) throw new RuntimeException('Consent-Datenbank fehlt.');
    $dir = dirname($path);
    if ($create && !is_dir($dir) && !mkdir($dir, 0770, true) && !is_dir($dir)) throw new RuntimeException('Consent-Speicher ist nicht verfügbar.');
    $pdo = new PDO('sqlite:' . $path, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec('PRAGMA busy_timeout = 3000');
    if ($create) {
        $pdo->exec('CREATE TABLE IF NOT EXISTS consent_receipts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            consent_id TEXT NOT NULL,
            revision INTEGER NOT NULL,
            material_hash TEXT NOT NULL,
            action TEXT NOT NULL CHECK(action IN (\'created\', \'changed\')),
            accepted_categories TEXT NOT NULL,
            rejected_categories TEXT NOT NULL,
            accepted_services TEXT NOT NULL,
            rejected_services TEXT NOT NULL,
            created_at_utc TEXT NOT NULL
        )');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_consent_receipts_consent_id ON consent_receipts(consent_id)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_consent_receipts_created_at ON consent_receipts(created_at_utc)');
    } else {
        $pdo->exec('PRAGMA query_only = ON');
    }
    return $pdo;
}

function kk_consent_status(): array
{
    $config = kk_consent_config();
    $paths = kk_consent_paths();
    $expected = is_file($paths['material_hash']) ? trim((string)file_get_contents($paths['material_hash'])) : '';
    $actual = kk_consent_material_hash($config);
    $status = [
        'config_errors' => kk_consent_validate_config($config),
        'material_hash_ok' => $expected !== '' && hash_equals($expected, $actual),
        'db_exists' => is_file($paths['db']),
        'db_readable' => false,
        'receipt_count' => 0,
        'last_receipt_utc' => '',
        'cleanup' => null,
    ];
    if ($status['db_exists']) {
        try {
            $pdo = kk_consent_db(false);
            $row = $pdo->query('SELECT COUNT(*) AS total, MAX(created_at_utc) AS latest FROM consent_receipts')->fetch(PDO::FETCH_ASSOC) ?: [];
            $status['db_readable'] = true;
            $status['receipt_count'] = (int)($row['total'] ?? 0);
            $status['last_receipt_utc'] = (string)($row['latest'] ?? '');
        } catch (Throwable) {
            $status['db_readable'] = false;
        }
    }
    if (is_file($paths['cleanup_status'])) {
        $decoded = json_decode((string)file_get_contents($paths['cleanup_status']), true);
        if (is_array($decoded)) $status['cleanup'] = $decoded;
    }
    return $status;
}
