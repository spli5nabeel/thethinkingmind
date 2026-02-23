# GitHub Setup Instructions

## Step 1: Initialize Local Git Repository

```bash
cd /Users/nabeel/work/ai-work/exam-simulator

# Initialize git
git init

# Add files to staging
git add .

# Create initial commit
git commit -m "Initial commit: The Thinking Mind assessment platform"
```

## Step 2: Create GitHub Repository

1. Go to https://github.com/new
2. Fill in repository name: `thethinkingmind`
3. Description: "Knowledge Assessment Platform - KCSA & Python Certifications"
4. Select: Public (or Private if you prefer)
5. ❌ Do NOT initialize with README (we already have one)
6. Click "Create repository"

## Step 3: Connect Local Repo to GitHub

Copy these commands from GitHub and run:

```bash
cd /Users/nabeel/work/ai-work/exam-simulator

# Add remote origin (replace USERNAME with your GitHub username)
git remote add origin https://github.com/YOUR_USERNAME/thethinkingmind.git

# Verify remote is added
git remote -v

# Rename branch to main (if they want)
git branch -M main

# Push to GitHub
git push -u origin main
```

## Step 4: Verify on GitHub

Visit: https://github.com/YOUR_USERNAME/thethinkingmind

You should see all your files!

## Ongoing - Make Changes and Push

After making changes locally:

```bash
# Check what changed
git status

# Add specific file
git add filename.php

# Or add all changes
git add .

# Commit changes
git commit -m "Add meaningful description of changes"

# Push to GitHub
git push origin main
```

## Common GitHub Commands

```bash
# Clone repository (if working from another machine)
git clone https://github.com/YOUR_USERNAME/thethinkingmind.git

# See commit history
git log

# See uncommitted changes
git diff

# Create new branch
git checkout -b feature/new-feature

# Push new branch
git push -u origin feature/new-feature

# Create Pull Request to merge back to main
# (Do this via GitHub web interface)
```

## Sensitive Files Already Excluded

✅ **config.php** - Not tracked (contains database credentials)
✅ **error.log** - Not tracked
✅ **Docker files** - You can add these if storing locally, or exclude

Anyone cloning your repo will need to create their own config.php with their database credentials.

## Share Repository

Once created, share the URL with others:
- https://github.com/YOUR_USERNAME/thethinkingmind

They can clone with:
```bash
git clone https://github.com/YOUR_USERNAME/thethinkingmind.git
```

## Add LICENSE (Optional)

```bash
git checkout --no-track origin/add-license
# Or add manually and commit
git add LICENSE
git commit -m "Add MIT license"
git push origin main
```

---

**Questions?** Let me know if you need help with any step!
