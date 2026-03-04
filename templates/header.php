<?php
/**
 * Common HTML header template
 * @param string $title Page title
 * @param string $description Meta description
 * @param string $canonical Canonical URL
 * @param array $extra_meta Additional meta tags
 */
function renderHeader($title = "The Thinking Mind - Knowledge Assessment Platform", 
                      $description = "Test your knowledge with interactive assessments and track your learning progress.",
                      $canonical = "",
                      $extra_meta = []) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
    <meta name="robots" content="index,follow">
    
    <?php if ($canonical): ?>
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical); ?>">
    <?php endif; ?>
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($description); ?>">
    <?php if ($canonical): ?>
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical); ?>">
    <?php endif; ?>
    <meta property="og:site_name" content="The Thinking Mind">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($description); ?>">
    
    <?php foreach ($extra_meta as $key => $value): ?>
    <meta name="<?php echo htmlspecialchars($key); ?>" content="<?php echo htmlspecialchars($value); ?>">
    <?php endforeach; ?>
    
    <!-- Stylesheet -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php
}
?>
