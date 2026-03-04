# Refactored Code Organization

This directory contains refactored, reusable code components for better maintainability.

## Structure

### includes/
- **bootstrap.php** - Main initialization file (include this at the start of each page)
- **functions.php** - Common utility functions
- **Database.php** - Database helper class for cleaner database operations

### templates/
- **header.php** - Common HTML header template
- **footer.php** - Common HTML footer template
- **navigation.php** - Navigation bar component

### classes/
- Reserved for future object-oriented models (Question, User, Exam, etc.)

## Usage

### Old Way (before refactoring):
```php
<?php
require_once 'config.php';
$conn = getDBConnection();

$category = $conn->real_escape_string($_GET['category']);
// More code...
?>
```

### New Way (after refactoring):
```php
<?php
require_once 'includes/bootstrap.php';

$category = sanitizeInput($conn, $_GET['category']);
// Or use the Database helper:
$questions = $db->getExamQuestions($category, $difficulty, 10);
?>
```

## Benefits

1. **Single include** - One `require_once 'includes/bootstrap.php'` loads everything
2. **Reusable functions** - Common operations in one place
3. **Cleaner code** - Less repetition, easier to maintain
4. **Templates** - Consistent HTML structure across pages
5. **Database abstraction** - Cleaner database operations
6. **Better security** - Centralized input sanitization

## Migration Guide

To use the refactored structure in existing files:

1. Replace multiple `require` statements with:
   ```php
   require_once 'includes/bootstrap.php';
   ```

2. Use utility functions instead of inline code:
   - `sanitizeInput()` instead of `real_escape_string()`
   - `validateQuestionCount()` for input validation
   - `calculateScore()` for score calculation
   - `formatCategoryName()` for display formatting

3. Use Database helper for queries:
   ```php
   // Instead of writing SQL:
   $questions = $db->getExamQuestions($category, $difficulty, 10);
   $categories = $db->getCategories();
   $question = $db->getQuestion($id);
   ```

4. Use template functions for HTML:
   ```php
   renderHeader("Page Title", "Description");
   renderNavigation('home');
   // Your page content
   renderFooter();
   ```

## Notes

- All existing files continue to work as-is
- Refactored code is backward compatible
- Gradually migrate files to use new structure
- Old patterns still work (config.php, direct $conn usage)

## Testing

After making changes:
1. Test in Docker container: `docker-compose up`
2. Validate PHP syntax: `docker exec exam_simulator_web php -l <file>`
3. Test all key features (categories, exams, admin panel)
4. Check database operations still work
