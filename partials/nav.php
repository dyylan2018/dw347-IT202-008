<?php
require_once(__DIR__ . "/../lib/functions.php");

// Note: this is to resolve cookie issues with port numbers
$domain = $_SERVER["HTTP_HOST"];
if (strpos($domain, ":")) {
    $domain = explode(":", $domain)[0];
}
$localWorks = true; // some people have issues with localhost for the cookie params
// if you're one of those people, make this false

// Only set session parameters if session is not started yet
if (session_status() == PHP_SESSION_NONE) {
    // This is where we set the session cookie parameters **only before starting the session**.
    if (($localWorks && $domain == "localhost") || $domain != "localhost") {
        session_set_cookie_params([
            "lifetime" => 60 * 60,
            "path" => "$BASE_PATH",
            "domain" => $domain,
            "secure" => true,
            "httponly" => true,
            "samesite" => "lax"
        ]);
    }
    // Now start the session
    session_start();
}
?>

<!-- boostrap inclusion 5.3 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- include css and js files -->
<link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
<script src="<?php echo get_url('helpers.js'); ?>"></script>

<nav class="navbar navbar-expand-lg mt-3 mx-4 small" style="background-color: rgb(81, 72, 72); border-radius: 10px; font-size: 0.90rem;">
  <div class="container-fluid">
    <a class="navbar-brand text-light" href="#">Pick Your Player</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
      <ul class="navbar-nav mb-2 mb-lg-0">
        <?php if (is_logged_in()) : ?>
          <li class="nav-item"><a class="nav-link text-light" href="<?php echo get_url('home.php'); ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link text-light" href="<?php echo get_url('profile.php'); ?>">Profile</a></li>
          <li class="nav-item"><a class="nav-link text-light" href="<?php echo get_url('favorites.php'); ?>">Favorites</a></li>
          <li class="nav-item"><a class="nav-link text-light" href="<?php echo get_url('my_players.php'); ?>">My Players</a></li>          
          <li class="nav-item"><a class="nav-link text-light" href="<?php echo get_url('other_players.php'); ?>">Other</a></li>          
          <li class="nav-item"><a class="nav-link text-light" href="<?php echo get_url('assigned.php'); ?>">Assigned</a></li>                    
          <li class="nav-item"><a class="nav-link text-light" href="<?php echo get_url('admin/manage_player.php'); ?>">Manage</a></li>
        <?php endif; ?>

        <?php if (!is_logged_in()) : ?>
          <li class="nav-item"><a class="nav-link text-light" href="<?php echo get_url('login.php'); ?>">Login</a></li>
          <li class="nav-item"><a class="nav-link text-light" href="<?php echo get_url('register.php'); ?>">Register</a></li>
        <?php endif; ?>

        <?php if (has_role("Admin")) : ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Players
            </a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li><a class="dropdown-item" href="<?php echo get_url('admin/create_player.php'); ?>">Create Player</a></li>
              <li><a class="dropdown-item" href="<?php echo get_url('admin/list_players.php'); ?>">List Players</a></li>
              <li><a class="dropdown-item" href="<?php echo get_url('admin/search_player.php'); ?>">Fetch Player</a></li>
              <li><a class="dropdown-item" href="<?php echo get_url('admin/edit_player.php'); ?>">Edit Player</a></li>
              <li><a class="dropdown-item" href="<?php echo get_url('admin/associate.php'); ?>">Associate Player</a></li>
              <li><a class="dropdown-item" href="<?php echo get_url('admin/delete_player.php'); ?>">Delete Player</a></li>
            </ul>
          </li>
        <?php endif; ?>

        <?php if (has_role("Admin")) : ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Admin
            </a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li><a class="dropdown-item" href="<?php echo get_url('admin/create_role.php'); ?>">Create Role</a></li>
              <li><a class="dropdown-item" href="<?php echo get_url('admin/list_roles.php'); ?>">List Roles</a></li>
              <li><a class="dropdown-item" href="<?php echo get_url('admin/assign_roles.php'); ?>">Assign Roles</a></li>
            </ul>
          </li>
        <?php endif; ?>

        <?php if (is_logged_in()) : ?>
          <li class="nav-item"><a class="nav-link text-light" href="<?php echo get_url('logout.php'); ?>">Logout</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>