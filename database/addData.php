
<?php
//connect to the DB
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


$query = "SELECT * FROM datatypeuser";
 #WHERE userId = {$_SESSION['userId']})"; 
$result = $db->query( $query );
if( !$result->num_rows ) {
  //an error occured
  die( "There was a problem executing the SQL query. MySQL error returned: {$db->error} (Error #{$db->errno})" );
}
?>

<form action="getDataTable.php" method="post" class="login">
  <p>
  <label for="selection">Select existing : </label>
  <select name="selection" id="selection">
    <?php

  $resultCopy = array();
  while( $row = $result->fetch_assoc() ) {

    $option = (object) $row;
    $resultCopy[] = $option;
    $dataType = $option->name;
    echo "<option value={$dataType}>{$dataType}</option>";


  }
?>
  </select>
</p>



<p class="login-submit">
      <button type="submit" class="login-button">Select</button>
</form>
 


