<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrfToken = (string)$_SESSION['csrf_token'];
$renderedAt = time();
$ROOT = __DIR__;
require_once dirname($ROOT) . '/app/provenexpert.php';
$peTrustLine = kk_provenexpert_trust_line();
$pageTitle = 'Küche modernisieren | KüchenFit – ein Service von Klas Küchen';
$pageDescription = 'Bestehende Küche sinnvoll modernisieren: Fronten, Arbeitsplatte, Spüle, Nischenrückwand, Licht, Geräte oder Stauraum vom Tischlermeister.';
$canonicalUrl = 'https://kuechenfit.de/';
$robots = 'index,follow';
$skipSiteCss = true; // Seite ist komplett eigenständig gestylt (modernisierung-lp.css), braucht kein Haupt-Stylesheet.
?>
<!doctype html>
<html lang="de">
<head>
  <?php require $ROOT . '/partials/head.php'; ?>
  <meta name="service-category" content="kuechenmodernisierung">
  <link rel="stylesheet" href="/assets/css/modernisierung-lp.css?v=<?= rawurlencode((string)filemtime($ROOT . '/assets/css/modernisierung-lp.css')) ?>">
</head>
<body class="page-landing page-modernisierung page-kuechenfit">
  <a class="ms-brand" href="https://kuechen-klas.de/" aria-label="Klas Küchen" data-track="kuechenfit_brand"><strong>KüchenFit</strong><span>ein Service von Klas Küchen</span></a>
  <header class="ms-hero">
    <picture class="ms-hero__media" aria-hidden="true">
      <img src="/assets/img/kuechensanierung.webp" alt="" width="1200" height="782" fetchpriority="high" decoding="async">
    </picture>
    <div class="ms-wrap ms-hero__inner">
      <p class="ms-eyebrow">Küchenmodernisierung vom Tischlermeister</p>
      <h1>Eine gute Küche muss nicht raus.</h1>
      <p class="ms-lead">Wenn die Küche in die Jahre gekommen ist, muss nicht gleich alles neu. Daniel Klas, Tischlermeister mit über 20 Jahren Erfahrung, prüft persönlich, was bleiben kann und welche Veränderung wirklich sinnvoll ist.</p>
      <p class="ms-trust">Meisterbrief seit 2006 · 20+ Jahre Tischlerhandwerk<?php if ($peTrustLine !== ''): ?> · <a href="https://kuechen-klas.de/kundenstimmen/"><?= htmlspecialchars($peTrustLine, ENT_QUOTES, 'UTF-8') ?></a><?php endif; ?></p>
      <a class="ms-button ms-button--light" href="#modernisierungscheck" data-track="kuechenfit_hero_check">Modernisierungs-Check starten</a>
    </div>
  </header>

  <main>
    <section class="ms-section" id="modernisierungscheck">
      <div class="ms-wrap">
        <p class="ms-eyebrow">Kurzer Modernisierungs-Check</p>
        <h2>Was ist bei deiner Küche der richtige Weg?</h2>
        <p class="ms-copy">Vier kurze Fragen für eine erste Einordnung: gezielte Modernisierung, genauer prüfen oder lieber mit einer Neuplanung vergleichen.</p>
        <div class="ms-quiz" data-fit-quiz>
          <div class="ms-quiz-progress"><div class="ms-quiz-progress__bar" data-quiz-progress></div></div>
          <div class="ms-quiz-step" data-quiz-step="1">
            <h3>Was stört dich an deiner Küche am meisten?</h3>
            <div class="ms-quiz-options" data-quiz-name="anliegen">
              <button type="button" class="ms-quiz-option" data-value="Die Optik passt nicht mehr">Die Optik passt nicht mehr</button>
              <button type="button" class="ms-quiz-option" data-value="Arbeitsplatte oder Spülbereich sind abgenutzt">Arbeitsplatte oder Spülbereich sind abgenutzt</button>
              <button type="button" class="ms-quiz-option" data-value="Stauraum und Bedienung funktionieren nicht gut">Stauraum und Bedienung funktionieren nicht gut</button>
              <button type="button" class="ms-quiz-option" data-value="Licht oder Geräte sollen erneuert werden">Licht oder Geräte sollen erneuert werden</button>
              <button type="button" class="ms-quiz-option" data-value="Mehrere Bereiche müssen verändert werden">Mehrere Bereiche müssen verändert werden</button>
              <button type="button" class="ms-quiz-option" data-value="Ich weiß nicht, ob Modernisieren noch sinnvoll ist">Ich weiß nicht, ob Modernisieren noch sinnvoll ist</button>
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
            <h3>Passt die heutige Aufteilung noch zu deinem Alltag?</h3>
            <div class="ms-quiz-options" data-quiz-name="aufteilung">
              <button type="button" class="ms-quiz-option" data-value="Ja">Ja</button>
              <button type="button" class="ms-quiz-option" data-value="Teilweise">Teilweise</button>
              <button type="button" class="ms-quiz-option" data-value="Nein">Nein</button>
              <button type="button" class="ms-quiz-option" data-value="Noch unklar">Noch unklar</button>
            </div>
          </div>
          <div class="ms-quiz-step" data-quiz-step="4" hidden>
            <h3>Wann möchtest du etwas verändern?</h3>
            <div class="ms-quiz-options" data-quiz-name="zeitpunkt">
              <button type="button" class="ms-quiz-option" data-value="Sofort">Sofort</button>
              <button type="button" class="ms-quiz-option" data-value="1–3 Monate">1–3 Monate</button>
              <button type="button" class="ms-quiz-option" data-value="3–6 Monate">3–6 Monate</button>
              <button type="button" class="ms-quiz-option" data-value="Später">Später</button>
              <button type="button" class="ms-quiz-option" data-value="Noch offen">Noch offen</button>
            </div>
          </div>
          <div class="ms-quiz-result" data-quiz-result hidden>
            <p class="ms-quiz-result__badge" data-quiz-badge></p>
            <p class="ms-quiz-result__text" data-quiz-text></p>
            <div class="ms-quiz-result__actions">
              <a class="ms-button" href="#anfrage" data-track="kuechenfit_quiz_request">Jetzt anfragen</a>
              <a class="ms-button ms-button--secondary" href="#leistungen" data-track="kuechenfit_quiz_services">Alle Leistungen ansehen</a>
            </div>
          </div>
          <button type="button" class="ms-quiz-back" data-quiz-back hidden>Zurück</button>
        </div>
      </div>
    </section>

    <section class="ms-section" id="leistungen">
      <div class="ms-wrap">
        <p class="ms-kicker">Erhalten und verbessern</p>
        <h2>Was stört dich an deiner heutigen Küche?</h2>
        <p class="ms-copy">Vielleicht gefällt dir die Optik nicht mehr. Vielleicht fehlen Stauraum, Licht oder gut erreichbare Auszüge. Oder Arbeitsplatte, Spüle und Geräte sind nicht mehr auf der Höhe. Der Zustand der Korpusse, die Aufteilung und der gewünschte Umfang entscheiden darüber, ob eine Modernisierung sinnvoll ist.</p>
        <div class="ms-grid ms-options">
          <article class="ms-card ms-option"><h3>Fronten und Farbe</h3><p>Folierung oder neue Fronten kommen infrage, wenn Korpusse und Aufteilung weiter passen.</p><a class="ms-card__link" href="https://kuechenfolieren.de/">Folierung prüfen</a></article>
          <article class="ms-card ms-option"><h3>Arbeitsplatte</h3><p>Eine neue Arbeitsplatte kann mit Anpassungen an Spüle, Nische oder Anschlüssen verbunden sein.</p><a class="ms-card__link" href="#anfrage" data-preset-service="Arbeitsplatte">Arbeitsplatte anfragen</a></article>
          <article class="ms-card ms-option"><h3>Spüle und Armatur</h3><p>Eine passende Spüle, eine funktionale Armatur und ein durchdachtes Müllsystem können Arbeitsabläufe deutlich erleichtern.</p><a class="ms-card__link" href="#anfrage" data-preset-service="Spüle und Armatur">Spülbereich prüfen</a></article>
          <article class="ms-card ms-option"><h3>Nischenrückwand</h3><p>Die Fläche zwischen Arbeitsplatte und Oberschränken verbindet Spritzschutz, Reinigung und Gestaltung.</p><a class="ms-card__link" href="#anfrage" data-preset-service="Nischenrückwand">Nische anfragen</a></article>
          <article class="ms-card ms-option"><h3>Licht und Geräte</h3><p>Beleuchtung und Geräte lassen sich abhängig von Einbaumaßen und vorhandenen Anschlüssen erneuern.</p><a class="ms-card__link" href="#anfrage" data-preset-service="Licht und Geräte">Details beschreiben</a></article>
          <article class="ms-card ms-option"><h3>Stauraum und Funktion</h3><p>Schubladen, Innenauszüge, Beschläge und einzelne Schranklösungen können vorhandenen Platz besser nutzbar machen.</p><a class="ms-card__link" href="#anfrage" data-preset-service="Stauraum und Funktion">Funktion prüfen</a></article>
          <article class="ms-card ms-option"><h3>Umfassende Modernisierung</h3><p>Wenn mehrere Bereiche betroffen sind, betrachten wir Küche, Technik und sichtbare Flächen zusammen.</p><a class="ms-card__link" href="#anfrage" data-preset-service="Komplettmodernisierung">Modernisierung anfragen</a></article>
          <article class="ms-card ms-option"><h3>Modernisieren oder neu planen?</h3><p>Wenn Substanz oder Grundriss nicht mehr passen, kann eine neue Planung die sinnvollere Lösung sein.</p><a class="ms-card__link" href="https://kuechen-klas.de/kuechenstudio/kuechenplanung/">Neuküchenplanung ansehen</a></article>
        </div>
      </div>
    </section>

    <section class="ms-section">
      <div class="ms-wrap"><p class="ms-kicker">Ein Projekt im Detail</p><h2>Echte Projekte entscheiden über Vertrauen.</h2><div class="ms-proof"><div class="ms-photo-placeholder"><span><strong>Vorher-Foto folgt</strong>Vor Veröffentlichung durch ein echtes Klas-Projekt ersetzen.</span></div><div class="ms-photo-placeholder"><span><strong>Nachher-Foto folgt</strong>Gleicher Blickwinkel und nachvollziehbare Beschreibung.</span></div></div></div>
    </section>

    <section class="ms-section ms-section--soft">
      <div class="ms-wrap ms-grid ms-grid--2">
        <div><p class="ms-kicker">Mehr als eine neue Oberfläche</p><h2>Optik und Funktion gemeinsam betrachten.</h2><p class="ms-copy">Eine neue Arbeitsplatte verändert das Gesamtbild. Gut erreichbare Auszüge schaffen Ordnung. Eine passend geplante Spüle oder Beleuchtung erleichtert tägliche Handgriffe. Deshalb betrachten wir nicht nur, was alt aussieht, sondern auch, was dich bei der Nutzung stört.</p></div>
        <div><p class="ms-kicker">Klare Entscheidung</p><h2>Erst den Bestand prüfen, dann den Umfang festlegen.</h2><div class="ms-copy"><p>Eine Modernisierung lohnt sich nicht automatisch. Beschädigte Korpusse, ungünstige Wege oder viele notwendige Einzelmaßnahmen können dafür sprechen, neu zu planen.</p><p>Genau deshalb beginnt KüchenFit mit einer Bestandsaufnahme und nicht mit einem pauschalen Sparversprechen.</p></div></div>
      </div>
    </section>

    <section class="ms-section">
      <div class="ms-wrap ms-grid ms-grid--2">
        <div><p class="ms-kicker">KüchenFit von Klas Küchen</p><h2>Ein Ansprechpartner für den vorhandenen Bestand.</h2><p class="ms-copy">Ich bin Daniel Klas, Tischlermeister und auf Küchen spezialisiert. Ich prüfe die vorhandene Küche, ordne die gewünschten Veränderungen ein und sage auch dann offen Bescheid, wenn eine Reparatur, eine andere Modernisierung oder eine Neuplanung sinnvoller ist.</p></div>
        <div class="ms-process">
          <article><span>1</span><div><h3>Problem beschreiben</h3><p>Fotos, ungefähre Maße und deine wichtigsten Wünsche helfen bei der ersten Einordnung.</p></div></article>
          <article><span>2</span><div><h3>Bestand prüfen</h3><p>Korpusse, Aufteilung, Anschlüsse und betroffene Bauteile bestimmen den sinnvollen Umfang.</p></div></article>
          <article><span>3</span><div><h3>Lösung festlegen</h3><p>Du erhältst eine konkrete Empfehlung und erst danach ein Angebot für die passende Ausführung.</p></div></article>
        </div>
      </div>
    </section>

    <section class="ms-section ms-section--dark" id="anfrage">
      <div class="ms-wrap ms-grid ms-grid--2">
        <div><p class="ms-eyebrow">Deine Anfrage</p><h2>Was soll sich verändern?</h2><p class="ms-copy">Schick uns die wichtigsten Eckdaten. Danach melden wir uns persönlich und klären, welcher nächste Schritt passt.</p></div>
        <form class="ms-check" action="/api/modernisierung_submit.php" method="post" data-modernisierung-form data-form-id="kuechenfit_check" data-prep-bullets='["Ein paar Fotos vom aktuellen Zustand der Küche","Ungefähre Maße der Bereiche, die sich verändern sollen","Was dich an der Küche aktuell am meisten stört"]' novalidate>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="form_rendered_at" value="<?= $renderedAt ?>">
          <input type="hidden" name="page_url"><input type="hidden" name="referrer">
          <?php foreach (['utm_source','utm_medium','utm_campaign','utm_content','utm_term','fbclid','gclid','wbraid','gbraid','msclkid','epik'] as $field): ?><input type="hidden" name="<?= $field ?>" data-campaign-field="<?= $field ?>"><?php endforeach; ?>
          <div class="ms-hidden" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label><label>Leer lassen <input name="hp_field" tabindex="-1" autocomplete="off"></label></div>
          <div class="ms-form-grid">
            <label class="ms-field"><span>Name *</span><input name="name" autocomplete="name" minlength="2" maxlength="100" required></label>
            <label class="ms-field"><span>E-Mail *</span><input type="email" name="email" autocomplete="email" maxlength="254" required></label>
            <label class="ms-field"><span>Telefon</span><input type="tel" name="phone" autocomplete="tel" maxlength="30"></label>
            <label class="ms-field"><span>PLZ *</span><input name="plz" inputmode="numeric" pattern="[0-9]{5}" maxlength="5" required></label>
            <label class="ms-field ms-field--wide"><span>Was möchtest du verändern? *</span><select name="service" required><option value="">Bitte wählen</option><option value="Folierung">Fronten folieren</option><option value="Frontentausch">Fronten tauschen</option><option value="Arbeitsplatte">Arbeitsplatte erneuern</option><option value="Spüle und Armatur">Spüle, Armatur oder Müllsystem</option><option value="Nischenrückwand">Nischenrückwand erneuern</option><option value="Licht und Geräte">Licht oder Geräte</option><option value="Stauraum und Funktion">Stauraum oder Funktion</option><option value="Komplettmodernisierung">Umfassende Modernisierung</option><option value="Entscheidungscheck">Modernisieren oder neu planen?</option></select></label>
            <label class="ms-field ms-field--wide"><span>Was stört dich aktuell? *</span><textarea name="details" minlength="10" maxlength="2000" required></textarea></label>
            <label class="ms-field"><span>Wann soll es losgehen? *</span><select name="zeitpunkt" required><option value="">Bitte wählen</option><option value="Sofort">Sofort</option><option value="1–3 Monate">1–3 Monate</option><option value="3–6 Monate">3–6 Monate</option><option value="Später">Später</option><option value="Noch offen">Noch offen</option></select></label>
            <label class="ms-privacy"><input type="checkbox" name="consent" value="1" required><span>Ich habe die <a href="/datenschutz/" target="_blank" rel="noopener">Datenschutzerklärung</a> gelesen und akzeptiert. *</span></label>
            <p class="ms-capacity">Ich übernehme Beratungen persönlich – Termine sind auf Do/Fr/Sa begrenzt.</p>
            <div class="ms-message" data-form-message role="status" aria-live="polite"></div>
            <button class="ms-button" type="submit">Küche prüfen lassen</button>
          </div>
        </form>
      </div>
    </section>

    <section class="ms-section ms-faq"><div class="ms-wrap"><h2>Häufige Fragen zur Küchenmodernisierung</h2><details><summary>Kann ich nur einzelne Bereiche erneuern?</summary><p>Ja. Ob einzelne Bauteile unabhängig sinnvoll erneuert werden können, hängt von Maßen, Anschlüssen und dem Zustand der angrenzenden Bereiche ab.</p></details><details><summary>Bleibt meine Küche während der Arbeiten nutzbar?</summary><p>Das hängt vom Umfang ab. Bei einzelnen Maßnahmen ist der Eingriff meist kleiner als bei einer vollständigen Neuplanung. Den konkreten Ablauf und mögliche Einschränkungen besprechen wir vorab.</p></details><details><summary>Wann lohnt sich eine Modernisierung nicht mehr?</summary><p>Wenn Korpusse stark beschädigt sind, die Aufteilung nicht mehr funktioniert oder sehr viele Bauteile gleichzeitig ersetzt werden müssten, kann eine Neuplanung sinnvoller sein.</p></details><details><summary>Muss ich schon genaue Maße haben?</summary><p>Nein. Fotos und ungefähre Angaben reichen für die erste Einordnung. Für ein verbindliches Angebot werden die benötigten Maße anschließend fachgerecht geprüft.</p></details></div></section>
  </main>
  <div class="ms-sticky-cta" role="region" aria-label="Schnellkontakt">
    <a class="ms-sticky-cta__call" href="tel:+4967635189970" data-track="kuechenfit_sticky_call" aria-label="Anrufen">📞</a>
    <a class="ms-sticky-cta__button" href="#modernisierungscheck" data-track="kuechenfit_sticky_check">Modernisierungs-Check starten</a>
  </div>
  <?php require $ROOT . '/partials/landing-mini-footer.php'; ?>
  <script src="/assets/js/modernisierung-lp.js?v=<?= rawurlencode((string)filemtime($ROOT . '/assets/js/modernisierung-lp.js')) ?>" defer></script>
</body>
</html>
