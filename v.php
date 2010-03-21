<?php

// Verify email

session_start();
include ('common.php');
include ('db.php');

sm_init_db();

if (!$_SESSION['veracccode']) {
	sm_raise_err("There's no account to verify...");
	sm_die();
}

print_header('Verify your account');

?>

