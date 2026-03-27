<?php
require 'config.php';

$schemaFile = __DIR__ . '/schema.sql';

$schema = file_get_contents($schemaFile);
$pdo->exec($schema);
