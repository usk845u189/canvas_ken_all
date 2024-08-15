<?php

$dsn = 'mysql:dbname=postal_db;host=127.0.0.1:3306';
$user = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $user, $password);
    $query = $pdo->prepare("SELECT * FROM postal_db WHERE id=1");
    $user = $query->fetch(PDO::FETCH_ASSOC);
    exit;
} catch (PDOException $e) {
    print('Error:' . $e->getMessage());
    exit;
}