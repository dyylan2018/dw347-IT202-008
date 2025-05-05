<?php
session_start();
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../lib/functions.php");
require_once(__DIR__ . "/../../partials/nav.php");


$pdo = getDB();
$userId = get_user_id(); // From session

// Fetch associated players for this user
$stmt = $pdo->prepare("
    SELECT p.id, p.display_name, p.position, p.age, p.team_id
    FROM Players p
    INNER JOIN UserPlayerAssociations upa ON p.id = upa.player_id
    WHERE upa.user_id = :uid
");
$stmt->execute([":uid" => $userId]);
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container" style="max-width: 900px; margin: 40px auto; font-family: Arial;">
    <h2 style="text-align:center;">My Associated Yankees Players</h2>

    <?php if (count($players) > 0): ?>
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 10px; border: 1px solid #ccc;">Name</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">Position</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">Age</th>
                    <th style="padding: 10px; border: 1px solid #ccc;">Team ID</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($players as $player): ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ccc;"><?= htmlspecialchars($player['display_name']) ?></td>
                        <td style="padding: 10px; border: 1px solid #ccc;"><?= htmlspecialchars($player['position']) ?></td>
                        <td style="padding: 10px; border: 1px solid #ccc;"><?= htmlspecialchars($player['age']) ?></td>
                        <td style="padding: 10px; border: 1px solid #ccc;"><?= htmlspecialchars($player['team_id']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center; margin-top: 20px;">You have no players associated with your account yet.</p>
    <?php endif; ?>
</div>