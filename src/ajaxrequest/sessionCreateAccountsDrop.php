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
if( isset($_POST['assignLab_ses'])) {
  $_SESSION['assignLab'] = $_POST['assignLab_ses'];
  $array = 1;
echo json_encode($array);
}
if( isset($_POST['id_number_ses'])) {
  $_SESSION['id_number'] = $_POST['id_number_ses'];
  $array = 1;
echo json_encode($array);
}
if( isset($_POST['email_ses'])) {
  $_SESSION['email'] = $_POST['email_ses'];
  $array = 1;
echo json_encode($array);
}
if( isset($_POST['firstname_ses'])) {
  $_SESSION['firstname'] = $_POST['firstname_ses'];
  $array = 1;
echo json_encode($array);
}
if( isset($_POST['lastname_ses'])) {
  $_SESSION['lastname'] = $_POST['lastname_ses'];
  $array = 1;
echo json_encode($array);
}
if( isset($_POST['contact_ses'])) {
  $_SESSION['contact'] = $_POST['contact_ses'];
  $array = 1;
echo json_encode($array);
}
 ?>
