<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Thinking Mind - Knowledge Assessment Platform</title>
        <meta name="description" content="The Thinking Mind is an online exam simulator to practice subject-based assessments, test your knowledge, and improve performance with instant feedback.">
        <meta name="robots" content="index,follow">
        <link rel="canonical" href="https://thethinkingmind.net/">
        <meta property="og:type" content="website">
        <meta property="og:title" content="The Thinking Mind - Knowledge Assessment Platform">
        <meta property="og:description" content="Practice assessments by category, track your learning, and strengthen exam readiness.">
        <meta property="og:url" content="https://thethinkingmind.net/">
        <meta property="og:site_name" content="The Thinking Mind">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="The Thinking Mind - Knowledge Assessment Platform">
        <meta name="twitter:description" content="Practice assessments by category and improve exam performance.">
    <link rel="stylesheet" href="css/style.css">
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "The Thinking Mind",
            "url": "https://thethinkingmind.net/",
            "description": "Online knowledge assessment platform with practice exams and learning tools."
        }
        </script>
</head>
<body>

<!-- Cookie Consent Banner -->
<div id="cookie-banner" style="display:none;" role="dialog" aria-label="Cookie consent">
    <div id="cookie-banner-inner">
        <div id="cookie-banner-text">
            <strong>🍪 We use cookies</strong>
            <p>This site uses cookies and local storage to save your session, remember your preferences, and improve your experience. By continuing, you agree to our use of cookies.</p>
        </div>
        <div id="cookie-banner-actions">
            <button id="cookie-accept" onclick="acceptCookies()">Accept All</button>
            <button id="cookie-decline" onclick="declineCookies()">Decline</button>
        </div>
    </div>
</div>

<style>
#cookie-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #1a3a3a;
    color: #f5f7f6;
    z-index: 9999;
    padding: 16px 24px;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.2);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
#cookie-banner-inner {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
}
#cookie-banner-text {
    flex: 1;
    min-width: 240px;
}
#cookie-banner-text strong {
    font-size: 1em;
    display: block;
    margin-bottom: 4px;
}
#cookie-banner-text p {
    font-size: 0.85em;
    color: #b2dfdb;
    margin: 0;
}
#cookie-banner-actions {
    display: flex;
    gap: 10px;
    flex-shrink: 0;
}
#cookie-accept {
    background: #00897b;
    color: white;
    border: none;
    padding: 10px 22px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.9em;
    font-weight: 600;
    transition: background 0.2s;
}
#cookie-accept:hover { background: #00695c; }
#cookie-decline {
    background: transparent;
    color: #b2dfdb;
    border: 1px solid #b2dfdb;
    padding: 10px 22px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.9em;
    font-weight: 600;
    transition: all 0.2s;
}
#cookie-decline:hover { background: rgba(255,255,255,0.05); }
</style>

<script>
(function() {
    var consent = localStorage.getItem('cookie_consent');
    if (!consent) {
        document.getElementById('cookie-banner').style.display = 'block';
    }
})();

function acceptCookies() {
    localStorage.setItem('cookie_consent', 'accepted');
    document.getElementById('cookie-banner').style.display = 'none';
}

function declineCookies() {
    localStorage.setItem('cookie_consent', 'declined');
    document.getElementById('cookie-banner').style.display = 'none';
}
</script>
    <div class="container">
        <header>
            <h1>🧠 The Thinking Mind</h1>
            <p class="subtitle">Expand your knowledge | Challenge your understanding | Track your growth</p>
        </header>

        <main class="home-content">
            <div class="welcome-box">
                <h2>Welcome to Your Learning Journey</h2>
                <p>Assess your knowledge across diverse subjects and build mastery through practice</p>
            </div>

            <div class="options-grid">
                <div class="option-card">
                    <div class="icon">💻</div>
                    <h3>Tech Assessments</h3>
                    <p>Practice IT Certifications Questions</p>
                    <a href="categories.php?type=IT" class="btn btn-primary">Get Started</a>
                </div>

                <div class="option-card">
                    <div class="icon">📚</div>
                    <h3>Academic Assessments</h3>
                    <p>Practice academic subjects and strengthen your fundamentals</p>
                    <a href="categories.php?type=Academic" class="btn btn-primary">Get Started</a>
                </div>
            </div>

            <div class="tools-section">
                <div class="options-grid">
                    <div class="option-card">
                        <div class="icon">🧰</div>
                        <h3>Tools &amp; Utilities</h3>
                        <p>Browse utilities and add new tools for your workflow.</p>
                        <a href="tools_utilities.php" class="btn btn-secondary">Browse</a>
                    </div>

                    <div class="option-card">
                        <div class="icon">⚙️</div>
                        <h3>Administrator Access</h3>
                        <p>Manage assessments, questions, and platform content.</p>
                        <a href="admin_login.php" class="btn btn-admin">Admin Portal</a>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h3>How It Works</h3>
                <ol>
                    <li>Choose your subject of interest</li>
                    <li>Enter your name to begin the assessment</li>
                    <li>Answer questions thoughtfully and deliberately</li>
                    <li>Review your performance and identify areas for growth</li>
                </ol>
            </div>
        </main>

        <footer>
            <p>&copy; 2026 The Thinking Mind | Cultivating Excellence in Learning</p>
        </footer>
    </div>
</body>
</html>
