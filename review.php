<?php
require_once 'config.php';

$conn = getDBConnection();

if (!isset($_GET['result_id'])) {
    header("Location: index.php");
    exit();
}

$result_id = intval($_GET['result_id']);

// Fetch exam result
$result = $conn->query("SELECT * FROM exam_results WHERE id = $result_id")->fetch_assoc();

if (!$result) {
    die("Result not found.");
}

// Fetch detailed answers
$answers = $conn->query("
    SELECT ea.*, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, 
           q.correct_answer, q.category, q.difficulty
    FROM exam_answers ea
    JOIN questions q ON ea.question_id = q.id
    WHERE ea.result_id = $result_id
    ORDER BY ea.id
");

$passed = $result['score_percentage'] >= PASSING_SCORE;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Review - Exam Simulator</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📊 Exam Results</h1>
            <p class="subtitle">Review your performance</p>
            <div class="header-buttons">
                <a href="exam.php" class="btn btn-primary">Take Another Exam</a>
                <a href="index.php" class="btn btn-back">← Back to Home</a>
            </div>
        </header>

        <main>
            <div class="result-summary <?php echo $passed ? 'passed' : 'failed'; ?>">
                <div class="result-icon">
                    <?php echo $passed ? '🎉' : '📚'; ?>
                </div>
                <h2><?php echo $passed ? 'Congratulations!' : 'Keep Learning!'; ?></h2>
                <p class="student-name"><?php echo htmlspecialchars($result['student_name']); ?></p>
                
                <div class="score-display">
                    <div class="score-circle">
                        <span class="score-percentage"><?php echo number_format($result['score_percentage'], 1); ?>%</span>
                    </div>
                </div>

                <div class="result-stats">
                    <div class="stat">
                        <div class="stat-value"><?php echo $result['correct_answers']; ?></div>
                        <div class="stat-label">Correct</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value"><?php echo $result['total_questions'] - $result['correct_answers']; ?></div>
                        <div class="stat-label">Incorrect</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value"><?php echo $result['total_questions']; ?></div>
                        <div class="stat-label">Total</div>
                    </div>
                </div>

                <div class="result-message">
                    <?php if ($passed): ?>
                        <p>You passed the exam! Great job! 🎓</p>
                    <?php else: ?>
                        <p>You need <?php echo PASSING_SCORE; ?>% to pass. Keep practicing! 💪</p>
                    <?php endif; ?>
                </div>
            </div>

            <section class="review-section">
                <h2>Detailed Review</h2>
                <p class="review-intro">Review each question and learn from your answers:</p>

                <?php 
                $question_number = 1;
                while ($answer = $answers->fetch_assoc()): 
                    $is_correct = $answer['is_correct'];
                    $user_answer = $answer['user_answer'];
                    $correct_answer = $answer['correct_answer'];
                ?>
                    <div class="review-question <?php echo $is_correct ? 'correct' : 'incorrect'; ?>">
                        <div class="question-header">
                            <span class="question-number">Question <?php echo $question_number; ?></span>
                            <div class="badges">
                                <span class="badge <?php echo strtolower($answer['difficulty']); ?>">
                                    <?php echo $answer['difficulty']; ?>
                                </span>
                                <span class="badge category"><?php echo $answer['category']; ?></span>
                                <span class="badge <?php echo $is_correct ? 'correct-badge' : 'incorrect-badge'; ?>">
                                    <?php echo $is_correct ? '✓ Correct' : '✗ Incorrect'; ?>
                                </span>
                            </div>
                        </div>

                        <div class="question-text">
                            <?php echo htmlspecialchars($answer['question_text']); ?>
                        </div>

                        <div class="review-options">
                            <?php foreach (['A', 'B', 'C', 'D'] as $option): 
                                $option_text = $answer['option_' . strtolower($option)];
                                $is_user_answer = ($user_answer === $option);
                                $is_correct_answer = ($correct_answer === $option);
                                
                                $class = '';
                                if ($is_correct_answer) {
                                    $class = 'correct-answer';
                                } elseif ($is_user_answer && !$is_correct) {
                                    $class = 'wrong-answer';
                                }
                            ?>
                                <div class="review-option <?php echo $class; ?>">
                                    <strong><?php echo $option; ?>.</strong>
                                    <?php echo htmlspecialchars($option_text); ?>
                                    <?php if ($is_correct_answer): ?>
                                        <span class="label-correct">✓ Correct Answer</span>
                                    <?php endif; ?>
                                    <?php if ($is_user_answer && !$is_correct): ?>
                                        <span class="label-your-answer">Your Answer</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php 
                $question_number++;
                endwhile; 
                ?>
            </section>

            <div class="action-buttons">
                <a href="exam.php" class="btn btn-primary">Take Another Exam</a>
                <a href="results.php" class="btn btn-secondary">View All Results</a>
            </div>
        </main>
    </div>
</body>
</html>

<?php $conn->close(); ?>
