<?php

include("config.php");

function print_header($area = "Home")
{
	print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	print '<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">';
	print '<head><title>' . $area . ' - ' . $sm_name . '</title></head>';
	print '<body>';
	// Proud Sponsor Of Killing Obselete Tech
	print '<!--[if lt IE 8]>';
	print '<script type="text/javascript">for (x in document.write) { document.write(x); }</script>';
	print '<![endif]-->';
}

function print_footer()
{
	print '</body>';
	print '</html>';
}

function sm_raise_err($msg = "Something funky is going on.", $html_init = 1) {
	if ($html_init == 1) {
		print_header("Error");
	}
	print '<div style="text-background: #FF0000"><b>Houston, we have a problem. An error has us! It says: '.$msg.' Sir?</b></div>';
	print 'Alas, we've gone as free as <i>Free Bird</i> by Lynyrd Skynyrd. Do something else.';
	if ($html_init == 1) {
		print_footer();
	}
}

function sm_die() {
	// The Best Way To Get Out Of This...
	exit;
}

// Internal function used by sm_germ-x
function __cleanInput($input) {
 
$search = array(
    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
);
 
    $output = preg_replace($search, '', $input);
    return $output;
}

function sm_germ-x($input) {
    if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = sm_germ-x($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $input  = cleanInput($input);
	// We could include db.php, but everybody won't run MySQL so...
        $output = addslashes($input);
    }
    return $output;
}
?>

