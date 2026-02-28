# IONOS Deployment Guide - feat-questions Branch (Delta Update)

**Date:** 28 February 2026  
**Branch:** `feat-questions` (commit: `19dd789`)  
**Base:** Main branch is already deployed; this guide covers the delta changes

---

## Overview

This deployment updates the exam-simulator with UI improvements and bug fixes:

| File | Changes |
|------|---------|
| `categories.php` | Removed quick selection buttons, removed "Start Mixed Exam" section, fixed URL encoding for category names |
| `exam.php` | Removed "Change Subject" button, fixed URL decoding for categories with special characters |
| `admin.php` | Updated Logout button styling to match other secondary buttons |
| `import_json.php` | Removed Home button, updated Logout button styling |
| `manage_categories.php` | Removed Home button, updated Logout button styling |
| `css/style.css` | Added modal styling for question count selector, cleanup for removed sections |

---

## Pre-Deployment Checklist

- ✅ Changes are committed and pushed to `feat-questions` branch
- ✅ All files tested locally at http://localhost:8080
- ✅ No database migrations required
- ✅ No new dependencies added
- ✅ Backward compatible with existing data

---

## Deployment Steps

### Step 1: SSH into IONOS Server

```bash
ssh -l user -p port_number your-domain.com
```

### Step 2: Navigate to Website Root

```bash
cd /home/user/public_html
# or wherever your exam-simulator is deployed
```

### Step 3: Verify Current Branch/Commit

```bash
git branch --show-current
git log --oneline -1
# Should show: 16253b4 Merge branch 'on-mac' into main for IONOS deployment
```

### Step 4: Pull Latest feat-questions Branch

```bash
git fetch origin feat-questions
git checkout feat-questions
git pull origin feat-questions
```

### Step 5: Verify Files Updated

Confirm these 6 files were changed:
```bash
git status
# Should show: nothing to commit, working tree clean
# (or only show expected files if you have local changes)
```

To see the diff of changes from current main:
```bash
git log --oneline feat-questions -1
# Should show: 19dd789 UI improvements: remove quick selection buttons...
```

### Step 6: Clear Browser Cache (Inform Users)

⚠️ **Important:** Users should hard-refresh their browsers to see UI changes:
- **Chrome/Firefox:** `Ctrl+Shift+R` (Windows) or `Cmd+Shift+R` (Mac)
- **Safari:** `Cmd+Option+R`

Or clear browser cache directly.

---

## What Changed - User-Facing

### Categories Page (`/categories.php`)
- ❌ Removed: "Start Mixed Exam" option for all subjects (users must select specific categories)
- ✅ Improved: Modal question count selector now uses only slider + quick preset labels removed
- 🐛 Fixed: Categories with special characters (e.g., `C++`) now work correctly

### Exam Page (`/exam.php`)
- ❌ Removed: "Change Subject" button from page header (only "Home" button remains)
- Reason: Streamlines navigation, reduces button clutter

### Admin Panel Pages
- 🎨 Updated: Logout buttons now styled consistently with other secondary buttons
- ❌ Removed: Home buttons from "Import JSON" and "Manage Categories" pages
- Reason: Admin users navigate back via "Back to Admin" button instead

---

## Rollback Instructions (If Needed)

If you need to revert to previous deployment:

```bash
git checkout main
git pull origin main
```

Then clear browser cache again.

---

## Testing on IONOS

### 1. Test Category Selection
- Navigate to http://your-domain.com/categories.php
- Verify "All Subjects" option is **gone**
- Click "Start Exam" on any category
- Modal should appear with slider (NO quick buttons)
- Select questions, submit → should redirect to exam.php with correct count

### 2. Test Special Character Categories
If you have categories with special chars (C++, C#, etc.):
- Click "Start Exam"
- Modal appears → set count
- Submit → exam loads with correct questions
- ✅ Should NOT show "No questions available" error

### 3. Test Exam Page
- Verify "Change Subject" button is **gone**
- Only "Home" button in header
- Take exam normally and submit

### 4. Test Admin Pages
- Log in to admin
- Navigate to "Import JSON" → verify no Home button, Logout styled like Import button
- Navigate to "Manage Categories" → verify no Home button, Logout styled in red
- Test category creation/rename/delete works normally

---

## Monitoring After Deployment

Check these things for 24 hours:

1. **Error Logs:** Monitor IONOS error logs for PHP warnings/errors
   ```bash
   tail -f /var/log/error_log  # or similar on your server
   ```

2. **User Reports:** Watch for complaints about missing features (mixed exam, change subject)
   - These are intentional removals; confirm users understand new workflow

3. **Category Issues:** Watch for errors with special character categories
   - Should be fixed by URL decoding in exam.php

---

## Deployment Checklist

- [ ] Fetched feat-questions from GitHub
- [ ] Checked out feat-questions locally
- [ ] Verified 6 files changed: `admin.php`, `categories.php`, `css/style.css`, `exam.php`, `import_json.php`, `manage_categories.php`
- [ ] Tested locally (all exams load correctly, categories work)
- [ ] Deployed to IONOS via git pull
- [ ] Verified files are updated on server
- [ ] Tested categories page loads
- [ ] Tested category with special characters
- [ ] Tested exam page
- [ ] Tested admin pages
- [ ] Informed users to hard-refresh browsers

---

## Summary of Changes

| Feature | Before | After | Status |
|---------|--------|-------|--------|
| Mixed Exam Option | ✅ Available | ❌ Removed | Intentional |
| Question Count Slider | Has quick buttons (5,10,20,30,50) | Slider only | Cleaner |
| Change Subject Button | ✅ Present | ❌ Removed | Intentional |
| Admin Logout Button | Different style | Matches other buttons | Consistency |
| Category URL Encoding | ❌ Broken for special chars | ✅ Fixed | Bug fix |

---

## Git Details

**Branch:** `feat-questions`  
**Latest Commit:** `19dd789` (8 commits ahead of main)  
**Files Changed:** 6  
**Lines Added:** 193  
**Lines Removed:** 38  

```bash
# View full changelog
git log --oneline main..feat-questions
```

---

## Questions?

If deployment fails or you encounter issues:

1. Check error logs: `/var/log/error_log` (PHP errors)
2. Verify database connection in `config.php` (no changes needed)
3. Ensure file permissions: `src` files should be readable (644), directories 755
4. Check git status for any merge conflicts

For urgent issues, rollback to main (see Rollback section above).

---

**Ready to deploy? Run Step 1 above and follow through Step 6!**
