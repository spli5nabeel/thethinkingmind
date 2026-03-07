<?php
require_once 'config.php';
require_once 'auth.php';

$conn = getDBConnection();

function hasColumn($conn, $table, $column) {
    $table_esc = $conn->real_escape_string($table);
    $column_esc = $conn->real_escape_string($column);
    $result = $conn->query("SHOW COLUMNS FROM `$table_esc` LIKE '$column_esc'");
    return $result && $result->num_rows > 0;
}

// Get category filter if provided
$category = isset($_GET['category']) ? urldecode($conn->real_escape_string($_GET['category'])) : null;
$difficulty = isset($_GET['difficulty']) ? $conn->real_escape_string($_GET['difficulty']) : null;

$category_name = $category ? $category : "All Subjects";
$difficulty_display = $difficulty ? ucfirst($difficulty) : "Mixed";

// Get question count if provided, otherwise use default from config
$question_count = isset($_GET['question_count']) ? intval($_GET['question_count']) : QUESTIONS_PER_EXAM;
// Validate question count (between 5 and 50)
$question_count = max(5, min(50, $question_count));

// Get timer duration in minutes (default: 30 minutes)
$timer_minutes = isset($_GET['timer']) ? intval($_GET['timer']) : 30;
// Validate timer (between 5 and 180 minutes, or 0 for no timer)
$timer_minutes = ($timer_minutes == 0) ? 0 : max(5, min(180, $timer_minutes));

// Handle exam submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_exam'])) {
    $student_name = trim($_POST['student_name'] ?? '');
    $answers = $_POST['answers'] ?? [];
    
    $evaluated_answers = [];
    $correct_answers = 0;
    
    // Calculate score
    foreach ($answers as $question_id => $user_answer) {
        $question_id = intval($question_id);
        $user_answer = strtoupper(trim($user_answer));
        if (!in_array($user_answer, ['A', 'B', 'C', 'D'], true)) {
            continue;
        }

        $result = $conn->query("SELECT correct_answer FROM questions WHERE id = $question_id");
        if ($result && $row = $result->fetch_assoc()) {
            $is_correct = ($row['correct_answer'] === $user_answer);
            if ($is_correct) {
                $correct_answers++;
            }

            $evaluated_answers[$question_id] = [
                'user_answer' => $user_answer,
                'is_correct' => $is_correct
            ];
        }
    }

    $total_questions = count($evaluated_answers);
    
    $score_percentage = ($total_questions > 0) ? ($correct_answers / $total_questions) * 100 : 0;

    // Persist attempts for all users (guest + logged-in); attach user_id when available.
    $result_saved = false;
    $result_id = 0;
    if ($total_questions > 0) {
        $linked_user_id = null;
        $linked_full_name = '';

        if (isLoggedIn()) {
            $current_user = getCurrentUser();
            if ($current_user) {
                $linked_user_id = intval($current_user['id']);
                $linked_full_name = $current_user['full_name'];
            }
        }

        $display_name = $student_name !== '' ? $student_name : $linked_full_name;
        if ($display_name === '') {
            $display_name = 'Guest User';
        }

        $display_name_esc = $conn->real_escape_string($display_name);
        $score_for_db = round($score_percentage, 2);

        $conn->begin_transaction();
        try {
            if (hasColumn($conn, 'exam_results', 'user_id')) {
                if ($linked_user_id !== null) {
                    $insert_result_sql = "INSERT INTO exam_results (user_id, student_name, total_questions, correct_answers, score_percentage) VALUES ($linked_user_id, '$display_name_esc', $total_questions, $correct_answers, $score_for_db)";
                } else {
                    $insert_result_sql = "INSERT INTO exam_results (user_id, student_name, total_questions, correct_answers, score_percentage) VALUES (NULL, '$display_name_esc', $total_questions, $correct_answers, $score_for_db)";
                }
            } else {
                $insert_result_sql = "INSERT INTO exam_results (student_name, total_questions, correct_answers, score_percentage) VALUES ('$display_name_esc', $total_questions, $correct_answers, $score_for_db)";
            }

            if (!$conn->query($insert_result_sql)) {
                throw new Exception('Failed to store exam result.');
            }

            $result_id = intval($conn->insert_id);

            foreach ($evaluated_answers as $qid => $answer_data) {
                $qid = intval($qid);
                $answer = $conn->real_escape_string($answer_data['user_answer']);
                $is_correct_int = $answer_data['is_correct'] ? 1 : 0;
                $insert_answer_sql = "INSERT INTO exam_answers (result_id, question_id, user_answer, is_correct) VALUES ($result_id, $qid, '$answer', $is_correct_int)";

                if (!$conn->query($insert_answer_sql)) {
                    throw new Exception('Failed to store exam answers.');
                }
            }

            $conn->commit();
            $result_saved = true;

            if ($linked_user_id !== null) {
                logActivity($linked_user_id, 'exam_complete', 'Completed exam with score ' . number_format($score_percentage, 1) . '%');
            }
        } catch (Throwable $e) {
            $conn->rollback();
        }
    }

    if ($result_saved && $result_id > 0) {
        header("Location: review.php?result_id=$result_id");
        exit();
    }
    
    // Fallback: store results in session for guest mode or legacy setups.
    $_SESSION['exam_review'] = [
        'student_name' => $student_name,
        'total_questions' => $total_questions,
        'correct_answers' => $correct_answers,
        'score_percentage' => $score_percentage,
        'answers' => array_map(function ($answer_data) {
            return $answer_data['user_answer'];
        }, $evaluated_answers),
        'category' => $category,
        'difficulty' => $difficulty
    ];
    
    // Redirect to review page
    header("Location: exam_review.php");
    exit();
}

// Fetch random questions (filtered by category and difficulty if specified)
if ($category && $difficulty) {
    $questions = $conn->query("SELECT * FROM questions WHERE category = '$category' AND difficulty = '$difficulty' ORDER BY RAND() LIMIT " . $question_count);
} else if ($category) {
    $questions = $conn->query("SELECT * FROM questions WHERE category = '$category' ORDER BY RAND() LIMIT " . $question_count);
} else {
    $questions = $conn->query("SELECT * FROM questions ORDER BY RAND() LIMIT " . $question_count);
}

if ($questions->num_rows === 0) {
    die("No questions available for this category. Please select another category or add questions from the admin panel.");
}

$student_name_value = '';
if (isLoggedIn()) {
    $current_user = getCurrentUser();
    if ($current_user) {
        $student_name_value = $current_user['full_name'];
    }
} elseif (isGuest()) {
    $student_name_value = getGuestName() ?: '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment - The Thinking Mind</title>
    <meta name="description" content="Assessment session page for The Thinking Mind practice exams.">
    <meta name="robots" content="noindex,follow">
    <link rel="canonical" href="https://thethinkingmind.net/exam.php">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🧠 Knowledge Assessment</h1>
            <p class="subtitle">Category: <?php echo htmlspecialchars($category_name); ?> • Difficulty: <?php echo htmlspecialchars($difficulty_display); ?></p>
            <div class="header-buttons">
                <a href="index.php" class="btn btn-back">Home</a>
            </div>
        </header>

        <main>
            <div class="exam-info">
                <div class="info-item">
                    <strong>Subject:</strong> <?php echo htmlspecialchars($category_name); ?>
                </div>
                <div class="info-item">
                    <strong>Difficulty:</strong> <?php echo htmlspecialchars($difficulty_display); ?>
                </div>
                <div class="info-item">
                    <strong>Total Questions:</strong> <?php echo $questions->num_rows; ?>
                </div>
                <div class="info-item">
                    <strong>Passing Score:</strong> <?php echo PASSING_SCORE; ?>%
                </div>
                <div class="info-item timer-info">
                    <strong>Time Remaining:</strong>
                    <span id="timer-display" class="timer-display">
                        <?php echo $timer_minutes > 0 ? $timer_minutes . ':00' : 'No limit'; ?>
                    </span>
                </div>
            </div>

            <form method="POST" id="examForm">
                <div class="form-group student-name">
                    <label for="student_name">Your Name:</label>
                    <input type="text" name="student_name" id="student_name" required placeholder="Enter your name" value="<?php echo htmlspecialchars($student_name_value); ?>">
                </div>

                <div class="questions-container">
                    <?php 
                    $question_number = 1;
                    while ($q = $questions->fetch_assoc()): 
                    ?>
                        <div class="exam-question">
                            <div class="question-header">
                                <span class="question-number">Question <?php echo $question_number; ?></span>
                                <span class="badge <?php echo strtolower($q['difficulty']); ?>">
                                    <?php echo $q['difficulty']; ?>
                                </span>
                            </div>
                            
                            <div class="question-text">
                                <?php echo htmlspecialchars($q['question_text']); ?>
                            </div>

                            <div class="options">
                                <label class="option-label">
                                    <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="A" required>
                                    <span class="option-content">
                                        <strong>A.</strong> <?php echo htmlspecialchars($q['option_a']); ?>
                                    </span>
                                </label>

                                <label class="option-label">
                                    <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="B" required>
                                    <span class="option-content">
                                        <strong>B.</strong> <?php echo htmlspecialchars($q['option_b']); ?>
                                    </span>
                                </label>

                                <label class="option-label">
                                    <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="C" required>
                                    <span class="option-content">
                                        <strong>C.</strong> <?php echo htmlspecialchars($q['option_c']); ?>
                                    </span>
                                </label>

                                <label class="option-label">
                                    <input type="radio" name="answers[<?php echo $q['id']; ?>]" value="D" required>
                                    <span class="option-content">
                                        <strong>D.</strong> <?php echo htmlspecialchars($q['option_d']); ?>
                                    </span>
                                </label>
                            </div>
                        </div>
                    <?php 
                    $question_number++;
                    endwhile; 
                    ?>
                </div>

                <div class="submit-section">
                    <button type="submit" name="submit_exam" class="btn btn-primary btn-large">
                        Submit Exam
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        // Timer configuration
        const TIMER_MINUTES = <?php echo $timer_minutes; ?>;
        const STORAGE_KEY = 'exam_timer_' + Date.now();
        let timerInterval = null;
        let timeRemaining = TIMER_MINUTES * 60; // in seconds
        let isSubmitting = false;

        // Initialize timer from localStorage or start fresh
        function initTimer() {
            if (TIMER_MINUTES === 0) return; // No timer

            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored) {
                const data = JSON.parse(stored);
                const elapsed = Math.floor((Date.now() - data.startTime) / 1000);
                timeRemaining = Math.max(0, data.totalTime - elapsed);
            } else {
                // Store start time
                localStorage.setItem(STORAGE_KEY, JSON.stringify({
                    startTime: Date.now(),
                    totalTime: timeRemaining
                }));
            }

            startTimer();
        }

        // Start countdown
        function startTimer() {
            updateTimerDisplay();
            timerInterval = setInterval(() => {
                timeRemaining--;
                updateTimerDisplay();

                // Warning at 5 minutes
                if (timeRemaining === 300) {
                    alert('⏰ Warning: 5 minutes remaining!');
                }

                // Warning at 1 minute
                if (timeRemaining === 60) {
                    alert('⏰ Warning: 1 minute remaining!');
                }

                // Time's up
                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    handleTimeUp();
                }
            }, 1000);
        }

        // Update timer display
        function updateTimerDisplay() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            const timerEl = document.getElementById('timer-display');
            if (timerEl) {
                timerEl.textContent = display;
                
                // Visual warnings
                if (timeRemaining <= 60) {
                    timerEl.classList.add('timer-critical');
                } else if (timeRemaining <= 300) {
                    timerEl.classList.add('timer-warning');
                }
            }
        }

        // Handle time expiry
        function handleTimeUp() {
            if (isSubmitting) return;
            isSubmitting = true;
            
            alert('⏰ Time is up! Your exam will be submitted automatically.');
            localStorage.removeItem(STORAGE_KEY);
            document.getElementById('examForm').submit();
        }

        // Form submission handler
        document.getElementById('examForm').addEventListener('submit', function(e) {
            if (isSubmitting) return; // Already submitting due to timer
            
            const radios = document.querySelectorAll('input[type="radio"]');
            const questions = new Set();
            radios.forEach(radio => {
                const name = radio.getAttribute('name');
                questions.add(name);
            });
            
            let answered = 0;
            questions.forEach(name => {
                const checked = document.querySelector(`input[name="${name}"]:checked`);
                if (checked) answered++;
            });
            
            if (answered < questions.size) {
                if (!confirm(`You have answered ${answered} out of ${questions.size} questions. Submit anyway?`)) {
                    e.preventDefault();
                    return;
                }
            } else {
                if (!confirm('Are you sure you want to submit your exam?')) {
                    e.preventDefault();
                    return;
                }
            }
            
            // Clear timer on successful submission
            clearInterval(timerInterval);
            localStorage.removeItem(STORAGE_KEY);
        });

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            if (!isSubmitting && TIMER_MINUTES > 0) {
                // Timer will resume from localStorage on return
            }
        });

        // Initialize timer on page load
        if (TIMER_MINUTES > 0) {
            initTimer();
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
