(() => {
  'use strict';

  const allowedEvents = new Set([
    'page_view', 'scroll_depth', 'ui_click', 'filter_select',
    'form_start', 'form_submit_attempt', 'form_submit_success', 'form_submit_error',
    'booking_type_select', 'booking_completed', 'tool_start', 'tool_step', 'tool_completed'
  ]);
  const allowedProperties = new Set([
    'page_path', 'page_type', 'service_category', 'element_id', 'position', 'link_type',
    'filter_id', 'filter_value', 'form_id', 'booking_type', 'tool_id', 'step', 'result',
    'depth', 'error_code', 'duration_bucket'
  ]);
  const blockedKey = /(email|phone|name|message|request|token|query|search|referrer|url)/i;
  const eventPropertyKeys = [...allowedProperties].filter((key) => key !== 'page_path');
  const campaignParameters = {
    mtm_campaign: ['mtm_campaign', 'utm_campaign'],
    mtm_source: ['mtm_source', 'utm_source'],
    mtm_medium: ['mtm_medium', 'utm_medium'],
    mtm_keyword: ['mtm_keyword', 'mtm_kwd', 'utm_term'],
    mtm_content: ['mtm_content', 'utm_content'],
    mtm_cid: ['mtm_cid', 'utm_id'],
    mtm_group: ['mtm_group'],
    mtm_placement: ['mtm_placement']
  };
  const state = { acceptedProviders: new Set(), mtmAllowed: false, started: false, pageViewSent: false };

  function safePath() {
    return String(window.location.pathname || '/').replace(/[^a-zA-Z0-9/_\-.]/g, '').slice(0, 160) || '/';
  }

  function safeCampaignValue(value) {
    const normalized = String(value || '').normalize('NFC').trim();
    if (normalized === '' || /[@\r\n\0]/.test(normalized)) return '';
    return normalized.replace(/[^\p{L}\p{N}._~-]/gu, '_').slice(0, 80);
  }

  function safeTrackingUrl() {
    const source = new URLSearchParams(window.location.search);
    const campaign = new URLSearchParams();
    Object.entries(campaignParameters).forEach(([target, candidates]) => {
      const sourceKey = candidates.find((candidate) => source.has(candidate));
      if (!sourceKey) return;
      const value = safeCampaignValue(source.get(sourceKey));
      if (value !== '') campaign.set(target, value);
    });
    const query = campaign.toString();
    return safePath() + (query === '' ? '' : `?${query}`);
  }

  function safeValue(value) {
    if (typeof value === 'number') return Number.isFinite(value) ? value : null;
    if (typeof value === 'boolean') return value;
    if (typeof value !== 'string') return null;
    return value.normalize('NFC').replace(/[^a-zA-Z0-9äöüÄÖÜß/_\-.]/g, '_').slice(0, 80);
  }

  function sanitize(properties) {
    const clean = { page_path: safePath() };
    Object.entries(properties || {}).forEach(([key, value]) => {
      if (!allowedProperties.has(key) || blockedKey.test(key)) return;
      const sanitized = safeValue(value);
      if (sanitized !== null && sanitized !== '') clean[key] = sanitized;
    });
    return clean;
  }

  function eventCategory(eventName) {
    if (eventName.startsWith('form_')) return 'form';
    if (eventName.startsWith('booking_')) return 'booking';
    if (eventName.startsWith('tool_')) return 'tool';
    return 'engagement';
  }

  function eventLabel(eventName, properties) {
    const candidates = {
      scroll_depth: ['depth'],
      ui_click: ['element_id', 'position', 'link_type'],
      filter_select: ['filter_id', 'filter_value'],
      form_start: ['form_id'],
      form_submit_attempt: ['form_id'],
      form_submit_success: ['form_id'],
      form_submit_error: ['form_id', 'error_code'],
      booking_type_select: ['booking_type'],
      booking_completed: ['booking_type'],
      tool_start: ['tool_id'],
      tool_step: ['tool_id', 'step'],
      tool_completed: ['tool_id', 'result']
    };
    return (candidates[eventName] || [])
      .map((key) => properties[key])
      .filter((value) => value !== undefined && value !== null && value !== '')
      .join('|')
      .slice(0, 160);
  }

  function track(eventName, properties = {}) {
    if (!allowedEvents.has(eventName) || !state.mtmAllowed) return false;
    const clean = sanitize(properties);
    window._mtm = window._mtm || [];
    window._mtm.push({
      event: `klas.${eventName}`,
      event_name: eventName,
      event_category: eventCategory(eventName),
      event_label: eventLabel(eventName, clean),
      tracking_url: safeTrackingUrl(),
      ...Object.fromEntries(eventPropertyKeys.map((key) => [key, null])),
      ...clean
    });
    return true;
  }

  function pageContext() {
    const classes = document.body ? document.body.className : '';
    let pageType = 'default';
    if (classes.includes('page-home')) pageType = 'home';
    else if (classes.includes('page-service')) pageType = 'service';
    else if (classes.includes('page-tool')) pageType = 'tool';
    else if (classes.includes('page-landing')) pageType = 'landing';
    else if (classes.includes('page-kontakt')) pageType = 'kontakt';
    else if (classes.includes('page-termin') || classes.includes('page-booking')) pageType = 'termin';
    const meta = document.querySelector('meta[name="service-category"]');
    return { page_type: pageType, service_category: meta?.content || 'general' };
  }

  function positionFor(element) {
    if (element.dataset.position) return element.dataset.position;
    if (element.closest('.hero')) return 'hero';
    if (element.closest('.topbar, header')) return 'header';
    if (element.closest('.footer, footer')) return 'footer';
    if (element.closest('.section, section')) return 'section';
    return 'unknown';
  }

  function initializeListeners() {
    if (state.started) return;
    state.started = true;
    if (!state.pageViewSent) {
      state.pageViewSent = true;
      track('page_view', pageContext());
    }

    document.addEventListener('click', (event) => {
      const element = event.target.closest('[data-track]');
      if (!element) return;
      if (element.matches('button[type="submit"], input[type="submit"]') && element.closest('form')) return;
      const href = element instanceof HTMLAnchorElement ? element.getAttribute('href') || '' : '';
      let linkType = 'button';
      if (href.startsWith('tel:')) linkType = 'phone';
      else if (href.startsWith('mailto:')) linkType = 'email';
      else if (/^https?:\/\//i.test(href)) linkType = 'external';
      else if (href) linkType = 'internal';
      track('ui_click', {
        element_id: element.getAttribute('data-track') || 'unknown',
        position: positionFor(element),
        link_type: linkType,
        ...pageContext()
      });
    }, { passive: true });

    document.querySelectorAll('form').forEach((form) => {
      const formId = form.getAttribute('data-form-id') || form.id || 'form';
      form.addEventListener('focusin', () => track('form_start', { form_id: formId }), { once: true });
      form.addEventListener('submit', () => track('form_submit_attempt', { form_id: formId }));
    });

    const fired = new Set();
    const milestones = [25, 50, 75, 90];
    window.addEventListener('scroll', () => {
      const height = document.documentElement.scrollHeight - window.innerHeight;
      if (height <= 0) return;
      const percent = Math.round(((window.scrollY || document.documentElement.scrollTop) / height) * 100);
      milestones.forEach((depth) => {
        if (percent >= depth && !fired.has(depth)) {
          fired.add(depth);
          track('scroll_depth', { depth });
        }
      });
    }, { passive: true });
  }

  function setConsent(acceptedProviders, mtmAllowed) {
    state.acceptedProviders = new Set(Array.isArray(acceptedProviders) ? acceptedProviders : []);
    state.mtmAllowed = Boolean(mtmAllowed);
    if (!state.mtmAllowed) return;
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeListeners, { once: true });
    else initializeListeners();
  }

  window.KlasTracking = Object.freeze({
    track,
    setConsent,
    isAllowed: (providerId) => state.acceptedProviders.has(String(providerId))
  });
  window.trackEvent = (eventName, properties = {}) => track(eventName, properties);
})();
