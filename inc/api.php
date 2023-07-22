<?php
function handleErrors($errno,$errstr,$errfile,$errline){
	http_response_code(500);
	exit(json_encode(array('code'=>500,'message'=>array(
		'errno'=>$errno,
		'errstr'=>$errstr,
		'errfile'=>$errfile,
		'errline'=>$errline
	)),JSON_UNESCAPED_SLASHES));
}
set_error_handler("handleErrors");

function respond($statusCode,$message){
	http_response_code($statusCode);
	exit(json_encode(array(
		'code'=>$statusCode,
		'message'=>$message
	),JSON_UNESCAPED_SLASHES));
}
?>