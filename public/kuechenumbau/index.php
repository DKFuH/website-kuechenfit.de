<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$ROOT = dirname(__DIR__);
$pageTitle = 'Bestehende Küche umbauen lassen | KüchenFit Sohren';
$pageDescription = 'Bestehende Küche umbauen lassen: Schränke ergänzen, Geräte versetzen, Arbeitsfläche vergrößern und den vorhandenen Bestand neu strukturieren.';
$canonicalUrl = 'https://kuechenfit.de/kuechenumbau/';
$robots = 'index,follow';
$skipSiteCss = true;
$anfrageFormId = 'kuechenfit_umbau';
$faqItems = [
    ['question' => 'Kann man vorhandene Küchenschränke versetzen?', 'answer' => 'Das kann möglich sein. Maße, Anschlusspositionen, Arbeitsplatte und der Zustand der Schränke müssen zur neuen Position passen.'],
    ['question' => 'Kann eine bestehende Küche erweitert werden?', 'answer' => 'Ja, sofern passende Schränke oder geeignete Ergänzungslösungen verfügbar beziehungsweise planbar sind.'],
    ['question' => 'Müssen Ergänzungen vom ursprünglichen Küchenhersteller stammen?', 'answer' => 'Nicht zwingend. Wenn das ursprüngliche Programm nicht mehr erhältlich ist, können je nach Bestand maßlich passende oder bewusst abgesetzte Ergänzungen geplant werden.'],
    ['question' => 'Kann aus einer Küchenzeile eine Küche mit Insel werden?', 'answer' => 'Das hängt von Raumgröße, Laufwegen, Öffnungsbereichen, gewünschtem Stauraum und der Funktion der Insel ab.'],
    ['question' => 'Können Wasseranschluss und Kochfeld versetzt werden?', 'answer' => 'Das kann technisch möglich sein, erfordert aber eine Prüfung der Leitungswege, Anschlüsse und beteiligten Fachgewerke.'],
    ['question' => 'Was kostet ein Küchenumbau?', 'answer' => 'Die Kosten hängen von den weiterverwendbaren Bauteilen, neuen Möbeln, Arbeitsplatten, Geräten, Anschlüssen und dem Anpassungsaufwand ab. Eine konkrete Kalkulation ist erst nach Prüfung des Bestands möglich.'],
    ['question' => 'Wann ist eine neue Küche sinnvoller?', 'answer' => 'Wenn fast alle Möbel, Fronten, Arbeitsplatten und Anschlüsse verändert werden müssen oder die vorhandene Substanz ungeeignet ist, sollte eine Neuplanung als Alternative verglichen werden.'],
];
?>
<!doctype html>
<html lang="de">
<head>
  <?php require $ROOT . '/partials/head.php'; ?>
  <link rel="stylesheet" href="/assets/css/kuechenfit-base.css?v=<?= rawurlencode((string)filemtime($ROOT . '/assets/css/kuechenfit-base.css')) ?>">
  <?php
  require dirname($ROOT) . '/app/seo-schema.php';
  kk_render_breadcrumb_schema([['name' => 'Küchenumbau', 'url' => $canonicalUrl]]);
  kk_render_service_schema('Bestehende Küche umbauen', $pageDescription, $canonicalUrl, 'Küchenumbau');
  kk_render_faq_schema($faqItems);
  ?>
</head>
<body class="page-kuechenfit">
  <div class="kf-topbar">
    <a class="kf-topbar__brand" href="/">KüchenFit <span>ein Service von Klas Küchen®</span></a>
    <a class="kf-topbar__cta" href="#anfrage" data-track="kuechenfit_topbar_cta">Umbau prüfen lassen</a>
  </div>
  <?php $kfNavCurrent = '/kuechenumbau/'; require $ROOT . '/partials/kf-leistungsnav.php'; ?>

  <header class="kf-hero">
    <div class="ms-wrap">
      <p class="ms-kicker">Küchenumbau im Bestand</p>
      <h1>Bestehende Küche umbauen und an neue Anforderungen anpassen</h1>
      <p>Ihre Küche soll bleiben, aber anders funktionieren? Mehr Arbeitsfläche, zusätzlicher Stauraum, ein größerer Kühlschrank oder eine ergänzende Insel: Wir prüfen, wie sich vorhandene Schränke weiterverwenden, mit neuen Elementen verbinden und an neue Anforderungen anpassen lassen.</p>
      <div class="kf-hero__cta">
        <a class="ms-button ms-button--light" href="#anfrage" data-track="kuechenfit_hero_check">Umbau prüfen lassen</a>
        <a class="ms-button ms-button--secondary" href="#moeglichkeiten" data-track="kuechenfit_hero_options">Möglichkeiten ansehen</a>
      </div>
      <p class="kf-hero__note">Küchenumbau im Raum Sohren und Umgebung.</p>
    </div>
  </header>

  <main>
    <section class="ms-section">
      <div class="ms-wrap kf-prose">
        <h2>Viele bestehende Küchen lassen sich verändern – aber nicht beliebig</h2>
        <p>Lebenssituationen und Anforderungen verändern sich. Vielleicht wurde die Küche ursprünglich für zwei Personen geplant und inzwischen kocht die ganze Familie darin. Vielleicht fehlt Arbeitsfläche, der Kühlschrank ist zu klein oder bestimmte Wege haben im Alltag nie richtig funktioniert.</p>
        <p>Dann kann es sinnvoll sein, die vorhandene Küche neu zu strukturieren. Entscheidend ist, ob Möbel, Maße, Anschlüsse und Raum die gewünschte Veränderung zulassen.</p>
      </div>
    </section>

    <section class="ms-section ms-section--soft" id="moeglichkeiten">
      <div class="ms-wrap kf-prose">
        <h2>Was kann durch einen Küchenumbau besser werden?</h2>
        <h3>Mehr Stauraum</h3>
        <p>Zusätzliche Schränke, ein ergänzter Hochschrank oder eine andere Innenaufteilung können ungenutzten Platz besser erschließen.</p>
        <h3>Mehr Arbeitsfläche</h3>
        <p>Je nach Raum kann die vorhandene Arbeitsfläche verlängert oder durch eine Halbinsel beziehungsweise freistehende Insel ergänzt werden.</p>
        <h3>Passende Geräte</h3>
        <p>Ein größerer Kühlschrank, ein neues Kochfeld oder ein Gerät an einer anderen Position können neue Möbel, angepasste Ausschnitte und veränderte Anschlüsse erfordern.</p>
        <h3>Bessere Arbeitsabläufe</h3>
        <p>Manchmal liegt das Problem nicht im Aussehen, sondern in schlecht erreichbarem Stauraum, zu wenig Ablagefläche oder ungünstigen Wegen zwischen Spüle, Arbeitsfläche und Kochfeld.</p>
        <h3>Ein stimmiges Gesamtbild</h3>
        <p>Neue Schränke müssen nicht nur technisch passen. Fronten, Arbeitsplatte, Wangen, Sockel, Griffe und Beleuchtung müssen mit dem vorhandenen Bestand sinnvoll verbunden werden.</p>
      </div>
    </section>

    <section class="ms-section">
      <div class="ms-wrap kf-prose">
        <h2>Was passiert, wenn das ursprüngliche Küchenprogramm nicht mehr erhältlich ist?</h2>
        <p>Gerade bei älteren Küchen sind identische Schränke, Fronten oder Dekore häufig nicht mehr lieferbar. Eine Erweiterung muss deshalb nicht zwingend wie ein nachträglich eingesetztes Fremdteil aussehen.</p>
        <p>Je nach Bestand kommen unterschiedliche Wege infrage:</p>
        <ul>
          <li>passende Elemente aus dem noch verfügbaren Küchenprogramm,</li>
          <li>maßlich geeignete Ergänzungen mit angepassten Sichtseiten,</li>
          <li>eine bewusst abgesetzte Ergänzung in einem zweiten Material,</li>
          <li>neue Fronten für den gesamten sichtbaren Bereich,</li>
          <li>eine eigenständige Insel oder ein ergänzendes Möbel.</li>
        </ul>
        <p>Welche Lösung sinnvoll ist, hängt davon ab, wie eng Alt und Neu technisch und gestalterisch zusammenarbeiten müssen.</p>
      </div>
    </section>

    <section class="ms-section ms-section--soft">
      <div class="ms-wrap kf-prose">
        <h2>Entscheidend ist die Verbindung zwischen Alt und Neu</h2>
        <p>Ein neuer Schrank kann für sich allein passen und trotzdem nicht zur vorhandenen Küche. Beim Küchenumbau prüfen wir deshalb unter anderem:</p>
        <ul>
          <li>Korpus-, Sockel- und Arbeitshöhe,</li>
          <li>Schrankbreiten und vorhandenes Küchenraster,</li>
          <li>Frontbild, Fugen und sichtbare Seiten,</li>
          <li>Arbeitsplatte und notwendige Verbindungen,</li>
          <li>Gerätemaße und Belüftung,</li>
          <li>Wasser-, Elektro- und gegebenenfalls Abluftführung,</li>
          <li>Raummaße, Laufwege und Öffnungsbereiche.</li>
        </ul>
        <p>Deshalb beginnt der Küchenumbau mit einer Bestandsaufnahme und einem belastbaren Aufmaß. Erst danach lässt sich beurteilen, welche Lösung technisch möglich und wirtschaftlich vernünftig ist.</p>
      </div>
    </section>

    <section class="ms-section">
      <div class="ms-wrap kf-prose">
        <h2>Wann ist ein Umbau nicht mehr sinnvoll?</h2>
        <p>Je stärker in die vorhandene Küche eingegriffen wird, desto wichtiger wird der Vergleich mit einer Neuplanung. Müssen fast alle Schränke versetzt, sämtliche Fronten ersetzt, die komplette Arbeitsplatte erneuert und mehrere Anschlüsse verändert werden, kann der Erhalt des Bestands mehr Aufwand als Nutzen verursachen.</p>
        <p>Dann sagen wir das offen und betrachten den Küchenumbau nicht automatisch als bessere Lösung.</p>
        <p><a href="/renovieren-oder-neue-kueche/">Umbau, Renovierung oder neue Küche?</a> &middot; <a href="/kuechenrenovierung-kosten/">Was beeinflusst die Kosten?</a></p>
      </div>
    </section>

    <section class="ms-section ms-section--soft">
      <div class="ms-wrap">
        <p class="ms-kicker">Ablauf</p>
        <h2>So wird der Küchenumbau geprüft</h2>
        <div class="ms-process">
          <article><span>1</span><div><h3>Problem beschreiben</h3><p>Sie zeigen uns die Küche und erklären, was im Alltag nicht mehr passt.</p></div></article>
          <article><span>2</span><div><h3>Bestand prüfen</h3><p>Möbel, Maße, Geräte, Anschlüsse und Raum werden als zusammenhängendes System betrachtet.</p></div></article>
          <article><span>3</span><div><h3>Lösung entwickeln</h3><p>Wir prüfen, welche vorhandenen Teile bleiben und welche Ergänzungen erforderlich sind.</p></div></article>
          <article><span>4</span><div><h3>Aufwand kalkulieren</h3><p>Nach Aufmaß und Festlegung der Ausführung kann der Umbau konkret kalkuliert werden.</p></div></article>
        </div>
      </div>
    </section>

    <section class="ms-section ms-section--dark" id="anfrage">
      <div class="ms-wrap ms-grid ms-grid--2">
        <div>
          <p class="ms-eyebrow">Küchenumbau anfragen</p>
          <h2>Was soll in Ihrer Küche besser funktionieren?</h2>
          <p class="ms-copy">Zeigen Sie uns die vorhandene Küche und beschreiben Sie nicht nur die gewünschte Lösung, sondern auch das Problem dahinter: zu wenig Arbeitsfläche, fehlender Stauraum, ein zu kleines Gerät oder ungünstige Arbeitsabläufe.</p>
          <p class="ms-copy">Hilfreich sind Gesamtaufnahmen, ungefähre Raummaße und Fotos der Bereiche, die verändert werden sollen.</p>
          <p class="ms-copy">KüchenFit ist ein Service von Klas Küchen®. Die Prüfung verbindet Küchenplanung mit dem handwerklichen Blick des Tischlermeisters auf Bestand, Konstruktion und Umsetzbarkeit.</p>
        </div>
        <?php require $ROOT . '/partials/anfrage-form.php'; ?>
      </div>
    </section>

    <section class="ms-section ms-section--soft ms-faq">
      <div class="ms-wrap">
        <h2>Häufige Fragen zum Küchenumbau</h2>
        <?php foreach ($faqItems as $faq): ?>
        <details><summary><?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8') ?></summary><p><?= htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8') ?></p></details>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="ms-section">
      <div class="ms-wrap kf-prose">
        <h2>Nicht neu anfangen, wenn die richtige Grundlage schon da ist.</h2>
        <p>Ein Küchenumbau kann vorhandene Qualität erhalten und trotzdem grundlegend verändern, wie der Raum funktioniert.</p>
        <p><a class="ms-button" href="#anfrage" data-track="kuechenfit_footer_cta">Bestehende Küche umbauen</a></p>
      </div>
    </section>
  </main>

  <?php require $ROOT . '/partials/landing-mini-footer.php'; ?>
  <script src="/assets/js/modernisierung-lp.js?v=<?= rawurlencode((string)filemtime($ROOT . '/assets/js/modernisierung-lp.js')) ?>" defer></script>
</body>
</html>
