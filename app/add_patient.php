<?php
require 'dbConfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDCI - Add New Patient</title>
    <link href="./styles/style.css" rel="stylesheet">
    <link href="./styles/tailwindStyles.css" rel="stylesheet">
</head>

<body>
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand-left">
                <a href="index.php" aria-label="Go to dashboard home">
                    <img class="logo-mark" src="./img/logo-symbol.png" alt="Solla Dental Clinic logo">
                </a>
            </div>

            <form class="search" method="GET" action="index.php">
                <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                    <img src="./assets/search.svg" alt="Search" width="18" height="18">
                </button>
                <input name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Search for patients by name">
                <input type="hidden" name="level" value="<?= htmlspecialchars($level ?? 'all') ?>">
            </form>

            <div class="user-area" id="userArea">
                <div class="user-pill" id="userPill">Hi, <?= htmlspecialchars($_SESSION["username"]) ?></div>
                <div class="user-dropdown" id="userDropdown" style="display:none">
                    <a class="dd-btn primary" href="./change_pass.php">Change Password</a>
                    <?php if ($_SESSION["user_role"] == "superadmin"): ?>
                        <a class="dd-btn primary" href="./personnel_list.php">Personnel List</a>
                    <?php endif; ?>
                    <a class="dd-btn danger" href="./logout.php">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="wizard">
        <div class="page-title">Add New Patient</div>

        <div class="wizard-frame">
            <?php if (!empty($error)): ?>
                <div style="background: rgba(220,53,69,0.12); border: 1px solid rgba(220,53,69,0.35); padding: 10px 12px; border-radius: 12px; margin: 10px 0; font-weight: 900; color:#dc3545;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="addPatientWizardForm">
                <!-- PAGE 1 -->
                <section class="wizard-step active" data-step="1">
                    <div class="wizard-grid-2">
                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Full Name [Surname, First Name, M.I.]</label>
                            <input type="text" name="patient_name" required placeholder="Surname, First Name M.I.">
                        </div>

                        <div class="field">
                            <label>Medical Alert Level</label>
                            <select name="alert_level" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>

                        <div class="field">
                            <label>Birthdate</label>
                            <input type="date" name="birthdate" required>
                        </div>
                    </div>

                    <div class="wizard-grid-4" style="margin-top: 12px;">
                        <div class="field">
                            <label>Age</label>
                            <input type="number" name="age" placeholder="Age">
                        </div>
                        <div class="field">
                            <label>Gender</label>
                            <select name="gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="others">Others</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Height</label>
                            <input type="number" step="0.01" name="height" placeholder="e.g. 1.67m">
                        </div>
                        <div class="field">
                            <label>Weight</label>
                            <input type="number" step="0.1" name="weight" placeholder="e.g. 65kg">
                        </div>
                    </div>

                    <div class="wizard-grid-4" style="margin-top: 12px;">
                        <div class="field">
                            <label>Civil Status</label>
                            <select name="civil_status" required>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="widowed">Widowed</option>
                                <option value="legally separated">Legally separated</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Religion</label>
                            <input type="text" name="religion" placeholder="Religion">
                        </div>
                        <div class="field">
                            <label>Occupation</label>
                            <input type="text" name="occupation" placeholder="Occupation">
                        </div>
                        <div class="field">
                            <label>Nationality</label>
                            <input type="text" name="nationality" placeholder="Nationality">
                        </div>
                    </div>

                    <div class="wizard-grid-2" style="margin-top: 12px;">
                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Home Address</label>
                            <input type="text" name="home_address" placeholder="Home Address">
                        </div>
                        <div class="field">
                            <label>Telephone Number</label>
                            <input type="text" name="patient_telephone" placeholder="Telephone Number">
                        </div>
                        <div class="field">
                            <label>Cellphone Number</label>
                            <input type="text" name="patient_cellphone" placeholder="Cellphone Number">
                        </div>
                    </div>
                </section>

                <!-- PAGE 2 -->
                <section class="wizard-step" data-step="2">
                    <div class="wizard-grid-2">
                        <div class="field">
                            <label>Emergency Contact Person</label>
                            <input type="text" name="emergency_person" placeholder="">
                        </div>
                        <div class="field">
                            <label>Relationship to Patient</label>
                            <input type="text" name="relationship_to_patient" placeholder="">
                        </div>

                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Emergency Contact's Home Address</label>
                            <div style="display: flex; gap: 16px; align-items: flex-start;">
                                <input type="text" name="emergency_address" id="emergencyAddressInput" placeholder="" style="flex: 1;">
                                <label style="display:flex; gap:8px; align-items:center; cursor:pointer; white-space: nowrap; margin-top: 2px;">
                                    <input type="checkbox" id="sameEmergencyCheckbox" name="same_emergency_address" value="1">
                                    Same as home
                                </label>
                            </div>
                        </div>

                        <div class="field">
                            <label>Telephone Number</label>
                            <input type="text" name="emergency_telephone" placeholder="">
                        </div>
                        <div class="field">
                            <label>Cellphone Number</label>
                            <input type="text" name="emergency_cellphone" placeholder="">
                        </div>

                        <div class="field">
                            <label>Chief Complaint</label>
                            <textarea name="chief_complaint" placeholder=""></textarea>
                        </div>
                        <div class="field">
                            <label>Present Illness</label>
                            <textarea name="present_illness" placeholder=""></textarea>
                        </div>
                    </div>
                </section>

                <!-- PAGE 3 -->
                <section class="wizard-step" data-step="3">
                    <div class="wizard-grid-2">
                        <div class="field">
                            <label>Frequency of Dental Visit</label>
                            <select name="frequency_visit">
                                <option value="every 6 months">Every 6 months</option>
                                <option value="once a year">Once a year</option>
                                <option value="rarely">Rarely</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Date of Last Dental Visit</label>
                            <input type="date" name="last_visit">
                        </div>
                        <div class="field">
                            <label>Procedure(s)/done on Last Dental Visit</label>
                            <input type="text" name="last_procedures" placeholder="">
                        </div>
                        <div class="field">
                            <label>Complication(s) during and/or after dental procedure</label>
                            <input type="text" name="procedure_complications" placeholder="">
                        </div>
                    </div>

                    <div class="wizard-grid-2" style="margin-top: 12px;">
                        <div class="field">
                            <label>Physician's Name</label>
                            <input type="text" name="physician_name" placeholder="">
                        </div>
                        <div class="field">
                            <label>Physician's Office Number</label>
                            <input type="text" name="physician_contact" placeholder="">
                        </div>
                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Physician's Office Address</label>
                            <input type="text" name="physician_address" placeholder="">
                        </div>
                    </div>
                </section>

                <!-- PAGE 4 -->
                <section class="wizard-step" data-step="4">
                    <!-- <div style="margin: 20px;"> -->
                    <div class="field" style="margin-bottom: 15px;">
                        <label>In good health?</label>
                        <input type="hidden" name="good_health" data-bool-group="good_health" value="0">
                        <div class="choice-row">
                            <div class="choice" data-bool-group="good_health" data-bool-value="1">Yes</div>
                            <div class="choice" data-bool-group="good_health" data-bool-value="0">No</div>
                        </div>
                    </div>

                    <div class="field" style="margin-bottom: 15px;">
                        <label>Currently in medical treatment?</label>
                        <input type="hidden" name="being_treated" data-bool-group="being_treated" value="0">
                        <div style="display: flex; gap: 16px; align-items: center;">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="being_treated" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="being_treated" data-bool-value="0">No</div>
                            </div>
                            <div data-conditional-for="being_treated" data-conditional-value="1" style="display:none; flex: 1;">
                                <input type="text" name="what_condition" placeholder="What is the condition being treated?">
                            </div>
                        </div>
                    </div>

                    <div class="field" style="margin-bottom: 15px;">
                        <label>Had serious illness or operation?</label>
                        <input type="hidden" name="serious_illness" data-bool-group="serious_illness" value="0">
                        <div style="display: flex; gap: 16px; align-items: center;">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="serious_illness" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="serious_illness" data-bool-value="0">No</div>
                            </div>
                            <div data-conditional-for="serious_illness" data-conditional-value="1" style="display:none; flex: 1;">
                                <input type="text" name="what_illness" placeholder="What illness?">
                            </div>
                        </div>
                    </div>

                    <div class="field" style="margin-bottom: 15px;">
                        <label>Was hospitalized?</label>
                        <input type="hidden" name="hospitalized" data-bool-group="hospitalized" value="0">
                        <div style="display: flex; gap: 16px; align-items: center;">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="hospitalized" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="hospitalized" data-bool-value="0">No</div>
                            </div>
                            <div data-conditional-for="hospitalized" data-conditional-value="1" style="display:none; flex: 1;">
                                <input type="text" name="when_why" placeholder="When and why?">
                            </div>
                        </div>
                    </div>

                    <div class="field" style="margin-bottom: 15px;">
                        <label>Taking medications?</label>
                        <input type="hidden" name="taking_medications" data-bool-group="taking_medications" value="0">
                        <div style="display: flex; gap: 16px; align-items: center;">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="taking_medications" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="taking_medications" data-bool-value="0">No</div>
                            </div>
                            <div data-conditional-for="taking_medications" data-conditional-value="1" style="display:none; flex: 1;">
                                <input type="text" name="what_medications" placeholder="Specify medications">
                            </div>
                        </div>
                    </div>
                    <!-- </div> -->
                </section>

                <!-- PAGE 5 -->
                <section class="wizard-step" data-step="5">
                    <div class="wizard-grid-2">
                        <div class="field">
                            <label>Using tobacco products?</label>
                            <input type="hidden" name="using_tobacco" data-bool-group="using_tobacco" value="0">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="using_tobacco" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="using_tobacco" data-bool-value="0">No</div>
                            </div>
                        </div>
                        <div class="field">
                            <label>Using alcohol, cocaine, or other dangerous drugs?</label>
                            <input type="hidden" name="using_alcohol" data-bool-group="using_alcohol" value="0">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="using_alcohol" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="using_alcohol" data-bool-value="0">No</div>
                            </div>
                        </div>

                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Allergic to the following:</label>
                            <div class="checkbox-grid">
                                <?php
                                $allergies = $pdo->query(
                                    "
                                    SELECT allergy_id, allergy_name
                                    FROM medical_allergies
                                    "
                                )->fetchAll();

                                foreach ($allergies as $allergy):
                                ?>
                                    <label class="check">
                                        <input type="checkbox" name="allergies[]" value="<?= (int)$allergy['allergy_id'] ?>">
                                        <?= htmlspecialchars((string)$allergy['allergy_name']) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <div class="wizard-grid-2" style="margin-top: 10px;">
                                <div class="field" style="grid-column: 1 / -1;">
                                    <label>Other allergies</label>
                                    <input type="text" name="allergies_other_text" placeholder="Other allergies">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- PAGE 6 -->
                <section class="wizard-step" data-step="6">
                    <div class="wizard-grid-2">
                        <div class="field">
                            <label>For women only: Pregnant?</label>
                            <input type="hidden" name="pregnant" data-bool-group="pregnant" value="0">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="pregnant" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="pregnant" data-bool-value="0">No</div>
                            </div>
                        </div>

                        <div class="field">
                            <label>Nursing?</label>
                            <input type="hidden" name="nursing" data-bool-group="nursing" value="0">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="nursing" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="nursing" data-bool-value="0">No</div>
                            </div>
                        </div>

                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Taking birth control pills?</label>
                            <input type="hidden" name="bc_pills" data-bool-group="bc_pills" value="0">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="bc_pills" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="bc_pills" data-bool-value="0">No</div>
                            </div>
                        </div>

                        <div class="field">
                            <label>Blood Type</label>
                            <select name="blood_type">
                                <option value="unknown">Unknown</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Blood Pressure</label>
                            <input type="text" name="blood_pressure" placeholder="">
                        </div>
                        <div class="field">
                            <label>Pulse Rate</label>
                            <input type="text" name="pulse_rate" placeholder="">
                        </div>
                        <div class="field">
                            <label>Respiratory Rate</label>
                            <input type="text" name="respiratory_rate" placeholder="">
                        </div>
                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Body Temperature</label>
                            <input type="text" name="body_temp" placeholder="">
                        </div>
                    </div>
                </section>

                <!-- PAGE 7 -->
                <section class="wizard-step" data-step="7">
                    <div class="wizard-grid-2">
                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Do you have or had any of the following?</label>
                            <div class="checkbox-grid" style="grid-template-columns: repeat(3, 1fr);">
                                <?php
                                $conditions = $pdo->query(
                                    "
                                    SELECT condition_id, condition_name
                                    FROM medical_conditions
                                    "
                                )->fetchAll();

                                foreach ($conditions as $c):
                                ?>
                                    <label class="check">
                                        <input type="checkbox" name="patient_conditions[]" value="<?= (int)$c['condition_id'] ?>">
                                        <?= htmlspecialchars((string)$c['condition_name']) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <div class="field" style="grid-column: 1 / -1; margin-top: 12px;">
                                <div style="display: flex; gap: 16px; align-items: flex-start;">
                                    <div style="flex: 1;">
                                        <input type="text" name="conditions_other_text" placeholder="Type here..." style="width: 100%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="wizard-bottom" style="margin-top: 18px;">
                    <a class="btn cancel" href="index.php">Cancel</a>
                    <button class="btn back" id="wizardBackBtn">Back</button>
                    <button class="btn next" id="wizardNextBtn">Next</button>
                    <button class="btn save" type="submit">Save</button>
                </div>
            </form>
        </div>
    </main>

    <script src="scripts/patientWizard.js"></script>
    <script>
        // Function to update conditional field visibility based on boolean group values
        function updateConditionalFields() {
            document.querySelectorAll('[data-conditional-for]').forEach(field => {
                const conditionalFor = field.getAttribute('data-conditional-for');
                const conditionalValue = field.getAttribute('data-conditional-value');
                const hiddenInput = document.querySelector(`input[data-bool-group="${conditionalFor}"]`);

                if (hiddenInput && hiddenInput.value === conditionalValue) {
                    field.style.display = 'block';
                } else {
                    field.style.display = 'none';
                }
            });
        }

        // Initialize conditional fields on page load
        document.addEventListener('DOMContentLoaded', updateConditionalFields);

        // Update conditional fields whenever a choice button is clicked
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('choice')) {
                setTimeout(updateConditionalFields, 0);
            }
        });

        // Update conditional fields when wizard step changes
        const observer = new MutationObserver(updateConditionalFields);
        document.querySelectorAll('.wizard-step').forEach(step => {
            observer.observe(step, {
                attributes: true,
                attributeFilter: ['class']
            });
        });

        // Emergency address sync
        (function() {
            const sameCheckbox = document.getElementById('sameEmergencyCheckbox');
            const emergencyAddress = document.getElementById('emergencyAddressInput');
            const homeAddress = document.querySelector('input[name="home_address"]');

            function sync() {
                if (!sameCheckbox || !emergencyAddress || !homeAddress) return;
                if (sameCheckbox.checked) {
                    emergencyAddress.value = homeAddress.value || '';
                    emergencyAddress.disabled = true;
                } else {
                    emergencyAddress.disabled = false;
                }
            }

            if (sameCheckbox) {
                sameCheckbox.addEventListener('change', sync);
            }
            if (homeAddress) {
                homeAddress.addEventListener('input', function() {
                    if (sameCheckbox && sameCheckbox.checked) {
                        emergencyAddress.value = homeAddress.value || '';
                    }
                });
            }
            sync();
        })();
    </script>

    <script src="scripts/jquery-4.0.0.min.js"></script>
    <script src="scripts/userDropdownPillScript.js"></script>
    <script src="scripts/patientFormScript.js"></script>
</body>

</html>