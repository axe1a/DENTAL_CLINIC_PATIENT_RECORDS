<?php
require '../dbConfig.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$data = $input['data'] ?? '';

if ($action === "loginUser") {
    $statement = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $statement->execute([$data["username"]]);
    $userData = $statement->fetch();

    error_log(print_r($userData, true));

    if ($userData === false) {
        http_response_code(400);
        echo json_encode(['message' => 'Username is not registered']);
        exit;
    }

    $stored = $userData['password'];
    $plainAttempt = (string)($data['password'] ?? '');

    // Current schema inserts sample password in plaintext (e.g. admin1/admin1).
    // If it looks like a bcrypt hash, use password_verify; otherwise compare plaintext.
    $looksBcrypt = is_string($stored) && str_starts_with($stored, '$2y$') || str_starts_with($stored, '$2a$') || str_starts_with($stored, '$2b$');
    $ok = false;
    if ($looksBcrypt) {
        $ok = password_verify($plainAttempt, $stored);
    } else {
        $ok = hash_equals((string)$stored, $plainAttempt);
    }

    if (!$ok) {
        http_response_code(400);
        echo json_encode(['message' => 'Incorrect password']);
        exit;
    }

    $_SESSION['user_id'] = $userData['user_id'];
    echo json_encode(['message' => 'Login successful', 'user_id' => $userData['user_id'], 'username' => $userData['username']]);
    exit;
}
