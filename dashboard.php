<?php
require_once('inc/session.php');
require_once('inc/mysql.php');
require_once('inc/config.php');
require_once('inc/utility.php');
require_once('lib/google-authenticator.php');

if(isset($_POST['logoutButton'])){
	sessionReset();
	redirect('index.php');
}

if(isset($_POST['changeUsernameButton'])){
	$username=$_POST['newUsernameTextbox'];
	if(!empty($username)){
		$updateQuery=queryDB('UPDATE Accounts SET Username="'.escapeDB($username).'" WHERE Username="'.escapeDB(getSessionUsername()).'";');
		if($updateQuery==true){
			setSessionUsername($username);
			echo('Your username has been changed.');
		}else{
			echo('An error occured while executing the update query! ('.$updateQuery[1].')');
		}
	}
}

if(isset($_POST['changePasswordButton'])){
	$password=$_POST['newPasswordTextbox'];
	if(!empty($password)){
		$hashedPassword=hash('sha512',$password);
		$updateQuery=queryDB('UPDATE Accounts SET Password="'.$password.'" WHERE Username="'.escapeDB(getSessionUsername()).';');
		if($updateQuery==true){
			echo('Your password has been changed.');
		}else{
			echo('An error occured while executing the update query! ('.$updateQuery[1].')');
		}
	}
}

if(isset($_POST['deactivateTOTPButton'])){
	$totpCode=$_POST['totpCodeTextbox'];
	if(!empty($totpCode)){
		$secretQuery=queryDB('SELECT Secret FROM Accounts WHERE Username="'.escapeDB(getSessionUsername()).'";');
		if($secretQuery[0]==true){
			$totpSecret=$secretQuery[1]['Secret'];
			if($totpSecret!=null){
				if(verifyTOTP($totpSecret,$totpCode)){
					$updateQuery=queryDB('UPDATE Accounts SET Secret=NULL WHERE Username="'.escapeDB(getSessionUsername()).'";');
					if($updateQuery[0]==true){
						echo('Your 2FA has been deactivated, please delete the code on your authenticator app.');
					}else{
						echo('An error occured while executing the update query! ('.$updateQuery[1].')');
					}
				}else{
					echo('You must enter the correct code to deactivate 2FA.');
				}
			}else{
				echo('You don\'t have 2FA enabled on your account.');
			}
		}else{
			echo('An error occured while executing the secret query! ('.$secretQuery[1].')');
		}

	}
}

if(isset($_POST['changeAvatarButton'])){
	$fileToUpload=$_FILES['avatarFileUpload'];
	$noErrors=FALSE;
	switch($fileToUpload['error']){
		case UPLOAD_ERR_INI_SIZE:
			echo('That file is too big for this server.');
			break;
		case UPLOAD_ERR_FORM_SIZE:
			echo('That file is too big to be submitted');
			break;
		case UPLOAD_ERR_PARTIAL:
			echo('Please upload the entire file.');
			break;
		case UPLOAD_ERR_NO_FILE:
			echo('No file was selected');
			break;
		case UPLOAD_ERR_NO_TMP_DIR;
			echo('No upload directory exists on the server');
			break;
		case UPLOAD_ERR_CANT_WRITE:
			echo('Cannot write to server disk.');
			break;
		case UPLOAD_ERR_EXTENSION:
			echo('The upload was prevented by an extension.');
			break;
		default:
			$noErrors=TRUE;
			break;
	}
	if($noErrors===TRUE){
		$fileTempPath=$fileToUpload['tmp_name'];
		if(getimagesize($fileTempPath)===FALSE){
			echo('That isn\'t a valid image file.');
			return;
		}
		$fileName=basename($fileToUpload['name']);
		$fileSize=$fileToUpload['size'];
		$fileExtension=strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
		if(in_array($fileExtension,$allowedFileExtensions)===FALSE){
			echo('That file type isn\'t allowed, please use one of the following: '.implode(', ',$allowedFileExtensions).'.');
			return;
		}
		if($fileSize>$maxAvatarFileSize){
			echo('That file is too large, it should be less than '.$maxAvatarFileSize.' bytes.');
			return;
		}
		$fileContent=file_get_contents($fileTempPath,true);
		$fileHash=hash('md5',$fileContent);
		$targetName=$fileHash.'.'.$fileExtension;
		$targetPath='uploads/avatars/'.$targetName;
		if(file_exists($targetPath)===TRUE){
			echo('That file already exists on the server.');
			return;
		}
		$updateQuery=queryDB('UPDATE Accounts SET Avatar="'.$targetName.'" WHERE Username="'.escapeDB(getSessionUsername()).'";');
		if($updateQuery[0]===true){
			echo('You\'ve successfully changed your avatar.');
			move_uploaded_file($fileTempPath,$targetPath);
			setSessionAvatar($targetName);
		}else{
			echo('An error occured while executing the update query! ('.$updateQuery[1].')');
		}
	}
}

if(isset($_POST['uploadFileButton'])){
	$fileToUpload=$_FILES['fileUpload'];
	$noErrors=FALSE;
	switch($fileToUpload['error']){
		case UPLOAD_ERR_INI_SIZE:
			echo('That file is too big for this server.');
			break;
		case UPLOAD_ERR_FORM_SIZE:
			echo('That file is too big to be submitted');
			break;
		case UPLOAD_ERR_PARTIAL:
			echo('Please upload the entire file.');
			break;
		case UPLOAD_ERR_NO_FILE:
			echo('No file was selected');
			break;
		case UPLOAD_ERR_NO_TMP_DIR;
			echo('No upload directory exists on the server');
			break;
		case UPLOAD_ERR_CANT_WRITE:
			echo('Cannot write to server disk.');
			break;
		case UPLOAD_ERR_EXTENSION:
			echo('The upload was prevented by an extension.');
			break;
		default:
			$noErrors=TRUE;
			break;
	}
	if($noErrors===TRUE){
		$fileTempPath=$fileToUpload['tmp_name'];
		$fileName=basename($fileToUpload['name']);
		$fileSize=$fileToUpload['size'];
		$fileExtension=strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
		if($fileSize>$maxFileSize){
			echo('That file is too large, it should be less than '.$maxFileSize.' bytes.');
			return;
		}
		$targetPath='uploads/'.$fileName;
		if(file_exists($targetPath)===TRUE){
			echo('That file already exists on the server.');
			return;
		}
		$insertQuery=queryDB('INSERT INTO Uploads (Name,AuthorUsername) VALUES ("'.$fileName.'","'.escapeDB(getSessionUsername()).'");');
		if($insertQuery[0]===true){
			echo('You\'ve successfully uploaded that file.');
			move_uploaded_file($fileTempPath,$targetPath);
		}else{
			echo('An error occured while executing the insert query! ('.$insertQuery[1].')');
		}
	}
}

if(isset($_GET['delete'])){
	$fileToDelete=rawurldecode($_GET['delete']);
	$selectQuery=queryDB('SELECT Name FROM Uploads WHERE AuthorUsername="'.escapeDB(getSessionUsername()).'" AND Name="'.$fileToDelete.'";');
	if($selectQuery[0]==true){
		$queriedFile=$selectQuery[1]['Name'];
		if($queriedFile!==NULL){
			$deleteQuery=queryDB('DELETE FROM Uploads WHERE AuthorUsername="'.escapeDB(getSessionUsername()).'" AND Name="'.$fileToDelete.'";');
			if($deleteQuery[0]==true){
				unlink('uploads/'.$fileToDelete);
				echo('Successfully deleted that file.');
			}else{
				echo('An error occured while executing the delete query! ('.$deleteQuery[1].')');
			}
		}else{
			echo('That file doesn\'t exist or you don\'t own it.');
		}
	}else{
		echo('An error occured while executing the select query! ('.$selectQuery[1].')');
	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Homepage</title>
		<link rel="stylesheet" href="/css/styles.css" type="text/css">
	</head>
	<body>
		<?php if(isLoggedIn()){ ?>
		<h3>You are logged in as <?=getSessionUsername();?>!</h3>
		<fieldset>
			<legend>Avatar</legend>
			<img src='uploads/avatars/<?=getSessionAvatar();?>' width='64px'>
			<form action="index.php" method="POST" enctype="multipart/form-data">
				<input type="file" name="avatarFileUpload"><br>
				<input type="submit" name="changeAvatarButton" value="Change avatar">
			</form>
		</fieldset>
		<br>
		<fieldset>
			<legend>Username</legend>
			<form action="index.php" method="POST">
				<input type="text" name="newUsernameTextbox" placeholder="New username..."><br>
				<input type="submit" name="changeUsernameButton" value="Change username">
			</form>
		</fieldset>
		<br>
		<fieldset>
			<legend>Password</legend>
			<form action="index.php" method="POST">
				<input type="password" name="newPasswordTextbox" placeholder="New password..."><br>
				<input type="submit" name="changePasswordButton" value="Change password">
			</form>
		</fieldset>
		<br>
		<fieldset>
			<legend>2FA</legend>
			<form action="index.php" method="POST">
				<input type="text" name="totpCodeTextbox" placeholder="2FA Code..."><br>
				<input type="submit" name="deactivateTOTPButton" value="Deactivate 2FA">
			</form>
			<a href="2fa.php">Activate 2FA</a><br>
		</fieldset>
		<br>
		<fieldset>
			<legend>Uploads</legend>
			<form action="index.php" method="POST" enctype="multipart/form-data">
				<input type="file" name="fileUpload"><br>
				<input type="submit" name="uploadFileButton" value="Upload file">
			</form>
			<ul>
				<?php
				$myResult=mysqli_query($dbConnection,'SELECT Name FROM Uploads WHERE AuthorUsername="'.escapeDB(getSessionUsername()).'";');
				while($myData=mysqli_fetch_row($myResult)){
					echo '<li><a href="uploads/'.$myData[0].'">'.$myData[0].'</a> <strong>[<a href="?delete='.rawurlencode($myData[0]).'">X</a>]</strong></li>';
				}
				mysqli_free_result($myResult);
				?>
			</ul>
		</fieldset>
		<br>
		<form action="index.php" method="POST">	
			<input type="submit" name="logoutButton" value="Logout">
		</form>
		<?php }else{ ?>
			<h3>You aren't logged in.</h3>
			<a href="login.php">Login to your account</a><br>
			<a href="register.php">Create an account</a>
		<?php } ?>
	</body>
</html>
<?php
disconnectDB();
?>