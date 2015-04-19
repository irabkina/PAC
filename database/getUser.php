
<link rel="stylesheet" href="css/style.css">

<?php

session_start();

//require_once 'api.password.php';

//connect to the DB
$dbName = "as_2a1e9dcfd6f405c";
  $dbHost = "us-cdbr-azure-west-a.cloudapp.net";
  $dbUser = "b682c0769dbd11";
  $dbPass = "696d3bff";

$db = new mysqli( $dbHost, $dbUser, $dbPass, $dbName );

if( $db->connect_errno )
    die( "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error );

if( !$db->set_charset( "utf8mb4" ) ) {
    printf("Error loading character set utf8mb4: %s\n", $db->error);
} 

$tablePrefix = "";
$userTable = $tablePrefix . "user";

if(!isset($_SESSION['userId'])){
	$username = $_POST['username'];
	$password = $_POST['password'];
	$query = "SELECT * FROM {$userTable} WHERE Username='{$username}'";
}
else{
	$userId = $_SESSION['userId'];
	$query= "SELECT * FROM {$userTable} WHERE ID='{$userId}'";
}

//try to login
$result = $db->query( $query );
if( !$result ) {
	//an error occured
	die( "There was a problem executing the SQL query. MySQL error returned: {$db->error} (Error #{$db->errno})" );
}

if( !$result->num_rows ) {

	?>
	<h1>User Does Not Exist</h1>
    <p>This user does not exist.</p>
	<p class="login"><a href="index.html">Try again.</a></p>
	<?php
	die();
}

//get the user as an object
while( $row = $result->fetch_assoc() ) {
	$user = (object) $row;
	

//free the memory, discard the db query
$result->free();

//verify the passwords match
// $matches = password_verify( $password, $user->Password );
// if( !$matches ) {
// 	die( "Invalid password." );
// }


// session variables
$_SESSION['userId'] = $user->ID;


include("patientSelect.php");
}

?>

