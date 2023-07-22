<?php
require_once('../../inc/api.php');

/*********************************************
URL: /api/accounts/create
Method: POST
Parameters:
 - [Required] Email (String)
 - [Required] Password (String)
*********************************************/

if($_SERVER['REQUEST_METHOD']!=='POST'){
	respond(405,'This endpoint only accepts POST requests.');
}

$accountEmail=$_POST['email'];
$accountPassword=$_POST['password'];

/* Check if the email & password meet requirements:
Email:
 - Name area only contains valid characters: a-z, A-Z, 0-9, ., -, ~
 - Contains one @
 - Ends with valid TLD (.com, .org, .co.uk, etc.)
 - Registered domain is after @ (maybe WHOIS lookup this?)
 - 128 characters maximum

Password:
 - 1 upper case letter
 - 2 numbers minimum
 - 12 characters minimum
 - 64 characters maximum
*/

// Check if a user with that email already exists in the database

// Insert new user into the database

// Respond appropriately

respond(501,'This endpoint hasn\'t been fully implemented yet.');
?>