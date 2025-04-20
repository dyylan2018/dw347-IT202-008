<?php
require(__DIR__ . "/../../../lib/db.php");

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["id"])) {
    $id = $_GET["id"];
    $db = getDB();

    $stmt = $db->prepare("DELETE FROM Players WHERE id = :id");
    $stmt->execute([":id" => $id]);
}

// Redirect back to manage.php after delete
header("Location: manage_player.php");
exit;
?>
