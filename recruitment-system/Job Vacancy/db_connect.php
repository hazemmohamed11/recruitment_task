<?php
$host = 'localhost';
$username = 'root';
$password = '';
$db_name = 'recruitment_task';

try {
    $dsn = "mysql:host=$host;dbname=$db_name";
    $connection = new PDO($dsn, $username, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
