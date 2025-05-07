<?php
session_start();
require_once(__DIR__ . "/../../../lib/db.php");
require_once(__DIR__ . "/../../../lib/functions.php");
require_once(__DIR__ . "/../../../partials/nav.php");

// Debugging: Check if user is logged in
if (!is_logged_in()) {
    echo "Not logged in."; // Debugging message to see if user is logged in
    flash("You must be logged in to perform this action", "warning");
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['player_id']) && is_numeric($_POST['player_id'])) {
        $player_id = $_POST['player_id'];

        // Get the logged-in user ID
        $user_id = get_user_id();

        $pdo = getDB();
        try {
            // Fetch the player's details (display_name, jersey, position, etc.)
            $stmt = $pdo->prepare("SELECT display_name, jersey, position, age, status FROM Players WHERE id = :player_id");
            $stmt->execute([":player_id" => $player_id]);
            $player = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the player exists
            if ($player) {
                // Check if the player is already in the user's favorites
                $stmt = $pdo->prepare("SELECT * FROM Favorites WHERE user_id = :user_id AND player_id = :player_id");
                $stmt->execute([":user_id" => $user_id, ":player_id" => $player_id]);
                $existing_favorite = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing_favorite) {
                    flash("Player is already in your favorites.", "warning");
                } else {
                    // Insert the favorite into the database
                    $stmt = $pdo->prepare("INSERT INTO Favorites (user_id, player_id, display_name, jersey, position, age, status) 
                                           VALUES (:user_id, :player_id, :display_name, :jersey, :position, :age, :status)");
                    $stmt->execute([
                        ":user_id"    => $user_id,
                        ":player_id"  => $player_id,
                        ":display_name" => $player['display_name'],
                        ":jersey"      => $player['jersey'],
                        ":position"    => $player['position'],
                        ":age"         => $player['age'],
                        ":status"      => $player['status'],
                    ]);
                    flash("Player favorited successfully!", "success");
                }
            } else {
                flash("Player not found.", "danger");
            }
        } catch (PDOException $e) {
            error_log("Favorite action error: " . var_export($e, true));
            flash("Error favoriting player: " . htmlspecialchars($e->getMessage()), "danger");
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