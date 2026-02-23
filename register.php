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
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $result = register($username, $email, $password, $full_name);
        
        if ($result['success']) {
            header('Location: login.php?registered=1');
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
    <title>Register - Exam Simulator</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📝 Register</h1>
            <p class="subtitle">Create your account</p>
            <a href="index.php" class="btn btn-back">← Back to Home</a>
        </header>

        <main>
            <div class="auth-container">
                <?php if ($error): ?>
                    <div class="message error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div class="auth-box">
                    <form method="POST" class="auth-form">
                        <div class="form-group">
                            <label for="full_name">Full Name:</label>
                            <input type="text" 
                                   name="full_name" 
                                   id="full_name" 
                                   required
                                   value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" 
                                   name="username" 
                                   id="username" 
                                   required
                                   minlength="3"
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                            <small>At least 3 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   required
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   required
                                   minlength="6">
                            <small>At least 6 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password:</label>
                            <input type="password" 
                                   name="confirm_password" 
                                   id="confirm_password" 
                                   required
                                   minlength="6">
                        </div>

                        <button type="submit" class="btn btn-primary btn-large">Register</button>
                    </form>

                    <div class="auth-links">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
