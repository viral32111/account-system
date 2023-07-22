<?php
require_once('inc/session.php');
require_once('inc/mysql.php');
require_once('inc/utility.php');
require_once('lib/google-authenticator.php');

if(isset($_POST['loginButton'])){
	$username=$_POST['usernameTextbox'];
	$password=$_POST['passwordTextbox'];
	$totpCode=$_POST['totpTextbox'];

	if(!empty($username)&&!empty($password)){
		$hashedPassword=hash('sha512',$password);
		$passwordQuery=queryDB('SELECT Avatar,Password FROM Accounts WHERE Username="'.escapeDB($username).'";');
		if($passwordQuery[0]==true){
			$avatar=$passwordQuery[1]['Avatar'];
			if($hashedPassword==$passwordQuery[1]['Password']){
				$secretQuery=queryDB('SELECT Secret FROM Accounts WHERE Username="'.escapeDB($username).'";');
				if($secretQuery[0]==true){
					$totpSecret=$secretQuery[1]['Secret'];
					if($totpSecret!=null){
						if(verifyTOTP($totpSecret,$totpCode)==true){
							setSessionUsername($username);
							setSessionAvatar($avatar);
							redirect('index.php');
						}else{
							echo('You entered an incorrect code.');
						}
					}else{
						setSessionUsername($username);
						setSessionAvatar($avatar);
						redirect('index.php');
					}
				}else{
					echo('An error occured while executing secret query! ('.$secretQuery[1].')');
				}
			}else{
				echo('You entered the wrong password.');
			}
		}else{
			echo('An error occured while executing password query! ('.$passwordQuery[1].')');
		}
	}else{
		echo('Please fill in both your username and password.');
	}
}

disconnectDB();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Login to your account</title>
	</head>
	<body>
		<h3>Login to your account</h3>
		<?php if(isLoggedIn()){ ?>
		<p>You're already logged in, visit the <a href="index.php">homepage</a> and logout first.</p>
		<?php }else{ ?>
		<p>If your account doesn't use 2FA then ignore the textbox for it.</p>
		<form action="login.php" method="POST">
			<input type="text" name="usernameTextbox" placeholder="Username..."><br>
			<input type="password" name="passwordTextbox" placeholder="Password..."><br>
			<input type="text" name="totpTextbox" placeholder="2FA Code..."><br>
			<input type="submit" name="loginButton" value="Login to your account">
		</form><br>
		<a href="register.php">Create an account instead</a>
		<?php } ?>
	</body>
</html>