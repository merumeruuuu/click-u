<?php
session_start();
  $array = array();
  // /session report dropdown
if( isset($_POST['inventory_ses_storage'])) {
  $_SESSION['default_inventory_storage'] = $_POST['inventory_ses_storage'];
  $array = 1;
echo json_encode($array);
}
  // /session report dropdown

if( isset($_POST['inventory_ses'])) {
  $_SESSION['default_inventory'] = $_POST['inventory_ses'];
  $array = 1;
echo json_encode($array);
}
if( isset($_POST['inventory_ses_date'])) {
  $_SESSION['default_inventory_date'] = $_POST['inventory_ses_date'];
  $_SESSION['inventory_date1'] = '00/00/0000';
  $_SESSION['inventory_date2'] = '00/00/0000';
  $_SESSION['default_control_inventory'] = 0;
  $array = 1;
echo json_encode($array);
}
//staff

// /session report dropdown
if( isset($_POST['inventory_ses_storage_staff'])) {
$_SESSION['default_report_inventory'] = $_POST['inventory_ses_storage_staff'];
$array = 1;
echo json_encode($array);
}
 ?>
