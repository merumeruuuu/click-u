<?php
include('config.php');
session_start();
$userID = $_SESSION['user']['user_id'];
$sid = $_SESSION['user']['storage_id'];
if(isset($_POST['view'])){
  //admin
  if ($_SESSION['account_type'] == 1 || $_SESSION['account_type'] == 2) {

    if($_POST["view"] != '')
     {
       $update_query = "UPDATE  notification
                       LEFT JOIN notification_control
                       ON      notification.notif_control_id = notification_control.notif_control_id
                       SET     notification.notif_count = 1
                       WHERE   notification_control.storage_id = $sid and notification.user_id = $userID";
      mysqli_query($dbcon, $update_query);

    }

     $query = "SELECT
            a.notif_control_id,a.trans_id,a.notif_type_id,
            b.id,b.notif_control_id,b.user_id,b.user_notif_type,b.notif_date,b.notif_approved_date,b.seen_admin,b.notif_count
            from notification_control a
            LEFT join notification b on a.notif_control_id = b.notif_control_id
            where b.user_id = $userID and b.user_notif_type = 3 group by b.notif_control_id order by b.id desc limit 5";

     $result = mysqli_query($dbcon, $query);
     $output = '';
     if(mysqli_num_rows($result) > 0)
    {
     while($row = mysqli_fetch_array($result))
     {
       if ($row['notif_type_id'] == 7) {
         if ($row['seen_admin']== 0) {
           $output .= '
           <li>
           <a href="get_notification.php?admin_unseen='.$row["id"].'" >
           <strong>Request For Releasing #'.$row["trans_id"].'</strong><br />
           <strong> <small><em>'.$row["notif_date"].' <i class="fa fa-eye text-success"></i></em></small></strong>
           </a>
           </li>
           ';
         }else {

           $output .= '
           <li class="">
          <a href="get_notification.php?admin_seen='.$row["id"].'" >
          Request For Releasing #'.$row["trans_id"].'<br />
           <small><em>'.$row["notif_date"].' <i class="fa fa-eye-slash text-success"></i></em></small>
           </a>
           </li>
           ';
         }
       }
     }
     $output .= '

     <center><li><a href="view_all_notifications.php" class="text-bold text-italic "style="color:gray;">See all</a></li></center>';
    }
    else{
         $output .= '
         <li><a href="#" class="text-bold text-italic">No Notification Found</a></li>';
    }
    $status_query = "SELECT
                         a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
                         b.notif_control_id,b.user_id,b.user_notif_type,b.notif_date,b.notif_approved_date,b.seen_admin,b.notif_count
                         from notification_control a
                         LEFT join notification b on a.notif_control_id = b.notif_control_id
                         where b.user_id = $userID  and a.notif_type_id = 7 and b.seen_admin = 0 and b.notif_count = 0 and b.user_notif_type = 3 group by b.notif_control_id";

    $result_query = mysqli_query($dbcon, $status_query);
    $count = mysqli_num_rows($result_query);
    $data = array(
      'notification' => $output,
      'unseen_notification'  => $count
    );
    echo json_encode($data);
  }
  //staff notification
if ($_SESSION['account_type'] == 3 || $_SESSION['account_type'] == 4 || $_SESSION['account_type'] == 5) {

  if($_POST["view"] != '')
   {
     $update_query = "UPDATE  notification
                     LEFT JOIN notification_control
                     ON      notification.notif_control_id = notification_control.notif_control_id
                     SET     notification.notif_count = 1
                     WHERE   notification_control.storage_id = $sid and notification.user_id = $userID";
    mysqli_query($dbcon, $update_query);

  }

   $query = "SELECT
          a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
          b.id,b.notif_control_id,b.user_id,b.user_notif_type,b.notif_date,b.notif_approved_date,b.seen_staff,b.notif_count
          from notification_control a
          LEFT join notification b on a.notif_control_id = b.notif_control_id
          where a.storage_id = $sid and b.user_id = $userID and b.user_notif_type = 2 group by b.notif_control_id order by b.id desc limit 5";

   $result = mysqli_query($dbcon, $query);
   $output = '';
   if(mysqli_num_rows($result) > 0)
  {
   while($row = mysqli_fetch_array($result))
   {
     //staff notification for utensil releasing
     if ($row['notif_type_id'] == 1) {
       if ($row['seen_staff']== 0) {
         $output .= '
         <li>
         <a href="get_notification.php?id='.$row["id"].'" >
         <strong>Request For Releasing #'.$row["trans_id"].'</strong><br />
         <strong> <small><em>'.$row["notif_date"].' <i class="fa fa-eye text-success"></i></em></small></strong>
         </a>
         </li>
         ';
       }else {

         $output .= '
         <li class="">
        <a href="get_notification.php?staff_seen_approve='.$row["id"].'" >
        Request For Releasing #'.$row["trans_id"].'<br />
         <small><em>'.$row["notif_date"].' <i class="fa fa-eye-slash text-success"></i></em></small>
         </a>
         </li>
         ';
       }


     }if ($row['notif_type_id'] == 2) {
        //staff notification for utensil receiving unseen
       if ($row['seen_staff']== 0) {
         $output .= '
         <li>
         <a href="get_notification.php?unseen_receive_staff='.$row["id"].'" >
         <strong>Request For Receiving #'.$row["trans_id"].'</strong><br />
         <strong><small ><em>'.$row["notif_date"].' <i class="fa fa-eye text-info"></i></em></small></strong><br />
         </a>
         </li>
         ';
         //seen received
       }else {
       $output .= '
       <li>
       <a href="get_notification.php?seen_received_staff='.$row["id"].'">
      Request For Receiving #'.$row["trans_id"].'<br />
       <small><em >'.$row["notif_date"].' <i class="fa fa-eye-slash text-info"></i></em></small>
       </a>
       </li>


       ';
     }

   }
   }
   $output .= '

   <center><li><a href="view_all_notifications.php" class="text-bold text-italic "style="color:gray;">See all</a></li></center>';
  }
  else{
       $output .= '
       <li><a href="#" class="text-bold text-italic">No Notification Found</a></li>';
  }
  $status_query = "SELECT
                       a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
                       b.notif_control_id,b.user_id,b.user_notif_type,b.notif_date,b.notif_approved_date,b.seen_staff,b.notif_count
                       from notification_control a
                       LEFT join notification b on a.notif_control_id = b.notif_control_id
                       where a.storage_id = $sid and b.user_id = $userID  and a.notif_type_id <=2 and b.seen_staff = 0 and b.notif_count = 0 and b.user_notif_type = 2 group by b.notif_control_id";

  $result_query = mysqli_query($dbcon, $status_query);
  $count = mysqli_num_rows($result_query);
  $data = array(
    'notification' => $output,
    'unseen_notification'  => $count
  );
  echo json_encode($data);
}
//user
if ($_SESSION['account_type'] == 6 || $_SESSION['account_type'] == 7) {


  if($_POST["view"] != '')
   {
    $update_query = "UPDATE notification SET notif_count = 1 WHERE user_id = $userID";
    mysqli_query($dbcon, $update_query);
  }

  $query = "SELECT
         a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
         b.id,b.notif_control_id,b.user_id,b.user_notif_type,b.notif_date,b.notif_approved_date,b.seen_user,b.notif_count
         from notification_control a
         LEFT join notification b on a.notif_control_id = b.notif_control_id
         where  b.user_id = $userID and b.user_notif_type = 1 group by b.notif_control_id order by b.id desc limit 5";

  $result = mysqli_query($dbcon, $query);
  $output = '';
  if(mysqli_num_rows($result) > 0 )
 {
  while($row = mysqli_fetch_array($result))
  {

    if (!empty($row['notif_date']) && $row['notif_type_id'] == 1 ) {
      if ($row['seen_user']== 1 ) {
        $output .= '
        <li>
        <a href="get_notification.php?userApproveId='.$row["id"].'">
        <strong>Request  #'.$row["trans_id"].' has been approved! </strong><br />
        <strong> <small><em>'.$row["notif_date"].' <i class="fa fa-eye text-success"></i></em></small></strong>
        </a>
        </li>
        ';
      }if( $row['seen_user']== 2 || $row['seen_user']== 3)  {

        $output .= '
        <li class="">
         <a href="get_notification.php?seenUser='.$row["id"].'">
       Request #'.$row["trans_id"].' has been approved!<br />
        <small><em>'.$row["notif_date"].' <i class="fa fa-eye-slash text-success"></i></em></small>
        </a>
        </li>
        ';
      }
    }if (!empty($row['notif_approved_date']) && $row['notif_type_id'] == 2) {
      if ($row['seen_user']== 1) {
        $output .= '
        <li>
        <a href="get_notification.php?unseen_return_user='.$row["id"].'">
        <strong>Items from Request  #'.$row["trans_id"].' has been returned! </strong><br />
        <strong> <small><em>'.$row["notif_date"].' <i class="fa fa-eye text-info"></i></em></small></strong>
        </a>
        </li>
        ';
      }if ($row['seen_user']== 2){

        $output .= '
        <li class="">
       <a href="get_notification.php?seen_return_user='.$row["id"].'">
       Items from request #'.$row["trans_id"].' has been returned!<br />
        <small><em>'.$row["notif_date"].' <i class="fa fa-eye-slash text-info"></i></em></small>
        </a>
        </li>
        ';
      }
    }if (!empty($row['notif_date'])  && $row['notif_type_id'] == 3){
      if ($row['seen_user']== 1) {
        $output .= '
        <li>
        <a href="get_notification.php?userSettleId='.$row["id"].'">
        <strong>Please settle your request  (Request  #'.$row["trans_id"].')  </strong><br />
        <strong> <small><em>'.$row["notif_date"].' <i class="fa fa-eye text-danger"></i></em></small></strong>
        </a>
        </li>
        ';
      }if ($row['seen_user']== 2){

        $output .= '
        <li class="">
       <a href="get_notification.php?seenuserSettleId='.$row["id"].'">
       Please settle your request request (Request  #'.$row["trans_id"].') <br />
        <small><em>'.$row["notif_date"].' <i class="fa fa-eye-slash text-danger"></i></em></small>
        </a>
        </li>
        ';
      }
    }if(!empty($row['notif_approved_date']) && $row['notif_type_id'] == 4) {
      if ($row['seen_user']== 1) {
        $output .= '
        <li>
        <a href="get_notification.php?userSettleId='.$row["id"].'">
        <strong> Your request (Request  #'.$row["trans_id"].') has been settled! </strong><br />
        <strong> <small><em>'.$row["notif_approved_date"].' <i class="fa fa-eye text-success"></i></em></small></strong>
        </a>
        </li>
        ';
      }if ($row['seen_user']== 2 ){

        $output .= '
        <li class="">
       <a href="get_notification.php?seenuserSettleId='.$row["id"].'">
       Your request (Request  #'.$row["trans_id"].') has been settled! <br />
        <small><em>'.$row["notif_approved_date"].' <i class="fa fa-eye-slash text-success"></i></em></small>
        </a>
        </li>
        ';
      }
    }if ($row['notif_type_id'] == 8) {
      if ($row['seen_user']== 1) {
        $output .= '
        <li>
        <a href="get_notification.php?userSettleId='.$row["id"].'">
        <strong> Your request (Request  #'.$row["trans_id"].') has been denied! </strong><br />
        <strong> <small><em>'.$row["notif_date"].' <i class="fa fa-eye text-danger"></i></em></small></strong>
        </a>
        </li>
        ';
      }if ($row['seen_user']== 2 ){
        $output .= '
        <li class="">
       <a href="get_notification.php?seenuserSettleId='.$row["id"].'">
       Your request (Request  #'.$row["trans_id"].') has been denied! <br />
        <small><em>'.$row["notif_date"].' <i class="fa fa-eye-slash text-danger"></i></em></small>
        </a>
        </li>
        ';
      }
    }

  }
  $output .= '

  <center><li><a href="view_all_notifications.php" class="text-bold text-italic"style="color:gray;">See all</a></li></center>';
 }
 else{
      $output .= '
      <li><a href="#" class="text-bold text-italic">No Notification Found</a></li>';
 }
 $status_query = "SELECT
        a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
        b.id,b.notif_control_id,b.user_id,b.user_notif_type,b.notif_date,b.notif_approved_date,b.seen_user,b.notif_count
        from notification_control a
        LEFT join notification b on a.notif_control_id = b.notif_control_id
        where  b.user_id = $userID and b.user_notif_type = 1 and b.seen_user = 1 and b.notif_count = 0 group by b.notif_control_id order by b.id desc";
 $result_query = mysqli_query($dbcon, $status_query);
 $count = mysqli_num_rows($result_query);
 $data = array(
   'notification' => $output,
   'unseen_notification'  => $count
 );
 echo json_encode($data);
}
}
?>
