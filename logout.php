<?php
session_start();
session_unset();
session_destroy();

// Redirect back to login page for both admin and user
header("Location: login.php");
exit;
?>
