<?php
require 'dbConfig.php';
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Page</title>
    <link href="./styles/tailwindStyles.css" rel="stylesheet">
</head>

<body>
    <form id="userLoginForm" class="m-3">
        <p>username</p>
        <input type="text" id="usernameField" class="border" required>

        <p>password</p>
        <input type="password" id="passwordField" class="border" required>

        <input type="submit" id="loginButton" value="login" class="border px-3">
    </form>

    <script src="scripts/jquery-4.0.0.min.js"></script>
    <script src="scripts/loginScript.js"></script>
</body>

</html>