# Quick Deploy: feat-questions to IONOS

## 30-Second Deploy

```bash
# SSH into IONOS
ssh -l user -p port your-domain.com

# Go to website root
cd /home/user/public_html

# Pull latest feat-questions
git fetch origin feat-questions
git checkout feat-questions
git pull origin feat-questions

# Verify
git log --oneline -1
# Should show: 19dd789 UI improvements...
```

## Changed Files (6 total)

✏️ **Modified:**
- `categories.php` - Removed quick buttons + mixed exam
- `exam.php` - Removed change subject button + fixed URL encoding
- `admin.php` - Updated logout button styling
- `import_json.php` - Removed home button + logout styling
- `manage_categories.php` - Removed home button + logout styling
- `css/style.css` - Modal styling + cleanup

## What Users Will See

✅ **New:**
- Question count slider modal (cleaner)
- Fixed categories with special characters (C++, C#)

❌ **Removed:**
- "Start Mixed Exam" button
- "Change Subject" button on exam page
- Redundant Home buttons on admin pages

## Testing (5 min)

1. Visit `/categories.php` → No "All Subjects" option ✓
2. Click "Start Exam" → Modal appears with slider ✓
3. Create category with C++ or C# → Works ✓
4. Admin pages → No Home button, logout styled ✓

## If something breaks

```bash
# Revert to main
git checkout main
git pull origin main
```

## Notes

- No database changes needed
- No new dependencies
- Cache browsers with Ctrl+Shift+R or Cmd+Shift+R
- Check `/var/log/error_log` if issues

---

**Full guide:** See `IONOS_DEPLOYMENT_FEAT_QUESTIONS.md` for detailed testing & rollback steps
