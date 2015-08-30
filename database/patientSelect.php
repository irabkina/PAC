<?php

//session_start();

//require_once 'api.password.php';

//connect to the DB
// $dbName = "as_2a1e9dcfd6f405c";
// 	$dbHost = "us-cdbr-azure-west-a.cloudapp.net";
// 	$dbUser = "b682c0769dbd11";
// 	$dbPass = "696d3bff";
// 	$db = new mysqli( $dbHost, $dbUser, $dbPass, $dbName );

// $db = new mysqli( $dbHost, $dbUser, $dbPass, $dbName );

// if( $db->connect_errno )
//     die( "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error );

// if( !$db->set_charset( "utf8mb4" ) ) {
//     printf("Error loading character set utf8mb4: %s\n", $db->error);
// } 

$userid = $_SESSION['userId'];

$tablePrefix = "";
$userTable = $tablePrefix . "patientaccess";

$query = "SELECT * FROM {$userTable} WHERE userId={$userid}";
$result = $db->query( $query );
if( !$result ) {
	//an error occured
	die( "There was a problem executing the SQL query. MySQL error returned: {$db->error} (Error #{$db->errno})" );
}


?>

<html lang="en">
  <head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>PAC Lab: View Patient Data</title>
	<link rel="stylesheet" href="css/style.css">
  </head>
  <body>
  </br>
  	<h1>View Patient Data</h1>
  	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="login">
      <p>Patient ID:
		<select name="patId" id="patId">
	<?php 
		foreach($result as $row){
			echo("<option>".$row['patientId']."</option>");
		}
	?>
			
		</select>
		<p class="login-submit">
      <button type="submit" class="login-button">Select</button>
    </p>
	</body>
<?php
if (isset($_POST['patId']))
{
	$_SESSION['patid']=$_POST['patId'];
   include("displayPlots.js");
 
}

else if (isset($_SESSION['patid']))
{
	include("displayPlots.js");
}
?>
