<?php
session_start();
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../lib/functions.php");
require_once(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to favorite a player.", "warning");
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player_id']) && is_numeric($_POST['player_id'])) {
    $player_id = $_POST['player_id'];
    $user_id = get_user_id();
    $pdo = getDB();

    try {
        // Fetch player info
        $stmt = $pdo->prepare("SELECT display_name, jersey, position, age, status FROM Players WHERE id = :player_id");
        $stmt->execute([":player_id" => $player_id]);
        $player = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($player) {
            // Check for duplicates
            $check = $pdo->prepare("SELECT id FROM Favorites WHERE user_id = :user_id AND player_id = :player_id");
            $check->execute([
                ":user_id" => $user_id,
                ":player_id" => $player_id
            ]);
            if ($check->fetch()) {
                flash("Player already in favorites.", "info");
            } else {
                // Insert into favorites
                $insert = $pdo->prepare("INSERT INTO Favorites (user_id, player_id, display_name, jersey, position, age, status)
                                         VALUES (:user_id, :player_id, :display_name, :jersey, :position, :age, :status)");
                $insert->execute([
                    ":user_id" => $user_id,
                    ":player_id" => $player_id,
                    ":display_name" => $player['display_name'],
                    ":jersey" => $player['jersey'],
                    ":position" => $player['position'],
                    ":age" => $player['age'],
                    ":status" => $player['status'],
                ]);
                flash("Player added to favorites!", "success");
            }
        } else {
            flash("Player not found.", "danger");
        }
    } catch (Exception $e) {
        error_log("Favorite from other error: " . $e->getMessage());
        flash("Something went wrong.", "danger");
    }
} else {
    flash("Invalid request.", "danger");
}

// Redirect back to other_players.php
header("Location: other_players.php");
exit;