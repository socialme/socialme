<?php

include('config.php');
include('db.php');
include('common.php');

sm_init_db();

if ($_POST['name']) {
	// It's an account request.
	// captcha check.
	session_start();
	if ($_COOKIE['sm_not_old'] == 'yes') {
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
			// We still need to verify the age fully, because we don't want 12-years
			// on social networks before they're 13!
			if ($uday >= $regday) {
				$uage++;
			}
		} else {
			if ($umon > $regmon) {
				$uage++;
			}
		}
		// Check age
		if ($uage < 13) {
			// In days, when can the user come back?
			if ($uage == 12) {
				$can_be_back = $umon - $regmon;
				$can_be_back *= 60;
			} else {
				$can_be_back = 13 - $uage;
				$can_be_back *= 365;
			}
			sm_raise_err("You are not 13 years of age or older. Come back soon!");
			setcookie('sm_not_old', 'yes', time()+60*60*24*$can_be_back);
			sm_die();
		}
		// Gender? WEE NEED NO STINKIN' GENDER!
		// No... wait! SocialMe is gender netural!
		// It's multisex enabled!

		// Hash password
		$hashed_pass = sha256($_POST['pass'] . $sm_secret);

		$sql_values = "('" . $_POST['name'] . "', '" . $_POST['mail'] . "', " . $uage . ", '" . $hashed_pass . ")";
		$sql_query = "INSERT INTO accounts VALUES " . $sql_values;
		sm_db_exec($sql_query);
		// The user MUST verify their mail
		$veracccode = md5(mt_rand() + time());
		$_SESSION['acco'] = $veracccode;
		mail($_POST['mail'], "Verify your " . $sm_name . "account", "Your " . $sm_name . " account needs verification. Your code is " . $veracccode . ". Return to the verification page and enter the code in.", "From: " . $sm_mail);
		header("Location: /v.php?ac_stamp=" . time());
	} else {
		sm_raise_err("The CAPTCHA was wrong! Sign up again and enter it correctly!");
		sm_die();
	}
}
?>

<?php print_header(); ?>

<form action="/r.php" method="POST">
Your name: <input name="name" /><br />
Your birthday: mm:<input name="umon"/>dd:<input name="uday"/>yy:<input name="uyea"/><br/>
Your password: <input type="password" name="pass"/><br/>
(Wondering about gender? We're gender-netural.)<br/>
CAPTCHA test:<br/>
<img src="captcha.php" alt="CAPTCHA" /><br/>
Are you disabled? <a href="/contact.php">Contact us for an account.</a><br/>
<input name="human"/>
<input type="submit" value="Hitch me up, baby!"/>
</form>

<?php print_header(); ?>
