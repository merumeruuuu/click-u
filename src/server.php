<?php require('config.php');
session_start();

$errors= array();
$success= array();
//insertion of inventory master
$checkInventoryControl = mysqli_query($dbcon,"SELECT * FROM inventory where date_added = CURRENT_DATE() order by inventory_control_id desc limit 1")or die(mysqli_error($dbcon));
foreach ($checkInventoryControl as $key => $inventoryControl);
if (mysqli_num_rows($checkInventoryControl)<=0) {
 $insertInventory = mysqli_query($dbcon, "INSERT INTO inventory (date_added)
 VALUES(NOW())") or die(mysqli_error($dbcon));
 $selectFromInventory = mysqli_query($dbcon,"SELECT * FROM inventory order by inventory_control_id desc limit 1");
 foreach ($selectFromInventory as $key => $inventory);
 $utensils = mysqli_query($dbcon,"SELECT * from utensils order by utensils_id");
  foreach ($utensils as $key => $utensilz) {
  $insertToInventoryDaily = mysqli_query($dbcon,"INSERT INTO inventory_all_record (inventory_control_id,utensils_id,original_stock,remain_stock)
  VALUES ('".$inventory['inventory_control_id']."','".$utensilz['utensils_id']."','".$utensilz['original_stock']."','".$utensilz['stock_on_hand']."' )");
}
//insert inventory storage details

$utnsilStorageStock = mysqli_query($dbcon,"SELECT * from storage_stocks "); //where original_stock !=0
  foreach ($utnsilStorageStock as $key => $sStock) {
  $insertInventoryDetails = mysqli_query($dbcon,"INSERT INTO inventory_storage (inventory_control_id,utensils_id,storage_id,original_stock,stock_remain,lost_qty,damaged_qty,reserved_qty,on_use)
  values('".$inventory['inventory_control_id']."','".$sStock['utensils_id']."',
    '".$sStock['storage_id']."','".$sStock['original_stock']."','".$sStock['storage_qty']."',
    '".$sStock['lost_qty']."','".$sStock['damaged_qty']."','".$sStock['reserved_qty']."','".$sStock['on_use']."')");
    }
}else {

$selectFrmInventory = mysqli_query($dbcon,"SELECT * FROM inventory order by inventory_control_id desc limit 1");
foreach ($selectFrmInventory as $key => $inventory);
//check for new items
$checkForNewItems = mysqli_query($dbcon,"SELECT count(utensils_id)as newItems from utensils order by utensils_id");
foreach ($checkForNewItems as $key => $newItems);
$checkFfromInventory = mysqli_query($dbcon,"SELECT count(utensils_id)as currentInventory from inventory_all_record where inventory_control_id = '".$inventory['inventory_control_id']."'");
foreach ($checkFfromInventory as $key => $newInventory);
if ($newItems['newItems']==$newInventory['currentInventory']) {
//if same items just update
 $utensils = mysqli_query($dbcon,"SELECT * from utensils where  stock_on_hand >0");
  foreach ($utensils as $key => $utensilz) {
  $updateUtensilInventory = mysqli_query($dbcon,"UPDATE inventory_all_record set original_stock = '".$utensilz['original_stock']."',remain_stock = '".$utensilz['stock_on_hand']."'
  where utensils_id = '".$utensilz['utensils_id']."' and inventory_control_id = '".$inventory['inventory_control_id']."'");
}
//update inventory storage
$checkForNewStorageItems = mysqli_query($dbcon,"SELECT count(utensils_id)as newItemsS from storage_stocks order by utensils_id");
foreach ($checkForNewStorageItems as $key => $newItemsS);
$checkFfromStorageInventory = mysqli_query($dbcon,"SELECT count(utensils_id)as currentInventoryS from inventory_storage where inventory_control_id = '".$inventory['inventory_control_id']."'");
foreach ($checkFfromStorageInventory as $key => $newInventoryS);
if ($newItemsS['newItemsS']==$newInventoryS['currentInventoryS']) {
$utensilStorageStock = mysqli_query($dbcon,"SELECT * from storage_stocks ");//where original_stock !=0
  foreach ($utensilStorageStock as $key => $sStock) {
    $updateStorageInventory = mysqli_query($dbcon,"UPDATE inventory_storage set original_stock = '".$sStock['original_stock']."',stock_remain = '".$sStock['storage_qty']."',
      lost_qty = '".$sStock['lost_qty']."',damaged_qty = '".$sStock['damaged_qty']."',reserved_qty = '".$sStock['reserved_qty']."',on_use = '".$sStock['on_use']."'
    where inventory_control_id = '".$inventory['inventory_control_id']."'and utensils_id = '".$sStock['utensils_id']."' and storage_id = '".$sStock['storage_id']."'  ");
    }
  }else {
    //remove old inventory storage
    $removeOld = mysqli_query($dbcon,"DELETE FROM inventory_storage where inventory_control_id = '".$inventory['inventory_control_id']."'");
    //copy new inventory record storage
    $newUtensils = mysqli_query($dbcon,"SELECT * from storage_stocks order by utensils_id");
     foreach ($newUtensils as $key => $sStock) {
       $insertInventoryDetails = mysqli_query($dbcon,"INSERT INTO inventory_storage (inventory_control_id,utensils_id,storage_id,original_stock,stock_remain,lost_qty,damaged_qty,reserved_qty,on_use)
       values('".$inventory['inventory_control_id']."','".$sStock['utensils_id']."',
         '".$sStock['storage_id']."','".$sStock['original_stock']."','".$sStock['storage_qty']."',
         '".$sStock['lost_qty']."','".$sStock['damaged_qty']."','".$sStock['reserved_qty']."','".$sStock['on_use']."')");
  }
}
}else {
 //remove old inventory
 $removeOld = mysqli_query($dbcon,"DELETE FROM inventory_all_record where inventory_control_id = '".$inventory['inventory_control_id']."'");
 //copy new inventory record
 $newUtensils = mysqli_query($dbcon,"SELECT * from utensils order by utensils_id");
  foreach ($newUtensils as $key => $Nutensilz) {
  $insertToInventoryDaily = mysqli_query($dbcon,"INSERT INTO inventory_all_record (inventory_control_id,utensils_id,original_stock,remain_stock)
  VALUES ('".$inventory['inventory_control_id']."','".$Nutensilz['utensils_id']."','".$Nutensilz['original_stock']."','".$Nutensilz['stock_on_hand']."' )");
}
}
} //end of inventory function
?>

 <?php
   //NEW ARRIVAL APPROVAL
 if(isset($_GET['confirm']))
 {

   $ids = $_GET['confirm'];
   $_SESSION['user']['user_id'];
   $dean = $_SESSION['user']['user_id'];
   $query = mysqli_query($dbcon,"SELECT * FROM new_arrival_utensils where new_arvl_id = $ids");
   $check = mysqli_fetch_array($query);
   $stock = $check['new_arvl_qty'];
   $uID = $check['utensils_id'];

  // $res = mysqli_query($dbcon,"UPDATE `utensils` SET `stock_on_hand`='$stock' WHERE `utensils`.`utensils_id`= $uID");
  $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage order by storage_id ");
  foreach ($fetchStorage as $key => $value) {
    $insertToStorage = mysqli_query($dbcon,"INSERT INTO storage_stocks (utensils_id,storage_id,storage_qty,original_stock,reserved_qty,lost_qty,damaged_qty,on_use)
    values('$uID','".$value['storage_id']."',0,0,0,0,0,0)")or die("details: ".mysqli_error($dbcon));
  }
  $fetchNewStock = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = $uID and storage_id = 1");
  foreach ($fetchNewStock as $key => $newStock);
  $original_stock = $newStock['original_stock'] + $stock;
  $storageQty = $newStock['storage_qty'] + $stock;
  $updateStats = mysqli_query($dbcon,"UPDATE utensils set status = 1 where utensils_id = $uID");
  $updateQTY = mysqli_query($dbcon,"UPDATE storage_stocks set original_stock = $original_stock,storage_qty = $storageQty where utensils_id = $uID and storage_id = 1");
  $result = mysqli_query($dbcon,"UPDATE `new_arrival_utensils` SET `approved_by`='$dean',`date_approved`= NOW(),`status` = '1' WHERE `new_arrival_utensils`.`new_arvl_id`= $ids");
      // echo "<script>alert('Approved!');window.location.href='new_arrival_admin_approval.php';</script>";
      header("Location:new_arrival_admin_approval.php");
 }
 ?>

 <?php
   //MOVE Utensils
 if(isset($_POST['move_to']))
 {
   $utensils_id = mysqli_real_escape_string($dbcon, $_POST['utensils_id']);
   $new_qty_input = mysqli_real_escape_string($dbcon, $_POST['qty']);
   $new_arrival = mysqli_real_escape_string($dbcon, $_POST['new_arrival']);
   $storage = mysqli_real_escape_string($dbcon, $_POST['storage']);

   $utensils_check = mysqli_query($dbcon,"SELECT * FROM utensils WHERE utensils_id='$utensils_id' and stock_on_hand < $new_qty_input");

    if (mysqli_num_rows($utensils_check)>0) {
      array_push($errors,"Invalid quantity!");
    }else {

   $storage_check = mysqli_query($dbcon,"SELECT * FROM storage_stocks WHERE storage_id = $storage and utensils_id = $utensils_id");
   $s_check = mysqli_fetch_array($storage_check);
  if (mysqli_num_rows($storage_check)>0) {
    $replace_qty = $s_check['storage_qty'] + $new_qty_input;
    $replace_orig_stock = $s_check['original_stock'] + $new_qty_input;
    $res = mysqli_query($dbcon,"UPDATE `storage_stocks` SET `storage_qty`='$replace_qty',`original_stock`='$replace_orig_stock' WHERE `storage_stocks`.`utensils_id`='$utensils_id' AND`storage_stocks`.`storage_id`='$storage'");
  }else {

    $storages = mysqli_query($dbcon,"SELECT * FROM storage order by storage_id");
    $value = 0;
    while ($check = mysqli_fetch_array($storages)) {
      $insertstoragestocks = mysqli_query($dbcon,"INSERT INTO storage_stocks(utensils_id,storage_id,storage_qty,original_stock,reserved_qty,lost_qty,damaged_qty,on_use)
      values('$utensils_id','".$check['storage_id']."',$value,$value,$value,$value,$value,$value)")or die("details: ".mysqli_error($dbcon));
    }
    $storage_check1 = mysqli_query($dbcon,"SELECT * FROM storage_stocks WHERE storage_id = $storage and utensils_id = $utensils_id");
    $s_check1 = mysqli_fetch_array($storage_check1);
    $replace_qty = $s_check1['storage_qty'] + $new_qty_input;
    $replace_orig_stock = $s_check1['original_stock'] + $new_qty_input;
    $updateStocks = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $replace_qty,original_stock =  $replace_orig_stock where utensils_id = $utensils_id and storage_id = $storage");
  }

   $new_qty = $new_arrival - $new_qty_input;
   $query = mysqli_query($dbcon,"SELECT * FROM utensils where utensils_id='$utensils_id'");
   $row = mysqli_fetch_array($query);
   $stock_on_hand = $row['stock_on_hand'] - $new_qty_input;
   $original_stock = $row['original_stock'] + $new_arrival;


  $res = mysqli_query($dbcon,"UPDATE `utensils` SET `stock_on_hand`='$stock_on_hand',`status`='1' WHERE `utensils`.`utensils_id`='$utensils_id'");
  $res2 = mysqli_query($dbcon,"UPDATE `new_arrival_utensils` SET `new_arvl_qty`='$new_qty' WHERE `new_arrival_utensils`.`utensils_id`='$utensils_id'");
// echo "<script>alert('Moved!');window.location.href='newarrival.php';</script>";
    array_push($success,"Successfully moved!");
    // header("Location: move_to_storages.php");
}

 }
 ?>
<?php
if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){
//staff storage selection
  if ($_REQUEST['action'] == 'selectStorageForStaff' && !empty($_REQUEST['id'])) {
    $_SESSION['staff_storage'] = $_REQUEST['id'];
    header("Location: borrow_requests.php");
  }

  //approve Requests
  // if ($_REQUEST['action'] == 'approveRequest' && !empty($_REQUEST['id'])) {
  //   $reqID = $_REQUEST['id'];
  //
  //  $query = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $reqID");
  //  $check = mysqli_fetch_array($query);
  //  if ($check['status']<2) {
  //    $query1 = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $reqID");
  //    while ($check = mysqli_fetch_array($query1)) {
  //      $utensilID = $check['utensils_id'];
  //      $storageID = $check['storage_id'];
  //      $requestQty = $check['qty'];
  //      $reservedtQty = $check['reserved_qty'];
  //
  //      $query2 = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = $utensilID and storage_id = $storageID");
  //      while ($rows = mysqli_fetch_array($query2)) {
  //        $storageQty = $rows['storage_qty'];
  //
  //        $deduct = $reservedtQty - $requestQty;
  //        $staff = $_SESSION['user']['user_id'];
  //        // $updateStocks = mysqli_query($dbcon,"UPDATE storage_stocks SET storage_qty = $deductStorageQty where utensils_id = $utensilID and storage_id = $storageID");
  //        $updateStatus = mysqli_query($dbcon,"UPDATE borrower_slip SET date_approved = NOW(),aprvd_n_rlsd_by = $staff, status = 2 where borrower_slip_id = $reqID");
  //        $updateItemOnUse = mysqli_query($dbcon,"UPDATE borrower_slip_details SET on_use = $requestQty,reserved_qty = $deduct where borrower_slip_id = $reqID and utensils_id = $utensilID and storage_id = $storageID");
  //        $updateNotification = mysqli_query($dbcon,"UPDATE notification set seen_user  = 1,seen_staff = 2,notif_approved_date = NOW(),notif_count = 1 where trans_id = $reqID");
  //      header("location: borrow_requests.php");
  //
  // }
  //   }
  //   }else {
  //          echo "<script>alert('Failed! Request maybe cancelled or approved by the other user!');window.location.href='borrow_requests.php';</script>";
  //     }
  // }
  // receive borrowed Items
  if ($_REQUEST['action'] == 'receiveItems' && !empty($_REQUEST['id'])) {
       $receiveID = $_REQUEST['id'];

     $query = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $receiveID");
     while ($check = mysqli_fetch_array($query)) {
       $utensilID1 = $check['utensils_id'];
       $storageID1 = $check['storage_id'];
       $requestQty1 = $check['qty'];
       $onUseQty = $check['on_use'];

       $query3 = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = $utensilID1 and storage_id = $storageID1");
       while ($rows = mysqli_fetch_array($query3)) {
         $storageQty1 = $rows['storage_qty'];

         $addStorageQty = $storageQty1 + $requestQty1;
         $updateOnUse =  $onUseQty - $requestQty1;
         $staff1 = $_SESSION['user']['user_id'];
         $updateStocks1 = mysqli_query($dbcon,"UPDATE storage_stocks SET storage_qty = $addStorageQty where utensils_id = $utensilID1 and storage_id = $storageID1");
         $updateStatus = mysqli_query($dbcon,"UPDATE borrower_slip SET date_received = NOW(), received_by = $staff1, status = 5 where borrower_slip_id = $receiveID");
         $updateStatus1 = mysqli_query($dbcon,"UPDATE borrower_slip_details SET on_use =  $updateOnUse where utensils_id = $utensilID1 and storage_id = $storageID1 ");
         $updateNotif = mysqli_query($dbcon,"UPDATE notification set seen_user = 1,notif_count = 1 where trans_id = $receiveID and notif_type_id = 2");
         $updateNotif2 = mysqli_query($dbcon,"UPDATE notification set seen_user = 4,notif_count = 1 where trans_id = $receiveID and notif_type_id = 1");
       header("location: returnRequest.php");

       }
    }

  }

 //session request id for modifying user Requests
     if ($_GET['action'] == 'modify' && !empty($_GET['id'])) {

       $reqID = $_GET['id'];
      $query = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $reqID");
      $check = mysqli_fetch_array($query);
      if ($check['status']<=1) {
        $_SESSION['modify_id'] = $_GET['id'];
        unset($_SESSION['item_tray']);
        $check_slip = "SELECT
                       a.borrower_slip_id,a.group_id,
                       b.group_id,b.group_name,b.group_leader_id,
                       c.group_id,c.user_id,
                       d.user_id,d.school_id,d.fname,d.lname

                       from borrower_slip a
                       left join group_table b on a.group_id = b.group_id
                       left join group_members c on b.group_id = c.group_id
                       left join users d on c.user_id = d.user_id

                       where a.borrower_slip_id = $reqID";
          $res = mysqli_query($dbcon,$check_slip);
           foreach ($res as $key => $value) {
             $new_members = array(
               'member_ID'           =>     $value["user_id"],
               'member_school_ID'    =>     $value["school_id"],
               'member_lname'        =>     $value["lname"],
               'member_fname'        =>      $value["fname"]
  					 );
             $_SESSION['new_group_members'][$key] = $new_members;
             $_SESSION['group_name'] = $value['group_name'];
             $_SESSION['group_leader2'] = $value['group_leader_id'];
             $_SESSION['group_id_mod'] = $value['group_id'];
           }
         header("location: modifyUserRequests.php");
      }else {
        echo "<script>alert('You cannot modify this request!');window.location.href='userRequestsMenu2.php';</script>";
      }

}
//session request id for staff in managing user Requests
      if ($_REQUEST['action'] == 'manageRequest' && !empty($_REQUEST['id'])) {

            $_SESSION['manage_id'] = $_REQUEST['id'];
            $_SESSION['item'] = 0;
      unset($_SESSION['discrepancy_tray']);
      header("location: manageUtensilRequest.php");

           }

//remove an item from approved/pending request
if($_REQUEST['action'] == 'removeAndModifyItem' && !empty($_REQUEST['id'])){
$user = $_SESSION['user']['user_id'];
  $userType = $_SESSION['account_type'];

  $ID = $_REQUEST['id'];
  $check1 = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where bsd_id = $ID");
  $item = mysqli_fetch_array($check1);
  $itemID = $item['utensils_id'];
  $itemQty = $item['qty'];
  $storageID = $item['storage_id'];
  $bID = $item['borrower_slip_id'];

  $check2 = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = $itemID and storage_id = $storageID");
  $storage = mysqli_fetch_array($check2);
  $storageQty = $storage['storage_qty'];

  $newStock = $itemQty + $storageQty; // for user
  $newReserved = $storage['reserved_qty'] - $itemQty; // for user

  $newStockS = $itemQty + $storageQty; // for staff
  $newOn_use = $storage['on_use'] - $itemQty; // for staff

if ($userType == "3"||$userType == "4"||$userType == "5") {

  $update = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStock,on_use = $newOn_use WHERE utensils_id = $itemID and storage_id = $storageID");
  $remove = mysqli_query($dbcon,"DELETE FROM borrower_slip_details where bsd_id = $ID");

  //inser history modification staff
  $historyType = 2;
  $insertHistorystaff = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
  values (NOW(),'$user','$bID','$storageID','$historyType')");
  header("Location:modifyUserRequests.php");
}if ($userType == "6"||$userType == "7") {
  //insert history for modification

  $update = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStock,reserved_qty = $newReserved WHERE utensils_id = $itemID and storage_id = $storageID");
  $remove = mysqli_query($dbcon,"DELETE FROM borrower_slip_details where bsd_id = $ID");

  $checkHistory = mysqli_query($dbcon,"SELECT * FROM history where trans_id = $bID and user_id = $user and history_type_id = 3");
  if (mysqli_num_rows($checkHistory)>0) {
    $updateHistory = mysqli_query($dbcon,"UPDATE history set date_added = NOW() where trans_id = $bID and user_id = $user and history_type_id = 3");
  }else {
    $historyType = 3;
    $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
    values (NOW(),'$user','$bID','$storageID','$historyType')");
  }
  header("Location:modifyUserRequests.php");
}

}

}

 ?>

 <?php
//user cancel requests
if (isset($_GET['cancel'])) {
  $ID = $_GET['cancel'];
  $_SESSION['user']['user_id'];
  $user = $_SESSION['user']['user_id'];

  $query1 = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $ID");
  $check1 = mysqli_fetch_array($query1);
if ($check1['status']==0||$check1['status']==1) {
  $query = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $ID");
  while ($check = mysqli_fetch_array($query)) {
    $utensilID1 = $check['utensils_id'];
    $storageID1 = $check['storage_id'];
    $requestQty1 = $check['qty'];
    $reservedtQty = $check['reserved_qty'];

  $query3 = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = $utensilID1 and storage_id = $storageID1");
  while ($rows = mysqli_fetch_array($query3)) {
    $storageQty1 = $rows['storage_qty'];

    $addStorageQty = $storageQty1 + $requestQty1;
    $updateReserved =  $reservedtQty - $requestQty1;
    $reservedtQtyS = $rows['reserved_qty'] - $reservedtQty;
    $staff1 = $_SESSION['user']['user_id'];
    $updateStocks1 = mysqli_query($dbcon,"UPDATE storage_stocks SET storage_qty = $addStorageQty,reserved_qty = $reservedtQtyS where utensils_id = $utensilID1 and storage_id = $storageID1");
    $update = mysqli_query($dbcon,"UPDATE `borrower_slip` SET `date_cancelled`= NOW(),`date_modified` = NOW(),`modified_by` = $user,`status`= 3 WHERE `borrower_slip`.`borrower_slip_id`= $ID");
    $updateStatus1 = mysqli_query($dbcon,"UPDATE borrower_slip_details SET reserved_qty =  $updateReserved where utensils_id = $utensilID1 and storage_id = $storageID1 ");
    header("Location:userRequestsMenu2.php");
  }
  }
  //insert history
  $historyType = 2;
  $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
  values (NOW(),'$user','$ID','$storageID1','$historyType')");


}if ($check1['status']==3) {
   echo "<script>alert('Failed! The request has been cancelled already by a member!');window.location.href='userRequestsMenu2.php';</script>";
}if ($check1['status']==2) {
  echo "<script>alert('Failed! The request has been approved already!');window.location.href='userRequestsMenu2.php';</script>";
}if ($check1['status']==9) {
  echo "<script>alert('Failed! The request has been denied!');window.location.href='userRequestsMenu2.php';</script>";
}



}
//user return request
if (isset($_GET['return'])) {
  $ID = $_GET['return'];
  $_SESSION['user']['user_id'];
  $user = $_SESSION['user']['user_id'];
  $forReceiving = 'To receive..';
  $update = mysqli_query($dbcon,"UPDATE `borrower_slip` SET `date_modified`= NOW(),`received_by`= '$forReceiving' WHERE `borrower_slip`.`borrower_slip_id`= $ID");
  $sql = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $ID");
  $row = mysqli_fetch_assoc($sql);

  //
  $notifType1 = 1;
  $notifType2 = 2;
  $insertControl = mysqli_query($dbcon,"INSERT INTO notification_control (trans_id,notif_type_id,storage_id)values('$ID','$notifType2','".$row['storage_id']."')") or die(mysqli_error($dbcon));
  $getControl = mysqli_query($dbcon,"SELECT * FROM notification_control where trans_id = '$ID' and notif_type_id = 2");
  $control = mysqli_fetch_array($getControl);
  $queryGroup = mysqli_query($dbcon,"SELECT * FROM group_members where group_id = '".$row['group_id']."'");
  while ($members = mysqli_fetch_array($queryGroup)) {
//insert user members
  $insertNotification = mysqli_query($dbcon,"INSERT INTO notification (notif_control_id,user_id,user_notif_type,notif_date)
  values ('".$control['notif_control_id']."','".$members['user_id']."','$notifType1',NOW())") or die(mysqli_error($dbcon));

 $checkStaffList = mysqli_query($dbcon,"SELECT * FROM user_settings where account_type_id = 3 || account_type_id = 4 || account_type_id = 5");
 while ($staff_ids = mysqli_fetch_array($checkStaffList)) {
//insert staff id
   $insertNotificationStaff = mysqli_query($dbcon,"INSERT INTO notification (notif_control_id,user_id,user_notif_type,notif_date)
   values ('".$control['notif_control_id']."','".$staff_ids['user_id']."','$notifType2',NOW())") or die(mysqli_error($dbcon));
 }

}
  $update_query = "UPDATE  notification
                  LEFT JOIN notification_control
                  ON      notification.notif_control_id = notification_control.notif_control_id
                  SET     notification.seen_user = 3
                  WHERE  notification.user_notif_type = 1 and notification_control.trans_id = $ID and notification_control.notif_type_id = 1 ";
 mysqli_query($dbcon, $update_query);
   header("Location:userRequestsMenu2.php");
}
  ?>

  <?php
  //add items with discrepancy
//      if (isset($_POST['add_discrepancy'])) {
//       $errors = array();
//       $requsestID = mysqli_real_escape_string($dbcon,$_POST['requestID']);
//       $itemID = mysqli_real_escape_string($dbcon,$_POST['itemID']);
//       $lost = mysqli_real_escape_string($dbcon,$_POST['lost']);
//       $damaged = mysqli_real_escape_string($dbcon,$_POST['damaged']);
//       $note = mysqli_real_escape_string($dbcon,$_POST['note']);
//       $compare = $lost + $damaged;
// if ($itemID!=0) {
//       $query_items = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $requsestID and utensils_id = $itemID");
//       $item_disc = mysqli_fetch_assoc($query_items);
//       $query_check = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = $requsestID and utensils_id = $itemID");
//
//       $reqQty = $item_disc['qty'];
//       $user = $_SESSION['user']['user_id'];
//    if (!empty($lost)||!empty($damaged)) {
//      if (mysqli_num_rows($query_check)<1) {
//        if ($compare > $reqQty || $lost < 0 || $damaged < 0 || $compare < 0) {
//          array_push($errors, "Invalid (Lost/Damaged) quantity !");
//        }else {
//           $insert = mysqli_query($dbcon,"INSERT INTO breakages_and_damages (borrower_slip_id,utensils_id,lost_qty,damaged_qty,reported_by,note,date_reported)
//                                          values('$requsestID','$itemID','$lost','$damaged','$user','$note',NOW())");
//           $_SESSION['item'] = 0;
//           header("Location:manageUtensilRequest.php");
//        }
//      }else {
//       array_push($errors, "Duplicate entry !");
//      }
//    }else {
//       array_push($errors, "Please input quantity !");
//
//    }
// }else {
//   array_push($errors, "Please select an item !");
// }
//      }
   ?>
   <?php
//submit items with discrepancy report
if (isset($_GET['add_report'])) {
   $user = $_SESSION['user']['user_id'];
    $reportID = $_GET['add_report'];
   $query_discrepancy = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = $reportID");
   if (mysqli_num_rows($query_discrepancy)<1) {

      foreach (array_filter($_SESSION['discrepancy_tray']) as $key => $discrep) { // insert discrepancies
        $insertDisc = mysqli_query($dbcon,"INSERT INTO breakages_and_damages (borrower_slip_id,utensils_id,lost_qty,damaged_qty,reported_by,note,date_reported)
        values('$reportID','".$discrep['item_id']."','".$discrep['lost_qty']."','".$discrep['damaged_qty']."','$user','".$discrep['note']."',NOW())");
     }
  $breakagesQuery = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = $reportID");
  foreach ($breakagesQuery as $key => $breaks) {

   $query_request_details = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $reportID and utensils_id = '".$breaks['utensils_id']."'");
     foreach ($query_request_details as $key => $reqst) {
       $combinedQty = $breaks['lost_qty'] + $breaks['damaged_qty'];
       $returnQty = $reqst['qty'] - $combinedQty;
       $updateRdetails = mysqli_query($dbcon,"UPDATE borrower_slip_details set on_use = 0,returned = $returnQty,date_returned = NOW(),remarks = 1
        where borrower_slip_id = $reportID and utensils_id = '".$reqst['utensils_id']."'"); // update request with discrepancies

   $queryStor = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = '".$reqst['utensils_id']."' and storage_id = '".$reqst['storage_id']."'");
   foreach ($queryStor as $key => $storDisc) {

      $newStockDisc = $storDisc['storage_qty'] + $returnQty;
      $newStorOnUse = $storDisc['on_use'] - $reqst['qty'];
      $lost_qtyS = $storDisc['lost_qty'] + $breaks['lost_qty'];
      $damaged_qtyS = $storDisc['damaged_qty'] + $breaks['damaged_qty'];



       $updateStorageRdetails = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStockDisc,on_use = $newStorOnUse,lost_qty = $lost_qtyS,damaged_qty = $damaged_qtyS
        where  utensils_id = '".$storDisc['utensils_id']."'and storage_id = '".$storDisc['storage_id']."'"); // update storage with discrepancies


      $update = mysqli_query($dbcon,"UPDATE borrower_slip set status = 6 where borrower_slip_id = $reportID");
    }
  } // for discrepancies end of loop

      // $querExemption = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $reportID and utensils_id != '".$breaks['utensils_id']."' and remarks = 0");
      // while ( $except1 = mysqli_fetch_array($querExemption)) {
      //
      //   $updateDetailsExm = mysqli_query($dbcon,"UPDATE borrower_slip_details set on_use = 0,returned = '".$except1['qty']."',date_returned = NOW() where borrower_slip_id = $reportID and utensils_id = '".$except1['utensils_id']."' and remarks = 0");
      //
      // $storageExmption = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = '".$except1['utensils_id']."' and storage_id = '".$except1['storage_id']."'");
      // while ( $exceptstorage = mysqli_fetch_array($storageExmption)) {
      //   $newStockqty = $exceptstorage['storage_qty'] + $except1['qty'];
      //   $onUseQty = $exceptstorage['on_use'] - $except1['qty'];
      //
      // $updateStorage = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStockqty,on_use = $onUseQty where utensils_id = '".$except1['utensils_id']."' and storage_id = '".$except1['storage_id']."'");
      //
      //
      //   }
      // }
   // code...
 }

 $querExemption = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $reportID and remarks = 0");
 while ( $except1 = mysqli_fetch_array($querExemption)) {

   $updateDetailsExm = mysqli_query($dbcon,"UPDATE borrower_slip_details set on_use = 0,returned = '".$except1['qty']."',date_returned = NOW() where borrower_slip_id = $reportID and utensils_id = '".$except1['utensils_id']."' and remarks = 0");

 $storageExmption = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = '".$except1['utensils_id']."' and storage_id = '".$except1['storage_id']."'");
 while ( $exceptstorage = mysqli_fetch_array($storageExmption)) {
   $newStockqty = $exceptstorage['storage_qty'] + $except1['qty'];
   $onUseQty = $exceptstorage['on_use'] - $except1['qty'];

 $updateStorage = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStockqty,on_use = $onUseQty where utensils_id = '".$except1['utensils_id']."' and storage_id = '".$except1['storage_id']."'");

   }
 }

  //insert notification
  $insertControl = mysqli_query($dbcon,"INSERT INTO notification_control (trans_id,notif_type_id,storage_id)
  values ('$reportID','3','".$newD['storage_id']."')");
  $queryControl = mysqli_query($dbcon,"SELECT * FROM notification_control where trans_id = $reportID and notif_type_id = 3 and storage_id = '".$newD['storage_id']."'");
  $control = mysqli_fetch_array($queryControl);
  $notifQuery = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $reportID");
  $groupID = mysqli_fetch_array($notifQuery);
  $notifGroupQuery = mysqli_query($dbcon,"SELECT * FROM group_members where group_id = ".$groupID['group_id']);
  foreach ($notifGroupQuery as $key => $memberNotif) {
    $notif_members = $memberNotif['user_id'];
    $insertNotification = mysqli_query($dbcon,"INSERT INTO notification (notif_control_id,user_id,user_notif_type,notif_date,seen_user)
    values ('".$control['notif_control_id']."','$notif_members','1',NOW(),'1')") or die(mysqli_error($dbcon));
  }
  //inser history report staff
  $historyType = 4;
  $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
  values (NOW(),'$user','$reportID','".$newD['storage_id']."','$historyType')");
header('Location:returnRequest.php');
}else {
  echo "<script>alert('Failed! Already reported by other user!');window.location.href='manageUtensilRequest.php';</script>";

}
}

    ?>
    <?php  // CHANGE UTENSIL LIST BY STORAGE
    if (isset($_GET['by_storage'])) {
      $_SESSION['switch_storage'] = $_GET['by_storage'];
   header("Location:switchStorage.php");
    }
     ?>
<?php //change custom date
 if (isset($_POST['confirm_date'])) {
       $_SESSION['date1'] = $_POST['date1'];
       $_SESSION['date2'] = $_POST['date2'];
       $_SESSION['default_control']= 1;
      if ($_SESSION['account_type']==3||$_SESSION['account_type']==4||$_SESSION['account_type']==5) {
        header("Location:reports.php");
      }else {
        header("Location:admin_view_reports.php");
      }
     }
     //change inventory custom dates
     if (isset($_POST['confirm_inventory_date'])) {
           $_SESSION['inventory_date1'] = $_POST['inventory_date1'];
           $_SESSION['inventory_date2'] = $_POST['inventory_date2'];
           $_SESSION['default_control_inventory']= 1;
          if ($_SESSION['account_type']==3||$_SESSION['account_type']==4||$_SESSION['account_type']==5) {
            header("Location:reports.php");
          }else {
            header("Location:inventory_admin.php");
          }
         }
  if (isset($_POST['confirm_report_date'])) {
               $_SESSION['in_report_date1'] = $_POST['in_report_date1'];
               $_SESSION['in_report_date2'] = $_POST['in_report_date2'];
               $_SESSION['default_control_inventory']= 1;
              if ($_SESSION['account_type']==3||$_SESSION['account_type']==4||$_SESSION['account_type']==5) {
                header("Location:staff_inventory_report.php");
              }else {
                header("Location:inventory_admin.php");
              }
             }
?>
<?php
//session
function getUserById($id){
  global $dbcon;
  $query = "SELECT * from users where user_id = $id";
  $result = mysqli_query($dbcon,$query);
  $user = mysqli_fetch_assoc($result);
  return $user;
}
?>
