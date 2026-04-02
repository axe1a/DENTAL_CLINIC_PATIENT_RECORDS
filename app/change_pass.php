<?php
require 'dbConfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
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
                <a href="index.php">
                    <img class="logo-mark" src="./img/logo-symbol.png" alt="Logo">
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
                    <?php if ($_SESSION['user_role'] == "superadmin"): ?>
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

            <div id="errorBox" style="display:none; background: rgba(220,53,69,0.12); border: 1px solid rgba(220,53,69,0.35); padding: 10px 12px; border-radius: 12px; margin: 10px 0; font-weight: 900; color:#dc3545;">
            </div>

            <form id="changePassForm">
                <input type="hidden" id="targetUserId" value="<?= $userId ?>">
                <div style="margin-top: 12px;">
                    <div class="field" style="margin-bottom: 16px;">
                        <label>Old Password</label>
                        <input type="password" id="old_password" required>
                    </div>
                    <div class="field" style="margin-bottom: 16px;">
                        <label>New Password</label>
                        <input type="password" id="new_password" required>
                    </div>
                    <div class="field">
                        <label>Confirm Password</label>
                        <input type="password" id="confirm_password" required>
                    </div>
                </div>

                <div class="wizard-bottom">
                    <a class="btn cancel" href="index.php">Cancel</a>
                    <button class="btn save" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script src="scripts/jquery-4.0.0.min.js"></script>
    <script src="scripts/accountManagementScript.js"></script>
    <script src="scripts/userDropdownPillScript.js"></script>
</body>

</html>