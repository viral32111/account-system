<?php
function redirect($url){
	header('Location: '.$url);
}

function verifyTOTP($secret,$code){
	//$counter=floor(time()/30);
	//$hash=hash_hmac('sha1',$counter,$secret);
	//$offset=$hash[19] & 0xf;
	//$truncatedHash=($hash[$offset++] & 0x7f) << 24 | ($hash[$offset++] & 0x7f) << 16  

	$googleAuth=new PHPGangsta_GoogleAuthenticator();
	return $googleAuth->verifyCode($secret,$code,2);
}
?>