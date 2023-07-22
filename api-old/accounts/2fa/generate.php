<?php
require_once('../../inc/api.php');

/*********************************************
URL: /api/accounts/2fa/generate
Method: GET
Parameters:
 - [Required] Email (String)
Response:
 - 2FA Secret (String)
*********************************************/

if($_SERVER['REQUEST_METHOD']!=='POST'){
	respond(405,'This endpoint only accepts POST requests.');
}

$accountEmail=$_POST['email'];

// Check if user exists with provided email

// Check if user already has 2FA enabled

// Generate a new secret

// Store it temporarily in the database alongside the provided email

// Respond with secret

respond(501,'This endpoint hasn\'t been fully implemented yet.');
?>