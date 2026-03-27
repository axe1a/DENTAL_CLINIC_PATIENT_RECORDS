<?php
require 'config.php';
echo "Dental Clinic Patient Records System";

// Insert sample data
$pdo->exec("
    INSERT INTO users (username, password) VALUES
    ('user1', 'user1pass'),
    ('user2', 'user2pass');
");

echo "Test table created and sample data inserted.<br>";

// Read data back
$stmt = $pdo->query("SELECT * FROM users");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($rows);
echo "</pre>";
