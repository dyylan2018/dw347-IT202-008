<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
//attempt to apply
if (isset($_POST["users"]) && isset($_POST["roles"])) {
    $user_ids = $_POST["users"]; //se() doesn't like arrays so we'll just do this
    $role_ids = $_POST["roles"]; //se() doesn't like arrays so we'll just do this
    if (empty($user_ids) || empty($role_ids)) {
        flash("Both users and roles need to be selected", "warning");
    } else {
        //for sake of simplicity, this will be a tad inefficient
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO UserRoles (user_id, role_id, is_active) VALUES (:uid, :rid, 1) ON DUPLICATE KEY UPDATE is_active = !is_active");
        foreach ($user_ids as $uid) {
            foreach ($role_ids as $rid) {
                try {
                    $stmt->execute([":uid" => $uid, ":rid" => $rid]);
                    flash("Updated role", "success");
                } catch (PDOException $e) {
                    flash(var_export($e->errorInfo, true), "danger");
                }
            }
        }
    }
}

//get active roles
$active_roles = [];
$db = getDB();
$stmt = $db->prepare("SELECT id, name, description FROM Roles WHERE is_active = 1 LIMIT 10");
try {
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        $active_roles = $results;
    }
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}

//search for user by username
$users = [];
if (isset($_POST["username"])) {
    $username = se($_POST, "username", "", false);
    if (!empty($username)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT Users.id, username, (SELECT GROUP_CONCAT(name, ' (' , IF(ur.is_active = 1,'active','inactive') , ')') from 
        UserRoles ur JOIN Roles on ur.role_id = Roles.id WHERE ur.user_id = Users.id) as roles
        from Users WHERE username like :username");
        try {
            $stmt->execute([":username" => "%$username%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($results) {
                $users = $results;
            }
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    } else {
        flash("Username must not be empty", "warning");
    }
}


?>
<div class="container text-center mt-4">
    <h1>Assign Roles</h1>

    <!-- Username search form -->
    <form method="POST" class="d-flex justify-content-center gap-2 mb-4">
        <input type="search" name="username" placeholder="Username search" class="form-control w-auto" />
        <input type="submit" value="Search" class="btn btn-primary" />
    </form>

    <!-- Roles assignment form -->
    <form method="POST" class="mx-auto" style="max-width: 1000px;">
        <?php if (isset($username) && !empty($username)) : ?>
            <input type="hidden" name="username" value="<?php se($username, false); ?>" />
        <?php endif; ?>

        <div class="table-responsive mb-3">
            <table class="table table-bordered align-middle text-start">
                <thead class="table-light">
                    <tr>
                        <th>Users</th>
                        <th>Roles to Assign</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <table class="table mb-0">
                                <?php foreach ($users as $user) : ?>
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="users[]" value="<?php se($user, 'id'); ?>" id="user_<?php se($user, 'id'); ?>" />
                                                <label class="form-check-label" for="user_<?php se($user, 'id'); ?>">
                                                    <?php se($user, "username"); ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td><?php se($user, "roles", "No Roles"); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </td>
                        <td>
                            <?php foreach ($active_roles as $role) : ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="<?php se($role, 'id'); ?>" id="role_<?php se($role, 'id'); ?>" />
                                    <label class="form-check-label" for="role_<?php se($role, 'id'); ?>">
                                        <?php se($role, "name"); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <input type="submit" value="Toggle Roles" class="btn btn-success" />
    </form>
</div>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>