-- USERS
CREATE TABLE IF NOT EXISTS users (
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    user_role TEXT CHECK(user_role IN ('superadmin','dentist')) NOT NULL
);

-- PATIENT RECORDS
CREATE TABLE IF NOT EXISTS patient_records (
    patient_id INTEGER PRIMARY KEY AUTOINCREMENT,

    -- Basic Info
    patient_name TEXT NOT NULL,
    alert_level TEXT CHECK(alert_level IN ('1','2','3','4')) NOT NULL,
    birthdate DATE NOT NULL,
    age INTEGER,
    gender TEXT CHECK(gender IN ('male','female','others')) NOT NULL,
    civil_status TEXT CHECK(civil_status IN ('single','married','widowed','legally separated')) NOT NULL,
    religion TEXT,
    occupation TEXT,
    nationality TEXT,
    height REAL,
    weight REAL,

    -- Contact Info
    home_address TEXT,
    patient_telephone TEXT,
    patient_cellphone TEXT,

    -- Emergency Contact
    emergency_person TEXT,
    emergency_address TEXT,
    emergency_telephone TEXT,
    emergency_cellphone TEXT,
    relationship_to_patient TEXT,

    -- Medical Notes
    chief_complaint TEXT,
    present_illness TEXT,

    -- Dental History
    frequency_visit TEXT CHECK(frequency_visit IN ('rarely','every 6 months','once a year')),
    last_visit DATETIME,
    last_procedures TEXT,
    anesthesia_response TEXT,
    procedure_complications TEXT,

    -- Physician Info
    physician_name TEXT,
    physician_contact TEXT,
    physician_address TEXT,

    -- Medical History
    good_health BOOLEAN,
    being_treated BOOLEAN,
    what_condition TEXT,
    serious_illness BOOLEAN,
    what_illness TEXT,
    hospitalized BOOLEAN,
    when_why TEXT,
    taking_medications BOOLEAN,
    what_medications TEXT,
    using_tobacco BOOLEAN,
    using_alcohol BOOLEAN,

    -- Allergies
    allergies TEXT,
    allergies_others TEXT,

    -- Women-only
    pregnant BOOLEAN,
    nursing BOOLEAN,
    bc_pills BOOLEAN,

    blood_type TEXT CHECK(blood_type IN (
        'A+','A-','B+','B-','AB+','AB-','O+','O-','unknown'
    )),
    blood_pressure TEXT,
    pulse_rate TEXT,
    respiratory_rate TEXT,
    body_temp TEXT,

    -- X-ray Image File name
    xray_filename TEXT,

    -- Patient Record data
    last_opened TEXT DEFAULT (datetime('now'))
);

-- MEDICAL CONDITIONS MASTER LIST
CREATE TABLE IF NOT EXISTS medical_conditions (
    condition_id INTEGER PRIMARY KEY AUTOINCREMENT,
    condition_name TEXT NOT NULL UNIQUE
);

INSERT OR IGNORE INTO medical_conditions (condition_name) VALUES
('High Blood Pressure'),
('Low Blood Pressure'),
('Epilepsy / Convulsions'),
('AIDS or HIV Infection'),
('Sexually Transmitted Disease'),
('Stomach Troubles / Ulcers'),
('Fainting Seizures'),
('Rapid Weight Loss'),
('Radiation Therapy'),
('Joint Replacement / Implant'),
('Diabetes'),
('Heart Surgery'),
('Heart Disease'),
('Heart Murmur'),
('Hepatitis / Liver Disease'),
('Rheumatic Fever'),
('Hay Fever / Allergies'),
('Respiratory Problems'),
('Hepatitis / Jaundice'),
('Tuberculosis'),
('Swollen Ankles'),
('Kidney Disease'),
('Chest Pain'),
('Heart Attack'),
('Cancer / Tumors'),
('Anemia'),
('Angina'),
('Asthma'),
('Emphysema'),
('Bleeding Problems'),
('Blood Diseases'),
('Head Injuries'),
('Arthritis / Rheumatism'),
('Thyroid Problem'),
('Stroke');

-- PATIENT ↔ CONDITIONS (MANY-TO-MANY)
CREATE TABLE IF NOT EXISTS patient_conditions (
    patient_id INTEGER,
    condition_id INTEGER,
    PRIMARY KEY (patient_id, condition_id),
    FOREIGN KEY (patient_id) REFERENCES patient_records(patient_id) ON DELETE CASCADE,
    FOREIGN KEY (condition_id) REFERENCES medical_conditions(condition_id) ON DELETE CASCADE
);

-- INSERT ADMIN
INSERT OR IGNORE INTO users (username, password, user_role) VALUES
('admin1', '$2y$12$n4YAsR4T28UinOK3tHUSCeamX6195ZKq6n5Tl5lro7m078Vg4YkiS', 'superadmin')