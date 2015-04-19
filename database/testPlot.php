<?php
require_once 'phplot/phplot.php';  // here we include the PHPlot code 
session_start();

$dbName = "PAC";
$dbHost = "localhost";
$dbUser = "Irina";
$dbPass = "DMs67325";

$db = new mysqli( $dbHost, $dbUser, $dbPass, $dbName );

if( $db->connect_errno )
    die( "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error );

if( !$db->set_charset( "utf8mb4" ) ) {
    printf("Error loading character set utf8mb4: %s\n", $db->error);
} 

$data = array();
$table = $_SESSION['table'];
$query = "SELECT * from {$table}";
	$result = $db->query($query);

	while( $row = $result->fetch_assoc() ) {
		$xy = array($row['time'],$row['distance']);
		array_push($data, $xy);	
	}
$plot = new PHPlot;    // here we define the variable


$plot->SetDataValues($data);

//Set titles
$plot->SetTitle("Test Data");
$plot->SetXTitle('X Value');
$plot->SetYTitle('Y Value');

//Turn off X axis ticks and labels because they get in the way:
$plot->SetXTickLabelPos('none');
$plot->SetXTickPos('none');

$plot->DrawGraph();