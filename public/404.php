<?php
declare(strict_types=1);

http_response_code(404);

$ROOT = __DIR__;
$pageTitle = 'Seite nicht gefunden | KüchenFit';
$pageDescription = 'Die angeforderte Seite wurde nicht gefunden.';
$robots = 'noindex,follow';
$skipSiteCss = true;

/*
 * Eine Fehlerseite sollte weder auf die fehlerhafte URL noch pauschal
 * auf die Startseite als Canonical verweisen.
 */
$canonicalUrl = '';
?>
<!doctype html>
<html lang="de">
<head>
  <?php require $ROOT . '/partials/head.php'; ?>

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #211c18;
      color: #fff;
      font: 16px/1.6 "Inter", system-ui, sans-serif;
    }

    main {
      width: 100%;
      max-width: 680px;
      padding: 48px 24px;
      text-align: center;
    }

    .error-code {
      margin: 0 0 8px;
      color: #cdb99f;
      font-size: .85rem;
      font-weight: 700;
      letter-spacing: .14em;
      text-transform: uppercase;
    }

    h1 {
      margin: 0 0 20px;
      font: 600 clamp(2.2rem, 8vw, 4rem)/1.08 Georgia, serif;
    }

    .lead {
      max-width: 560px;
      margin: 0 auto 32px;
      color: #e6ded5;
      font-size: 1.08rem;
    }

    .actions {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 12px;
      margin-bottom: 36px;
    }

    .button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 48px;
      padding: 11px 20px;
      border: 1px solid #eadcc8;
      border-radius: 4px;
      color: #211c18;
      background: #eadcc8;
      font-weight: 700;
      text-decoration: none;
    }

    .button--secondary {
      color: #eadcc8;
      background: transparent;
    }

    .links {
      margin: 0;
      color: #bfb4aa;
      font-size: .92rem;
    }

    .links a {
      color: #eadcc8;
      text-underline-offset: 3px;
    }

    .brand {
      margin-top: 40px;
      color: #a99d92;
      font-size: .85rem;
    }
  </style>
</head>

<body>
  <main>
    <p class="error-code">Fehler 404</p>

    <h1>Diese Seite wurde nicht gefunden.</h1>

    <p class="lead">
      Vielleicht wurde die Adresse geändert oder der Link war nicht vollständig.
      Über die Startseite und den Modernisierungs-Check finden Sie schnell zum
      passenden KüchenFit-Angebot.
    </p>

    <div class="actions">
      <a class="button" href="/">
        Zur KüchenFit-Startseite
      </a>

      <a class="button button--secondary" href="/#modernisierungscheck">
        Modernisierungs-Check starten
      </a>
    </div>

    <p class="links">
      Direkt zu:
      <a href="/kuechenrenovierung/">Küchenrenovierung</a>
      &middot;
      <a href="/kuechenfronten-austauschen/">Frontentausch</a>
      &middot;
      <a href="/arbeitsplatte-austauschen/">Arbeitsplatte</a>
      &middot;
      <a href="/kuechenumbau/">Küchenumbau</a>
    </p>

    <p class="brand">
      KüchenFit – ein Service von Klas Küchen®
    </p>
  </main>
</body>
</html>
