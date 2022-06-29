<?php
//DB details
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'clicku';

//Create connection and select DB
$dbcon = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($dbcon->connect_error) {
    die("Unable to connect database: " . $dbcon->connect_error);
}
