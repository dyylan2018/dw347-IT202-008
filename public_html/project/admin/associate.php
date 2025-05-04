<?php
session_start();
require_once(__DIR__ . "/../../../lib/db.php");
require_once(__DIR__ . "/../../../lib/functions.php");
require_once(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}

$pdo = getDB();

$userSearch = trim($_GET['user_search'] ?? '');
$entitySearch = trim($_GET['entity_search'] ?? '');

$users = [];
$entities = [];

if ($userSearch) {
    $stmt = $pdo->prepare("SELECT id, username FROM Users WHERE username LIKE :search LIMIT 25");
    $stmt->execute([":search" => "%$userSearch%"]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($entitySearch) {
    $stmt = $pdo->prepare("SELECT id, display_name FROM Players WHERE display_name LIKE :search LIMIT 25");
    $stmt->execute([":search" => "%$entitySearch%"]);
    $entities = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<style>
.container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 20px;
    font-family: Arial, sans-serif;
}
h2 {
    margin: -90px 0 30px;
    text-align: center;
}
.form-section label {
    margin-right: 15px;
}
button {
    padding: 8px 16px;
    background-color: #343a40;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
button:hover {
    background-color: #23272b;
}
.results {
    display: flex;
    gap: 40px;
    margin-top: 20px;
}
.column {
    flex: 1;
}
h3 {
    text-align: center;
    margin-bottom: 10px;
}
.checkbox-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #ccc;
    padding: 10px;
    background-color: #f8f9fa;
}
.checkbox-list label {
    display: block;
    margin-bottom: 15px;
}
.toggle {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}
.toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}
.toggle .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 50px;
}
.toggle .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    border-radius: 50px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: 0.4s;
}
input:checked + .slider {
    background-color: #2196F3;
}
input:checked + .slider:before {
    transform: translateX(26px);
}
.centered-submit {
    text-align: center;
    margin-top: 20px;
}
</style>

<div class="container">
    <?php require(__DIR__ . "/../../../partials/flash.php"); ?>
    <h2>Associate Users with Players</h2>

    <!-- Search Form -->
    <form method="GET" class="form-section" style="text-align: center;">
        <label>
            Username (partial):
            <input type="text" name="user_search" value="<?= htmlspecialchars($userSearch) ?>" />
        </label>
        <label>
            Player Name (partial):
            <input type="text" name="entity_search" value="<?= htmlspecialchars($entitySearch) ?>" />
        </label>
        <button type="submit">Search</button>
    </form>

    <?php if ($userSearch || $entitySearch): ?>
    <form method="POST" action="associate_action.php">
        <div class="results">
            <!-- Users Column -->
            <div class="column">
                <h3>Users</h3>
                <div class="checkbox-list">
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <label>
                                <?= htmlspecialchars($user['username']) ?>
                                <label class="toggle">
                                    <input type="checkbox" name="user_ids[]" value="<?= $user['id'] ?>" />
                                    <span class="slider"></span>
                                </label>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No matching users found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Players Column -->
            <div class="column">
                <h3>Players</h3>
                <div class="checkbox-list">
                    <?php if (count($entities) > 0): ?>
                        <?php foreach ($entities as $entity): ?>
                            <label>
                                <?= htmlspecialchars($entity['display_name']) ?>
                                <label class="toggle">
                                    <input type="checkbox" name="entity_ids[]" value="<?= $entity['id'] ?>" />
                                    <span class="slider"></span>
                                </label>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No matching players found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Submit Associations -->
        <div class="centered-submit">
            <button type="submit">Apply Associations</button>
        </div>
    </form>
    <?php endif; ?>
</div>