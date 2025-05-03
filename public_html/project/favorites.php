<?php
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../lib/functions.php");
require_once(__DIR__ . "/../../partials/nav.php");

$pdo = getDB();

// Handle Remove Favorite action
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["remove"])) {
    $player_id = $_POST["favorite_id"] ?? null;
    if ($player_id) {
        $stmt = $pdo->prepare("DELETE FROM Favorites WHERE player_id = :pid");
        try {
            $stmt->execute([":pid" => $player_id]);
            flash("Player removed from favorites", "success");
            header("Location: " . get_url("favorites.php"));
            exit;
        } catch (Exception $e) {
            flash("Error removing favorite: " . $e->getMessage(), "danger");
        }
    }
}

// Fetching favorites with player details
$query = "SELECT Players.id, Players.display_name, Players.jersey, Players.position, Players.age, Players.status
          FROM Favorites 
          JOIN Players ON Favorites.player_id = Players.id 
          ORDER BY Favorites.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$favorites = $stmt->fetchAll();
?>

<div class="container mt-5">
    <?php require(__DIR__ . "/../../partials/flash.php"); ?>
    <h2 class="text-center mb-4">Favorite Players</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Player ID</th>
                <th>Display Name</th>
                <th>Jersey</th>
                <th>Position</th>
                <th>Age</th>
                <th>Status</th>
                <th>Actions</th> <!-- New column for Remove button -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($favorites as $fav): ?>
                <tr>
                    <td><?= htmlspecialchars($fav["id"]); ?></td>
                    <td><?= htmlspecialchars($fav["display_name"]); ?></td>
                    <td><?= htmlspecialchars($fav["jersey"] ?? 'N/A'); ?></td> <!-- Handle NULL for jersey -->
                    <td><?= htmlspecialchars($fav["position"] ?? 'N/A'); ?></td> <!-- Handle NULL for position -->
                    <td><?= htmlspecialchars($fav["age"] ?? 'N/A'); ?></td> <!-- Handle NULL for age -->
                    <td><?= htmlspecialchars($fav["status"] ?? 'Unknown'); ?></td> <!-- Handle NULL for status -->
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="favorite_id" value="<?= htmlspecialchars($fav["id"]); ?>">
                            <button type="submit" name="remove" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>