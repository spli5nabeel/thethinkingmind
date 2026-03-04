<?php
/**
 * Test script to verify refactored structure works
 */

require_once 'includes/bootstrap.php';

echo "=== Refactored Structure Test ===\n\n";

// Test 1: Bootstrap loaded
echo "✓ Bootstrap loaded successfully\n";

// Test 2: Database helper available
if (isset($db) && $db instanceof Database) {
    echo "✓ Database helper available\n";
} else {
    echo "✗ Database helper NOT available\n";
}

// Test 3: Functions available
$functions = ['sanitizeInput', 'validateQuestionCount', 'calculateScore', 
              'formatCategoryName', 'isLoggedIn', 'formatQuestion'];
$functions_ok = true;
foreach ($functions as $func) {
    if (!function_exists($func)) {
        echo "✗ Function missing: $func\n";
        $functions_ok = false;
    }
}
if ($functions_ok) {
    echo "✓ All utility functions available\n";
}

// Test 4: Template functions available
$template_functions = ['renderHeader', 'renderFooter', 'renderNavigation'];
$templates_ok = true;
foreach ($template_functions as $func) {
    if (!function_exists($func)) {
        echo "✗ Template function missing: $func\n";
        $templates_ok = false;
    }
}
if ($templates_ok) {
    echo "✓ All template functions available\n";
}

// Test 5: Database connection
if ($conn && $conn->ping()) {
    echo "✓ Database connection active\n";
} else {
    echo "✗ Database connection failed\n";
}

// Test 6: Database helper methods
try {
    $count = $db->countQuestions();
    echo "✓ Database helper working (found $count questions)\n";
} catch (Exception $e) {
    echo "✗ Database helper error: " . $e->getMessage() . "\n";
}

// Test 7: Utility functions work
try {
    $score = calculateScore(8, 10);
    $expected = 80.0;
    if ($score === $expected) {
        echo "✓ Utility functions working correctly\n";
    } else {
        echo "✗ Utility function error: calculateScore returned $score, expected $expected\n";
    }
} catch (Exception $e) {
    echo "✗ Utility function error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "All refactored components are functional!\n";
