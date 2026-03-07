<?php
require_once 'config.php';
require_once 'auth.php';

requireAdmin();

$conn = getDBConnection();
$has_users_table = tableExists($conn, 'users');

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? 'all';
$from_date = trim($_GET['from_date'] ?? '');
$to_date = trim($_GET['to_date'] ?? '');

$allowed_status = ['all', 'passed', 'failed'];
if (!in_array($status, $allowed_status, true)) {
    $status = 'all';
}

$where_parts = [];

if ($search !== '') {
    $search_esc = $conn->real_escape_string($search);
    if ($has_users_table) {
        $where_parts[] = "(er.student_name LIKE '%$search_esc%' OR u.full_name LIKE '%$search_esc%' OR u.username LIKE '%$search_esc%' OR u.email LIKE '%$search_esc%')";
    } else {
        $where_parts[] = "er.student_name LIKE '%$search_esc%'";
    }
}

if ($status === 'passed') {
    $where_parts[] = 'er.score_percentage >= ' . PASSING_SCORE;
} elseif ($status === 'failed') {
    $where_parts[] = 'er.score_percentage < ' . PASSING_SCORE;
}

if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from_date)) {
    $from_esc = $conn->real_escape_string($from_date);
    $where_parts[] = "DATE(er.exam_date) >= '$from_esc'";
}

if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_date)) {
    $to_esc = $conn->real_escape_string($to_date);
    $where_parts[] = "DATE(er.exam_date) <= '$to_esc'";
}

$where_sql = '';
if (!empty($where_parts)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_parts);
}

$users_join = $has_users_table ? 'LEFT JOIN users u ON er.user_id = u.id' : '';
$participant_expr = $has_users_table
    ? "CASE WHEN er.user_id IS NULL THEN er.student_name ELSE COALESCE(u.full_name, u.username, er.student_name) END"
    : 'er.student_name';
$username_expr = $has_users_table ? "COALESCE(u.username, '')" : "''";
$email_expr = $has_users_table ? 'u.email' : 'NULL';
$participants_count_expr = $has_users_table
    ? "COUNT(DISTINCT COALESCE(er.user_id, CONCAT('guest:', er.student_name)))"
    : "COUNT(DISTINCT er.student_name)";

$summary_sql = "
    SELECT
        COUNT(*) AS total_attempts,
        $participants_count_expr AS total_participants,
        AVG(er.score_percentage) AS avg_score,
        SUM(CASE WHEN er.score_percentage >= " . PASSING_SCORE . " THEN 1 ELSE 0 END) AS passed_attempts
    FROM exam_results er
    $users_join
    $where_sql
";
$summary = $conn->query($summary_sql)->fetch_assoc();

$participants_sql = "
    SELECT
        er.user_id,
        $participant_expr AS participant_name,
        $username_expr AS username,
        COUNT(*) AS attempts,
        AVG(er.score_percentage) AS avg_score,
        MAX(er.score_percentage) AS best_score,
        MAX(er.exam_date) AS last_exam
    FROM exam_results er
    $users_join
    $where_sql
    GROUP BY er.user_id, participant_name, username
    ORDER BY last_exam DESC
    LIMIT 30
";
$participants = $conn->query($participants_sql);

$attempts_sql = "
    SELECT
        er.id,
        er.user_id,
        er.student_name,
        er.total_questions,
        er.correct_answers,
        er.score_percentage,
        er.exam_date,
        $username_expr AS username,
        $participant_expr AS full_name,
        $email_expr AS email
    FROM exam_results er
    $users_join
    $where_sql
    ORDER BY er.exam_date DESC
    LIMIT 300
";
$attempts = $conn->query($attempts_sql);

$attempt_count = intval($summary['total_attempts'] ?? 0);
$passed_count = intval($summary['passed_attempts'] ?? 0);
$pass_rate = $attempt_count > 0 ? ($passed_count / $attempt_count) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Reports - Admin</title>
    <meta name="description" content="Admin assessment reports showing user attempts and performance trends.">
    <meta name="robots" content="noindex,follow">
    <link rel="canonical" href="https://thethinkingmind.net/admin_assessments.php">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📊 Assessment Reports</h1>
            <p class="subtitle">Track all assessment attempts and performance trends</p>
            <div class="header-buttons">
                <a href="admin.php" class="btn btn-back">← Admin Panel</a>
                <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
                <a href="logout.php" class="btn btn-secondary">🚪 Logout</a>
            </div>
        </header>

        <main>
            <section class="admin-section" style="padding: 24px;">
                <h2>Filters</h2>
                <form method="GET" class="question-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="search">Search User / Name</label>
                            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Name, username, or email">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All</option>
                                <option value="passed" <?php echo $status === 'passed' ? 'selected' : ''; ?>>Passed</option>
                                <option value="failed" <?php echo $status === 'failed' ? 'selected' : ''; ?>>Failed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="from_date">From Date</label>
                            <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>">
                        </div>
                        <div class="form-group">
                            <label for="to_date">To Date</label>
                            <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="admin_assessments.php" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </section>

            <section class="statistics">
                <h2>Overview</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">📝</div>
                        <div class="stat-value"><?php echo $attempt_count; ?></div>
                        <div class="stat-label">Total Attempts</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-value"><?php echo intval($summary['total_participants'] ?? 0); ?></div>
                        <div class="stat-label">Participants</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📈</div>
                        <div class="stat-value"><?php echo $attempt_count > 0 ? number_format(floatval($summary['avg_score']), 1) : '0.0'; ?>%</div>
                        <div class="stat-label">Average Score</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">✅</div>
                        <div class="stat-value"><?php echo number_format($pass_rate, 1); ?>%</div>
                        <div class="stat-label">Pass Rate</div>
                    </div>
                </div>
            </section>

            <section class="dashboard-section">
                <h2>Top Participants</h2>
                <?php if ($participants && $participants->num_rows > 0): ?>
                    <div class="results-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Participant</th>
                                    <th>Attempts</th>
                                    <th>Average</th>
                                    <th>Best Score</th>
                                    <th>Last Attempt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $participants->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($row['participant_name']); ?></strong>
                                            <?php if (!empty($row['username'])): ?>
                                                <div style="font-size: 0.85em; color: #666;">@<?php echo htmlspecialchars($row['username']); ?></div>
                                            <?php else: ?>
                                                <div style="font-size: 0.85em; color: #666;">Guest</div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo intval($row['attempts']); ?></td>
                                        <td><?php echo number_format(floatval($row['avg_score']), 1); ?>%</td>
                                        <td><?php echo number_format(floatval($row['best_score']), 1); ?>%</td>
                                        <td><?php echo date('M d, Y H:i', strtotime($row['last_exam'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">📝</div>
                        <h2>No attempt data</h2>
                        <p>No matching attempts found for the selected filters.</p>
                    </div>
                <?php endif; ?>
            </section>

            <section class="dashboard-section">
                <h2>Recent Attempts</h2>
                <?php if ($attempts && $attempts->num_rows > 0): ?>
                    <div class="results-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Date</th>
                                    <th>Questions</th>
                                    <th>Correct</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($attempt = $attempts->fetch_assoc()):
                                    $passed = floatval($attempt['score_percentage']) >= PASSING_SCORE;
                                    $display_name = $attempt['full_name'] ?: $attempt['student_name'];
                                ?>
                                    <tr>
                                        <td><?php echo intval($attempt['id']); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($display_name); ?></strong>
                                            <?php if (!empty($attempt['username'])): ?>
                                                <div style="font-size: 0.85em; color: #666;">@<?php echo htmlspecialchars($attempt['username']); ?></div>
                                            <?php else: ?>
                                                <div style="font-size: 0.85em; color: #666;">Guest</div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($attempt['exam_date'])); ?></td>
                                        <td><?php echo intval($attempt['total_questions']); ?></td>
                                        <td><?php echo intval($attempt['correct_answers']); ?></td>
                                        <td>
                                            <span class="score-badge <?php echo $passed ? 'passed' : 'failed'; ?>">
                                                <?php echo number_format(floatval($attempt['score_percentage']), 1); ?>%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $passed ? 'status-passed' : 'status-failed'; ?>">
                                                <?php echo $passed ? '✓ Passed' : '✗ Failed'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="review.php?result_id=<?php echo intval($attempt['id']); ?>" class="btn btn-small">View</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">📉</div>
                        <h2>No attempts found</h2>
                        <p>There are no assessment attempts matching the current filter.</p>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>

<?php $conn->close(); ?>
