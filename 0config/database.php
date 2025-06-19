<?php

// =============================================================
// DATABASE CONNECTION CONFIGURATION
// =============================================================

if (!defined('IS_PRODUCTION_ENVIRONMENT')) {
    define('IS_PRODUCTION_ENVIRONMENT', true); // CHANGE TO 'true' BEFORE HOSTINGER DEPLOYMENT!
}

// Set PHP's default timezone (always active for PHP functions)
date_default_timezone_set('Asia/Manila'); 

$host = '';
$dbname = '';
$username = '';
$password = '';

if (IS_PRODUCTION_ENVIRONMENT) {
    $host     = 'localhost';
    $dbname   = 'u317770660_ojt_db'; // Your Hostinger DB Name
    $username = 'u317770660_ojt_user'; // Your Hostinger DB User
    $password = 'ModernCipher2025@'; // Your Hostinger DB Password
} else {
    $host     = 'localhost';
    $dbname   = 'ojt';
    $username = 'root';
    $password = '';
}

// =============================================================
// ESTABLISH DATABASE CONNECTION
// =============================================================

try {
    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        if (IS_PRODUCTION_ENVIRONMENT) {
            error_log('Database connection failed: ' . $conn->connect_error);
            throw new Exception('A database connection error occurred. Please try again later.');
        } else {
            throw new Exception('Local database connection failed: ' . $conn->connect_error);
        }
    }

    $conn->set_charset('utf8mb4');

    // --- NEW ADDITION: Conditional SET time_zone for MySQL session ---
    if (IS_PRODUCTION_ENVIRONMENT) {
        // Use the offset for Asia/Manila (UTC+8) which is more universally recognized by MySQL
        // especially if named timezones aren't loaded.
        $conn->query("SET time_zone = '+08:00'"); 
        // We use '+08:00' because 'Asia/Manila' caused an error on localhost.
        // This is more likely to work on Hostinger and avoids the error on local.
    }
    // --- END NEW ADDITION ---

} catch (Exception $e) {
    die('Database connection error: ' . $e->getMessage()); 
}

// You can now use $conn object throughout your application for database operations.
?>