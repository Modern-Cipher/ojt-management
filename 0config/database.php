<?php

// =============================================================
// DATABASE CONNECTION CONFIGURATION
// =============================================================

// Define environment constant only if it hasn't been defined yet
// This prevents the "Constant already defined" warning if this file is included multiple times
if (!defined('IS_PRODUCTION_ENVIRONMENT')) {
    // Set to 'true' if running on the live Hostinger server (production)
    // Set to 'false' if running on your local development server
    define('IS_PRODUCTION_ENVIRONMENT', true); // <-- Default for local testing. CHANGE TO 'true' BEFORE HOSTINGER DEPLOYMENT!
}

// Initialize variables for database credentials
$host = '';
$dbname = '';
$username = '';
$password = '';

// Conditional logic to set credentials based on environment
if (IS_PRODUCTION_ENVIRONMENT) {
    // Hostinger Database Credentials
    // You MUST replace these placeholder values with your ACTUAL Hostinger database details
    // You will get these details after creating your database in Hostinger hPanel.
    $host     = 'localhost';                                 // Usually 'localhost' for Hostinger
    $dbname   = 'u317770660_ojt_db';                           // Example: u123456789_ojt_database
    $username = 'u317770660_ojt_user';                         // Example: u123456789_ojt_user
    $password = 'ModernCipher2025@';            // The password you set for the DB user
} else {
    // Local Development Database Credentials
    // These are your current local settings (e.g., for XAMPP, WAMP, Laragon)
    $host     = 'localhost';
    $dbname   = 'ojt';
    $username = 'root';
    $password = ''; // Typically empty for 'root' user in local dev
}

// =============================================================
// ESTABLISH DATABASE CONNECTION
// =============================================================

try {
    // Create a new MySQLi connection object
    $conn = new mysqli($host, $username, $password, $dbname);
    
    // Check if the connection was successful
    if ($conn->connect_error) {
        // If in production, log the error but show a generic message to the user for security.
        // If local, show the direct error for easier debugging.
        if (IS_PRODUCTION_ENVIRONMENT) {
            error_log('Database connection failed: ' . $conn->connect_error);
            throw new Exception('A database connection error occurred. Please try again later.');
        } else {
            // For local development, display the specific error for debugging
            throw new Exception('Local database connection failed: ' . $conn->connect_error);
        }
    }
    
    // Set the character encoding for the connection to UTF-8
    // This helps prevent issues with special characters in your data.
    $conn->set_charset('utf8mb4');

} catch (Exception $e) {
    // If a connection error occurs, terminate script execution and display the error message.
    die('Database connection error: ' . $e->getMessage()); 
}

// You can now use $conn object throughout your application for database operations.
?>