<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Prompt Generator - The Thinking Mind</title>
    <meta name="description" content="Generate clear, structured AI prompts for studying, assignments, and assessment preparation on The Thinking Mind.">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="https://thethinkingmind.net/ai_prompt_generator.php">
    <meta property="og:type" content="website">
    <meta property="og:title" content="AI Prompt Generator - The Thinking Mind">
    <meta property="og:description" content="Build high-quality AI prompts for learning and productivity.">
    <meta property="og:url" content="https://thethinkingmind.net/ai_prompt_generator.php">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="AI Prompt Generator - The Thinking Mind">
    <meta name="twitter:description" content="Build high-quality AI prompts for learning and productivity.">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>✨ AI Prompt Generator</h1>
            <p class="subtitle">Create clear prompts for study and assessment tasks</p>
            <div class="header-buttons">
                <a href="tools_utilities.php" class="btn btn-back">Back</a>
            </div>
        </header>

        <main>
            <div class="info-section">
                <h3>Prompt Builder</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="promptGoal">Goal</label>
                        <input type="text" id="promptGoal" class="form-control" placeholder="e.g. Generate practice questions">
                    </div>
                    <div class="form-group">
                        <label for="promptAudience">Audience</label>
                        <input type="text" id="promptAudience" class="form-control" placeholder="e.g. Beginner learners">
                    </div>
                </div>

                <div class="form-group">
                    <label for="promptContext">Context or Topic</label>
                    <textarea id="promptContext" class="form-control" rows="4" placeholder="Describe the topic or constraints"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="promptTone">Tone</label>
                        <select id="promptTone" class="form-control">
                            <option value="clear and concise">Clear and concise</option>
                            <option value="friendly and encouraging">Friendly and encouraging</option>
                            <option value="formal and structured">Formal and structured</option>
                            <option value="creative and exploratory">Creative and exploratory</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="promptFormat">Output Format</label>
                        <select id="promptFormat" class="form-control">
                            <option value="bullet points">Bullet points</option>
                            <option value="step-by-step instructions">Step-by-step instructions</option>
                            <option value="table">Table</option>
                            <option value="short paragraph">Short paragraph</option>
                        </select>
                    </div>
                </div>

                <button id="generatePrompt" class="btn btn-secondary">Generate Prompt</button>
                <div class="message success" id="promptResult" style="display: none; margin-top: 15px; white-space: pre-wrap;"></div>
            </div>
        </main>

        <footer>
            <p>&copy; 2026 The Thinking Mind | Cultivating Excellence in Learning</p>
        </footer>
    </div>

    <script>
        const promptGoal = document.getElementById('promptGoal');
        const promptAudience = document.getElementById('promptAudience');
        const promptContext = document.getElementById('promptContext');
        const promptTone = document.getElementById('promptTone');
        const promptFormat = document.getElementById('promptFormat');
        const generatePrompt = document.getElementById('generatePrompt');
        const promptResult = document.getElementById('promptResult');

        generatePrompt.addEventListener('click', () => {
            const goal = promptGoal.value.trim();
            const audience = promptAudience.value.trim();
            const context = promptContext.value.trim();

            if (!goal || !context) {
                promptResult.style.display = 'block';
                promptResult.className = 'message error';
                promptResult.textContent = 'Please provide a goal and topic context.';
                return;
            }

            const tone = promptTone.value;
            const format = promptFormat.value;

            const prompt = [
                `You are a helpful assistant.`,
                `Goal: ${goal}.`,
                audience ? `Audience: ${audience}.` : null,
                `Context: ${context}.`,
                `Tone: ${tone}.`,
                `Format: ${format}.`,
                `Include clear, actionable guidance and keep the response focused.`
            ].filter(Boolean).join('\n');

            promptResult.style.display = 'block';
            promptResult.className = 'message success';
            promptResult.textContent = prompt;
        });
    </script>
</body>
</html>
