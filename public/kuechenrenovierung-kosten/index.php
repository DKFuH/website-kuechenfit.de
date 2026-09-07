<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$ROOT = dirname(__DIR__);
$pageTitle = 'Küchenrenovierung Kosten: individuell kalkuliert | KüchenFit';
$pageDescription = 'Was kostet Ihre Küchenrenovierung? Wir prüfen Bestand, gewünschte Veränderungen und technischen Aufwand für eine fachlich belastbare Kalkulation.';
$canonicalUrl = 'https://kuechenfit.de/kuechenrenovierung-kosten/';
$robots = 'index,follow';
$skipSiteCss = true;
$anfrageFormId = 'kuechenfit_kosten';
$faqItems = [
    ['question' => 'Was kostet eine Küchenrenovierung?', 'answer' => 'Das hängt vom Umfang ab. Ein Frontentausch wird anders kalkuliert als eine Renovierung mit neuer Arbeitsplatte, Geräten und technischen Veränderungen. Preisbeispiele dienen deshalb nur als Orientierung.'],
    ['question' => 'Ist eine Küchenrenovierung immer günstiger als eine neue Küche?', 'answer' => 'Nein. Sind Korpusse und Aufteilung geeignet, kann eine Renovierung sinnvoll sein. Müssen fast alle Bestandteile verändert werden, sollte auch eine Neuplanung verglichen werden.'],
    ['question' => 'Warum reicht ein Preis pro laufendem Meter nicht aus?', 'answer' => 'Küchen gleicher Länge können sehr unterschiedlich aufgebaut sein. Frontanzahl, Eckverbindungen, Ausschnitte, Geräte, Materialien und technische Anpassungen beeinflussen den Aufwand.'],
    ['question' => 'Kann der Preis anhand von Fotos bestimmt werden?', 'answer' => 'Fotos ermöglichen eine erste Einschätzung des möglichen Umfangs. Für eine verbindliche Kalkulation sind eine genaue Bestandsprüfung und in der Regel ein Aufmaß erforderlich.'],
    ['question' => 'Welche Angaben werden für eine erste Einschätzung benötigt?', 'answer' => 'Hilfreich sind Gesamtaufnahmen, ungefähre Maße, die Küchenform und eine Beschreibung der Bauteile, die erhalten oder verändert werden sollen.'],
];
?>
<!doctype html>
<html lang="de">
<head>
  <?php require $ROOT . '/partials/head.php'; ?>
  <link rel="stylesheet" href="/assets/css/kuechenfit-base.css?v=<?= rawurlencode((string)filemtime($ROOT . '/assets/css/kuechenfit-base.css')) ?>">
  <?php
  require dirname($ROOT) . '/app/seo-schema.php';
  kk_render_breadcrumb_schema([['name' => 'Küchenrenovierung Kosten', 'url' => $canonicalUrl]]);
  kk_render_webpage_schema('Küchenrenovierung Kosten', $pageDescription, $canonicalUrl);
  kk_render_faq_schema($faqItems);
  ?>
</head>
<body class="page-kuechenfit">
  <div class="kf-topbar">
    <a class="kf-topbar__brand" href="/">KüchenFit <span>ein Service von Klas Küchen®</span></a>
    <a class="kf-topbar__cta" href="#anfrage" data-track="kuechenfit_topbar_cta">Renovierung einschätzen lassen</a>
  </div>
  <?php $kfNavCurrent = '/kuechenrenovierung-kosten/'; require $ROOT . '/partials/kf-leistungsnav.php'; ?>

  <header class="kf-hero">
    <div class="ms-wrap">
      <p class="ms-kicker">Kosten fachlich einschätzen</p>
      <h1>Was kostet eine Küchenrenovierung?</h1>
      <p>Darauf gibt es keinen sinnvollen Pauschalpreis. Entscheidend ist nicht nur die Größe der Küche, sondern welche Bauteile bleiben können, was erneuert werden soll und welche Anpassungen am vorhandenen Bestand notwendig sind. Deshalb betrachten wir zuerst die Küche – und kalkulieren den tatsächlichen Umfang danach individuell.</p>
      <div class="kf-hero__cta">
        <a class="ms-button ms-button--light" href="#anfrage" data-track="kuechenfit_hero_check">Kosten fachlich einschätzen lassen</a>
        <a class="ms-button ms-button--secondary" href="#kostentreiber" data-track="kuechenfit_hero_factors">Kostentreiber ansehen</a>
      </div>
    </div>
  </header>

  <main>
    <section class="ms-section" id="kostentreiber">
      <div class="ms-wrap kf-prose">
        <h2>Was den Preis einer Küchenrenovierung bestimmt</h2>
        <p>Zwei ähnlich große Küchen können einen vollkommen unterschiedlichen Renovierungsaufwand verursachen. Für eine fachliche Kalkulation betrachten wir deshalb die einzelnen Bausteine.</p>
        <h3>Fronten und sichtbare Bauteile</h3>
        <p>Nicht nur die Anzahl der Türen zählt. Maße, Oberfläche, Kanten, Griffe, Scharniere, Wangen, Blenden und Sockel beeinflussen Material- und Montageaufwand.</p>
        <h3>Arbeitsplatte</h3>
        <p>Material, Gesamtlänge, Plattenverbindungen und Wandverlauf wirken sich ebenso auf den Preis aus wie Ausschnitte für Spüle und Kochfeld.</p>
        <h3>Geräte, Spüle und Armatur</h3>
        <p>Bei neuen Geräten müssen neben dem Gerätepreis auch Einbaumaße, Belüftung, Anschlüsse und mögliche Anpassungen an den vorhandenen Schränken berücksichtigt werden.</p>
        <h3>Technische Veränderungen</h3>
        <p>Werden Geräte versetzt oder Wasser-, Elektro- und Lichtanschlüsse verändert, entsteht zusätzlicher Planungs- und Ausführungsaufwand.</p>
        <h3>Demontage, Anpassung und Montage</h3>
        <p>In einer bestehenden Küche muss Neues an Vorhandenes angepasst werden. Ecken, Wandverläufe, ältere Beschläge und Sondermaße können dabei mehr Aufwand verursachen als die reine Küchengröße vermuten lässt.</p>
      </div>
    </section>

    <section class="ms-section ms-section--soft">
      <div class="ms-wrap kf-prose">
        <h2>Warum wir keinen pauschalen Renovierungspreis nennen</h2>
        <p>Eine große Preisspanne würde zwar eine schnelle Zahl liefern, aber kaum etwas über die Kosten Ihrer Küche aussagen. Ein kleiner Frontentausch ist ein anderes Projekt als eine Renovierung mit neuer Arbeitsplatte, Geräten und veränderten Anschlüssen.</p>
        <p>Deshalb klären wir zunächst drei Fragen:</p>
        <ol>
          <li>Welche Bestandteile der vorhandenen Küche sind weiterhin geeignet?</li>
          <li>Was soll optisch oder funktional besser werden?</li>
          <li>Welche technischen und handwerklichen Anpassungen sind dafür nötig?</li>
        </ol>
        <p>Erst daraus entsteht ein sinnvoller Renovierungsumfang – und damit eine Kalkulation, die zu Ihrem tatsächlichen Projekt passt.</p>
      </div>
    </section>

    <section class="ms-section">
      <div class="ms-wrap">
        <p class="ms-kicker">Der Weg zum Preis</p>
        <h2>Vom ersten Foto zum konkreten Preis</h2>
        <div class="ms-process">
          <article><span>1</span><div><h3>Küche zeigen</h3><p>Fotos und Ihre Beschreibung zeigen uns, was vorhanden ist und was sich verändern soll.</p></div></article>
          <article><span>2</span><div><h3>Umfang einordnen</h3><p>Wir prüfen, welche Bauteile bleiben können und welche Maßnahmen grundsätzlich infrage kommen.</p></div></article>
          <article><span>3</span><div><h3>Bestand aufnehmen</h3><p>Für die genaue Planung werden Maße, Beschläge, Anschlüsse und notwendige Anpassungen am Bestand erfasst.</p></div></article>
          <article><span>4</span><div><h3>Ausführung festlegen</h3><p>Materialien, Geräte und gewünschte Veränderungen werden konkretisiert.</p></div></article>
          <article><span>5</span><div><h3>Projekt kalkulieren</h3><p>Auf dieser Grundlage kann der tatsächliche Renovierungsumfang nachvollziehbar kalkuliert werden.</p></div></article>
        </div>
      </div>
    </section>

    <section class="ms-section ms-section--dark" id="anfrage">
      <div class="ms-wrap ms-grid ms-grid--2">
        <div>
          <p class="ms-eyebrow">Ihre Küche einschätzen lassen</p>
          <h2>Was würde die Renovierung Ihrer Küche kosten?</h2>
          <p class="ms-copy">Zeigen Sie uns die vorhandene Küche und beschreiben Sie, was bleiben und was verändert werden soll. Mit Fotos, ungefährer Größe und Ihren Vorstellungen können wir den möglichen Umfang zunächst fachlich einordnen.</p>
          <p class="ms-copy">Hilfreich sind Gesamtaufnahmen sowie Bilder von Fronten, Arbeitsplatte, Geräten, Ecken und beschädigten Bereichen.</p>
          <p class="ms-copy">KüchenFit ist ein Service von Klas Küchen®. Die Einschätzung verbindet Küchenplanung mit dem handwerklichen Blick des Tischlermeisters auf Substanz, Konstruktion und Umsetzbarkeit.</p>
          <p class="ms-copy">Ein verbindlicher Preis ist erst nach Bestandsprüfung, Aufmaß und Materialauswahl möglich.</p>
        </div>
        <?php require $ROOT . '/partials/anfrage-form.php'; ?>
      </div>
    </section>

    <section class="ms-section ms-section--soft ms-faq">
      <div class="ms-wrap">
        <h2>Häufige Fragen zu den Kosten</h2>
        <?php foreach ($faqItems as $faq): ?>
        <details><summary><?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8') ?></summary><p><?= htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8') ?></p></details>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="ms-section">
      <div class="ms-wrap kf-prose">
        <h2>Renovieren – oder doch neu?</h2>
        <p>Ob sich die Renovierung gegenüber einer neuen Küche rechnet, hängt von der Ausgangslage ab. Diese Abwägung gehört zur Beratung – mehr dazu unter <a href="/renovieren-oder-neue-kueche/">Renovieren oder neue Küche?</a> und <a href="/kuechenrenovierung/">Küchenrenovierung</a>.</p>
        <p><a class="ms-button" href="#anfrage" data-track="kuechenfit_footer_cta">Kosten fachlich einschätzen lassen</a></p>
      </div>
    </section>
  </main>

  <?php require $ROOT . '/partials/landing-mini-footer.php'; ?>
  <script src="/assets/js/modernisierung-lp.js?v=<?= rawurlencode((string)filemtime($ROOT . '/assets/js/modernisierung-lp.js')) ?>" defer></script>
</body>
</html>
