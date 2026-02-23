# Deployment Guide - The Thinking Mind

Complete guide for deploying to production PHP hosting environments.

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Database Setup](#database-setup)
3. [File Upload](#file-upload)
4. [Configuration](#configuration)
5. [Security Hardening](#security-hardening)
6. [Testing](#testing)
7. [Troubleshooting](#troubleshooting)

## Prerequisites

- PHP 7.4 or higher
- MySQL 8.0 or higher (or MariaDB 10.3+)
- FTP/SFTP or SSH access
- SSL/TLS certificate (Let's Encrypt recommended)
- Domain name configured

### Check Server Compatibility

Visit hosting control panel → PHP Info or create test file:
```php
<?php
echo phpversion();
echo extension_loaded('mysqli') ? 'MySQLi: OK' : 'MySQLi: MISSING';
echo ini_get('max_upload_size');
?>
```

## Database Setup

### 1. Create Database via cPanel/Plesk

**cPanel:**
- Home → Databases → MySQL Databases
- Database Name: `thethinkingmind`
- Click "Create Database"

**Plesk:**
- Databases → Add Database
- Name: `thethinkingmind`
- Click "Create"

### 2. Create Database User

**cPanel:**
- MySQL Users → Add User
- Username: `ttm_user` (or similar)
- Password: Use strong password (20+ chars)
- Add User
- User Privileges → Select user and database → Check "All" → Make Changes

**Plesk:**
- Same database → Add User
- Grant all privileges

**Save these credentials - you'll need them in config.php**

### 3. Import Database Schema

**Method A: phpMyAdmin**
```
1. Login to phpMyAdmin
2. Click on your database (thethinkingmind)
3. Click "Import" tab
4. Choose "database.sql" file
5. Click "Import"
```

**Method B: SSH/Command Line**
```bash
mysql -h localhost -u ttm_user -p thethinkingmind < database.sql
# When prompted, enter your database user password
```

**Method C: FTP Upload & Execute**
```
1. Upload database.sql to server via FTP
2. Use Remote MySQL tool or SSH to execute
3. Delete database.sql after import for security
```

## File Upload

### Step 1: Create Project Directory

**Via FTP:**
- Connect to server
- Navigate to `public_html` (or `www`)
- Create new folder: `thethinkingmind`

**Via SSH:**
```bash
ssh user@yourdomain.com
mkdir ~/public_html/thethinkingmind
cd ~/public_html/thethinkingmind
```

### Step 2: Upload Files

**Via FTP (FileZilla, Cyberduck, WinSCP):**
```
1. Connect with FTP credentials
2. Navigate to public_html/thethinkingmind/
3. Upload files (NOT docker-compose.yml or start.sh):
   - index.php
   - categories.php
   - exam.php
   - exam_review.php
   - admin.php
   - admin_login.php
   - manage_categories.php
   - auth.php
   - config.php (after editing!)
   - css/ folder (entire folder)
```

**Via SSH:**
```bash
scp -r ~/work/ai-work/exam-simulator/*.php user@yourdomain.com:~/public_html/thethinkingmind/
scp -r ~/work/ai-work/exam-simulator/css user@yourdomain.com:~/public_html/thethinkingmind/
```

**⚠️ Do NOT upload:**
- docker-compose.yml
- start.sh
- Dockerfile
- .git/ folder
- .env files with credentials

## Configuration

### Step 1: Edit config.php

**Template for config.php:**
```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'ttm_user');          // From Step 2 above
define('DB_PASS', 'YourPassword123!');  // From Step 2 above
define('DB_NAME', 'thethinkingmind');
define('DB_PORT', 3306);

// Application settings
define('QUESTIONS_PER_EXAM', 500);
define('PASSING_SCORE', 70);

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection function
function getDBConnection() {
    $conn = new mysqli(
        DB_HOST,
        DB_USER,
        DB_PASS,
        DB_NAME,
        DB_PORT
    );
    
    if ($conn->connect_error) {
        die('Database Connection Error: ' . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

?>
```

**Get credentials from:**
- cPanel: Databases → MySQL Databases → View Details
- Plesk: Databases → Select database → Users
- Hosting email/documentation
- Contact hosting support

### Step 2: Upload config.php

```bash
# Via FTP - Upload to public_html/thethinkingmind/config.php
# Via SCP:
scp config.php user@yourdomain.com:~/public_html/thethinkingmind/
```

## Security Hardening

### 1. File Permissions

**Via SSH:**
```bash
cd ~/public_html/thethinkingmind/

# Set directory permissions
chmod 755 .
chmod 755 css/

# Set PHP file permissions
chmod 644 *.php

# Protect config.php (critical!)
chmod 600 config.php

# Make it readable by web server
chown nobody:nobody config.php  # Or www-data:www-data
```

**Via FTP:** Right-click file → Properties → Set permissions (644 for files, 755 for directories)

### 2. Create .htaccess (Apache)

**File: public_html/thethinkingmind/.htaccess**

```apache
# Redirect HTTP to HTTPS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

# Prevent access to sensitive files
<FilesMatch "^(config|auth|database)\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Disable directory listing
<IfModule mod_autoindex.c>
    Options -Indexes
</IfModule>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Security headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
</IfModule>
```

### 3. SSL/HTTPS

**Via cPanel:**
- SSL/TLS Status → Find your domain
- Click "Install" (Usually free via Let's Encrypt AutoSSL)

**Update config.php to enforce HTTPS:**
```php
// Add at the very top after <?php
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}
```

### 4. Change Default Admin Password

```bash
# SSH into server
ssh user@yourdomain.com

# Generate password hash
php -r "echo password_hash('YourNewPassword123!', PASSWORD_BCRYPT);"

# Copy the output hash, then:
mysql -u ttm_user -p exam_simulator
```

```sql
UPDATE users SET password_hash='PASTE_HASH_HERE' WHERE username='admin';
```

## Testing

### 1. Verify Database Connection

Create test file: `test.php`
```php
<?php
require_once 'config.php';
$conn = getDBConnection();
$result = $conn->query("SELECT COUNT(*) as count FROM questions");
$row = $result->fetch_assoc();
echo "Questions in database: " . $row['count'];
$conn->close();
?>
```

Visit: `https://yourdomain.com/thethinkingmind/test.php`
Then delete test.php for security.

### 2. Test Student Portal

1. https://yourdomain.com/thethinkingmind/
2. Click "Take Assessment"
3. Select KCSA or Python
4. Complete exam

### 3. Test Admin Portal

1. https://yourdomain.com/thethinkingmind/admin_login.php
2. Login with admin credentials
3. Test adding a question
4. Test managing categories

### 4. Check Error Logs

**Via cPanel:**
- Home → Metrics → Error Log

**Via SSH:**
```bash
tail -f ~/public_html/thethinkingmind/error.log
tail -f ~/logs/error_log
```

## Troubleshooting

### "Database Connection Error"
- ✅ Verify DB_HOST, DB_USER, DB_PASS, DB_NAME in config.php
- ✅ Check database user has SELECT/INSERT/UPDATE/DELETE privileges
- ✅ Confirm database was imported (check tables exist in phpMyAdmin)

### "Cannot connect to database at localhost"
- ✅ Try `127.0.0.1` instead of `localhost`
- ✅ Ask hosting provider for correct database host (often `db.yourdomain.com`)
- ✅ Check MySQL service is running (cPanel → Service Manager)

### "Class mysqli not found"
- ✅ PHP doesn't have MySQLi extension
- ✅ Contact hosting support to enable it
- ✅ Use phpinfo() to verify MySQLi is listed

### "No questions available"
- ✅ Database imported but has no questions
- ✅ Run database.sql again
- ✅ Check if DELETE query accidentally cleared data

### "500 Internal Server Error"
- ✅ Check error logs (cPanel → Error Log)
- ✅ Enable error reporting in config.php (temporarily):
```php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
```
- ✅ Check file permissions (chmod 755 directories, 644 files)

### "Access Denied - Admin Login"
- ✅ Verify admin user exists: `SELECT * FROM users WHERE username='admin';`
- ✅ Reset password using SQL UPDATE above
- ✅ Check user role is 'admin'

## Maintenance

### Regular Tasks

**Daily:**
- Monitor error logs
- Verify site is accessible

**Weekly:**
- Backup database via cPanel
- Check server resources

**Monthly:**
- Test backup restoration
- Review admin logs
- Change admin password (every 90 days)

**Quarterly:**
- Update PHP version if available
- Update MySQL/MariaDB
- Security audit

### Backup Database

**Via cPanel:**
- Backup Wizard → Full Backup → Download

**Via SSH:**
```bash
mysqldump -u ttm_user -p exam_simulator > backup.sql
```

**Via phpMyAdmin:**
- Select database → Export → Download

## Contact Support

If problems persist:
1. Check error logs
2. Verify file permissions
3. Test database connection
4. Contact hosting technical support with error messages

---

**Deployment Complete!** Your platform is now live at https://yourdomain.com/thethinkingmind/
