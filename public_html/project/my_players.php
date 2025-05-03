<?php
session_start();
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true);

$db = getDB();
$search_results = [];

// Handle adding a player
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_player_id"])) {
    $player_id = $_POST["add_player_id"];
    $user_id = get_user_id();

    $check = $db->prepare("SELECT id FROM MyPlayers WHERE user_id = :uid AND player_id = :pid");
    $check->execute([":uid" => $user_id, ":pid" => $player_id]);

    if (!$check->fetch()) {
        $insert = $db->prepare("INSERT INTO MyPlayers (user_id, player_id) VALUES (:uid, :pid)");
        $insert->execute([":uid" => $user_id, ":pid" => $player_id]);
        flash("Player added to your list.", "success");
    } else {
        flash("Player is already in your list.", "warning");
    }

    header("Location: my_players.php");
    exit;
}

// Handle removing a player
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["remove_player_id"])) {
    $player_id = $_POST["remove_player_id"];
    $user_id = get_user_id();

    $stmt = $db->prepare("DELETE FROM MyPlayers WHERE user_id = :uid AND player_id = :pid");
    $stmt->execute([":uid" => $user_id, ":pid" => $player_id]);
    flash("Player removed from your list.", "success");

    header("Location: my_players.php");
    exit;
}

// Handle player search
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["player_name"])) {
    $name = trim($_POST["player_name"]);
    $stmt = $db->prepare("SELECT id, first_name, last_name, position, age FROM Players WHERE CONCAT(first_name, ' ', last_name) LIKE :name");
    $stmt->execute([":name" => "%$name%"]);
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Players</title>
    <style>
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            text-align: center;
        }
        .flash {
            margin: 10px auto;
            padding: 10px;
            border-radius: 5px;
        }
        .flash.success { background-color: #d4edda; color: #155724; }
        .flash.warning { background-color: #fff3cd; color: #856404; }
        .flash.danger  { background-color: #f8d7da; color: #721c24; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f4f4f4;
        }
        h1 {
            margin-top: 50px;
        }
        form.inline-form {
            display: inline;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Your Players</h1>

    <!-- Flash messages -->
    <?php foreach (getMessages() as $msg): ?>
        <div class="flash <?= $msg['color'] ?>"><?= $msg['text'] ?></div>
    <?php endforeach; ?>

    <!-- Search Form -->
    <form method="POST">
        <label for="player_name">Search Players by Name:</label><br>
        <input type="text" name="player_name" id="player_name" required>
        <input type="submit" value="Search">
    </form>

    <!-- Matching search results -->
    <?php if (!empty($search_results)): ?>
        <h2>Search Results</h2>
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Age</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($search_results as $player): ?>
                <tr>
                    <td><?= htmlspecialchars($player["first_name"] . " " . $player["last_name"]) ?></td>
                    <td><?= htmlspecialchars($player["position"]) ?></td>
                    <td><?= htmlspecialchars($player["age"]) ?></td>
                    <td>
                        <form method="POST" class="inline-form">
                            <input type="hidden" name="add_player_id" value="<?= $player["id"] ?>">
                            <input type="submit" value="Add">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["player_name"])): ?>
        <p>No players found with that name.</p>
    <?php endif; ?>

    <!-- Display current user's players -->
    <h2 style="margin-top: 40px;">Your List</h2>
    <?php
    $stmt = $db->prepare("SELECT p.id, p.first_name, p.last_name, p.position, p.age FROM Players p
                          JOIN MyPlayers mp ON p.id = mp.player_id
                          WHERE mp.user_id = :user_id");
    $stmt->execute([":user_id" => get_user_id()]);
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if ($players): ?>
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Age</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($players as $player): ?>
                <tr>
                    <td><?= htmlspecialchars($player['first_name'] . " " . $player['last_name']) ?></td>
                    <td><?= htmlspecialchars($player['position']) ?></td>
                    <td><?= htmlspecialchars($player['age']) ?></td>
                    <td>
                        <form method="POST" class="inline-form">
                            <input type="hidden" name="remove_player_id" value="<?= $player["id"] ?>">
                            <input type="submit" value="Remove">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have no players in your list yet.</p>
    <?php endif; ?>
</div>
</body>
</html>