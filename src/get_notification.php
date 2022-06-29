<?php
  include 'config.php';
  session_start();
//STAFF APPROVE not seen
if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $update_query = "UPDATE notification SET seen_staff = 1 WHERE id = $id";

  mysqli_query($dbcon, $update_query);
  $query = "SELECT
         a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
         b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_staff,b.notif_count
         from notification_control a
         LEFT join notification b on a.notif_control_id = b.notif_control_id
         where b.id = $id ";
   $select = mysqli_query($dbcon,$query);
  $row = mysqli_fetch_array($select);
  $_SESSION['notif_transId'] = $row['trans_id'];
  $_SESSION['notif_id'] = $row['id'];
  header("Location:view_notification.php");
}
//staff seen for approval
if (isset($_GET['staff_seen_approve'])) {
  $id = $_GET['staff_seen_approve'];
  $query = "SELECT
         a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
         b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_staff,b.notif_count
         from notification_control a
         LEFT join notification b on a.notif_control_id = b.notif_control_id
         where b.id = $id ";
   $select = mysqli_query($dbcon,$query);
  $row = mysqli_fetch_array($select);
  $_SESSION['notif_transId'] = $row['trans_id'];
  $_SESSION['notif_id'] = $row['id'];
  header("Location:view_notification.php");
}
//STAFF RECEIVE not seen
if (isset($_GET['unseen_receive_staff'])) {
  $rid = $_GET['unseen_receive_staff'];
  $update_query = "UPDATE notification SET seen_staff = 1 WHERE id = $rid";
  mysqli_query($dbcon, $update_query);
  $query = "SELECT
         a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
         b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_staff,b.notif_count
         from notification_control a
         LEFT join notification b on a.notif_control_id = b.notif_control_id
         where b.id = $rid ";
  $select = mysqli_query($dbcon,$query);
  $row = mysqli_fetch_array($select);
  $_SESSION['notif_transId'] = $row['trans_id'];
  $_SESSION['notif_id'] = $row['id'];
  header("Location:view_notification.php");
}
//STAFF RECEIVE and seen
if (isset($_GET['seen_received_staff'])) {
  $rid = $_GET['seen_received_staff'];
  $query = "SELECT
         a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
         b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_staff,b.notif_count
         from notification_control a
         LEFT join notification b on a.notif_control_id = b.notif_control_id
         where b.id = $rid ";
  $select = mysqli_query($dbcon,$query);
  $row = mysqli_fetch_array($select);
  $_SESSION['notif_transId'] = $row['trans_id'];
  $_SESSION['notif_id'] = $row['id'];
  header("Location:view_notification.php");
}

///////////////////////////////////////////////////////////////

//if not seen user notif approval
if (isset($_GET['userApproveId'])) {
  $rid = $_GET['userApproveId'];

  $update_query = "UPDATE notification SET seen_user = 2 WHERE id = $rid";
  mysqli_query($dbcon, $update_query);
  $query = "SELECT
         a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
         b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_user,b.notif_count
         from notification_control a
         LEFT join notification b on a.notif_control_id = b.notif_control_id
         where b.id = $rid ";
   $select = mysqli_query($dbcon,$query);
  $row = mysqli_fetch_array($select);
  $_SESSION['notif_transId'] = $row['trans_id'];
  $_SESSION['notif_id'] = $row['id'];
  header("Location:view_notification.php");
}
//if seen user notif approval
if (isset($_GET['seenUser'])) {
  $seenID = $_GET['seenUser'];

  $query = "SELECT
         a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
         b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_user,b.notif_count
         from notification_control a
         LEFT join notification b on a.notif_control_id = b.notif_control_id
         where b.id = $seenID ";
   $select = mysqli_query($dbcon,$query);
  $row = mysqli_fetch_array($select);
  $_SESSION['notif_transId'] = $row['trans_id'];
  $_SESSION['notif_id'] = $row['id'];
  header("Location:view_notification.php");
}
//if not seen user return
if (isset($_GET['unseen_return_user'])) {
  $seenID = $_GET['unseen_return_user'];
  $update_query = "UPDATE notification SET seen_user = 2 WHERE id = $seenID";
  mysqli_query($dbcon, $update_query);
  $query = "SELECT
         a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
         b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_user,b.notif_count
         from notification_control a
         LEFT join notification b on a.notif_control_id = b.notif_control_id
         where b.id = $seenID ";
   $select = mysqli_query($dbcon,$query);

  $row = mysqli_fetch_array($select);
  $_SESSION['notif_transId'] = $row['trans_id'];
  $_SESSION['notif_id'] = $row['id'];
  header("Location:view_notification.php");
}
//if seen user return
if (isset($_GET['seen_return_user'])) {
  $seenID = $_GET['seen_return_user'];

  $query = "SELECT
         a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
         b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_user,b.notif_count
         from notification_control a
         LEFT join notification b on a.notif_control_id = b.notif_control_id
         where b.id = $seenID ";
   $select = mysqli_query($dbcon,$query);
  $row = mysqli_fetch_array($select);
  $_SESSION['notif_transId'] = $row['trans_id'];
  $_SESSION['notif_id'] = $row['id'];
  header("Location:view_notification.php");
}
//  if not seen user report discrepancy
if (isset($_GET['userSettleId'])) {
  $seenID = $_GET['userSettleId'];
  $update_query = "UPDATE notification SET seen_user = 2 WHERE id = $seenID";
  mysqli_query($dbcon, $update_query);
  $query = "SELECT
         a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
         b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_user,b.notif_count
         from notification_control a
         LEFT join notification b on a.notif_control_id = b.notif_control_id
         where b.id = $seenID ";
   $select = mysqli_query($dbcon,$query);

  $row = mysqli_fetch_array($select);
  $_SESSION['notif_transId'] = $row['trans_id'];
  $_SESSION['notif_id'] = $row['id'];
  header("Location:view_notification.php");
}

//  if  seen user report discrepancy
if (isset($_GET['seenuserSettleId'])) {
  $seenID = $_GET['seenuserSettleId'];
  $query = "SELECT
         a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
         b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_user,b.notif_count
         from notification_control a
         LEFT join notification b on a.notif_control_id = b.notif_control_id
         where b.id = $seenID ";
   $select = mysqli_query($dbcon,$query);
  $row = mysqli_fetch_array($select);
  $_SESSION['notif_transId'] = $row['trans_id'];
  $_SESSION['notif_id'] = $row['id'];
  header("Location:view_notification.php");
}

// if admin

//if not seen user notif approval
if (isset($_GET['admin_unseen'])) {
  $rid = $_GET['admin_unseen'];

  $update_query = "UPDATE notification SET seen_admin = 1 WHERE id = $rid";
  mysqli_query($dbcon, $update_query);
  $query = "SELECT
         a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
         b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_user,b.notif_count
         from notification_control a
         LEFT join notification b on a.notif_control_id = b.notif_control_id
         where b.id = $rid ";
   $select = mysqli_query($dbcon,$query);
  $row = mysqli_fetch_array($select);
  $_SESSION['notif_transId'] = $row['trans_id'];
  $_SESSION['notif_id'] = $row['id'];
  header("Location:view_notification.php");
}
//if seen user notif approval
if (isset($_GET['admin_seen'])) {
  $seenID = $_GET['admin_seen'];

  $query = "SELECT
         a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
         b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_user,b.notif_count
         from notification_control a
         LEFT join notification b on a.notif_control_id = b.notif_control_id
         where b.id = $seenID ";
   $select = mysqli_query($dbcon,$query);
  $row = mysqli_fetch_array($select);
  $_SESSION['notif_transId'] = $row['trans_id'];
  $_SESSION['notif_id'] = $row['id'];
  header("Location:view_notification.php");
}
 ?>
