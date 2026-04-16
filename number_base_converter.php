<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Number Base Converter - The Thinking Mind</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .tool-wrapper { padding: 32px; max-width: 720px; margin: 0 auto; }

        .base-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 24px;
        }
        @media (max-width: 520px) { .base-grid { grid-template-columns: 1fr; } }

        .base-field label {
            display: block;
            font-size: 0.82em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #555;
            margin-bottom: 6px;
        }
        .base-field .base-tag {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            font-size: 0.7em;
            padding: 2px 8px;
            border-radius: 20px;
            margin-left: 6px;
            vertical-align: middle;
            font-weight: 600;
        }
        .base-field input {
            width: 100%;
            padding: 12px 14px;
            font-size: 1.05em;
            font-family: 'Courier New', monospace;
            border: 2px solid #ddd;
            border-radius: var(--border-radius);
            transition: border-color 0.2s;
            letter-spacing: 0.05em;
        }
        .base-field input:focus { outline: none; border-color: var(--primary-color); }
        .base-field input.error { border-color: var(--danger-color); }

        .error-msg {
            color: var(--danger-color);
            font-size: 0.82em;
            margin-top: 5px;
            min-height: 18px;
        }

        .action-row { margin-top: 24px; display: flex; gap: 12px; }
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

        .info-box {
            margin-top: 32px;
            background: #f0f9f8;
            border-left: 4px solid var(--primary-color);
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
            padding: 16px 20px;
            font-size: 0.88em;
            color: #444;
            line-height: 1.7;
        }
        .info-box strong { color: var(--dark-color); }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>🔢 Number Base Converter</h1>
        <p class="subtitle">Convert between Binary, Octal, Decimal and Hexadecimal</p>
        <div class="header-buttons">
            <a href="tools_utilities.php" class="btn btn-back">Back to Tools</a>
        </div>
    </header>

    <main>
        <div class="tool-wrapper">
            <p style="color:#666; font-size:0.95em;">Type a value in any field — all others update instantly.</p>

            <div class="base-grid">
                <div class="base-field">
                    <label>Decimal <span class="base-tag">Base 10</span></label>
                    <input type="text" id="dec" placeholder="e.g. 255" oninput="convertFrom('dec')" />
                    <div class="error-msg" id="err-dec"></div>
                </div>
                <div class="base-field">
                    <label>Binary <span class="base-tag">Base 2</span></label>
                    <input type="text" id="bin" placeholder="e.g. 11111111" oninput="convertFrom('bin')" />
                    <div class="error-msg" id="err-bin"></div>
                </div>
                <div class="base-field">
                    <label>Octal <span class="base-tag">Base 8</span></label>
                    <input type="text" id="oct" placeholder="e.g. 377" oninput="convertFrom('oct')" />
                    <div class="error-msg" id="err-oct"></div>
                </div>
                <div class="base-field">
                    <label>Hexadecimal <span class="base-tag">Base 16</span></label>
                    <input type="text" id="hex" placeholder="e.g. FF" oninput="convertFrom('hex')" />
                    <div class="error-msg" id="err-hex"></div>
                </div>
            </div>

            <div class="action-row">
                <button class="btn-clear" onclick="clearAll()">Clear All</button>
            </div>

            <div class="info-box">
                <strong>Allowed characters:</strong><br>
                Decimal: 0–9 &nbsp;|&nbsp; Binary: 0–1 &nbsp;|&nbsp; Octal: 0–7 &nbsp;|&nbsp; Hexadecimal: 0–9, A–F
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 The Thinking Mind | Cultivating Excellence in Learning</p>
    </footer>
</div>

<script>
const configs = {
    dec: { base: 10, pattern: /^[0-9]+$/, label: 'Decimal' },
    bin: { base: 2,  pattern: /^[01]+$/,  label: 'Binary' },
    oct: { base: 8,  pattern: /^[0-7]+$/,  label: 'Octal' },
    hex: { base: 16, pattern: /^[0-9a-fA-F]+$/, label: 'Hexadecimal' }
};

function convertFrom(source) {
    const input = document.getElementById(source).value.trim();
    clearErrors();

    if (input === '') { clearOthers(source); return; }

    const cfg = configs[source];
    if (!cfg.pattern.test(input)) {
        document.getElementById('err-' + source).textContent = 'Invalid ' + cfg.label + ' value.';
        document.getElementById(source).classList.add('error');
        clearOthers(source);
        return;
    }

    document.getElementById(source).classList.remove('error');
    const decimal = parseInt(input, cfg.base);

    for (const key of ['dec','bin','oct','hex']) {
        if (key === source) continue;
        const base = configs[key].base;
        const result = decimal.toString(base).toUpperCase();
        document.getElementById(key).value = result;
    }
}

function clearErrors() {
    for (const key of ['dec','bin','oct','hex']) {
        document.getElementById('err-' + key).textContent = '';
        document.getElementById(key).classList.remove('error');
    }
}

function clearOthers(source) {
    for (const key of ['dec','bin','oct','hex']) {
        if (key !== source) document.getElementById(key).value = '';
    }
}

function clearAll() {
    for (const key of ['dec','bin','oct','hex']) {
        document.getElementById(key).value = '';
    }
    clearErrors();
}
</script>
</body>
</html>
