<?php
require_once('inc/session.php');
require_once('inc/mysql.php');
require_once('inc/utility.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Demo Website</title>
		<script src="/js/lib/jquery-3.4.1.min.js"></script>
		<script src="/js/api.js"></script>
	</head>
	<body>
		<h1>Demo Website</h1>
		<h3>Sign-up or Sign-in</h3>
		<form id="registrationForm">
			<input type="text" id="email" placeholder="Email address..." pattern=".{3,255}" title="Must be a valid email address." required autofocus><br>
			<input type="password" id="password" placeholder="Password..." pattern=".{8,72}" title="Must be a valid password." required><br>
			<input type="submit" value="Submit">
		</form>
	</body>
	<script>
	$("#registrationForm").submit(function(event){
		event.preventDefault();
		const email=$("#email").val(),password=$("#password").val();
		signUp(email,password);
	});
	</script>
</html>
<?php
databaseDisconnect();
?>