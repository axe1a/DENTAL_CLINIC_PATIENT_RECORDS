<?php
session_start();

$databaseDir = __DIR__ . '/../database';
$database = $databaseDir . '/database.sqlite';

// The app expects the SQLite directory to exist for first-time runs.
if (!is_dir($databaseDir)) {
    mkdir($databaseDir, 0777, true);
}

try {
    $pdo = new PDO("sqlite:$database");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $error) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Database connection failed', 'details' => $error]);
    exit;
}
