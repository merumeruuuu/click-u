<?php
// initialize utensils requests class
include 'requests.php';
$requests = new Utensils;

// include database configuration file
include 'config.php';

if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){
  //Storage selection
  if ($_REQUEST['action'] == 'selectStorage' && !empty($_REQUEST['id'])) {
    $clear = $requests->clear();
    unset($_SESSION['group_members']);
    unset($_SESSION['group_name']);
    $_SESSION['group_storage'] = $_REQUEST['id'];

    header("Location: select_utensils.php");
  }
  //Utensil Requests
    elseif($_REQUEST['action'] == 'addToRequests' && !empty($_REQUEST['id'])){

          $productID = $_REQUEST['id'];
          $storageID = $_REQUEST['s_id'];
          $query1 = "SELECT
                        a.storage_id,a.storage_name,a.initials,
                        b.storage_id as storageID,b.utensils_id,b.storage_qty,
                        c.utensils_id,c.utensils_name,c.utensils_cat_id,
                        d.utensils_cat_id,d.category

                        FROM storage a
                        LEFT JOIN storage_stocks b On a.storage_id = b.storage_id
                       LEFT JOIN utensils c on b.utensils_id = c.utensils_id
                       LEFT JOIN utensils_category d on c.utensils_cat_id = d.utensils_cat_id

                       where a.storage_id = $storageID AND c.utensils_id = $productID";

          $query = $dbcon->query($query1);
          $row = $query->fetch_assoc();
          $itemData = array(
              'id' => $row['utensils_id'],
              'name' => $row['utensils_name'],
              'category' => $row['category'],
              'storageID' => $row['storageID'],
              'qty' => 1
          );

        $insertItem = $requests->insert($itemData);
        $redirectLoc = $insertItem?'select_utensils.php':'select_utensils.php';

        header("Location: ".$redirectLoc);

    }elseif($_REQUEST['action'] == 'removeRequestItem' && !empty($_REQUEST['id'])){
        $deleteItem = $requests->remove($_REQUEST['id']);
        header("Location: select_utensils.php");
//Unset utensil requests
    }elseif($_REQUEST['action'] == 'clearItems' && !empty($_REQUEST['id'])){
        $clear = $requests->clear();
        header("Location: select_utensils.php");
    }

  //submition of each user requests
    elseif($_REQUEST['action'] == 'submitRequest' && $requests->total_items() > 0 ){
      $borrowedItems = $requests->contents();
          $borrower = $_SESSION['user']['user_id'];
      // for teacher borrower insertion
   if($_SESSION['account_type']=="6" && empty($_SESSION['group_members'])) {
     $purpose = trim($_SESSION['group_purpose']);
    $defaultStatus ='0';
     $receiveStatus = '0';
     $errors = false;
    //trap qty
    foreach ($borrowedItems as $key => $validateQTY) {
      $id = $validateQTY['id'];
      $sid = $validateQTY['storageID'];
      $qty = $validateQTY['qty'];
       $checkStorageStock = mysqli_query($dbcon,"SELECT * FROM storage_stocks where $id = utensils_id  and  $sid = storage_id  and $qty > storage_qty");
       $numRows = mysqli_num_rows($checkStorageStock);
      if ($numRows > 0) {
       $errors = true;
       echo "<script>alert('Failed! Please review your request!');window.location.href='select_utensils.php';</script>";
  }  }if ($errors == false) {

    $insertGroup = mysqli_query($dbcon,"INSERT INTO group_table  (instructor)
         values ('$borrower')")or die("details: ".mysqli_error($dbcon));

    $groupQuery = mysqli_query($dbcon,"SELECT group_id from group_table order by group_id desc limit 1")or die (mysqli_error($dcon));
    $groupID = mysqli_fetch_array($groupQuery);

    $insertMembers = mysqli_query($dbcon,"INSERT INTO group_members (group_id,user_id,added_by,date_added)
            values('".$groupID['group_id']."',$borrower,$borrower,NOW())")or die("details:".mysqli_error($dbcon));
     $storID = $_SESSION['group_storage'];
    //insertion of borrower slip master table
    $insertBorrow = mysqli_query($dbcon, "INSERT INTO borrower_slip (group_id,purpose, date_use, time_use, date_requested, added_by,received_by,storage_id,status)
    VALUES('".$groupID['group_id']."','$purpose','".$_SESSION['date_use']."','".$_SESSION['time_use']."',NOW(),'$borrower',$receiveStatus,'$storID',$defaultStatus)") or die(mysqli_error($dbcon));


    $query = mysqli_query($dbcon, "SELECT borrower_slip_id FROM borrower_slip ORDER BY borrower_slip_id DESC LIMIT 1") or die(mysqli_error($dbcon));
    $borrowID = mysqli_fetch_array($query);

    // insert pin request for teacher
    $pin = rand(1000,100);
    $insertPin = mysqli_query($dbcon,"INSERT INTO pin (pin_id,borrower_slip_id)values('$pin','".$borrowID['borrower_slip_id']."')");
    foreach ($borrowedItems as $item) {
      $insertBorrowDetails = mysqli_query($dbcon, "INSERT INTO borrower_slip_details (borrower_slip_id, utensils_id,storage_id, qty,reserved_qty,remarks)
      VALUES('".$borrowID['borrower_slip_id']."','".$item['id']."','".$item['storageID']."','".$item['qty']."','".$item['qty']."','".$defaultStatus."')") or die("details: ".mysqli_error($dbcon));
      $storageID = $item['storageID'];
      $utensilId = $item['id'];
      $requestQty =  $item['qty'];

      $storageCheck = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_id = $storageID and utensils_id = $utensilId");
      while ($row = mysqli_fetch_array($storageCheck)) {
      $storageQty = $row['storage_qty'];
      $reservedtQty = $row['reserved_qty'] + $requestQty;
      $deductedQty =  $storageQty - $requestQty;
      $updateStocks = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $deductedQty,reserved_qty = $reservedtQty where storage_id = $storageID and utensils_id = $utensilId");
      }


    }
    // insert notif for teacher
    $notifType1 = 1;
    $notifType2 = 2;
    $insertControl = mysqli_query($dbcon,"INSERT INTO notification_control (trans_id,notif_type_id,storage_id)values('".$borrowID['borrower_slip_id']."','$notifType1','$storageID')") or die(mysqli_error($dbcon));
    $getControl = mysqli_query($dbcon,"SELECT * FROM notification_control where trans_id = '".$borrowID['borrower_slip_id']."' and notif_type_id = 1");
    $control = mysqli_fetch_array($getControl);
    $insertNotification = mysqli_query($dbcon,"INSERT INTO notification (notif_control_id,user_id,user_notif_type)
    values ('".$control['notif_control_id']."','$borrower','$notifType1')") or die(mysqli_error($dbcon));

   $checkStaffList = mysqli_query($dbcon,"SELECT * FROM user_settings where account_type_id = 3 || account_type_id = 4 || account_type_id = 5");
   while ($staff_ids = mysqli_fetch_array($checkStaffList)) {

     $insertNotificationStaff = mysqli_query($dbcon,"INSERT INTO notification (notif_control_id,user_id,user_notif_type,notif_date)
     values ('".$control['notif_control_id']."','".$staff_ids['user_id']."','$notifType2',NOW())") or die(mysqli_error($dbcon));
   }
    //insert history
    $historyType = 1;
    $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
    values (NOW(),'$borrower','".$borrowID['borrower_slip_id']."','$storageID','$historyType')");
   if($insertGroup && $insertMembers && $insertBorrow && $insertBorrowDetails) {
    echo "<script>alert('Success!');window.location.href='userRequestsMenu2.php';</script>";
    }
  }

  }
      //for by group borrowing insertion
    elseif (!empty($_SESSION['group_members']) && $_SESSION['account_type']!="6") {
      $purpose = trim($_SESSION['group_purpose']);
      $defaultStatus ='0';
      $grpStorage = $_SESSION['group_storage'];
      $grpInstructor = $_SESSION['group_instructor'];
      $grpLeader = $_SESSION['group_leader'];
      $grpMembers = array_filter($_SESSION['group_members']);
      $grpName = $_SESSION['group_name'];
      $addedBy = $_SESSION['user']['user_id'];
      $receiveStatus = '0';
      $errors = false;
      foreach ($borrowedItems as $key => $validateQTY) {
        $id = $validateQTY['id'];
        $sid = $validateQTY['storageID'];
        $qty = $validateQTY['qty'];
         $checkStorageStock = mysqli_query($dbcon,"SELECT * FROM storage_stocks where $id = utensils_id  and  $sid = storage_id  and $qty > storage_qty");

         $numRows = mysqli_num_rows($checkStorageStock);
        if ($numRows > 0) {
         $errors = true;
         echo "<script>alert('Failed! Please review your request!');window.location.href='select_utensils.php';</script>";
      } }if ($errors == false) {
      $insertGroup = mysqli_query($dbcon,"INSERT INTO group_table  (group_name,group_leader_id,instructor)
           values ('$grpName','$grpLeader','$grpInstructor')")or die("details: ".mysqli_error($dbcon));

      $groupQuery = mysqli_query($dbcon,"SELECT group_id from group_table order by group_id desc limit 1")or die (mysqli_error($dcon));
      $groupID = mysqli_fetch_array($groupQuery);

     foreach ($grpMembers as  $members) {
      $insertMembers = mysqli_query($dbcon,"INSERT INTO group_members (group_id,user_id,added_by,date_added)
              values('".$groupID['group_id']."','".$members['member_ID']."',$borrower,NOW())")or die("details:".mysqli_error($dbcon));
            }
     //insertion of borrower_slip master
      $insertBorrow = mysqli_query($dbcon, "INSERT INTO borrower_slip (group_id,purpose, date_requested, added_by,received_by,storage_id,status)
      VALUES('".$groupID['group_id']."','$purpose',NOW(),'$addedBy',$receiveStatus,'$grpStorage',$defaultStatus)") or die(mysqli_error($dbcon));

      $query = mysqli_query($dbcon, "SELECT borrower_slip_id FROM borrower_slip ORDER BY borrower_slip_id DESC LIMIT 1") or die(mysqli_error($dbcon));
      $borrowID = mysqli_fetch_array($query);

     $pin = rand(1000,100);
     $insertPin = mysqli_query($dbcon,"INSERT INTO pin (pin_id,borrower_slip_id)values('$pin','".$borrowID['borrower_slip_id']."')");
       //insertion of borrow details
      foreach ($borrowedItems as $item) {
        $insertBorrowDetails = mysqli_query($dbcon, "INSERT INTO borrower_slip_details (borrower_slip_id, utensils_id,storage_id, qty,reserved_qty,remarks)
        VALUES('".$borrowID['borrower_slip_id']."','".$item['id']."',$grpStorage,'".$item['qty']."','".$item['qty']."','".$defaultStatus."')") or die("details: ".mysqli_error($dbcon));
        $storageID = $item['storageID'];
        $utensilId = $item['id'];
        $requestQty =  $item['qty'];

        $storageCheck = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_id = $storageID and utensils_id = $utensilId");
        while ($row = mysqli_fetch_array($storageCheck)) {
        $storageQty = $row['storage_qty'];
        $reservedtQty = $row['reserved_qty'] + $requestQty;
        $deductedQty =  $storageQty - $requestQty;
        $updateStocks = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $deductedQty,reserved_qty = $reservedtQty where storage_id = $storageID and utensils_id = $utensilId");
        }



      }

    if($insertGroup && $insertMembers && $insertBorrow && $insertBorrowDetails ) {

      echo "<script>alert('Success!');window.location.href='userRequestsMenu2.php';</script>";
      }

      // notification insertion
      $notifType1 = 1;
      $notifType2 = 2;
      $insertControl = mysqli_query($dbcon,"INSERT INTO notification_control (trans_id,notif_type_id,storage_id)values('".$borrowID['borrower_slip_id']."','$notifType1','$storageID')") or die(mysqli_error($dbcon));
      $getControl = mysqli_query($dbcon,"SELECT * FROM notification_control where trans_id = '".$borrowID['borrower_slip_id']."' and notif_type_id = 1");
      $control = mysqli_fetch_array($getControl);

      $notifGroupQuery = mysqli_query($dbcon,"SELECT * FROM group_members where group_id = ".$groupID['group_id']);
      foreach ($notifGroupQuery as $key => $memberNotif) {
        $notif_members = $memberNotif['user_id'];
        $notif_group = $memberNotif['group_id'];

        $insertNotification = mysqli_query($dbcon,"INSERT INTO notification (notif_control_id,user_id,user_notif_type)
        values ('".$control['notif_control_id']."','$notif_members','$notifType1')") or die(mysqli_error($dbcon));
      }

        $checkStaffList = mysqli_query($dbcon,"SELECT * FROM user_settings where account_type_id = 3 || account_type_id = 4 || account_type_id = 5");
        while ($staff_ids = mysqli_fetch_array($checkStaffList)) {

      $insertNotificationStaff = mysqli_query($dbcon,"INSERT INTO notification (notif_control_id,user_id,user_notif_type,notif_date)
      values ('".$control['notif_control_id']."','".$staff_ids['user_id']."','$notifType2',NOW())") or die(mysqli_error($dbcon));
        }

      //insert history
      $historyType = 1;
      $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
      values (NOW(),'$addedBy','".$borrowID['borrower_slip_id']."','$storageID','$historyType')");
    }

    }


  // for Individual borrower
  else{

      $defaultStatus ='0';
      $receiveStatus = '0';
      $grpInstructor = $_SESSION['group_instructor'];
      $error = false;
foreach ($borrowedItems as $key => $validateQTY) {
 $id = $validateQTY['id'];
 $sid = $validateQTY['storageID'];
 $qty = $validateQTY['qty'];
  $checkStorageStock = mysqli_query($dbcon,"SELECT * FROM storage_stocks where $id = utensils_id  and  $sid = storage_id  and $qty > storage_qty");
  $numRows = mysqli_num_rows($checkStorageStock);
 if ($numRows > 0) {
   $error = TRUE;
    echo "<script>alert('Failed! Please review your request!');window.location.href='select_utensils.php';</script>";
    return $error;
 }  }if($error == false) {

      $purpose = trim($_SESSION['group_purpose']);
      $insertGroup = mysqli_query($dbcon,"INSERT INTO group_table  (instructor)
           values ('$grpInstructor')")or die("details: ".mysqli_error($dbcon));

      $groupQuery = mysqli_query($dbcon,"SELECT group_id from group_table order by group_id desc limit 1")or die (mysqli_error($dcon));
      $groupID = mysqli_fetch_array($groupQuery);

      $insertMembers = mysqli_query($dbcon,"INSERT INTO group_members (group_id,user_id,added_by,date_added)
              values('".$groupID['group_id']."',$borrower,$borrower,NOW())")or die("details:".mysqli_error($dbcon));
     $storID = $_SESSION['group_storage'];
     //insertion of borrower slip master table
      $insertBorrow = mysqli_query($dbcon, "INSERT INTO borrower_slip (group_id ,purpose,date_use,time_use, date_requested, added_by,received_by,storage_id,status)
      VALUES('".$groupID['group_id']."','$purpose','".$_SESSION['date_use']."','".$_SESSION['time_use']."',NOW(),'$borrower',$receiveStatus,'$storID',$defaultStatus)") or die(mysqli_error($dbcon));


      $query = mysqli_query($dbcon, "SELECT borrower_slip_id FROM borrower_slip ORDER BY borrower_slip_id DESC LIMIT 1") or die(mysqli_error($dbcon));
      $borrowID = mysqli_fetch_array($query);

      $pin = rand(1000,100);
      $insertPin = mysqli_query($dbcon,"INSERT INTO pin (pin_id,borrower_slip_id)values('$pin','".$borrowID['borrower_slip_id']."')");
      foreach ($borrowedItems as $item) {
        $insertBorrowDetails = mysqli_query($dbcon, "INSERT INTO borrower_slip_details (borrower_slip_id, utensils_id,storage_id, qty,reserved_qty,remarks)
        VALUES('".$borrowID['borrower_slip_id']."','".$item['id']."','".$item['storageID']."','".$item['qty']."','".$item['qty']."','".$defaultStatus."')") or die("details: ".mysqli_error($dbcon));
        $storageID = $item['storageID'];
        $utensilId = $item['id'];
        $requestQty =  $item['qty'];

        $storageCheck = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_id = $storageID and utensils_id = $utensilId");
        while ($row = mysqli_fetch_array($storageCheck)) {
        $storageQty = $row['storage_qty'];
        $reservedtQty = $row['reserved_qty'] + $requestQty;
        $deductedQty =  $storageQty - $requestQty;
        $updateStocks = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $deductedQty,reserved_qty = $reservedtQty where storage_id = $storageID and utensils_id = $utensilId");

        }

      }
      $notifType1 = 1;
      $notifType2 = 2;
      $insertControl = mysqli_query($dbcon,"INSERT INTO notification_control (trans_id,notif_type_id,storage_id)values('".$borrowID['borrower_slip_id']."','$notifType1','$storID')") or die(mysqli_error($dbcon));
      $getControl = mysqli_query($dbcon,"SELECT * FROM notification_control where trans_id = '".$borrowID['borrower_slip_id']."' and notif_type_id = 1");
      $control = mysqli_fetch_array($getControl);
      $insertNotification = mysqli_query($dbcon,"INSERT INTO notification (notif_control_id,user_id,user_notif_type)
      values ('".$control['notif_control_id']."','$borrower','$notifType1')") or die(mysqli_error($dbcon));

     $checkStaffList = mysqli_query($dbcon,"SELECT * FROM user_settings where account_type_id = 3 || account_type_id = 4 || account_type_id = 5");
     while ($staff_ids = mysqli_fetch_array($checkStaffList)) {

       $insertNotificationStaff = mysqli_query($dbcon,"INSERT INTO notification (notif_control_id,user_id,user_notif_type,notif_date)
       values ('".$control['notif_control_id']."','".$staff_ids['user_id']."','$notifType2',NOW())") or die(mysqli_error($dbcon));
     }
     //insert history
     $historyType = 1;
     $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
     values (NOW(),'$borrower','".$borrowID['borrower_slip_id']."','$storID','$historyType')");


  if($insertGroup && $insertMembers && $insertBorrow && $insertBorrowDetails ) {
      echo "<script>alert('Success!');window.location.href='userRequestsMenu2.php';</script>";
      }
}

    }
    }
    else{
      if($_REQUEST['action'] == 'submitRequest' && $requests->total_items() > 0 && empty($_SESSION['borrowers'])) {
        header("Location: index.php?error=1");
      } else {
        header("Location: index.php");
      }
    }


//Update quantity
if($_REQUEST['action'] == 'updateRequestItem' && !empty($_REQUEST['id'])){
  $ItemId = $_REQUEST['id'];
  $StorageId =  $_SESSION['group_storage'];
  // $query1 = "SELECT
  //               a.storage_id,
  //               b.storage_id ,b.utensils_id,b.storage_qty,
  //               c.utensils_id
  //
  //               FROM storage a
  //               LEFT JOIN storage_stocks b On a.storage_id = b.storage_id
  //              LEFT JOIN utensils c on b.utensils_id = c.utensils_id
  //
  //              where a.storage_id ='$StorageId' and c.utensils_id ='$ItemId'";
  $check = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = $ItemId and storage_id = $StorageId");
  $row = mysqli_fetch_assoc($check);

   if($_REQUEST['qty']>$row['storage_qty']){
     echo $updateItem?:'err';

 }else{
    $itemData = array(
        'rowid' => $_REQUEST['id'],
        'qty' => $_REQUEST['qty']
    );
    $updateItem = $requests->update($itemData);
    echo $updateItem?'ok':'err';
  }
}
}else{
    header("Location: index.php");
}
//modify

?>
