<a href="login.php">Logout</a>

<?php
session_start();

session_unset();
session_destroy();

header("Location: login.php");
exit();
?>