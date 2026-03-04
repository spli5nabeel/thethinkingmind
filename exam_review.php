<?php
require_once 'config.php';

// Get exam review from session
if (!isset($_SESSION['exam_review'])) {
    header('Location: index.php');
    exit();
}

$review = $_SESSION['exam_review'];
$student_name = $review['student_name'];
$total_questions = $review['total_questions'];
$correct_answers = $review['correct_answers'];
$score_percentage = $review['score_percentage'];
$answers = $review['answers'];

$passed = $score_percentage >= PASSING_SCORE;

// Get question details for review
$conn = getDBConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Results - The Thinking Mind</title>
    <meta name="description" content="Assessment results and answer review page.">
    <meta name="robots" content="noindex,follow">
    <link rel="canonical" href="https://thethinkingmind.net/exam_review.php">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📊 Your Assessment Results</h1>
            <p class="subtitle">Analyze your performance and identify growth opportunities</p>
            <div class="header-buttons">
                <a href="categories.php" class="btn btn-primary">Take Another Assessment</a>
                <a href="index.php" class="btn btn-back">← Home</a>
            </div>
        </header>

        <main>
            <div class="result-summary <?php echo $passed ? 'passed' : 'failed'; ?>">
                <div class="result-icon">
                    <?php echo $passed ? '🎉' : '📚'; ?>
                </div>
                <h2><?php echo $passed ? 'Outstanding Performance!' : 'Opportunity for Growth'; ?></h2>
                <p class="student-name"><?php echo htmlspecialchars($student_name); ?></p>
                
                <div class="score-display">
                    <div class="score-circle">
                        <span class="score-percentage"><?php echo number_format($score_percentage, 1); ?>%</span>
                    </div>
                </div>

                <div class="result-stats">
                    <div class="stat">
                        <div class="stat-value"><?php echo $correct_answers; ?></div>
                        <div class="stat-label">Correct</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value"><?php echo $total_questions - $correct_answers; ?></div>
                        <div class="stat-label">Incorrect</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value"><?php echo $total_questions; ?></div>
                        <div class="stat-label">Total</div>
                    </div>
                </div>

                <div class="result-message">
                    <?php if ($passed): ?>
                        <p>You've demonstrated strong mastery of this material. Continue building on this foundation! 🎓</p>
                    <?php else: ?>
                        <p>You're making progress. Review the questions you missed and return ready to excel. Target: <?php echo PASSING_SCORE; ?>%</p>
                    <?php endif; ?>
                </div>
            </div>

            <section class="review-section">
                <h2>Detailed Assessment Review</h2>
                <p class="review-intro">Learn from each question — understanding mistakes is the path to mastery:</p>

                <?php 
                $question_number = 1;
                foreach ($answers as $question_id => $user_answer):
                    $q_result = $conn->query("SELECT * FROM questions WHERE id = $question_id");
                    if ($q_result && $question = $q_result->fetch_assoc()):
                        $is_correct = ($question['correct_answer'] === $user_answer);
                ?>
                    <div class="review-question <?php echo $is_correct ? 'correct' : 'incorrect'; ?>">
                        <div class="question-header">
                            <span class="question-number">Question <?php echo $question_number; ?></span>
                            <div class="badges">
                                <span class="badge <?php echo strtolower($question['difficulty']); ?>">
                                    <?php echo $question['difficulty']; ?>
                                </span>
                                <span class="badge category"><?php echo $question['category']; ?></span>
                                <span class="badge <?php echo $is_correct ? 'correct-badge' : 'incorrect-badge'; ?>">
                                    <?php echo $is_correct ? '✓ Correct' : '✗ Incorrect'; ?>
                                </span>
                            </div>
                        </div>

                        <div class="question-text">
                            <?php echo htmlspecialchars($question['question_text']); ?>
                        </div>

                        <div class="review-options">
                            <?php foreach (['A', 'B', 'C', 'D'] as $option): 
                                $option_text = $question['option_' . strtolower($option)];
                                $is_user_answer = ($user_answer === $option);
                                $is_correct_answer = ($question['correct_answer'] === $option);
                                
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
                    endif;
                endforeach; 
                ?>
            </section>

            <div class="action-buttons">
                <a href="pdf_export.php" class="btn btn-primary" title="Download your results as PDF">📥 Download as PDF</a>
            </div>
        </main>
    </div>
</body>
</html>

<?php 
$conn->close(); 
?>
