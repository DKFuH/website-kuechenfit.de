(() => {
  'use strict';

  const params = new URLSearchParams(window.location.search);
  document.querySelectorAll('[data-campaign-field]').forEach((field) => {
    const key = field.dataset.campaignField;
    const maxLength = ['fbclid', 'gclid', 'wbraid', 'gbraid', 'msclkid', 'epik'].includes(key) ? 180 : 100;
    field.value = (params.get(key) || '').slice(0, maxLength);
  });
  document.querySelectorAll('[name="page_url"]').forEach((field) => { field.value = window.location.href.slice(0, 500); });
  document.querySelectorAll('[name="referrer"]').forEach((field) => { field.value = (document.referrer || '').slice(0, 500); });

  const track = (name, properties = {}) => {
    if (typeof window.trackEvent === 'function') window.trackEvent(name, properties);
  };

  document.querySelectorAll('[data-preset-service]').forEach((link) => {
    link.addEventListener('click', () => {
      const select = document.querySelector('select[name="service"]');
      if (select) select.value = link.dataset.presetService;
    });
  });

  document.querySelectorAll('[data-modernisierung-form]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      if (!form.reportValidity()) return;
      const button = form.querySelector('button[type="submit"]');
      const message = form.querySelector('[data-form-message]');
      const original = button.textContent;
      button.disabled = true;
      button.textContent = 'Wird sicher gesendet …';
      message.textContent = '';
      message.classList.remove('is-success');
      try {
        const response = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json' } });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.ok) throw new Error(data.message || 'Die Anfrage konnte nicht übermittelt werden. Bitte versuchen Sie es erneut.');
        let prepBullets = [];
        try { prepBullets = JSON.parse(form.dataset.prepBullets || '[]'); } catch { prepBullets = []; }
        const list = prepBullets.length
          ? `<ul class="ms-message__list">${prepBullets.map((item) => `<li>${item}</li>`).join('')}</ul>`
          : '';
        message.innerHTML = `<span class="ms-message__title">Vielen Dank. Wir melden uns persönlich bei Ihnen.</span><span class="ms-message__lead">Eine Bestätigungs-E-Mail ist unterwegs. Antworten Sie darauf mit Ihren Küchenfotos – dann wird das Gespräch schnell konkret:</span>${list}`;
        message.classList.add('is-success');
        form.reset();
        track('form_submit_success', { form_id: form.dataset.formId || 'modernisierung' });
      } catch (error) {
        message.textContent = error instanceof Error ? error.message : 'Bitte versuchen Sie es erneut.';
        track('form_submit_error', { form_id: form.dataset.formId || 'modernisierung', error_code: 'submit_failed' });
      } finally {
        button.disabled = false;
        button.textContent = original;
      }
    });
  });

  function initDiagnosisQuiz(root, { toolId, evaluate, prefill }) {
    if (!root) return;
    const steps = [...root.querySelectorAll('[data-quiz-step]')];
    const progressBar = root.querySelector('[data-quiz-progress]');
    const result = root.querySelector('[data-quiz-result]');
    const backButton = root.querySelector('[data-quiz-back]');
    const badge = root.querySelector('[data-quiz-badge]');
    const text = root.querySelector('[data-quiz-text]');
    const answers = {};
    let current = 1;
    let started = false;

    const showStep = (n) => {
      current = n;
      steps.forEach((step) => { step.hidden = Number(step.dataset.quizStep) !== n; });
      result.hidden = true;
      backButton.hidden = n === 1;
      progressBar.style.width = `${((n - 1) / steps.length) * 100}%`;
    };

    const finish = () => {
      progressBar.style.width = '100%';
      steps.forEach((step) => { step.hidden = true; });
      backButton.hidden = true;
      result.hidden = false;
      const { outcome, badgeText, resultText } = evaluate(answers);
      badge.textContent = badgeText;
      text.textContent = resultText;
      track('tool_completed', { tool_id: toolId, result: outcome });
      if (prefill) prefill(answers);
    };

    root.querySelectorAll('.ms-quiz-options').forEach((group) => {
      const name = group.dataset.quizName;
      group.querySelectorAll('.ms-quiz-option').forEach((button) => {
        button.addEventListener('click', () => {
          if (!started) { started = true; track('tool_start', { tool_id: toolId }); }
          answers[name] = button.dataset.value;
          group.querySelectorAll('.ms-quiz-option').forEach((b) => b.classList.toggle('is-selected', b === button));
          track('tool_step', { tool_id: toolId, step: String(current) });
          window.setTimeout(() => { current < steps.length ? showStep(current + 1) : finish(); }, 150);
        });
      });
    });

    backButton.addEventListener('click', () => { if (current > 1) showStep(current - 1); });
    showStep(1);
  }

  initDiagnosisQuiz(document.querySelector('[data-fit-quiz]'), {
    toolId: 'kuechenfit_check',
    evaluate: (answers) => {
      const heavyDamage = answers.zustand === 'Mehrere Bauteile sind deutlich beschädigt';
      const layoutBroken = answers.aufteilung === 'Nein';
      const layoutOk = answers.aufteilung === 'Ja' || answers.aufteilung === 'Teilweise';
      const goodSubstance = answers.zustand === 'Korpusse und Fronten sind grundsätzlich intakt' || answers.zustand === 'Es gibt kleinere Schäden';
      const wantsUmbau = answers.umbauumfang === 'Die Küche soll deutlich umgebaut werden';
      const wantsPartialChange = answers.umbauumfang === 'Einzelne Bereiche sollen verändert werden';
      const unsure = answers.anliegen === 'Ich weiß nicht, ob eine Renovierung noch sinnvoll ist'
        || answers.zustand === 'Kann ich nicht beurteilen'
        || answers.umbauumfang === 'Kann ich noch nicht beurteilen';

      if ((heavyDamage && layoutBroken) || (layoutBroken && wantsUmbau)) {
        return { outcome: 'vergleich', badgeText: 'Mit Neuplanung vergleichen', resultText: 'Wenn Substanz und Aufteilung nicht mehr passen und ohnehin viel verändert werden soll, sollten Renovierung und Neuplanung ehrlich verglichen werden. Genau diese Abwägung nehmen wir in der Bestandsprüfung vor.' };
      }
      if (goodSubstance && !layoutBroken && (wantsUmbau || wantsPartialChange)) {
        return { outcome: 'umbau', badgeText: 'Küchenumbau prüfen', resultText: 'Die Substanz ist eine gute Grundlage, aber einzelne Bereiche sollen anders angeordnet werden. Dann ist ein gezielter Küchenumbau der wahrscheinlich passende Weg – das prüfen wir am Bestand.' };
      }
      if (goodSubstance && layoutOk && !unsure) {
        return { outcome: 'gezielt', badgeText: 'Gezielte Renovierung möglich', resultText: 'Aufteilung und Substanz sprechen für eine gezielte Renovierung: Fronten, Arbeitsplatte, Geräte oder Ausstattung erneuern, während geeignete Teile erhalten bleiben.' };
      }
      return { outcome: 'pruefen', badgeText: 'Zustand vorab prüfen', resultText: 'Der genaue Zustand entscheidet, ob eine Renovierung ausreicht oder weitere Bauteile betroffen sind. Das lässt sich am besten anhand von Fotos einschätzen.' };
    },
    prefill: (answers) => {
      const details = document.querySelector('textarea[name="details"]');
      if (details && !details.value) {
        details.value = `Anliegen: ${answers.anliegen} · Zustand: ${answers.zustand} · Aufteilung passt: ${answers.aufteilung} · Umbauwunsch: ${answers.umbauumfang}`;
      }
    }
  });
})();
