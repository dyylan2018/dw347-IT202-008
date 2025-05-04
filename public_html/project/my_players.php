<?php
session_start();
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true);

$db = getDB();
$user_id = get_user_id();
$search_results = [];
$filter_limit = $_POST["player_filter_limit"] ?? "all";
$filter_position = $_POST["position_filter"] ?? "all";

// Get the list of current player_ids
$stmt = $db->prepare("SELECT player_id FROM MyPlayers WHERE user_id = :uid");
$stmt->execute([":uid" => $user_id]);
$current_ids = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), "player_id");

// Handle search
$search = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["search"])) {
    $search = trim($_POST["search"]);
    if ($search) {
        $stmt = $db->prepare("SELECT id, first_name, last_name, position, age FROM Players WHERE CONCAT(first_name, ' ', last_name) LIKE :q");
        $stmt->execute([":q" => "%$search%"]);
        $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Handle Add
if (isset($_POST["add_player_id"])) {
    $player_id = (int)$_POST["add_player_id"];
    if (!in_array($player_id, $current_ids)) {
        $stmt = $db->prepare("INSERT INTO MyPlayers (user_id, player_id) VALUES (:uid, :pid)");
        $stmt->execute([":uid" => $user_id, ":pid" => $player_id]);
        flash("Player added to your list", "success");
        $current_ids[] = $player_id;
    } else {
        flash("Player already in your list", "warning");
    }
}

// Handle Remove
if (isset($_POST["remove_player_id"])) {
    $player_id = (int)$_POST["remove_player_id"];
    $stmt = $db->prepare("DELETE FROM MyPlayers WHERE user_id = :uid AND player_id = :pid");
    $stmt->execute([":uid" => $user_id, ":pid" => $player_id]);
    flash("Player removed from your list", "success");
    $current_ids = array_diff($current_ids, [$player_id]);
}

// Handle Remove All
if (isset($_POST["remove_all"])) {
    $stmt = $db->prepare("DELETE FROM MyPlayers WHERE user_id = :uid");
    $stmt->execute([":uid" => $user_id]);
    flash("All players removed from your list", "success");
    $current_ids = [];
}

// Get all My Players with order of addition
$stmt = $db->prepare("SELECT p.id, p.first_name, p.last_name, p.position, p.age, mp.created_at FROM Players p
                      JOIN MyPlayers mp ON p.id = mp.player_id WHERE mp.user_id = :uid ORDER BY mp.created_at ASC");
$stmt->execute([":uid" => $user_id]);
$all_my_players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Apply Filters
$filtered_players = array_filter($all_my_players, function($player) use ($filter_position) {
    return $filter_position === "all" || $player["position"] === $filter_position;
});

if ($filter_limit === "limit10") {
    $my_players = array_slice($filtered_players, 0, 10);
} else {
    $my_players = $filtered_players;
}

// Get list of distinct positions
$stmt = $db->query("SELECT DISTINCT position FROM Players ORDER BY position ASC");
$positions = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!-- Styling same as before -->
<style>
.container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
    font-family: Arial, sans-serif;
    text-align: center;
}
input[type="text"], select {
    padding: 8px;
    width: 250px;
    margin-bottom: 10px;
}
input[type="submit"], button {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    margin: 4px;
}
input[type="submit"]:hover, button:hover {
    background-color: #0056b3;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}
th, td {
    padding: 10px;
    border: 1px solid #ccc;
    vertical-align: middle;
}
th {
    background-color: #f4f4f4;
}
.flash.success { background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px; }
.flash.warning { background: #fff3cd; color: #856404; padding: 10px; margin: 10px 0; border-radius: 5px; }
.action-col {
    width: 180px;
}
</style>

<div class="container">
    <h1>My Players</h1>

    <?php foreach (getMessages() as $msg): ?>
        <div class="flash <?= $msg['color'] ?>"><?= $msg['text'] ?></div>
    <?php endforeach; ?>

    <!-- Search Form -->
    <form method="POST">
        <input type="text" name="search" placeholder="Search player by name" value="<?= htmlspecialchars($search) ?>" required>
        <input type="submit" value="Search">
    </form>

    <!-- Search Results -->
    <?php if (!empty($search_results)): ?>
        <h3>Search Results</h3>
        <table>
            <tr><th>Name</th><th>Position</th><th>Age</th><th class="action-col">Action</th></tr>
            <?php foreach ($search_results as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row["first_name"] . " " . $row["last_name"]) ?></td>
                    <td><?= htmlspecialchars($row["position"]) ?></td>
                    <td><?= htmlspecialchars($row["age"]) ?></td>
                    <td>
                        <?php if (in_array($row["id"], $current_ids)): ?>
                            <span>Already in list</span>
                        <?php else: ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="add_player_id" value="<?= $row["id"] ?>">
                                <input type="submit" value="Add">
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif ($search !== ""): ?>
        <p>No matching players found.</p>
    <?php endif; ?>

    <!-- Filter Dropdowns -->
    <form method="POST" style="margin-top: 20px;">
        <label for="player_filter_limit">Show:</label>
        <select name="player_filter_limit">
            <option value="all" <?= $filter_limit === "all" ? "selected" : "" ?>>All Players</option>
            <option value="limit10" <?= $filter_limit === "limit10" ? "selected" : "" ?>>First 10 Added</option>
        </select>

        <label for="position_filter">Position:</label>
        <select name="position_filter">
            <option value="all" <?= $filter_position === "all" ? "selected" : "" ?>>All</option>
            <?php foreach ($positions as $pos): ?>
                <option value="<?= $pos ?>" <?= $filter_position === $pos ? "selected" : "" ?>><?= $pos ?></option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Apply Filters">
    </form>

    <!-- My Players Table -->
    <h3 style="margin-top:30px;">Your Player List</h3>
    <?php if ($my_players): ?>
        <form method="POST">
            <input type="hidden" name="remove_all" value="1">
            <input type="submit" value="Remove All">
        </form>

        <table>
            <tr><th>Name</th><th>Position</th><th>Age</th><th class="action-col">Action</th></tr>
            <?php foreach ($my_players as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row["first_name"] . " " . $row["last_name"]) ?></td>
                    <td><?= htmlspecialchars($row["position"]) ?></td>
                    <td><?= htmlspecialchars($row["age"]) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="remove_player_id" value="<?= $row["id"] ?>">
                            <input type="submit" value="Remove">
                        </form>
                        <form method="GET" action="player_info.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $row["id"] ?>">
                            <input type="submit" value="View Player">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>You have no players in your list.</p>
    <?php endif; ?>
</div>