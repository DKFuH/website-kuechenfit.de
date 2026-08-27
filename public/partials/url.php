<?php

/**
 * Pfad-/Asset-Helfer. Anders als im kuechen-klas.de-Hauptprojekt gibt es
 * hier nur eine einzige Domain -- die Multi-Domain-Allowlist
 * (kk_canonical_host) entfaellt deshalb ersatzlos.
 */

if (!function_exists('kk_detect_base_path')) {
  function kk_detect_base_path(): string
  {
    $scriptName = (string)($_SERVER['SCRIPT_NAME'] ?? '');
    $scriptName = str_replace('\\', '/', $scriptName);

    $pubIdx = strpos($scriptName, '/public/');
    if ($pubIdx !== false) {
      $base = substr($scriptName, 0, $pubIdx);
      return ($base === '' || $base === '/') ? '' : rtrim($base, '/');
    }

    return '';
  }
}

if (!function_exists('kk_base_path')) {
  function kk_base_path(string $path = '/'): string
  {
    $basePath = kk_detect_base_path();

    if ($path === '' || $path === '/') {
      return $basePath === '' ? '/' : $basePath . '/';
    }

    if ($path[0] !== '/') {
      $path = '/' . $path;
    }

    return ($basePath === '' ? '' : $basePath) . $path;
  }
}

if (!function_exists('kk_asset')) {
  function kk_asset(string $path): string
  {
    $relativePath = ltrim($path, '/');
    $query = '';
    if (str_contains($relativePath, '?')) {
      [$relativePath, $query] = explode('?', $relativePath, 2);
    }

    $url = kk_base_path('/assets/' . $relativePath);
    $file = dirname(__DIR__) . '/assets/' . $relativePath;
    $version = is_file($file) ? (string)filemtime($file) : '';
    $parameters = array_filter([
      $query,
      $version !== '' ? 'v=' . rawurlencode($version) : '',
    ]);

    return $url . ($parameters !== [] ? '?' . implode('&', $parameters) : '');
  }
}

if (!function_exists('kk_request_path')) {
  function kk_request_path(): string
  {
    $requestUri = (string)($_SERVER['REQUEST_URI'] ?? '/');
    $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';
    $basePath = kk_detect_base_path();

    if ($basePath !== '' && strpos($path, $basePath) === 0) {
      $path = substr($path, strlen($basePath)) ?: '/';
    }

    return $path === '' ? '/' : $path;
  }
}

if (!function_exists('kk_rewrite_root_relative_urls')) {
  function kk_rewrite_root_relative_urls(string $html): string
  {
    $basePath = kk_detect_base_path();
    if ($basePath === '') {
      return $html;
    }

    $prefix = rtrim($basePath, '/') . '/';
    $notAlreadyPrefixed = '(?!' . preg_quote(ltrim($prefix, '/'), '~') . ')';

    $html = preg_replace(
      '~\b(href|src|action|poster|data-api-url|data-assets-base)=([\'"])/(?!/)' . $notAlreadyPrefixed . '~i',
      '$1=$2' . $prefix,
      $html
    );

    $html = preg_replace(
      '~\burl\(([\'"]?)/(?!/)' . $notAlreadyPrefixed . '~i',
      'url($1' . $prefix,
      $html
    );

    $html = preg_replace(
      '~\b(fetch|open)\(([\'"])/(?!/)' . $notAlreadyPrefixed . '~i',
      '$1($2' . $prefix,
      $html
    );

    $html = preg_replace(
      '~\b(location(?:\.href)?\s*=\s*)([\'"])/(?!/)' . $notAlreadyPrefixed . '~i',
      '$1$2' . $prefix,
      $html
    );

    return $html;
  }
}

if (!function_exists('kk_start_output_buffering')) {
  function kk_start_output_buffering(): void
  {
    static $started = false;

    if ($started || kk_detect_base_path() === '') {
      return;
    }

    ob_start('kk_rewrite_root_relative_urls');
    $started = true;
  }
}
