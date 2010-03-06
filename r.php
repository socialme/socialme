<?php

require_once('config.php');
require('db.php');

sm_init_db();

if ($_POST['name']) {
	// It's an account request.
	// captcha check.
	session_start();
	if ($_COOKIE['sm_not_old']) {
		sm_raise_err("You are not old enough. You must be 13+.");
		sm_die();
	}
	if ($_SESSION['vercode'] == $_POST['human']) {
		// Clean $_POST's dirty hands
		sm_germ-x($_POST);
		// Get the current date.
		// Users have to be 13+.
		$regday = date("d");
		$regmon = date("m");
		$regyea = date("Y");
		$uday = intval($_POST['bdayd']);
		$umon = intval($_POST['bdaym']);
		$uyea = intval($_POST['bdayy']);
		// Estimate age
		$uage = $regyea - $uyea;
		if ($umon == $regmon) {
			if ($uday == $regday) {
				$uage++;
			}
		}
		// Check age
		if ($uage < 13) {
			// In days, when can the user come back?
			$can_be_back = 13 - $uage;
			$can_be_back = $can_be_back * 365;
			sm_raise_err("You are not 13 years of age or older. Come back soon!");
			setcookie('sm_not_old', 'yes', time()+60*60*24*$can_be_back);
			sm_die();
		}
		// Gender? WEE NEED NO STINKIN' GENDER!
		// No... wait! SocialMe is gender netural!
		// It's multisex enabled!

		$sql_values = "('" . $_POST['name'] . "', '" . $_POST['mail'] . "', " . $uage . ", 0)";
		$sql_query = "INSERT INTO accounts VALUES " . $sql_values;
		sm_db_exec($sql_query);
		// The user MUST verify their mail
		$veracccode = md5(mt_rand() + time());
		$_SESSION['acco'] = $veracccode;
		mail($_POST['mail'], "Verify your " . $sm_name . "account", "Your " . $sm_name . " account needs verification. Your code is " . $veracccode . ". Return to the verification page and enter the code in.", "From: " . $sm_mail);
		header("Location: /v.php?ac_stamp=" . time());
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=ISO-8859-1" http-enquiv="content-type" />
<title><?php print $sm_name; ?> - Register</title>
</head>
<body>
