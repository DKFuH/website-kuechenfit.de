<?php
declare(strict_types=1);

/**
 * Kompakte Leistungsnavigation fuer die KüchenFit-Inhaltsseiten.
 * Gibt Nutzern auf Unterseiten eine Orientierung ueber das Leistungscluster,
 * statt nur Marke + CTA in der Topbar zu zeigen.
 *
 * Optional vor dem require setzbar:
 *   $kfNavCurrent – Pfad der aktuellen Seite (z. B. '/kuechenrenovierung/'),
 *                   markiert den aktiven Punkt via aria-current.
 *
 * Styling: .kf-leistungsnav aus kuechenfit-base.css
 */

$kfNavCurrent = $kfNavCurrent ?? '';
$kfNavItems = [
    '/kuechenrenovierung/' => 'Küche renovieren',
    '/kuechenfronten-austauschen/' => 'Fronten austauschen',
    '/arbeitsplatte-austauschen/' => 'Arbeitsplatte',
    '/kuechenumbau/' => 'Küche umbauen',
    '/kuechenrenovierung-kosten/' => 'Kosten',
    '/renovieren-oder-neue-kueche/' => 'Renovieren oder neu?',
];
?>
<nav class="kf-leistungsnav" aria-label="KüchenFit Leistungen">
  <div class="kf-leistungsnav__track">
    <?php foreach ($kfNavItems as $href => $label): ?>
      <a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>"<?= $kfNavCurrent === $href ? ' aria-current="page"' : '' ?> data-track="kuechenfit_leistungsnav"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
    <?php endforeach; ?>
  </div>
</nav>
