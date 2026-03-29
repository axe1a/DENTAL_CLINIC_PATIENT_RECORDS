<?php
require 'dbConfig.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
    <div class="m-3">
        <button id="logoutButton" class="border px-3">logout</button>
    </div>

    <script src="scripts/jquery-4.0.0.min.js"></script>
    <script src="scripts/generalScript.js"></script>
</body>

</html>