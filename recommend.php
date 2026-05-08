<?php
session_start();

// ============================================================
// STEP 1: DISEASE NAME MAPPING
// Front_End.php uses display names → Prolog uses atom names
// ============================================================
$diseaseMap = [
    // exact matches from your combo box values → prolog atoms
    'Diabetes'           => 'diabetes',
    'High Blood Pressure'=> 'hypertension',
    'Heart Disease'      => 'heart_disease',
    'Kidney Disease'     => 'ckd',
    'Cholesterol'        => 'cholesterol',
    // also accept direct prolog names (from text input)
    'diabetes'           => 'diabetes',
    'hypertension'       => 'hypertension',
    'heart_disease'      => 'heart_disease',
    'ckd'                => 'ckd',
    'cholesterol'        => 'cholesterol',
];

// Allergy mapping from Front_End.php select → prolog atoms
$allergyMap = [
    'seafood'  => 'fish',
    'eggs'     => 'egg',
    'milk'     => 'milk',
    'peanuts'  => 'peanut',
    'soy'      => 'soy',
    'coconut'  => null,   // not in prolog, safely ignored
    'spices'   => null,   // not in prolog, safely ignored
];

// Age group mapping from Front_End.php → prolog atoms
$ageMap = [
    'child'  => 'child',
    'teen'   => 'young',   // teen → young (closest match)
    'adult'  => 'adult',
    'senior' => 'elderly',
];

// Diet preference mapping
$prefMap = [
    'Vegetarian'     => 'veg',
    'Non-Vegetarian' => 'nonveg',
    'veg'            => 'veg',
    'nonveg'         => 'nonveg',
];

// ============================================================
// STEP 2: COLLECT & VALIDATE INPUTS FROM FORM
// diseases[] comes as array from Front_End.php
// ============================================================

$rawDiseases  = $_POST['diseases']  ?? [];
$rawAge       = $_POST['age_group'] ?? 'adult';
$rawPref      = $_POST['preference'] ?? 'Vegetarian';
$rawAllergy   = $_POST['allergies']  ?? '';

// If diseases sent as comma-separated string from JS (diseaseList)
// handle both array and string formats
if (is_string($rawDiseases)) {
    $rawDiseases = array_map('trim', explode(',', $rawDiseases));
}

// Map diseases to prolog atoms, skip unknown ones gracefully
$prologDiseases = [];
$displayDiseases = [];
foreach ($rawDiseases as $d) {
    $d = trim($d);
    if (isset($diseaseMap[$d])) {
        $atom = $diseaseMap[$d];
        if (!in_array($atom, $prologDiseases)) {
            $prologDiseases[] = $atom;
            $displayDiseases[] = $d;
        }
    }
    // Unknown diseases are silently ignored - system won't crash
}

// If no supported diseases found, show friendly error
if (empty($prologDiseases)) {
    $errorMessage = "None of the selected diseases are currently supported by our AI system. We support: Diabetes, High Blood Pressure, Heart Disease, Kidney Disease, and High Cholesterol.";
    $showError = true;
}

// Map age group
$prologAge = $ageMap[$rawAge] ?? 'adult';

// Map preference
$prologPref = $prefMap[$rawPref] ?? 'veg';

// Map allergy (single select in your front end)
$prologAllergies = [];
if (!empty($rawAllergy) && isset($allergyMap[$rawAllergy])) {
    $mapped = $allergyMap[$rawAllergy];
    if ($mapped !== null) {
        $prologAllergies[] = $mapped;
    }
}

// ============================================================
// STEP 3: BUILD PROLOG QUERY STRING
// recommend([diabetes, hypertension], adult, veg, [milk])
// ============================================================

$diseaseList  = '[' . implode(',', $prologDiseases) . ']';
$allergyList  = empty($prologAllergies) ? '[]' : '[' . implode(',', $prologAllergies) . ']';
$prologQuery  = "recommend({$diseaseList},{$prologAge},{$prologPref},{$allergyList})";

// ============================================================
// STEP 4: RUN PROLOG VIA shell_exec
// Adjust $swiplPath to your installation
// ============================================================

$prologFile = __DIR__ . '/foodie.pl';

// ---- CHANGE THIS PATH TO MATCH YOUR SYSTEM ----
// Windows default:
$swiplPath = '"C:\Program Files\swipl\bin\swipl.exe"';
// Linux/Mac: $swiplPath = 'swipl';
// ------------------------------------------------

$command = "{$swiplPath} -g \"{$prologQuery},halt.\" -t \"halt(1).\" -f " . escapeshellarg($prologFile) . " 2>&1";

$output = '';
$runError = false;

if (!isset($showError)) {
    $output = shell_exec($command);
    if (empty(trim($output))) {
        $runError = true;
    }
}

// ============================================================
// STEP 5: PARSE PROLOG OUTPUT INTO PHP VARIABLES
// ============================================================

function extractValue($label, $output) {
    $pattern = '/' . preg_quote($label, '/') . '\s*:\s*(.+)/';
    if (preg_match($pattern, $output, $m)) {
        return trim($m[1]);
    }
    return null;
}

function extractBlock($start, $end, $output) {
    $pattern = '/' . preg_quote($start, '/') . '\s*(.*?)\s*' . preg_quote($end, '/') . '/s';
    if (preg_match($pattern, $output, $m)) {
        $lines = array_filter(
            array_map('trim', explode("\n", $m[1])),
            fn($l) => !empty($l) && !str_starts_with($l, '===') && !str_starts_with($l, '---') && !str_starts_with($l, '***')
        );
        return array_values($lines);
    }
    return [];
}

function extractTip($output) {
    $pattern = '/=== HEALTH TIP ===\s*(.*?)\s*-{3,}/s';
    if (preg_match($pattern, $output, $m)) {
        return trim($m[1]);
    }
    return null;
}

$breakfast  = extractValue('BREAKFAST',     $output);
$lunch      = extractValue('LUNCH',         $output);
$snack      = extractValue('EVENING SNACK', $output);
$dinner     = extractValue('DINNER',        $output);
$beverage   = extractValue('BEVERAGE',      $output);

$recommendedFoods = extractBlock('=== HIGHLY RECOMMENDED FOODS ===', '=== FOODS TO AVOID ===',    $output);
$avoidFoods       = extractBlock('=== FOODS TO AVOID ===',           '=== NUTRITION TARGETS ===', $output);
$nutritionLines   = extractBlock('=== NUTRITION TARGETS ===',        '=== HEALTH TIP ===',        $output);
$tip              = extractTip($output);

// Clean bullet/plus/dash prefixes from lists
$recommendedFoods = array_map(fn($f) => ltrim($f, '+ '), $recommendedFoods);
$avoidFoods       = array_map(fn($f) => ltrim($f, '- '), $avoidFoods);

// ============================================================
// STEP 6: DISPLAY — styled to match MedMeal site aesthetic
// ============================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedMeal – Your Meal Plan</title>
    <link rel="icon" href="images/titleLogo.png" type="image/png" sizes="19x19">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Noto+Sans+Sinhala&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        /* ── Result page specific styles ── */
        :root {
            --green:      #2d9e6b;
            --green-light:#e8f7f1;
            --green-mid:  #c3ebd9;
            --red-soft:   #fff0f0;
            --red-accent: #e05252;
            --gold:       #f5a623;
            --gold-light: #fff8ec;
            --blue-soft:  #eef4ff;
            --blue-accent:#4a7ee8;
            --text-dark:  #1a2e25;
            --text-mid:   #4a6358;
            --text-light: #8aa99a;
            --card-bg:    #ffffff;
            --page-bg:    #f4faf7;
            --radius:     16px;
            --shadow:     0 4px 24px rgba(45,158,107,0.10);
            --shadow-hover: 0 8px 32px rgba(45,158,107,0.18);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--page-bg);
            font-family: 'Nunito', 'Noto Sans Sinhala', sans-serif;
            color: var(--text-dark);
            min-height: 100vh;
        }

        /* ── NAV (same as Front_End) ── */
        .login-btn {
            position: fixed;
            top: 16px; right: 24px;
            z-index: 100;
        }
        .user-name {
            background: var(--green);
            color: #fff;
            padding: 8px 18px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* ── HERO HEADER ── */
        .result-hero {
            background: linear-gradient(135deg, #1a5c3a 0%, #2d9e6b 60%, #5ecba1 100%);
            padding: 56px 24px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .result-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .result-hero .logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        .result-hero .logo-row img { height: 40px; }
        .result-hero h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
        }
        .result-hero h1 span { opacity: 0.85; }
        .result-hero p {
            color: rgba(255,255,255,0.82);
            font-size: 1rem;
            margin-top: 6px;
        }

        /* ── PATIENT BADGE ── */
        .patient-badge {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin: -20px auto 0;
            max-width: 860px;
            padding: 0 20px;
            position: relative;
            z-index: 2;
        }
        .badge-chip {
            background: #fff;
            border-radius: 999px;
            padding: 8px 18px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--green);
            box-shadow: 0 2px 12px rgba(45,158,107,0.15);
            border: 2px solid var(--green-mid);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .badge-chip .chip-icon { font-size: 1rem; }

        /* ── MAIN CONTENT WRAPPER ── */
        .result-wrap {
            max-width: 960px;
            margin: 36px auto 60px;
            padding: 0 20px;
        }

        /* ── SECTION TITLE ── */
        .section-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 40px 0 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title::after {
            content: '';
            flex: 1;
            height: 2px;
            background: var(--green-mid);
            border-radius: 2px;
        }

        /* ── MEAL TIMELINE ── */
        .meal-timeline {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
        }
        .meal-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 22px 18px 20px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            border-top: 4px solid var(--green);
            transition: transform 0.2s, box-shadow 0.2s;
            animation: fadeUp 0.5s ease both;
        }
        .meal-card:nth-child(1) { animation-delay: 0.05s; border-color: #f5a623; }
        .meal-card:nth-child(2) { animation-delay: 0.12s; border-color: #2d9e6b; }
        .meal-card:nth-child(3) { animation-delay: 0.19s; border-color: #e05252; }
        .meal-card:nth-child(4) { animation-delay: 0.26s; border-color: #4a7ee8; }
        .meal-card:nth-child(5) { animation-delay: 0.33s; border-color: #9b59b6; }
        .meal-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }
        .meal-icon {
            font-size: 2rem;
            line-height: 1;
        }
        .meal-label {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-light);
        }
        .meal-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.4;
        }

        /* ── TWO-COLUMN FOOD LISTS ── */
        .food-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 600px) {
            .food-columns { grid-template-columns: 1fr; }
            .meal-timeline { grid-template-columns: 1fr 1fr; }
        }

        .food-panel {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 24px 22px;
            animation: fadeUp 0.5s ease 0.3s both;
        }
        .food-panel.recommended { border-left: 5px solid var(--green); }
        .food-panel.avoid       { border-left: 5px solid var(--red-accent); }

        .panel-heading {
            font-size: 1rem;
            font-weight: 800;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .recommended .panel-heading { color: var(--green); }
        .avoid       .panel-heading { color: var(--red-accent); }

        .food-panel ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 9px;
        }
        .food-panel ul li {
            font-size: 0.88rem;
            color: var(--text-mid);
            padding-left: 20px;
            position: relative;
            line-height: 1.45;
        }
        .recommended ul li::before {
            content: '✓';
            position: absolute; left: 0;
            color: var(--green);
            font-weight: 800;
        }
        .avoid ul li::before {
            content: '✕';
            position: absolute; left: 0;
            color: var(--red-accent);
            font-weight: 800;
        }

        /* ── NUTRITION PANEL ── */
        .nutrition-panel {
            background: var(--blue-soft);
            border-radius: var(--radius);
            padding: 24px 26px;
            box-shadow: var(--shadow);
            border-left: 5px solid var(--blue-accent);
            animation: fadeUp 0.5s ease 0.4s both;
        }
        .nutrition-panel .panel-heading { color: var(--blue-accent); }
        .nutrition-row {
            margin-bottom: 14px;
        }
        .nutrition-row:last-child { margin-bottom: 0; }
        .nutrition-disease-label {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--blue-accent);
            margin-bottom: 5px;
        }
        .nutrition-values {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .nutr-chip {
            background: #fff;
            border: 1.5px solid #c5d8f8;
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--blue-accent);
        }

        /* ── TIP BOX ── */
        .tip-box {
            background: var(--gold-light);
            border-radius: var(--radius);
            padding: 24px 26px;
            border-left: 5px solid var(--gold);
            box-shadow: var(--shadow);
            animation: fadeUp 0.5s ease 0.5s both;
        }
        .tip-box .panel-heading { color: #b07d10; }
        .tip-box p {
            font-size: 0.95rem;
            color: #7a5c10;
            line-height: 1.65;
            font-weight: 600;
        }

        /* ── DISCLAIMER ── */
        .disclaimer {
            background: #fff3f3;
            border: 1.5px solid #fdd;
            border-radius: var(--radius);
            padding: 16px 22px;
            font-size: 0.82rem;
            color: #a04040;
            font-weight: 600;
            text-align: center;
            margin-top: 32px;
            animation: fadeUp 0.5s ease 0.6s both;
        }

        /* ── ERROR BOX ── */
        .error-box {
            background: #fff5f5;
            border: 2px solid var(--red-accent);
            border-radius: var(--radius);
            padding: 32px 28px;
            text-align: center;
            margin: 40px auto;
            max-width: 560px;
        }
        .error-box .error-icon { font-size: 2.5rem; margin-bottom: 12px; }
        .error-box h2 { color: var(--red-accent); font-size: 1.2rem; margin-bottom: 10px; }
        .error-box p  { color: var(--text-mid); font-size: 0.9rem; line-height: 1.6; }

        /* ── BACK BUTTON ── */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--green);
            color: #fff;
            font-weight: 800;
            font-size: 0.95rem;
            padding: 13px 28px;
            border-radius: 999px;
            text-decoration: none;
            margin-top: 32px;
            transition: background 0.2s, transform 0.15s;
            box-shadow: 0 4px 16px rgba(45,158,107,0.25);
        }
        .back-btn:hover {
            background: #228a57;
            transform: translateY(-2px);
        }

        /* ── FOOTER ── */
        footer {
            text-align: center;
            padding: 20px;
            font-size: 0.82rem;
            color: var(--text-light);
            border-top: 1px solid var(--green-mid);
            background: #fff;
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── DEBUG (remove in production) ── */
        .debug-box {
            background: #1a1a1a;
            color: #00ff88;
            font-family: monospace;
            font-size: 0.75rem;
            padding: 16px;
            border-radius: 8px;
            margin-top: 24px;
            white-space: pre-wrap;
            display: none; /* set to block to debug */
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav class="login-btn">
    <?php if (isset($_SESSION['user_name'])): ?>
        <p class="user-name">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></p>
    <?php elseif (isset($_SESSION['user_email'])): ?>
        <p class="user-name">👤 <?= htmlspecialchars(substr($_SESSION['user_email'], 0, 6)) ?></p>
    <?php endif; ?>
</nav>

<!-- HERO -->
<div class="result-hero">
    <div class="logo-row">
        <img src="images/logo.png" alt="MedMeal Logo">
        <h1><span class="medi">Medi</span><span style="opacity:0.85">ආහාර</span></h1>
    </div>
    <p>Your personalized Sri Lankan meal plan is ready</p>
</div>

<?php if (isset($showError) || $runError): ?>
<!-- ── ERROR STATE ── -->
<div class="result-wrap">
    <div class="error-box">
        <div class="error-icon">⚠️</div>
        <h2>Could Not Generate Meal Plan</h2>
        <p>
            <?php if (isset($showError)): ?>
                <?= htmlspecialchars($errorMessage) ?>
            <?php else: ?>
                The AI system could not generate a recommendation. Please check that SWI-Prolog is installed and the path in <code>recommend.php</code> is correct.<br><br>
                <strong>Command tried:</strong><br>
                <code style="font-size:0.75rem;word-break:break-all"><?= htmlspecialchars($command) ?></code>
            <?php endif; ?>
        </p>
    </div>
    <div style="text-align:center">
        <a href="Front_End.php" class="back-btn">← Try Again</a>
    </div>
</div>

<?php else: ?>
<!-- ── SUCCESS STATE ── -->

<!-- PATIENT INFO BADGES -->
<div class="patient-badge">
    <div class="badge-chip">
        <span class="chip-icon">🩺</span>
        <?= htmlspecialchars(implode(' + ', array_map('ucfirst', $prologDiseases))) ?>
    </div>
    <div class="badge-chip">
        <span class="chip-icon">👤</span>
        <?= htmlspecialchars(ucfirst($prologAge)) ?>
    </div>
    <div class="badge-chip">
        <span class="chip-icon">🥗</span>
        <?= $prologPref === 'veg' ? 'Vegetarian' : 'Non-Vegetarian' ?>
    </div>
    <?php if (!empty($prologAllergies)): ?>
    <div class="badge-chip">
        <span class="chip-icon">⚠️</span>
        Allergy: <?= htmlspecialchars(implode(', ', $prologAllergies)) ?>
    </div>
    <?php endif; ?>
</div>

<div class="result-wrap">

    <!-- ── MEAL PLAN ── -->
    <div class="section-title">🍽️ Today's Meal Plan</div>
    <div class="meal-timeline">

        <?php
        $meals = [
            ['icon'=>'🌅', 'label'=>'Breakfast',     'value'=>$breakfast],
            ['icon'=>'☀️', 'label'=>'Lunch',          'value'=>$lunch],
            ['icon'=>'🍎', 'label'=>'Evening Snack',  'value'=>$snack],
            ['icon'=>'🌙', 'label'=>'Dinner',         'value'=>$dinner],
            ['icon'=>'🍵', 'label'=>'Beverage',       'value'=>$beverage],
        ];
        foreach ($meals as $meal):
            if (empty($meal['value'])) continue;
        ?>
        <div class="meal-card">
            <div class="meal-icon"><?= $meal['icon'] ?></div>
            <div class="meal-label"><?= $meal['label'] ?></div>
            <div class="meal-name"><?= htmlspecialchars($meal['value']) ?></div>
        </div>
        <?php endforeach; ?>

    </div>

    <!-- ── FOOD LISTS ── -->
    <?php if (!empty($recommendedFoods) || !empty($avoidFoods)): ?>
    <div class="section-title">🥦 Food Guide</div>
    <div class="food-columns">

        <?php if (!empty($recommendedFoods)): ?>
        <div class="food-panel recommended">
            <div class="panel-heading">✅ Highly Recommended</div>
            <ul>
                <?php foreach ($recommendedFoods as $f): ?>
                    <li><?= htmlspecialchars($f) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (!empty($avoidFoods)): ?>
        <div class="food-panel avoid">
            <div class="panel-heading">❌ Foods to Avoid</div>
            <ul>
                <?php foreach ($avoidFoods as $f): ?>
                    <li><?= htmlspecialchars($f) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

    </div>
    <?php endif; ?>

    <!-- ── NUTRITION TARGETS ── -->
    <?php if (!empty($nutritionLines)): ?>
    <div class="section-title">📊 Nutrition Targets</div>
    <div class="nutrition-panel">
        <div class="panel-heading">📊 Daily Nutrition Goals</div>
        <?php foreach ($nutritionLines as $line):
            // line format: "  [diabetes]: Calories: 1600-2000..."
            preg_match('/\[(\w+)\]:\s*(.+)/', $line, $parts);
            $disease = $parts[1] ?? '';
            $info    = $parts[2] ?? $line;
            // Split by | into chips
            $chips = array_map('trim', explode('|', $info));
        ?>
        <div class="nutrition-row">
            <?php if ($disease): ?>
            <div class="nutrition-disease-label"><?= htmlspecialchars(ucfirst($disease)) ?></div>
            <?php endif; ?>
            <div class="nutrition-values">
                <?php foreach ($chips as $chip): ?>
                    <span class="nutr-chip"><?= htmlspecialchars($chip) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- ── HEALTH TIP ── -->
    <?php if (!empty($tip)): ?>
    <div class="section-title">💡 Health Tip</div>
    <div class="tip-box">
        <div class="panel-heading">💡 Expert Advice</div>
        <p><?= htmlspecialchars($tip) ?></p>
    </div>
    <?php endif; ?>

    <!-- ── DISCLAIMER ── -->
    <div class="disclaimer">
        ⚠️ <strong>Disclaimer:</strong> This is an educational tool only.
        Always consult your doctor or registered nutritionist before making any dietary changes.
    </div>

    <!-- ── BACK BUTTON ── -->
    <div style="text-align:center">
        <a href="Front_End.php" class="back-btn">← Get Another Recommendation</a>
    </div>

    <!-- DEBUG BOX — set display:block in CSS to see raw Prolog output -->
    <div class="debug-box"><?= htmlspecialchars($output ?? '') ?></div>

</div>
<?php endif; ?>

<footer>
    <p>&copy; 2026 MedMeal. All rights reserved.</p>
</footer>

</body>
</html>