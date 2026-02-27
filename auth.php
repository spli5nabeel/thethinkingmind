<?php
require_once 'config.php';

// Authentication helper functions

function tableExists($conn, $table_name) {
    $table_name = $conn->real_escape_string($table_name);
    $result = $conn->query("SHOW TABLES LIKE '$table_name'");
    return $result && $result->num_rows > 0;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if ($_SESSION['user_role'] !== 'admin') {
        $_SESSION['error_message'] = "Access denied. Admin privileges required.";
        header('Location: index.php');
        exit();
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    try {
        $conn = getDBConnection();
        if (!tableExists($conn, 'users')) {
            return null;
        }

        $user_id = intval($_SESSION['user_id']);
        $result = $conn->query("SELECT id, username, email, full_name, role, created_at FROM users WHERE id = $user_id AND is_active = 1");

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    } catch (Throwable $e) {
        return null;
    }
    
    // Invalid session
    logout();
    return null;
}

function login($username_or_email, $password) {
    try {
        $conn = getDBConnection();

        if (!tableExists($conn, 'users')) {
            return ['success' => false, 'message' => 'User authentication is not initialized. Please import user_management.sql on the server.'];
        }

        $username_or_email = $conn->real_escape_string($username_or_email);

        $sql = "SELECT * FROM users WHERE (username = '$username_or_email' OR email = '$username_or_email') AND is_active = 1";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];

                $user_id = $user['id'];
                $conn->query("UPDATE users SET last_login = NOW() WHERE id = $user_id");

                logActivity($user['id'], 'login', 'User logged in');

                return ['success' => true, 'user' => $user];
            }
        }
    } catch (Throwable $e) {
        return ['success' => false, 'message' => 'Login is temporarily unavailable. Please verify server database setup.'];
    }
    
    return ['success' => false, 'message' => 'Invalid username/email or password'];
}

function register($username, $email, $password, $full_name) {
    $conn = getDBConnection();
    
    // Validate inputs
    $username = trim($username);
    $email = trim($email);
    $full_name = trim($full_name);
    
    if (strlen($username) < 3) {
        return ['success' => false, 'message' => 'Username must be at least 3 characters'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email address'];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters'];
    }
    
    try {
        if (!tableExists($conn, 'users')) {
            return ['success' => false, 'message' => 'Registration is not initialized. Please import user_management.sql on the server.'];
        }

        $username_esc = $conn->real_escape_string($username);
        $email_esc = $conn->real_escape_string($email);

        $check = $conn->query("SELECT id FROM users WHERE username = '$username_esc' OR email = '$email_esc'");
        if ($check && $check->num_rows > 0) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $full_name_esc = $conn->real_escape_string($full_name);

        $sql = "INSERT INTO users (username, email, password_hash, full_name, role) 
                VALUES ('$username_esc', '$email_esc', '$password_hash', '$full_name_esc', 'student')";

        if ($conn->query($sql)) {
            return ['success' => true, 'message' => 'Registration successful! Please login.'];
        }
    } catch (Throwable $e) {
        return ['success' => false, 'message' => 'Registration is temporarily unavailable. Please verify server database setup.'];
    }
    
    return ['success' => false, 'message' => 'Registration failed. Please try again.'];
}

function logout() {
    if (isLoggedIn()) {
        logActivity($_SESSION['user_id'], 'logout', 'User logged out');
    }
    
    session_destroy();
    session_start();
}

function logActivity($user_id, $activity_type, $details = '') {
    try {
        $conn = getDBConnection();
        if (!tableExists($conn, 'user_activity')) {
            return;
        }

        $user_id = intval($user_id);
        $activity_type = $conn->real_escape_string($activity_type);
        $details = $conn->real_escape_string($details);
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $conn->query("INSERT INTO user_activity (user_id, activity_type, activity_details, ip_address) 
                      VALUES ($user_id, '$activity_type', '$details', '$ip')");
    } catch (Throwable $e) {
        return;
    }
}

function getGuestName() {
    return isset($_SESSION['guest_name']) ? $_SESSION['guest_name'] : null;
}

function isGuest() {
    return !isLoggedIn() && isset($_SESSION['guest_name']);
}
?>
