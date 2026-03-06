<?php
require_once 'config.php';
require_once 'auth.php';

requireAdmin();

$conn = getDBConnection();
$message = '';
$messageType = '';
$import_summary = [];

// Handle JSON file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['json_file'])) {
    $file = $_FILES['json_file'];
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = "Error uploading file: " . $file['error'];
        $messageType = "error";
    } elseif (mime_content_type($file['tmp_name']) !== 'application/json' && pathinfo($file['name'], PATHINFO_EXTENSION) !== 'json') {
        $message = "Please upload a valid JSON file";
        $messageType = "error";
    } else {
        // Read and parse JSON
        $json_content = file_get_contents($file['tmp_name']);
        $questions = json_decode($json_content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $message = "Invalid JSON file: " . json_last_error_msg();
            $messageType = "error";
        } elseif (!is_array($questions)) {
            $message = "JSON must contain an array of questions";
            $messageType = "error";
        } else {
            // Process questions
            $imported = 0;
            $skipped = 0;
            $errors = [];
            
            foreach ($questions as $index => $q) {
                try {
                    // Validate required fields
                    $required_fields = ['category', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer'];
                    $missing = [];
                    
                    foreach ($required_fields as $field) {
                        if (empty($q[$field])) {
                            $missing[] = $field;
                        }
                    }
                    
                    if (!empty($missing)) {
                        $errors[] = "Question " . ($index + 1) . ": Missing fields - " . implode(', ', $missing);
                        $skipped++;
                        continue;
                    }
                    
                    // Validate correct answer
                    $correct = strtoupper($q['correct_answer']);
                    if (!in_array($correct, ['A', 'B', 'C', 'D'])) {
                        $errors[] = "Question " . ($index + 1) . ": Invalid correct answer (must be A, B, C, or D)";
                        $skipped++;
                        continue;
                    }
                    
                    // Sanitize inputs
                    $category = $conn->real_escape_string(trim($q['category']));
                    $question_text = $conn->real_escape_string(trim($q['question_text']));
                    $option_a = $conn->real_escape_string(trim($q['option_a']));
                    $option_b = $conn->real_escape_string(trim($q['option_b']));
                    $option_c = $conn->real_escape_string(trim($q['option_c']));
                    $option_d = $conn->real_escape_string(trim($q['option_d']));
                    
                    // Map difficulty levels to database allowed values
                    $difficulty_map = [
                        'beginner' => 'Easy',
                        'easy' => 'Easy',
                        'intermediate' => 'Medium',
                        'medium' => 'Medium',
                        'advanced' => 'Hard',
                        'hard' => 'Hard'
                    ];
                    $raw_difficulty = !empty($q['difficulty']) ? strtolower(trim($q['difficulty'])) : 'medium';
                    $difficulty = $difficulty_map[$raw_difficulty] ?? 'Medium';
                    
                    $explanation = !empty($q['explanation']) ? $conn->real_escape_string(trim($q['explanation'])) : '';
                    
                    // Insert question
                    $sql = "INSERT INTO questions (category, question_text, option_a, option_b, option_c, option_d, correct_answer, difficulty, explanation) 
                            VALUES ('$category', '$question_text', '$option_a', '$option_b', '$option_c', '$option_d', '$correct', '$difficulty', '$explanation')";
                    
                    if ($conn->query($sql)) {
                        $imported++;
                        
                        // Store category metadata if not exists
                        $category_type = isset($q['category_type']) ? $q['category_type'] : 'Academic';
                        if (in_array($category_type, ['IT', 'Academic'])) {
                            $metadata_sql = "INSERT IGNORE INTO category_metadata (category_name, category_type) VALUES ('$category', '$category_type')";
                            $conn->query($metadata_sql);
                        }
                    } else {
                        $errors[] = "Question " . ($index + 1) . ": Database error - " . $conn->error;
                        $skipped++;
                    }
                    
                } catch (Exception $e) {
                    $errors[] = "Question " . ($index + 1) . ": " . $e->getMessage();
                    $skipped++;
                }
            }
            
            // Set summary message
            if ($imported > 0) {
                $message = "✅ Successfully imported $imported question(s)";
                $messageType = "success";
            } else {
                $message = "❌ No questions were imported";
                $messageType = "error";
            }
            
            if ($skipped > 0) {
                $message .= " ($skipped skipped)";
            }
            
            $import_summary = [
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Questions from JSON - Exam Simulator</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .import-container {
            max-width: 700px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .upload-area {
            border: 2px dashed #00897b;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            background: #f0f8f7;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            background: #e0f2f1;
            border-color: #00695c;
        }
        .upload-area.dragover {
            background: #c8e6e5;
            border-color: #00695c;
        }
        .upload-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .upload-text {
            color: #333;
            margin-bottom: 10px;
        }
        .upload-hint {
            color: #999;
            font-size: 0.9em;
        }
        #jsonFile {
            display: none;
        }
        .file-info {
            margin-top: 20px;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 5px;
            display: none;
        }
        .file-info.show {
            display: block;
        }
        .template-link {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: #00897b;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9em;
            cursor: pointer;
        }
        .template-link:hover {
            background: #00695c;
        }
        .sample-json {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #00897b;
            max-height: 300px;
            overflow-y: auto;
            font-family: monospace;
            font-size: 0.85em;
        }
        .error-list {
            background: #ffebee;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
            max-height: 250px;
            overflow-y: auto;
        }
        .error-list li {
            color: #c62828;
            margin-bottom: 5px;
        }
        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        .summary-card {
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            color: white;
        }
        .summary-card.imported {
            background: #4caf50;
        }
        .summary-card.skipped {
            background: #ff9800;
        }
        .summary-card.errors {
            background: #f44336;
        }
        .summary-card .number {
            font-size: 2.5em;
            font-weight: bold;
        }
        .summary-card .label {
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📥 Import Questions from JSON</h1>
            <p class="subtitle">Bulk upload exam questions from a JSON file</p>
            <div class="header-buttons">
                <a href="admin.php" class="btn btn-back">← Back to Admin</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </header>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>" style="margin: 20px auto; max-width: 700px;">
                <?php echo $message; ?>
            </div>
            
            <?php if (!empty($import_summary)): ?>
                <div class="import-container">
                    <div class="summary">
                        <div class="summary-card imported">
                            <div class="number"><?php echo $import_summary['imported']; ?></div>
                            <div class="label">Imported</div>
                        </div>
                        <div class="summary-card skipped">
                            <div class="number"><?php echo $import_summary['skipped']; ?></div>
                            <div class="label">Skipped</div>
                        </div>
                        <div class="summary-card errors">
                            <div class="number"><?php echo count($import_summary['errors']); ?></div>
                            <div class="label">Errors</div>
                        </div>
                    </div>
                    
                    <?php if (!empty($import_summary['errors'])): ?>
                        <div style="margin-top: 20px;">
                            <h3 style="color: #d32f2f; margin-bottom: 10px;">Issues Found:</h3>
                            <div class="error-list">
                                <ul>
                                    <?php foreach ($import_summary['errors'] as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="import-container">
            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">📄</div>
                    <div class="upload-text">Click to upload or drag and drop</div>
                    <div class="upload-hint">JSON file with question array</div>
                    <input type="file" id="jsonFile" name="json_file" accept=".json" required>
                </div>
                
                <div class="file-info" id="fileInfo">
                    <strong>Selected file:</strong> <span id="fileName"></span>
                    <br><strong>Size:</strong> <span id="fileSize"></span>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px; padding: 12px;">
                    🚀 Import Questions
                </button>
            </form>

            <div style="margin-top: 30px;">
                <h3>📋 JSON Format Reference</h3>
                <div class="sample-json">
<pre>[
  {
    "category": "KCSA",
    "question_text": "Your question here?",
    "option_a": "Option A text",
    "option_b": "Option B text",
    "option_c": "Option C text",
    "option_d": "Option D text",
    "correct_answer": "C",
    "difficulty": "intermediate",
    "explanation": "Why C is correct...",
    "category_type": "IT"
  }
]</pre>
                </div>
                
                <p style="margin-top: 15px; color: #666;">
                    <strong>Required fields:</strong> category, question_text, option_a, option_b, option_c, option_d, correct_answer<br>
                    <strong>Optional fields:</strong> difficulty (Easy/Medium/Hard), explanation, category_type (IT/Academic)
                </p>
            </div>
        </div>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const jsonFile = document.getElementById('jsonFile');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');

        // Click to upload
        uploadArea.addEventListener('click', () => jsonFile.click());

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            jsonFile.files = e.dataTransfer.files;
            updateFileInfo();
        });

        // File selection
        jsonFile.addEventListener('change', updateFileInfo);

        function updateFileInfo() {
            if (jsonFile.files.length > 0) {
                const file = jsonFile.files[0];
                fileName.textContent = file.name;
                fileSize.textContent = (file.size / 1024).toFixed(2) + ' KB';
                fileInfo.classList.add('show');
            }
        }
    </script>
</body>
</html>
