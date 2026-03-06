<?php
error_log("ROUTER DEBUG: REQUEST_METHOD=" . $_SERVER['REQUEST_METHOD'] . ", REQUEST_URI=" . $_SERVER['REQUEST_URI'] . ", POST data keys: " . implode(',', array_keys($_POST)));

// Fix Apache query string issue if needed
// When rewriting occurs, QUERY_STRING may be lost but REQUEST_URI should preserve it
if (empty($_SERVER['QUERY_STRING']) && strpos($_SERVER['REQUEST_URI'], '?') !== false) {
    list($path_part, $query_part) = explode('?', $_SERVER['REQUEST_URI'], 2);
    $_SERVER['QUERY_STRING'] = $query_part;
    parse_str($query_part, $_GET);
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$route = ltrim($path, '/');

if ($route === '' || $route === '/') {
    $route = 'index.php';
}

$routeMap = [
    'index.php' => __DIR__ . '/pages/public/index.php',
    'categories.php' => __DIR__ . '/pages/public/categories.php',
    'category_detail.php' => __DIR__ . '/pages/public/category_detail.php',
    'exam.php' => __DIR__ . '/pages/public/exam.php',
    'exam_review.php' => __DIR__ . '/pages/public/exam_review.php',
    'guest.php' => __DIR__ . '/pages/public/guest.php',
    'login.php' => __DIR__ . '/pages/public/login.php',
    'logout.php' => __DIR__ . '/pages/public/logout.php',
    'register.php' => __DIR__ . '/pages/public/register.php',
    'my_results.php' => __DIR__ . '/pages/public/my_results.php',
    'results.php' => __DIR__ . '/pages/public/results.php',
    'review.php' => __DIR__ . '/pages/public/review.php',

    'admin_login.php' => __DIR__ . '/pages/admin/admin_login.php',
    'admin.php' => __DIR__ . '/pages/admin/admin.php',
    'dashboard.php' => __DIR__ . '/pages/admin/dashboard.php',
    'manage_categories.php' => __DIR__ . '/pages/admin/manage_categories.php',
    'import_csv.php' => __DIR__ . '/pages/admin/import_csv.php',
    'import_json.php' => __DIR__ . '/pages/admin/import_json.php',
    'create_users.php' => __DIR__ . '/pages/admin/create_users.php',
    'ai_prompt_generator.php' => __DIR__ . '/pages/admin/ai_prompt_generator.php',

    'date_calculator.php' => __DIR__ . '/pages/utilities/date_calculator.php',
    'unit_converter.php' => __DIR__ . '/pages/utilities/unit_converter.php',
    'tools_utilities.php' => __DIR__ . '/pages/utilities/tools_utilities.php',

    'pdf_export.php' => __DIR__ . '/pages/reports/pdf_export.php',
];

if (!isset($routeMap[$route])) {
    http_response_code(404);
    echo '404 Not Found';
    exit;
}

http_response_code(200);
require_once $routeMap[$route];
