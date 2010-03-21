<?php

// Logging in to SocialMe

if ($_SESSION['sm_id']) {
	if ($_SESSION['sm_security_rea']) {
	} else {
		sm_raise_err("You don't need to login AGAIN!");
		sm_die();
	}
}


