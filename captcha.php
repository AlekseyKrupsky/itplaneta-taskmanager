<?php
session_start();
$Random = mt_rand(100001,999999);
$_SESSION['captcha'] = md5($Random);
$im = imagecreatetruecolor(150,30);
imagefilledrectangle($im,0,0,150,30,imagecolorallocate($im , 255,255,255) );
imagettftext($im,40,0,30,23,imagecolorallocate($im , 85,85,85),'font.ttf',$Random);
header('Expires: Wed, 1 Jan 1997 00:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header ('Content-type: image/gif');
imagegif($im);
imagedestroy($im);
