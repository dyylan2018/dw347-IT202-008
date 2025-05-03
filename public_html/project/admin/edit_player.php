<?php
require_once(__DIR__ . "/../../../lib/db.php");
require_once(__DIR__ . "/../../../lib/functions.php");
require_once(__DIR__ . "/../../../partials/nav.php");


$players = [];
$player = null;
$success = isset($_GET["success"]) && $_GET["success"] == 1;

// POST: Update selected player
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = $_POST["id"];

    $fields = [
        "sport_id", "team_id", "first_name", "last_name", "display_name",
        "weight", "height", "age", "position"
    ];
    $updates = [];
    $params = [];

    foreach ($fields as $field) {
        $updates[] = "$field = :$field";
        $params[":$field"] = $_POST[$field] ?? null;
    }

    // Handle date_of_birth separately to retain value if not changed
    $dob = $_POST["date_of_birth"] ?? "";
    if (!empty($dob)) {
        $updates[] = "date_of_birth = :date_of_birth";
        $params[":date_of_birth"] = $dob;
    }

    $params[":id"] = $id;

    $db = getDB();
    $stmt = $db->prepare("UPDATE Players SET " . implode(", ", $updates) . ", updated_at = NOW() WHERE id = :id");
    $stmt->execute($params);

    // Redirect back to this page with success flag
    header("Location: edit_player.php?id=" . $id . "&success=1");
    exit;
}

// GET: Load player by ID
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["id"])) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Players WHERE id = :id");
    $stmt->execute([":id" => $_GET["id"]]);
    $player = $stmt->fetch();
}

// GET: Search by name
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["name"])) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Players WHERE first_name LIKE :name OR last_name LIKE :name");
    $stmt->execute([":name" => "%" . $_GET["name"] . "%"]);
    $players = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Player</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .centered-form {
            max-width: 600px;
            margin: auto;
            text-align: center;
        }
        .form-control {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Edit Player</h2>

    <?php if ($success): ?>
        <div class="alert alert-success text-center">Player updated successfully!</div>
    <?php endif; ?>

    <?php if ($player): ?>
        <form method="POST" class="centered-form">
            <input type="hidden" name="id" value="<?= htmlspecialchars($player['id']) ?>">

            <?php
            $fields = [
                "sport_id" => "Sport ID", "team_id" => "Team ID", "first_name" => "First Name",
                "last_name" => "Last Name", "display_name" => "Display Name", "weight" => "Weight",
                "height" => "Height", "age" => "Age", "date_of_birth" => "Date of Birth", "position" => "Position"
            ];
            foreach ($fields as $field => $label):
            ?>
                <div class="mb-3">
                    <label for="<?= $field ?>" class="form-label"><?= $label ?></label>
                    <input
                        type="<?= ($field === 'date_of_birth') ? 'date' : (is_numeric($player[$field]) ? 'number' : 'text') ?>"
                        class="form-control mx-auto"
                        style="max-width: 400px;"
                        name="<?= $field ?>"
                        id="<?= $field ?>"
                        value="<?= htmlspecialchars($player[$field]) ?>">
                </div>
            <?php endforeach; ?>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update Player</button>
            </div>
        </form>
    <?php elseif (!empty($players)): ?>
        <div class="centered-form">
            <p class="text-center">Select a player to edit:</p>
            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Display Name</th>
                        <th>First</th>
                        <th>Last</th>
                        <th>Team ID</th>
                        <th>Position</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($players as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['id']) ?></td>
                        <td><?= htmlspecialchars($p['display_name']) ?></td>
                        <td><?= htmlspecialchars($p['first_name']) ?></td>
                        <td><?= htmlspecialchars($p['last_name']) ?></td>
                        <td><?= htmlspecialchars($p['team_id']) ?></td>
                        <td><?= htmlspecialchars($p['position']) ?></td>
                        <td>
                            <a href="edit_player.php?id=<?= htmlspecialchars($p['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="centered-form">
            <form method="GET">
                <label for="name" class="form-label">Enter Player Name (First or Last)</label>
                <input type="text" name="name" class="form-control mx-auto" style="max-width: 300px;" required>
                <button type="submit" class="btn btn-secondary mt-3">Search</button>
            </form>
        </div>
    <?php endif; ?>
</div>
</body>
</html>