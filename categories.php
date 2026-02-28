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

            <?php 
            // Fetch category types from database
            $metadata_result = $conn->query("SELECT category_name, category_type FROM category_metadata");
            $category_types = [];
            while ($row = $metadata_result->fetch_assoc()) {
                $category_types[$row['category_name']] = $row['category_type'];
            }
            
            // Default category mapping for existing categories without metadata
            $default_types = [
                'KCSA' => 'IT',
                'Maths' => 'Academic',
                'Python' => 'Academic',
                'PHP Basics' => 'IT',
                'Database' => 'IT',
                'General' => 'Academic'
            ];
            
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
                'General' => '📖',
                'Mathematics' => '🔢',
                'Science' => '🔬',
                'History' => '📚',
                'Literature' => '📖',
                'Biology' => '🧬',
                'Chemistry' => '⚗️',
                'Physics' => '⚛️',
                'English' => '✍️',
                'Economics' => '💰',
                'KCSA' => '🎓',
                'Maths' => '🔢',
                'Python' => '🐍'
            ];
            
            // Reorganize categories by type from database
            $all_cats = [];
            $categories_by_type = ['IT' => [], 'Academic' => []];
            
            while ($cat = $categories->fetch_assoc()) {
                $all_cats[$cat['category']] = $cat;
                
                // Determine category type
                $type = $category_types[$cat['category']] ?? $default_types[$cat['category']] ?? 'Academic';
                
                if (!isset($categories_by_type[$type])) {
                    $categories_by_type[$type] = [];
                }
                $categories_by_type[$type][] = $cat['category'];
            }
            ?>

            <!-- TECH ASSESSMENTS SECTION -->
            <section class="assessment-section">
                <div class="section-header">
                    <h2 class="section-title">💻 Tech Assessments</h2>
                    <p class="section-subtitle">Test your knowledge in programming, databases, and technology fundamentals</p>
                </div>
                <div class="categories-grid">
                    <?php 
                    foreach ($categories_by_type['IT'] as $cat_name):
                        if (isset($all_cats[$cat_name])): 
                            $cat = $all_cats[$cat_name];
                            $icon = isset($icons[$cat_name]) ? $icons[$cat_name] : '📚';
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
                                <a href="category_detail.php?category=<?php echo urlencode($cat['category']); ?>" class="btn btn-primary">
                                    View Assessments
                                </a>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </section>

            <!-- ACADEMIC ASSESSMENTS SECTION -->
            <section class="assessment-section">
                <div class="section-header">
                    <h2 class="section-title">📚 Academic Assessments</h2>
                    <p class="section-subtitle">Explore subjects across science, humanities, and general knowledge</p>
                </div>
                <div class="categories-grid">
                    <?php 
                    foreach ($categories_by_type['Academic'] as $cat_name):
                        if (isset($all_cats[$cat_name])): 
                            $cat = $all_cats[$cat_name];
                            $icon = isset($icons[$cat_name]) ? $icons[$cat_name] : '📚';
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
                                <a href="category_detail.php?category=<?php echo urlencode($cat['category']); ?>" class="btn btn-primary">
                                    View Assessments
                                </a>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </section>

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
