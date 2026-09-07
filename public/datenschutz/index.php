<?php
declare(strict_types=1);
$ROOT = dirname(__DIR__);
$pageTitle = 'Datenschutz | KüchenFit';
$pageDescription = 'Datenschutzerklärung von KüchenFit mit Informationen zur Verarbeitung personenbezogener Daten auf dieser Website.';
$canonicalUrl = 'https://kuechenfit.de/datenschutz/';
$robots = 'index,follow';
$skipSiteCss = true; // Seite ist komplett eigenstaendig gestylt (Inline-<style> unten).
?>
<!doctype html>
<html lang="de">
<head>
  <?php require $ROOT . '/partials/head.php'; ?>
  <style>
    body.page-legal { margin: 0; background: #f4f0e9; color: #25211d; font: 16px/1.6 "Inter", system-ui, -apple-system, "Segoe UI", sans-serif; }
    .legal-header { padding: 22px max(18px, calc((100vw - 1120px) / 2)); }
    .legal-header a { color: #513827; font-weight: 700; text-decoration: none; }
    .legal-main { width: min(1120px, calc(100% - 36px)); margin: 0 auto 60px; }
    .legal-document__header h1 { font: 600 clamp(2rem, 4.5vw, 3rem)/1.1 Georgia, serif; }
    .legal-document__intro { max-width: 68ch; margin: 12px 0 24px; color: #4a4038; }
    .legal-document__intro p { margin: 0 0 .9em; }
    .legal-document__intro a { color: #76533d; }
    .legal-document__embed { border: 1px solid #d9cfc2; border-radius: 18px; overflow: hidden; background: #fffdf9; }
    .legal-document__frame { width: 100%; min-height: 1800px; border: 0; display: block; }
    .legal-document__fallback { margin-top: 14px; font-size: .9rem; }
    .legal-document__fallback a { color: #76533d; }
  </style>
</head>
<body class="page-legal">
  <header class="legal-header"><a href="/">← Zurück zu KüchenFit</a></header>
  <main id="main" class="legal-main">
    <div class="legal-document__header">
      <h1>Datenschutzerklärung von KüchenFit</h1>
    </div>
    <div class="legal-document__intro">
      <p>Diese Datenschutzerklärung informiert darüber, welche personenbezogenen Daten beim Besuch der Website kuechenfit.de verarbeitet werden, zu welchem Zweck und auf welcher Rechtsgrundlage. Verantwortlich im Sinne der DSGVO ist Daniel Klas (Klas Küchen®), Sohren.</p>
      <p>Beim Aufruf der Seiten werden technisch notwendige Server-Daten verarbeitet. Wenn Sie das Anfrageformular nutzen, verarbeiten wir die von Ihnen angegebenen Kontakt- und Projektdaten zur Bearbeitung Ihrer Anfrage. Analyse- und Marketing-Dienste werden ausschließlich nach Ihrer Einwilligung geladen; die Einwilligung können Sie jederzeit über die Datenschutz-Einstellungen im Fußbereich widerrufen.</p>
      <p>Betroffene Personen haben insbesondere das Recht auf Auskunft, Berichtigung, Löschung, Einschränkung der Verarbeitung, Datenübertragbarkeit und Widerspruch sowie das Recht auf Beschwerde bei einer Aufsichtsbehörde. Der vollständige Text mit allen Einzelheiten ist nachfolgend eingebunden; die Anbieterkennzeichnung steht im <a href="/impressum/">Impressum</a>.</p>
    </div>
    <div class="legal-document__embed">
      <iframe
        src="https://itrk.legal/aqE.8V.8cZ-iframe.html"
        title="Datenschutzerklärung – Daniel Klas"
        referrerpolicy="no-referrer"
        class="legal-document__frame"
      ></iframe>
    </div>
    <p class="legal-document__fallback">
      <a href="https://itrk.legal/aqE.8V.8cZ.html" target="_blank" rel="noopener noreferrer">Datenschutzerklärung in einem neuen Fenster öffnen</a>
    </p>
  </main>
  <?php require $ROOT . '/partials/landing-mini-footer.php'; ?>
</body>
</html>
