<?php
require 'dbConfig.php';
if (isset($_SESSION['user_id'])) {
    //header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SDCI Patient Records</title>
    <link href="./styles/style.css" rel="stylesheet">
    <link href="./styles/tailwindStyles.css" rel="stylesheet">
</head>

<body>
    <div class="login-outer">
        <div class="login-inner">
            <div class="login-left">
                <div class="login-brand">
                    <img class="logo-full" src="./img/logo-complete-hor.png" alt="Solla Dental Clinic">
                </div>

                <div class="login-title">
                    <span class="light">PATIENT</span><br />RECORDS
                </div>
            </div>

            <form id="userLoginForm" class="login-card">
                <label for="usernameField">Username</label>
                <input type="text" id="usernameField" required placeholder="Username">

                <label for="passwordField">Password</label>
                <input type="password" id="passwordField" required placeholder="Password">

                <button type="submit" id="loginButton" class="login-btn">Login</button>
            </form>
        </div>
    </div>

    <script src="scripts/jquery-4.0.0.min.js"></script>
    <script src="scripts/loginScript.js"></script>
</body>

</html>