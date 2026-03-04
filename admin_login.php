<?php
require_once 'config.php';
require_once 'auth.php';

// If already logged in as admin, redirect to admin panel
if (isLoggedIn() && $_SESSION['user_role'] === 'admin') {
    header('Location: admin.php');
    exit();
}

// Clear any previous session
if (isLoggedIn()) {
    logout();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username_or_email) || empty($password)) {
        $error = 'Please enter both username/email and password';
    } else {
        $result = login($username_or_email, $password);
        
        if ($result['success']) {
            // Check if user is admin
            if ($result['user']['role'] === 'admin') {
                // Admin login successful
                $_SESSION['admin_login'] = true;
                header('Location: admin.php');
                exit();
            } else {
                // Not admin, logout and show error
                logout();
                $error = 'Only administrators can access this area. Please contact your administrator.';
            }
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - The Thinking Mind</title>
    <meta name="description" content="Administrator login for The Thinking Mind platform management.">
    <meta name="robots" content="noindex,follow">
    <link rel="canonical" href="https://thethinkingmind.net/admin_login.php">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🔐 Administrator Access</h1>
            <p class="subtitle">Platform management and content administration</p>
            <a href="index.php" class="btn btn-back">← Back to Home</a>
        </header>

        <main>
            <div class="auth-container">
                <?php if ($error): ?>
                    <div class="message error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div class="auth-box">
                    <div class="admin-warning">
                        <h3>🔒 Admin Access Only</h3>
                        <p>This portal is restricted to administrators. Only authorized personnel should have login credentials.</p>
                    </div>

                    <form method="POST" class="auth-form">
                        <div class="form-group">
                            <label for="username">Username or Email:</label>
                            <input type="text" 
                                   name="username" 
                                   id="username" 
                                   required 
                                   autofocus
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" name="password" id="password" required>
                        </div>

                        <button type="submit" class="btn btn-admin btn-large">Login as Admin</button>
                    </form>
                </div>
            </div>

            <div class="info-section" style="margin-top: 40px; max-width: 600px; margin-left: auto; margin-right: auto;">
                <h3>📖 Information</h3>
                <ul>
                    <li><strong>Students/Visitors:</strong> Go back to <a href="index.php">home page</a> to take exams without logging in</li>
                    <li><strong>Admins:</strong> Use your credentials to access the admin panel</li>
                    <li><strong>Forgot Password:</strong> Contact your system administrator</li>
                </ul>
            </div>
        </main>
    </div>
</body>
</html>
