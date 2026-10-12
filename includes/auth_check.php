<?php
// PHP code starts here
session_start();
// open the visitor's session so we can see who is logged in

function require_role($allowed_roles) {
// make a tool called require_role; it needs a list of allowed roles, e.g. ['staff']
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    // if nobody is logged in OR this person's role is not in the allowed list
        header("Location: /system/login.php");
        // send the browser to the login page
        exit;
        // stop the script so nothing else runs
    }
    // end of the if
}
// end of the tool