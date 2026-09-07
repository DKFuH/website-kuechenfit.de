<?php
declare(strict_types=1);
$ROOT = dirname(__DIR__);
$pageTitle = 'Impressum | KüchenFit';
$pageDescription = 'Impressum und gesetzlich erforderliche Anbieterkennzeichnung von KüchenFit, ein Service von Klas Küchen.';
$canonicalUrl = 'https://kuechenfit.de/impressum/';
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
    .legal-document__frame { width: 100%; min-height: 1400px; border: 0; display: block; }
    .legal-document__fallback { margin-top: 14px; font-size: .9rem; }
    .legal-document__fallback a { color: #76533d; }
  </style>
</head>
<body class="page-legal">
  <header class="legal-header"><a href="/">← Zurück zu KüchenFit</a></header>
  <main id="main" class="legal-main">
    <div class="legal-document__header">
      <h1>Impressum von KüchenFit</h1>
    </div>
    <div class="legal-document__intro">
      <p>KüchenFit ist die Marke für Küchenrenovierung und -modernisierung von Daniel Klas, Tischlermeister aus Sohren – ein Service von Klas Küchen®. Betreiber der Website kuechenfit.de ist Daniel Klas.</p>
      <p>Die nachfolgend eingebundene Anbieterkennzeichnung enthält die vollständigen Pflichtangaben nach § 5 DDG: Name und Anschrift des Anbieters, Kontaktdaten sowie die weiteren gesetzlich vorgeschriebenen Angaben. Sie wird über einen geprüften Rechtstext-Dienst laufend aktuell gehalten.</p>
      <p>Wie personenbezogene Daten beim Besuch dieser Website verarbeitet werden und welche Rechte betroffene Personen haben, steht in der <a href="/datenschutz/">Datenschutzerklärung</a>. Bei Fragen zur Anbieterkennzeichnung erreichen Sie uns über die im Impressum genannten Kontaktdaten.</p>
    </div>
    <div class="legal-document__embed">
      <iframe
        src="https://itrk.legal/aqE.0.8cZ-de-iframe.html"
        title="Impressum – Daniel Klas"
        referrerpolicy="no-referrer"
        class="legal-document__frame"
      ></iframe>
    </div>
    <p class="legal-document__fallback">
      <a href="https://itrk.legal/aqE.0.8cZ-de.html" target="_blank" rel="noopener noreferrer">Impressum in einem neuen Fenster öffnen</a>
    </p>
  </main>
  <?php require $ROOT . '/partials/landing-mini-footer.php'; ?>
</body>
</html>
