<?php
/**
 * Database Connection Test for IONOS
 * Upload this file and visit: http://thethinkingmind.net/test-connection.php
 * DELETE THIS FILE after confirming the connection works!
 */

// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔍 IONOS Connection Diagnostics</h1>";
echo "<hr>";

// Test 1: PHP Version
echo "<h2>✓ PHP Version</h2>";
echo "PHP Version: <strong>" . phpversion() . "</strong><br>";
echo (version_compare(phpversion(), '7.4.0', '>=')) ? "✅ Compatible (7.4+)<br>" : "❌ Too old, need 7.4+<br>";
echo "<hr>";

// Test 2: MySQLi Extension
echo "<h2>✓ MySQLi Extension</h2>";
echo extension_loaded('mysqli') ? "✅ MySQLi is installed<br>" : "❌ MySQLi is NOT installed<br>";
echo "<hr>";

// Test 3: Config file exists
echo "<h2>✓ Config File</h2>";
if (file_exists('config.php')) {
    echo "✅ config.php exists<br>";
    require_once 'config.php';
    
    echo "<h3>Current Settings:</h3>";
    echo "DB_HOST: <strong>" . DB_HOST . "</strong><br>";
    echo "DB_USER: <strong>" . DB_USER . "</strong><br>";
    echo "DB_NAME: <strong>" . DB_NAME . "</strong><br>";
    echo "DB_PASS: " . (DB_PASS ? "✅ Set (hidden)" : "❌ Empty") . "<br>";
    echo "<hr>";
    
    // Test 4: Database Connection
    echo "<h2>✓ Database Connection</h2>";
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        echo "❌ <strong>Connection FAILED</strong><br>";
        echo "Error: " . $conn->connect_error . "<br>";
        echo "Error Code: " . $conn->connect_errno . "<br><br>";
        
        echo "<h3>Common Fixes:</h3>";
        echo "<ul>";
        echo "<li>Check DB_HOST in config.php (try 'localhost' if using IONOS)</li>";
        echo "<li>Verify DB_USER and DB_PASS are correct</li>";
        echo "<li>Confirm database DB_NAME exists in IONOS panel</li>";
        echo "<li>Check if remote connections are allowed</li>";
        echo "</ul>";
    } else {
        echo "✅ <strong>Connection SUCCESS!</strong><br>";
        echo "Connected to: <strong>" . DB_NAME . "</strong><br>";
        echo "Server: " . $conn->server_info . "<br>";
        echo "<hr>";
        
        // Test 5: Check if tables exist
        echo "<h2>✓ Database Tables</h2>";
        $tables = ['questions', 'exam_results', 'exam_answers', 'users'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
                $count = $count_result->fetch_assoc()['count'];
                echo "✅ Table '<strong>$table</strong>' exists ($count rows)<br>";
            } else {
                echo "❌ Table '<strong>$table</strong>' is MISSING<br>";
            }
        }
        
        $conn->close();
        echo "<hr>";
        echo "<h2>🎉 Diagnosis Complete</h2>";
        echo "<p><strong>If all checks passed, your site should work!</strong></p>";
        echo "<p style='color: red;'><strong>⚠️ DELETE THIS FILE (test-connection.php) NOW for security!</strong></p>";
    }
} else {
    echo "❌ config.php NOT FOUND<br>";
    echo "<p>You need to upload config.php with your IONOS database settings.</p>";
}
?>
