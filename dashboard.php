<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();

$conn = getDBConnection();
$user = getCurrentUser();
$user_id = $user['id'];

// Fetch user statistics
$stats = $conn->query("
    SELECT 
        COUNT(*) as total_exams,
        AVG(score_percentage) as avg_score,
        MAX(score_percentage) as best_score,
        SUM(correct_answers) as total_correct,
        SUM(total_questions) as total_answered
    FROM exam_results 
    WHERE user_id = $user_id
")->fetch_assoc();

// Fetch recent exams
$recent_exams = $conn->query("
    SELECT * FROM exam_results 
    WHERE user_id = $user_id 
    ORDER BY exam_date DESC 
    LIMIT 5
");

// Fetch category performance
$category_stats = $conn->query("
    SELECT 
        q.category,
        COUNT(*) as attempts,
        AVG(CASE WHEN ea.is_correct = 1 THEN 100 ELSE 0 END) as accuracy
    FROM exam_answers ea
    JOIN questions q ON ea.question_id = q.id
    JOIN exam_results er ON ea.result_id = er.id
    WHERE er.user_id = $user_id
    GROUP BY q.category
    ORDER BY attempts DESC
    LIMIT 5
");

// Fetch recent activity
$activities = $conn->query("
    SELECT * FROM user_activity 
    WHERE user_id = $user_id 
    ORDER BY created_at DESC 
    LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Exam Simulator</title>
    <meta name="description" content="User dashboard with exam progress, performance, and activity insights.">
    <meta name="robots" content="noindex,follow">
    <link rel="canonical" href="https://thethinkingmind.net/dashboard.php">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📊 Dashboard</h1>
            <p class="subtitle">Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
            <div class="header-buttons">
                <a href="categories.php" class="btn btn-primary">Take Exam</a>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="admin.php" class="btn btn-admin">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-back">Logout</a>
            </div>
        </header>

        <main>
            <!-- User Info -->
            <div class="user-info-card">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                </div>
                <div class="user-details">
                    <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <p>@<?php echo htmlspecialchars($user['username']); ?></p>
                    <span class="badge <?php echo $user['role'] === 'admin' ? 'badge-admin' : 'badge-student'; ?>">
                        <?php echo ucfirst($user['role']); ?>
                    </span>
                </div>
            </div>

            <!-- Statistics -->
            <?php if ($stats['total_exams'] > 0): ?>
                <div class="statistics">
                    <h2>Your Performance</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">📝</div>
                            <div class="stat-value"><?php echo $stats['total_exams']; ?></div>
                            <div class="stat-label">Exams Taken</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">📈</div>
                            <div class="stat-value"><?php echo number_format($stats['avg_score'], 1); ?>%</div>
                            <div class="stat-label">Average Score</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">🏆</div>
                            <div class="stat-value"><?php echo number_format($stats['best_score'], 1); ?>%</div>
                            <div class="stat-label">Best Score</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">✓</div>
                            <div class="stat-value"><?php echo $stats['total_correct']; ?> / <?php echo $stats['total_answered']; ?></div>
                            <div class="stat-label">Correct Answers</div>
                        </div>
                    </div>
                </div>

                <!-- Category Performance -->
                <?php if ($category_stats->num_rows > 0): ?>
                    <section class="dashboard-section">
                        <h2>Category Performance</h2>
                        <div class="category-performance">
                            <?php while ($cat = $category_stats->fetch_assoc()): ?>
                                <div class="performance-item">
                                    <div class="performance-header">
                                        <strong><?php echo htmlspecialchars($cat['category']); ?></strong>
                                        <span><?php echo $cat['attempts']; ?> attempts</span>
                                    </div>
                                    <div class="performance-bar">
                                        <div class="performance-fill" style="width: <?php echo $cat['accuracy']; ?>%"></div>
                                    </div>
                                    <div class="performance-accuracy"><?php echo number_format($cat['accuracy'], 1); ?>% accuracy</div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Recent Exams -->
                <section class="dashboard-section">
                    <h2>Recent Exams</h2>
                    <div class="results-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Questions</th>
                                    <th>Correct</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($exam = $recent_exams->fetch_assoc()): 
                                    $passed = $exam['score_percentage'] >= PASSING_SCORE;
                                ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($exam['exam_date'])); ?></td>
                                        <td><?php echo $exam['total_questions']; ?></td>
                                        <td><?php echo $exam['correct_answers']; ?></td>
                                        <td>
                                            <span class="score-badge <?php echo $passed ? 'passed' : 'failed'; ?>">
                                                <?php echo number_format($exam['score_percentage'], 1); ?>%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $passed ? 'status-passed' : 'status-failed'; ?>">
                                                <?php echo $passed ? '✓ Passed' : '✗ Failed'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="review.php?result_id=<?php echo $exam['id']; ?>" class="btn btn-small">Review</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="my_results.php" class="btn btn-secondary">View All Results</a>
                    </div>
                </section>
            <?php else: ?>
                <!-- No exams yet -->
                <div class="empty-state">
                    <div class="empty-icon">📝</div>
                    <h2>No Exams Yet</h2>
                    <p>You haven't taken any exams yet. Start practicing now!</p>
                    <a href="categories.php" class="btn btn-primary">Take Your First Exam</a>
                </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3>Quick Actions</h3>
                <div class="action-buttons-group">
                    <a href="categories.php" class="btn btn-primary">📝 Take New Exam</a>
                    <a href="my_results.php" class="btn btn-secondary">📊 View All Results</a>
                    <?php if ($user['role'] === 'admin'): ?>
                        <a href="admin.php" class="btn btn-admin">⚙️ Manage Questions</a>
                        <a href="manage_categories.php" class="btn btn-admin">📁 Manage Categories</a>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

<?php $conn->close(); ?>
