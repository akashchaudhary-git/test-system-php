<?php
$host = 'localhost';  // Replace with your database host
$db = 'testup';  // Replace with your database name
$user = 'root';  // Replace with your database username
$password = '';  // Replace with your database password

// Create a new PDO instance for database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // You can add additional configuration options if needed

    // Pass the PDO object to the form page
    //require_once('form.php');
    
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
