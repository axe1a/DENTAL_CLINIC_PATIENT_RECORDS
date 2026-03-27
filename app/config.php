<?php

$database = __DIR__ . '/../database/database.sqlite';

$pdo = new PDO("sqlite:$database");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
