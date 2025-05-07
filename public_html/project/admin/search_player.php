<?php
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../lib/db.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

$filtered = [];
$hasSearched = false;
// dw347

$playerSearch = strtolower(trim($_GET["player"] ?? ""));
$team = (int)($_GET["team"] ?? 48); // Default team ID

if (!empty($playerSearch)) {
    $hasSearched = true;

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Players WHERE LOWER(display_name) LIKE :player AND team_id = :team");
    $stmt->execute([
        ":player" => "%$playerSearch%",
        ":team" => $team
    ]);
    $filtered = $stmt->fetchAll();
}
?>
<div class="d-flex justify-content-center align-items-start pt-5" style="min-height: 80vh;">
    <div class="container text-center">
        <h1 class="mb-3">Player Info</h1>
        <p class="mb-4">Search for NY Yankees players by name (Team ID defaults to 48).</p>
        <form method="GET">
            <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap mb-3">
                <label for="team" class="form-label m-0 align-self-center">Team ID</label>
                <input name="team" id="team" type="number" class="form-control w-auto" value="<?php se($_GET, 'team', '48'); ?>" />
                <label for="player" class="form-label m-0 align-self-center">Player</label>
                <input name="player" id="player" class="form-control w-auto" value="<?php se($_GET, 'player'); ?>" required />
                <input type="submit" value="Fetch Player(s)" class="btn btn-primary" />
            </div>
        </form>

        <?php if ($hasSearched): ?>
            <?php if (!empty($filtered)) : ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Display Name</th>
                                <th>Position</th>
                                <th>Age</th>
                                <th>Status</th>
                                <th>Bats</th>
                                <th>Throws</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($filtered as $player) : ?>
                                <tr>
                                    <td><?php se($player, "id"); ?></td>
                                    <td><?php se($player, "display_name"); ?></td>
                                    <td><?php se($player, "position"); ?></td>
                                    <td><?php se($player, "age"); ?></td>
                                    <td><?php se($player, "status"); ?></td>
                                    <td><?php se($player, "bats"); ?></td>
                                    <td><?php se($player, "throws"); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No players found matching '<?php echo htmlspecialchars($playerSearch); ?>'.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require(__DIR__ . "/../../../partials/flash.php");
?>