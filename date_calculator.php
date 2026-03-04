<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Date Calculator - The Thinking Mind</title>
    <meta name="description" content="Calculate date differences and add or subtract days using the date calculator on The Thinking Mind.">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="https://thethinkingmind.net/date_calculator.php">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Date Calculator - The Thinking Mind">
    <meta property="og:description" content="Find date differences and perform quick date arithmetic.">
    <meta property="og:url" content="https://thethinkingmind.net/date_calculator.php">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Date Calculator - The Thinking Mind">
    <meta name="twitter:description" content="Find date differences and perform quick date arithmetic.">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🗓️ Date Calculator</h1>
            <p class="subtitle">Find differences and add or subtract days</p>
            <div class="header-buttons">
                <a href="tools_utilities.php" class="btn btn-back">Back</a>
            </div>
        </header>

        <main>
            <div class="options-grid">
                <div class="option-card">
                    <div class="icon">⏳</div>
                    <h3>Date Difference</h3>
                    <p>Calculate the number of days between two dates.</p>
                    <div class="form-group">
                        <label for="startDate">Start Date</label>
                        <input type="date" id="startDate" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="endDate">End Date</label>
                        <input type="date" id="endDate" class="form-control">
                    </div>
                    <button id="diffBtn" class="btn btn-secondary btn-small">Calculate</button>
                    <p id="diffResult" class="message success" style="display: none; margin-top: 12px;"></p>
                </div>

                <div class="option-card">
                    <div class="icon">➕</div>
                    <h3>Add/Subtract Days</h3>
                    <p>Move forward or backward by a number of days.</p>
                    <div class="form-group">
                        <label for="baseDate">Base Date</label>
                        <input type="date" id="baseDate" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="dayOffset">Days Offset</label>
                        <input type="number" id="dayOffset" class="form-control" placeholder="e.g. 7 or -14">
                    </div>
                    <button id="offsetBtn" class="btn btn-secondary btn-small">Calculate</button>
                    <p id="offsetResult" class="message success" style="display: none; margin-top: 12px;"></p>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2026 The Thinking Mind | Cultivating Excellence in Learning</p>
        </footer>
    </div>

    <script>
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        const diffBtn = document.getElementById('diffBtn');
        const diffResult = document.getElementById('diffResult');

        const baseDate = document.getElementById('baseDate');
        const dayOffset = document.getElementById('dayOffset');
        const offsetBtn = document.getElementById('offsetBtn');
        const offsetResult = document.getElementById('offsetResult');

        const formatDate = (date) => date.toISOString().split('T')[0];

        const showMessage = (element, message, isError = false) => {
            element.style.display = 'block';
            element.className = isError ? 'message error' : 'message success';
            element.textContent = message;
        };

        diffBtn.addEventListener('click', () => {
            if (!startDate.value || !endDate.value) {
                showMessage(diffResult, 'Please select both dates.', true);
                return;
            }
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            const diffMs = end - start;
            const diffDays = Math.round(diffMs / (1000 * 60 * 60 * 24));
            showMessage(diffResult, `Difference: ${diffDays} day(s)`);
        });

        offsetBtn.addEventListener('click', () => {
            if (!baseDate.value || !dayOffset.value) {
                showMessage(offsetResult, 'Please select a date and enter a number.', true);
                return;
            }
            const base = new Date(baseDate.value);
            const offset = Number(dayOffset.value);
            if (Number.isNaN(offset)) {
                showMessage(offsetResult, 'Please enter a valid number.', true);
                return;
            }
            const result = new Date(base);
            result.setDate(result.getDate() + offset);
            showMessage(offsetResult, `Resulting date: ${formatDate(result)}`);
        });
    </script>
</body>
</html>
