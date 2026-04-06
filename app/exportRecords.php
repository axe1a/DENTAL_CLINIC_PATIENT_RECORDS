<?php
require 'dbConfig.php';

$date = date("Y-m-d_H-i-s");
$filename = "patient_records_$date.csv";

header('Content-Type: text/csv');
header("Content-Disposition: attachment; filename=\"$filename\"");

$statement = $pdo->query("
    SELECT
        pr.patient_name AS full_name,
        pr.alert_level AS medical_alert_level,
        pr.birthdate,
        pr.age,
        pr.gender,
        pr.civil_status,
        pr.religion,
        pr.occupation,
        pr.nationality,
        pr.height,
        pr.weight,
        pr.home_address,
        pr.patient_telephone,
        pr.patient_cellphone,

        pr.emergency_person AS emergency_contact_person,
        pr.emergency_address AS emergency_contact_home_address,
        pr.emergency_telephone,
        pr.emergency_cellphone,
        pr.relationship_to_patient,
        pr.chief_complaint,
        pr.present_illness,
       
        pr.frequency_visit AS frequency_of_dental_visit,
        pr.last_visit AS date_of_last_dental_visit,
        pr.last_procedures AS procedures_done_on_last_dental_visit,
        pr.anesthesia_response,
        pr.procedure_complications AS complications_during_and_or_after_dental_procedure,
        pr.physician_name,
        pr.physician_contact AS physicians_office_number,
        pr.physician_address AS physician_office_address,

        CASE WHEN pr.good_health THEN 'Yes' ELSE 'No' END AS in_good_health,
        CASE WHEN pr.being_treated THEN 'Yes' ELSE 'No' END AS currently_in_medical_treatment,
        pr.what_condition,
        CASE WHEN pr.serious_illness THEN 'Yes' ELSE 'No' END AS had_serious_illness_or_operation,
        pr.what_illness AS what_illness_or_operation,
        CASE WHEN pr.hospitalized THEN 'Yes' ELSE 'No' END AS hospitalized,
        pr.when_why AS what_and_why_hospitalized,
        CASE WHEN pr.taking_medications THEN 'Yes' ELSE 'No' END AS taking_medications,
        pr.what_medications,

        CASE WHEN pr.using_tobacco THEN 'Yes' ELSE 'No' END AS using_tobacco_products,
        CASE WHEN pr.using_alcohol THEN 'Yes' ELSE 'No' END AS using_alcohol_cocaine_or_other_dangerous_drugs,
        GROUP_CONCAT(DISTINCT ma.allergy_name) AS allergic_to_the_following,
        pr.allergies_other_text AS other_allergies,

        CASE WHEN pr.pregnant THEN 'Yes' ELSE 'No' END AS pregnant,
        CASE WHEN pr.nursing THEN 'Yes' ELSE 'No' END AS nursing,
        CASE WHEN pr.bc_pills THEN 'Yes' ELSE 'No' END AS taking_birth_control_pills,
        pr.blood_type,
        pr.blood_pressure,
        pr.pulse_rate,
        pr.respiratory_rate,
        pr.body_temp AS body_temperature,

        GROUP_CONCAT(DISTINCT mc.condition_name) AS conditions,
        pr.conditions_other_text AS other_diseases,
        pr.treatment_plan,

        pr.last_opened
    FROM patient_records pr

    LEFT JOIN patient_allergies pa ON pr.patient_id = pa.patient_id
    LEFT JOIN medical_allergies ma ON pa.allergy_id = ma.allergy_id
    LEFT JOIN patient_conditions pc ON pr.patient_id = pc.patient_id
    LEFT JOIN medical_conditions mc ON pc.condition_id = mc.condition_id

    GROUP BY pr.patient_id
");
$records = $statement->fetchAll();

$output = fopen("php://output", "w");

// Check if we have rows
if (!empty($records)) {
    // Write headers
    fputcsv($output, array_keys($records[0]), ",", '"', "\\", "\n");

    // Write data rows
    foreach ($records as $row) {
        fputcsv($output, $row, ",", '"', "\\", "\n");
    }
}

// Close stream
fclose($output);
exit;
