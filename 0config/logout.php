<?php
session_start();
include("../0config/database.php");

if (isset($_SESSION["user_id"])) {
    $session_id = session_id();
    $conn->query("DELETE FROM sessions WHERE sessions_id = '$session_id'");
}

session_unset();
session_destroy();

header("Location: ../login/login.php");
exit();
?>
