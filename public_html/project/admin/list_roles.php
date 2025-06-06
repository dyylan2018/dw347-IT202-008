<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}
//handle the toggle first so select pulls fresh data
if (isset($_POST["role_id"])) {
    $role_id = se($_POST, "role_id", "", false);
    if (!empty($role_id)) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE Roles SET is_active = !is_active WHERE id = :rid");
        try {
            $stmt->execute([":rid" => $role_id]);
            flash("Updated Role", "success");
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    }
}
$query = "SELECT id, name, description, is_active from Roles";
$params = null;
if (isset($_POST["role"])) {
    $search = se($_POST, "role", "", false);
    $query .= " WHERE name LIKE :role";
    $params =  [":role" => "%$search%"];
}
$query .= " ORDER BY modified desc LIMIT 10";
$db = getDB();
$stmt = $db->prepare($query);
$roles = [];
try {
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        $roles = $results;
    } else {
        flash("No matches found", "warning");
    }
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}

?>
<div class="container text-center mt-4">
    <h1>List Roles</h1>
    <form method="POST" class="d-flex justify-content-center gap-2 mb-3">
        <input type="search" name="role" placeholder="Role Filter" value="<?php se($_POST, 'role'); ?>" class="form-control w-auto" />
        <input type="submit" value="Search" class="btn btn-primary" />
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped mx-auto" style="max-width: 900px;">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Active</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($roles)) : ?>
                    <tr>
                        <td colspan="5">No roles</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($roles as $role) : ?>
                        <tr>
                            <td><?php se($role, "id"); ?></td>
                            <td><?php se($role, "name"); ?></td>
                            <td><?php se($role, "description"); ?></td>
                            <td><?php echo (se($role, "is_active", 0, false) ? "active" : "disabled"); ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="role_id" value="<?php se($role, 'id'); ?>" />
                                    <?php if (isset($search) && !empty($search)) : ?>
                                        <input type="hidden" name="role" value="<?php se($search, null); ?>" />
                                    <?php endif; ?>
                                    <input type="submit" value="Toggle" class="btn btn-sm btn-secondary" />
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>