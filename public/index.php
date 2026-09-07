<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$ROOT = __DIR__;
require_once dirname($ROOT) . '/app/provenexpert.php';
$peTrustLine = kk_provenexpert_trust_line();
$pageTitle = 'Küche modernisieren im Hunsrück | KüchenFit Sohren';
$pageDescription = 'Küche im Hunsrück modernisieren: KüchenFit prüft erst den Bestand und sagt, ob Frontentausch, neue Arbeitsplatte, Umbau oder Neuplanung passt.';
$canonicalUrl = 'https://kuechenfit.de/';
$robots = 'index,follow';
$skipSiteCss = true; // Seite ist komplett eigenständig gestylt (modernisierung-lp.css), braucht kein Haupt-Stylesheet.
// Startseite = Überblick & Entscheidungseinstieg. Die Detail-Suchintention
// "Küche renovieren lassen" bedient bewusst /kuechenrenovierung/.
$faqItems = [
    ['question' => 'Renovieren, umbauen oder neu planen – was passt zu meiner Küche?', 'answer' => 'Das hängt von Substanz, Aufteilung und dem gewünschten Umfang ab. Genau diese Abwägung nimmt KüchenFit in der Bestandsprüfung vor, bevor Bauteile bestellt werden.'],
    ['question' => 'Was leistet der Modernisierungs-Check?', 'answer' => 'Der kurze Check ordnet Ihr Anliegen anhand weniger Fragen ein und zeigt eine erste Richtung an: gezielte Renovierung, Küchenumbau oder Vergleich mit einer Neuplanung. Die fachliche Prüfung ersetzt er nicht.'],
    ['question' => 'Deckt KüchenFit auch einzelne Maßnahmen ab?', 'answer' => 'Ja. Von einzelnen neuen Fronten oder einer neuen Arbeitsplatte bis zum abgestimmten Küchenumbau. Welche Maßnahme sinnvoll ist, klärt die Bestandsaufnahme.'],
    ['question' => 'Muss ich schon genaue Maße haben?', 'answer' => 'Nein. Fotos und ungefähre Angaben reichen für die erste Einordnung. Für ein verbindliches Angebot werden die benötigten Maße anschließend fachgerecht geprüft.'],
];
?>
<!doctype html>
<html lang="de">
<head>
  <?php require $ROOT . '/partials/head.php'; ?>
  <meta name="service-category" content="kuechenmodernisierung">
  <link rel="stylesheet" href="/assets/css/modernisierung-lp.css?v=<?= rawurlencode((string)filemtime($ROOT . '/assets/css/modernisierung-lp.css')) ?>">
  <?php
  require dirname($ROOT) . '/app/seo-schema.php';
  kk_render_service_schema('Küchenmodernisierung', $pageDescription, $canonicalUrl, 'Küchenmodernisierung');
  kk_render_faq_schema($faqItems);
  ?>
</head>
<body class="page-landing page-modernisierung page-kuechenfit">
  <a class="ms-brand" href="/" aria-label="KüchenFit Startseite" data-track="kuechenfit_brand"><strong>KüchenFit</strong><span>ein Service von Klas Küchen®</span></a>
  <header class="ms-hero">
    <picture class="ms-hero__media">
      <img src="/assets/img/kuechensanierung.webp" alt="Modernisierte Küche mit neuen Fronten und Arbeitsplatte" width="1200" height="782" fetchpriority="high" decoding="async">
    </picture>
    <div class="ms-wrap ms-hero__inner">
      <p class="ms-eyebrow">Küchenmodernisierung im Hunsrück</p>
      <h1>Küche im Hunsrück modernisieren &ndash; erst prüfen, dann erneuern</h1>
      <p class="ms-lead">Wenn Sie Ihre Küche modernisieren möchten, prüft KüchenFit zuerst Korpusse, Aufteilung und Anschlüsse. Danach wissen Sie, ob Frontentausch, neue Arbeitsplatte, Umbau oder Neuplanung sinnvoller ist.</p>
      <p class="ms-trust">Persönliche Prüfung durch Daniel Klas · Tischlermeister seit 2006<?php if ($peTrustLine !== ''): ?> · <a href="https://kuechen-klas.de/kundenstimmen/"><?= htmlspecialchars($peTrustLine, ENT_QUOTES, 'UTF-8') ?></a><?php endif; ?></p>
      <a class="ms-button ms-button--light" href="#modernisierungscheck" data-track="kuechenfit_hero_check">Küche fachlich prüfen lassen</a>
    </div>
  </header>

  <main>
    <section class="ms-section" id="modernisierungscheck">
      <div class="ms-wrap">
        <p class="ms-eyebrow">Kurzer Modernisierungs-Check</p>
        <h2>Was ist bei Ihrer Küche der richtige Weg?</h2>
        <p class="ms-copy">Vier kurze Fragen für eine erste Einordnung: Küche gezielt modernisieren, umbauen oder lieber mit einer Neuplanung vergleichen.</p>
        <div class="ms-quiz" data-fit-quiz>
          <div class="ms-quiz-progress"><div class="ms-quiz-progress__bar" data-quiz-progress></div></div>
          <div class="ms-quiz-step" data-quiz-step="1">
            <h3>Was stört Sie an Ihrer Küche am meisten?</h3>
            <div class="ms-quiz-options" data-quiz-name="anliegen">
              <button type="button" class="ms-quiz-option" data-value="Die Optik passt nicht mehr">Die Optik passt nicht mehr</button>
              <button type="button" class="ms-quiz-option" data-value="Arbeitsplatte oder Spülbereich sind abgenutzt">Arbeitsplatte oder Spülbereich sind abgenutzt</button>
              <button type="button" class="ms-quiz-option" data-value="Stauraum und Bedienung funktionieren nicht gut">Stauraum und Bedienung funktionieren nicht gut</button>
              <button type="button" class="ms-quiz-option" data-value="Licht oder Geräte sollen erneuert werden">Licht oder Geräte sollen erneuert werden</button>
              <button type="button" class="ms-quiz-option" data-value="Mehrere Bereiche müssen verändert werden">Mehrere Bereiche müssen verändert werden</button>
              <button type="button" class="ms-quiz-option" data-value="Ich weiß nicht, ob eine Renovierung noch sinnvoll ist">Ich weiß nicht, ob eine Renovierung noch sinnvoll ist</button>
            </div>
          </div>
          <div class="ms-quiz-step" data-quiz-step="2" hidden>
            <h3>Wie ist der Zustand der Küche?</h3>
            <div class="ms-quiz-options" data-quiz-name="zustand">
              <button type="button" class="ms-quiz-option" data-value="Korpusse und Fronten sind grundsätzlich intakt">Korpusse und Fronten sind grundsätzlich intakt</button>
              <button type="button" class="ms-quiz-option" data-value="Es gibt kleinere Schäden">Es gibt kleinere Schäden</button>
              <button type="button" class="ms-quiz-option" data-value="Beschichtungen oder Kanten lösen sich">Beschichtungen oder Kanten lösen sich</button>
              <button type="button" class="ms-quiz-option" data-value="Mehrere Bauteile sind deutlich beschädigt">Mehrere Bauteile sind deutlich beschädigt</button>
              <button type="button" class="ms-quiz-option" data-value="Kann ich nicht beurteilen">Kann ich nicht beurteilen</button>
            </div>
          </div>
          <div class="ms-quiz-step" data-quiz-step="3" hidden>
            <h3>Passt die heutige Aufteilung noch zu Ihrem Alltag?</h3>
            <div class="ms-quiz-options" data-quiz-name="aufteilung">
              <button type="button" class="ms-quiz-option" data-value="Ja">Ja</button>
              <button type="button" class="ms-quiz-option" data-value="Teilweise">Teilweise</button>
              <button type="button" class="ms-quiz-option" data-value="Nein">Nein</button>
              <button type="button" class="ms-quiz-option" data-value="Noch unklar">Noch unklar</button>
            </div>
          </div>
          <div class="ms-quiz-step" data-quiz-step="4" hidden>
            <h3>Sollen Schränke, Geräte oder Anschlüsse neu angeordnet werden?</h3>
            <div class="ms-quiz-options" data-quiz-name="umbauumfang">
              <button type="button" class="ms-quiz-option" data-value="Nein, die Aufteilung soll bleiben">Nein, die Aufteilung soll bleiben</button>
              <button type="button" class="ms-quiz-option" data-value="Einzelne Bereiche sollen verändert werden">Einzelne Bereiche sollen verändert werden</button>
              <button type="button" class="ms-quiz-option" data-value="Die Küche soll deutlich umgebaut werden">Die Küche soll deutlich umgebaut werden</button>
              <button type="button" class="ms-quiz-option" data-value="Kann ich noch nicht beurteilen">Kann ich noch nicht beurteilen</button>
            </div>
          </div>
          <div class="ms-quiz-result" data-quiz-result hidden>
            <p class="ms-quiz-result__badge" data-quiz-badge></p>
            <p class="ms-quiz-result__text" data-quiz-text></p>
            <div class="ms-quiz-result__actions">
              <a class="ms-button" href="#anfrage" data-track="kuechenfit_quiz_request">Ergebnis fachlich prüfen lassen</a>
              <a class="ms-button ms-button--secondary" href="#leistungen" data-track="kuechenfit_quiz_services">Alle Wege ansehen</a>
            </div>
          </div>
          <button type="button" class="ms-quiz-back" data-quiz-back hidden>Zurück</button>
        </div>
      </div>
    </section>

    <section class="ms-section" id="leistungen">
      <div class="ms-wrap">
        <p class="ms-kicker">Erhalten und verbessern</p>
        <h2>Vier Wege, eine bestehende Küche zu verbessern</h2>
        <p class="ms-copy">Welcher Weg zu Ihrer Küche passt, hängt von Substanz, Aufteilung und dem gewünschten Umfang ab. Einen Gesamtüberblick gibt die Seite <a href="/kuechenrenovierung/">Küchenrenovierung</a>.</p>
        <div class="ms-grid ms-options">
          <article class="ms-card ms-option"><h3>Küche gezielt renovieren</h3><p>Fronten, Arbeitsplatte, Geräte oder Ausstattung erneuern, während geeignete Bestandteile erhalten bleiben.</p><a class="ms-card__link" href="/kuechenrenovierung/">Möglichkeiten der Küchenrenovierung</a></article>
          <article class="ms-card ms-option"><h3>Küchenfronten austauschen</h3><p>Neue Türen und Schubladenfronten, wenn Korpusse und Aufteilung weiterhin eine gute Grundlage bieten.</p><a class="ms-card__link" href="/kuechenfronten-austauschen/">Frontentausch prüfen</a></article>
          <article class="ms-card ms-option"><h3>Arbeitsplatte erneuern</h3><p>Eine neue Arbeitsplatte mit passenden Ausschnitten, Anschlüssen und Übergängen zum vorhandenen Bestand.</p><a class="ms-card__link" href="/arbeitsplatte-austauschen/">Arbeitsplattenwechsel prüfen</a></article>
          <article class="ms-card ms-option"><h3>Küche umbauen</h3><p>Schränke ergänzen, Arbeitsfläche vergrößern oder Geräte und Funktionen neu anordnen.</p><a class="ms-card__link" href="/kuechenumbau/">Küchenumbau im Bestand</a></article>
        </div>
        <p class="ms-copy">Sie sind nicht sicher, ob sich der vorhandene Bestand noch lohnt? <a href="/renovieren-oder-neue-kueche/">Renovierung, Umbau und Neuplanung vergleichen</a>.</p>
      </div>
    </section>

    <?php /* TODO Vorher/Nachher-Abschnitt: erst neu aufbauen, wenn ein echtes
             Klas-Projekt mit Freigabe vorliegt (gleicher Blickwinkel, was blieb,
             was erneuert wurde, welches Problem gelöst wurde – kein Stock-/KI-Bild).
             Bis dahin bewusst keine Platzhalter im Markup. */ ?>

    <section class="ms-section ms-section--soft">
      <div class="ms-wrap ms-grid ms-grid--2">
        <div><p class="ms-kicker">Mehr als eine neue Oberfläche</p><h2>Optik und Funktion gemeinsam betrachten.</h2><p class="ms-copy">Eine neue Arbeitsplatte verändert das Gesamtbild. Gut erreichbare Auszüge schaffen Ordnung. Eine passend geplante Spüle oder Beleuchtung erleichtert tägliche Handgriffe. Deshalb betrachten wir nicht nur, was alt aussieht, sondern auch, was Sie bei der Nutzung stört.</p></div>
        <div><p class="ms-kicker">Klare Entscheidung</p><h2>Erst den Bestand prüfen, dann den Umfang festlegen.</h2><div class="ms-copy"><p>Eine Renovierung lohnt sich nicht automatisch. Trotzdem ist es selten sinnvoll, eine funktionierende Küche vorschnell komplett zu ersetzen. Beschädigte Korpusse, ungünstige Wege oder viele notwendige Einzelmaßnahmen können dafür sprechen, neu zu planen.</p><p>Genau deshalb beginnt KüchenFit mit einer Bestandsaufnahme und nicht mit einem pauschalen Sparversprechen. Mehr dazu: <a href="/kuechenrenovierung-kosten/">Was eine Küchenrenovierung kostet</a>.</p></div></div>
      </div>
    </section>

    <section class="ms-section">
      <div class="ms-wrap ms-grid ms-grid--2">
        <div><p class="ms-kicker">KüchenFit von Klas Küchen®</p><h2>Fachliche Prüfung statt pauschaler Renovierungsempfehlung</h2><p class="ms-copy">Ich bin Daniel Klas, Tischlermeister und auf Küchen spezialisiert. Ich prüfe die vorhandene Küche, ordne die gewünschten Veränderungen ein und sage auch offen, wenn ein anderer Renovierungsumfang oder eine Neuplanung sinnvoller wäre.</p><p class="ms-copy">KüchenFit ist ein Service von <a href="https://kuechen-klas.de/">Klas Küchen®</a>.</p></div>
        <div class="ms-process">
          <article><span>1</span><div><h3>Küche zeigen</h3><p>Fotos, ungefähre Maße und Ihre Beschreibung ermöglichen eine erste Einordnung.</p></div></article>
          <article><span>2</span><div><h3>Bestand prüfen</h3><p>Korpusse, Aufteilung, Anschlüsse und betroffene Bauteile bestimmen die möglichen Wege.</p></div></article>
          <article><span>3</span><div><h3>Nächsten Schritt festlegen</h3><p>Sie erhalten eine fachliche Empfehlung. Für die gewählte Ausführung wird anschließend der konkrete Umfang geplant und kalkuliert.</p></div></article>
        </div>
      </div>
    </section>

    <section class="ms-section ms-section--dark" id="anfrage">
      <div class="ms-wrap ms-grid ms-grid--2">
        <div>
          <p class="ms-eyebrow">Ihre Anfrage</p>
          <h2>Was soll an Ihrer Küche besser werden?</h2>
          <p class="ms-copy">Beschreiben Sie kurz, was optisch oder funktional nicht mehr passt. Sie erhalten sofort eine Bestätigungs-E-Mail – Ihre Küchenfotos schicken Sie einfach als Antwort darauf. Wir melden uns anschließend persönlich.</p>
          <p class="ms-copy">Hilfreich sind Gesamtaufnahmen sowie Bilder von Fronten, Arbeitsplatte, Geräten, beschädigten Stellen und Bereichen, die verändert werden sollen.</p>
        </div>
        <?php require $ROOT . '/partials/anfrage-form.php'; ?>
      </div>
    </section>

    <section class="ms-section ms-faq"><div class="ms-wrap"><h2>Häufige Fragen zur Küchenmodernisierung</h2><?php foreach ($faqItems as $faq): ?><details><summary><?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8') ?></summary><p><?= htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8') ?></p></details><?php endforeach; ?></div></section>
  </main>
  <div class="ms-sticky-cta" role="region" aria-label="Schnellkontakt">
    <a class="ms-sticky-cta__call" href="tel:+4967635189970" data-track="kuechenfit_sticky_call" aria-label="Anrufen">📞</a>
    <a class="ms-sticky-cta__button" href="#modernisierungscheck" data-track="kuechenfit_sticky_check">Modernisierungs-Check starten</a>
  </div>
  <?php require $ROOT . '/partials/landing-mini-footer.php'; ?>
  <script src="/assets/js/modernisierung-lp.js?v=<?= rawurlencode((string)filemtime($ROOT . '/assets/js/modernisierung-lp.js')) ?>" defer></script>
</body>
</html>
