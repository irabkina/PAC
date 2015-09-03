<?php

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

	// get existing labels

	// exec python file TODO: fix  file and argument names
	exec('/usr/bin/python2.7 /srv/http/assets/py/switch.py arg1 arg2')

	?>