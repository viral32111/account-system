<?php
require_once('inc/credentials.php');

$databaseConnection=mysqli_connect($dbHostname,$dbUsername,$dbPassword,$dbName,$dbPort);
if($databaseConnection===FALSE){
	exit('A fatal error occured while connecting to the database: '.mysqli_connect_error());
}

function databaseQuery($template,...$rawArguments){
	global $databaseConnection;

	$safeArguments=array();
	foreach($rawArguments as $index=>$rawArgument)$safeArguments[$index]=mysqli_real_escape_string($databaseConnection,$rawArgument);

	$result=array('success'=>FALSE,'data'=>NULL);
	
	$sqlQuery=vsprintf($template,$safeArguments);
	$response=mysqli_query($databaseConnection,$sqlQuery);
	if(is_a($response,'mysqli_result')===TRUE){ // Successful SELECT, SHOW, etc
		$result['success']=TRUE;
		$result['data']=mysqli_fetch_assoc($response);
		mysqli_free_result($response);
	}elseif($response===TRUE){ // Successful INSERT, UPDATE, etc
		$result['success']=TRUE;
	}elseif($response===FALSE){ // Failure
		$result['data']=mysqli_error($databaseConnection);
	}
	
	return $result;
}

function databaseDisconnect(){
	global $databaseConnection;
	mysqli_close($databaseConnection);
}
?>