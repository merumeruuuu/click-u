<?php
include '../config.php';
session_start();
  $array = array();
if( isset($_GET['id']) ) {
    // save values from other page to session
    $_SESSION['item'] = $_GET['id'];
    $array = 0;
  echo json_encode($array);
}


?>
