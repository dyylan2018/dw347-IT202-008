<?php
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../lib/db.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['display_name'] ?? '');

    if ($name === '') {
        flash("Please enter a player's display name.", "danger");
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("DELETE FROM Players WHERE display_name = :display_name");
            $stmt->execute([":display_name" => $name]);

            if ($stmt->rowCount() > 0) {
                flash("Player deleted successfully!", "success");
            } else {
                flash("No player found with that display name.", "warning");
            }
        } catch (PDOException $e) {
            error_log("Delete player error: " . var_export($e, true));
            flash("Error deleting player: " . htmlspecialchars($e->getMessage()), "danger");
        }
    }
}
?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Delete Player</h1>
    <form method="POST" class="text-center">
        <div class="mb-3">
            <label for="display_name" class="form-label">Player Display Name</label>
            <input type="text" class="form-control mx-auto text-center" name="display_name" style="max-width: 400px;" required>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-danger">Delete Player</button>
        </div>
    </form>
</div>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>