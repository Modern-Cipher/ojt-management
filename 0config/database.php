<?php

// Database connection settings
$host = 'localhost'; // Change if necessary
$dbname = 'ojt';
$username = 'root'; // Change if necessary
$password = ''; // Change if you have set a password

try {
    // Create a new MySQLi connection
    $conn = new mysqli($host, $username, $password, $dbname);
    
    // Check the connection
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Set character encoding to UTF-8
    $conn->set_charset('utf8mb4');

} catch (Exception $e) {
    die('Database error: ' . $e->getMessage()); //removed the loader div.
}
?>