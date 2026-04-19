<?php

require_once('config.php');         

// Database connection                                   
$mysqli = mysqli_init();
$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
$mysqli->real_connect($config['db_host'],$config['db_user'],$config['db_password'],$config['db_name']); 

// Get all parameter provided by the javascript
$name = $mysqli->real_escape_string(strip_tags($_POST['name']));
$email = $mysqli->real_escape_string(strip_tags($_POST['firstname']));
//$tablename = $mysqli->real_escape_string(strip_tags($_POST['tablename']));

$return=false;
if ( $stmt = $mysqli->prepare("INSERT INTO tblUsers (UserName, UserEmail) VALUES (  ?, ?)")) {

	//$stmt->bind_param("ss", $name, $email);
    $stmt->bind_param("ss", $name, $email);
    $return = $stmt->execute();
	$stmt->close();
}             
$mysqli->close();        

echo $return ? "ok" : "error";

      

