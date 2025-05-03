<?php
require_once(__DIR__ . "/../../../lib/db.php");
require_once(__DIR__ . "/../../../lib/functions.php");
require_once(__DIR__ . "/../../../partials/nav.php");

$pdo = getDB();
$query = "SELECT id, display_name, created, modified FROM Players ORDER BY created DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$players = $stmt->fetchAll();
?>

<div class="container mt-5">
    <?php require(__DIR__ . "/../../../partials/flash.php"); ?>
    <h2 class="text-center mb-4">Manage Players</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Display Name</th>
                <th>Created</th>
                <th>Modified</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $player): ?>
                <tr>
                    <td><?= htmlspecialchars($player["id"]); ?></td>
                    <td><?= htmlspecialchars($player["display_name"]); ?></td>
                    <td><?= htmlspecialchars($player["created"]); ?></td>
                    <td><?= htmlspecialchars($player["modified"]); ?></td>
                    <td>
                        <a href="edit_player.php?id=<?= urlencode($player["id"]); ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_player.php?id=<?= urlencode($player["id"]); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this player?');">Delete</a>
                        <form method="POST" action="favorite_action.php" style="display:inline-block;">
                            <input type="hidden" name="player_id" value="<?= htmlspecialchars($player["id"]); ?>">
                            <input type="hidden" name="redirect" value="manage_player.php"> <!-- Add redirect field -->
                            <button type="submit" class="btn btn-success btn-sm">Favorite</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>