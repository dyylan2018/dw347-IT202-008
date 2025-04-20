<?php
require(__DIR__ . "/../../partials/nav.php");
?>

<style> body, html { height: 100%; margin: 0; } .home-wrapper
{ display: flex; justify-content: center; align-items: center; height: calc(100vh - 60px);
/* Adjust if your nav bar height differs */
background-color: #f0f0f0; text-align: center; } h1 { font-size: 3rem; color: #333; margin: 0; }
</style> <div class="home-wrapper">
    <h2>Welcome to your NY Yankees Info Homepage</h2>
</div>
<?php
if (is_logged_in(true)) {
    //comment this out if you don't want to see the session variables
    error_log("Session data: " . var_export($_SESSION, true));
}
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>