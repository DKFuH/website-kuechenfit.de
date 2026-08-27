(() => {
  'use strict';

  function isAccepted(service, category) {
    return Boolean(
      window.CookieConsent
      && typeof window.CookieConsent.acceptedService === 'function'
      && window.CookieConsent.acceptedService(service, category)
    );
  }

  function updateComponent(component) {
    const service = component.dataset.consentService || '';
    const category = component.dataset.consentCategory || '';
    const content = component.querySelector('[data-consent-content]');
    const placeholder = component.querySelector('[data-consent-placeholder]');
    if (!service || !category || !content || !placeholder) return;

    const accepted = isAccepted(service, category);
    content.hidden = !accepted;
    placeholder.hidden = accepted;

    const iframe = content.querySelector('iframe[data-src]');
    if (!iframe) return;
    if (accepted && !iframe.src) iframe.src = iframe.dataset.src || '';
    if (!accepted) iframe.removeAttribute('src');
  }

  function updateAll() {
    document.querySelectorAll('[data-consent-service]').forEach(updateComponent);
  }

  document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-open-consent-preferences]');
    if (!button || !window.CookieConsent) return;
    window.CookieConsent.showPreferences();
  });

  window.addEventListener('klas:consent-applied', updateAll);
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', updateAll, { once: true });
  else updateAll();
})();
