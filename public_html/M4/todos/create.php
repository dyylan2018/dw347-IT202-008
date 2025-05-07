<?php
require_once(__DIR__ . "/../../../lib/db.php"); ?>

<?php
// don't edit - this
// dw347 3/7/25
$expected_fields = ["task", "due", "assigned"];
$diff = array_diff($expected_fields, array_keys($_GET));

if (empty($diff)) {

    // data variables, don't edit
    $task = trim($_GET["task"]);
    $due = trim($_GET["due"]); // hint: must be a valid MySQL date format
    $assigned = trim($_GET["assigned"]); // Must be "self" or a valid format (not empty or equivalent)

    $is_valid = true;
    $errors = [];

    // Start validations
    if (empty($task)) {
        $is_valid = false;
        $errors[] = "Task cannot be empty.";
    }
    
    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $due) || !strtotime($due)) {
        $is_valid = false;
        $errors[] = "Due date must be a valid date in YYYY-MM-DD format.";
    }

    if (empty($assigned) || strtolower($assigned) === "self") {
        $assigned = "self";
    }
    // End validations

    if ($is_valid) {
        /*
        Design a query to insert the incoming data to the proper columns.
        Ensure valid and proper PDO named placeholders are used.
        https://phpdelusions.net/pdo
        */
        $query = "INSERT INTO M4_Todos (task, due, assigned) VALUES (:task, :due, :assigned)";
        $params = [":task" => $task, ":due" => $due, ":assigned" => $assigned];
        try {
            $db = getDB();
            $stmt = $db->prepare($query);
            $r = $stmt->execute($params);
            if ($r) {
                echo "Inserted new Todo with id " . $db->lastInsertId();
            } else {
                echo "Failed to insert";
            }
        } catch (PDOException $e) {
            // extra credit
            // check if the exception was related to a unique constraint
            if ($e->getCode() == 23000) {
                echo "Duplicate entry error: This task already exists for the specified due date.";
            } else {
                echo "There was an error inserting the record; check the logs (terminal)";
                error_log("Insert Error: " . var_export($e, true)); // shows in the terminal
            }
        }
    } else {
        echo "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>";
        error_log("Creation input wasn't valid");
    }
}
?>
<html>
<body>
    <?php require_once(__DIR__ . "/../nav.php"); ?>
    <section>
        <h2>Create ToDo</h2>
        <form method="GET">
            <!-- design the form with proper labels and input fields with the correct types based on the SQL table.
             Wrap each label/input pair in a div tag.
             For "Assigned" ensure the default value is "self". -->
            <div>
                <label for="task">Task:</label>
                <input type="text" id="task" name="task" required maxlength="128" />
            </div>
            <div>
                <label for="due">Due Date:</label>
                <input type="date" id="due" name="due" required />
            </div>
            <div>
                <label for="assigned">Assigned To:</label>
                <input type="text" id="assigned" name="assigned" value="self" required maxlength="60" />
            </div>
            <div>
                <input type="submit" value="Create Todo" />
            </div>
        </form>
    </section>
</body>
</html>
