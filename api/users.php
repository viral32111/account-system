<?php
require_once('inc/api.php');
require_once('inc/validation.php');
require_once('inc/mysql.php');

/*********************************************
GET /api/users - Returns user information
Parameters:
 - [Required] User ID (String)

POST /api/users - Create new user
Parameters:
 - [Required] Email (String)
 - [Required] Password (String)

DELETE /api/users - Delete a user
Parameters:
 - [Required] User ID (String)
 - [Required] Password (String)
 - [Required if 2FA enabled] 2FA Code (String)

PATCH /api/users - Update a user
Parameters:
 - [Required] User ID (String)
 - [Required if changing email, password or 2FA status] Password (String)
 - [Required if changing email, password or 2FA status & 2FA is enabled] 2FA Code (String)
 - [Optional] New Email (String)
 - [Optional] New Password (String)
 - [Optional] New Username (String)
 - [Optional] New Avatar (String) [NULL for default/reset]
 - [Optional] New 2FA Secret (String) [NULL to deactivate]

*********************************************/

$requestMethod=$_SERVER['REQUEST_METHOD'];

if($requestMethod==='GET'){ // Fetch user information
	$providedUserID=$_GET['userid'];
	if(empty($providedUserID)===TRUE)respond(400,'No User ID provided.');

	$response=databaseQuery('SELECT Username,Avatar FROM Users WHERE UserID="%s";',$providedUserID);
	if($response['success']===TRUE){
		if($response['data']===NULL)respond(404,'No user with that ID exists.');
		if($response['data']['Avatar']===NULL)$response['data']['Avatar']='img/default-avatar.png';
		respond(200,$response['data']);
	}else{
		respond(500,$response['data']);
	}
}elseif($requestMethod==='POST'){ // Create new user
	$providedEmail=$_POST['email'];
	if(isValidEmail($providedEmail)===FALSE)respond(400,'Invalid email address.');

	$desiredPassword=$_POST['password'];
	if(isValidPassword($desiredPassword)===FALSE)respond(400,'Invalid password.');

	$hashedPassword=password_hash($desiredPassword,PASSWORD_BCRYPT,array('cost'=>10));
	if($hashedPassword===FALSE)respond(500,'There was an error hashing the password.');

	$userIDExists=FALSE;
	$attempts=0;
	do{
		if($attempts>=10)respond(500,'Exceeded maximum retries for creating user.');
		$userID=bin2hex(random_bytes(6));
		$response=databaseQuery('INSERT INTO Users (UserID,Email,Password,Username) VALUES ("%s","%s","%s","%s");',$userID,$providedEmail,$hashedPassword,$providedEmail);
		if($response['success']===TRUE){
			respond(200,$userID);
		}else{
			if(strstr($response['data'],'Duplicate entry')!==FALSE){
				if(strstr($response['data'],'PRIMARY')!==FALSE){
					$userIDExists=TRUE;
				}elseif(strstr($response['data'],'UNIQUE')!==FALSE){
					respond(409,'A user with that email address already exists.');
				}else{
					respond(500,$response['data']);
				}
			}else{
				respond(500,$response['data']);
			}
		}
		$attempts++;
	}while($userIDExists===TRUE);
}elseif($requestMethod==='DELETE'){ // Delete existing user
	$attemptedPassword=$_DELETE['password'];
	respond(501,'This function hasn\'t been fully implemented yet.');
}elseif($requestMethod==='PATCH'){ // Update existing user
	$attemptedPassword=$_POST['password'];
	respond(501,'This function hasn\'t been fully implemented yet.');
}else{
	respond(405,'This endpoint only accepts GET, POST, DELETE or PATCH requests.');
}
?>