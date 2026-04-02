<?php
require 'dbConfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


$statement = $pdo->prepare("
    UPDATE patient_records
    SET last_opened = datetime('now')
    WHERE patient_id = :patient_id
");
$statement->execute([
    ":patient_id" => $_GET['patient_id']
]);

$patient = $pdo->query(
    "
    SELECT *
    FROM patient_records
    "
)->fetch();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDCI - Edit Patient Information</title>
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
        <div class="page-title">Edit Patient Information</div>

        <div class="wizard-frame">
            <?php if (!empty($error)): ?>
                <div style="background: rgba(220,53,69,0.12); border: 1px solid rgba(220,53,69,0.35); padding: 10px 12px; border-radius: 12px; margin: 10px 0; font-weight: 900; color:#dc3545;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="edit_patient.php?patient_id=<?= (int)$patientId ?>" id="editPatientWizardForm">
                <!-- PAGE 1 -->
                <section class="wizard-step active" data-step="1">
                    <div class="wizard-grid-2">
                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Full Name [Surname, First Name, M.I.]</label>
                            <input type="text" name="patient_name" required value="<?= htmlspecialchars((string)$patient['patient_name'] ?? '') ?>">
                        </div>

                        <div class="field">
                            <label>Medical Alert Level</label>
                            <select name="alert_level" required>
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <option value="<?= $i ?>" <?= (string)($patient['alert_level'] ?? '') === (string)$i ? 'selected' : '' ?>>Level <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="field">
                            <label>Birthdate</label>
                            <input type="date" name="birthdate" required value="<?= htmlspecialchars((string)$patient['birthdate'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="wizard-grid-4" style="margin-top: 12px;">
                        <div class="field">
                            <label>Age</label>
                            <input type="number" name="age" value="<?= htmlspecialchars((string)($patient['age'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Gender</label>
                            <select name="gender" required>
                                <?php
                                $gender = (string)($patient['gender'] ?? 'male');
                                ?>
                                <option value="male" <?= $gender === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= $gender === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="others" <?= $gender === 'others' ? 'selected' : '' ?>>Others</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Height</label>
                            <input type="number" step="0.01" name="height" value="<?= htmlspecialchars((string)($patient['height'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Weight</label>
                            <input type="number" step="0.1" name="weight" value="<?= htmlspecialchars((string)($patient['weight'] ?? '')) ?>">
                        </div>
                    </div>

                    <div class="wizard-grid-4" style="margin-top: 12px;">
                        <div class="field">
                            <label>Civil Status</label>
                            <select name="civil_status" required>
                                <?php
                                $civil = (string)($patient['civil_status'] ?? 'single');
                                ?>
                                <option value="single" <?= $civil === 'single' ? 'selected' : '' ?>>Single</option>
                                <option value="married" <?= $civil === 'married' ? 'selected' : '' ?>>Married</option>
                                <option value="widowed" <?= $civil === 'widowed' ? 'selected' : '' ?>>Widowed</option>
                                <option value="legally separated" <?= $civil === 'legally separated' ? 'selected' : '' ?>>Legally separated</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Religion</label>
                            <input type="text" name="religion" value="<?= htmlspecialchars((string)($patient['religion'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Occupation</label>
                            <input type="text" name="occupation" value="<?= htmlspecialchars((string)($patient['occupation'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Nationality</label>
                            <input type="text" name="nationality" value="<?= htmlspecialchars((string)($patient['nationality'] ?? '')) ?>">
                        </div>
                    </div>

                    <div class="wizard-grid-2" style="margin-top: 12px;">
                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Home Address</label>
                            <input type="text" name="home_address" value="<?= htmlspecialchars((string)($patient['home_address'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Telephone Number</label>
                            <input type="text" name="patient_telephone" value="<?= htmlspecialchars((string)($patient['patient_telephone'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Cellphone Number</label>
                            <input type="text" name="patient_cellphone" value="<?= htmlspecialchars((string)($patient['patient_cellphone'] ?? '')) ?>">
                        </div>
                    </div>
                </section>

                <!-- PAGE 2 -->
                <section class="wizard-step" data-step="2">
                    <div class="wizard-grid-2">
                        <div class="field">
                            <label>Emergency Contact Person</label>
                            <input type="text" name="emergency_person" value="<?= htmlspecialchars((string)($patient['emergency_person'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Relationship to Patient</label>
                            <input type="text" name="relationship_to_patient" value="<?= htmlspecialchars((string)($patient['relationship_to_patient'] ?? '')) ?>">
                        </div>

                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Home Address</label>
                            <div style="display: flex; gap: 16px; align-items: flex-start;">
                                <input type="text" name="emergency_address" id="emergencyAddressInput" value="<?= htmlspecialchars($emergencyAddress) ?>" placeholder="" style="flex: 1;">
                                <label style="display:flex; gap:8px; align-items:center; cursor:pointer; white-space: nowrap; margin-top: 2px;">
                                    <input type="checkbox" id="sameEmergencyCheckbox" name="same_emergency_address" value="1" <?= $sameEmergencyInitial ? 'checked' : '' ?>> Same as home
                                </label>
                            </div>
                        </div>

                        <div class="field">
                            <label>Telephone Number</label>
                            <input type="text" name="emergency_telephone" value="<?= htmlspecialchars((string)($patient['emergency_telephone'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Cellphone Number</label>
                            <input type="text" name="emergency_cellphone" value="<?= htmlspecialchars((string)($patient['emergency_cellphone'] ?? '')) ?>">
                        </div>

                        <div class="field">
                            <label>Chief Complaint</label>
                            <textarea name="chief_complaint"><?= htmlspecialchars((string)($patient['chief_complaint'] ?? '')) ?></textarea>
                        </div>
                        <div class="field">
                            <label>Present Illness</label>
                            <textarea name="present_illness"><?= htmlspecialchars((string)($patient['present_illness'] ?? '')) ?></textarea>
                        </div>
                    </div>
                </section>

                <!-- PAGE 3 -->
                <section class="wizard-step" data-step="3">
                    <div class="wizard-grid-2">
                        <div class="field">
                            <label>Frequency of Dental Visit</label>
                            <select name="frequency_visit">
                                <?php $freq = (string)($patient['frequency_visit'] ?? ''); ?>
                                <option value="every 6 months" <?= $freq === 'every 6 months' ? 'selected' : '' ?>>Every 6 months</option>
                                <option value="once a year" <?= $freq === 'once a year' ? 'selected' : '' ?>>Once a year</option>
                                <option value="rarely" <?= $freq === 'rarely' ? 'selected' : '' ?>>Rarely</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Date of Last Dental Visit</label>
                            <input type="date" name="last_visit" value="<?= htmlspecialchars((string)($patient['last_visit'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Procedure(s)/done on Last Dental Visit</label>
                            <input type="text" name="last_procedures" value="<?= htmlspecialchars((string)($patient['last_procedures'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Complication(s) during and/or after dental procedure</label>
                            <input type="text" name="procedure_complications" value="<?= htmlspecialchars((string)($patient['procedure_complications'] ?? '')) ?>">
                        </div>
                    </div>

                    <div class="wizard-grid-2" style="margin-top: 12px;">
                        <div class="field">
                            <label>Physician's Name</label>
                            <input type="text" name="physician_name" value="<?= htmlspecialchars((string)($patient['physician_name'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Physician's Office Number</label>
                            <input type="text" name="physician_contact" value="<?= htmlspecialchars((string)($patient['physician_contact'] ?? '')) ?>">
                        </div>
                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Physician's Office Address</label>
                            <input type="text" name="physician_address" value="<?= htmlspecialchars((string)($patient['physician_address'] ?? '')) ?>">
                        </div>
                    </div>
                </section>

                <!-- PAGE 4 -->
                <section class="wizard-step" data-step="4">
                    <div class="wizard-grid-2">
                        <div class="field">
                            <label>In good health?</label>
                            <input type="hidden" name="good_health" data-bool-group="good_health" value="<?= ((int)($patient['good_health'] ?? 0) === 1) ? '1' : '0' ?>">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="good_health" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="good_health" data-bool-value="0">No</div>
                            </div>
                        </div>

                        <div class="field">
                            <label>Currently in medical treatment?</label>
                            <input type="hidden" name="being_treated" data-bool-group="being_treated" value="<?= ((int)($patient['being_treated'] ?? 0) === 1) ? '1' : '0' ?>">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="being_treated" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="being_treated" data-bool-value="0">No</div>
                            </div>

                            <div class="field" data-conditional-for="being_treated" data-conditional-value="1" style="display:none; margin-top: 10px;">
                                <label>if yes, what is the condition being treated?</label>
                                <input type="text" name="what_condition" value="<?= htmlspecialchars((string)($patient['what_condition'] ?? '')) ?>" placeholder="Condition">
                            </div>
                        </div>

                        <div class="field">
                            <label>Had serious illness or operation?</label>
                            <input type="hidden" name="serious_illness" data-bool-group="serious_illness" value="<?= ((int)($patient['serious_illness'] ?? 0) === 1) ? '1' : '0' ?>">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="serious_illness" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="serious_illness" data-bool-value="0">No</div>
                            </div>

                            <div class="field" data-conditional-for="serious_illness" data-conditional-value="1" style="display:none; margin-top: 10px;">
                                <label>If yes, what illness?</label>
                                <input type="text" name="what_illness" value="<?= htmlspecialchars((string)($patient['what_illness'] ?? '')) ?>" placeholder="Illness/Operation">
                            </div>
                        </div>

                        <div class="field">
                            <label>Was hospitalized?</label>
                            <input type="hidden" name="hospitalized" data-bool-group="hospitalized" value="<?= ((int)($patient['hospitalized'] ?? 0) === 1) ? '1' : '0' ?>">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="hospitalized" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="hospitalized" data-bool-value="0">No</div>
                            </div>

                            <div class="field" data-conditional-for="hospitalized" data-conditional-value="1" style="display:none; margin-top: 10px;">
                                <label>If yes, when and why?</label>
                                <input type="text" name="when_why" value="<?= htmlspecialchars((string)($patient['when_why'] ?? '')) ?>" placeholder="When/Why">
                            </div>
                        </div>

                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Taking medications?</label>
                            <input type="hidden" name="taking_medications" data-bool-group="taking_medications" value="<?= ((int)($patient['taking_medications'] ?? 0) === 1) ? '1' : '0' ?>">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="taking_medications" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="taking_medications" data-bool-value="0">No</div>
                            </div>

                            <div class="field" data-conditional-for="taking_medications" data-conditional-value="1" style="display:none; margin-top: 10px;">
                                <label>If yes, specify medications</label>
                                <input type="text" name="what_medications" value="<?= htmlspecialchars((string)($patient['what_medications'] ?? '')) ?>" placeholder="Medications">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- PAGE 5 -->
                <section class="wizard-step" data-step="5">
                    <div class="wizard-grid-2">
                        <div class="field">
                            <label>Using tobacco products?</label>
                            <input type="hidden" name="using_tobacco" data-bool-group="using_tobacco" value="<?= ((int)($patient['using_tobacco'] ?? 0) === 1) ? '1' : '0' ?>">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="using_tobacco" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="using_tobacco" data-bool-value="0">No</div>
                            </div>
                        </div>
                        <div class="field">
                            <label>Using alcohol, cocaine, or other dangerous drugs?</label>
                            <input type="hidden" name="using_alcohol" data-bool-group="using_alcohol" value="<?= ((int)($patient['using_alcohol'] ?? 0) === 1) ? '1' : '0' ?>">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="using_alcohol" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="using_alcohol" data-bool-value="0">No</div>
                            </div>
                        </div>

                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Allergic to the following:</label>
                            <div class="checkbox-grid">
                                <?php
                                $has = function (string $needle) use ($allergiesArr) {
                                    return in_array($needle, $allergiesArr, true);
                                };
                                ?>
                                <label class="check"><input type="checkbox" name="allergies[]" value="Local anesthetic (ex. Lidocaine)" <?= $has('Local anesthetic (ex. Lidocaine)') ? 'checked' : '' ?>> Local anesthetic (ex. Lidocaine)</label>
                                <label class="check"><input type="checkbox" name="allergies[]" value="Penicillin Antibiotics" <?= $has('Penicillin Antibiotics') ? 'checked' : '' ?>> Penicillin Antibiotics</label>
                                <label class="check"><input type="checkbox" name="allergies[]" value="Aspirin" <?= $has('Aspirin') ? 'checked' : '' ?>> Aspirin</label>
                                <label class="check"><input type="checkbox" name="allergies[]" value="Latex" <?= $has('Latex') ? 'checked' : '' ?>> Latex</label>
                                <label class="check"><input type="checkbox" name="allergies[]" value="Others" <?= $has('Others') ? 'checked' : '' ?>> Others</label>
                            </div>

                            <div class="wizard-grid-2" style="margin-top: 10px;">
                                <div class="field" style="grid-column: 1 / -1;">
                                    <label>Other allergies</label>
                                    <input type="text" name="allergies_others" value="<?= htmlspecialchars((string)($patient['allergies_others'] ?? '')) ?>" placeholder="Other allergies">
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
                            <input type="hidden" name="pregnant" data-bool-group="pregnant" value="<?= ((int)($patient['pregnant'] ?? 0) === 1) ? '1' : '0' ?>">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="pregnant" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="pregnant" data-bool-value="0">No</div>
                            </div>
                        </div>

                        <div class="field">
                            <label>Nursing?</label>
                            <input type="hidden" name="nursing" data-bool-group="nursing" value="<?= ((int)($patient['nursing'] ?? 0) === 1) ? '1' : '0' ?>">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="nursing" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="nursing" data-bool-value="0">No</div>
                            </div>
                        </div>

                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Taking birth control pills?</label>
                            <input type="hidden" name="bc_pills" data-bool-group="bc_pills" value="<?= ((int)($patient['bc_pills'] ?? 0) === 1) ? '1' : '0' ?>">
                            <div class="choice-row">
                                <div class="choice" data-bool-group="bc_pills" data-bool-value="1">Yes</div>
                                <div class="choice" data-bool-group="bc_pills" data-bool-value="0">No</div>
                            </div>
                        </div>

                        <div class="field">
                            <label>Blood Type</label>
                            <select name="blood_type">
                                <?php $bt = (string)($patient['blood_type'] ?? 'unknown'); ?>
                                <option value="unknown" <?= $bt === 'unknown' ? 'selected' : '' ?>>Unknown</option>
                                <option value="A+" <?= $bt === 'A+' ? 'selected' : '' ?>>A+</option>
                                <option value="A-" <?= $bt === 'A-' ? 'selected' : '' ?>>A-</option>
                                <option value="B+" <?= $bt === 'B+' ? 'selected' : '' ?>>B+</option>
                                <option value="B-" <?= $bt === 'B-' ? 'selected' : '' ?>>B-</option>
                                <option value="AB+" <?= $bt === 'AB+' ? 'selected' : '' ?>>AB+</option>
                                <option value="AB-" <?= $bt === 'AB-' ? 'selected' : '' ?>>AB-</option>
                                <option value="O+" <?= $bt === 'O+' ? 'selected' : '' ?>>O+</option>
                                <option value="O-" <?= $bt === 'O-' ? 'selected' : '' ?>>O-</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Blood Pressure</label>
                            <input type="text" name="blood_pressure" value="<?= htmlspecialchars((string)($patient['blood_pressure'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Pulse Rate</label>
                            <input type="text" name="pulse_rate" value="<?= htmlspecialchars((string)($patient['pulse_rate'] ?? '')) ?>">
                        </div>
                        <div class="field">
                            <label>Respiratory Rate</label>
                            <input type="text" name="respiratory_rate" value="<?= htmlspecialchars((string)($patient['respiratory_rate'] ?? '')) ?>">
                        </div>
                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Body Temperature</label>
                            <input type="text" name="body_temp" value="<?= htmlspecialchars((string)($patient['body_temp'] ?? '')) ?>">
                        </div>
                    </div>
                </section>

                <!-- PAGE 7 -->
                <section class="wizard-step" data-step="7">
                    <div class="wizard-grid-2">
                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Do you have or had any of the following?</label>
                            <div class="checkbox-grid" style="grid-template-columns: repeat(3, 1fr);">
                                <?php foreach ($conditions as $c): ?>
                                    <label class="check">
                                        <input type="checkbox" name="patient_conditions[]" value="<?= (int)$c['condition_id'] ?>" <?= in_array((int)$c['condition_id'], $selectedConditions, true) ? 'checked' : '' ?>>
                                        <?= htmlspecialchars((string)$c['condition_name']) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <div class="wizard-grid-2" style="margin-top: 12px;">
                                <div class="field">
                                    <label>Other diseases?</label>
                                    <label style="display:flex; gap:8px; align-items:center; cursor:pointer;">
                                        <input type="checkbox" name="conditions_other" value="1">
                                        Others
                                    </label>
                                </div>
                                <div class="field">
                                    <label>Other diseases:</label>
                                    <input type="text" name="conditions_other_text" placeholder="Type here...">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="wizard-bottom" style="margin-top: 18px;">
                    <div class="page-counter" id="pageCounter">page 1</div>
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
        (function() {
            const sameCheckbox = document.getElementById('sameEmergencyCheckbox');
            const emergencyAddress = document.getElementById('emergencyAddressInput');
            const homeAddress = document.querySelector('input[name="home_address"]');

            function sync() {
                if (!sameCheckbox || !emergencyAddress || !homeAddress) return;
                if (sameCheckbox.checked) {
                    emergencyAddress.disabled = true;
                } else {
                    emergencyAddress.disabled = false;
                }
            }

            if (sameCheckbox) sameCheckbox.addEventListener('change', sync);
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
</body>

</html>