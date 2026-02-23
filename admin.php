<?php
require_once 'config.php';
require_once 'auth.php';

// Require admin login
requireAdmin();

$conn = getDBConnection();
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $question = $conn->real_escape_string($_POST['question']);
                $option_a = $conn->real_escape_string($_POST['option_a']);
                $option_b = $conn->real_escape_string($_POST['option_b']);
                $option_c = $conn->real_escape_string($_POST['option_c']);
                $option_d = $conn->real_escape_string($_POST['option_d']);
                $correct = $conn->real_escape_string($_POST['correct_answer']);
                $category = $conn->real_escape_string($_POST['category']);
                $difficulty = $conn->real_escape_string($_POST['difficulty']);
                
                $sql = "INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_answer, category, difficulty) 
                        VALUES ('$question', '$option_a', '$option_b', '$option_c', '$option_d', '$correct', '$category', '$difficulty')";
                
                if ($conn->query($sql)) {
                    $message = "Question added successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error adding question: " . $conn->error;
                    $messageType = "error";
                }
                break;
                
            case 'delete':
                $id = intval($_POST['question_id']);
                $sql = "DELETE FROM questions WHERE id = $id";
                
                if ($conn->query($sql)) {
                    $message = "Question deleted successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error deleting question: " . $conn->error;
                    $messageType = "error";
                }
                break;
        }
    }
}

// Fetch all questions
$questions = $conn->query("SELECT * FROM questions ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Exam Simulator</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>⚙️ Admin Panel</h1>
            <p class="subtitle">Manage exam questions and categories</p>
            <div class="header-buttons">
                <a href="manage_categories.php" class="btn btn-secondary">📁 Manage Categories</a>
                <a href="index.php" class="btn btn-back">← Back to Home</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </header>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <main>
            <section class="admin-section">
                <h2>Add New Question</h2>
                <form method="POST" class="question-form">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="question">Question Text:</label>
                        <textarea name="question" id="question" required rows="3"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="option_a">Option A:</label>
                            <input type="text" name="option_a" id="option_a" required>
                        </div>
                        <div class="form-group">
                            <label for="option_b">Option B:</label>
                            <input type="text" name="option_b" id="option_b" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="option_c">Option C:</label>
                            <input type="text" name="option_c" id="option_c" required>
                        </div>
                        <div class="form-group">
                            <label for="option_d">Option D:</label>
                            <input type="text" name="option_d" id="option_d" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="correct_answer">Correct Answer:</label>
                            <select name="correct_answer" id="correct_answer" required>
                                <option value="">Select...</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="category">Category:</label>
                            <input type="text" name="category" id="category" value="General" required>
                        </div>
                        <div class="form-group">
                            <label for="difficulty">Difficulty:</label>
                            <select name="difficulty" id="difficulty" required>
                                <option value="Easy">Easy</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="Hard">Hard</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Question</button>
                </form>
            </section>

            <section class="admin-section">
                <h2>Existing Questions (<?php echo $questions->num_rows; ?>)</h2>
                <div class="questions-list">
                    <?php while ($q = $questions->fetch_assoc()): ?>
                        <div class="question-item">
                            <div class="question-header">
                                <span class="question-id">#<?php echo $q['id']; ?></span>
                                <span class="badge <?php echo strtolower($q['difficulty']); ?>">
                                    <?php echo $q['difficulty']; ?>
                                </span>
                                <span class="badge category"><?php echo $q['category']; ?></span>
                            </div>
                            <div class="question-text">
                                <strong>Q:</strong> <?php echo htmlspecialchars($q['question_text']); ?>
                            </div>
                            <div class="question-options">
                                <div class="option <?php echo $q['correct_answer'] === 'A' ? 'correct' : ''; ?>">
                                    <strong>A:</strong> <?php echo htmlspecialchars($q['option_a']); ?>
                                </div>
                                <div class="option <?php echo $q['correct_answer'] === 'B' ? 'correct' : ''; ?>">
                                    <strong>B:</strong> <?php echo htmlspecialchars($q['option_b']); ?>
                                </div>
                                <div class="option <?php echo $q['correct_answer'] === 'C' ? 'correct' : ''; ?>">
                                    <strong>C:</strong> <?php echo htmlspecialchars($q['option_c']); ?>
                                </div>
                                <div class="option <?php echo $q['correct_answer'] === 'D' ? 'correct' : ''; ?>">
                                    <strong>D:</strong> <?php echo htmlspecialchars($q['option_d']); ?>
                                </div>
                            </div>
                            <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="question_id" value="<?php echo $q['id']; ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

<?php $conn->close(); ?>
