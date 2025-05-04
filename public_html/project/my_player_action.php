<?php
session_start(); // <-- ADD THIS LINE AT THE TOP
require_once(__DIR__ . "/../../lib/db.php");
require_once(__DIR__ . "/../../lib/functions.php");


$user_id = get_user_id();
$redirect = $_POST['redirect'] ?? 'other_players.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["player_id"])) {
    $player_id = $_POST["player_id"];

    $pdo = getDB();

    try {
        $stmt = $pdo->prepare("INSERT INTO MyPlayers (user_id, player_id) VALUES (:user_id, :player_id)");
        $stmt->execute([
            ":user_id" => $user_id,
            ":player_id" => $player_id
        ]);
        flash("Player added to your list.", "success");
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), "Duplicate entry")) {
            flash("Player already in your list.", "warning");
        } else {
            error_log("Add my_player error: " . var_export($e, true));
            flash("Error adding player: " . $e->getMessage(), "danger");
        }
    }
}

header("Location: " . $redirect);
exit;