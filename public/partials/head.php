<?php
require_once __DIR__ . '/url.php';
require_once dirname(__DIR__, 2) . '/app/business.php';
kk_start_output_buffering();
$requestPath = kk_request_path();
$canonicalUrl = $canonicalUrl ?? ('https://kuechenfit.de' . ($requestPath === '/' ? '/' : rtrim($requestPath, '/') . '/'));
$businessSchema = klas_business_schema();
?>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?= htmlspecialchars($pageTitle ?? "KüchenFit") ?></title>
<meta name="description" content="<?= htmlspecialchars($pageDescription ?? "") ?>" />
<meta name="robots" content="<?= htmlspecialchars($robots ?? 'index,follow') ?>" />
<meta name="theme-color" content="#76533d" />
<link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>" />
<link rel="icon" href="<?= htmlspecialchars(kk_base_path('/favicon.ico')) ?>" sizes="any" />
<link rel="icon" href="<?= htmlspecialchars(kk_asset('img/etc/klas-favicon-32.png')) ?>" type="image/png" sizes="32x32" />
<link rel="icon" href="<?= htmlspecialchars(kk_asset('img/etc/klas-site-icon-192.png')) ?>" type="image/png" sizes="192x192" />
<link rel="icon" href="<?= htmlspecialchars(kk_asset('img/etc/klas-site-icon-512.png')) ?>" type="image/png" sizes="512x512" />
<link rel="apple-touch-icon" href="<?= htmlspecialchars(kk_asset('img/etc/klas-apple-touch-icon.png')) ?>" sizes="180x180" />

<?php if (empty($skipSiteCss)): ?>
<link rel="stylesheet" href="<?= htmlspecialchars(kk_asset('css/fonts.css')) ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(kk_asset('css/style.css')) ?>">
<?php endif; ?>
<link rel="stylesheet" href="<?= htmlspecialchars(kk_asset('vendor/cookieconsent/cookieconsent.css')) ?>" media="print" onload="this.media='all';">
<link rel="stylesheet" href="<?= htmlspecialchars(kk_asset('css/consent.css')) ?>" media="print" onload="this.media='all';">
<link rel="stylesheet" href="<?= htmlspecialchars(kk_asset('css/animations.min.css')) ?>" media="print" onload="this.media='all';">

<meta property="og:site_name" content="KüchenFit – ein Service von Klas Küchen" />
<meta property="og:locale" content="de_DE" />
<meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? "KüchenFit") ?>" />
<meta property="og:description" content="<?= htmlspecialchars($pageDescription ?? "") ?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>" />
<meta property="og:image" content="<?= htmlspecialchars($ogImage ?? 'https://kuechenfit.de/assets/og-image.jpg') ?>" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:image" content="<?= htmlspecialchars($ogImage ?? 'https://kuechenfit.de/assets/og-image.jpg') ?>" />

<script type="application/ld+json">
<?= json_encode($businessSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
</script>
