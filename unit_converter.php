<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit Converter - The Thinking Mind</title>
    <meta name="description" content="Convert length, weight, and temperature units quickly with The Thinking Mind unit converter tool.">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="https://thethinkingmind.net/unit_converter.php">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Unit Converter - The Thinking Mind">
    <meta property="og:description" content="Convert common units for study and daily tasks.">
    <meta property="og:url" content="https://thethinkingmind.net/unit_converter.php">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Unit Converter - The Thinking Mind">
    <meta name="twitter:description" content="Convert common units for study and daily tasks.">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📏 Unit Converter</h1>
            <p class="subtitle">Convert length, weight, and temperature</p>
            <div class="header-buttons">
                <a href="tools_utilities.php" class="btn btn-back">Back</a>
            </div>
        </header>

        <main>
            <div class="info-section">
                <h3>Converter</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="unitType">Unit Type</label>
                        <select id="unitType" class="form-control">
                            <option value="length">Length</option>
                            <option value="weight">Weight</option>
                            <option value="temperature">Temperature</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="inputValue">Value</label>
                        <input type="number" id="inputValue" class="form-control" placeholder="Enter value">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fromUnit">From</label>
                        <select id="fromUnit" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="toUnit">To</label>
                        <select id="toUnit" class="form-control"></select>
                    </div>
                </div>

                <button id="convertBtn" class="btn btn-secondary">Convert</button>
                <p id="convertResult" class="message success" style="display: none; margin-top: 15px;"></p>
            </div>
        </main>

        <footer>
            <p>&copy; 2026 The Thinking Mind | Cultivating Excellence in Learning</p>
        </footer>
    </div>

    <script>
        const unitType = document.getElementById('unitType');
        const inputValue = document.getElementById('inputValue');
        const fromUnit = document.getElementById('fromUnit');
        const toUnit = document.getElementById('toUnit');
        const convertBtn = document.getElementById('convertBtn');
        const convertResult = document.getElementById('convertResult');

        const unitOptions = {
            length: [
                { value: 'm', label: 'Meters' },
                { value: 'km', label: 'Kilometers' },
                { value: 'ft', label: 'Feet' },
                { value: 'in', label: 'Inches' },
                { value: 'mi', label: 'Miles' }
            ],
            weight: [
                { value: 'kg', label: 'Kilograms' },
                { value: 'g', label: 'Grams' },
                { value: 'lb', label: 'Pounds' },
                { value: 'oz', label: 'Ounces' }
            ],
            temperature: [
                { value: 'c', label: 'Celsius' },
                { value: 'f', label: 'Fahrenheit' },
                { value: 'k', label: 'Kelvin' }
            ]
        };

        const toMeters = {
            m: 1,
            km: 1000,
            ft: 0.3048,
            in: 0.0254,
            mi: 1609.344
        };

        const toKilograms = {
            kg: 1,
            g: 0.001,
            lb: 0.45359237,
            oz: 0.028349523125
        };

        const populateUnits = () => {
            const type = unitType.value;
            fromUnit.innerHTML = '';
            toUnit.innerHTML = '';
            unitOptions[type].forEach((unit) => {
                const fromOpt = document.createElement('option');
                fromOpt.value = unit.value;
                fromOpt.textContent = unit.label;
                const toOpt = document.createElement('option');
                toOpt.value = unit.value;
                toOpt.textContent = unit.label;
                fromUnit.appendChild(fromOpt);
                toUnit.appendChild(toOpt);
            });
        };

        const convertTemperature = (value, from, to) => {
            if (from === to) return value;
            let celsius;
            if (from === 'c') celsius = value;
            if (from === 'f') celsius = (value - 32) * (5 / 9);
            if (from === 'k') celsius = value - 273.15;

            if (to === 'c') return celsius;
            if (to === 'f') return (celsius * 9 / 5) + 32;
            return celsius + 273.15;
        };

        const formatNumber = (value) => {
            if (!isFinite(value)) return '0';
            return Number.parseFloat(value.toFixed(6)).toString();
        };

        const performConversion = () => {
            const value = Number(inputValue.value);
            if (Number.isNaN(value)) {
                convertResult.style.display = 'block';
                convertResult.className = 'message error';
                convertResult.textContent = 'Please enter a valid number.';
                return;
            }

            const type = unitType.value;
            const from = fromUnit.value;
            const to = toUnit.value;
            let result;

            if (type === 'length') {
                const meters = value * toMeters[from];
                result = meters / toMeters[to];
            } else if (type === 'weight') {
                const kilograms = value * toKilograms[from];
                result = kilograms / toKilograms[to];
            } else {
                result = convertTemperature(value, from, to);
            }

            convertResult.style.display = 'block';
            convertResult.className = 'message success';
            convertResult.textContent = `${value} ${from.toUpperCase()} = ${formatNumber(result)} ${to.toUpperCase()}`;
        };

        unitType.addEventListener('change', populateUnits);
        convertBtn.addEventListener('click', performConversion);

        populateUnits();
    </script>
</body>
</html>
