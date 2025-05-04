<?php
session_start();
require_once(__DIR__ . "/../../../lib/db.php");
require_once(__DIR__ . "/../../../lib/functions.php");
is_logged_in(true); // Only logged-in users can perform actions

$pdo = getDB();

// Ensure form data exists
$user_ids = $_POST['user_ids'] ?? [];
$entity_ids = $_POST['entity_ids'] ?? [];

if (!empty($user_ids) && !empty($entity_ids)) {
    foreach ($user_ids as $uid) {
        foreach ($entity_ids as $eid) {
            // Check if this association already exists
            $check = $pdo->prepare("SELECT 1 FROM UserPlayerAssociations WHERE user_id = :uid AND player_id = :pid");
            $check->execute([":uid" => $uid, ":pid" => $eid]);
            $exists = $check->fetchColumn();

            if ($exists) {
                // Remove the association
                $del = $pdo->prepare("DELETE FROM UserPlayerAssociations WHERE user_id = :uid AND player_id = :pid");
                $del->execute([":uid" => $uid, ":pid" => $eid]);
            } else {
                // Add the association
                $ins = $pdo->prepare("INSERT INTO UserPlayerAssociations (user_id, player_id) VALUES (:uid, :pid)");
                $ins->execute([":uid" => $uid, ":pid" => $eid]);
            }
        }
    }
    flash("Associations updated.");
} else {
    flash("Please select at least one user and one player to associate.", "warning");
}

header("Location: associate.php");
exit;