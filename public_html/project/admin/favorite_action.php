<?php
require_once(__DIR__ . "/../../../lib/db.php");
require_once(__DIR__ . "/../../../lib/functions.php");
require_once(__DIR__ . "/../../../partials/nav.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['player_id']) && is_numeric($_POST['player_id'])) {
        $player_id = $_POST['player_id'];

        $pdo = getDB();
        try {
            // Insert the favorite into the database
            $stmt = $pdo->prepare("INSERT INTO Favorites (player_id) VALUES (:player_id)");
            $stmt->execute([":player_id" => $player_id]);
            flash("Player favorited successfully!", "success");
        } catch (PDOException $e) {
            error_log("Favorite action error: " . var_export($e, true));
            if (str_contains($e->getMessage(), "Duplicate entry")) {
                flash("Player is already favorited.", "warning");
            } else {
                flash("Error favoriting player: " . htmlspecialchars($e->getMessage()), "danger");
            }
        }
    } else {
        flash("Invalid player ID.", "danger");
    }
}

// Get the redirect URL from the form submission
$redirect_url = $_POST['redirect'] ?? 'list_players.php';  // Default to list.php if no redirect is provided

// Redirect back to the page from which the action was initiated
header("Location: $redirect_url");
exit;
?>