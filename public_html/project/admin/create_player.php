<?php
require(__DIR__ . "/../../../partials/nav.php");
require_once(__DIR__ . "/../../../lib/db.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}
$success = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Players (
            id, sport_id, team_id, first_name, last_name, display_name, weight, height, age, date_of_birth, position, active, status, bats, throws
        ) VALUES (
            :id, :sport_id, :team_id, :first_name, :last_name, :display_name, :weight, :height, :age, :date_of_birth, :position, 1, 'active', 'R', 'R'
        )");

        $stmt->execute([
            ":id" => $_POST["id"],
            ":sport_id" => $_POST["sport_id"],
            ":team_id" => $_POST["team_id"],
            ":first_name" => $_POST["first_name"],
            ":last_name" => $_POST["last_name"],
            ":display_name" => $_POST["display_name"],
            ":weight" => $_POST["weight"],
            ":height" => $_POST["height"],
            ":age" => $_POST["age"],
            ":date_of_birth" => $_POST["date_of_birth"],
            ":position" => $_POST["position"]
        ]);
        $success = true;
    } catch (Exception $e) {
        error_log("Create player error: " . var_export($e, true));
        $error = "Error creating player: " . $e->getMessage();
    }
}
?>

<div class="container mt-5">
    <h1 class="mb-4 text-center">Create Player</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">Player created successfully!</div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" onsubmit="return validateForm()" class="needs-validation" novalidate>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="POST" onsubmit="return validateForm()" class="text-center">
                    <div class="mb-3">
                        <label for="id" class="form-label w-100 text-center">ID</label>
                        <input type="number" class="form-control mx-auto text-center" name="id" required style="max-width: 500px;">
                    </div>
                    <div class="mb-3">
                        <label for="sport_id" class="form-label w-100 text-center">Sport ID</label>
                        <input type="number" class="form-control mx-auto text-center" name="sport_id" required style="max-width: 500px;">
                    </div>
                    <div class="mb-3">
                        <label for="team_id" class="form-label w-100 text-center">Team ID</label>
                        <input type="number" class="form-control mx-auto text-center" name="team_id" required style="max-width: 500px;">
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label w-100 text-center">First Name</label>
                        <input type="text" class="form-control mx-auto text-center" name="first_name" required style="max-width: 500px;">
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label w-100 text-center">Last Name</label>
                        <input type="text" class="form-control mx-auto text-center" name="last_name" required style="max-width: 500px;">
                    </div>
                    <div class="mb-3">
                        <label for="display_name" class="form-label w-100 text-center">Display Name</label>
                        <input type="text" class="form-control mx-auto text-center" name="display_name" required style="max-width: 500px;">
                    </div>
                    <div class="mb-3">
                        <label for="weight" class="form-label w-100 text-center">Weight</label>
                        <input type="number" class="form-control mx-auto text-center" name="weight" style="max-width: 500px;">
                    </div>
                    <div class="mb-3">
                        <label for="height" class="form-label w-100 text-center">Height</label>
                        <input type="number" class="form-control mx-auto text-center" name="height" style="max-width: 500px;">
                    </div>
                    <div class="mb-3">
                        <label for="age" class="form-label w-100 text-center">Age</label>
                        <input type="number" class="form-control mx-auto text-center" name="age" style="max-width: 500px;">
                    </div>
                    <div class="mb-3">
                        <label for="date_of_birth" class="form-label w-100 text-center">Date of Birth</label>
                        <input type="date" class="form-control mx-auto text-center" name="date_of_birth" style="max-width: 500px;">
                    </div>
                    <div class="mb-3">
                        <label for="position" class="form-label w-100 text-center">Position</label>
                        <input type="text" class="form-control mx-auto text-center" name="position" style="max-width: 500px;">
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-success">Create Player</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</form>
</div>

<script>
function validateForm() {
    // Add more detailed validation if needed
    return true;
}
</script>

<?php require(__DIR__ . "/../../../partials/flash.php");
?>