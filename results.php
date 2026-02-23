<?php
require_once 'config.php';

$conn = getDBConnection();

// Fetch all exam results
$results = $conn->query("SELECT * FROM exam_results ORDER BY exam_date DESC");

// Calculate statistics
$stats = $conn->query("
    SELECT 
        COUNT(*) as total_exams,
        AVG(score_percentage) as avg_score,
        MAX(score_percentage) as highest_score,
        MIN(score_percentage) as lowest_score
    FROM exam_results
")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Results - Exam Simulator</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📊 Exam Results</h1>
            <p class="subtitle">View all exam attempts and statistics</p>
            <a href="index.php" class="btn btn-back">← Back to Home</a>
        </header>

        <main>
            <?php if ($stats['total_exams'] > 0): ?>
                <div class="statistics">
                    <h2>Overall Statistics</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">📝</div>
                            <div class="stat-value"><?php echo $stats['total_exams']; ?></div>
                            <div class="stat-label">Total Exams</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">📊</div>
                            <div class="stat-value"><?php echo number_format($stats['avg_score'], 1); ?>%</div>
                            <div class="stat-label">Average Score</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">🏆</div>
                            <div class="stat-value"><?php echo number_format($stats['highest_score'], 1); ?>%</div>
                            <div class="stat-label">Highest Score</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">📉</div>
                            <div class="stat-value"><?php echo number_format($stats['lowest_score'], 1); ?>%</div>
                            <div class="stat-label">Lowest Score</div>
                        </div>
                    </div>
                </div>

                <section class="results-section">
                    <h2>All Exam Attempts</h2>
                    <div class="results-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student Name</th>
                                    <th>Date</th>
                                    <th>Questions</th>
                                    <th>Correct</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($result = $results->fetch_assoc()): 
                                    $passed = $result['score_percentage'] >= PASSING_SCORE;
                                ?>
                                    <tr>
                                        <td><?php echo $result['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($result['student_name']); ?></strong></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($result['exam_date'])); ?></td>
                                        <td><?php echo $result['total_questions']; ?></td>
                                        <td><?php echo $result['correct_answers']; ?></td>
                                        <td>
                                            <span class="score-badge <?php echo $passed ? 'passed' : 'failed'; ?>">
                                                <?php echo number_format($result['score_percentage'], 1); ?>%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $passed ? 'status-passed' : 'status-failed'; ?>">
                                                <?php echo $passed ? '✓ Passed' : '✗ Failed'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="review.php?result_id=<?php echo $result['id']; ?>" class="btn btn-small">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">📝</div>
                    <h2>No Results Yet</h2>
                    <p>No one has taken the exam yet. Be the first!</p>
                    <a href="exam.php" class="btn btn-primary">Take Exam Now</a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>

<?php $conn->close(); ?>
