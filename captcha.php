<?php

session_start();
mt_srand(microtime()+time());
mt_srand(mt_rand()+microtime());

function _randstr($length = 6) {
	$rando = "";
	for ($i = 0; $i < $length; $i++) {
		$rando .= chr(mt_rand(97, 122));
	}
	return $rando;
}

$ra = _randstr();

$im = imagecreate(75, 30);
$bg = imagecolorallocate($im, 255, 255, 255);
$tx = imagecolorallocate($im, mt_rand(0, 245), mt_rand(0, 245), mt_rand(245));
imagestring($im, 4, 0, 0, $ra, $tx);

header('Content-type: image/png');
imagepng($im);
imagedestroy($im);

?>
