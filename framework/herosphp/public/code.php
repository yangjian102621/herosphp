<?php
//1.create a new VerifyCode instance.
include __DIR__."/VerifyCode.class.php";
//尺寸大小
$_size = $_GET['size'];
$_config = array('x'=>10, 'y'=>20, 'w'=>100, 'h'=>30, 'f'=>18);
switch ( $_size ) {
	
	case 'big' :
		$_config = array('x'=>15, 'y'=>30, 'w'=>110, 'h'=>40, 'f'=>22);
		break;
		
}
$_verify = VerifyCode::getInstance();
//2.configure the verify code and generate 4 chars
$_vcode = $_verify->configure($_config)->generate(4);
//3.send the image to the browser(gif,jpg,png)
session_start();
$_SESSION['scode'] = strtoupper($_vcode);
$_verify->show('gif');
?>
