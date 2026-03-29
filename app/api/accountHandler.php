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

    if (!password_verify($data['password'], $userData['password'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Incorrect password']);
        exit;
    }

    $_SESSION['user_id'] = $userData['user_id'];
    echo json_encode(['message' => 'something']);
    exit;
}
