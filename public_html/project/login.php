<?php
require(__DIR__ . "/../../partials/nav.php");
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

  .login-form {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    min-width: 300px;
  }

  .login-form div {
    margin-bottom: 1rem;
  }

  label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
  }

  input[type="text"],
  input[type="password"] {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 5px;
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
  <form class="login-form" onsubmit="return validate(this)" method="POST" novalidate>
    <div>
      <label for="email">Email/Username</label>
      <input type="text" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>" />
    </div>

    <div>
      <label for="pw">Password</label>
      <input type="password" id="pw" name="password" required minlength="8" />
    </div>

    <input type="submit" value="Login" />
  </form>
</div>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success
        let isValid = true;
        let pw = form.password.value;
        if(!isValidPassword(pw)){
            flash("Hey Buddy That's Wrong");
            isValid = false;
        }
        return isValid;
    }
</script>
<?php
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);

    //TODO 3
    $hasError = false;
    if (empty($email)) {
        flash("Email must not be empty");
        $hasError = true;
    }
    if (str_contains($email, "@")) {
        //sanitize
        //$email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $email = sanitize_email($email);
        //validate
        /*if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash("Invalid email address");
            $hasError = true;
        }*/
        if (!is_valid_email($email)) {
            flash("Invalid email address");
            $hasError = true;
        }
    } else {
        if (!is_valid_username($email)) {
            flash("Invalid username");
            $hasError = true;
        }
    }
    if (empty($password)) {
        flash("password must not be empty");
        $hasError = true;
    }
    if (!is_valid_password($password)) {
        flash("Password too short");
        $hasError = true;
    }
    if (!$hasError) {
        //flash("Welcome, $email");
        //TODO 4
        $db = getDB();
        $stmt = $db->prepare("SELECT id, email, username, password from Users 
        where email = :email or username = :email");
        try {
            $r = $stmt->execute([":email" => $email]);
            if ($r) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $hash = $user["password"];
                    unset($user["password"]);
                    if (password_verify($password, $hash)) {
                        //flash("Weclome $email");
                        $_SESSION["user"] = $user; //sets our session data from db
                        //lookup potential roles
                        $stmt = $db->prepare("SELECT Roles.name FROM Roles 
                        JOIN UserRoles on Roles.id = UserRoles.role_id 
                        where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                        $stmt->execute([":user_id" => $user["id"]]);
                        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch all since we'll want multiple
                        //save roles or empty array
                        if ($roles) {
                            $_SESSION["user"]["roles"] = $roles; //at least 1 role
                        } else {
                            $_SESSION["user"]["roles"] = []; //no roles
                        }
                        flash("Welcome, " . get_username());
                        die(header("Location: home.php"));
                    } else {
                        flash("Invalid password");
                    }
                } else {
                    flash("Email/Username not found");
                }
            }
        } catch (Exception $e) {
            flash("<pre>" . var_export($e, true) . "</pre>");
        }
    }
}
?>
<?php
require(__DIR__ . "/../../partials/flash.php");