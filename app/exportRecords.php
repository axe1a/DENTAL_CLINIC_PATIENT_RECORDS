<?php
require 'dbConfig.php';

$date = date("Y-m-d_H-i-s");
$filename = "patient_records_$date.csv";

header('Content-Type: text/csv');
header("Content-Disposition: attachment; filename=\"$filename\"");

$statement = $pdo->query("
        SELECT
        pr.*,

        GROUP_CONCAT(DISTINCT ma.allergy_name) AS allergies,

        GROUP_CONCAT(DISTINCT mc.condition_name) AS conditions

    FROM patient_records pr

    LEFT JOIN patient_allergies pa
        ON pr.patient_id = pa.patient_id

    LEFT JOIN medical_allergies ma
        ON pa.allergy_id = ma.allergy_id

    LEFT JOIN patient_conditions pc
        ON pr.patient_id = pc.patient_id

    LEFT JOIN medical_conditions mc
        ON pc.condition_id = mc.condition_id

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
