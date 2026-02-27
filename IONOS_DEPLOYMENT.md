# IONOS Deployment Checklist for The Thinking Mind

## Current Issue: 404 Error
If you're getting 404, files are either:
1. Not uploaded yet
2. In the wrong folder
3. Domain not pointing to the upload folder

## Step-by-Step IONOS Upload

### 1. Find Your Web Root Directory
IONOS typically uses one of these:
- `/` (root)
- `htdocs/`
- `public_html/`
- `/yourdomain.com/`

**How to find it:**
- Login to IONOS File Manager
- Look for a folder that contains default IONOS pages (index.html or placeholder)
- Upload to that EXACT folder

### 2. Test Upload Location

**Upload these test files FIRST:**
1. `test.html` - If this works, your upload location is correct
2. `test.php` - If this works, PHP is enabled

**Visit:**
- http://thethinkingmind.net/test.html (should show green text)
- http://thethinkingmind.net/test.php (should show PHP info)

### 3. If test.html gives 404:
- You're uploading to the wrong folder
- Check IONOS documentation for "web root" or "document root"
- Try uploading to different folders until test.html works
- Contact IONOS support to confirm the correct upload path

### 4. Once test.html works, upload these files:

**PHP Files (to same folder as test.html):**
```
index.php
categories.php
exam.php
exam_review.php
admin.php
admin_login.php
auth.php
login.php
register.php
dashboard.php
logout.php
manage_categories.php
results.php
my_results.php
guest.php
tools_utilities.php
unit_converter.php
date_calculator.php
ai_prompt_generator.php
config.php (MUST be updated with IONOS credentials!)
```

**Folders:**
```
css/ (entire folder with style.css inside)
```

### 5. Database Setup

**In IONOS phpMyAdmin:**
1. Create database or use existing one
2. Import database.sql
3. Note down:
   - Database host (usually 'localhost')
   - Database name
   - Username
   - Password

### 6. Update config.php

Replace config.php content with your IONOS credentials:
```php
<?php
define('DB_HOST', 'localhost');              // IONOS host
define('DB_USER', 'your_username');          // IONOS username
define('DB_PASS', 'your_password');          // IONOS password
define('DB_NAME', 'your_database_name');     // IONOS database
```

### 7. Test the site
Visit: http://thethinkingmind.net/

## Common IONOS Issues

**404 on root but test.html works:**
- index.php might not be set as default document
- Check .htaccess or IONOS settings

**Blank page:**
- Database connection issue
- Run test-connection.php to diagnose

**Need Help?**
1. Confirm test.html shows up at your domain
2. Share what happens with test.php
3. Check IONOS control panel for web root path
