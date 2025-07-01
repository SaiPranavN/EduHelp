<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$host = 'localhost';
$db   = 'mini_project';
$user = 'root';
$pass = 'KVNM_710';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}

?>
