<?php
<<<<<<< HEAD
require(__DIR__ . "/../../partials/nav.php");
reset_session();
=======
require(__DIR__ . "/../../lib/functions.php");
>>>>>>> prod
?>
<style>
  body, html {
    height: 100%;
    margin: 0;
  }

  .page-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    height: calc(100vh - 60px); /* Adjust depending on nav height */
    background-color: #f5f5f5;
  }

  .register-form {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    min-width: 300px;
    width: 100%;
  }

  .register-form div {
    margin-bottom: 1rem;
  }

  label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    text-align: center;
  }

  input[type="text"],
  input[type="email"],
  input[type="password"] {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 5px;
    text-align: center;
  }

  input[type="submit"] {
    width: 100%;
    padding: 0.7rem;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  input[type="submit"]:hover {
    background-color: #0056b3;
  }
</style>

<div class="page-wrapper">
  <form class="register-form" onsubmit="return validate(this)" method="POST" novalidate>
    <div>
      <label for="email">Email/Username</label>
      <input type="text" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>" />
    </div>

    <div>
      <label for="username">Username</label>
      <input type="text" name="username" required maxlength="30" />
    </div>
<<<<<<< HEAD

    <div>
      <label for="pw">Password</label>
      <input type="password" id="pw" name="password" required minlength="8" />
    </div>

    <div>
      <label for="confirm">Confirm Password</label>
      <input type="password" name="confirm" required minlength="8" />
    </div>

=======
    <div>
        <label for="confirm">Confirm</label>
        <input type="password" name="confirm" required minlength="8" />
    </div>
>>>>>>> prod
    <input type="submit" value="Register" />
  </form>
</div>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success
        let email = form.email.value.trim();
        let username = form.username.value.trim();
        let password = form.password.value;
        let confirm = form.confirm.value;

        // Simple regex for basic email check
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert("Please enter a valid email address.");
            return false;
        }

        if (username.length < 3 || username.length > 30) {
            alert("Username must be between 3 and 30 characters.");
            return false;
        }

        if (password.length < 8) {
            alert("Password must be at least 8 characters.");
            return false;
        }

        if (password !== confirm) {
            alert("Passwords do not match.");
            return false;
        }
        return true;
    }
</script>
<?php
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    $confirm = se(
        $_POST,
        "confirm",
        "",
        false
    );
    $username = se($_POST, "username", "", false);
    //TODO 3
    $hasError = false;
    if (empty($email)) {
        flash("Email must not be empty", "danger");
        $hasError = true;
    }
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        flash("Invalid email address", "danger");
        $hasError = true;
    }
    if (!is_valid_username($username)) {
        flash("Username must only contain 3-30 characters a-z, 0-9, _, or -", "danger");
        $hasError = true;
    }
    if (empty($password)) {
        flash("password must not be empty", "danger");
        $hasError = true;
    }
    if (empty($confirm)) {
        flash("Confirm password must not be empty", "danger");
        $hasError = true;
    }
    if (!is_valid_password($password)) {
        flash("Password too short", "danger");
        $hasError = true;
    }
    if (
        strlen($password) > 0 && $password !== $confirm
    ) {
        flash("Passwords must match", "danger");
        $hasError = true;
    }
    if (!$hasError) {
        //TODO 4
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES(:email, :password, :username)");
        try {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            flash("Successfully registered!", "success");
        } catch (PDOException $e) {
            users_check_duplicate($e->errorInfo);
        }
    }
}
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>