<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$ROOT = dirname(__DIR__);
$pageTitle = 'Küche renovieren lassen | KüchenFit Sohren';
$pageDescription = 'Küche renovieren lassen: Wir prüfen Fronten, Arbeitsplatte, Geräte und Korpusse und planen, was sinnvoll erhalten oder erneuert werden kann.';
$canonicalUrl = 'https://kuechenfit.de/kuechenrenovierung/';
$robots = 'index,follow';
$skipSiteCss = true;
$anfrageFormId = 'kuechenfit_renovierung';
$faqItems = [
    ['question' => 'Muss bei einer Küchenrenovierung die ganze Küche ausgebaut werden?', 'answer' => 'Nein. Der Umfang hängt von den geplanten Maßnahmen ab. Für einen Frontentausch ist ein vollständiger Ausbau normalerweise nicht erforderlich. Beim Austausch der Arbeitsplatte oder bei größeren Umbauten können weitere Demontagearbeiten notwendig werden.'],
    ['question' => 'Was kostet es, eine Küche renovieren zu lassen?', 'answer' => 'Die Kosten hängen vom Zustand und Aufbau der Küche sowie von Fronten, Arbeitsplatte, Geräten, Materialien und notwendigen Anpassungen ab. Nach der Bestandsprüfung kann der Umfang konkreter kalkuliert werden.'],
    ['question' => 'Wie lange dauert eine Küchenrenovierung?', 'answer' => 'Das hängt vom Umfang der Arbeiten und den bestellten Bauteilen ab. Der reine Montagezeitraum lässt sich nach Aufmaß und Festlegung der Maßnahmen genauer einschätzen.'],
    ['question' => 'Kann auch eine ältere Küche renoviert werden?', 'answer' => 'Ja, sofern die vorhandene Substanz dafür geeignet ist. Das Alter allein entscheidet nicht. Wichtiger sind Zustand, Konstruktion, Beschläge und die gewünschte Veränderung.'],
    ['question' => 'Können Fronten und Arbeitsplatte gleichzeitig erneuert werden?', 'answer' => 'Ja. Bei einer optischen Neugestaltung kann es sinnvoll sein, Fronten und Arbeitsplatte gemeinsam auszuwählen. Zwingend erforderlich ist das nicht.'],
    ['question' => 'Ist eine Renovierung immer günstiger als eine neue Küche?', 'answer' => 'Nein. Bei einer geeigneten Ausgangslage kann eine Renovierung weniger Eingriff bedeuten. Müssen jedoch fast alle Bauteile oder die gesamte Aufteilung verändert werden, sollte eine Neuplanung als Alternative verglichen werden.'],
];
?>
<!doctype html>
<html lang="de">
<head>
  <?php require $ROOT . '/partials/head.php'; ?>
  <link rel="stylesheet" href="/assets/css/kuechenfit-base.css?v=<?= rawurlencode((string)filemtime($ROOT . '/assets/css/kuechenfit-base.css')) ?>">
  <?php
  require dirname($ROOT) . '/app/seo-schema.php';
  kk_render_breadcrumb_schema([['name' => 'Küchenrenovierung', 'url' => $canonicalUrl]]);
  kk_render_service_schema('Küche renovieren lassen', $pageDescription, $canonicalUrl, 'Küchenrenovierung');
  kk_render_faq_schema($faqItems);
  ?>
</head>
<body class="page-kuechenfit">
  <div class="kf-topbar">
    <a class="kf-topbar__brand" href="/">KüchenFit <span>ein Service von Klas Küchen®</span></a>
    <a class="kf-topbar__cta" href="#anfrage" data-track="kuechenfit_topbar_cta">Küche prüfen lassen</a>
  </div>
  <?php $kfNavCurrent = '/kuechenrenovierung/'; require $ROOT . '/partials/kf-leistungsnav.php'; ?>

  <header class="kf-hero">
    <div class="ms-wrap">
      <p class="ms-kicker">Küchenrenovierung im Raum Sohren</p>
      <h1>Küche renovieren lassen – gezielt statt komplett neu</h1>
      <p>Die Schränke funktionieren und die Aufteilung passt grundsätzlich noch, aber Fronten, Arbeitsplatte, Geräte oder Ausstattung sind in die Jahre gekommen? Dann prüfen wir zuerst, was sinnvoll erhalten werden kann und welche Veränderungen wirklich etwas verbessern.</p>
      <div class="kf-hero__cta">
        <a class="ms-button ms-button--light" href="#anfrage" data-track="kuechenfit_hero_check">Renovierung einschätzen lassen</a>
        <a class="ms-button ms-button--secondary" href="#moeglichkeiten" data-track="kuechenfit_hero_moeglichkeiten">Möglichkeiten ansehen</a>
      </div>
      <p class="kf-hero__note">Von einzelnen neuen Bauteilen bis zum abgestimmten Küchenumbau.</p>
    </div>
  </header>

  <main>
    <section class="ms-section">
      <div class="ms-wrap kf-prose">
        <h2>Erst klären, was an der Küche wirklich stört</h2>
        <p>Eine Küche besteht aus Bauteilen mit sehr unterschiedlicher Lebensdauer. Nicht alles ist gleichzeitig verschlissen oder technisch überholt.</p>
        <p>Vielleicht sind die Korpusse noch vollkommen in Ordnung, während die Fronten nicht mehr gefallen. Vielleicht funktioniert die gesamte Küche, aber die Arbeitsplatte ist beschädigt. Oder Optik und Geräte stimmen noch, während Stauraum und Arbeitsabläufe nicht mehr zum Alltag passen.</p>
        <p>Deshalb beginnen wir nicht mit der Frage <strong>„Was können wir alles austauschen?“</strong>, sondern mit <strong>„Was funktioniert noch – und was soll besser werden?“</strong></p>
        <p>So vermeiden wir zwei unnötig teure Wege: eine brauchbare Küche vollständig zu ersetzen oder viel Geld in Einzelmaßnahmen zu investieren, obwohl der eigentliche Grundriss das Problem ist.</p>
      </div>
    </section>

    <section class="ms-section ms-section--soft" id="moeglichkeiten">
      <div class="ms-wrap kf-prose">
        <h2>Was lässt sich an einer bestehenden Küche verändern?</h2>
        <p>Statt die Küche komplett zu ersetzen, verändern wir gezielt die Bereiche, die im Alltag oder in der Optik wirklich stören.</p>

        <h3>Küchenfronten erneuern</h3>
        <p>Neue Türen und Schubladenfronten verändern einen großen Teil der sichtbaren Küche, während geeignete vorhandene Korpusse weitergenutzt werden können. Dabei betrachten wir nicht nur die Frontfarbe, sondern auch Griffe, Wangen, Blenden, Sockel und die vorhandene Arbeitsplatte.</p>
        <p><a href="/kuechenfronten-austauschen/">Küchenfronten austauschen</a></p>

        <h3>Arbeitsplatte austauschen</h3>
        <p>Ist die Arbeitsplatte beschädigt oder passt sie nicht mehr zum neuen Materialkonzept, kann sie separat erneuert werden. Spüle, Armatur, Kochfeld, Nischenanschlüsse und Wandverlauf müssen dabei neu aufgenommen werden.</p>
        <p><a href="/arbeitsplatte-austauschen/">Küchenarbeitsplatte austauschen</a></p>

        <h3>Geräte erneuern</h3>
        <p>Auch einzelne Einbaugeräte lassen sich ersetzen, sofern Einbaumaße, Anschlüsse, Belüftung und vorhandene Möbel zum neuen Gerät passen.</p>

        <h3>Ausstattung verbessern</h3>
        <p>Nicht jede Renovierung muss sofort die sichtbaren Hauptflächen betreffen. Auch diese Veränderungen können sinnvoll sein:</p>
        <ul>
          <li>bessere Innenorganisation</li>
          <li>neue Auszüge oder Beschläge</li>
          <li>Mülltrennung</li>
          <li>neue Griffe</li>
          <li>Spüle und Armatur</li>
          <li>Arbeits- und Nischenbeleuchtung</li>
          <li>Planung zusätzlicher Steckdosen und Elektroanschlüsse</li>
          <li>einzelne Ergänzungsschränke</li>
        </ul>

        <h3>Küche umbauen</h3>
        <p>Manchmal reicht der reine Austausch nicht. Soll ein Hochschrank ergänzt, ein Gerät versetzt, die Arbeitsfläche verlängert oder die Küche an einen veränderten Grundriss angepasst werden, sprechen wir von einem Küchenumbau.</p>
        <p><a href="/kuechenumbau/">Bestehende Küche umbauen</a></p>

        <p>Sie sind noch nicht sicher, welche dieser Maßnahmen zu Ihrer Küche passt? Für die erste Anfrage müssen Sie das nicht selbst entscheiden.</p>
        <p><a class="ms-button" href="#anfrage" data-track="kuechenfit_options_cta">Küche einschätzen lassen</a></p>
      </div>
    </section>

    <section class="ms-section">
      <div class="ms-wrap kf-prose">
        <h2>Wann lohnt sich die Renovierung?</h2>
        <p>Eine gute Ausgangslage besteht häufig, wenn:</p>
        <ul>
          <li>Korpusse stabil und trocken sind,</li>
          <li>die vorhandene Aufteilung grundsätzlich funktioniert,</li>
          <li>Beschläge noch brauchbar sind oder ersetzt werden können,</li>
          <li>hauptsächlich Oberfläche, Arbeitsplatte oder Ausstattung stören,</li>
          <li>die Küche technisch sinnvoll weiterentwickelt werden kann.</li>
        </ul>
        <h3>Und wann eher nicht?</h3>
        <p>Auch das gehört zu einer vernünftigen Beratung. Wenn Korpusse stark beschädigt sind, Feuchtigkeitsschäden bestehen, die Küchenform heute nicht mehr funktioniert oder ohnehin sämtliche Anschlüsse und Schrankpositionen verändert werden sollen, kann eine neue Küche die bessere Grundlage sein.</p>
        <p>Eine Renovierung ist <strong>nicht automatisch sinnvoller, nur weil mehr erhalten bleibt</strong>. Ob Renovieren, Umbauen oder Neuplanen der richtige Weg ist, klären wir in der Bestandsprüfung – siehe <a href="/renovieren-oder-neue-kueche/">Renovieren oder neue Küche?</a></p>
      </div>
    </section>

    <section class="ms-section ms-section--soft">
      <div class="ms-wrap kf-prose">
        <h2>Was kostet es, eine Küche renovieren zu lassen?</h2>
        <p>Eine Küchenrenovierung kann vom Austausch einzelner Bauteile bis zum umfangreichen Umbau reichen. Deshalb wäre ein pauschaler Preis ohne Kenntnis der vorhandenen Küche wenig aussagekräftig.</p>
        <p>Für die Kosten sind unter anderem entscheidend:</p>
        <ul>
          <li>Größe und Aufbau der vorhandenen Küche,</li>
          <li>Zustand von Korpussen, Beschlägen und Anschlüssen,</li>
          <li>Anzahl und Ausführung neuer Fronten,</li>
          <li>Material und Form einer neuen Arbeitsplatte,</li>
          <li>neue Geräte, Spüle, Armatur oder Beleuchtung,</li>
          <li>notwendige Demontage- und Anpassungsarbeiten,</li>
          <li>Ergänzung oder Veränderung vorhandener Schränke.</li>
        </ul>
        <p>Nach der Bestandsprüfung lässt sich der Umfang eingrenzen. Dann kann auch ehrlich verglichen werden, ob die geplante Renovierung wirtschaftlich zur vorhandenen Küche passt. Details zu den einzelnen Posten stehen unter <a href="/kuechenrenovierung-kosten/">Küchenrenovierung Kosten</a>.</p>
      </div>
    </section>

    <section class="ms-section">
      <div class="ms-wrap">
        <p class="ms-kicker">Der Weg zur passenden Lösung</p>
        <h2>So läuft die Küchenrenovierung ab</h2>
        <div class="ms-process">
          <article><span>1</span><div><h3>Küche zeigen</h3><p>Fotos, vorhandene Unterlagen und Ihre Beschreibung geben uns einen ersten Eindruck.</p></div></article>
          <article><span>2</span><div><h3>Ausgangslage prüfen</h3><p>Wir betrachten Korpusse, Maße, Beschläge, Arbeitsplatte, Geräte und mögliche Anschlusspunkte.</p></div></article>
          <article><span>3</span><div><h3>Renovierungsumfang festlegen</h3><p>Gemeinsam wird entschieden, was erhalten bleibt und was sich ändern soll.</p></div></article>
          <article><span>4</span><div><h3>Aufmaß und Materialauswahl</h3><p>Neue Bauteile und erforderliche Anpassungen werden passend zum Bestand geplant.</p></div></article>
          <article><span>5</span><div><h3>Renovierung umsetzen</h3><p>Die vereinbarten Bauteile werden demontiert, angepasst und montiert.</p></div></article>
        </div>
      </div>
    </section>

    <section class="ms-section ms-section--dark" id="anfrage">
      <div class="ms-wrap ms-grid ms-grid--2">
        <div>
          <p class="ms-eyebrow">Ersteinschätzung anfragen</p>
          <h2>Was ist bei Ihrer Küche sinnvoll?</h2>
          <p class="ms-copy">Sie müssen vor der Anfrage nicht wissen, ob neue Fronten, eine andere Arbeitsplatte oder ein größerer Umbau erforderlich sind. Zeigen Sie uns die Küche und beschreiben Sie, was Sie stört oder im Alltag nicht mehr funktioniert.</p>
          <p class="ms-copy">KüchenFit ist ein Service von Klas Küchen®. Die Bestandsprüfung verbindet Küchenplanung mit dem handwerklichen Blick des Tischlermeisters auf Konstruktion, Substanz und Umsetzbarkeit.</p>
          <p class="ms-copy">Fotos brauchen Sie hier nicht hochzuladen: Nach dem Absenden erhalten Sie eine Bestätigungs-E-Mail – schicken Sie Ihre Küchenfotos einfach als Antwort darauf. Hilfreich sind Gesamtaufnahmen sowie Fotos von beschädigten Bauteilen, Ecken, Geräten und Bereichen, die verändert werden sollen.</p>
        </div>
        <?php require $ROOT . '/partials/anfrage-form.php'; ?>
      </div>
    </section>

    <section class="ms-section ms-section--soft ms-faq">
      <div class="ms-wrap">
        <h2>Häufige Fragen zur Küchenrenovierung</h2>
        <?php foreach ($faqItems as $faq): ?>
        <details><summary><?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8') ?></summary><p><?= htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8') ?></p></details>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="ms-section">
      <div class="ms-wrap kf-prose">
        <h2>Erst prüfen. Dann entscheiden.</h2>
        <p>Vielleicht braucht Ihre Küche nur neue Fronten. Vielleicht mehr. Vielleicht wäre eine Neuplanung vernünftiger. Genau das klären wir, bevor unnötig Teile bestellt werden.</p>
        <p><a class="ms-button" href="#anfrage" data-track="kuechenfit_footer_cta">Küchenrenovierung anfragen</a></p>
      </div>
    </section>
  </main>

  <?php require $ROOT . '/partials/landing-mini-footer.php'; ?>
  <script src="/assets/js/modernisierung-lp.js?v=<?= rawurlencode((string)filemtime($ROOT . '/assets/js/modernisierung-lp.js')) ?>" defer></script>
</body>
</html>
