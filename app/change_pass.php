<?php
require 'dbConfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, password FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$userRow = $stmt->fetch();
$username = $userRow ? (string)$userRow['username'] : 'User';
$storedPassword = $userRow ? (string)$userRow['password'] : '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = (string)($_POST['old_password'] ?? '');
    $new = (string)($_POST['new_password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    if ($new === '' || $new !== $confirm) {
        $error = 'New password and confirmation do not match.';
    } else {
        $looksBcrypt = is_string($storedPassword) && (str_starts_with($storedPassword, '$2y$') || str_starts_with($storedPassword, '$2a$') || str_starts_with($storedPassword, '$2b$'));
        $okOld = $looksBcrypt ? password_verify($old, $storedPassword) : hash_equals($storedPassword, $old);

        if (!$okOld) {
            $error = 'Old password is incorrect.';
        } else {
            $newHash = password_hash($new, PASSWORD_BCRYPT);
            $upd = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $upd->execute([$newHash, $userId]);
            header("Location: dashboard/index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SDCI - Change Password</title>
    <link href="./styles/style.css" rel="stylesheet">
    <link href="./styles/tailwindStyles.css" rel="stylesheet">
</head>

<body>
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand-left">
                <a href="dashboard/index.php" aria-label="Go to dashboard home">
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
                <div class="user-pill" id="userPill">Hi, <?= htmlspecialchars($username) ?></div>
                <div class="user-dropdown" id="userDropdown" style="display:none">
                    <a class="dd-btn primary" href="./change_pass.php">Change Password</a>
                    <?php if ($isSuperadmin): ?>
                        <a class="dd-btn primary" href="./personnel_list.php">Personnel List</a>
                    <?php endif; ?>
                    <a class="dd-btn danger" href="./logout.php">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="table-wrap">
        <div class="table-card">
            <h3>Change Password</h3>

            <?php if (!empty($error)): ?>
                <div style="background: rgba(220,53,69,0.12); border: 1px solid rgba(220,53,69,0.35); padding: 10px 12px; border-radius: 12px; margin: 10px 0; font-weight: 900; color:#dc3545;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="change_pass.php">
                <div style="margin-top: 12px;">
                    <div class="field" style="margin-bottom: 16px;">
                        <label>Old Password</label>
                        <input type="password" name="old_password" required>
                    </div>
                    <div class="field" style="margin-bottom: 16px;">
                        <label>New Password</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="field">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                </div>

                <div class="wizard-bottom">
                    <a class="btn cancel" href="dashboard/index.php">Cancel</a>
                    <button class="btn save" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</body>

<script>
    (function(){
        const userArea = document.getElementById('userArea');
        const userPill = document.getElementById('userPill');
        const dropdown = document.getElementById('userDropdown');
        if(!userArea || !userPill || !dropdown) return;
        userPill.addEventListener('click', function(event){
            event.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
        document.addEventListener('click', function(event){
            if(!userArea.contains(event.target)){
                dropdown.style.display = 'none';
            }
        });
    })();
</script>
</html>