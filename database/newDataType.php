
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
</head>

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

?>

<form action="createDataTable.php" method="post" class="login">
    <p>
      <label for="dataType">Data Type Name:</label>
      <input type="text" name="dataType" id="dataType" value="">
    </p>
    <p>
      <div class = "dimensions"/>
      <label>Dimension Name: </label>
        <input type="text" name="dimensions[]" id="dimensions">
      </p>
      <label>Dimension Units: </label>
      <input type="text" name="units[]" id="units">
      </p>
      </div>

    <input type="button" id="add-row" name="add-row" value="Add Dimension" />

    <p class="login-submit">
      <button type="submit" class="login-button">Create Data Type</button>
    </p>

</form>
 
    <!--jquery to create more dimension rows-->
    <script>
 
   jQuery(function($) {
    var $button = $('#add-row'),
        $row = $('.dimensions').clone();

    $button.click(function() {
        $row.clone().insertBefore( $button );
    });
    });
 
    </script>


