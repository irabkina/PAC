<?php 
	//session_start();

	if( $db->connect_errno )
	    die( "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error );

	if( !$db->set_charset( "utf8mb4" ) ) {
	    printf("Error loading character set utf8mb4: %s\n", $db->error);
	} 

	// arrays to hold results
	$data = array();
	$activityLabels = array();
	$activityDetails = array();

	$patid = $_SESSION['patid'];

	// table names for queries
	
	$tablePrefix = "";
	$dataTable = $tablePrefix ."sensors";
	$userTable = $tablePrefix . "patientdata";
	$datasetTable = $tablePrefix . "dataset";
	$activityDatasetTable = $tablePrefix . "activitydataset";
	$activityTable = $tablePrefix . "activity";

	// data
	$query = "SELECT * from {$dataTable} WHERE patientId={$patid}";
	$result = $db->query($query);
	if( !$result ) {
	//an error occured
	die( "There was a problem executing the SQL query. MySQL error returned: {$db->error} (Error #{$db->errno})" );
}

	// label names
	$query2 = "SELECT * FROM {$activityDatasetTable} WHERE datasetID IN (SELECT ID FROM {$datasetTable} WHERE patientId={$patid})";
	$result2 = $db->query( $query2 );
	if( !$result2 ) {
	//an error occured
	die( "There was a problem executing the SQL query. MySQL error returned: {$db->error} (Error #{$db->errno})" );
	}
	
	// label info
	$query3 = "SELECT activity FROM {$activityDatasetTable} WHERE datasetID IN (SELECT ID FROM {$datasetTable} WHERE patientId={$patid})";
	$result3 = $db->query( $query3 );
	if( !$result3) {
	//an error occured
	die( "There was a problem executing the SQL query. MySQL error returned: {$db->error} (Error #{$db->errno})" );
	}
	

	// get the data -- hard coded for accelerometer
	while( $row = $result->fetch_assoc() ) {
		$data[] = array( $row['timestamp'], $row['accelerometer_x_CAL'], $row['accelerometer_y_CAL'], $row['accelerometer_z_CAL'],);
	}

	// get the label names
	while($row2 = $result2->fetch_assoc()){
		//echo "row2";
		//echo json_encode($row2);
		$activityDetails[] = array($row2['startTime'],$row2['endTime']);
	}

	// get the label times
	while($row3 = $result3->fetch_assoc()){
		//echo "row3";
		//echo json_encode($row3);
		$activityLabels[] = array($row3['activity']);
	}

	?>