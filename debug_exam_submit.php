<?php
require_once 'config.php';

$conn = getDBConnection();

echo "DEBUG - REQUEST METHOD: " . $_SERVER['REQUEST_METHOD'] . "<br>";
echo "DEBUG - POST data: " . var_export($_POST, true) . "<br>";
echo "DEBUG - isset(\$_POST['submit_exam']): " . (isset($_POST['submit_exam']) ? 'TRUE' : 'FALSE') . "<br>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_exam'])) {
    echo "DEBUG - Inside POST submit condition<br>";
    echo "DEBUG - Attempting to redirect to exam_review.php<br>";
    header("Location: exam_review.php");
    exit();
} else {
    echo "DEBUG - Not entering submit condition<br>";
}

$conn->close();
?>
