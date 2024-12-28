<?php

$_SESSION = array();

session_destroy();

header("Location: /MARANGUIDE/visitor/admin_login.php");
exit();
?>
