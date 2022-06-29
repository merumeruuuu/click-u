<?php
session_start();
  $array = array();
  // /session create account dropdown
if( isset($_POST['userType_ses'])) {
  $_SESSION['userType'] = $_POST['userType_ses'];
  $array = 1;
echo json_encode($array);
}
if( isset($_POST['deptStrand_ses'])) {
  $_SESSION['deptStrand'] = $_POST['deptStrand_ses'];
  $array = 1;
echo json_encode($array);
}

 ?>
