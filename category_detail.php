<?php
require_once 'config.php';

$conn = getDBConnection();

// Get category from URL parameter
$category = isset($_GET['category']) ? urldecode($conn->real_escape_string($_GET['category'])) : '';

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
    <style>
        .category-detail-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            margin: -20px -20px 40px -20px;
            border-radius: 0 0 10px 10px;
            text-align: center;
        }

        .category-detail-header h1 {
            font-size: 2.5em;
            margin: 20px 0 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .category-detail-header .category-icon {
            font-size: 3em;
        }

        .category-detail-header p {
            font-size: 1.1em;
            opacity: 0.95;
            margin: 10px 0;
        }

        .assessment-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin: 40px 0;
        }

        .assessment-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .assessment-card:hover {
            transform: translateY(-5px);
            border-color: #667eea;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.15);
        }

        .difficulty-emoji {
            font-size: 3em;
            margin-bottom: 15px;
            display: block;
        }

        .assessment-card h3 {
            font-size: 1.5em;
            margin: 15px 0;
            color: #1f2937;
        }

        .assessment-card .description {
            color: #6b7280;
            font-size: 0.95em;
            margin: 15px 0;
            line-height: 1.5;
        }

        .question-stats {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            font-size: 0.95em;
        }

        .stat-label {
            color: #6b7280;
        }

        .stat-value {
            font-weight: bold;
            color: #1f2937;
            font-size: 1.1em;
        }

        .no-questions {
            color: #9ca3af;
            font-style: italic;
            padding: 20px;
        }

        .btn-start-assessment {
            display: inline-block;
            width: 100%;
            margin-top: 20px;
            padding: 12px 20px;
            font-size: 1em;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-start-assessment:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 30px;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #764ba2;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .empty-state h3 {
            font-size: 1.5em;
            color: #6b7280;
            margin: 20px 0;
        }

        .info-box {
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="categories.php" class="back-link">← Back to Categories</a>

        <div class="category-detail-header">
            <h1>
                <span class="category-icon"><?php echo $category_icon; ?></span>
                <span><?php echo htmlspecialchars($category); ?> Assessments</span>
            </h1>
            <p>Total questions available: <strong><?php echo $cat_data['total_count']; ?></strong></p>
        </div>

        <main>
            <div class="info-box">
                <strong>📋 How it works:</strong> Select a difficulty level below to start an assessment. Each level contains different questions tailored to that difficulty.
            </div>

            <?php if ($cat_data['total_count'] > 0): ?>
                <div class="assessment-cards-container">
                    <?php 
                    $difficulties = ['easy', 'medium', 'hard'];
                    foreach ($difficulties as $difficulty):
                        $count_key = $difficulty . '_count';
                        $count = (int)$cat_data[$count_key];
                        $info = $difficulty_info[$difficulty];
                    ?>
                        <div class="assessment-card">
                            <span class="difficulty-emoji"><?php echo $info['emoji']; ?></span>
                            <h3><?php echo $info['label']; ?> Assessment</h3>
                            <p class="description"><?php echo $info['description']; ?></p>
                            
                            <?php if ($count > 0): ?>
                                <div class="question-stats">
                                    <div class="stat-row">
                                        <span class="stat-label">Questions available:</span>
                                        <span class="stat-value"><?php echo $count; ?></span>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-start-assessment" 
                                        onclick="startAssessment('<?php echo urlencode($category); ?>', '<?php echo $difficulty; ?>')">
                                    Start <?php echo $info['label']; ?> Assessment
                                </button>
                            <?php else: ?>
                                <div class="no-questions">
                                    No <?php echo $difficulty; ?> questions available yet
                                </div>
                                <button class="btn-start-assessment" disabled>
                                    Coming Soon
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No assessments available</h3>
                    <p>This category doesn't have any questions yet.</p>
                    <a href="categories.php" class="btn btn-primary" style="margin-top: 20px;">Back to Categories</a>
                </div>
            <?php endif; ?>
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
