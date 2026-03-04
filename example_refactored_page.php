<?php
/**
 * Example page demonstrating refactored structure usage
 * This shows how new pages should be built using the organized codebase
 */

// Single include loads everything: config, functions, database helper, templates
require_once 'includes/bootstrap.php';

// Page configuration
$page_title = "Example Page - The Thinking Mind";
$page_description = "Example demonstrating refactored code structure";
$active_nav = 'home';

// Example 1: Using Database helper instead of raw SQL
$categories = $db->getCategories();
$total_questions = $db->countQuestions();
$stats = $db->getStatistics();

// Example 2: Using utility functions
$sample_score = calculateScore(85, 100);
$grade = getGrade($sample_score);
$passing = isPassing($sample_score);

// Example 3: Get specific data
$featured_question = $db->getExamQuestions('KCSA', null, 1);

// Render page using templates
renderHeader($page_title, $page_description);
?>

<div class="container">
    <?php renderNavigation($active_nav); ?>
    
    <header>
        <h1>🎯 Refactored Code Example</h1>
        <p class="subtitle">This page demonstrates the new organized structure</p>
    </header>

    <main>
        <!-- Flash Messages (if any) -->
        <?php echo displayFlashMessage(); ?>
        
        <section class="example-section">
            <h2>Database Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $stats['total_questions']; ?></h3>
                    <p>Total Questions</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['total_categories']; ?></h3>
                    <p>Categories</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['total_users']; ?></h3>
                    <p>Users</p>
                </div>
            </div>
        </section>

        <section class="example-section">
            <h2>Available Categories</h2>
            <ul class="category-list">
                <?php foreach ($categories as $category): ?>
                <li>
                    <strong><?php echo formatCategoryName($category['category']); ?></strong>
                    - <?php echo $category['question_count']; ?> questions
                </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="example-section">
            <h2>Utility Functions Demo</h2>
            <p>Score: <strong><?php echo number_format($sample_score, 1); ?>%</strong></p>
            <p>Grade: <strong><?php echo $grade; ?></strong></p>
            <p>Status: <strong><?php echo $passing ? '✓ Passed' : '✗ Failed'; ?></strong></p>
        </section>

        <?php if (!empty($featured_question)): ?>
        <section class="example-section">
            <h2>Featured Question</h2>
            <div class="question-preview">
                <p><strong>Category:</strong> <?php echo formatCategoryName($featured_question[0]['category']); ?></p>
                <p><strong>Difficulty:</strong> <?php echo formatDifficultyLevel($featured_question[0]['difficulty']); ?></p>
                <p><?php echo formatQuestion($featured_question[0]['question_text']); ?></p>
            </div>
        </section>
        <?php endif; ?>

        <section class="example-section">
            <h2>Benefits of Refactored Structure</h2>
            <ul>
                <li>✓ Single <code>require_once 'includes/bootstrap.php'</code> loads everything</li>
                <li>✓ Clean Database helper methods instead of raw SQL</li>
                <li>✓ Reusable utility functions for common operations</li>
                <li>✓ Template functions for consistent HTML structure</li>
                <li>✓ Better organization and maintainability</li>
                <li>✓ Backward compatible - old code still works</li>
            </ul>
        </section>

        <div class="action-buttons">
            <a href="index.php" class="btn btn-primary">Back to Home</a>
            <a href="includes/README.md" class="btn btn-secondary">View Documentation</a>
        </div>
    </main>
</div>

<?php renderFooter(); ?>

<style>
.example-section {
    margin: 2rem 0;
    padding: 1.5rem;
    background: #f5f5f5;
    border-radius: 8px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-card h3 {
    font-size: 2rem;
    color: #2c3e50;
    margin: 0;
}

.stat-card p {
    margin: 0.5rem 0 0 0;
    color: #666;
}

.category-list {
    list-style: none;
    padding: 0;
}

.category-list li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #ddd;
}

.question-preview {
    background: white;
    padding: 1rem;
    border-radius: 4px;
    margin-top: 1rem;
}

.action-buttons {
    margin-top: 2rem;
    display: flex;
    gap: 1rem;
}
</style>
