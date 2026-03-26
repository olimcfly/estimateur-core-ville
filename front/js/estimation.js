(() => {
  const cfg = window.ESTIMATION_CONFIG || {};
  const resultSection = document.getElementById('estimation-result');
  const popup = document.getElementById('lead-popup');
  const popupForm = popup ? popup.querySelector('form[data-role="lead-form"]') : null;

  if (!resultSection || !popup || !popupForm) {
    return;
  }

  const observer = new MutationObserver(() => {
    const isHidden = resultSection.hasAttribute('hidden');
    if (!isHidden) {
      popup.removeAttribute('hidden');
      observer.disconnect();
    }
  });

  observer.observe(resultSection, {
    attributes: true,
    attributeFilter: ['hidden'],
  });

  popupForm.addEventListener('submit', async (event) => {
    event.preventDefault();

    const endpoint = cfg?.endpoints?.lead || '/api/leads.php';
    const formData = new FormData(popupForm);
    formData.append('ville', cfg.ville || '');

    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
      });

      const payload = await response.json();
      if (!response.ok || payload.success !== true) {
        throw new Error(payload.message || 'Lead non enregistré.');
      }

      popup.setAttribute('hidden', 'hidden');
    } catch (error) {
      const errorNode = popup.querySelector('[data-role="lead-error"]');
      if (errorNode) {
        errorNode.textContent = error instanceof Error ? error.message : 'Erreur inattendue.';
      }
    }
  });
})();
