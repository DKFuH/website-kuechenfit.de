<?php
declare(strict_types=1);
http_response_code(404);
$ROOT = __DIR__;
$pageTitle = 'Seite nicht gefunden | KüchenFit';
$pageDescription = 'Die angeforderte Seite wurde nicht gefunden.';
$robots = 'noindex,follow';
require_once __DIR__ . '/partials/url.php';
$canonicalUrl = 'https://kuechenfit.de' . kk_request_path();
?>
<!doctype html>
<html lang="de">
<head>
  <?php require $ROOT . '/partials/head.php'; ?>
  <style>
    body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #211c18; color: #fff; font: 16px/1.6 "Inter", system-ui, sans-serif; text-align: center; }
    main { padding: 40px 20px; max-width: 520px; }
    h1 { font: 600 3rem/1 Georgia, serif; margin: 0 0 16px; }
    a { color: #eadcc8; }
  </style>
</head>
<body>
  <main>
    <h1>404</h1>
    <p>Diese Seite gibt es nicht (mehr).</p>
    <p><a href="/">Zurück zu KüchenFit</a></p>
  </main>
</body>
</html>
