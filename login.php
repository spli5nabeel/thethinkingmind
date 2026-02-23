<?php
require_once 'config.php';
require_once 'auth.php';

$error = '';
$success = '';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username_or_email) || empty($password)) {
        $error = 'Please enter both username/email and password';
    } else {
        $result = login($username_or_email, $password);
        
        if ($result['success']) {
            // Redirect to requested page or dashboard
            $redirect = $_SESSION['redirect_after_login'] ?? 'dashboard.php';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit();
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
    <title>Login - Exam Simulator</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🔐 Login</h1>
            <p class="subtitle">Sign in to your account</p>
            <a href="index.php" class="btn btn-back">← Back to Home</a>
        </header>

        <main>
            <div class="auth-container">
                <?php if ($error): ?>
                    <div class="message error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if (isset($_GET['registered'])): ?>
                    <div class="message success">Registration successful! Please login.</div>
                <?php endif; ?>

                <div class="auth-box">
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

                        <button type="submit" class="btn btn-primary btn-large">Login</button>
                    </form>

                    <div class="auth-links">
                        <p>Don't have an account? <a href="register.php">Register here</a></p>
                        <p>Or <a href="guest.php">continue as guest</a></p>
                    </div>

                    <div class="demo-credentials">
                        <h4>Demo Accounts:</h4>
                        <p><strong>Admin:</strong> username: <code>admin</code>, password: <code>admin123</code></p>
                        <p><strong>Student:</strong> username: <code>student</code>, password: <code>student123</code></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
