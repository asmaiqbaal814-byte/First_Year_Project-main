<?php
session_start();

// ============================================================
// 1.  INPUT SANITISATION
// ============================================================
$raw_diseases  = $_POST['diseases']   ?? '';
$raw_age       = $_POST['age_group']  ?? 'adult';
$raw_pref      = $_POST['preference'] ?? '';
$raw_allergy   = $_POST['allergies']  ?? '';

// Map display names → Prolog atoms
$disease_map = [
    'diabetes'           => 'diabetes',
    'high blood pressure'=> 'hypertension',
    'heart disease'      => 'heart_disease',
    'kidney disease'     => 'ckd',
    'kidney disease (ckd)' => 'ckd',
    'cholesterol'        => 'cholesterol',
    'high cholesterol'   => 'cholesterol',
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
    'coconut' => '',   // not in Prolog allergy list → ignore
    'spices'  => '',
    ''        => '',
];

// Convert diseases
$disease_labels = [];   // human-readable, for display
$disease_atoms  = [];   // Prolog atoms
foreach (array_filter(array_map('trim', explode(',', $raw_diseases))) as $d) {
    $key = strtolower($d);
    if (isset($disease_map[$key])) {
        $atom = $disease_map[$key];
        if (!in_array($atom, $disease_atoms)) {
            $disease_atoms[]  = $atom;
            $disease_labels[] = $d;
        }
    }
}

$age_atom  = $age_map[$raw_age]            ?? 'adult';
$pref_atom = $pref_map[strtolower($raw_pref)] ?? 'veg';
$allergy_atom = $allergy_map[strtolower($raw_allergy)] ?? '';

// Build Prolog list args
$pl_diseases  = '[' . implode(',', $disease_atoms) . ']';
$pl_allergies = ($allergy_atom !== '') ? "[$allergy_atom]" : '[]';

// ============================================================
// 2.  RUN PROLOG
// ============================================================


ini_set('display_errors', 1);
error_reporting(E_ALL);

$pl_file = __DIR__ . '/foodie.pl';

$pl_diseases = "[diabetes]";
$age_atom = "adult";
$pref_atom = "veg";
$pl_allergies = "[]";

$pl_query = "recommend($pl_diseases,$age_atom,$pref_atom,$pl_allergies)";

$command = '"C:\\Program Files\\swipl\\bin\\swipl.exe" -q -s ' .
           escapeshellarg($pl_file) .
           ' -g "' . $pl_query . ',halt." 2>&1';

$output = shell_exec($command);




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

$breakfast  = extract_line('BREAKFAST',     $output);
$lunch      = extract_line('LUNCH',         $output);
$snack      = extract_line('EVENING SNACK', $output);
$dinner     = extract_line('DINNER',        $output);
$beverage   = extract_line('BEVERAGE',      $output);
$tip        = extract_block('=== HEALTH TIP ===',              $output);
$avoid      = extract_block('=== FOODS TO AVOID ===',          $output);
$recommended= extract_block('=== HIGHLY RECOMMENDED FOODS ===', $output);
$nutrition  = extract_block('=== NUTRITION TARGETS ===',       $output);

$no_result = ($breakfast === '' && $lunch === '');

// Display labels
$age_display_map  = ['child'=>'Child','teen'=>'Teenager','adult'=>'Adult','senior'=>'Senior'];
$pref_display_map = ['veg'=>'Vegetarian','nonveg'=>'Non-Vegetarian'];
$age_display      = $age_display_map[$raw_age]               ?? ucfirst($raw_age);
$pref_display     = $pref_display_map[$pref_atom]            ?? ucfirst($pref_atom);
$allergy_display  = $raw_allergy !== '' ? ucfirst($raw_allergy) : 'None';
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
        /* ── Results page extras ─────────────────────────────── */

        .results-wrapper {
            max-width: 900px;
            margin: 24px auto 40px;
            padding: 0 16px;
            animation: fadeUp 0.5s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Back button */
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
            width: auto;
            box-shadow: none;
        }
        .back-btn:hover {
            background: #019C78;
            color: #fff;
            transform: none;
        }

        /* Profile chip bar */
        .profile-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 22px;
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
        .profile-chip .chip-label {
            color: #019C78;
            font-weight: 800;
        }

        /* Section header */
        .section-heading {
            font-size: 1rem;
            font-weight: 800;
            color: #019C78;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 0 0 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-heading::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, #a8e8cf 0%, transparent 100%);
            border-radius: 2px;
        }

        /* Meal timeline card */
        .meal-timeline {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }
        .meal-card {
            background: #fff;
            border-radius: 18px;
            padding: 18px 16px;
            box-shadow: 0 4px 18px rgba(1,156,120,0.10);
            border-top: 4px solid #019C78;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            animation: fadeUp 0.5s ease both;
        }
        .meal-card:nth-child(1) { animation-delay: 0.05s; }
        .meal-card:nth-child(2) { animation-delay: 0.10s; }
        .meal-card:nth-child(3) { animation-delay: 0.15s; }
        .meal-card:nth-child(4) { animation-delay: 0.20s; }
        .meal-card:nth-child(5) { animation-delay: 0.25s; }
        .meal-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 28px rgba(1,156,120,0.18);
        }
        .meal-card .meal-icon {
            font-size: 2rem;
            margin-bottom: 8px;
            display: block;
        }
        .meal-card .meal-time {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #019C78;
            margin-bottom: 6px;
        }
        .meal-card .meal-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: #2c3e50;
            line-height: 1.4;
        }

        /* Two-column info grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 22px;
        }
        @media (max-width: 600px) {
            .info-grid { grid-template-columns: 1fr; }
            .meal-timeline { grid-template-columns: 1fr 1fr; }
        }

        .info-card {
            background: #fff;
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.07);
            animation: fadeUp 0.5s ease both;
        }
        .info-card.green-tint  { border-left: 4px solid #019C78; }
        .info-card.red-tint    { border-left: 4px solid #e05c5c; }
        .info-card.blue-tint   { border-left: 4px solid #4a90d9; }
        .info-card.amber-tint  { border-left: 4px solid #f0a500; }

        .info-card ul {
            margin: 0;
            padding: 0 0 0 18px;
            list-style: none;
        }
        .info-card ul li {
            font-size: 0.88rem;
            color: #444;
            font-weight: 600;
            margin-bottom: 7px;
            line-height: 1.4;
            padding-left: 4px;
            position: relative;
        }
        .info-card ul li::before {
            content: attr(data-bullet);
            position: absolute;
            left: -18px;
        }

        /* Health tip banner */
        .tip-banner {
            background: linear-gradient(135deg, #019C78 0%, #0dbf94 100%);
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
        .tip-banner .tip-icon {
            font-size: 2rem;
            flex-shrink: 0;
        }

        
        /* Error state */
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
        <p class="user-name">👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
    <?php elseif(isset($_SESSION['user_email'])): ?>
        <p class="user-name">👤 <?php echo htmlspecialchars(substr($_SESSION['user_email'], 0, 6)); ?></p>
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

        <!-- Back button -->
        <a href="Front_End.php" class="back-btn">← Back to Search</a>

        <!-- Profile summary chips -->
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
                    This may be because the selected disease is not yet supported, or the combination
                    has no safe meals matching your allergy/diet filters. Please try a different combination.
                </p>
                <a href="Front_End.php" class="back-btn" style="margin-top:18px;display:inline-flex;">← Try Again</a>
            </div>

        <?php else: ?>

            <!-- ── DAILY MEAL PLAN ── -->
            <div class="section-heading">🍽️ Your Daily Meal Plan</div>
            <div class="meal-timeline">
                <?php
                $meals = [
                    ['🌅', 'Breakfast',     $breakfast],
                    ['☀️',  'Lunch',         $lunch],
                    ['🍎', 'Evening Snack', $snack],
                    ['🌙', 'Dinner',        $dinner],
                    ['🍵', 'Beverage',      $beverage],
                   
                ];
                foreach ($meals as [$icon, $label, $name]):
                    if ($name === '') continue;
                ?>
                    <div class="meal-card">
                        <span class="meal-icon"><?= $icon ?></span>
                        <div class="meal-time"><?= $label ?></div>
                        <div class="meal-name"><?= htmlspecialchars($name) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- ── INFO GRID ── -->
            <div class="info-grid">

                <?php if (!empty($recommended)): ?>
                <div class="info-card green-tint" style="animation-delay:0.1s">
                    <div class="section-heading" style="font-size:0.85rem">✅ Highly Recommended</div>
                    <ul>
                        <?php foreach ($recommended as $r): ?>
                            <li data-bullet="✓"><?= htmlspecialchars($r) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($avoid)): ?>
                <div class="info-card red-tint" style="animation-delay:0.15s">
                    <div class="section-heading" style="font-size:0.85rem;color:#e05c5c">🚫 Foods to Avoid</div>
                    <ul>
                        <?php foreach ($avoid as $a): ?>
                            <li data-bullet="✗" style="color:#c0392b"><?= htmlspecialchars($a) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($nutrition)): ?>
                <div class="info-card blue-tint" style="animation-delay:0.2s; grid-column: 1 / -1;">
                    <div class="section-heading" style="font-size:0.85rem;color:#4a90d9">📊 Nutrition Targets</div>
                    <ul>
                        <?php foreach ($nutrition as $n): ?>
                            <li data-bullet="📌" style="color:#2c5282"><?= htmlspecialchars($n) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div>

            <!-- ── HEALTH TIP ── -->
            <?php if (!empty($tip)): ?>
            <div class="tip-banner">
                <span class="tip-icon">💡</span>
                <div>
                    <strong style="display:block;margin-bottom:4px;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.1em;opacity:0.85">Health Tip</strong>
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