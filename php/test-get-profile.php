<?php
session_start();
$_SESSION['user_id'] = 2; // Use the test user we just created
include 'get-profile.php';
?>
