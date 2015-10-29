<?php

session_start();

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

	$patid = $_SESSION['patid'];
	$datasetId = 31; //currently not functional

	// table names for queries
	
	$tablePrefix = "";
	$dataTable = $tablePrefix ."sensors";
	$userTable = $tablePrefix . "patientdata";
	$datasetTable = $tablePrefix . "dataset";
	$activityDatasetTable = $tablePrefix . "activityDataset";
	$activityTable = $tablePrefix . "activity";

	$query = "truncate table {$activityDatasetTable} "; //delete from has weird behavior (doesn't delete last added label)

	$result = $db->query( $query );
	if( !$result ) {
	//an error occured
	die( "There was a problem executing the SQL query. MySQL error returned: {$db->error} (Error #{$db->errno})" );
	}
	?>