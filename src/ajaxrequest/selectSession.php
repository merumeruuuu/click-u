<?php
session_start();
$array = array();
$gID = $_SESSION['group_id_mod'];
if( isset($_POST['group_leader2']) ) {
    $_SESSION['group_leader'] = $_POST['group_leader2'];

    $array = 0;
  echo json_encode($array);
}
if( isset($_POST['group_storage']) ) {
    // save values from other page to session
    $_SESSION['group_storage'] = $_POST['group_storage'];
    $array = 0;
  echo json_encode($array);
}
if( isset($_POST['group_instructor']) ) {
    // save values from other page to session
    $_SESSION['group_instructor'] = $_POST['group_instructor'];
    $array = 0;
  echo json_encode($array);
}
if( isset($_POST['purpose']) ) {
    $_SESSION['group_purpose'] = $_POST['purpose'];
    $array = 0;
  echo json_encode($array);
}
if( isset($_POST['group_nameS']) ) {
    $_SESSION['group_name'] = $_POST['group_nameS'];
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
