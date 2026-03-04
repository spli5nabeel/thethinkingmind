<?php
require_once 'config.php';

$conn = getDBConnection();

// Get category from URL parameter
$category = isset($_GET['category']) ? urldecode($conn->real_escape_string($_GET['category'])) : '';

// Get type parameter and validate it
$type = isset($_GET['type']) ? $conn->real_escape_string($_GET['type']) : '';
if (!in_array($type, ['IT', 'Academic'], true)) {
    $type = ''; // Reset to empty if invalid
}

if (empty($category)) {
    header("Location: categories.php");
    exit;
}

// Get category details
$cat_query = $conn->query("
    SELECT category, COUNT(*) as total_count,
           SUM(CASE WHEN difficulty = 'easy' THEN 1 ELSE 0 END) as easy_count,
           SUM(CASE WHEN difficulty = 'medium' THEN 1 ELSE 0 END) as medium_count,
           SUM(CASE WHEN difficulty = 'hard' THEN 1 ELSE 0 END) as hard_count
    FROM questions 
    WHERE category = '$category'
    GROUP BY category
");

if ($cat_query->num_rows === 0) {
    header("Location: categories.php");
    exit;
}

$cat_data = $cat_query->fetch_assoc();

// Icon mapping
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

$category_icon = isset($icons[$category]) ? $icons[$category] : '📚';

// Difficulty colors and emojis
$difficulty_info = [
    'easy' => [
        'emoji' => '⭐',
        'label' => 'Easy',
        'description' => 'Perfect for beginners and warming up',
        'color' => '#10b981',
        'data_value' => 'easy'
    ],
    'medium' => [
        'emoji' => '⭐⭐',
        'label' => 'Medium',
        'description' => 'For intermediate learners to test their skills',
        'color' => '#f59e0b',
        'data_value' => 'medium'
    ],
    'hard' => [
        'emoji' => '⭐⭐⭐',
        'label' => 'Hard',
        'description' => 'Challenge yourself with advanced questions',
        'color' => '#ef4444',
        'data_value' => 'hard'
    ]
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category); ?> Assessments - The Thinking Mind</title>
    <meta name="description" content="Take <?php echo htmlspecialchars($category); ?> assessments with different difficulty levels.">
    <link rel="canonical" href="https://thethinkingmind.net/category_detail.php?category=<?php echo urlencode($category); ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($category); ?> Assessments">
    <meta property="og:description" content="Choose your difficulty level and start a focused knowledge assessment.">
    <meta property="og:url" content="https://thethinkingmind.net/category_detail.php?category=<?php echo urlencode($category); ?>">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo $category_icon; ?> <?php echo htmlspecialchars($category); ?> Assessments</h1>
            <p class="subtitle">Choose a difficulty level to start your assessment</p>
            <a href="categories.php<?php echo $type ? '?type=' . urlencode($type) : ''; ?>" class="btn btn-back">← Back to Categories</a>
        </header>

        <main>
            <section class="assessment-section">
                <div class="section-header">
                    <h2 class="section-title">Difficulty Levels</h2>
                    <p class="section-subtitle">Total questions available: <strong><?php echo $cat_data['total_count']; ?></strong></p>
                </div>

                <?php if ($cat_data['total_count'] > 0): ?>
                    <div class="categories-grid">
                        <?php 
                        $difficulties = ['easy', 'medium', 'hard'];
                        foreach ($difficulties as $difficulty):
                            $count_key = $difficulty . '_count';
                            $count = (int)$cat_data[$count_key];
                            $info = $difficulty_info[$difficulty];
                        ?>
                            <div class="category-card">
                                <div class="category-icon"><?php echo $info['emoji']; ?></div>
                                <h3><?php echo $info['label']; ?> Assessment</h3>
                                <p><?php echo $info['description']; ?></p>

                                <div class="category-stats">
                                    <span class="stat-item">
                                        <strong><?php echo $count; ?></strong> questions available
                                    </span>
                                </div>

                                <div class="category-actions">
                                    <?php if ($count > 0): ?>
                                        <button class="btn btn-primary" 
                                                onclick="startAssessment('<?php echo urlencode($category); ?>', '<?php echo $difficulty; ?>')">
                                            Start <?php echo $info['label']; ?> Assessment
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled>
                                            Coming Soon
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="exam-options">
                        <h3>No assessments available</h3>
                        <div class="settings-info">
                            <div class="setting-item">
                                <strong>No questions found</strong>
                                <span>Please select another category</span>
                            </div>
                        </div>
                        <div style="text-align:center; margin-top:20px;">
                            <a href="categories.php" class="btn btn-primary">Back to Categories</a>
                        </div>
                    </div>
                <?php endif; ?>
            </section>

            <div class="exam-options">
                <h3>How It Works</h3>
                <div class="settings-info">
                    <div class="setting-item">
                        <strong>Step 1</strong>
                        <span>Select difficulty level</span>
                    </div>
                    <div class="setting-item">
                        <strong>Step 2</strong>
                        <span>Choose question count</span>
                    </div>
                    <div class="setting-item">
                        <strong>Step 3</strong>
                        <span>Start your assessment</span>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Question Count Modal for Selected Difficulty -->
    <div id="assessmentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAssessmentModal()">&times;</span>
            <h3 id="modalTitle" style="margin-bottom: 20px;">Select Number of Questions</h3>
            
            <form id="assessmentForm" method="GET" action="exam.php">
                <input type="hidden" id="categoryInput" name="category">
                <input type="hidden" id="difficultyInput" name="difficulty">
                
                <div class="form-group" style="margin-bottom: 25px;">
                    <label for="questionCount">Number of Questions:</label>
                    <div style="display: flex; align-items: center; gap: 15px; margin-top: 10px;">
                        <input type="range" id="questionCountSlider" name="question_count" 
                               min="5" max="50" value="10" step="5"
                               style="flex: 1; cursor: pointer;">
                        <span id="countDisplay" style="font-size: 1.2em; font-weight: bold; min-width: 50px;">10</span>
                    </div>
                    <p style="margin-top: 10px; color: #999; font-size: 0.9em;">
                        Adjust the slider to select between 5 and 50 questions
                    </p>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 25px;">
                    <button type="button" class="btn btn-secondary" onclick="closeAssessmentModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Start Assessment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function startAssessment(category, difficulty) {
            document.getElementById('categoryInput').value = category;
            document.getElementById('difficultyInput').value = difficulty;
            document.getElementById('modalTitle').textContent = 'How many ' + difficulty + ' questions do you want?';
            document.getElementById('assessmentModal').style.display = 'block';
            document.getElementById('questionCountSlider').value = 10;
            document.getElementById('countDisplay').textContent = '10';
        }

        function closeAssessmentModal() {
            document.getElementById('assessmentModal').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('questionCountSlider');
            if (slider) {
                slider.addEventListener('input', function() {
                    document.getElementById('countDisplay').textContent = this.value;
                });
            }
        });

        window.onclick = function(event) {
            const modal = document.getElementById('assessmentModal');
            if (event.target == modal) {
                closeAssessmentModal();
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
