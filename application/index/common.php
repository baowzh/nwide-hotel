<?php
use think\Db;
//require_once 'phpqrcode/phpqrcode.php';
function qrcode($url = "http://blog.csdn.net/zhihua_w", $filename = '', $level = 3, $size = 4) {
	Vendor ( 'phpqrcode.phpqrcode' );
	$errorCorrectionLevel = intval ( $level );
	$matrixPointSize = intval ( $size );
	//$object = new \QRcode ();
	//$object->png ( $url, $filename, $errorCorrectionLevel, $matrixPointSize, 2 );
	QRcode::png($url);
}
 