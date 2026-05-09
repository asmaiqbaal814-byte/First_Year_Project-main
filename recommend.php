<?php
session_start();

// ============================================================
// 1.  INPUT SANITISATION
// ============================================================
$raw_diseases  = $_POST['diseases']   ?? '';
$raw_age       = $_POST['age_group']  ?? 'adult';
$raw_pref      = $_POST['preference'] ?? '';
$raw_allergy   = $_POST['allergies']  ?? '';

$disease_map = [
    'diabetes'             => 'diabetes',
    'high blood pressure'  => 'hypertension',
    'heart disease'        => 'heart_disease',
    'kidney disease'       => 'ckd',
    'kidney disease (ckd)' => 'ckd',
    'cholesterol'          => 'cholesterol',
    'high cholesterol'     => 'cholesterol',
];

$age_map = [
    'child'  => 'child',
    'teen'   => 'young',
    'adult'  => 'adult',
    'senior' => 'elderly',
];

$pref_map = [
    'vegetarian'     => 'veg',
    'non-vegetarian' => 'nonveg',
];

$allergy_map = [
    'seafood' => 'fish',
    'eggs'    => 'egg',
    'milk'    => 'milk',
    'peanuts' => 'peanut',
    'soy'     => 'soy',
    'coconut' => '',
    'spices'  => '',
    ''        => '',
];

// Map diseases — uses str_starts_with to handle Sinhala/Tamil suffix
function map_disease(string $raw): string {
    $s = strtolower(trim($raw));
    if (str_starts_with($s, 'diabetes'))           return 'diabetes';
    if (str_starts_with($s, 'high blood pressure')) return 'hypertension';
    if (str_starts_with($s, 'heart disease'))       return 'heart_disease';
    if (str_starts_with($s, 'kidney disease'))      return 'ckd';
    if (str_starts_with($s, 'high cholesterol'))    return 'cholesterol';
    if (str_starts_with($s, 'cholesterol'))         return 'cholesterol';
    return '';
}

$disease_labels = [];
$disease_atoms  = [];
foreach (array_filter(array_map('trim', explode(',', $raw_diseases))) as $d) {
    $atom = map_disease($d);
    if ($atom !== '' && !in_array($atom, $disease_atoms)) {
        $disease_atoms[]  = $atom;
        $english_part     = preg_replace('/\s+[\x{0D80}-\x{0DFF}\x{0B80}-\x{0BFF}].*/u', '', $d);
        $disease_labels[] = trim($english_part) ?: $d;
    }
}

$age_atom     = $age_map[$raw_age]                     ?? 'adult';
$pref_atom    = $pref_map[strtolower($raw_pref)]       ?? 'veg';
$allergy_atom = $allergy_map[strtolower($raw_allergy)] ?? '';

$pl_diseases  = empty($disease_atoms) ? '[]' : '[' . implode(',', $disease_atoms) . ']';
$pl_allergies = ($allergy_atom !== '') ? "[$allergy_atom]" : '[]';

// ============================================================
// 2.  RUN PROLOG
// ============================================================
$pl_file  = __DIR__ . '/foodie.pl';
$pl_query = "recommend($pl_diseases,$age_atom,$pref_atom,$pl_allergies)";
$command  = '"C:\\Program Files\\swipl\\bin\\swipl.exe" -q -s ' .
            escapeshellarg($pl_file) .
            ' -g "' . $pl_query . ',halt." 2>&1';
$output   = shell_exec($command);

// ============================================================
// 3.  PARSE OUTPUT
// ============================================================
function extract_line(string $label, string $text): string {
    if (preg_match('/^' . preg_quote($label, '/') . '\s*:\s*(.+)$/m', $text, $m))
        return trim($m[1]);
    return '';
}

function extract_block(string $header, string $text): array {
    $items = [];
    if (preg_match('/' . preg_quote($header, '/') . '\s*\n(.*?)(?===|\z)/s', $text, $m)) {
        foreach (explode("\n", trim($m[1])) as $line) {
            $line = trim($line);
            if ($line !== '') $items[] = ltrim($line, '+-* ');
        }
    }
    return $items;
}

$breakfast   = extract_line('BREAKFAST',     $output);
$lunch       = extract_line('LUNCH',         $output);
$snack       = extract_line('EVENING SNACK', $output);
$dinner      = extract_line('DINNER',        $output);
$beverage    = extract_line('BEVERAGE',      $output);
$tip         = extract_block('=== HEALTH TIP ===',               $output);
$avoid       = extract_block('=== FOODS TO AVOID ===',           $output);
$recommended = extract_block('=== HIGHLY RECOMMENDED FOODS ===', $output);
$nutrition   = extract_block('=== NUTRITION TARGETS ===',        $output);

$no_result = ($breakfast === '' && $lunch === '');

$age_display_map  = ['child'=>'Child','teen'=>'Teenager','adult'=>'Adult','senior'=>'Senior'];
$pref_display_map = ['veg'=>'Vegetarian','nonveg'=>'Non-Vegetarian'];
$age_display     = $age_display_map[$raw_age]    ?? ucfirst($raw_age);
$pref_display    = $pref_display_map[$pref_atom] ?? ucfirst($pref_atom);
$allergy_display = $raw_allergy !== '' ? ucfirst($raw_allergy) : 'None';
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
        /* ── wrapper ── */
        .results-wrapper {
            max-width: 900px;
            margin: 24px auto 40px;
            padding: 0 16px;
            animation: fadeUp 0.5s ease both;
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(24px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* ── back button ── */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 2px solid #019C78;
            color: #019C78;
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 8px 20px;
            border-radius: 999px;
            cursor: pointer;
            text-decoration: none;
            margin-bottom: 22px;
            transition: all 0.2s;
            box-shadow: none;
            width: auto;
        }
        .back-btn:hover { background:#019C78; color:#fff; transform:none; }

        /* ── profile chips ── */
        .profile-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 28px;
            align-items: center;
        }
        .profile-chip {
            background: #e8fdf5;
            border: 1.5px solid #a8e8cf;
            color: #1a6644;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .profile-chip .chip-label { color:#019C78; font-weight:800; }

        /* ── section heading ── */
        .section-heading {
            font-size: 1rem;
            font-weight: 800;
            color: #019C78;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 0 0 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-heading::after {
            content:'';
            flex:1;
            height:2px;
            background: linear-gradient(90deg,#a8e8cf 0%,transparent 100%);
            border-radius:2px;
        }

        /* ════════════════════════════════
           MEAL TIMELINE
        ════════════════════════════════ */
        .meal-timeline {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 0;
            margin-bottom: 36px;
        }
        .meal-timeline::before {
            content: '';
            position: absolute;
            left: 38px;
            top: 28px;
            bottom: 28px;
            width: 2px;
            background: linear-gradient(180deg, #a8e8cf 0%, #019C78 50%, #a8e8cf 100%);
            border-radius: 2px;
            z-index: 0;
        }
        .meal-row {
            display: flex;
            align-items: flex-start;
            gap: 0;
            position: relative;
            z-index: 1;
            padding: 10px 0;
            animation: slideIn 0.45s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        .meal-row:nth-child(1){ animation-delay:0.05s; }
        .meal-row:nth-child(2){ animation-delay:0.12s; }
        .meal-row:nth-child(3){ animation-delay:0.19s; }
        .meal-row:nth-child(4){ animation-delay:0.26s; }
        .meal-row:nth-child(5){ animation-delay:0.33s; }
        @keyframes slideIn {
            from { opacity:0; transform:translateX(-18px); }
            to   { opacity:1; transform:translateX(0); }
        }
        .meal-node {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.55rem;
            flex-shrink: 0;
            border: 3px solid #fff;
            box-shadow: 0 4px 16px rgba(1,156,120,0.18);
            position: relative;
            z-index: 2;
            margin-left: 11px;
        }
        .meal-row:nth-child(1) .meal-node { background:#fff8e8; }
        .meal-row:nth-child(2) .meal-node { background:#e8fdf5; }
        .meal-row:nth-child(3) .meal-node { background:#fdecea; }
        .meal-row:nth-child(4) .meal-node { background:#e8f0fd; }
        .meal-row:nth-child(5) .meal-node { background:#f3e8fd; }
        .meal-bubble {
            flex: 1;
            background: #fff;
            border-radius: 18px;
            padding: 14px 20px;
            margin-left: 16px;
            box-shadow: 0 3px 16px rgba(0,0,0,0.07);
            border-left: 4px solid #019C78;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .meal-row:nth-child(1) .meal-bubble { border-color:#f5a623; }
        .meal-row:nth-child(2) .meal-bubble { border-color:#019C78; }
        .meal-row:nth-child(3) .meal-bubble { border-color:#e05252; }
        .meal-row:nth-child(4) .meal-bubble { border-color:#4a7ee8; }
        .meal-row:nth-child(5) .meal-bubble { border-color:#9b59b6; }
        .meal-bubble:hover { transform:translateX(4px); box-shadow:0 6px 24px rgba(1,156,120,0.14); }
        .meal-time-label {
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #019C78;
            margin-bottom: 4px;
        }
        .meal-row:nth-child(1) .meal-time-label { color:#c97d10; }
        .meal-row:nth-child(3) .meal-time-label { color:#c03838; }
        .meal-row:nth-child(4) .meal-time-label { color:#2c5fa8; }
        .meal-row:nth-child(5) .meal-time-label { color:#7b3fac; }
        .meal-text {
            font-size: 0.92rem;
            font-weight: 700;
            color: #1a2e25;
            line-height: 1.4;
        }
        @media (max-width:480px) {
            .meal-timeline::before { left:20px; }
            .meal-node { width:42px; height:42px; font-size:1.2rem; margin-left:0; }
            .meal-bubble { padding:11px 14px; margin-left:12px; }
            .meal-text { font-size:0.85rem; }
        }

        /* ════════════════════════════════
           FOOD GUIDE — tag mosaic cards
        ════════════════════════════════ */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 22px;
        }
        @media (max-width:600px) { .info-grid { grid-template-columns:1fr; } }

        .info-card {
            border-radius: 20px;
            padding: 22px 20px 20px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.07);
            animation: fadeUp 0.5s ease both;
            border: 1.5px solid transparent;
            position: relative;
            overflow: hidden;
        }

        /* Giant faded watermark in corner */
        .info-card::after {
            content: attr(data-watermark);
            position: absolute;
            bottom: -14px;
            right: 6px;
            font-size: 5.5rem;
            line-height: 1;
            opacity: 0.06;
            pointer-events: none;
            user-select: none;
        }

        .info-card.green-tint {
            background: linear-gradient(145deg, #f0fdf8 0%, #ffffff 55%);
            border-color: #b8e8d0;
        }
        .info-card.red-tint {
            background: linear-gradient(145deg, #fff5f5 0%, #ffffff 55%);
            border-color: #f5c0c0;
        }

        /* Header row with icon stamp */
        .food-card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1.5px dashed #e8e8e8;
        }
        .food-card-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .green-tint .food-card-icon { background:#d4f5e8; }
        .red-tint   .food-card-icon { background:#fde0e0; }

        .food-card-title {
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .green-tint .food-card-title { color:#0a7a56; }
        .red-tint   .food-card-title { color:#c03030; }

        /* Floating tag mosaic */
        .food-tag-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .food-tag {
            display: inline-flex;
            align-items: flex-start;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            line-height: 1.35;
            animation: chipPop 0.3s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        .green-tint .food-tag {
            background: #e6faf2;
            color: #0a6644;
            border: 1px solid #a8e8c8;
        }
        .red-tint .food-tag {
            background: #fff0f0;
            color: #9a2020;
            border: 1px solid #f5b8b8;
        }
        .food-tag .tag-dot {
            margin-top: 4px;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .green-tint .tag-dot { background:#019C78; }
        .red-tint   .tag-dot { background:#e05252; }

        /* stagger tags */
        .food-tag:nth-child(1){animation-delay:0.05s}
        .food-tag:nth-child(2){animation-delay:0.10s}
        .food-tag:nth-child(3){animation-delay:0.15s}
        .food-tag:nth-child(4){animation-delay:0.20s}
        .food-tag:nth-child(5){animation-delay:0.25s}
        .food-tag:nth-child(6){animation-delay:0.30s}
        .food-tag:nth-child(7){animation-delay:0.35s}

        @keyframes chipPop {
            from { transform:scale(0.7); opacity:0; }
            to   { transform:scale(1);   opacity:1; }
        }

        /* ════════════════════════════════
           NUTRITION — pill grid
        ════════════════════════════════ */
        .nutrition-section {
            background: #fff;
            border-radius: 20px;
            padding: 24px 22px;
            margin-bottom: 22px;
            box-shadow: 0 4px 18px rgba(74,126,232,0.10);
            border-top: 4px solid #4a7ee8;
            animation: fadeUp 0.5s ease 0.2s both;
        }
        .nutrition-disease-label {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #4a7ee8;
            margin: 0 0 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .nutrition-disease-label::before {
            content:'';
            display:inline-block;
            width:8px; height:8px;
            border-radius:50%;
            background:#4a7ee8;
            flex-shrink:0;
        }
        .nutr-pill-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px,1fr));
            gap: 10px;
            margin-bottom: 18px;
        }
        .nutr-pill-grid:last-child { margin-bottom:0; }
        .nutr-pill {
            background: #eef4ff;
            border: 1.5px solid #c5d8f8;
            border-radius: 14px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 3px;
            animation: chipPop 0.35s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        .nutr-pill:nth-child(1){animation-delay:0.05s}
        .nutr-pill:nth-child(2){animation-delay:0.10s}
        .nutr-pill:nth-child(3){animation-delay:0.15s}
        .nutr-pill:nth-child(4){animation-delay:0.20s}
        .nutr-pill:nth-child(5){animation-delay:0.25s}
        .nutr-pill:nth-child(6){animation-delay:0.30s}
        .nutr-pill:nth-child(7){animation-delay:0.35s}
        .nutr-pill-label {
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #4a7ee8;
        }
        .nutr-pill-value {
            font-size: 0.88rem;
            font-weight: 700;
            color: #1a2e60;
            line-height: 1.3;
        }
        @media (max-width:480px) {
            .nutr-pill-grid { grid-template-columns:1fr 1fr; }
        }

        /* ════════════════════════════════
           TIP + ERROR
        ════════════════════════════════ */
        .tip-banner {
            background: linear-gradient(135deg,#019C78 0%,#0dbf94 100%);
            border-radius: 18px;
            padding: 22px 26px;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1.6;
            margin-bottom: 22px;
            display: flex;
            gap: 14px;
            align-items: flex-start;
            animation: fadeUp 0.5s ease 0.3s both;
        }
        .tip-banner .tip-icon { font-size:2rem; flex-shrink:0; }

        .error-card {
            background: #fff0f0;
            border: 2px solid #e05c5c;
            border-radius: 18px;
            padding: 28px 24px;
            text-align: center;
            color: #b02020;
            font-weight: 700;
            font-size: 1rem;
        }
    </style>
</head>

<body>
    <nav class="login-btn">
    <?php if (isset($_SESSION['user_name'])): ?>
        <p class="user-name">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></p>
    <?php elseif(isset($_SESSION['user_email'])): ?>
        <p class="user-name">👤 <?= htmlspecialchars(substr($_SESSION['user_email'],0,6)) ?></p>
    <?php else: ?>
        <button type="button" id="signin" onclick="window.location.href='signin.html'"></button>
    <?php endif; ?>
    </nav>

    <div class="container">
        <h1>
            <img class="logo" src="images/logo.png" alt="MedMeal Logo">
            <span class="medi">Medi</span><span class="meal">ආහාර</span>
        </h1>
        <p>Personalized Sri Lankan meal plans for better health.</p>
        <p class="sinhala">ඔබගේ සෞඛ්‍යයට ගැළපෙන ආහාර සැලසුම්</p>
    </div>

    <div class="results-wrapper">

        <a href="Front_End.php" class="back-btn">← Back to Search</a>

        <!-- Profile chips -->
        <div class="profile-bar">
            <?php foreach ($disease_labels as $dl): ?>
                <span class="profile-chip">🦠 <span class="chip-label">Condition:</span> <?= htmlspecialchars($dl) ?></span>
            <?php endforeach; ?>
            <span class="profile-chip">👤 <span class="chip-label">Age:</span> <?= htmlspecialchars($age_display) ?></span>
            <span class="profile-chip">🥗 <span class="chip-label">Diet:</span> <?= htmlspecialchars($pref_display) ?></span>
            <?php if ($allergy_display !== 'None'): ?>
                <span class="profile-chip">⚠️ <span class="chip-label">Allergy:</span> <?= htmlspecialchars($allergy_display) ?></span>
            <?php endif; ?>
        </div>

        <?php if ($no_result): ?>
        <div class="error-card">
            <div style="font-size:2.5rem;margin-bottom:12px;">😔</div>
            <p style="margin:0 0 8px;">No meal plan could be generated for this combination.</p>
            <p style="margin:0;font-size:0.85rem;font-weight:600;color:#c0392b;">
                Supported conditions: Diabetes, High Blood Pressure, Heart Disease,
                Kidney Disease, High Cholesterol. Please check your selection and try again.
            </p>
            <a href="Front_End.php" class="back-btn" style="margin-top:18px;display:inline-flex;">← Try Again</a>
        </div>

        <?php else: ?>

        <!-- ══ DAILY MEAL PLAN ══ -->
        <div class="section-heading">🍽️ Your Daily Meal Plan</div>
        <div class="meal-timeline">
            <?php
            $meals = [
                ['🌅','Breakfast',    $breakfast],
                ['☀️', 'Lunch',        $lunch],
                ['🍎','Evening Snack',$snack],
                ['🌙','Dinner',       $dinner],
                ['🍵','Beverage',     $beverage],
            ];
            foreach ($meals as [$icon,$label,$name]):
                if ($name === '') continue;
            ?>
            <div class="meal-row">
                <div class="meal-node"><?= $icon ?></div>
                <div class="meal-bubble">
                    <div class="meal-time-label"><?= $label ?></div>
                    <div class="meal-text"><?= htmlspecialchars($name) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- ══ FOOD GUIDE — tag mosaic ══ -->
        <?php if (!empty($recommended) || !empty($avoid)): ?>
        <div class="section-heading">🥦 Food Guide</div>
        <div class="info-grid">

            <?php if (!empty($recommended)): ?>
            <div class="info-card green-tint" data-watermark="✓" style="animation-delay:0.1s">
                <div class="food-card-header">
                    <div class="food-card-icon">✅</div>
                    <span class="food-card-title">Highly Recommended</span>
                </div>
                <div class="food-tag-wrap">
                    <?php foreach ($recommended as $r): ?>
                        <span class="food-tag">
                            <span class="tag-dot"></span>
                            <?= htmlspecialchars($r) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($avoid)): ?>
            <div class="info-card red-tint" data-watermark="✕" style="animation-delay:0.15s">
                <div class="food-card-header">
                    <div class="food-card-icon">🚫</div>
                    <span class="food-card-title">Foods to Avoid</span>
                </div>
                <div class="food-tag-wrap">
                    <?php foreach ($avoid as $a): ?>
                        <span class="food-tag">
                            <span class="tag-dot"></span>
                            <?= htmlspecialchars($a) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
        <?php endif; ?>

        <!-- ══ NUTRITION TARGETS ══ -->
        <?php if (!empty($nutrition)): ?>
        <div class="section-heading">📊 Nutrition Targets</div>
        <div class="nutrition-section">
            <?php foreach ($nutrition as $line):
                preg_match('/\[(\w+)\]:\s*(.+)/', $line, $parts);
                $dis_name = isset($parts[1]) ? ucfirst($parts[1]) : '';
                $info_str = $parts[2] ?? $line;
                $pills    = array_filter(array_map('trim', explode('|', $info_str)));
            ?>
            <?php if ($dis_name): ?>
                <div class="nutrition-disease-label"><?= htmlspecialchars($dis_name) ?></div>
            <?php endif; ?>
            <div class="nutr-pill-grid">
                <?php foreach ($pills as $pill):
                    $colon = strpos($pill, ':');
                    if ($colon !== false) {
                        $pill_label = trim(substr($pill, 0, $colon));
                        $pill_value = trim(substr($pill, $colon + 1));
                    } else {
                        $pill_label = '';
                        $pill_value = $pill;
                    }
                ?>
                <div class="nutr-pill">
                    <?php if ($pill_label): ?>
                        <span class="nutr-pill-label"><?= htmlspecialchars($pill_label) ?></span>
                    <?php endif; ?>
                    <span class="nutr-pill-value"><?= htmlspecialchars($pill_value) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- ══ HEALTH TIP ══ -->
        <?php if (!empty($tip)): ?>
        <div class="tip-banner">
            <span class="tip-icon">💡</span>
            <div>
                <strong style="display:block;margin-bottom:4px;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.1em;opacity:0.85">Health Tip</strong>
                <?= htmlspecialchars(implode(' ', $tip)) ?>
            </div>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2026 MedMeal. All rights reserved.</p>
    </footer>
</body>
</html>