<?php
session_start();
  $array = array();
  // /session report dropdown

if( isset($_POST['reports_ses_storage'])) {
  $_SESSION['default_storage'] = $_POST['reports_ses_storage'];
  $array = 1;
echo json_encode($array);
}
  // /session report dropdown

if( isset($_POST['reports_ses'])) {
  $_SESSION['default_report'] = $_POST['reports_ses'];
  $array = 1;
echo json_encode($array);
}
// /session report date dropdown
if( isset($_POST['reports_ses_date'])) {
  $_SESSION['default_report_date'] = $_POST['reports_ses_date'];
   if ($_POST['reports_ses_date']==3) {
     $_SESSION['default_control'] = 0;
   }
  $array = 1;
echo json_encode($array);
}
 ?>
