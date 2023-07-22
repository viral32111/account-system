<?php
$sessionOptions=array(
	'name'=>'demoSessionID',
	'cookie_domain'=>'localhost',
	'cookie_secure'=>false,
	'cookie_httponly'=>true,
	'cookie_samesite'=>'Lax'
);

if(session_start($sessionOptions)==FALSE){
	die('There was an error creating your session information.');
}

function sessionReset(){
	$_SESSION['account']=null;
	session_reset();
	setcookie(session_name(),'',time()-3600,'/');
	session_destroy();
}

function createSession(){
	$_SESSION['account']=array();
}

function isLoggedIn(){
	return array_key_exists('account',$_SESSION);
}

function getSessionUsername(){
	return(array_key_exists('account',$_SESSION))?$_SESSION['account']['username']:null;
}
function setSessionUsername($username){
	if(array_key_exists('account',$_SESSION)==FALSE)createSession();
	$_SESSION['account']['username']=$username;
}

function getSessionAvatar(){
	return(array_key_exists('account',$_SESSION))?$_SESSION['account']['avatar']:null;
}
function setSessionAvatar($avatar){
	if(array_key_exists('account',$_SESSION)==FALSE)createSession();
	$_SESSION['account']['avatar']=($avatar===NULL?"default.png":$avatar);
}
?>