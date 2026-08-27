<?php
declare(strict_types=1);
?>
<style>
.landing-mini-footer__button {
  display: inline-flex;
  align-items: center;
  padding: 5px 14px;
  border: 1px solid rgba(255, 255, 255, 0.45);
  border-radius: 9999px;
  background: transparent;
  color: #fff;
  font: inherit;
  text-decoration: none;
  cursor: pointer;
  transition: border-color 0.2s ease, background 0.2s ease;
}
.landing-mini-footer__button:hover,
.landing-mini-footer__button:focus-visible {
  border-color: #fff;
  background: rgba(255, 255, 255, 0.14);
}
</style>
<footer class="landing-mini-footer" aria-label="Rechtliche Informationen">
  <span>© <?= date('Y') ?> KüchenFit – ein Service von Klas Küchen</span>
  <nav aria-label="Rechtliche Links">
    <a href="/impressum/" data-track="landing_footer_impressum">Impressum</a>
    <a href="/datenschutz/" data-track="landing_footer_datenschutz">Datenschutz</a>
    <button type="button" data-cc="show-preferencesModal" class="landing-mini-footer__button">Datenschutz-Einstellungen</button>
  </nav>
</footer>
<?php require __DIR__ . '/tracking.php'; ?>
