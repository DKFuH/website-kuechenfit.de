<?php

declare(strict_types=1);

/**
 * ProvenExpert API cache.
 *
 * The public website only reads the local cache. Network access happens
 * exclusively through scripts/refresh-provenexpert.php (usually via cron).
 */

function kk_provenexpert_env(string $name, string $default = ''): string
{
    $value = getenv($name);
    if (is_string($value) && $value !== '') {
        return $value;
    }

    static $loaded = null;
    if ($loaded === null) {
        $loaded = [];
        foreach ([dirname(__DIR__) . '/.env', '/etc/klas-kuechen/.env'] as $file) {
            if (!is_file($file) || !is_readable($file)) {
                continue;
            }
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$key, $raw] = array_map('trim', explode('=', $line, 2));
                if ($key !== '') {
                    $loaded[$key] = trim($raw, "\"'");
                }
            }
        }
    }

    return isset($loaded[$name]) && is_string($loaded[$name]) ? $loaded[$name] : $default;
}

function kk_provenexpert_config(): array
{
    $root = dirname(__DIR__);
    $cachePath = kk_provenexpert_env('PROVENEXPERT_CACHE_PATH', $root . '/storage/provenexpert/rating.json');

    return [
        'api_id' => kk_provenexpert_env('PROVENEXPERT_API_ID'),
        'api_key' => kk_provenexpert_env('PROVENEXPERT_API_KEY'),
        'api_url' => 'https://www.provenexpert.com/api_rating_v3.json',
        'cache_path' => $cachePath,
        'max_stale' => max(3600, (int) kk_provenexpert_env('PROVENEXPERT_MAX_STALE_SECONDS', '604800')),
    ];
}

function kk_provenexpert_is_configured(?array $config = null): bool
{
    $config ??= kk_provenexpert_config();
    return ($config['api_id'] ?? '') !== '' && ($config['api_key'] ?? '') !== '';
}

function kk_provenexpert_validate_payload(string $json): ?array
{
    if ($json === '' || strlen($json) > 131072) {
        return null;
    }

    try {
        $data = json_decode($json, true, 32, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        return null;
    }

    if (!is_array($data) || ($data['status'] ?? null) !== 'success') {
        return null;
    }

    $markup = $data['aggregateRating'] ?? null;
    if (!is_string($markup) || trim($markup) === '' || strlen($markup) > 100000) {
        return null;
    }

    // Zahlenwerte zuerst aus einem bereits validierten Cache uebernehmen
    // (dort ist das ld+json unten schon entfernt), sonst frisch aus der
    // API-Antwort lesen, bevor das Script-Tag entfernt wird.
    $ratingValue = is_numeric($data['rating_value'] ?? null) ? round((float) $data['rating_value'], 2) : null;
    $reviewCount = is_numeric($data['review_count'] ?? null) ? max(0, (int) $data['review_count']) : null;

    if (preg_match(
        '#<script[^>]+type=["\']application/ld\+json["\'][^>]*>(.*?)</script>#si',
        $markup,
        $scriptMatch
    )) {
        $ld = json_decode($scriptMatch[1], true);
        $aggregate = is_array($ld) ? ($ld['aggregateRating'] ?? null) : null;
        if (is_array($aggregate)) {
            if (is_numeric($aggregate['ratingValue'] ?? null)) {
                $ratingValue = round((float) $aggregate['ratingValue'], 2);
            }
            if (is_numeric($aggregate['reviewCount'] ?? null)) {
                $reviewCount = max(0, (int) $aggregate['reviewCount']);
            }
        }
    }

    // ProvenExpert liefert im Widget-HTML ein eigenes ld+json mit @type
    // "Product" für die Bewertungsperson mit. Google erlaubt kein
    // selbst eingebundenes Bewertungs-Markup zu Person/LocalBusiness und
    // stuft den "Product"-Umweg als Structured-Data-Missbrauch ein. Nur die
    // sichtbare Sterne-Darstellung behalten, das Script-Tag entfernen.
    $markup = trim((string) preg_replace(
        '#<script[^>]+type=["\']application/ld\+json["\'][^>]*>.*?</script>#si',
        '',
        $markup
    ));
    if ($markup === '') {
        return null;
    }

    return [
        'status' => 'success',
        'aggregateRating' => $markup,
        'fetched_at' => gmdate(DATE_ATOM),
        'rating_value' => $ratingValue,
        'review_count' => $reviewCount,
    ];
}

function kk_provenexpert_fetch(?array $config = null, ?callable $transport = null): ?array
{
    $config ??= kk_provenexpert_config();
    if (!kk_provenexpert_is_configured($config)) {
        return null;
    }

    if ($transport !== null) {
        $response = $transport($config);
        return is_string($response) ? kk_provenexpert_validate_payload($response) : null;
    }

    if (!function_exists('curl_init')) {
        return null;
    }

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $config['api_url'] . '?v=1.8',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT => 8,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => $config['api_id'] . ':' . $config['api_key'],
        CURLOPT_USERAGENT => 'Klas-ProvenExpert-Cache/1.0',
        CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
    ]);

    $response = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if (!is_string($response) || $status < 200 || $status >= 300) {
        return null;
    }

    return kk_provenexpert_validate_payload($response);
}

function kk_provenexpert_write_cache(array $data, ?array $config = null): bool
{
    $config ??= kk_provenexpert_config();
    $path = (string) $config['cache_path'];
    $dir = dirname($path);
    if ((!is_dir($dir) && !mkdir($dir, 0770, true) && !is_dir($dir)) || !is_writable($dir)) {
        return false;
    }

    $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    $lock = fopen($path . '.lock', 'c');
    if ($lock === false || !flock($lock, LOCK_EX)) {
        if (is_resource($lock)) {
            fclose($lock);
        }
        return false;
    }

    $temp = $path . '.tmp-' . bin2hex(random_bytes(8));
    $written = file_put_contents($temp, $json, LOCK_EX);
    $ok = $written === strlen($json);
    if ($ok && is_file($path) && DIRECTORY_SEPARATOR === '\\') {
        $backup = $path . '.previous';
        @unlink($backup);
        $ok = rename($path, $backup) && rename($temp, $path);
        if (!$ok && is_file($backup) && !is_file($path)) {
            @rename($backup, $path);
        }
        @unlink($backup);
    } elseif ($ok) {
        $ok = rename($temp, $path);
    }

    if (is_file($temp)) {
        @unlink($temp);
    }
    flock($lock, LOCK_UN);
    fclose($lock);

    return $ok;
}

function kk_provenexpert_refresh(?array $config = null, ?callable $transport = null): bool
{
    $config ??= kk_provenexpert_config();
    $data = kk_provenexpert_fetch($config, $transport);
    return $data !== null && kk_provenexpert_write_cache($data, $config);
}

function kk_provenexpert_cached(?array $config = null): ?array
{
    $config ??= kk_provenexpert_config();
    $path = (string) $config['cache_path'];
    if (!is_file($path) || !is_readable($path) || filesize($path) > 131072) {
        return null;
    }
    if (time() - (int) filemtime($path) > (int) $config['max_stale']) {
        return null;
    }

    $json = file_get_contents($path);
    return is_string($json) ? kk_provenexpert_validate_payload($json) : null;
}

function kk_provenexpert_rating(): string
{
    $data = kk_provenexpert_cached();
    return $data['aggregateRating'] ?? '';
}

function kk_provenexpert_summary(?array $config = null): array
{
    $data = kk_provenexpert_cached($config);
    return [
        'rating_value' => $data['rating_value'] ?? null,
        'review_count' => $data['review_count'] ?? null,
    ];
}

// Oeffentlich dokumentierte ProvenExpert-Notenskala, nicht selbst erfunden:
// https://www.provenexpert.com (Bewertungsskala 1,0-5,0).
function kk_provenexpert_rating_label(float $value): string
{
    return match (true) {
        $value >= 4.5 => 'Sehr gut',
        $value >= 3.5 => 'Gut',
        $value >= 2.5 => 'Befriedigend',
        $value >= 1.5 => 'Ausreichend',
        default => 'Mangelhaft',
    };
}

function kk_provenexpert_trust_line(?array $config = null): string
{
    $summary = kk_provenexpert_summary($config);
    $value = $summary['rating_value'];
    $count = $summary['review_count'];
    if (!is_numeric($value) || !is_int($count) || $count <= 0) {
        return '';
    }

    return sprintf(
        '%s★ %s (%d Bewertungen)',
        number_format((float) $value, 2, ',', '.'),
        kk_provenexpert_rating_label((float) $value),
        $count
    );
}
