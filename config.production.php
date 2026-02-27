<?php
/**
 * Production Database Configuration for IONOS
 * RENAME THIS FILE TO config.php after filling in your IONOS database credentials
 */

// Enable error reporting during setup (DISABLE after site is working!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// IONOS Database credentials - GET THESE FROM YOUR IONOS CONTROL PANEL
define('DB_HOST', 'YOUR_IONOS_DB_HOST');        // Example: 'dbXXXXXX.db.ionos.com' or 'localhost'
define('DB_USER', 'YOUR_IONOS_DB_USER');        // Example: 'dbXXXXXX'
define('DB_PASS', 'YOUR_IONOS_DB_PASSWORD');    // Your database password
define('DB_NAME', 'YOUR_IONOS_DB_NAME');        // Example: 'dbXXXXXX' or 'exam_simulator'

// Application settings
define('QUESTIONS_PER_EXAM', 500);
define('PASSING_SCORE', 70);

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection function
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        // Show detailed error during setup
        die("Database Connection Failed: " . $conn->connect_error . "<br><br>" .
            "Host: " . DB_HOST . "<br>" .
            "User: " . DB_USER . "<br>" .
            "Database: " . DB_NAME);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}
?>
