<?php
require 'dbConfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT user_id, username, password FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$me = $stmt->fetch();
$username = $me ? (string)$me['username'] : 'User';

if ($username !== 'admin1') {
    header("Location: index.php");
    exit;
}

$error = '';
$mode = (string)($_GET['mode'] ?? 'create'); // create | update
$editUserId = isset($_GET['edit_user_id']) ? (int)$_GET['edit_user_id'] : 0;

if (isset($_GET['delete_user_id'])) {
    $deleteId = (int)$_GET['delete_user_id'];
    if ($deleteId > 0 && $deleteId !== $userId) {
        $del = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $del->execute([$deleteId]);
    }
    header("Location: personnel_list.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formUsername = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    $targetUserId = (int)($_POST['target_user_id'] ?? 0);

    if ($password === '' || $password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Update mode: username is disabled, so we only need the target user_id.
        if ($mode === 'update' && $targetUserId > 0) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $upd = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $upd->execute([$hash, $targetUserId]);
            header("Location: personnel_list.php");
            exit;
        }

        // Create mode
        if ($formUsername === '') {
            $error = 'Username is required.';
        } else {
            $exists = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
            $exists->execute([$formUsername]);
            if ($exists->fetch()) {
                $error = 'Username already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $ins = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $ins->execute([$formUsername, $hash]);
                header("Location: personnel_list.php");
                exit;
            }
        }
    }
}

$personnel = $pdo->query("SELECT user_id, username FROM users WHERE username != 'admin1' ORDER BY user_id DESC")->fetchAll();

$editUsername = '';
if ($mode === 'update' && $editUserId > 0) {
    $st = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
    $st->execute([$editUserId]);
    $row = $st->fetch();
    $editUsername = $row ? (string)$row['username'] : '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDCI - Personnel List</title>
    <link href="./styles/style.css" rel="stylesheet">
    <link href="./styles/tailwindStyles.css" rel="stylesheet">
</head>

<body>
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand-left">
                <a href="index.php" aria-label="Go to dashboard home">
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
                    <?php if (isset($isSuperadmin) && $isSuperadmin): ?>
                        <a class="dd-btn primary" href="./personnel_list.php">Personnel List</a>
                    <?php endif; ?>
                    <a class="dd-btn danger" href="./logout.php">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="table-wrap">
        <div class="table-card">
            <h3>Add New Personnel</h3>

            <form method="POST" action="personnel_list.php?mode=<?= htmlspecialchars($mode) ?>&edit_user_id=<?= (int)$editUserId ?>">
                <?php if ($mode === 'update'): ?>
                    <input type="hidden" name="target_user_id" value="<?= (int)$editUserId ?>">
                <?php endif; ?>

                <div>
                    <div class="field" style="margin-bottom: 16px;">
                        <label>Username</label>
                        <input
                            type="text"
                            name="username"
                            value="<?= htmlspecialchars($mode === 'update' ? $editUsername : '') ?>"
                            <?= $mode === 'update' ? 'disabled' : '' ?>
                            placeholder="Username"
                            required>
                        <?php if ($mode === 'update'): ?>
                            <small style="display:block; margin-top:6px;">Editing only changes password.</small>
                        <?php endif; ?>
                    </div>
                    <div class="field" style="margin-bottom: 16px;">
                        <label>Password</label>
                        <input type="password" name="password" required placeholder="New Password">
                    </div>
                    <div class="field">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" required placeholder="Confirm Password">
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                    <div style="background: rgba(220,53,69,0.12); border: 1px solid rgba(220,53,69,0.35); padding: 10px 12px; border-radius: 12px; margin: 10px 0; font-weight: 900; color:#dc3545;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="wizard-bottom">
                    <a class="btn cancel" href="personnel_list.php">Cancel</a>
                    <button class="btn save" type="submit">Save</button>
                </div>
            </form>
        </div>

        <div class="table-card" style="margin-top: 14px;">
            <h3 style="margin-top:0;">Personnel List</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th style="width:130px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($personnel as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars((string)$p['username']) ?></td>
                            <td>
                                <div class="table-actions">
                                    <a class="icon-btn" style="display:flex; align-items:center; justify-content:center; text-decoration:none;" href="personnel_list.php?mode=update&edit_user_id=<?= (int)$p['user_id'] ?>" title="Edit">
                                        <img src="./assets/edit.svg" alt="Edit" width="16" height="16" style="filter: brightness(0) saturate(100%) invert(18%) sepia(89%) saturate(1313%) hue-rotate(206deg) brightness(96%) contrast(99%);">
                                    </a>
                                    <a
                                        class="icon-btn danger"
                                        style="display:flex; align-items:center; justify-content:center; text-decoration:none;"
                                        href="personnel_list.php?delete_user_id=<?= (int)$p['user_id'] ?>"
                                        onclick="return confirm('Delete this personnel?')"
                                        title="Delete">
                                        <img src="./assets/delete.svg" alt="Delete" width="16" height="16" style="filter: brightness(0) saturate(100%) invert(50%) sepia(66%) saturate(1128%) hue-rotate(348deg) brightness(103%) contrast(101%);">
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($personnel) === 0): ?>
                        <tr>
                            <td colspan="2" style="color:#64748b; font-weight:800;">No personnel yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        (function() {
            const userArea = document.getElementById('userArea');
            const userPill = document.getElementById('userPill');
            const dropdown = document.getElementById('userDropdown');
            if (!userArea || !userPill || !dropdown) return;
            userPill.addEventListener('click', function(event) {
                event.stopPropagation();
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            });
            document.addEventListener('click', function(event) {
                if (!userArea.contains(event.target)) {
                    dropdown.style.display = 'none';
                }
            });
        })();
    </script>
</body>

</html>