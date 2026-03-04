<?php
// Must start session before any includes that use it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// Simple logging for debugging
$debug_log = '/tmp/pdf_export_debug.log';
file_put_contents($debug_log, "[" . date('Y-m-d H:i:s') . "] PDF Export accessed\n", FILE_APPEND);
file_put_contents($debug_log, "Session exam_review exists: " . (isset($_SESSION['exam_review']) ? 'YES' : 'NO') . "\n", FILE_APPEND);

// Check if exam review data is available
if (!isset($_SESSION['exam_review'])) {
    file_put_contents($debug_log, "ERROR: No exam_review in session, redirecting to index.php\n", FILE_APPEND);
    header('Location: index.php');
    exit();
}

// Verify TCPDF library exists
if (!file_exists('lib/tcpdf/tcpdf.php')) {
    file_put_contents($debug_log, "ERROR: TCPDF library not found at lib/tcpdf/tcpdf.php\n", FILE_APPEND);
    die('Error: TCPDF library not found. Please contact administrator.');
}

file_put_contents($debug_log, "TCPDF library found, loading...\n", FILE_APPEND);
require_once 'lib/tcpdf/tcpdf.php';

$review = $_SESSION['exam_review'];
$student_name = $review['student_name'];
$total_questions = $review['total_questions'];
$correct_answers = $review['correct_answers'];
$score_percentage = $review['score_percentage'];
$answers = $review['answers'];
$category = $review['category'] ?? 'Assessment';
$difficulty = $review['difficulty'] ?? 'Mixed';

$passed = $score_percentage >= PASSING_SCORE;

// Create PDF object using default settings
$pdf = new TCPDF();

// Set document properties
$pdf->SetCreator('The Thinking Mind');
$pdf->SetAuthor('The Thinking Mind');
$pdf->SetTitle($category . ' Assessment Results');
$pdf->SetSubject('Assessment Results Report');

// Set margins
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, 15);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', 'B', 20);

// Header with logo concept
$pdf->SetTextColor(0, 137, 123); // Primary color: #00897b
$pdf->Cell(0, 15, '🧠 The Thinking Mind', 0, FALSE, 'C', FALSE, '', 0, FALSE, 'M', 'M');
$pdf->Ln(8);

// Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetTextColor(26, 58, 58); // Dark color
$pdf->Cell(0, 10, 'Assessment Results Report', 0, FALSE, 'C', FALSE, '', 0, FALSE, 'M', 'M');
$pdf->Ln(12);

// Separator line
$pdf->SetDrawColor(0, 137, 123);
$pdf->Cell(0, 0, '', 'T', FALSE, 'C');
$pdf->Ln(8);

// Student Info Section
$pdf->SetFont('helvetica', '', 11);
$pdf->SetTextColor(0, 0, 0);

$info_data = array(
    array('Student Name:', htmlspecialchars($student_name)),
    array('Category:', htmlspecialchars($category)),
    array('Difficulty Level:', htmlspecialchars($difficulty)),
    array('Date:', date('M d, Y')),
    array('Time:', date('h:i A')),
);

foreach ($info_data as $row) {
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(50, 6, $row[0], 0, FALSE, 'L');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, $row[1], 0, FALSE, 'L');
    $pdf->Ln(6);
}

$pdf->Ln(5);

// Score Section with background
$pdf->SetFillColor(0, 137, 123);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 12, 'SCORE: ' . number_format($score_percentage, 1) . '%', 0, FALSE, 'C', TRUE);
$pdf->Ln(8);

// Results Table
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('helvetica', 'B', 10);

$pdf->Cell(80, 7, 'Result', 1, FALSE, 'C', TRUE);
$pdf->Cell(50, 7, 'Count', 1, FALSE, 'C', TRUE);
$pdf->Cell(60, 7, 'Percentage', 1, FALSE, 'C', TRUE);
$pdf->Ln(7);

$pdf->SetFont('helvetica', '', 10);
$pdf->SetFillColor(255, 255, 255);

// Correct row
$pdf->SetTextColor(16, 185, 129); // Green
$pdf->Cell(80, 6, '✓ Correct Answers', 1, FALSE, 'L', FALSE);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(50, 6, $correct_answers, 1, FALSE, 'C', FALSE);
$percentage_correct = ($total_questions > 0) ? ($correct_answers / $total_questions) * 100 : 0;
$pdf->Cell(60, 6, number_format($percentage_correct, 1) . '%', 1, FALSE, 'C', FALSE);
$pdf->Ln(6);

// Incorrect row
$incorrect = $total_questions - $correct_answers;
$pdf->SetTextColor(239, 68, 68); // Red
$pdf->Cell(80, 6, '✗ Incorrect Answers', 1, FALSE, 'L', FALSE);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(50, 6, $incorrect, 1, FALSE, 'C', FALSE);
$percentage_incorrect = ($total_questions > 0) ? ($incorrect / $total_questions) * 100 : 0;
$pdf->Cell(60, 6, number_format($percentage_incorrect, 1) . '%', 1, FALSE, 'C', FALSE);
$pdf->Ln(6);

// Total row
$pdf->SetFillColor(245, 247, 246);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(80, 6, 'Total Questions', 1, FALSE, 'L', TRUE);
$pdf->Cell(50, 6, $total_questions, 1, FALSE, 'C', TRUE);
$pdf->Cell(60, 6, '100%', 1, FALSE, 'C', TRUE);
$pdf->Ln(10);

// Performance Message
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(0, 0, 0);
$message = $passed ? 
    'Excellent performance! You have successfully passed this assessment. Keep up the great work!' :
    'You did not meet the passing score of ' . PASSING_SCORE . '%. Review the questions you missed and try again!';

$pdf->MultiCell(0, 5, $message, 0, 'L', FALSE);
$pdf->Ln(5);

// Add detailed review on new page
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor(0, 137, 123);
$pdf->Cell(0, 10, 'Detailed Question Review', 0, FALSE, 'L');
$pdf->Ln(8);

// Questions review
$conn = getDBConnection();
$question_number = 1;
$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(0, 0, 0);

foreach ($answers as $question_id => $user_answer):
    $q_result = $conn->query("SELECT * FROM questions WHERE id = $question_id");
    if ($q_result && $question = $q_result->fetch_assoc()):
        $is_correct = ($question['correct_answer'] === $user_answer);
        
        // Question number and status
        $status_text = $is_correct ? '✓ CORRECT' : '✗ INCORRECT';
        $status_color = $is_correct ? array(16, 185, 129) : array(239, 68, 68);
        
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor($status_color[0], $status_color[1], $status_color[2]);
        $pdf->Cell(0, 6, 'Q' . $question_number . ': ' . $status_text, 0, FALSE, 'L');
        $pdf->Ln(6);
        
        // Question text
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(0, 4, 'Question: ' . strip_tags($question['question_text']), 0, 'L', FALSE);
        $pdf->Ln(2);
        
        // User's answer
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->MultiCell(0, 4, 'Your Answer: ' . htmlspecialchars($user_answer), 0, 'L', FALSE);
        
        // Correct answer (if wrong)
        if (!$is_correct) {
            $pdf->SetTextColor(16, 185, 129);
            $pdf->MultiCell(0, 4, 'Correct Answer: ' . htmlspecialchars($question['correct_answer']), 0, 'L', FALSE);
        }
        
        $pdf->Ln(3);
        $question_number++;
        
        // Check if we need a new page
        if ($pdf->GetY() > 250) {
            $pdf->AddPage();
        }
    endif;
endforeach;

// Footer
$pdf->SetY(-20);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColor(150, 150, 150);
$pdf->Cell(0, 5, 'Generated by The Thinking Mind | ' . date('M d, Y h:i A'), 0, FALSE, 'C');

// Output PDF
try {
    $filename = str_replace(' ', '_', $category) . '-' . $difficulty . '-' . date('Y-m-d') . '.pdf';
    file_put_contents($debug_log, "Generating PDF with filename: $filename\n", FILE_APPEND);
    // Force download by setting proper headers
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: public');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    file_put_contents($debug_log, "Headers sent, outputting PDF...\n", FILE_APPEND);
    $pdf->Output($filename, 'D'); // 'D' = download to browser
    file_put_contents($debug_log, "PDF output complete\n", FILE_APPEND);
} catch (Exception $e) {
    file_put_contents($debug_log, "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    die('Error generating PDF: ' . $e->getMessage());
}

$conn->close();

// Clear the session after PDF generation is complete
unset($_SESSION['exam_review']);
?>
