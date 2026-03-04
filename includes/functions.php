<?php
/**
 * Common utility functions for the exam simulator
 * Refactored for better code organization
 */

/**
 * Sanitize user input to prevent SQL injection
 * @param mysqli $conn Database connection
 * @param string $value Value to sanitize
 * @return string Sanitized value
 */
function sanitizeInput($conn, $value) {
    return $conn->real_escape_string(trim($value));
}

/**
 * Validate and sanitize question count
 * @param int $count Question count from user input
 * @param int $min Minimum allowed questions (default: 5)
 * @param int $max Maximum allowed questions (default: 50)
 * @return int Validated question count
 */
function validateQuestionCount($count, $min = 5, $max = 50) {
    $count = intval($count);
    return max($min, min($max, $count));
}

/**
 * Calculate score percentage
 * @param int $correct Number of correct answers
 * @param int $total Total number of questions
 * @return float Score percentage
 */
function calculateScore($correct, $total) {
    return ($total > 0) ? ($correct / $total) * 100 : 0;
}

/**
 * Get passing status based on score
 * @param float $score Score percentage
 * @param float $passing_score Passing threshold (default: from config)
 * @return bool True if passed
 */
function isPassing($score, $passing_score = null) {
    if ($passing_score === null) {
        $passing_score = defined('PASSING_SCORE') ? PASSING_SCORE : 70;
    }
    return $score >= $passing_score;
}

/**
 * Format category name for display
 * @param string|null $category Category name
 * @return string Formatted category name
 */
function formatCategoryName($category) {
    return $category ? htmlspecialchars($category) : "All Subjects";
}

/**
 * Format difficulty level for display
 * @param string|null $difficulty Difficulty level
 * @return string Formatted difficulty
 */
function formatDifficultyLevel($difficulty) {
    return $difficulty ? ucfirst(htmlspecialchars($difficulty)) : "Mixed";
}

/**
 * Generate a random exam ID
 * @return string Exam ID
 */
function generateExamId() {
    return 'exam_' . date('YmdHis') . '_' . substr(md5(uniqid(rand(), true)), 0, 8);
}

/**
 * Redirect to a page
 * @param string $page Page to redirect to
 * @param array $params Optional query parameters
 */
function redirectTo($page, $params = []) {
    $url = $page;
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    header("Location: $url");
    exit();
}

/**
 * Set flash message in session
 * @param string $message Message text
 * @param string $type Message type (success, error, info)
 */
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Get and clear flash message from session
 * @return array|null Array with 'message' and 'type' keys, or null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'message' => $_SESSION['flash_message'],
            'type' => $_SESSION['flash_type']
        ];
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message HTML
 * @return string HTML for flash message
 */
function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        $type = htmlspecialchars($flash['type']);
        $message = htmlspecialchars($flash['message']);
        return "<div class='message {$type}'>{$message}</div>";
    }
    return '';
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Check if user is admin
 * @return bool
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Get current logged in user info
 * @return array|null User info array or null
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'] ?? 'student'
        ];
    }
    return null;
}

/**
 * Validate difficulty level
 * @param string $difficulty Difficulty to validate
 * @return bool
 */
function isValidDifficulty($difficulty) {
    return in_array($difficulty, ['easy', 'medium', 'hard'], true);
}

/**
 * Validate answer option
 * @param string $answer Answer option (A, B, C, D)
 * @return bool
 */
function isValidAnswer($answer) {
    return in_array($answer, ['A', 'B', 'C', 'D'], true);
}

/**
 * Format question for display (prevent XSS)
 * @param string $text Question text
 * @return string Safe HTML
 */
function formatQuestion($text) {
    return nl2br(htmlspecialchars($text));
}

/**
 * Get grade based on score percentage
 * @param float $score Score percentage
 * @return string Grade letter
 */
function getGrade($score) {
    if ($score >= 90) return 'A';
    if ($score >= 80) return 'B';
    if ($score >= 70) return 'C';
    if ($score >= 60) return 'D';
    return 'F';
}

/**
 * Format timestamp for display
 * @param string $timestamp MySQL timestamp
 * @return string Formatted date
 */
function formatDate($timestamp) {
    return date('M d, Y g:i A', strtotime($timestamp));
}
