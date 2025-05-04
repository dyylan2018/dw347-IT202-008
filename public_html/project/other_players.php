<?php
session_start();
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../lib/functions.php");
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true); // Ensure only logged-in users can access

$pdo = getDB();
$user_id = get_user_id();

// Handle search and sort input
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'display_name';
$validSorts = ['display_name', 'age'];
$orderBy = in_array($sort, $validSorts) ? $sort : 'display_name';

// Get all players with search and sort
$query = "SELECT * FROM Players";
$params = [];

if (!empty($search)) {
    $query .= " WHERE display_name LIKE :search";
    $params[':search'] = "%$search%";
}

$query .= " ORDER BY $orderBy";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get favorite player IDs
$fav_stmt = $pdo->prepare("SELECT player_id FROM Favorites WHERE user_id = :uid");
$fav_stmt->execute([":uid" => $user_id]);
$fav_ids = array_column($fav_stmt->fetchAll(PDO::FETCH_ASSOC), "player_id");

// Get my player IDs
$my_stmt = $pdo->prepare("SELECT player_id FROM MyPlayers WHERE user_id = :uid");
$my_stmt->execute([":uid" => $user_id]);
$my_ids = array_column($my_stmt->fetchAll(PDO::FETCH_ASSOC), "player_id");
?>

<style>
.container {
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    font-family: Arial, sans-serif;
}

h2 {
    margin: -50px 0 30px;
    text-align: center;
}

form.mb-3 {
    text-align: center;
    margin-bottom: 20px;
}

form.mb-3 input,
form.mb-3 select {
    padding: 6px;
    margin-right: 8px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: center;
}

th {
    background-color: #343a40;
    color: white;
}

.btn {
    padding: 6px 12px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    color: white;
    font-size: 14px;
}

.btn-fav {
    background-color: #17a2b8;
}

.btn-my {
    background-color: #28a745;
}

.btn:hover {
    opacity: 0.9;
}
</style>

<div class="container">
    <?php require(__DIR__ . "/../../partials/flash.php"); ?>
    <h2>Other Available Players</h2>

    <form method="GET" class="mb-3">
        <input type="text" name="search" placeholder="Search by name" value="<?= htmlspecialchars($search) ?>" />
        <select name="sort">
            <option value="display_name" <?= $sort === 'display_name' ? 'selected' : '' ?>>Sort by Name</option>
            <option value="age" <?= $sort === 'age' ? 'selected' : '' ?>>Sort by Age</option>
        </select>
        <button type="submit" class="btn btn-my">Apply</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Display Name</th>
                <th>Jersey</th>
                <th>Position</th>
                <th>Age</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $p): 
                $pid = $p["id"];
                $in_fav = in_array($pid, $fav_ids);
                $in_my = in_array($pid, $my_ids);

                // Only show players not in both lists
                if ($in_fav && $in_my) continue;
            ?>
                <tr>
                    <td><?= htmlspecialchars($pid) ?></td>
                    <td><?= htmlspecialchars($p["display_name"]) ?></td>
                    <td><?= htmlspecialchars($p["jersey"] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($p["position"] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($p["age"] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($p["status"] ?? 'Unknown') ?></td>
                    <td>
                        <?php if (!$in_fav): ?>
                            <form method="POST" action="favorite_from_other.php" style="display:inline;">
                                <input type="hidden" name="player_id" value="<?= $pid ?>">
                                <input type="hidden" name="redirect" value="other_players.php">
                                <button type="submit" class="btn btn-fav">Favorite</button>
                            </form>
                        <?php endif; ?>
                        <?php if (!$in_my): ?>
                            <form method="POST" action="my_player_action.php" style="display:inline;">
                                <input type="hidden" name="player_id" value="<?= $pid ?>">
                                <input type="hidden" name="redirect" value="other_players.php">
                                <button type="submit" class="btn btn-my">Add to My Players</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>