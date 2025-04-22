<?php
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../lib/db.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

$player = null;
$id = (int)($_GET["id"] ?? 0);

if ($id > 0) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Players WHERE id = :id");
    $stmt->execute([":id" => $id]);
    $player = $stmt->fetch();
}
?>

<div class="container py-5">
    <h2 class="text-center mb-4">Player Details</h2>

    <?php if ($player) : ?>
        <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
            <h4 class="mb-3 text-primary"><?php se($player, "display_name"); ?></h4>
            <ul class="list-group list-group-flush text-start">
                <li class="list-group-item"><strong>ID:</strong> <?php se($player, "id"); ?></li>
                <li class="list-group-item"><strong>Jersey:</strong> <?php se($player, "jersey"); ?></li>
                <li class="list-group-item"><strong>Position:</strong> <?php se($player, "position"); ?></li>
                <li class="list-group-item"><strong>Age:</strong> <?php se($player, "age"); ?></li>
                <li class="list-group-item"><strong>Status:</strong> <?php se($player, "status"); ?></li>
                <li class="list-group-item"><strong>Bats:</strong> <?php se($player, "bats"); ?></li>
                <li class="list-group-item"><strong>Throws:</strong> <?php se($player, "throws"); ?></li>
                <li class="list-group-item"><strong>Date of Birth:</strong> <?php se($player, "date_of_birth"); ?></li>
                <li class="list-group-item"><strong>Weight:</strong> <?php se($player, "display_weight"); ?></li>
                <li class="list-group-item"><strong>Height:</strong> <?php se($player, "display_height"); ?></li>
                <li class="list-group-item"><strong>Created:</strong> <?php se($player, "created"); ?></li>
                <li class="list-group-item"><strong>Modified:</strong> <?php se($player, "modified"); ?></li>
            </ul>

            <!-- Action Buttons -->
            <style>
            .action-btn
            {
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            </style>

            <div class="d-flex gap-2 mt-4">
                <a href="edit_player.php?id=<?= urlencode($player['id']) ?>" class="btn btn-warning w-50 action-btn">Edit</a>
                <form method="POST" action="delete_player.php" class="w-50" onsubmit="return confirm('Are you sure you want to delete this player?');">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($player['id']) ?>">
                    <button type="submit" class="btn btn-danger w-100 action-btn">Delete</button>
                </form>
            </div>
                <a href="list_players.php" class="btn btn-secondary mt-3 w-100">← Back to List</a>
            </div>
    <?php else : ?>
        <p class="text-danger text-center">Player not found.</p>
    <?php endif; ?>
</div>

<?php
require(__DIR__ . "/../../../partials/flash.php");
?>