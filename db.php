<?php

include ('config.php');
include ('common.php');

// Simple database layer for many databases
// Currently supporting MySQL and PostgreSQL

// sm_init_db connects to the database.
// If initialized, does nothing.

$sm_inited_db = 0;
$sm_db_handle;

function sm_init_db()
{
	if ($sm_inited_db == 1) {
		return 0;
	} else {
		if ($sm_db_type == "pgsql") {
			$sm_db_handle = pg_connect('host=$sm_db_host dbname=$sm_db_name user=$sm_db_user password=$sm_db_pass');
				or sm_raise_err("PostgreSQL threw peanuts at us: ".pg_last_error()); sm_die();
		}
		if ($sm_db_type == "mysql") {
			$sm_db_handle = mysql_connect($sm_db_host, $sm_db_user, $sm_db_pass);
			if (!$sm_db_handle) {
				sm_raise_err("The super dolphin master of MySQL has refused SocialMe entry: ".mysql_error());
				sm_die();
			}
			@mysql
		} else {
			sm_raise_err("Your database isn't supported. Shoo.");
			sm_die();
		}
	}
	$sm_inited_db = 1;
}
