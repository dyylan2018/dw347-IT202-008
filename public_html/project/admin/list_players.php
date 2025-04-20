<?php
require_once(__DIR__ . "/../../../lib/db.php");
require(__DIR__ . "/../../../partials/nav.php");
if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}


$db = getDB();
$stmt = $db->prepare("SELECT id, display_name, jersey, position, age, status, bats, throws, created, modified FROM Players ORDER BY created DESC");
$stmt->execute();
$players = $stmt->fetchAll();
?>

<div class="d-flex justify-content-center align-items-start pt-5" style="min-height: 80vh;">
    <div class="container text-center">
        <h1 class="mb-3">All Players</h1>
        <p class="mb-4">Listing all players from the database including timestamps.</p>

        <?php if (!empty($players)) : ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Display Name</th>
                            <th>Jersey</th>
                            <th>Position</th>
                            <th>Age</th>
                            <th>Status</th>
                            <th>Bats</th>
                            <th>Throws</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($players as $player) : ?>
                            <tr>
                                <td><?php se($player, "id"); ?></td>
                                <td><?php se($player, "display_name"); ?></td>
                                <td><?php se($player, "jersey"); ?></td>
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
        <?php else : ?>
            <p>No player records found.</p>
        <?php endif; ?>
    </div>
</div>

<?php require(__DIR__ . "/../../../partials/flash.php");
?>
