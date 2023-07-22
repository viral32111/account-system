<?php
require_once('../../inc/api.php');

/*********************************************
URL: /api/accounts/2fa/enable
Method: POST
Parameters:
 - [Required] Email (String)
 - [Required] 2FA Code (String)
Response:
 - Success (Boolean) - Does code match?
*********************************************/

if($_SERVER['REQUEST_METHOD']!=='POST'){
	respond(405,'This endpoint only accepts POST requests.');
}

// Check if user exists with provided email

// Check if user already has 2FA enabled

// Check if email is in temporary database

// Check if code matches server generated code based off the secret

// Enable 2FA on the users account

$accountEmail=$_POST['email'];

respond(501,'This endpoint hasn\'t been fully implemented yet.');
?>