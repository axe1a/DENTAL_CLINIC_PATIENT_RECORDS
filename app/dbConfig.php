<?php
session_start();

$database = __DIR__ . '/../database/database.sqlite';

try {
    $pdo = new PDO("sqlite:$database");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $error) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Database connection failed', 'details' => $error]);
    exit;
}
