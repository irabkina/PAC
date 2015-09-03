<?php

//session_start();

//require_once 'api.password.php';


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
</form>
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
