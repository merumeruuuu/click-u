<?php include('header.php')
 ?>
<script>
$(document).ready(function(){
	$('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
		localStorage.setItem('activeTab', $(e.target).attr('href'));
	});
	var activeTab = localStorage.getItem('activeTab');
	if(activeTab){
		$('#myTab a[href="' + activeTab + '"]').tab('show');
	}
});
//add new items
// function check_id()
// {
// if (document.getElementById('xxx').checked)
// {
//     document.getElementById('totalCost').value = 10;
// } else {
//     calculate();
// }
// }
//change group leader
function session_groupLeader(value) {
      $.ajax({
          type: "POST",
          url: 'ajaxrequest/selectSession.php',
          data: 'group_leader2=' + value,
          dataType: 'json',
          success: function (data) {
            if (data==0) {
              // location.reload();
              location.href = 'modifyUserRequests.php';
              setInterval( 1000);
            }
          }
      });
  }

</script>
<?php
$modID2 = $_SESSION['modify_id']; ?>
<?php if(isset($_POST["add_new_item"]))
 {

     $storID = mysqli_real_escape_string($dbcon,$_POST['storageID']);
     $qty = mysqli_real_escape_string($dbcon,$_POST['qty']);
     $uID = $_GET["id"];
     if ($qty < 0) {
       array_push($errors,"Invalid quantity!");
     }else {

     $query = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $modID2 and utensils_id = $uID");
     $storageQuery = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_id = $storID and utensils_id = $uID");
     $checkqty = mysqli_fetch_array($storageQuery);
     if ($qty > $checkqty['storage_qty']) {
       array_push($errors,"Failed! invalid quantity!");
     }else {
       if (mysqli_num_rows($query)> 0) {
         array_push($errors,"Item Already in your request!");
       }else {

  			if(isset($_SESSION["item_tray"]))
  			{
  					 $item_array_id = array_column($_SESSION["item_tray"], "item_id");
  					 if(!in_array($_GET["id"], $item_array_id))
  					 {
  								$count = count($_SESSION["item_tray"]);
  								$item_array = array(
  										 'item_id'    =>     $_GET["id"],
                       'storage_id' =>     $_POST["storageID"],
                       'item_name'  =>     $_POST["item_name"],
                       'category'   =>     $_POST["category"],
                       'qty'        =>     $_POST["qty"]

  								);
  								$_SESSION["item_tray"][$count] = $item_array;

  					 }
            else
  					 {
  						 array_push($errors,"Item Already Added!");
  						 // unset($_SESSION["item_tray"]);
  					 }

  			}
  			else
  			{
  					 $item_array = array(
               'item_id'    =>     $_GET["id"],
               'storage_id' =>     $_POST["storageID"],
               'item_name'  =>      $_POST["item_name"],
               'category'   =>     $_POST["category"],
               'qty'        =>     $_POST["qty"]
  					 );

  					 $_SESSION["item_tray"][0] = $item_array;


  			}

        }
     }
     // code...
   }

 }
 if(isset($_GET["action"]))
 {
			if($_GET["action"] == "delete")
			{
					 foreach($_SESSION["item_tray"] as $keys => $values)
					 {
								if($values["item_id"] == $_GET["ids"])
								{
										 // unset($_SESSION["item_tray"][$keys]);
                     $_SESSION["item_tray"][$keys] = Null;
										 // echo '<script>alert("Item cancelled!")</script>';
										 // echo '<script>window.location="modifyUserRequests.php"</script>';

								}
					 }
			}
 } ?>
 <?php
  if (isset($_GET['save'])) {
    $modIDs = $_SESSION['modify_id'];
    $user = $_SESSION['user']['user_id'];
    $error = false;
    foreach ($_SESSION['item_tray'] as $key => $value) {
      // $checkRequest = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $modIDs");
      $checkStock = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_id = '".$value['storage_id']."' and utensils_id = '".$value['item_id']."' and storage_qty < '".$value['qty']."'");
    if (mysqli_num_rows($checkStock)>0) {
      $error = true;
      echo '<script>alert("Failed!")</script>';
      echo '<script>window.location="modifyUserRequests.php"</script>';
      return $error;
    }
  }if($error == false) {
    foreach ($_SESSION['item_tray'] as $key => $new) {
      $checkStock1 = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_id = '".$new['storage_id']."' and utensils_id = '".$new['item_id']."'");
      foreach ($checkStock1 as $key => $values) {


          if ($_SESSION['account_type']==6||$_SESSION['account_type']==7) {
            // update request and storage details user
            $newStock = $values['storage_qty'] - $new['qty'];
            $newReserved = $values['reserved_qty'] + $new['qty'];
            $update = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStock,reserved_qty = $newReserved where storage_id = '".$new['storage_id']."' and utensils_id = '".$new['item_id']."'");
            $insert = mysqli_query($dbcon,"INSERT INTO borrower_slip_details (borrower_slip_id,utensils_id,storage_id,qty,reserved_qty)
              values('$modIDs','".$new['item_id']."','".$new['storage_id']."','".$new['qty']."','".$new['qty']."')");
              $updateBorrower = mysqli_query($dbcon,"UPDATE borrower_slip set date_modified = NOW(), modified_by = $user where borrower_slip_id = $modIDs");
              echo '<script>alert("Saved!")</script>';

            //insert history for modification
            foreach ($_SESSION['item_tray'] as $key => $storage);
            $stor = $storage['storage_id'];
            $checkHistory = mysqli_query($dbcon,"SELECT * FROM history where trans_id = $modIDs and user_id = $user and history_type_id = 3");
            if (mysqli_num_rows($checkHistory)>0) {
              $updateHistory = mysqli_query($dbcon,"UPDATE history set date_added = NOW() where trans_id = $modIDs and user_id = $user and history_type_id = 3");
            }else {
              $historyType = 3;
              $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
              values (NOW(),'$user','$modIDs','$stor','$historyType')");
            }
          echo '<script>window.location="userRequestsMenu2.php"</script>';
          }else {
            // update request and storage details staff
            $newStock = $values['storage_qty'] - $new['qty'];
            $newon_use = $values['on_use'] + $new['qty'];
            $update = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStock,on_use = $newon_use where storage_id = '".$new['storage_id']."' and utensils_id = '".$new['item_id']."'");
            $insert = mysqli_query($dbcon,"INSERT INTO borrower_slip_details (borrower_slip_id,utensils_id,storage_id,qty,on_use)
              values('$modIDs','".$new['item_id']."','".$new['storage_id']."','".$new['qty']."','".$new['qty']."')");
              $updateBorrower = mysqli_query($dbcon,"UPDATE borrower_slip set date_modified = NOW(), modified_by = $user where borrower_slip_id = $modIDs");
              echo '<script>alert("Saved!")</script>';

            //inser history modification staff
            $historyTypeStaff = 2;
            $insertHistorystaff = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
            values (NOW(),'$user','$modIDs','".$value['storage_id']."','$historyTypeStaff')");
          echo '<script>window.location="activeRequests.php"</script>';
          }
        }
      }

    }

  }
  //
  if (isset($_GET['clear'])) {
    unset($_SESSION["item_tray"]);
  }
  ?>
  <?php //manage group

  if(isset($_POST['add_member']))
  {
   $str = $_POST['add_member'];
   $query = "SELECT
                    a.user_id as userID,a.fname,a.lname,a.school_id,
                    b.user_id,b.account_type_id

                    from users a
                    left join user_settings b on a.user_id = b.user_id
                    where a.school_id like '%$str%' and b.account_type_id =7";
   $result = mysqli_query($dbcon,$query);
   $check = mysqli_num_rows($result);
   if($check==0){
  // echo "<script>alert('Student not found!');window.location.href='creategroup.php';</script>";
  array_push($errors, "Student not found!! " );
}
else {

  if(isset($_POST['add_member']))
  {
    $str1 = $_POST['add_member'];
    $user = $_SESSION['user']['user_id'];
    $member = mysqli_query($dbcon,"SELECT * FROM users where school_id = $str1");
    $row_member = mysqli_fetch_array($member);
    $group = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $modID2");
    $row_group = mysqli_fetch_array($group);

     $userGroup = $row_group['group_id'];
     $user = $row_member['user_id'];
     $storageID = $row_member['storage_id'];
     $check_member = mysqli_query($dbcon,"SELECT * FROM group_members where group_id = $userGroup and user_id = $user");
     if (mysqli_num_rows($check_member)>0) {
       array_push($errors,"Member already added!");
     }else {
       $insert = mysqli_query($dbcon,"INSERT INTO group_members (group_id,user_id,added_by,date_added)
       values('$userGroup','$user','$user',NOW() )");
       $user = $_SESSION['user']['user_id'];
       $updateBorrower = mysqli_query($dbcon,"UPDATE borrower_slip set date_modified = NOW(), modified_by = $user where borrower_slip_id = $modID2");

       //insert history for modification

       $checkHistory = mysqli_query($dbcon,"SELECT * FROM history where trans_id = $modID2 and user_id = $user and history_type_id = 3");
       if (mysqli_num_rows($checkHistory)>0) {
         $updateHistory = mysqli_query($dbcon,"UPDATE history set date_added = NOW() where trans_id = $modID2 and user_id = $user and history_type_id = 3");
       }else {
         $historyType = 3;
         $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
         values (NOW(),'$user','$modID2','$storageID','$historyType')");
       }

     }

  }
 }
}
   ?>
   <?php //new group name
   if (isset($_POST["new_group_name"])) {
     $newGname = $_POST["new_group_name"];
     $grpID = $_SESSION['group_id_mod'];
     $update = mysqli_query($dbcon,"UPDATE group_table set group_name = '$newGname' where group_id = $grpID");
     $_SESSION['group_name'] = $newGname;
     $user = $_SESSION['user']['user_id'];
     $updateBorrower = mysqli_query($dbcon,"UPDATE borrower_slip set date_modified = NOW(), modified_by = $user where borrower_slip_id = $modID2");
     //insert history for modification
      $res = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $modID2");
      foreach ($res as $key => $storID);
      $storageID = $storID['storage_id'];
     $checkHistory = mysqli_query($dbcon,"SELECT * FROM history where trans_id = $modID2 and user_id = $user and history_type_id = 3");
     if (mysqli_num_rows($checkHistory)>0) {
       $updateHistory = mysqli_query($dbcon,"UPDATE history set date_added = NOW() where trans_id = $modID2 and user_id = $user and history_type_id = 3");
     }else {
       $historyType = 3;
       $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
       values (NOW(),'$user','$modID2','$storageID','$historyType')");
     }
   }
    ?>
   <?php //remove member
   if(isset($_REQUEST['action'])&& !empty($_REQUEST['action']))
   {
      if($_REQUEST['action'] == 'removeMember')
      {
      $user = $_SESSION['user']['user_id'];
      $GID = $_REQUEST['grID'];
      $memID = $_REQUEST['memID'];
     $checkMember = mysqli_query($dbcon,"SELECT * FROM group_members where group_id = $GID");
     if (mysqli_num_rows($checkMember)>2) {
       if ($memID == $user) {
         array_push($errors,"Remove Failed! Only other member can remove you from the group!");
       }else {

       $delete = mysqli_query($dbcon,"DELETE FROM group_members where group_id = $GID and user_id = $memID");

       $updateBorrower = mysqli_query($dbcon,"UPDATE borrower_slip set date_modified = NOW(), modified_by = $user where borrower_slip_id = $modID2");

       //insert history for modification
        $res = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $modID2");
        foreach ($res as $key => $storID);
        $storageID = $storID['storage_id'];
       $checkHistory = mysqli_query($dbcon,"SELECT * FROM history where trans_id = $modID2 and user_id = $user and history_type_id = 3");
       if (mysqli_num_rows($checkHistory)>0) {
         $updateHistory = mysqli_query($dbcon,"UPDATE history set date_added = NOW() where trans_id = $modID2 and user_id = $user and history_type_id = 3");
       }else {
         $historyType = 3;
         $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
         values (NOW(),'$user','$modID2','$storageID','$historyType')");
       }

     }

     }else {
    array_push($errors,"Remove Failed! At least two members is required!");
     }

    }
  }
    ?>
  <?php //change group leader on refresh
   $grpIDS = $_SESSION['group_id_mod'];
   $checkLeader = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = $grpIDS");
   $rowID = mysqli_fetch_array($checkLeader);
   if ($rowID['group_leader_id'] != $_SESSION['group_leader2']) {
     $newGL = $_SESSION['group_leader2'];
     $updateLeader = mysqli_query($dbcon,"UPDATE group_table set group_leader_id = $newGL where group_id = $grpIDS");
     $user = $_SESSION['user']['user_id'];
     $updateBorrower = mysqli_query($dbcon,"UPDATE borrower_slip set date_modified = NOW(), modified_by = $user where borrower_slip_id = $modID2");
     //insert history for modification

      $res = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $modID2");
      foreach ($res as $key => $storID);
      $storageID = $storID['storage_id'];
     $checkHistory = mysqli_query($dbcon,"SELECT * FROM history where trans_id = $modID2 and user_id = $user and history_type_id = 3");
     if (mysqli_num_rows($checkHistory)>0) {
       $updateHistory = mysqli_query($dbcon,"UPDATE history set date_added = NOW() where trans_id = $modID2 and user_id = $user and history_type_id = 3");
     }else {
       $historyType = 3;
       $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
       values (NOW(),'$user','$modID2','$storageID','$historyType')");
     }
   }
   ?>
   <?php
   if (isset($_GET['add_qty'])) {

     $user = $_SESSION['user']['user_id'];
       $detailsID = $_GET['id'];
       $addtnlQTY = 1;
       $check = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where bsd_id = $detailsID ");
       $row = mysqli_fetch_assoc($check);
       $brID = $row['borrower_slip_id'];
       $uID = $row['utensils_id'];
       $sID = $row['storage_id'];
       $qty = $row['qty'];
       $resrvdQty = $row['reserved_qty'];
       $tempQty = $addtnlQTY + $resrvdQty;
       $on_useQty = $row['qty'] + $addtnlQTY;
       // for request details
       $checkStorageq = mysqli_query($dbcon,"SELECT * FROM storage_stocks WHERE utensils_id = $uID and storage_id = $sID");
       $rowS = mysqli_fetch_assoc($checkStorageq);
       if ($addtnlQTY > $rowS['storage_qty']) {
         array_push($errors,"Quantity exceeds!");
       }else {
         if ($_SESSION['account_type']==6||$_SESSION['account_type']==7) {

       $newStock = $rowS['storage_qty'] - $addtnlQTY; // for storage update for user still on request
       $newReserved = $rowS['reserved_qty'] + $addtnlQTY;
       $updateNewStock = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStock,reserved_qty = $newReserved WHERE utensils_id = $uID and storage_id = $sID");
       $updateQty = mysqli_query($dbcon,"UPDATE borrower_slip_details set qty = $tempQty, reserved_qty = $tempQty where bsd_id = $detailsID");
       $updateBorrower = mysqli_query($dbcon,"UPDATE borrower_slip set date_modified = NOW(), modified_by = $user where borrower_slip_id = $brID");
     }else {
       $checkStorageq1 = mysqli_query($dbcon,"SELECT * FROM storage_stocks WHERE utensils_id = $uID and storage_id = $sID");
       $rowS1 = mysqli_fetch_assoc($checkStorageq1);
       $addtnlQTY1 = 1;
       $newStock1 = $rowS1['storage_qty'] - $addtnlQTY1; // for storage update for staff change to on use
       $newReserved1 = $rowS1['on_use'] + $addtnlQTY1;
       $updateNewStock1 = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStock1,on_use = $newReserved1 WHERE utensils_id = $uID and storage_id = $sID");
       $updateQty1 = mysqli_query($dbcon,"UPDATE borrower_slip_details set qty = $on_useQty, on_use = $on_useQty where bsd_id = $detailsID");
       $updateBorrower = mysqli_query($dbcon,"UPDATE borrower_slip set date_modified = NOW(), modified_by = $user where borrower_slip_id = $brID");
     }
       //inser history modification staff
       $historyTypeS = 2;
       $insertHistorystaff = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
       values (NOW(),'$user','$brID','$sID','$historyTypeS')");

       //insert history for modification
       $checkHistory = mysqli_query($dbcon,"SELECT * FROM history where trans_id = $brID and user_id = $user and history_type_id = 3");
       if (mysqli_num_rows($checkHistory)>0) {
         $updateHistory = mysqli_query($dbcon,"UPDATE history set date_added = NOW() where trans_id = $brID and user_id = $user and history_type_id = 3");
       }else {
         $historyType = 3;
         $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
         values (NOW(),'$user','$brID','$sID','$historyType')");
       }
     }
   } //end of add
if (isset($_GET['minus_qty'])) {
  $user = $_SESSION['user']['user_id'];
  $deductQTY = 1;
  $deductID = $_GET['id'];
  $check2 = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where bsd_id = $deductID");
  $show = mysqli_fetch_array($check2);
  $bID = $show['borrower_slip_id'];
  $uID2 = $show['utensils_id'];
  $sID2 = $show['storage_id'];
  $rsrvdQty = $show['reserved_qty'];
  $rQty = $show['qty'];
  $on_useQty2 = $show['on_use'];

 if ($deductQTY >= $rQty) {
   array_push($errors,"Minimum of 1 quantity!");
 }else {
   $check  = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = $uID2 and storage_id = $sID2");
   $rows2 = mysqli_fetch_array($check);
   $storageQty2 = $rows2['storage_qty'];

  if ($_SESSION['account_type']==6||$_SESSION['account_type']==7) {
    $newQty = $rsrvdQty - $deductQTY;
    $newStock1 = $storageQty2 + $deductQTY;
    $updateNewStock1 = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStock1 WHERE utensils_id = $uID2 and storage_id = $sID2");
    $updateQty1 = mysqli_query($dbcon,"UPDATE borrower_slip_details set qty = $newQty, reserved_qty = $newQty where bsd_id = $deductID");
    $updateBorrower = mysqli_query($dbcon,"UPDATE borrower_slip set date_modified = NOW(), modified_by = $user where borrower_slip_id = $bID");
  }else {
    $newQty = $rQty - $deductQTY;
    $newStock1 = $rows2['storage_qty'] + $deductQTY;
    $on_useQty = $rows2['on_use'] - $deductQTY;
    $updateNewStock1 = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStock1,on_use = $on_useQty WHERE utensils_id = $uID2 and storage_id = $sID2");
    $updateQty1 = mysqli_query($dbcon,"UPDATE borrower_slip_details set qty = $newQty, on_use = $on_useQty2 where bsd_id = $deductID");
    $updateBorrower = mysqli_query($dbcon,"UPDATE borrower_slip set date_modified = NOW(), modified_by = $user where borrower_slip_id = $bID");
  }

     //inser history modification staff
     $historyTypeS = 2;
     $insertHistorystaff = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
     values (NOW(),'$user','$bID','$sID2','$historyTypeS')");

     //insert history for modification

     $checkHistory = mysqli_query($dbcon,"SELECT * FROM history where trans_id = $bID and user_id = $user and history_type_id = 3");
     if (mysqli_num_rows($checkHistory)>0) {
       $updateHistory = mysqli_query($dbcon,"UPDATE history set date_added = NOW() where trans_id = $bID and user_id = $user and history_type_id = 3");
     }else {
       $historyType = 3;
       $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
       values (NOW(),'$user','$bID','$sID2','$historyType')");
     }
   }
}
   ?>
<br><br>
<div class="content" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                  <?php if ($_SESSION['account_type']==6||$_SESSION['account_type']==7) {
                    ?>
                    <div class="header">
                      <div class="col-md-2">
                        <h4 class="title">My Requests :</h4>
                         </div>
                          <br><br>
                           </div>
                           <?php
                         }
                           ?>
                            <div class="content" >
                            <div class="bs-example">
                        <ul class="nav nav-tabs" id="myTab">
                          <?php if ($_SESSION['account_type']==3||$_SESSION['account_type']==4||$_SESSION['account_type']==5) {
                            ?>
                              <li><a href="activeRequests.php"><i class="fa fa-chevron-left "></i> Go back</a></li>
                            <?php
                          }else {
                            ?>
                             <li><a href="userRequestsMenu2.php"><i class="fa fa-chevron-left "></i> Go back</a></li>
                            <?php
                          }?>

                          <li class="active"><a data-toggle="tab" href="#section1"><i class="fa fa-adjust"></i> Adjust Quantity</a></li>
                          <li><a data-toggle="tab" href="#section2"><i class="fa fa-plus"></i> Add more items</a></li>
                          <?php

                          $check_slip = mysqli_query($dbcon,"SELECT  * from borrower_slip where borrower_slip_id = $modID2");
                           $grp = mysqli_fetch_array($check_slip);
                           $check1 = mysqli_query($dbcon,"SELECT *  from group_table where group_id = '".$grp['group_id']."'");
                            $grp_row = mysqli_fetch_array($check1);
                           if (!empty($grp_row['group_name'])&& !empty($grp_row['group_leader_id'])) {
                             ?>
                          <li><a data-toggle="tab" href="#section3"><i class="fa fa-users"></i> Manage group</a></li>
                             <?php
                           }   ?>

                        </ul>
                        <?php
                        $reqID = $_SESSION['modify_id'];
                        		$queryString = "SELECT
                        			a.bsd_id,a.borrower_slip_id,a.utensils_id,a.qty,a.storage_id,
                        			b.utensils_id as itemID,b.utensils_name,b.utensils_cat_id,
                        			c.utensils_cat_id,c.category

                        			from borrower_slip_details a
                        			left JOIN utensils b on a.utensils_id = b.utensils_id
                        			left join utensils_category c on b.utensils_cat_id = c.utensils_cat_id

                        			where a.borrower_slip_id = $reqID";
                        			$itemQuery = mysqli_query($dbcon,$queryString);

                         ?>
                        <div class="tab-content">
        <div id="section1" class="tab-pane fade in active">
          <div class="row">
      					<div class="col-md-5">
      					 <h4 class=" ">Request # <strong style="color:#07bfea"> <?php echo $reqID; ?></strong></h4>
      					</div>
                <div class="col-md-6">
                  <br>
                  <?php include 'errors.php'; ?>
      					</div>
                  <div class="col-md-12">
      										<div class="card">
                              <div class="content">
                                            <strong><label for=""><i class="fa fa-table"></i> Requested Items :</label></strong>
                                          <table class="table "id=""style="text-align:center;">
                                                        <thead class="bg-default">
      																										<col span="3" style="background-color:auto;">
                                                          <col span="1"style="background-color:#e9e7e0;">
      																										<col span="3"style="background-color:#e9e7e0;">
                                                            <tr >
                                                              <th style="text-align:center;">Item ID</th>
                                                              <th style="text-align:center;">Item Name</th>
                                                              <th style="text-align:center;">Item Category</th>

                                                              <th style="text-align:center;">Request Quantity</th>
      																												<th style="text-align:center;">Available Stocks</th>
      																												<!-- <th style="text-align:center;"><i class="fa fa-plus"></i> Add qty</th>
                                                              <th style="text-align:center;"><i class="fa fa-minus"></i> Reduce qty</th> -->
                                                              <th style="text-align:center;">Remove item</th>
                                                                    </tr>
                                                                  </thead>
                                                                  <tbody>
                                                                    <?php while ($items = mysqli_fetch_array($itemQuery)) {
                                                                      ?>
                                                                    <tr>
                                                                      <td><?php echo  $items['itemID']?></td>
                                                                      <td><?php echo  $items['utensils_name']?></td>
                                                                      <td><?php echo  $items['category']?></td>

                                                                      <td>
                                                                        <div class="form-inline">
                                                                          <a href="?add_qty&id=<?php echo $items['bsd_id']; ?>" class="btn btn-sm btn-success btn-fill"><i class="fa fa-plus"></i></a>
                                                                          <strong><input type="text"readonly class="form-control text-center" value="<?php echo  $items['qty']?>"/>
                                                                        </strong>
                                                                        <a href="?minus_qty&id=<?php echo $items['bsd_id']; ?>"class="btn btn-sm btn-warning btn-fill"><i class="fa fa-minus"></i></a>
                                                                        </div>
                                                                    </td>
      																																<?php
      																																	 $uID = $items['itemID'];
      																																	 $stID = $items['storage_id'];
      																																	 $check = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = $uID and storage_id = $stID");
      																																	 while ($show = mysqli_fetch_array($check)) {
      																																		 ?>
                                                                           <td><?php echo  $show['storage_qty']?></td>
      																																		 <?php
      																																} ?>

                                                                        <!-- <td><input type="number" class="form-control text-center" id="numInput" value="" onchange="addQty(this, '<?php echo $items['bsd_id']; ?>');"></td>
                                                                        <td><input type="number" class="form-control text-center"id="numInput1"  value="" onchange="deductQty(this, '<?php echo $items['bsd_id']; ?>');"></td> -->
                                                                       <?php
                                                                         $count = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $reqID");
                                                                        if (mysqli_num_rows($count)==1){ ?>
                                                                         <td><a href="#"onclick="alert('Sorry you cannot remove one remaining item!')"class="btn btn-sm btn-danger btn-fill"><i class="fa fa-trash"></i></a></td>
                                                                         <?php
                                                                       }else {
                                                                         ?>
                                                                         <td><a href="server.php?action=removeAndModifyItem&id=<?php echo $items['bsd_id']; ?>"onclick="return confirm('Are you sure to remove this item?')" class="btn btn-sm btn-danger btn-fill"><i class="fa fa-trash"></i></a></td>
                                                                         <?php
                                                                       } ?>

                                                                   <?php
                                                            }?>
                                                    </tr>
                                          </tbody>
                                      </table>

                                 </div>
      										 </div>
                 </div>
            </div>
        </div>
        <div id="section2" class="tab-pane fade">
          <div class="row">
        		<div class="col-md-5">
        		<h4>Request # <strong style="color:#07bfea"> <?php echo $_SESSION['modify_id']; ?></strong></h4>
        		</div>
        		<div class="col-md-5">
        			<br>
        		<?php include('errors.php'); ?>
        		</div>
        		<!-- <div class="col-md-6">
        			<div class="category">
              <h4><strong>Select new items : </strong></h4>
        			</div>
        		</div> -->
        		<div class="col-md-2">
        			<div class="content">
        				<?php
                if (isset($_SESSION['item_tray'])) {
  						      	$new_items = $_SESSION['item_tray'];
  										$count = count(array_filter($new_items));
  								}
                     if (empty($_SESSION['item_tray'])||empty($count)) {
                     	?>
                      <button class="btn  btn-success btn-fill"data-toggle="modal"><i class="fa fa-eye"></i> New items : 0</button>
        							<?php
        						}else {

        							?>
                        <button class="btn  btn-success btn-fill"data-toggle="modal" data-target="#myModal1"><i class="fa fa-eye"></i> New items :
                          <span class="label label-pill label-danger count blink" style="border-radius:8px;"><?php echo $count; ?></span></button>
        							<?php
        						}
        					?>
        			</div>
        		</div>
        			<div class="col-md-12">
        					<div class="card">
        							 <div class="content">
        								 <strong><label for=""><i class="fa fa-plus"></i> select new items :</label></strong>
        								 <br>
        									<table class="table table-bordered table-hover"id="requestTable1">
        										<thead>
        											<tr>
        												<th>ID</th>
        												<th>Item Name</th>
        												<th>Available</th>
        												<th>Category</th>
        												<th >Action</th>
        											</tr>
        										</thead>
        										<tbody>
        			 				   <?php
        			 				     $modID = $_SESSION['modify_id'];
        									 $check = mysqli_query($dbcon,"SELECT storage_id from borrower_slip where borrower_slip_id = $modID");
        									 $storID = mysqli_fetch_array($check);
        									 $id = $storID['storage_id'];

        			 				     $query1 = "SELECT
        			 				                   a.storage_id as storageID,a.utensils_id,a.storage_qty,
        			 				                   b.utensils_id,b.utensils_name,b.utensils_cat_id,
        			 				                   c.utensils_cat_id,c.category

        			 				                   FROM storage_stocks a
        			 				                   LEFT JOIN utensils b on a.utensils_id = b.utensils_id
        			 				                   LEFT JOIN utensils_category c on b.utensils_cat_id = c.utensils_cat_id

        			 				                  where  a.storage_id ='$id' and a.storage_qty > 0 ";

        			 				     $utensil = mysqli_query($dbcon,$query1);
        			 				   ?>
        			 				        <?php
        			 				        while ($rows=mysqli_fetch_array($utensil)) {


        											?>
        			 				        <tr>
        			 				          <td><?php echo $rows['utensils_id'] ?></td>
        			 				          <td><?php echo $rows['utensils_name'] ?></td>
        			 				          <td><?php echo $rows['storage_qty'] ?></td>
        			 				          <td><?php echo $rows['category'] ?></td>
        			 				          <td>

                              <form class="form-inline " action="modifyUserRequests.php?action=add&id=<?php echo $rows["utensils_id"]; ?>" method="post">
                                <input type="hidden" name="storageID" value="<?php echo $rows["storageID"]; ?>">
                                <input type="hidden" name="item_name" value="<?php echo $rows["utensils_name"]; ?>">
                                <input type="hidden" name="category" value="<?php echo $rows["category"]; ?>">
                                  <input type="number"class="form-control text-center" name="qty"id="numInput2" value=""placeholder="Qty"required>
                                  <button type="submit"name="add_new_item" class="btn btn-info ">
                                  <i class="fa fa-check"></i>
                                  </button>
                              </form>
        			 				          </td>
        			 				        </tr>
        			 				      <?php
        									}?>
        			 				      </tbody>
        			 				    </table>
        			 				  </div>
        			       </div>
        			</div>
           </div>
        </div>
        <div id="section3" class="tab-pane fade">
          <div class="row">
        		<div class="col-md-4">
        		<h4>Request # <strong style="color:#07bfea"> <?php echo $_SESSION['modify_id']; ?></strong></h4>
        		</div>
        		<div class="col-md-4">
              <br><br>
        			<div class="title">
                <?php $select_user = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$_SESSION['group_leader2']."'");
                      foreach ($select_user as $key => $value);
                  ?>
              <span>GL : <?php echo $value['school_id'] ?> - <?php echo $value['lname'] ?> , <?php echo $value['fname'] ?></span>
        			</div>
        		</div>
        		<div class="col-md-4">
              <br><br>
              <div class="title">
              <span>GN : " <?php echo $_SESSION['group_name'] ?> " </span>
              </div>
        		</div>
        			<div class="col-md-12">

        						<div class="content">
                      <div class="card">
                        <div class="content">
                          <div class="row">
                             <div class="col-md-4">
                            <div class="">
                              <div class=""style="color:gray;">
                              <h5><i class="fa fa-plus"></i> Add members </h5>
                             </div>
                            </div>
                            <form class="form-inline my-2 my-lg-0" action="modifyUserRequests.php"method="post">
                            <input class="form-control mr-sm-2"style="border-color: green;" type="number" name="add_member" placeholder="Enter ID number" aria-label="Search" required>
                            <button type="submit" name=""  class="btn btn-success btn-fill" >
                              <i class="fa fa-search"></i>
                            </button>
                             </form>
                                </div>
                                <div class="col-md-4">
                                  <div class=""style="color:gray;">
                                  <h5><i class="fa fa-pencil"></i> Change Group Leader </h5>
                                 </div>
                             <select class="form-control"name="group_leader"id="group_leader"onchange="session_groupLeader(this.value)"style="border-color: green;" required>
                             <option value="" selected disabled hidden >Choose here..</option>
                             <?php
                             $check_slip = "SELECT
                                            a.borrower_slip_id,a.group_id,
                                            b.group_id,b.group_name,b.group_leader_id,
                                            c.group_id,c.user_id,
                                            d.user_id,d.school_id,d.fname,d.lname

                                            from borrower_slip a
                                            left join group_table b on a.group_id = b.group_id
                                            left join group_members c on b.group_id = c.group_id
                                            left join users d on c.user_id = d.user_id

                                            where a.borrower_slip_id = $modID2";
                               $res = mysqli_query($dbcon,$check_slip);
                                  foreach($res as $keys => $values)
                                  {
                             ?>
                               <option value="<?php echo $values['user_id'] ?>"><?php echo $values['fname'] ?> , <?php echo $values['lname'] ?></option>
                             <?php } ?>

                             </select>
                             </div>
                             <div class="col-md-4">
                               <div class=""style="color:gray;">
                               <h5><i class="fa fa-pencil"></i> Change Group Name </h5>
                               <form class="form-inline my-2 my-lg-0" action="modifyUserRequests.php"method="post">
                               <input class="form-control mr-sm-2"style="border-color: green;" type="text" name="new_group_name" placeholder="Enter new group name"  required>
                               <button type="submit"   class="btn btn-success btn-fill">
                                 <i class="fa fa-save"></i>
                               </button>
                                </form>
                              </div>
                             </div>


                        </div>
                        </div>
                         <div class="row">
                          <div class="col-md-4">
                             <div class="header">
                               <div class=""style="color:gray;">
                               <h5><i class="fa fa-users"></i> All Members </h5>
                              </div>
                             </div>
                          </div>
                          <div class="col-md-4">
                           <?php include('errors.php'); ?>
                         </div>
                         </div>

                        <div class="content">

                         <div class="card">


                         <table class="table table-bordered table-hover ">
                           <thead>

                             <th>ID Number</th>
                             <th>First Name</th>
                             <th>Last Name</th>
                             <th>Action</th>
                           </thead>
                           <tbody >

                           <?php
                           $check_slip = "SELECT
                                          a.borrower_slip_id,a.group_id,
                                          b.group_id,b.group_name,b.group_leader_id,
                                          c.group_id,c.user_id,
                                          d.user_id,d.school_id,d.fname,d.lname

                                          from borrower_slip a
                                          left join group_table b on a.group_id = b.group_id
                                          left join group_members c on b.group_id = c.group_id
                                          left join users d on c.user_id = d.user_id

                                          where a.borrower_slip_id = $modID2";
                             $res = mysqli_query($dbcon,$check_slip);
                                foreach($res as $keys => $values)
                                {
                           ?>
                           <tr>
                                <td><?php echo $values['school_id']; ?></td>
                                <td><?php echo $values['fname']; ?></td>
                                <td> <?php echo $values['lname']; ?></td>
                                <td><a href="modifyUserRequests.php?action=removeMember&memID=<?php echo $values['user_id']; ?>&grID=<?php echo $values['group_id']; ?>"onclick="return confirm('Are you sure ?')"><span class="text-danger">Remove</span></a></td>
                           </tr>

                       <?php }
                     ?>

                           </tbody>
                           </table>
                              </div>
                             </div>

        						</div>
        			       </div>
        			</div>
           </div>
        </div>

        <!-- Mini Modal -->
        <div class="modal fade  modal-primary" id="myModal1" data-backdrop="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header justify-content-center">
                      <span >New items to be added..</span>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body ">
                      <div class="content">
        							<table class="table table-hover table-bordered"style='width:100%;' border="0" alt="Null">
                        <thead >
                          <tr>
                            <th >ID</th>
                            <th >Item name</th>
                            <th >Category</th>
                            <th >QTY</th>
                            <th >Available</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($_SESSION['item_tray'] as $key => $value) {
                               $checkStock = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_id = '".$value['storage_id']."' and utensils_id = '".$value['item_id']."'");
                               foreach ($checkStock as $key => $values) {
                            ?>
                            <tr>
                              <td><?php echo $value['item_id']; ?></td>
                              <td><?php echo $value['item_name']; ?></td>
                              <td><?php echo $value['category']; ?></td>
                              <td><?php echo $value['qty']; ?></td>
                              <?php if ($value['qty'] > $values['storage_qty']) {
                                ?>
                                 <td> <span class="text-danger"><?php echo $values['storage_qty']; ?> (insufficient!)</span> </td>
                                <?php
                              }else {
                                ?>
                                 <td><?php echo $values['storage_qty']; ?></td>
                                <?php
                              } ?>

                              <td><a href="modifyUserRequests.php?action=delete&ids=<?php echo $value["item_id"]; ?>"><span class="text-info">cancel</span></a></td>
                            </tr>
                          <?php }
                          } ?>

                        </tbody>
                      </table>
                    </div>
               </div>
                    <div class="modal-footer">
                        <a href="modifyUserRequests.php?save" class="btn btn-sm btn-info btn-fill">Save</a>
                        <a href="modifyUserRequests.php?clear" class="btn btn-sm btn-danger btn-fill">Clear</a>
                    </div>
                </div>
            </div>
        </div>
        <!--  End Modal -->
                        </div>
                     </div>
                 </div>
              </div>
           </div>
        </div>
    </div>
</div>
<?php include('dataTables2.php'); ?>
<script type="text/javascript">
var trapNumber2 = document.getElementById("numInput2")
trapNumber2.addEventListener("keydown", function(e) {
// prevent: "e", "=", ",", "-", "."
if ([69, 187, 188, 189, 190].includes(e.keyCode)) {
e.preventDefault();
}
})
</script>
<script type="text/javascript">

var trapNumber = document.getElementById("numInput")
trapNumber.addEventListener("keydown", function(e) {
// prevent: "e", "=", ",", "-", "."
if ([69, 187, 188, 189, 190].includes(e.keyCode)) {
e.preventDefault();
}
})
var trapNumber1 = document.getElementById("numInput1")
trapNumber1.addEventListener("keydown", function(e) {
// prevent: "e", "=", ",", "-", "."
if ([69, 187, 188, 189, 190].includes(e.keyCode)) {
e.preventDefault();
}
})

    $('#requestTable1').DataTable();
</script>
<?php include('footer.php'); ?>
