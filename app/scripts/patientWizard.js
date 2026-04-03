// Shared wizard behavior for add/edit patient pages.
(function () {
  function qs(sel) {
    return document.querySelector(sel);
  }

  function qsa(sel) {
    return Array.from(document.querySelectorAll(sel));
  }

  function validateStep(step) {
    const stepEl = qs(`.wizard-step[data-step="${step}"]`);
    if (!stepEl) return true;

    const requiredInputs = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
    let valid = true;

    requiredInputs.forEach(input => {
      if (!input.value.trim()) {
        input.style.borderColor = 'red';
        valid = false;
      } else {
        input.style.borderColor = '';
      }
    });

    return valid;
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
    const saveBtn = qs(".btn.save");
    const maxStep = steps.length ? Math.max(...steps.map((el) => Number(el.getAttribute("data-step")) || 1)) : 7;

    if (backBtn) backBtn.disabled = step <= 1;
    if (nextBtn) nextBtn.disabled = step >= maxStep;

    const isEditMode = Boolean(qs("#editPatientWizardForm"));
    if (saveBtn) {
      if (isEditMode) {
        saveBtn.disabled = false;
        saveBtn.style.display = "";
      } else {
        saveBtn.disabled = step !== maxStep;
        saveBtn.style.display = step === maxStep ? "" : "none";
      }
    }

    // Keep any selected values synced
    syncBoolChoices();
  }

  function updateConditional(groupName) {
    const hidden = qs(`input[type="hidden"][data-bool-group="${groupName}"]`);
    const val = hidden ? String(hidden.value) : "";

    qsa("[data-conditional-for]").forEach((wrap) => {
      const forGroup = wrap.getAttribute("data-conditional-for");
      const showValue = wrap.getAttribute("data-conditional-value");
      const groupHidden = qs(`input[type="hidden"][data-bool-group="${forGroup}"]`);
      const groupVal = groupHidden ? String(groupHidden.value) : "";

      let shouldShow = false;
      if (forGroup) {
        if (showValue !== null && showValue !== undefined && showValue !== "") {
          shouldShow = String(showValue) === groupVal;
        } else if (groupVal !== "") {
          shouldShow = groupVal === "1";
        } else {
          // if condition group exists but no hidden value yet, show by default.
          shouldShow = true;
        }
      }

      wrap.style.display = shouldShow ? "" : "none";
    });
  }

  function syncBoolChoices() {
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

    syncBoolChoices();
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
        if (!validateStep(current)) {
          // Optionally show a message
          alert('Please fill in all required fields before proceeding.');
          return;
        }
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

