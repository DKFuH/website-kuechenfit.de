(() => {
  'use strict';

  const config = window.KLAS_CONSENT_CONFIG;
  if (!config || !window.CookieConsent || !window.KlasTracking) return;
  let mtmLoaded = false;

  const escapeHtml = (value) => String(value).replace(/[&<>'"]/g, (character) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
  }[character]));

  function cookiePatterns(patterns) {
    return (patterns || []).map((value) => {
      if (typeof value !== 'string') return null;
      if (value.startsWith('/') && value.endsWith('/') && value.length > 2) {
        try { return { name: new RegExp(value.slice(1, -1)) }; } catch (_) { return null; }
      }
      return { name: value };
    }).filter(Boolean);
  }

  function serviceLabel(provider) {
    return `<strong>${escapeHtml(provider.name)}</strong><br>` +
      `<small>${escapeHtml(provider.purpose)} Daten: ${escapeHtml(provider.data)} ` +
      `Empfänger: ${escapeHtml(provider.recipient)} Drittland: ${escapeHtml(provider.thirdCountry)} ` +
      `Speicherung: ${escapeHtml(provider.storage)} Speicherdauer: ${escapeHtml(provider.retention)} ` +
      `<a href="${escapeHtml(provider.privacyUrl)}">Datenschutz</a></small>`;
  }

  const categories = {};
  Object.entries(config.categories).forEach(([categoryId, category]) => {
    const services = {};
    Object.values(config.providers).filter((provider) => provider.category === categoryId).forEach((provider) => {
      services[provider.id] = {
        label: serviceLabel(provider),
        cookies: cookiePatterns(provider.cookies)
      };
    });
    categories[categoryId] = {
      readOnly: Boolean(category.read_only),
      enabled: categoryId === 'necessary',
      services
    };
  });

  function currentSelection() {
    const preferences = window.CookieConsent.getUserPreferences();
    const acceptedProviders = [];
    Object.values(config.providers).forEach((provider) => {
      if (window.CookieConsent.acceptedService(provider.id, provider.category)) acceptedProviders.push(provider.id);
    });
    return { preferences, acceptedProviders };
  }

  function loadMtm(acceptedProviders) {
    const allowedMtmProviders = acceptedProviders.filter((id) => config.providers[id]?.managedBy === 'mtm');
    window.KlasTracking.setConsent(acceptedProviders, allowedMtmProviders.length > 0);
    if (allowedMtmProviders.length === 0 || mtmLoaded || !config.mtm.container) return;
    mtmLoaded = true;
    window._mtm = window._mtm || [];
    window._mtm.push({
      event: 'klas.consent_ready',
      consent_revision: config.revision,
      consent_material_version: config.materialVersion,
      consent_providers: allowedMtmProviders.join(',')
    });
    window._mtm.push({ 'mtm.startTime': Date.now(), event: 'mtm.Start' });
    const script = document.createElement('script');
    script.async = true;
    script.src = `${config.mtm.baseUrl}js/container_${encodeURIComponent(config.mtm.container)}.js`;
    script.referrerPolicy = 'no-referrer';
    document.head.appendChild(script);
  }

  function applyConsent() {
    const { acceptedProviders } = currentSelection();
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      event: 'klas.google_consent_default',
      ad_storage: 'denied',
      analytics_storage: 'denied',
      ad_user_data: 'denied',
      ad_personalization: 'denied'
    });
    loadMtm(acceptedProviders);
    window.dispatchEvent(new CustomEvent('klas:consent-applied', {
      detail: { acceptedProviders: [...acceptedProviders] }
    }));
  }

  function logConsent(action) {
    const cookie = window.CookieConsent.getCookie();
    const preferences = window.CookieConsent.getUserPreferences();
    return fetch(config.receiptEndpoint, {
      method: 'POST',
      credentials: 'same-origin',
      keepalive: true,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        action,
        consentId: cookie.consentId,
        revision: config.revision,
        acceptedCategories: preferences.acceptedCategories,
        rejectedCategories: preferences.rejectedCategories,
        acceptedServices: preferences.acceptedServices,
        rejectedServices: preferences.rejectedServices
      })
    }).catch(() => undefined);
  }

  const sections = [{
    title: 'Deine Datenschutzeinstellungen',
    description: 'Du entscheidest, welche optionalen Dienste geladen werden. Deine Auswahl kannst du jederzeit im Footer ändern.'
  }];
  Object.entries(config.categories).forEach(([categoryId, category]) => {
    sections.push({
      title: category.label,
      description: category.description,
      linkedCategory: categoryId
    });
  });

  window.CookieConsent.run({
    mode: 'opt-in',
    revision: config.revision,
    manageScriptTags: false,
    autoClearCookies: true,
    disablePageInteraction: true,
    cookie: {
      name: config.cookie.name,
      expiresAfterDays: config.cookie.expires_after_days,
      sameSite: 'Lax',
      secure: window.location.protocol === 'https:'
    },
    guiOptions: {
      consentModal: { layout: 'box wide', position: 'middle center', equalWeightButtons: false },
      preferencesModal: { layout: 'box', equalWeightButtons: false }
    },
    categories,
    language: {
      default: 'de',
      translations: {
        de: {
          consentModal: {
            label: 'Datenschutzeinstellungen',
            title: 'Du entscheidest, was geladen wird',
            description: 'Wir nutzen Cookies und externe Dienste, um dir die bestmögliche Planung und Nutzererfahrung zu bieten. Notwendige Funktionen laufen dafür immer, alles andere startet erst nach deiner Auswahl.',
            acceptAllBtn: 'Alle akzeptieren & fortfahren',
            acceptNecessaryBtn: 'Nur essenzielle Cookies',
            showPreferencesBtn: 'Auswahl festlegen',
            footer: '<a href="/datenschutz/">Datenschutz</a><a href="/impressum/">Impressum</a>'
          },
          preferencesModal: {
            title: 'Datenschutzeinstellungen',
            acceptAllBtn: 'Alle akzeptieren & fortfahren',
            acceptNecessaryBtn: 'Nur essenzielle Cookies',
            savePreferencesBtn: 'Auswahl speichern',
            closeIconLabel: 'Einstellungen schließen',
            serviceCounterLabel: 'Dienst|Dienste',
            sections
          }
        }
      }
    },
    onFirstConsent: () => {
      applyConsent();
      logConsent('created');
    },
    onConsent: applyConsent,
    onChange: () => {
      const { acceptedProviders } = currentSelection();
      window.KlasTracking.setConsent(acceptedProviders, false);
      logConsent('changed');
      window.setTimeout(() => window.location.reload(), 180);
    }
  });
})();
