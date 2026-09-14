<?php
// DATABASE CONFIG
$host = "localhost";
$db_name = "your_database_name";
$username = "your_database_username";
$password = "your_database_password";

// PDO connection
try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$db_name;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Database connection failed. Please try again later.");
}
