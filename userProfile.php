<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    header('Location: signin.html');
    exit();
}

$recommendedFoods = $_SESSION['user_recommendations'] ?? [];
$recommendationHistory = $_SESSION['recommendation_history'] ?? [];
$userEmail = $_SESSION['user_email'] ?? '';
$totalSearches = count($recommendationHistory);
$memberSince = $_SESSION['profile_created'] ?? 'Today';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - MedMeal</title>
    <link rel="icon" href="images/titleLogo.png" type="image/png" sizes="19x19" >
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f2fbf7; color: #1a2e25; font-family: 'Nunito', sans-serif; margin: 0; }
        .profile-container { max-width: 960px; margin: 40px auto; padding: 24px; background: #ffffff; border-radius: 28px; box-shadow: 0 24px 80px rgba(22, 75, 62, 0.08); }
        .profile-header { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 20px; align-items: center; }
        .profile-user { display: flex; align-items: center; gap: 18px; }
        .profile-avatar { width: 72px; height: 72px; border-radius: 20px; display: grid; place-items: center; background: #019c78; color: #fff; font-size: 2rem; font-weight: 800; }
        .profile-user h1 { margin: 0; font-size: 2rem; color: #0f4f38; }
        .profile-summary { margin: 6px 0 0; font-size: 1rem; color: #51655b; }
        .profile-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 4px; }
        .profile-stats-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin: 24px 0; }
        .stat-card { padding: 22px; border-radius: 20px; background: #eef8f3; border: 1px solid #dcefe6; }
        .stat-card--accent { background: #eafdf5; border-color: #b7ead5; }
        .stat-label { margin: 0 0 10px; font-size: 0.95rem; color: #617a6e; }
        .stat-value { margin: 0; font-size: 1.75rem; font-weight: 800; color: #0f4f38; }
        .history-card { border-radius: 22px; border: 1px solid #d9f1e3; padding: 24px; background: #f7fffb; margin: 16px 0; }
        .history-item { display: flex; flex-direction: column; gap: 16px; padding: 18px 20px; border-radius: 18px; background: #ffffff; border: 1px solid #e4f5ec; margin-bottom: 12px; width: 100%; text-align: left; }
        .history-item:last-child { margin-bottom: 0; }
        .history-button { cursor: pointer; display: flex; flex-direction: column; width: 100%; background: transparent; border: none; padding: 0; text-align: left; }
        .history-button:hover,
        .history-button:focus {
            background: #f7fffb;
            outline: none;
        }
        .history-top { display: flex; justify-content: space-between; align-items: center; gap: 16px; width: 100%; }
        .history-meta { display: flex; flex-direction: column; gap: 6px; }
        .history-date { font-size: 0.95rem; color: #4f6b5a; }
        .history-tag { display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 999px; background: #e1f6e7; color: #17603f; font-size: 0.85rem; font-weight: 700; }
        .history-toggle { color: #019c78; font-size: 0.95rem; font-weight: 700; }
        .history-details { margin-top: 14px; padding-top: 14px; border-top: 1px solid #e6f0e8; display: none; gap: 12px; }
        .history-item--expanded .history-details { display: grid; }
        .food-list { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
        .food-chip { padding: 8px 12px; border-radius: 999px; background: #e1f6e7; color: #17603f; font-size: 0.9rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 12px 18px; border-radius: 999px; border: none; cursor: pointer; font-size: 0.95rem; font-weight: 700; }
        .btn-primary { background: #019c78; color: #fff; }
        .btn-secondary { background: #f2f7f4; color: #0f4f38; border: 1px solid #d4e7df; }
        a.btn { text-decoration: none; }
        @media (max-width: 820px) { .profile-stats-grid { grid-template-columns: 1fr; } }
        @media (max-width: 600px) { .profile-header { flex-direction: column; align-items: flex-start; } }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-user">
                <div class="profile-avatar"><?php echo strtoupper(substr(htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8'), 0, 1)); ?></div>
                <div>
                    <h1><?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="profile-summary"><?php echo $userEmail ? htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') : 'No email available'; ?></p>
                </div>
            </div>
            <div class="profile-actions">
                <a href="Front_End.php" class="btn btn-secondary">Back to Home</a>
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </div>

        <div class="profile-stats-grid">
            <div class="stat-card">
                <p class="stat-label">Total Searches</p>
                <p class="stat-value"><?php echo $totalSearches; ?></p>
            </div>
            <div class="stat-card stat-card--accent">
                <p class="stat-label">Account Status</p>
                <p class="stat-value">Active</p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Member Since</p>
                <p class="stat-value"><?php echo htmlspecialchars($memberSince, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>

        

        <div class="history-card">
            <h2>Recommendation History</h2>
            <?php if (!empty($recommendationHistory)): ?>
                <?php foreach ($recommendationHistory as $index => $entry): ?>
                    <button type="button" class="history-item history-button" aria-expanded="false" data-history-index="<?php echo $index; ?>">
                        <div class="history-top">
                            <div class="history-meta">
                                <span class="history-date"><?php echo htmlspecialchars(date('M j, Y - h:i A', strtotime($entry['timestamp'])), ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="history-tag"><?php echo htmlspecialchars(implode(', ', $entry['conditions']), ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <span class="history-toggle">View recommendation</span>
                        </div>
                        <div class="history-details" id="history-details-<?php echo $index; ?>" hidden>
                            <p class="history-detail">Recommended foods for this history entry:</p>
                            <div class="food-list">
                                <?php foreach ($entry['foods'] as $food): ?>
                                    <span class="food-chip"><?php echo htmlspecialchars($food, ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </button>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    No recommendation history is available yet. Create your first health plan to populate this section.
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        document.querySelectorAll('.history-button').forEach(button => {
            button.addEventListener('click', () => {
                const details = button.querySelector('.history-details');
                const toggle = button.querySelector('.history-toggle');
                const expanded = details.hasAttribute('hidden');
                if (expanded) {
                    details.removeAttribute('hidden');
                    button.classList.add('history-item--expanded');
                    toggle.textContent = 'Hide recommendation';
                    button.setAttribute('aria-expanded', 'true');
                } else {
                    details.setAttribute('hidden', '');
                    button.classList.remove('history-item--expanded');
                    toggle.textContent = 'View recommendation';
                    button.setAttribute('aria-expanded', 'false');
                }
            });
        });
    </script>
</body>
</html>
