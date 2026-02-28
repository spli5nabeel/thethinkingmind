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

            <!-- IT ASSESSMENTS SECTION -->
            <section class="assessment-section">
                <div class="section-header">
                    <h2 class="section-title">💻 IT & Technology Assessments</h2>
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
                                <button type="button" class="btn btn-primary" onclick="openQuestionModal('<?php echo urlencode($cat['category']); ?>')">
                                    Start Exam
                                </button>
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
                                <button type="button" class="btn btn-primary" onclick="openQuestionModal('<?php echo urlencode($cat['category']); ?>')">
                                    Start Exam
                                </button>
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

    <!-- Question Count Selection Modal -->
    <div id="questionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeQuestionModal()">&times;</span>
            <h3 style="margin-bottom: 20px;">How many questions do you want?</h3>
            
            <form id="questionForm" method="GET" action="exam.php">
                <input type="hidden" id="categoryInput" name="category">
                
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
                    <button type="button" class="btn btn-secondary" onclick="closeQuestionModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Start Assessment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentCategory = '';

        function openQuestionModal(category) {
            currentCategory = category;
            document.getElementById('categoryInput').value = category;
            document.getElementById('questionModal').style.display = 'block';
            // Set default to 10
            document.getElementById('questionCountSlider').value = 10;
            document.getElementById('countDisplay').textContent = '10';
        }

        function closeQuestionModal() {
            document.getElementById('questionModal').style.display = 'none';
        }

        // Update display when slider changes
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('questionCountSlider');
            if (slider) {
                slider.addEventListener('input', function() {
                    document.getElementById('countDisplay').textContent = this.value;
                });
            }
        });

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('questionModal');
            if (event.target == modal) {
                closeQuestionModal();
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
