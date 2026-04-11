<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Word & Character Counter - The Thinking Mind</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .tool-wrapper { padding: 32px; max-width: 860px; margin: 0 auto; }
        .tool-wrapper h2 { margin-bottom: 8px; color: var(--dark-color); }
        .tool-wrapper p.desc { color: #666; margin-bottom: 24px; font-size: 0.95em; }

        textarea#input-text {
            width: 100%;
            height: 260px;
            padding: 16px;
            font-size: 1em;
            border: 2px solid #ddd;
            border-radius: var(--border-radius);
            resize: vertical;
            font-family: inherit;
            transition: border-color 0.2s;
            line-height: 1.6;
        }
        textarea#input-text:focus { outline: none; border-color: var(--primary-color); }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }
        .stat-card {
            background: #f5f7f6;
            border-radius: var(--border-radius);
            padding: 20px 16px;
            text-align: center;
            border: 1px solid #e0e0e0;
        }
        .stat-card .stat-value {
            font-size: 2em;
            font-weight: 700;
            color: var(--primary-color);
            line-height: 1;
        }
        .stat-card .stat-label {
            font-size: 0.8em;
            color: #666;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .action-row {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .btn-clear {
            background: transparent;
            color: var(--danger-color);
            border: 1px solid var(--danger-color);
            padding: 10px 22px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9em;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-clear:hover { background: var(--danger-color); color: white; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>🔤 Word & Character Counter</h1>
        <p class="subtitle">Paste or type text to analyse it instantly</p>
        <div class="header-buttons">
            <a href="tools_utilities.php" class="btn btn-back">Back to Tools</a>
        </div>
    </header>

    <main>
        <div class="tool-wrapper">
            <textarea id="input-text" placeholder="Start typing or paste your text here…" oninput="analyse()"></textarea>

            <div class="action-row">
                <button class="btn-clear" onclick="clearText()">Clear</button>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value" id="stat-words">0</div>
                    <div class="stat-label">Words</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-chars">0</div>
                    <div class="stat-label">Characters</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-chars-no-space">0</div>
                    <div class="stat-label">Chars (no spaces)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-sentences">0</div>
                    <div class="stat-label">Sentences</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-paragraphs">0</div>
                    <div class="stat-label">Paragraphs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-read-time">0</div>
                    <div class="stat-label">Read time (min)</div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 The Thinking Mind | Cultivating Excellence in Learning</p>
    </footer>
</div>

<script>
function analyse() {
    const text = document.getElementById('input-text').value;

    const words = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
    const chars = text.length;
    const charsNoSpace = text.replace(/\s/g, '').length;
    const sentences = text.trim() === '' ? 0 : (text.match(/[^.!?]*[.!?]+/g) || []).length;
    const paragraphs = text.trim() === '' ? 0 : text.trim().split(/\n\s*\n/).filter(p => p.trim() !== '').length || (text.trim() !== '' ? 1 : 0);
    const readTime = Math.ceil(words / 200);

    document.getElementById('stat-words').textContent = words.toLocaleString();
    document.getElementById('stat-chars').textContent = chars.toLocaleString();
    document.getElementById('stat-chars-no-space').textContent = charsNoSpace.toLocaleString();
    document.getElementById('stat-sentences').textContent = sentences.toLocaleString();
    document.getElementById('stat-paragraphs').textContent = paragraphs.toLocaleString();
    document.getElementById('stat-read-time').textContent = readTime;
}

function clearText() {
    document.getElementById('input-text').value = '';
    analyse();
}
</script>
</body>
</html>
