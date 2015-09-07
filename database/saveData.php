<?php 
	session_start();
	//connect to the DB
	$dbHost = "us-cdbr-azure-central-a.cloudapp.net";
	$dbUser = "b125155e5e1df5";
	$dbPass = "bba28a8d";
	$dbName = "PACMySQLDatabase";
	$db = new mysqli( $dbHost, $dbUser, $dbPass, $dbName );


	if( $db->connect_errno )
	    die( "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error );

	if( !$db->set_charset( "utf8mb4" ) ) {
	    printf("Error loading character set utf8mb4: %s\n", $db->error);
	} 

	if( $db->connect_errno )
	    die( "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error );

	if( !$db->set_charset( "utf8mb4" ) ) {
	    printf("Error loading character set utf8mb4: %s\n", $db->error);
	} 

	#print_r($_POST);

	$patid = $_SESSION['patid'];
	$datasetId = 1; //currently not functional

	// table names for queries
	
	$tablePrefix = "";
	$dataTable = $tablePrefix ."sensors";
	$userTable = $tablePrefix . "patientdata";
	$datasetTable = $tablePrefix . "dataset";
	$activityDatasetTable = $tablePrefix . "activityDataset";
	$activityTable = $tablePrefix . "activity";

	//print_r($_POST);
	$activity = $_POST['activity'];
	$startTime = $_POST['date']." ".$_POST['x-start'];
	#print_r($startTime);
	$endTime = $_POST['date']." ".$_POST['x-end'];
	
	$request = "insert into {$activityDatasetTable} (activity, startTime, endTime, datasetId) values ('{$activity}', '{$startTime}','{$endTime}', {$datasetId});" ;
	$result = $db->query( $request );
	if( !$result ) {
	//an error occured
	die( "There was a problem executing the SQL query. MySQL error returned: {$db->error} (Error #{$db->errno})" );
}

	include "patientSelect.php";
