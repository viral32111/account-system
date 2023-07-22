<?php
require_once('inc/session.php');
require_once('inc/mysql.php');
require_once('inc/utility.php');
require_once('lib/google-authenticator.php');

if(isset($_POST['activateTOTPButton'])){
	$totpCode=$_POST['2faCode'];
	$generatedSecret=$_SESSION['secret'];
	if(verifyTOTP($generatedSecret,$totpCode)==true){
		$updateQuery=queryDB('UPDATE Accounts SET Secret="'.escapeDB($generatedSecret).'" WHERE Username="'.escapeDB(getSessionUsername()).'";');
		if($updateQuery[0]==true){
			$_SESSION['secret']=null;
			header('Location: index.php');
		}else{
			echo('An error occured while executing update query! ('.$updateQuery[1].')');
		}
	}else{
		echo('Sorry that code was incorrect, please try again.<br>');
	}
}else{
	$secretQuery=queryDB('SELECT Secret FROM Accounts WHERE Username="'.escapeDB(getSessionUsername()).'";');
	if($secretQuery[0]==true){
		$totpSecret=$secretQuery[1]['Secret'];
		if($totpSecret!=null){
			$has2FA=true;
		}else{
			$has2FA=false;
		}
	}else{
		echo('An error occured while executing secret query! ('.$secretQuery[1].')');
	}

	if($has2FA==false){
		// Secret generation
		$generatedSecret='';
		$base32='ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; // base32 range
		for($i=0; $i<16; $i++){
			$value=random_int(1,31);
			$part=substr($base32,$value,1); // the documentation was wrong about this function
			$generatedSecret.=$part;
		}

		// Information
		$issuer=rawurlencode('Login Example'); // issuer name
		$username=rawurlencode(getSessionUsername()); // user's account name

		// QR Code
		$uri='otpauth://totp/'.$issuer.':'.$username.'?secret='.$generatedSecret.'&issuer='.$issuer;
		$result=shell_exec('qrencode --output=- -m=1 '.escapeshellarg($uri));
		$qrCode="data:image/png;base64,".base64_encode($result);

		// Save secret
		$_SESSION['secret']=$generatedSecret;
	}else{
		echo('You already have 2FA on your account, please remove it first!');
	}
}

disconnectDB($dbConnection);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Setup 2FA for your account</title>
	</head>
	<body>
		<h3>Setup 2FA for your account</h3>
		<?php if($has2FA==true){ ?>
		<p>You already have 2FA on your account, visit the <a href="index.php">homepage</a> and deactivate 2FA first.</p>
		<?php }else{ ?>
		<p>If you want to secure your account using 2FA scan the QR code below using Google Authenticator then fill in the code field.
		<br>If you don't want to do this, click skip (you can set it up at a later time).</p><br>
		<img src="<?=$qrCode;?>" width="148px">
		<form action="2fa.php" method="POST">
			<input type="text" name="2faCode" placeholder="2FA Code..."><br>
			<input type="submit" name="activateTOTPButton" value="Activate 2FA">
		</form><br>
		<a href="index.php">Skip this step for now >:(</a>
		<?php } ?>
	</body>
</html>