// Shared wizard behavior for add/edit patient pages.
(function () {
  function qs(sel) {
    return document.querySelector(sel);
  }

  function qsa(sel) {
    return Array.from(document.querySelectorAll(sel));
  }

  function showStep(step) {
    const steps = qsa(".wizard-step");
    steps.forEach((el) => {
      const s = el.getAttribute("data-step");
      el.classList.toggle("active", String(s) === String(step));
    });

    const counter = qs("#pageCounter");
    if (counter) counter.textContent = `page ${step}`;

    const backBtn = qs("#wizardBackBtn");
    const nextBtn = qs("#wizardNextBtn");
    const maxStep = steps.length ? Math.max(...steps.map((el) => Number(el.getAttribute("data-step")) || 1)) : 7;

    if (backBtn) backBtn.disabled = step <= 1;
    if (nextBtn) nextBtn.disabled = step >= maxStep;
  }

  function updateConditional(groupName) {
    const hidden = qs(`input[type="hidden"][data-bool-group="${groupName}"]`);
    const val = hidden ? String(hidden.value) : "0";

    qsa("[data-conditional-for]").forEach((wrap) => {
      const forGroup = wrap.getAttribute("data-conditional-for");
      const showValue = wrap.getAttribute("data-conditional-value");
      const shouldShow = forGroup === groupName && String(showValue) === val;
      wrap.style.display = shouldShow ? "" : "none";
    });
  }

  function initBoolChoice() {
    qsa(".choice").forEach((choiceEl) => {
      choiceEl.addEventListener("click", () => {
        const group = choiceEl.getAttribute("data-bool-group");
        const value = choiceEl.getAttribute("data-bool-value");
        if (!group) return;

        const hidden = qs(`input[type="hidden"][data-bool-group="${group}"]`);
        if (hidden) hidden.value = value;

        const groupChoices = qsa(`.choice[data-bool-group="${group}"]`);
        groupChoices.forEach((c) => {
          c.classList.remove("on");
        });
        choiceEl.classList.add("on");

        updateConditional(group);
      });
    });

    // Set initial styles + conditional visibility.
    const groups = new Set(qsa("input[type='hidden'][data-bool-group]").map((i) => i.getAttribute("data-bool-group")));
    groups.forEach((g) => {
      const current = qs(`input[type="hidden"][data-bool-group="${g}"]`);
      const currentVal = current ? String(current.value) : "0";
      qsa(`.choice[data-bool-group="${g}"]`).forEach((c) => {
        const v = c.getAttribute("data-bool-value");
        c.classList.toggle("on", String(v) === currentVal);
      });
      updateConditional(g);
    });
  }

  function initWizard() {
    const nextBtn = qs("#wizardNextBtn");
    const backBtn = qs("#wizardBackBtn");
    if (!nextBtn && !backBtn) return;

    let current = 1;
    showStep(current);

    if (nextBtn) {
      nextBtn.addEventListener("click", (e) => {
        e.preventDefault();
        current += 1;
        showStep(current);
      });
    }
    if (backBtn) {
      backBtn.addEventListener("click", (e) => {
        e.preventDefault();
        current -= 1;
        showStep(current);
      });
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    initBoolChoice();
    initWizard();
  });
})();

