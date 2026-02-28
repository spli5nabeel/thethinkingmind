<?php
require_once 'config.php';

$conn = getDBConnection();

// Get all unique categories with question counts
$categories = $conn->query("
    SELECT category, COUNT(*) as question_count, 
           MIN(difficulty) as min_difficulty,
           MAX(difficulty) as max_difficulty
    FROM questions 
    GROUP BY category 
    ORDER BY category
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knowledge Assessment - The Thinking Mind</title>
    <meta name="description" content="Browse exam categories and start a focused knowledge assessment on The Thinking Mind.">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="https://thethinkingmind.net/categories.php">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Assessment Categories - The Thinking Mind">
    <meta property="og:description" content="Choose a subject category and start your practice exam.">
    <meta property="og:url" content="https://thethinkingmind.net/categories.php">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🧠 Choose Your Assessment Path</h1>
            <p class="subtitle">Select a subject to challenge your knowledge</p>
            <a href="index.php" class="btn btn-back">← Back to Home</a>
        </header>

        <main>
            <div class="categories-intro">
                <h2>Assessment Categories</h2>
                <p>Select a topic to evaluate your mastery. Each assessment features carefully curated questions to challenge your understanding.</p>
            </div>

            <div class="categories-grid">
                <?php 
                $icons = [
                    'PHP Basics' => '🔵',
                    'Database' => '🗄️',
                    'Functions' => '⚙️',
                    'Forms' => '📝',
                    'Operators' => '➗',
                    'String Functions' => '📄',
                    'Arrays' => '📊',
                    'OOP' => '🎯',
                    'Security' => '🔒',
                    'General' => '📖'
                ];
                
                while ($cat = $categories->fetch_assoc()): 
                    $icon = isset($icons[$cat['category']]) ? $icons[$cat['category']] : '📚';
                ?>
                    <div class="category-card">
                        <div class="category-icon"><?php echo $icon; ?></div>
                        <h3><?php echo htmlspecialchars($cat['category']); ?></h3>
                        <div class="category-stats">
                            <span class="stat-item">
                                <strong><?php echo $cat['question_count']; ?></strong> questions
                            </span>
                        </div>
                        <div class="category-actions">
                            <a href="exam.php?category=<?php echo urlencode($cat['category']); ?>" 
                               class="btn btn-primary">
                                Start Exam
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>

                <!-- All Subjects Option -->
                <div class="category-card featured">
                    <div class="category-icon">🌟</div>
                    <h3>All Subjects</h3>
                    <div class="category-stats">
                        <span class="stat-item">
                            <strong>Mixed</strong> questions
                        </span>
                    </div>
                    <div class="category-actions">
                        <a href="exam.php" class="btn btn-primary">
                            Start Mixed Exam
                        </a>
                    </div>
                </div>
            </div>

            <div class="exam-options">
                <h3>Exam Settings</h3>
                <div class="settings-info">
                    <div class="setting-item">
                        <strong>Passing score:</strong> <?php echo PASSING_SCORE; ?>%
                    </div>
                    <div class="setting-item">
                        <strong>Time limit:</strong> No limit
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

<?php $conn->close(); ?>
