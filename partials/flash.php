<?php
/*put this at the bottom of the page so any templates
 populate the flash variable and then display at the proper timing*/

// Ensure session is started
if (!isset($_SESSION)) {
    session_start();
}

// Define getMessages if not already defined
if (!function_exists("getMessages")) {
    function getMessages() {
        $flashes = $_SESSION["flash"] ?? [];
        // Clear flash messages once fetched
        unset($_SESSION["flash"]);
        return $flashes;
    }
}
?>
<div class="container" id="flash">
    <?php $messages = getMessages(); ?>
    <?php if ($messages) : ?>
        <?php foreach ($messages as $msg) : ?>
            <div class="row justify-content-center">
                <div class="alert alert-<?php se($msg, 'color', 'info'); ?>" role="alert"><?php se($msg, "text"); ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<script>
    //used to pretend the flash messages are below the first nav element
    function moveMeUp(ele) {
        let target = document.getElementsByTagName("nav")[0];
        if (target) {
            target.after(ele);
        }
    }

    moveMeUp(document.getElementById("flash"));
</script>