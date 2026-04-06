<?php
require 'dbConfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$q = trim((string)($_GET['q'] ?? ''));
$level = trim((string)($_GET['level'] ?? 'all')); // all | 1,2,3,4, 

$patients = [];
$patientsStmt = $pdo->query("
    SELECT patient_id, patient_name, alert_level
    FROM patient_records
    ORDER BY last_opened DESC
");
$patients = $patientsStmt->fetchAll();

// Apply filters for medical alert level and search query
$filtered = [];
foreach ($patients as $p) {
    if ($level !== 'all' && (string)$p['alert_level'] !== $level) continue;
    if ($q !== '' && stripos((string)$p['patient_name'], $q) === false) continue;
    $filtered[] = $p;
}

// Show a "recent" grid
$recent = array_slice($filtered, 0, 5);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDCI - Dashboard</title>
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="./styles/tailwindStyles.css">
</head>

<body>
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand-left">
                <a href="index.php" aria-label="Go to dashboard home">
                    <img class="logo-mark" src="../img/logo-symbol.png" alt="Solla Dental Clinic logo">
                </a>
            </div>

            <form class="search" method="GET" action="index.php">
                <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
                    <img src="../assets/search.svg" alt="Search" width="18" height="18">
                </button>
                <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search for patients by name">
                <input type="hidden" name="level" value="<?= htmlspecialchars($level) ?>">
            </form>

            <div class="user-area" id="userArea">
                <div class="user-pill" id="userPill">Hi, <?= htmlspecialchars($_SESSION['username']) ?></div>
                <div class="user-dropdown" id="userDropdown" style="display:none">
                    <a class="dd-btn primary" href="../change_pass.php">Change Password</a>
                    <?php if ($_SESSION['user_role'] == "superadmin"): ?>
                        <a class="dd-btn primary" href="../personnel_list.php">Personnel List</a>
                    <?php endif; ?>
                    <a class="dd-btn danger" href="../logout.php">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="dash-wrap">
        <div class="dash-grid">
            <section class="dash-left">
                <div class="section-title">Recent</div> 
                <div class="patient-cards">
                    <a class="patient-card new-patient-tile" href="../add_patient.php">
                        <div class="plus" aria-hidden="true">+</div>
                        <div class="pname" style="color:#0f172a">New Patient</div>
                    </a>

                    <?php if (count($recent) === 0): ?>
                        <div class="patient-card" style="border-style:dashed; border-color: rgba(15,23,42,0.25); cursor:default;">
                            <div class="pname">No patients yet</div>
                            <div class="plevel">Add your first patient</div>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($recent as $p):
                        $lvl = (string)$p['alert_level'];
                        $parts = explode(',', (string)$p['patient_name'], 2);
                        $surname = trim($parts[0] ?? '');
                        $firstName = trim($parts[1] ?? '');
                        $iconPath = "../img/lvl-{$lvl}.png";
                    ?>
                        <a class="patient-card" href="../edit_patient.php?patient_id=<?= (int)$p['patient_id'] ?>">
                            <img class="icon alert-icon" src="<?= htmlspecialchars($iconPath) ?>" alt="Alert level <?= htmlspecialchars($lvl) ?> symbol">
                            <div class="pname"><?= htmlspecialchars($surname) ?><?php if ($firstName !== ''): ?><br /><?= htmlspecialchars($firstName) ?><?php endif; ?></div>
                            <div class="plevel">Level <?= htmlspecialchars($lvl) ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <aside class="dash-right">
                <div class="filter-panel">
                    <div class="filter-title">Medical Alert Level</div>
                    <select class="filter-select" id="levelSelect">
                        <option value="all" <?= $level === 'all' ? 'selected' : '' ?>>All</option>
                        <option value="1" <?= $level === '1' ? 'selected' : '' ?>>Level 1</option>
                        <option value="2" <?= $level === '2' ? 'selected' : '' ?>>Level 2</option>
                        <option value="3" <?= $level === '3' ? 'selected' : '' ?>>Level 3</option>
                        <option value="4" <?= $level === '4' ? 'selected' : '' ?>>Level 4</option>
                    </select>
                    <div class="inline-note" style="margin-top:10px;">
                        Filter the recent patient tiles.
                    </div>
                </div>
                <button class="export-btn" type="button">
                        Save All Records as CSV/Excel
                </button>  
            </aside>
        </div>
    </main>

    <script>
        (function() {
            const levelSelect = document.getElementById('levelSelect');
            if (levelSelect) {
                levelSelect.addEventListener('change', function() {
                    const params = new URLSearchParams(window.location.search);

                    // Keep filter value
                    params.set('level', levelSelect.value);

                    // Remove empty search query when input text was cleared
                    const qInput = document.querySelector('.search input[name="q"]');
                    if (qInput) {
                        const value = (qInput.value || '').trim();
                        if (value === '') {
                            params.delete('q');
                        } else {
                            params.set('q', value);
                        }
                    }

                    window.location.search = params.toString();
                });
            }
        })();
    </script>

    <script src="scripts/jquery-4.0.0.min.js"></script>
    <script src="scripts/userDropdownPillScript.js"></script>
</body>

</html>