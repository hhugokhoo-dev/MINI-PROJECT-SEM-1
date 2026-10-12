<?php
session_start();
// open the session so we can destroy it
session_destroy();
// delete the session on the server, so the user is logged out
header('Location: /mainpage/index.php');
// send the browser to the home page
exit;
