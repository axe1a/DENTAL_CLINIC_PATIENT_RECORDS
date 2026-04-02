<?php
require '../dbConfig.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$action = $input['action'] ?? '';
$data = $input['data'] ?? '';

if ($action === "addPatientRecord") {
    try {
        $pdo->beginTransaction();

        // HANDLE ARRAY FIELDS
        $allergies = $data["allergies[]"] ?? [];
        $conditions = $data["patient_conditions[]"] ?? [];

        // Normalize array fields (accept comma-separated string or single value from some clients)
        if (!is_array($allergies)) {
            if (is_string($allergies)) {
                $allergies = trim($allergies) === '' ? [] : explode(',', $allergies);
            } else {
                $allergies = [];
            }
        }
        if (!is_array($conditions)) {
            if (is_string($conditions)) {
                $conditions = trim($conditions) === '' ? [] : explode(',', $conditions);
            } else {
                $conditions = [];
            }
        }

        // Cast items to int where appropriate and remove empty values
        $allergies = array_values(array_filter(array_map('intval', (array)$allergies)));
        $conditions = array_values(array_filter(array_map('intval', (array)$conditions)));

        // INSERT PATIENT RECORD
        $stmt = $pdo->prepare("
            INSERT INTO patient_records (
                patient_name, alert_level, birthdate, age, gender, civil_status, religion, occupation, nationality, height, weight, home_address, patient_telephone, patient_cellphone,

                emergency_person, emergency_address, emergency_telephone, emergency_cellphone, relationship_to_patient, chief_complaint, present_illness,

                frequency_visit, last_visit, last_procedures, procedure_complications, physician_name, physician_contact, physician_address,
                
                good_health,
                being_treated, what_condition,
                serious_illness, what_illness,
                hospitalized, when_why,
                taking_medications, what_medications,
                
                using_tobacco, using_alcohol, allergies_other_text,
                
                pregnant, nursing, bc_pills,
                blood_type, blood_pressure, pulse_rate, respiratory_rate, body_temp,

                conditions_other_text
            )
            VALUES (
                :patient_name, :alert_level, :birthdate, :age, :gender, :civil_status, :religion, :occupation, :nationality, :height, :weight, :home_address, :patient_telephone, :patient_cellphone,
                
                :emergency_person, :emergency_address, :emergency_telephone, :emergency_cellphone, :relationship_to_patient, :chief_complaint, :present_illness,
                
                :frequency_visit, :last_visit, :last_procedures, :procedure_complications, :physician_name, :physician_contact, :physician_address,
                
                :good_health,
                :being_treated, :what_condition,
                :serious_illness, :what_illness,
                :hospitalized, :when_why,
                :taking_medications, :what_medications,
                
                :using_tobacco, :using_alcohol, :allergies_other_text,
                
                :pregnant, :nursing, :bc_pills,
                :blood_type, :blood_pressure, :pulse_rate, :respiratory_rate, :body_temp,

                :conditions_other_text
            )
        ");

        $stmt->execute([
            ":patient_name" => $data["patient_name"] ?? null,
            ":alert_level" => $data["alert_level"] ?? null,
            ":birthdate" => $data["birthdate"] ?? null,
            ":age" => $data["age"] ?? null,
            ":gender" => $data["gender"] ?? null,
            ":civil_status" => $data["civil_status"] ?? null,
            ":religion" => $data["religion"] ?? null,
            ":occupation" => $data["occupation"] ?? null,
            ":nationality" => $data["nationality"] ?? null,
            ":height" => $data["height"] ?? null,
            ":weight" => $data["weight"] ?? null,
            ":home_address" => $data["home_address"] ?? null,
            ":patient_telephone" => $data["patient_telephone"] ?? null,
            ":patient_cellphone" => $data["patient_cellphone"] ?? null,
            ":emergency_person" => $data["emergency_person"] ?? null,
            ":emergency_address" => $data["emergency_address"] ?? null,
            ":emergency_telephone" => $data["emergency_telephone"] ?? null,
            ":emergency_cellphone" => $data["emergency_cellphone"] ?? null,
            ":relationship_to_patient" => $data["relationship_to_patient"] ?? null,
            ":chief_complaint" => $data["chief_complaint"] ?? null,
            ":present_illness" => $data["present_illness"] ?? null,
            ":frequency_visit" => $data["frequency_visit"] ?? null,
            ":last_visit" => $data["last_visit"] ?? null,
            ":last_procedures" => $data["last_procedures"] ?? null,
            ":procedure_complications" => $data["procedure_complications"] ?? null,
            ":physician_name" => $data["physician_name"] ?? null,
            ":physician_contact" => $data["physician_contact"] ?? null,
            ":physician_address" => $data["physician_address"] ?? null,
            ":good_health" => $data["good_health"] ?? 0,
            ":being_treated" => $data["being_treated"] ?? 0,
            ":what_condition" => $data["what_condition"] ?? null,
            ":serious_illness" => $data["serious_illness"] ?? 0,
            ":what_illness" => $data["what_illness"] ?? null,
            ":hospitalized" => $data["hospitalized"] ?? 0,
            ":when_why" => $data["when_why"] ?? null,
            ":taking_medications" => $data["taking_medications"] ?? 0,
            ":what_medications" => $data["what_medications"] ?? null,
            ":using_tobacco" => $data["using_tobacco"] ?? 0,
            ":using_alcohol" => $data["using_alcohol"] ?? 0,
            ":allergies_other_text" => $data["allergies_other_text"] ?? null,
            ":pregnant" => $data["pregnant"] ?? 0,
            ":nursing" => $data["nursing"] ?? 0,
            ":bc_pills" => $data["bc_pills"] ?? 0,
            ":blood_type" => $data["blood_type"] ?? "unknown",
            ":blood_pressure" => $data["blood_pressure"] ?? null,
            ":pulse_rate" => $data["pulse_rate"] ?? null,
            ":respiratory_rate" => $data["respiratory_rate"] ?? null,
            ":body_temp" => $data["body_temp"] ?? null,
            ":conditions_other_text" => $data["conditions_other_text"] ?? null
        ]);

        $patientId = $pdo->lastInsertId();

        // INSERT PATIENT ALLERGIES
        if (!empty($allergies)) {
            $stmt = $pdo->prepare("
                INSERT INTO patient_allergies (patient_id, allergy_id)
                VALUES (?, ?)
            ");

            foreach ($allergies as $allergyId) {
                $stmt->execute([$patientId, $allergyId]);
            }
        }

        // INSERT PATIENT CONDITIONS
        if (!empty($conditions)) {
            $stmt = $pdo->prepare("
                INSERT INTO patient_conditions (patient_id, condition_id)
                VALUES (?, ?)
            ");

            foreach ($conditions as $conditionId) {
                $stmt->execute([$patientId, $conditionId]);
            }
        }

        $pdo->commit();

        echo json_encode([
            "message" => "Successfully added patient record",
            "patient_id" => $patientId
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();

        http_response_code(500);
        echo json_encode([
            "message" => "Failed to add patient record",
            "error" => $e->getMessage()
        ]);
    }
}

if ($action === "editPatientRecord") {
    try {
        $pdo->beginTransaction();

        $patientId = $data["patient_id"];

        $boolFields = [
            'good_health',
            'being_treated',
            'serious_illness',
            'hospitalized',
            'taking_medications',
            'using_tobacco',
            'using_alcohol',
            'pregnant',
            'nursing',
            'bc_pills'
        ];
        foreach ($boolFields as $field) {
            $data[$field] = isset($data[$field]) && (int)$data[$field] === 1 ? 1 : 0;
        }

        // Handle arrays
        $allergies = $data["allergies[]"] ?? [];
        $conditions = $data["patient_conditions[]"] ?? [];

        // Normalize array fields (accept comma-separated string or single value from some clients)
        if (!is_array($allergies)) {
            if (is_string($allergies)) {
                $allergies = trim($allergies) === '' ? [] : explode(',', $allergies);
            } else {
                $allergies = [];
            }
        }
        if (!is_array($conditions)) {
            if (is_string($conditions)) {
                $conditions = trim($conditions) === '' ? [] : explode(',', $conditions);
            } else {
                $conditions = [];
            }
        }

        // UPDATE patient record
        $fields = [
            "patient_name",
            "alert_level",
            "birthdate",
            "age",
            "gender",
            "civil_status",
            "religion",
            "occupation",
            "nationality",
            "height",
            "weight",
            "home_address",
            "patient_telephone",
            "patient_cellphone",

            "emergency_person",
            "emergency_address",
            "emergency_telephone",
            "emergency_cellphone",
            "relationship_to_patient",
            "chief_complaint",
            "present_illness",

            "frequency_visit",
            "last_visit",
            "last_procedures",
            "procedure_complications",
            "physician_name",
            "physician_contact",
            "physician_address",

            "good_health",
            "being_treated",
            "what_condition",
            "serious_illness",
            "what_illness",
            "hospitalized",
            "when_why",
            "taking_medications",
            "what_medications",

            "using_tobacco",
            "using_alcohol",
            "allergies_other_text",

            "pregnant",
            "nursing",
            "bc_pills",
            "blood_type",
            "blood_pressure",
            "pulse_rate",
            "respiratory_rate",
            "body_temp",

            "conditions_other_text"
        ];

        $updateParts = [];
        $params = [];
        foreach ($fields as $f) {
            $updateParts[] = "$f = :$f";
            $params[":$f"] = $data[$f] ?? null;
        }
        $params[":patient_id"] = $patientId;

        $stmt = $pdo->prepare("UPDATE patient_records SET " . implode(", ", $updateParts) . " WHERE patient_id = :patient_id");
        $stmt->execute($params);

        $pdo->prepare("DELETE FROM patient_allergies WHERE patient_id = :patient_id")->execute([':patient_id' => $patientId]);
        $pdo->prepare("DELETE FROM patient_conditions WHERE patient_id = :patient_id")->execute([':patient_id' => $patientId]);

        if (!empty($allergies)) {
            $stmt = $pdo->prepare("INSERT INTO patient_allergies (patient_id, allergy_id) VALUES (:pid, :aid)");
            foreach ($allergies as $aid) {
                $stmt->execute([':pid' => $patientId, ':aid' => $aid]);
            }
        }

        if (!empty($conditions)) {
            $stmt = $pdo->prepare("INSERT INTO patient_conditions (patient_id, condition_id) VALUES (:pid, :cid)");
            foreach ($conditions as $cid) {
                $stmt->execute([':pid' => $patientId, ':cid' => $cid]);
            }
        }

        $pdo->commit();
        echo json_encode([
            'message' => 'Successfully updated patient record',
            'patient_id' => $patientId
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode([
            "message" => "Failed to add patient record",
            'message' => $e->getMessage()
        ]);
    }
}
