<?php

$dsn = 'mysql:dbname=postal_db;host=127.0.0.1:3306';
$user = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = $pdo->query("SHOW TABLES");
    $tables = $query->fetchAll(PDO::FETCH_ASSOC);

    var_dump($tables);
    
} catch (PDOException $e) {
    print('Error:' . $e->getMessage());
    exit;
}