<?php
require_once(__DIR__ . "/../../../lib/db.php");
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

$sort = $_GET['sort'] ?? 'created';
$filter_position = $_GET['position'] ?? '';
$filter_status = $_GET['status'] ?? '';
$filter_bats = $_GET['bats'] ?? '';

$query = "SELECT * FROM Players WHERE 1=1";
$params = [];

if ($filter_position) {
    if ($filter_position === "Pitcher") {
        $query .= " AND position IN ('Starting Pitcher', 'Relief Pitcher')";
    }
    elseif ($filter_position === "Outfielder") {
        $query .= " AND position IN ('Right Fielder', 'Left Fielder', 'Center Fielder')";
    }
    elseif ($filter_position === "Infielder") {
        $query .= " AND position IN ('First Baseman', 'Second Baseman', 'Shortstop', 'Third Baseman')";
    }
    else {
        $query .= " AND position = :position";
        $params[':position'] = $filter_position;
    }
}

if ($filter_status) {
    if ($filter_status === "Active") {
        $query .= " AND status = 'Active'";
    }
    elseif ($filter_status === "Inactive") {
        $query .= " AND status = 'Inactive'";
    }
}

if ($filter_bats) {
    $query .= " AND bats = :bats";
    $params[':bats'] = $filter_bats;
}

switch ($sort) {
    case 'age':
        $query .= " ORDER BY age DESC";
        break;
    case 'name':
        $query .= " ORDER BY display_name ASC";
        break;
    case 'jersey':
        $query .= " ORDER BY jersey ASC";
        break;
    default:
        $query .= " ORDER BY created DESC";
}

$db = getDB();
$stmt = $db->prepare($query);
$stmt->execute($params);
$players = $stmt->fetchAll();
?>

<div class="container py-5">
    <h2 class="text-center mb-4">NY Yankees Player List</h2>

    <form method="GET" class="mb-4">
        <div class="row g-2 justify-content-center">
            <div class="col-md-2">
                <label for="position" class="form-label">Position</label>
                <select name="position" id="position" class="form-select">
                    <option value="">All</option>
                    <option value="Pitcher" <?= ($filter_position === 'Pitcher') ? 'selected' : '' ?>>Pitcher</option>
                    <option value="Catcher" <?= ($filter_position === 'Catcher') ? 'selected' : '' ?>>Catcher</option>
                    <option value="Infielder" <?= ($filter_position === 'Infielder') ? 'selected' : '' ?>>Infielder</option>
                    <option value="Outfielder" <?= ($filter_position === 'Outfielder') ? 'selected' : '' ?>>Outfielder</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All</option>
                    <option value="Active" <?= ($filter_status === 'Active') ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= ($filter_status === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="bats" class="form-label">Bats</label>
                <select name="bats" id="bats" class="form-select">
                    <option value="">All</option>
                    <option value="Right" <?= ($filter_bats === 'Right') ? 'selected' : '' ?>>Right</option>
                    <option value="Left" <?= ($filter_bats === 'Left') ? 'selected' : '' ?>>Left</option>
                    <option value="Both" <?= ($filter_bats === 'Both') ? 'selected' : '' ?>>Switch</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="sort" class="form-label">Sort By</label>
                <select name="sort" id="sort" class="form-select">
                    <option value="created" <?= ($sort === 'created') ? 'selected' : '' ?>>Newest</option>
                    <option value="name" <?= ($sort === 'name') ? 'selected' : '' ?>>Name (A-Z)</option>
                    <option value="age" <?= ($sort === 'age') ? 'selected' : '' ?>>Age</option>
                    <option value="jersey" <?= ($sort === 'jersey') ? 'selected' : '' ?>>Jersey</option>
                </select>
            </div>

            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-primary w-100">Apply</button>
            </div>
        </div>
    </form>

    <?php if ($players) : ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Jersey</th>
                        <th>Position</th>
                        <th>Age</th>
                        <th>Status</th>
                        <th>Bats</th>
                        <th>Throws</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($players as $p) : ?>
                        <tr>
                            <td><?php se($p, "id"); ?></td>
                            <td><?php se($p, "display_name"); ?></td>
                            <td><?php se($p, "jersey"); ?></td>
                            <td><?php se($p, "position"); ?></td>
                            <td><?php se($p, "age"); ?></td>
                            <td><?php se($p, "status"); ?></td>
                            <td><?php se($p, "bats"); ?></td>
                            <td><?php se($p, "throws"); ?></td>
                            <td>
                                <a href="view_player.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-info mb-1">View</a>
                                <form method="POST" action="favorite_action.php" style="display:inline;">
                                    <input type="hidden" name="player_id" value="<?= $p['id']; ?>">
                                    <input type="hidden" name="redirect" value="list_players.php"> <!-- Add redirect field -->
                                    <button type="submit" class="btn btn-sm btn-warning">Favorite</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p class="text-center mt-3">No players found matching your criteria.</p>
    <?php endif; ?>
</div>

<?php
require(__DIR__ . "/../../../partials/flash.php");
?>