# kuechenfit.de

Eigenstaendiges Projekt fuer die KüchenFit-Landingpage (Küchenmodernisierung),
herausgeloest aus dem gemeinsamen `kuechen-klas.de-2026`-Webroot. Gehoert zu
Klas Küchen® / Daniel Klas, Sohren.

## Stand

- Lokal vollstaendig getestet: PHP-Lint, Rendering, Formular-Roundtrip
  (eigene SQLite-DB unter `storage/`, unabhaengig von kuechen-klas.de und
  kuechenfolieren.de), robots-Meta `index,follow`, Canonical korrekt.
- `.htaccess` ist fuer echtes Apache-Hosting geschrieben; der 404-Handler
  laesst sich mit dem PHP-Buildin-Server (`php -S`) ohne Router-Skript nicht
  1:1 nachstellen -- auf dem echten Server pruefen.

## Vor dem Produktiv-Deployment noch offen

- [ ] `.env` aus `.env.example` anlegen (SMTP-Zugangsdaten, Matomo-ID,
      ProvenExpert-Zugangsdaten optional)
- [ ] Eigenes Matomo-Website-Objekt anlegen (fuer domain-getrennte
      Auswertung statt der geteilten Container-ID aus dem Hauptprojekt)
- [ ] Vorher-/Nachher-Fotos eines echten Projekts ergaenzen (aktuell
      Platzhalter in `public/index.php`, Abschnitt "Ein Projekt im Detail")
- [ ] Hosting beim Domain-Provider von kuechenfit.de einrichten, Projekt
      hochladen, `.env` dort mit Produktionswerten anlegen
- [ ] Google Search Console Property fuer kuechenfit.de anlegen, Sitemap
      einreichen

## Architektur

Bewusst dieselbe schlanke PHP-Struktur wie das Hauptprojekt, kein Framework:

```
public/
  index.php              Startseite (komplette Landingpage)
  impressum/index.php    Impressum (itrk.legal-Einbettung, gleiche
  datenschutz/index.php  Rechtsperson wie kuechen-klas.de)
  api/
    modernisierung_submit.php   Formular-Adapter (Validierung, Themen-Text)
    contact_submit.php          Generische Zustell-Pipeline (CSRF, Rate-
                                 Limit, SQLite, Mailer, optionaler Webhook)
    consent-receipt.php         Speichert Cookie-Consent-Entscheidungen
  partials/              head.php, url.php, tracking.php, landing-mini-footer.php
  assets/                CSS/JS/Bilder/Icons/Cookieconsent-Vendor-Lib
app/
  mailer.php, consent.php, business.php, provenexpert.php
  lib/phpmailer/         Drittanbieter-Bibliothek (unveraendert)
config/
  business.php, consent.php   Stammdaten (Rechtsperson identisch mit den
                               anderen beiden Marken, nur schema_url/
                               -image/-description sind domainspezifisch)
storage/                 SQLite-DB + JSON-Vault (git-ignoriert)
```

## Bekannte, bewusste Abweichungen zum Hauptprojekt

- `kk_canonical_host()` (Multi-Domain-Allowlist) entfaellt ersatzlos, da
  hier nur eine Domain existiert -- `url.php` ist entsprechend schlanker.
- `landing-mini-footer.php` verlinkt nur Impressum/Datenschutz/Consent-
  Einstellungen (keine `/bildquellen/`- oder `/widerruf/`-Seite, da es
  diese Seiten in diesem eigenstaendigen Projekt nicht gibt).
- Impressum/Datenschutz haben einen schlanken eigenen Header/Footer statt
  des vollen Hauptseiten-Menues (das hier inhaltlich nicht passen wuerde).
