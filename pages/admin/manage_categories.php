<?php
require_once 'config.php';
require_once 'auth.php';

requireAdmin();

$conn = getDBConnection();
$message = '';
$messageType = '';

// Ensure category_metadata table exists
$conn->query("CREATE TABLE IF NOT EXISTS category_metadata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) UNIQUE NOT NULL,
    category_type ENUM('IT', 'Academic') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $new_category = $conn->real_escape_string(trim($_POST['new_category_name']));
                $category_type = $conn->real_escape_string($_POST['category_type']);
                
                if (empty($new_category)) {
                    $message = "Category name cannot be empty!";
                    $messageType = "error";
                } elseif (!in_array($category_type, ['IT', 'Academic'])) {
                    $message = "Invalid category type!";
                    $messageType = "error";
                } else {
                    // Check if category already exists
                    $check = $conn->query("SELECT COUNT(*) as count FROM questions WHERE category = '$new_category'");
                    $exists = $check->fetch_assoc()['count'] > 0;
                    
                    if ($exists) {
                        $message = "Category '$new_category' already exists!";
                        $messageType = "error";
                    } else {
                        // Create a placeholder question to establish the category
                        $sql = "INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_answer, category, difficulty) 
                                VALUES ('[Placeholder] Add your first question for this category', 'Option A', 'Option B', 'Option C', 'Option D', 'A', '$new_category', 'Easy')";
                        
                        if ($conn->query($sql)) {
                            // Store category type metadata
                            $meta_sql = "INSERT INTO category_metadata (category_name, category_type) VALUES ('$new_category', '$category_type')";
                            $conn->query($meta_sql);
                            
                            $message = "Category '$new_category' created successfully as " . strtoupper($category_type) . "! Add more questions from the Admin Panel.";
                            $messageType = "success";
                        } else {
                            $message = "Error creating category: " . $conn->error;
                            $messageType = "error";
                        }
                    }
                }
                break;
                
            case 'rename':
                $old_category = $conn->real_escape_string($_POST['old_category']);
                $new_category = $conn->real_escape_string($_POST['new_category']);
                
                if (empty($new_category)) {
                    $message = "Category name cannot be empty!";
                    $messageType = "error";
                } else {
                    $sql = "UPDATE questions SET category = '$new_category' WHERE category = '$old_category'";
                    
                    if ($conn->query($sql)) {
                        $affected = $conn->affected_rows;
                        $message = "Category renamed successfully! Updated $affected question(s).";
                        $messageType = "success";
                    } else {
                        $message = "Error renaming category: " . $conn->error;
                        $messageType = "error";
                    }
                }
                break;
                
            case 'delete':
                $category = $conn->real_escape_string($_POST['category']);
                $delete_action = $_POST['delete_action'];
                
                if ($delete_action === 'delete_questions') {
                    // Delete all questions in this category
                    $sql = "DELETE FROM questions WHERE category = '$category'";
                } else {
                    // Move questions to "General" category
                    $sql = "UPDATE questions SET category = 'General' WHERE category = '$category'";
                }
                
                if ($conn->query($sql)) {
                    $affected = $conn->affected_rows;
                    if ($delete_action === 'delete_questions') {
                        $message = "Category deleted along with $affected question(s).";
                    } else {
                        $message = "Category deleted. $affected question(s) moved to 'General'.";
                    }
                    $messageType = "success";
                } else {
                    $message = "Error deleting category: " . $conn->error;
                    $messageType = "error";
                }
                break;
                
            case 'merge':
                $from_category = $conn->real_escape_string($_POST['from_category']);
                $to_category = $conn->real_escape_string($_POST['to_category']);
                
                if ($from_category === $to_category) {
                    $message = "Cannot merge a category with itself!";
                    $messageType = "error";
                } else {
                    $sql = "UPDATE questions SET category = '$to_category' WHERE category = '$from_category'";
                    
                    if ($conn->query($sql)) {
                        $affected = $conn->affected_rows;
                        $message = "Merged successfully! Moved $affected question(s) from '$from_category' to '$to_category'.";
                        $messageType = "success";
                    } else {
                        $message = "Error merging categories: " . $conn->error;
                        $messageType = "error";
                    }
                }
                break;
        }
    }
}

// Fetch all categories with counts
$categories = $conn->query("
    SELECT category, 
           COUNT(*) as question_count,
           MIN(difficulty) as min_difficulty,
           MAX(difficulty) as max_difficulty,
           MIN(created_at) as first_added,
           MAX(created_at) as last_added
    FROM questions 
    GROUP BY category 
    ORDER BY category
");

$total_questions = $conn->query("SELECT COUNT(*) as total FROM questions")->fetch_assoc()['total'];
$total_categories = $categories->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Exam Simulator</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .action-buttons-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .modal-header {
            margin-bottom: 20px;
        }
        .modal-header h3 {
            color: var(--dark-color);
            margin-bottom: 10px;
        }
        .modal-footer {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #000;
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📁 Manage Categories</h1>
            <p class="subtitle">Organize your exam questions by subject</p>
            <div class="header-buttons">
                <a href="admin.php" class="btn btn-back">← Back to Admin</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </header>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <main>
            <!-- Create New Category -->
            <section class="admin-section">
                <h2>➕ Create New Category</h2>
                <form method="POST" class="question-form">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="form-row">
                        <div class="form-group" style="flex: 2;">
                            <label for="new_category_name">Category Name:</label>
                            <input type="text" 
                                   name="new_category_name" 
                                   id="new_category_name" 
                                   placeholder="e.g., JavaScript, Python, Data Structures..." 
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="category_type">Category Type:</label>
                            <select name="category_type" id="category_type" required>
                                <option value="">Select Type...</option>
                                <option value="IT">💻 IT & Technology</option>
                                <option value="Academic">📚 Academic</option>
                            </select>
                        </div>
                        <div class="form-group" style="display: flex; align-items: flex-end;">
                            <button type="submit" class="btn btn-primary">Create Category</button>
                        </div>
                    </div>
                    
                    <p style="margin-top: 10px; color: #7f8c8d; font-size: 0.9em;">
                        💡 A placeholder question will be created. You can edit or delete it from the Admin Panel and add your own questions.
                    </p>
                </form>
            </section>

            <!-- Statistics -->
            <div class="statistics">
                <h2>Category Overview</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">📚</div>
                        <div class="stat-value"><?php echo $total_categories; ?></div>
                        <div class="stat-label">Total Categories</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📝</div>
                        <div class="stat-value"><?php echo $total_questions; ?></div>
                        <div class="stat-label">Total Questions</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📊</div>
                        <div class="stat-value"><?php echo $total_questions > 0 ? round($total_questions / $total_categories, 1) : 0; ?></div>
                        <div class="stat-label">Avg per Category</div>
                    </div>
                </div>
            </div>

            <!-- Categories List -->
            <section class="admin-section">
                <h2>All Categories (<?php echo $total_categories; ?>)</h2>
                <div class="questions-list">
                    <?php 
                    $categories->data_seek(0); // Reset pointer
                    while ($cat = $categories->fetch_assoc()): 
                    ?>
                        <div class="question-item">
                            <div class="question-header">
                                <span class="question-id">📁 <?php echo htmlspecialchars($cat['category']); ?></span>
                                <span class="badge category"><?php echo $cat['question_count']; ?> questions</span>
                            </div>
                            
                            <div class="category-details">
                                <p><strong>Difficulty Range:</strong> <?php echo $cat['min_difficulty']; ?> to <?php echo $cat['max_difficulty']; ?></p>
                                <p><strong>First Added:</strong> <?php echo date('M d, Y', strtotime($cat['first_added'])); ?></p>
                                <p><strong>Last Updated:</strong> <?php echo date('M d, Y', strtotime($cat['last_added'])); ?></p>
                            </div>

                            <div class="action-buttons-group">
                                <button onclick="openRenameModal('<?php echo htmlspecialchars($cat['category'], ENT_QUOTES); ?>')" 
                                        class="btn btn-secondary btn-small">
                                    ✏️ Rename
                                </button>
                                
                                <button onclick="openMergeModal('<?php echo htmlspecialchars($cat['category'], ENT_QUOTES); ?>')" 
                                        class="btn btn-admin btn-small">
                                    🔀 Merge
                                </button>
                                
                                <button onclick="openDeleteModal('<?php echo htmlspecialchars($cat['category'], ENT_QUOTES); ?>', <?php echo $cat['question_count']; ?>)" 
                                        class="btn btn-danger btn-small">
                                    🗑️ Delete
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>

            <!-- Quick Tips -->
            <div class="info-section">
                <h3>💡 Category Management Tips</h3>
                <ul>
                    <li><strong>Rename:</strong> Change the category name for all questions in that category</li>
                    <li><strong>Merge:</strong> Combine two categories by moving all questions from one to another</li>
                    <li><strong>Delete:</strong> Remove a category and either delete its questions or move them to "General"</li>
                    <li><strong>Auto-Create:</strong> New categories are automatically created when you add questions in the admin panel</li>
                </ul>
            </div>
        </main>
    </div>

    <!-- Rename Modal -->
    <div id="renameModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('renameModal')">&times;</span>
            <div class="modal-header">
                <h3>✏️ Rename Category</h3>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="rename">
                <input type="hidden" name="old_category" id="rename_old_category">
                
                <div class="form-group">
                    <label>Current Name:</label>
                    <input type="text" id="rename_current_name" disabled class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="new_category">New Name:</label>
                    <input type="text" name="new_category" id="new_category" required class="form-control">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-back" onclick="closeModal('renameModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Rename Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            <div class="modal-header">
                <h3>🗑️ Delete Category</h3>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="category" id="delete_category">
                
                <div class="warning-box">
                    <strong>⚠️ Warning:</strong> You are about to delete the category "<span id="delete_category_name"></span>" 
                    which contains <strong><span id="delete_question_count"></span> question(s)</strong>.
                </div>
                
                <div class="form-group">
                    <label>What should happen to the questions?</label>
                    <label class="option-label">
                        <input type="radio" name="delete_action" value="move_to_general" checked>
                        <span class="option-content">Move questions to "General" category</span>
                    </label>
                    <label class="option-label">
                        <input type="radio" name="delete_action" value="delete_questions">
                        <span class="option-content">Delete all questions permanently</span>
                    </label>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-back" onclick="closeModal('deleteModal')">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Merge Modal -->
    <div id="mergeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('mergeModal')">&times;</span>
            <div class="modal-header">
                <h3>🔀 Merge Categories</h3>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="merge">
                <input type="hidden" name="from_category" id="merge_from_category">
                
                <div class="form-group">
                    <label>From Category:</label>
                    <input type="text" id="merge_from_name" disabled class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="to_category">Merge Into:</label>
                    <select name="to_category" id="to_category" required class="form-control">
                        <option value="">Select target category...</option>
                        <?php 
                        $categories->data_seek(0);
                        while ($cat = $categories->fetch_assoc()): 
                        ?>
                            <option value="<?php echo htmlspecialchars($cat['category']); ?>">
                                <?php echo htmlspecialchars($cat['category']); ?> (<?php echo $cat['question_count']; ?> questions)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="info-section" style="margin: 15px 0; padding: 15px;">
                    <p><strong>Note:</strong> All questions from the source category will be moved to the target category. 
                    The source category will be removed.</p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-back" onclick="closeModal('mergeModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Merge Categories</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRenameModal(category) {
            document.getElementById('rename_old_category').value = category;
            document.getElementById('rename_current_name').value = category;
            document.getElementById('new_category').value = category;
            document.getElementById('renameModal').style.display = 'block';
        }

        function openDeleteModal(category, questionCount) {
            document.getElementById('delete_category').value = category;
            document.getElementById('delete_category_name').textContent = category;
            document.getElementById('delete_question_count').textContent = questionCount;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function openMergeModal(category) {
            document.getElementById('merge_from_category').value = category;
            document.getElementById('merge_from_name').value = category;
            // Remove the source category from target dropdown
            const select = document.getElementById('to_category');
            Array.from(select.options).forEach(option => {
                if (option.value === category) {
                    option.disabled = true;
                    option.style.display = 'none';
                }
            });
            document.getElementById('mergeModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            // Re-enable all options in merge dropdown
            if (modalId === 'mergeModal') {
                const select = document.getElementById('to_category');
                Array.from(select.options).forEach(option => {
                    option.disabled = false;
                    option.style.display = 'block';
                });
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
