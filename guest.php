<?php
require_once 'config.php';
require_once 'auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guest_name = trim($_POST['guest_name'] ?? '');
    if (!empty($guest_name)) {
        $_SESSION['guest_name'] = $guest_name;
        $_SESSION['is_guest'] = true;
        header('Location: categories.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Mode - Exam Simulator</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>👤 Guest Mode</h1>
            <p class="subtitle">Take exams without creating an account</p>
            <a href="index.php" class="btn btn-back">← Back to Home</a>
        </header>

        <main>
            <div class="auth-container">
                <div class="auth-box">
                    <div class="info-section" style="margin-bottom: 30px;">
                        <h3>Guest Mode Features:</h3>
                        <ul>
                            <li>✓ Take practice exams immediately</li>
                            <li>✓ See your scores and review answers</li>
                            <li>✗ Results won't be saved permanently</li>
                            <li>✗ Can't track progress over time</li>
                        </ul>
                        <p style="margin-top: 15px;"><strong>Note:</strong> To save your progress, <a href="register.php">create a free account</a>.</p>
                    </div>

                    <form method="POST" class="auth-form">
                        <div class="form-group">
                            <label for="guest_name">Enter your name:</label>
                            <input type="text" 
                                   name="guest_name" 
                                   id="guest_name" 
                                   required
                                   autofocus
                                   placeholder="Your name">
                        </div>

                        <button type="submit" class="btn btn-primary btn-large">Continue as Guest</button>
                    </form>

                    <div class="auth-links">
                        <p><a href="login.php">Login</a> or <a href="register.php">Register</a> for full features</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
