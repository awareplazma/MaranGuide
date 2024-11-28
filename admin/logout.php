<?php

$_SESSION = array();

session_destroy();

header("Location: ../visitor/admin_login.php");
exit();
?>
