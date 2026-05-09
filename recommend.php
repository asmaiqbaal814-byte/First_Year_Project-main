<?php
header('Content-Type: application/json');

function map_disease(string $raw): string {
    $s = strtolower(trim($raw));
    if (str_starts_with($s, 'diabetes'))            return 'diabetes';
    if (str_starts_with($s, 'high blood pressure')) return 'hypertension';
    if (str_starts_with($s, 'heart disease'))       return 'heart_disease';
    if (str_starts_with($s, 'kidney disease'))      return 'ckd';
    if (str_starts_with($s, 'cholesterol'))         return 'cholesterol';
    return '';
}

$body        = json_decode(file_get_contents('php://input'), true);
$raw_diseases = $body['diseases']   ?? [];
$raw_age      = $body['age_group']  ?? 'adult';
$raw_pref     = $body['preference'] ?? 'Vegetarian';
$raw_allergy  = $body['allergy']    ?? '';

$age_map = [
    'child'=>'child','teen'=>'young','adult'=>'adult','senior'=>'elderly',
];
$pref_map = [
    'vegetarian'=>'veg','non-vegetarian'=>'nonveg',
    'veg'=>'veg','nonveg'=>'nonveg',
];
$allergy_map = [
    'fish'=>'fish','eggs'=>'egg','milk'=>'milk',
    'peanuts'=>'peanut','soy'=>'soy',
    'sesame'=>'sesame','wheat'=>'wheat','tree_nuts'=>'tree_nuts',
];

$disease_atoms  = [];
$disease_labels = [];
$skipped        = [];

foreach ($raw_diseases as $d) {
    $atom = map_disease($d);
    if ($atom !== '' && !in_array($atom, $disease_atoms)) {
        $disease_atoms[]  = $atom;
        $clean = preg_replace('/\s+[\x{0D80}-\x{0DFF}\x{0B80}-\x{0BFF}].*/u', '', $d);
        $disease_labels[] = trim($clean) ?: $d;
    } elseif ($atom === '') {
        $skipped[] = $d;
    }
}

if (empty($disease_atoms)) {
    echo json_encode([
        'success' => false,
        'skipped' => $skipped,
        'message' => 'None of the selected conditions are currently supported. Supported: Diabetes, High Blood Pressure, Heart Disease, Kidney Disease, High Cholesterol.',
    ]);
    exit;
}

$age_atom     = $age_map[strtolower($raw_age)]     ?? 'adult';
$pref_atom    = $pref_map[strtolower($raw_pref)]   ?? 'veg';
$allergy_atom = $allergy_map[strtolower($raw_allergy)] ?? '';

$pl_diseases  = '[' . implode(',', $disease_atoms) . ']';
$pl_allergies = $allergy_atom !== '' ? "[$allergy_atom]" : '[]';
$pl_query     = "recommend($pl_diseases,$age_atom,$pref_atom,$pl_allergies)";

$pl_file = __DIR__ . '/foodie.pl';
$command = '"C:\\Program Files\\swipl\\bin\\swipl.exe" -q -s '
         . escapeshellarg($pl_file)
         . ' -g "' . $pl_query . ',halt." 2>&1';

$output = shell_exec($command) ?? '';

if (empty(trim($output))) {
    echo json_encode([
        'success' => false,
        'message' => 'Prolog returned no output. Check swipl path in recommend.php.',
    ]);
    exit;
}

function extract_line(string $label, string $text): string {
    if (preg_match('/^' . preg_quote($label, '/') . '\s*:\s*(.+)$/m', $text, $m))
        return trim($m[1]);
    return '';
}

function extract_block(string $header, string $text): array {
    $items = [];
    if (preg_match('/' . preg_quote($header, '/') . '\s*\n(.*?)(?===|\z)/s', $text, $m))
        foreach (explode("\n", trim($m[1])) as $line) {
            $line = trim($line);
            if ($line !== '') $items[] = ltrim($line, '+-* ');
        }
    return $items;
}

function parse_nutrition(array $lines): array {
    $result = [];
    foreach ($lines as $line) {
        preg_match('/\[(\w+)\]:\s*(.+)/', $line, $p);
        $disease = isset($p[1]) ? ucfirst($p[1]) : '';
        $info    = $p[2] ?? $line;
        $chips   = array_values(array_filter(array_map('trim', explode('|', $info))));
        $parsed  = [];
        foreach ($chips as $chip) {
            $pos = strpos($chip, ':');
            $parsed[] = [
                'label' => $pos !== false ? trim(substr($chip, 0, $pos))  : '',
                'value' => $pos !== false ? trim(substr($chip, $pos + 1)) : $chip,
            ];
        }
        $result[] = ['disease' => $disease, 'chips' => $parsed];
    }
    return $result;
}

$tip_raw = extract_block('=== HEALTH TIP ===', $output);

echo json_encode([
    'success'     => true,
    'skipped'     => $skipped,
    'labels'      => $disease_labels,
    'age'         => $age_atom,
    'preference'  => $pref_atom,
    'allergy'     => $raw_allergy ?: '',
    'meals' => [
        'breakfast' => extract_line('BREAKFAST',     $output),
        'lunch'     => extract_line('LUNCH',         $output),
        'snack'     => extract_line('EVENING SNACK', $output),
        'dinner'    => extract_line('DINNER',        $output),
        'beverage'  => extract_line('BEVERAGE',      $output),
    ],
    'recommended' => extract_block('=== HIGHLY RECOMMENDED FOODS ===', $output),
    'avoid'       => extract_block('=== FOODS TO AVOID ===',           $output),
    'nutrition'   => parse_nutrition(extract_block('=== NUTRITION TARGETS ===', $output)),
    'tip'         => implode(' ', $tip_raw),
]);