# 📚 Practice Exam Simulator

A comprehensive PHP-based exam simulator for creating, managing, and taking practice exams with instant results and detailed review.

## ✨ Features

- **Multi-Question Exam System**: Random question selection for each exam attempt
- **Admin Panel**: Easy question management (add/delete questions)
- **Instant Grading**: Automatic scoring with percentage calculation
- **Detailed Review**: See correct answers and review your mistakes
- **Results Tracking**: Store and view all exam attempts
- **Statistics Dashboard**: Track performance over time
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Category & Difficulty Tags**: Organize questions by topic and difficulty level

## 🛠️ Technologies Used

- PHP 7.4+
- MySQL 5.7+
- HTML5
- CSS3
- JavaScript (Vanilla)

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **Web Server**: Apache, Nginx, or XAMPP/WAMP/MAMP
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Web Browser**: Modern browser (Chrome, Firefox, Safari, Edge)

## 🚀 Installation & Setup

### Step 1: Download/Clone the Project

Place the `exam-simulator` folder in your web server's document root:

- **XAMPP**: `C:/xampp/htdocs/exam-simulator`
- **WAMP**: `C:/wamp64/www/exam-simulator`
- **MAMP**: `/Applications/MAMP/htdocs/exam-simulator`
- **Linux**: `/var/www/html/exam-simulator`

### Step 2: Create Database

1. Open phpMyAdmin (usually at `http://localhost/phpmyadmin`)
2. Click on "New" to create a new database
3. Name it `exam_simulator`
4. Go to the "Import" tab
5. Click "Choose File" and select `database.sql` from the project folder
6. Click "Go" to import the database structure and sample questions

**Alternatively**, you can run the SQL file directly:

```bash
mysql -u root -p < database.sql
```

### Step 3: Configure Database Connection

Open `config.php` and update the database credentials if needed:

```php
define('DB_HOST', 'localhost');    // Usually 'localhost'
define('DB_USER', 'root');          // Your MySQL username
define('DB_PASS', '');              // Your MySQL password (empty for XAMPP)
define('DB_NAME', 'exam_simulator');// Database name
```

### Step 4: Start the Application

1. Start your web server (Apache) and MySQL service
2. Open your browser and visit:
   ```
   http://localhost/exam-simulator/
   ```

## 📁 Project Structure

```
exam-simulator/
│
├── index.php           # Home page with navigation options
├── exam.php            # Take exam page
├── review.php          # Review exam results with answers
├── results.php         # View all exam attempts
├── admin.php           # Admin panel for question management
├── config.php          # Database configuration
├── database.sql        # Database schema and sample data
│
├── css/
│   └── style.css       # All styling
│
└── README.md           # This file
```

## 🎯 Usage Guide

### For Students

1. **Start Exam**:
   - Click "Take Exam" from the home page
   - Enter your name
   - Answer all questions
   - Submit when ready

2. **View Results**:
   - See your score immediately after submission
   - Review correct answers and explanations
   - Identify areas for improvement

3. **Track Progress**:
   - View all your past exam attempts
   - See overall statistics
   - Monitor improvement over time

### For Administrators

1. **Add Questions**:
   - Go to Admin Panel
   - Fill in the question form
   - Specify correct answer, category, and difficulty
   - Click "Add Question"

2. **Manage Questions**:
   - View all existing questions
   - Delete questions as needed
   - Questions are automatically included in random exam selection

## ⚙️ Configuration Options

Edit `config.php` to customize:

```php
// Number of questions per exam
define('QUESTIONS_PER_EXAM', 10);

// Minimum passing score percentage
define('PASSING_SCORE', 70);
```

## 🎨 Customization

### Change Colors

Edit `css/style.css` to modify the color scheme:

```css
:root {
    --primary-color: #4a90e2;    /* Main theme color */
    --secondary-color: #7b68ee;   /* Secondary accent */
    --success-color: #2ecc71;     /* Success messages */
    --danger-color: #e74c3c;      /* Error messages */
}
```

### Add More Question Categories

Simply enter new category names when adding questions in the admin panel. Categories are automatically recognized.

## 🔒 Security Features

- SQL injection protection using prepared statements and real_escape_string
- Input validation on all forms
- Session management for user tracking
- XSS prevention with htmlspecialchars()

## 🐛 Troubleshooting

### Database Connection Error

**Problem**: "Connection failed: Access denied"

**Solution**:
- Check your database credentials in `config.php`
- Ensure MySQL service is running
- Verify database user has proper privileges

### Questions Not Displaying

**Problem**: "No questions available"

**Solution**:
- Import `database.sql` to create tables and sample questions
- Or add questions manually through the Admin Panel

### Blank Page

**Problem**: White/blank page appears

**Solution**:
- Enable PHP error reporting: Add to `config.php`:
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```
- Check web server error logs
- Verify PHP version is 7.4 or higher

### CSS Not Loading

**Problem**: Page appears unstyled

**Solution**:
- Verify the `css` folder exists
- Check file path in HTML files
- Clear browser cache

## 🔧 Advanced Modifications

### Add Time Limit

Add to `exam.php`:

```javascript
let timeLeft = 1800; // 30 minutes in seconds
const timer = setInterval(function() {
    timeLeft--;
    if (timeLeft <= 0) {
        clearInterval(timer);
        document.getElementById('examForm').submit();
    }
}, 1000);
```

### Add User Authentication

Create a login system:
1. Add `users` table to database
2. Create `login.php` and `register.php`
3. Use sessions to track logged-in users
4. Link exam results to user IDs

### Export Results to PDF

Use a PHP PDF library like TCPDF or FPDF:

```php
require_once('tcpdf/tcpdf.php');
$pdf = new TCPDF();
// Generate PDF from results
```

## 📊 Database Schema

### Tables

1. **questions**: Stores all exam questions
   - id, question_text, option_a, option_b, option_c, option_d
   - correct_answer, category, difficulty, created_at

2. **exam_results**: Stores exam attempt summary
   - id, student_name, total_questions, correct_answers
   - score_percentage, exam_date

3. **exam_answers**: Stores detailed answer data
   - id, result_id, question_id, user_answer, is_correct

## 🤝 Contributing

Feel free to fork this project and make improvements:

1. Add question categories filtering
2. Implement question difficulty selection
3. Add timer functionality
4. Create user authentication system
5. Add image support for questions
6. Implement multiple exam types

## 📝 License

This project is open source and available for educational purposes. Feel free to use, modify, and distribute.

## 👨‍💻 Support

For issues or questions:
- Review the Troubleshooting section
- Check database configuration
- Verify PHP/MySQL versions
- Test with sample data first

## 🎓 Learning Resources

This project demonstrates:
- PHP database connectivity (MySQLi)
- CRUD operations (Create, Read, Update, Delete)
- Form handling and validation
- Session management
- Responsive web design
- SQL database design

Perfect for:
- PHP beginners learning web development
- Students practicing database integration
- Teachers creating classroom quizzes
- Anyone needing a simple exam system

## 🚀 Future Enhancements

Potential features to add:
- [ ] User registration and login
- [ ] Question editing functionality
- [ ] Image/media support in questions
- [ ] Timed exams
- [ ] Question randomization within categories
- [ ] Export results to CSV/PDF
- [ ] Email notifications
- [ ] Multiple choice and true/false questions
- [ ] Question bank import/export
- [ ] Admin dashboard with charts

---

**Built with ❤️ using PHP and MySQL**

*Last Updated: February 2026*
