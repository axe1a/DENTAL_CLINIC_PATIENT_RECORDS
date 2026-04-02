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
    $_SESSION['username'] = $userData['username'];
    $_SESSION['user_role'] = $userData['user_role'];

    echo json_encode(['message' => 'Login successful']);
    exit;
}

if ($action === "addUser") {
    $username = $data['username'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $role = "dentist";

    $check = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
    $check->execute([$username]);

    if ($check->fetch()) {
        http_response_code(400);
        echo json_encode(['message' => 'Username already exists']);
        exit;
    }

    $statement = $pdo->prepare(
        "INSERT INTO users (username, password, user_role) VALUES (?, ?, ?)"
    );

    $success = $statement->execute([$username, $password, $role]);

    if (!$success) {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to add user']);
        exit;
    }

    echo json_encode(['message' => 'User added successfully']);
    exit;
}

if ($action === "changePass") {
    $userId = (int)$data['user_id'];
    $oldPassword = $data['old_password'] ?? null;
    $newPassword = $data['new_password'] ?? '';

    if ($oldPassword) {
        $statement = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
        $statement->execute([$userId]);
        $user = $statement->fetch();

        if (!$user || !password_verify($oldPassword, $user['password'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Old password is incorrect']);
            exit;
        }
    }

    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $statement = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $success = $statement->execute([$hashedNewPassword, $userId]);

    if (!$success) {
        http_response_code(500);
        echo json_encode(['message' => 'Database update failed']);
        exit;
    }

    echo json_encode(['message' => 'Password updated successfully']);
    exit;
}

if ($action === "deleteUser") {
    $userId = $data['user_id'];

    $statement = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $success = $statement->execute([$userId]);

    if (!$success) {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to delete user']);
        exit;
    }

    echo json_encode(['message' => 'User deleted successfully']);
    exit;
}

http_response_code(400);
echo json_encode(['message' => 'Unknown action provided']);
