<?php
require_once 'config.php';
require_once 'auth.php';

// Require admin login
requireAdmin();

$conn = getDBConnection();
$message = '';
$messageType = '';
$imported_count = 0;
$skipped_count = 0;
$errors = [];

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file'];
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $messageType = 'error';
        $message = 'File upload error: ' . $file['error'];
    } elseif ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        $messageType = 'error';
        $message = 'File is too large. Maximum size is 5MB.';
    } elseif (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'csv') {
        $messageType = 'error';
        $message = 'Please upload a CSV file.';
    } else {
        // Open and read CSV file
        if (($handle = fopen($file['tmp_name'], 'r')) !== false) {
            $row_number = 0;
            $header_row = null;
            
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $row_number++;
                
                // Skip empty rows
                if (empty(array_filter($data))) {
                    continue;
                }
                
                // First row is header
                if ($row_number === 1) {
                    $header_row = $data;
                    // Validate header
                    $expected_headers = ['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'category', 'difficulty'];
                    if (count($data) < 8) {
                        $messageType = 'error';
                        $message = 'CSV format error: Missing required columns.';
                        break;
                    }
                    continue;
                }
                
                // Validate row has enough columns
                if (count($data) < 8) {
                    $errors[] = "Row $row_number: Missing required columns.";
                    $skipped_count++;
                    continue;
                }
                
                // Extract data from row
                $question = trim($data[0]);
                $option_a = trim($data[1]);
                $option_b = trim($data[2]);
                $option_c = trim($data[3]);
                $option_d = trim($data[4]);
                $correct_answer = strtoupper(trim($data[5]));
                $category = trim($data[6]);
                $difficulty = trim($data[7]);
                
                // Validate required fields
                if (empty($question) || empty($option_a) || empty($option_b) || 
                    empty($option_c) || empty($option_d) || empty($correct_answer) || 
                    empty($category) || empty($difficulty)) {
                    $errors[] = "Row $row_number: Missing required fields.";
                    $skipped_count++;
                    continue;
                }
                
                // Validate correct answer
                if (!in_array($correct_answer, ['A', 'B', 'C', 'D'])) {
                    $errors[] = "Row $row_number: Correct answer must be A, B, C, or D.";
                    $skipped_count++;
                    continue;
                }
                
                // Validate difficulty
                if (!in_array($difficulty, ['Easy', 'Medium', 'Hard'])) {
                    $errors[] = "Row $row_number: Difficulty must be Easy, Medium, or Hard.";
                    $skipped_count++;
                    continue;
                }
                
                // Escape strings for SQL
                $question = $conn->real_escape_string($question);
                $option_a = $conn->real_escape_string($option_a);
                $option_b = $conn->real_escape_string($option_b);
                $option_c = $conn->real_escape_string($option_c);
                $option_d = $conn->real_escape_string($option_d);
                $category = $conn->real_escape_string($category);
                
                // Insert question
                $sql = "INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_answer, category, difficulty) 
                        VALUES ('$question', '$option_a', '$option_b', '$option_c', '$option_d', '$correct_answer', '$category', '$difficulty')";
                
                if ($conn->query($sql)) {
                    $imported_count++;
                } else {
                    $errors[] = "Row $row_number: Database error - " . $conn->error;
                    $skipped_count++;
                }
            }
            
            fclose($handle);
            
            // Set final message
            if ($imported_count > 0 || $skipped_count > 0) {
                $messageType = 'success';
                $message = "📊 Import completed! Imported: $imported_count questions";
                if ($skipped_count > 0) {
                    $messageType = 'success';
                    $message .= " | Skipped: $skipped_count rows";
                }
            }
        } else {
            $messageType = 'error';
            $message = 'Error reading file.';
        }
    }
}

// Redirect back to admin.php with message
$_SESSION['import_message'] = $message;
$_SESSION['import_type'] = $messageType;
$_SESSION['import_errors'] = $errors;

header('Location: admin.php');
exit;

?>
