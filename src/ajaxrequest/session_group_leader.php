<?php
session_start();
$array = array();

if( isset($_POST['group_leader']) ) {
    // save values from other page to session
    $_SESSION['group_leader'] = $_POST['group_leader'];
    $array = 0;
  echo json_encode($array);
}
if( isset($_POST['group_date']) ) {
    $_SESSION['date_use'] = $_POST['group_date'];
    $array = 1;
  echo json_encode($array);
}
if( isset($_POST['group_time']) ) {
    $_SESSION['time_use'] = $_POST['group_time'];
    $array = 1;
  echo json_encode($array);
}
?>
