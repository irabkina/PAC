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
	$datasetId = 1; //currently not functional

	// table names for queries
	
	$tablePrefix = "";
	$dataTable = $tablePrefix ."sensors";
	$userTable = $tablePrefix . "patientdata";
	$datasetTable = $tablePrefix . "dataset";
	$activityDatasetTable = $tablePrefix . "activityDataset";
	$activityTable = $tablePrefix . "activity";
	

	$query = "select * from {$activityDatasetTable} where datasetId in (select id from {$datasetTable} where patientId={$patid})";
	$result = $db->query( $query );
	if( !$result ) {
	//an error occured
	die( "There was a problem executing the SQL query. MySQL error returned: {$db->error} (Error #{$db->errno})" );
	}
	$query2 = "select * from {$dataTable} where patientId={$patid}";
	$result2 = $db->query( $query2 );
	if( !$result2 ) {
	//an error occured
	die( "There was a problem executing the SQL query. MySQL error returned: {$db->error} (Error #{$db->errno})" );
}
	// get existing labels (data_mat and labels; -1 for unlabeled data)
	$data_mat = array();
	$labels = array();
	
	#print_r($result);
	foreach($result as $row){
		#print_r("kjsalkjdf;aksfaskd");
		#print_r($row);
		$start = date('U',strtotime($row['startTime']));
		$end = date('U',strtotime($row['endTime']));;
		$arr = array($start, $end);
		array_push($data_mat, $arr);
		if ($row['activity']==='inactive'){
			array_push($labels,0);	
		}
		else if ($row['activity']==='active'){
			array_push($labels,1);
		}
		else{
			array_push($labels,-1);
		}
	}

	

	// get raw data
	$raw_data = array();
	foreach($result2 as $row2){
		#print_r($row2);
		$time = date('U',strtotime($row2['timestamp']));
		$raw_data[$time] = array($row2['accelerometer_x_CAL'], $row2['accelerometer_y_CAL'], $row2['accelerometer_z_CAL']);
		#$raw_data[] = $arr2;
		#print_r(count($raw_data));
	}

	$data_mat_j = json_encode($data_mat);
	$labels_j = json_encode($labels);
	$raw_data_j = json_encode($raw_data);

	// exec python file 
	$command = 'python predict.py ' ."{$data_mat_j}". " {$labels_j}". " {$raw_data_j}";
	//print_r("command is: ".$command);
	$output = shell_exec(escapeshellcmd($command). ' 2>&1');

	echo $output;
	//print_r("Prediction complete. Suggestions will appear in labeling box.");
	//return $output;

	// for each result, display (NOTE: #output is a string)

	?>