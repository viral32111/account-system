<?php
require_once('../../inc/api.php');

/*********************************************
URL: /api/accounts/delete
Method: POST
Parameters:
 - [Required] Email (String)
 - [Required] Password (String)
 - [Optional] 2FA Code (String)
*********************************************/

if($_SERVER['REQUEST_METHOD']!=='POST'){
	respond(405,'This endpoint only accepts POST requests.');
}

$accountEmail=$_POST['email'];
$accountPassword=$_POST['password'];
$account2FACode=$_POST['code'];

// Check if user with that email exists in the database

// Check if password is matches the one in the database

// Check if 2FA code is valid

// Delete user from the database

// Respond appropriately

respond(501,'This endpoint hasn\'t been fully implemented yet.');
?>