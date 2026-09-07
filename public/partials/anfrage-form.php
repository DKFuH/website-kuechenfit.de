<?php
declare(strict_types=1);

/**
 * Wiederverwendbares KüchenFit-Anfrageformular.
 * Bindet an modernisierung-lp.js (Attribut data-modernisierung-form) und
 * postet an /api/modernisierung_submit.php.
 *
 * Optional vor dem require setzbar:
 *   $anfrageFormId        – Wert für data-form-id / Tracking (Default: 'kuechenfit_check')
 *   $anfragePresetService – vorausgewählter <select name="service">-Wert (Default: keiner)
 *   $anfragePrepBullets   – string[] für die Erfolgsmeldung (Default: Standardliste)
 *
 * Styling: .ms-check / .ms-form-grid / .ms-field … aus kuechenfit-base.css
 * (auf der Startseite aus modernisierung-lp.css).
 */

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$anfrageCsrf = (string) $_SESSION['csrf_token'];
$anfrageRenderedAt = time();
$anfrageFormId = $anfrageFormId ?? 'kuechenfit_check';
$anfragePresetService = $anfragePresetService ?? '';
$anfragePrepBullets = $anfragePrepBullets ?? [
    'Antworten Sie auf die Bestätigungs-E-Mail und hängen Sie Ihre Küchenfotos an',
    'Hilfreich: Gesamtaufnahme sowie Fotos von Fronten, Arbeitsplatte und beschädigten Stellen',
    'Ungefähre Maße der Bereiche, die sich verändern sollen',
];

// Werte müssen mit der Allowlist in api/modernisierung_submit.php übereinstimmen.
$anfrageServices = [
    'Folierung' => 'Fronten folieren',
    'Frontentausch' => 'Fronten tauschen',
    'Arbeitsplatte' => 'Arbeitsplatte erneuern',
    'Spüle und Armatur' => 'Spüle, Armatur oder Müllsystem',
    'Nischenrückwand' => 'Nischenrückwand erneuern',
    'Licht und Geräte' => 'Licht oder Geräte',
    'Stauraum und Funktion' => 'Stauraum oder Funktion',
    'Komplettmodernisierung' => 'Umfassende Modernisierung',
    'Entscheidungscheck' => 'Modernisieren oder neu planen?',
    'Bestandsprüfung' => 'Bestandsprüfung / Entscheidungshilfe',
];
$anfrageTimeframes = ['Sofort', '1–3 Monate', '3–6 Monate', 'Später', 'Noch offen'];
?>
<form class="ms-check" action="/api/modernisierung_submit.php" method="post" data-modernisierung-form data-form-id="<?= htmlspecialchars($anfrageFormId, ENT_QUOTES, 'UTF-8') ?>" data-prep-bullets='<?= htmlspecialchars(json_encode(array_values($anfragePrepBullets), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>' novalidate>
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($anfrageCsrf, ENT_QUOTES, 'UTF-8') ?>">
  <input type="hidden" name="form_rendered_at" value="<?= $anfrageRenderedAt ?>">
  <input type="hidden" name="page_url"><input type="hidden" name="referrer">
  <?php foreach (['utm_source','utm_medium','utm_campaign','utm_content','utm_term','fbclid','gclid','wbraid','gbraid','msclkid','epik'] as $field): ?><input type="hidden" name="<?= $field ?>" data-campaign-field="<?= $field ?>"><?php endforeach; ?>
  <div class="ms-hidden" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label><label>Leer lassen <input name="hp_field" tabindex="-1" autocomplete="off"></label></div>
  <div class="ms-form-grid">
    <label class="ms-field"><span>Name *</span><input name="name" autocomplete="name" minlength="2" maxlength="100" required></label>
    <label class="ms-field"><span>E-Mail *</span><input type="email" name="email" autocomplete="email" maxlength="254" required></label>
    <label class="ms-field"><span>Telefon</span><input type="tel" name="phone" autocomplete="tel" maxlength="30"></label>
    <label class="ms-field"><span>PLZ *</span><input name="plz" inputmode="numeric" pattern="[0-9]{5}" maxlength="5" required></label>
    <label class="ms-field ms-field--wide"><span>Was möchten Sie verändern? *</span><select name="service" required><option value="">Bitte wählen</option><?php foreach ($anfrageServices as $value => $label): ?><option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>"<?= $anfragePresetService === $value ? ' selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></label>
    <label class="ms-field ms-field--wide"><span>Was stört Sie aktuell? *</span><textarea name="details" minlength="10" maxlength="2000" required></textarea></label>
    <label class="ms-field"><span>Wann soll es losgehen? *</span><select name="zeitpunkt" required><option value="">Bitte wählen</option><?php foreach ($anfrageTimeframes as $tf): ?><option value="<?= htmlspecialchars($tf, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($tf, ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></label>
    <label class="ms-privacy"><input type="checkbox" name="consent" value="1" required><span>Ich habe die <a href="/datenschutz/" target="_blank" rel="noopener">Datenschutzerklärung</a> gelesen und akzeptiert. *</span></label>
    <p class="ms-capacity">Ich übernehme Beratungen persönlich – Termine sind auf Do/Fr/Sa begrenzt.</p>
    <p class="ms-form-hint">Fotos brauchen Sie hier nicht hochzuladen: Nach dem Absenden erhalten Sie eine Bestätigungs-E-Mail – schicken Sie Ihre Küchenfotos einfach als Antwort darauf.</p>
    <div class="ms-message" data-form-message role="status" aria-live="polite"></div>
    <button class="ms-button" type="submit">Küche prüfen lassen</button>
  </div>
</form>
