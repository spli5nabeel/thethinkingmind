<?php
require_once 'config.php';

$conn = getDBConnection();

// Get category filter if provided
$category = isset($_GET['category']) ? urldecode($conn->real_escape_string($_GET['category'])) : null;
$difficulty = isset($_GET['difficulty']) ? $conn->real_escape_string($_GET['difficulty']) : null;

$category_name = $category ? $category : "All Subjects";
$difficulty_display = $difficulty ? ucfirst($difficulty) : "Mixed";

// Get question count if provided, otherwise use default from config
$question_count = isset($_GET['question_count']) ? intval($_GET['question_count']) : QUESTIONS_PER_EXAM;
// Validate question count (between 5 and 50)
$question_count = max(5, min(50, $question_count));

// Handle exam submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_exam'])) {
    $student_name = $conn->real_escape_string($_POST['student_name']);
    $answers = $_POST['answers'] ?? [];
    
    $total_questions = count($answers);
    $correct_answers = 0;
    
    // Calculate score
    foreach ($answers as $question_id => $user_answer) {
        $result = $conn->query("SELECT correct_answer FROM questions WHERE id = $question_id");
        if ($result && $row = $result->fetch_assoc()) {
            if ($row['correct_answer'] === $user_answer) {
                $correct_answers++;
            }
        }
    }
    
    $score_percentage = ($total_questions > 0) ? ($correct_answers / $total_questions) * 100 : 0;
    
    // Store results temporarily in session (not in database)
    $_SESSION['exam_review'] = [
        'student_name' => $student_name,
        'total_questions' => $total_questions,
        'correct_answers' => $correct_answers,
        'score_percentage' => $score_percentage,
        'answers' => $answers,
        'category' => $category,
        'difficulty' => $difficulty
    ];
    
    // Redirect to review page
    header("Location: exam_review.php");
    exit();
}

// Fetch random questions (filtered by category and difficulty if specified)
if ($category && $difficulty) {
    $questions = $conn->query("SELECT * FROM questions WHERE category = '$category' AND difficulty = '$difficulty' ORDER BY RAND() LIMIT " . $question_count);
} else if ($category) {
    $questions = $conn->query("SELECT * FROM questions WHERE category = '$category' ORDER BY RAND() LIMIT " . $question_count);
} else {
    $questions = $conn->query("SELECT * FROM questions ORDER BY RAND() LIMIT " . $question_count);
}

if ($questions->num_rows === 0) {
    die("No questions available for this category. Please select another category or add questions from the admin panel.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment - The Thinking Mind</title>
    <meta name="description" content="Assessment session page for The Thinking Mind practice exams.">
    <meta name="robots" content="noindex,follow">
    <link rel="canonical" href="https://thethinkingmind.net/exam.php">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🧠 Knowledge Assessment</h1>
            <p class="subtitle">Category: <?php echo htmlspecialchars($category_name); ?> • Difficulty: <?php echo htmlspecialchars($difficulty_display); ?></p>
            <div class="header-buttons">
                <a href="index.php" class="btn btn-back">Home</a>
            </div>
        </header>

        <main>
            <div class="exam-info">
                <div class="info-item">
                    <strong>Subject:</strong> <?php echo htmlspecialchars($category_name); ?>
                </div>
                <div class="info-item">
                    <strong>Difficulty:</strong> <?php echo htmlspecialchars($difficulty_display); ?>
                </div>
                <div class="info-item">
                    <strong>Total Questions:</strong> <?php echo $questions->num_rows; ?>
                </div>
                <div class="info-item">
                    <strong>Passing Score:</strong> <?php echo PASSING_SCORE; ?>%
                </div>
                <div class="info-item">
                    <strong>Time:</strong> No time limit
                </div>
            </div>

            <form method="POST" id="examForm">
                <div class="form-group student-name">
                    <label for="student_name">Your Name:</label>
                    <input type="text" name="student_name" id="student_name" required placeholder="Enter your name">
                </div>

                <div class="questions-container">
                    <?php 
                    $question_number = 1;
                    while ($q = $questions->fetch_assoc()): 
                    ?>
                        <div class="exam-question">
                            <div class="question-header">
                                <span class="question-number">Question <?php echo $question_number; ?></span>
                                <span class="badge <?php echo strtolower($q['difficulty']); ?>">
                                    <?php echo $q['difficulty']; ?>
                                </span>
                            </div>
                            
                            <div class="question-text">
                                <?php echo htmlspecialchars($q['question_text']); ?>
                            </div>

                            <div class="options">
                                <label class="option-label">
                                    <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="A" required>
                                    <span class="option-content">
                                        <strong>A.</strong> <?php echo htmlspecialchars($q['option_a']); ?>
                                    </span>
                                </label>

                                <label class="option-label">
                                    <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="B" required>
                                    <span class="option-content">
                                        <strong>B.</strong> <?php echo htmlspecialchars($q['option_b']); ?>
                                    </span>
                                </label>

                                <label class="option-label">
                                    <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="C" required>
                                    <span class="option-content">
                                        <strong>C.</strong> <?php echo htmlspecialchars($q['option_c']); ?>
                                    </span>
                                </label>

                                <label class="option-label">
                                    <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="D" required>
                                    <span class="option-content">
                                        <strong>D.</strong> <?php echo htmlspecialchars($q['option_d']); ?>
                                    </span>
                                </label>
                            </div>
                        </div>
                    <?php 
                    $question_number++;
                    endwhile; 
                    ?>
                </div>

                <div class="submit-section">
                    <button type="submit" name="submit_exam" class="btn btn-primary btn-large">
                        Submit Exam
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        document.getElementById('examForm').addEventListener('submit', function(e) {
            const radios = document.querySelectorAll('input[type="radio"]');
            const questions = new Set();
            radios.forEach(radio => {
                const name = radio.getAttribute('name');
                questions.add(name);
            });
            
            let answered = 0;
            questions.forEach(name => {
                const checked = document.querySelector(`input[name="${name}"]:checked`);
                if (checked) answered++;
            });
            
            if (answered < questions.size) {
                if (!confirm(`You have answered ${answered} out of ${questions.size} questions. Submit anyway?`)) {
                    e.preventDefault();
                }
            } else {
                if (!confirm('Are you sure you want to submit your exam?')) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>
