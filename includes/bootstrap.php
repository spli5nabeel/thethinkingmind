<?php
/**
 * Bootstrap file - loads all common includes
 * Include this file at the start of each page instead of multiple requires
 */

// Load configuration first
require_once __DIR__ . '/../config.php';

// Load utility functions
require_once __DIR__ . '/functions.php';

// Load database helper class
require_once __DIR__ . '/Database.php';

// Load template functions
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/footer.php';
require_once __DIR__ . '/../templates/navigation.php';

// Initialize database helper instance for convenience
$db = new Database(getDBConnection());

// Make $conn available for backward compatibility
$conn = $db->getConnection();
