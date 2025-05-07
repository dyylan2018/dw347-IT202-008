<?php
session_start();
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true);

$db = getDB();
$player = null;

$redirect = $_GET['redirect'] ?? 'my_players.php'; // fallback if no redirect is passed

if (isset($_GET["id"])) {
    $id = (int)$_GET["id"];
    $stmt = $db->prepare("SELECT * FROM Players WHERE id = :id");
    $stmt->execute([":id" => $id]);
    $player = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<style>
.container {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.player-card {
    background-color: #f9f9f9;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
    text-align: center;
}

h2 {
    margin-bottom: 25px;
}

.player-detail {
    text-align: left;
    margin-top: 10px;
}

.player-detail p {
    font-size: 16px;
    margin: 8px 0;
}

button {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    margin-top: 20px;
}

button:hover {
    background-color: #0056b3;
}
</style>

<div class="container">
    <?php if ($player): ?>
        <div class="player-card">
            <h2><?= htmlspecialchars($player["first_name"] . " " . $player["last_name"]) ?></h2>
            <div class="player-detail">
                <p><strong>Position:</strong> <?= htmlspecialchars($player["position"]) ?></p>
                <p><strong>Age:</strong> <?= htmlspecialchars($player["age"]) ?></p>
                <p><strong>Date of Birth:</strong> <?= htmlspecialchars($player["date_of_birth"]) ?></p>
                <p><strong>Height:</strong> <?= htmlspecialchars($player["height"]) ?> in</p>
                <p><strong>Weight:</strong> <?= htmlspecialchars($player["weight"]) ?> lbs</p>
                <p><strong>Jersey #:</strong> <?= htmlspecialchars($player["jersey"]) ?></p>
                <p><strong>Slug:</strong> <?= htmlspecialchars($player["slug"]) ?></p>
                <p><strong>Team ID:</strong> <?= htmlspecialchars($player["team_id"]) ?></p>
                <p><strong>Sport ID:</strong> <?= htmlspecialchars($player["sport_id"]) ?></p>
                <p><strong>Last Updated:</strong> <?= htmlspecialchars($player["updated_at"]) ?></p>
            </div>
            <a href="<?= get_url(htmlspecialchars($redirect)) ?>">
                <button>Back</button>
            </a>
        </div>
    <?php else: ?>
        <p>Player not found.</p>
    <?php endif; ?>
</div>