<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/consent.php';

$consentPublicConfig = kk_consent_public_config();
$consentJson = json_encode(
    $consentPublicConfig,
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR
);
?>
<script>window.KLAS_CONSENT_CONFIG=<?= $consentJson ?>;</script>
<script src="<?= htmlspecialchars(kk_asset('js/klas-tracking.js'), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(kk_asset('vendor/cookieconsent/cookieconsent.umd.js'), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(kk_asset('js/klas-consent.js'), ENT_QUOTES, 'UTF-8') ?>" defer></script>
<script src="<?= htmlspecialchars(kk_asset('js/klas-components.js'), ENT_QUOTES, 'UTF-8') ?>" defer></script>
