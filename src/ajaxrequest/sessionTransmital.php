<?php

  session_start();
  $array = array();

if( isset($_POST['storage_from_ses'])) {
  $_SESSION['storage_from'] = $_POST['storage_from_ses'];
  $_SESSION['storage_to'] = 0;
  unset($_SESSION['error_msg']);
  unset($_SESSION['transfer_item_tray']);
  $array = 1;
echo json_encode($array);
}
if( isset($_POST['storage_to_ses'])) {
  $_SESSION['storage_to'] = $_POST['storage_to_ses'];
  unset($_SESSION['error_msg']);
  unset($_SESSION['transfer_item_tray']);
  $array = 1;
echo json_encode($array);
}
//second tabs
if( isset($_POST['storage_from_ses2'])) {
  $_SESSION['storage_from2'] = $_POST['storage_from_ses2'];
  $_SESSION['storage_to2'] = 0;
  $_SESSION['hide_panel']=1;
  unset($_SESSION['error_msg2']);
  unset($_SESSION['transfer_item_tray']);
  $array = 1;
echo json_encode($array);
}
if( isset($_POST['storage_to_ses2'])) {
  $_SESSION['storage_to2'] = $_POST['storage_to_ses2'];
  $_SESSION['hide_panel'] = 1;
  unset($_SESSION['error_msg2']);
  unset($_SESSION['transfer_item_tray']);
  $array = 1;
echo json_encode($array);
}
 ?>
