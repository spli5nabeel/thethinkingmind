<?php
// Script to create default admin and student users with properly hashed passwords

require_once 'config.php';

$conn = getDBConnection();

// Create admin user
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$admin_sql = "INSERT INTO users (username, email, password_hash, full_name, role) 
              VALUES ('admin', 'admin@example.com', '$admin_password', 'Administrator', 'admin')
              ON DUPLICATE KEY UPDATE password_hash='$admin_password'";

if ($conn->query($admin_sql)) {
    echo "✓ Admin user created/updated successfully\n";
    echo "  Username: admin\n";
    echo "  Password: admin123\n\n";
} else {
    echo "✗ Error creating admin user: " . $conn->error . "\n\n";
}

// Create student user
$student_password = password_hash('student123', PASSWORD_DEFAULT);
$student_sql = "INSERT INTO users (username, email, password_hash, full_name, role) 
                VALUES ('student', 'student@example.com', '$student_password', 'Sample Student', 'student')
                ON DUPLICATE KEY UPDATE password_hash='$student_password'";

if ($conn->query($student_sql)) {
    echo "✓ Student user created/updated successfully\n";
    echo "  Username: student\n";
    echo "  Password: student123\n\n";
} else {
    echo "✗ Error creating student user: " . $conn->error . "\n\n";
}

echo "Default users are ready!\n";

$conn->close();
?>
