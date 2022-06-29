<?php include('header.php');
?>

<script type="text/javascript">

// session_inventory_date
function session_inventory_date(value) {
        $.ajax({
            type: "POST",
            url: 'ajaxrequest/sessionInventory_admin.php',
            data: 'inventory_ses_date=' + value,
            dataType: 'json',
            success: function (data) {
              if (data==1) {
                // location.reload();
                location.href = 'staff_inventory_report.php';
                setInterval( 1000);
              }
            }
        });
    }
  function session_inventory_storage(value) {
          $.ajax({
              type: "POST",
              url: 'ajaxrequest/sessionInventory_admin.php',
              data: 'inventory_ses_storage_staff=' + value,
              dataType: 'json',
              success: function (data) {
                if (data==1) {
                  // location.reload();
                  location.href = 'staff_inventory_report.php';
                  setInterval( 1000);
                }
              }
          });
      }
</script>
<?php
$storageID = $_SESSION['user']['storage_id'];
if (isset($_GET['inventory_rep'])) {
 $_SESSION['default_report_inventory'] = 1;
 $_SESSION['default_control_inventory'] = 0;
 $_SESSION['default_inventory_date'] = 1;
 $_SESSION['in_report_date1'] = '00/00/0000';
 $_SESSION['in_report_date2'] = '00/00/0000';
  unset($_SESSION['incident_report']);

}
if (isset($_GET['close_modal_inventory'])) {
 $_SESSION['default_control_inventory'] = 1;
 $_SESSION['default_inventory_date'] = 1;
}
  $inventory_current_date = 1;
  $create_incident_report = 2;
  $all_storage_report = 3;
  $all_inventory_date = 2;
  $custom_inventory_date = 3;
  $all_storage = 1;

  $selected = "selected";
 ?>
 <?php
 if (isset($_GET['add_incident_report'])) {
  $_SESSION['incident_modal_control'] = 1;
  $_SESSION['report_id'] = $_GET['id'];
 }
 if (isset($_GET['close_modal_incident'])) {
  $_SESSION['incident_modal_control'] = 0;
 }
 if (isset($_GET['clear_report'])) {
   unset($_SESSION['incident_report']);
 } ?>
 <?php
 if(isset($_POST["add_report_item"]))
  {
      $storageID = $_SESSION['user']['storage_id'];
      $utensils_id = mysqli_real_escape_string($dbcon,$_POST['utensils_id']);
      $lost_qty = mysqli_real_escape_string($dbcon,$_POST['lost_qty']);
      $damaged_qty = mysqli_real_escape_string($dbcon,$_POST['damaged_qty']);
      $comment = mysqli_real_escape_string($dbcon,$_POST['comment']);
      if (empty($lost_qty)&&empty($damaged_qty)) {
        array_push($errors,"Please fill in fields!");
      }else {

      if ($lost_qty < 0 || $damaged_qty < 0) {
        array_push($errors,"Invalid quantity!");
      }else {
      if (ctype_space($comment)) {
          array_push($errors,"Please fill in fields!");
      }else {
      $Query = mysqli_query($dbcon,"SELECT * FROM storage_stocks where  utensils_id = $utensils_id and storage_id = $storageID");
      $checkqty = mysqli_fetch_array($Query);

       $qty =  (int)$lost_qty + (int)$damaged_qty;

      if ($qty > $checkqty['storage_qty']) {
        array_push($errors,"Failed! invalid quantity!");
      }else {

   			if(isset($_SESSION["incident_report"]))
   			{
   					 $item_array_id = array_column($_SESSION["incident_report"], "utensils_id");
   					 if(!in_array($_POST['utensils_id'], $item_array_id))
   					 {
   								$count = count($_SESSION["incident_report"]);
   								$item_array = array(
                     'utensils_id'    =>     $_POST['utensils_id'],
                     'lost_qty'  =>     $_POST['lost_qty'],
                     'damaged_qty'  =>     $_POST['damaged_qty'],
                     'comment'        =>     $_POST["comment"]

   								);
   								$_SESSION["incident_report"][$count] = $item_array;
               $_SESSION['incident_modal_control'] = 0;
   					 }
             else
   					 {
   						 array_push($errors,"Item Already Added!");
   					 }

   			}
   			else
   			{

   					 $item_array = array(
               'utensils_id'    =>     $_POST['utensils_id'],
               'lost_qty'  =>     $_POST['lost_qty'],
               'damaged_qty'  =>     $_POST['damaged_qty'],
               'comment'        =>     $_POST["comment"]
   					 );
             $_SESSION['incident_modal_control'] = 0;
   					 $_SESSION["incident_report"][0] = $item_array;

   			}
      }

      }
     }
    }

  }
  if(isset($_GET["action"]))
  {
      if($_GET["action"] == "remove_report")
      {
           foreach(array_filter($_SESSION["incident_report"]) as $keys => $values)
           {
                if($values["utensils_id"] == $_GET["id"])
                {
                     // unset($_SESSION["item_tray"][$keys]);
                      $_SESSION["incident_report"][$keys] = Null;
                     // echo '<script>alert("Item cancelled!")</script>';
                     // echo '<script>window.location="modifyUserRequests.php"</script>';

                }
           }
      }
  }   ?>
  <?php
  if (isset($_GET['save_incident_report'])) {
    foreach (array_filter($_SESSION['incident_report']) as $key => $value) {
      $combined = (int)$value['lost_qty'] + (int)$value['damaged_qty'];
      $checkUtensils = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_qty < $combined and utensils_id = '".$value['utensils_id']."' and storage_id = $storageID");
      if (mysqli_num_rows($checkUtensils)>0) {
        array_push($errors,"Failed! Please review report quantities!");
        $err = true;
      }else {
        $err = false;
      }
    }
    if ($err == false) {
      $user = $_SESSION['user']['user_id'];
      $checkReportControl = mysqli_query($dbcon,"SELECT * FROM report_date_control where date_range = CURRENT_DATE() order by rd_control_id desc limit 1")or die(mysqli_error($dbcon));
      if (mysqli_num_rows($checkReportControl)<=0) {
        $insertNewDate =  mysqli_query($dbcon,"INSERT INTO report_date_control (date_range)values(NOW())");
        //fetch new control id
        $fetchControl = mysqli_query($dbcon,"SELECT * FROM report_date_control order by rd_control_id desc limit 1")or die(mysqli_error($dbcon));
        foreach ($fetchControl as $key => $dControlId);
        //insert report master
        $insertReportMaster = mysqli_query($dbcon,"INSERT INTO reports (rd_control_id,storage_id,report_date,reported_by,report_type_id)
        VALUES('".$dControlId['rd_control_id']."','$storageID',NOW(),'$user',3)");

        $fetchReport = mysqli_query($dbcon,"SELECT * FROM reports order by report_id desc limit 1")or die(mysqli_error($dbcon));
        foreach ($fetchReport as $key => $reportId);
        // insert report details
        foreach (array_filter($_SESSION['incident_report']) as $key => $rContents) {
          $insertIncidentDetails = mysqli_query($dbcon,"INSERT INTO incident_report_details (report_id,utensils_id,storage_id,lost_qty,damaged_qty,comment)
          VALUES('".$reportId['report_id']."','".$rContents['utensils_id']."','$storageID','".$rContents['lost_qty']."','".$rContents['damaged_qty']."','".$rContents['comment']."')");
        }
        // execute reported utensils
        foreach (array_filter($_SESSION['incident_report']) as $key => $values) {
          $combined1 = (int)$values['lost_qty'] + (int)$values['damaged_qty'];
         $checkUtensilsx = mysqli_query($dbcon,"SELECT * FROM storage_stocks where  utensils_id = '".$values['utensils_id']."' and storage_id = $storageID");
         foreach ($checkUtensilsx as $key => $storage) {
           $newStQty = $storage['storage_qty'] - $combined1;
           $newLost = $storage['lost_qty'] + $values['lost_qty'];
           $newDamaged= $storage['damaged_qty'] + $values['damaged_qty'];
           $updateStorage = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStQty,lost_qty = $newLost,damaged_qty = $newDamaged
           where utensils_id = '".$values['utensils_id']."'and storage_id = $storageID ");
           echo "<script>alert('Report successfully saved!');window.location.href='kitchen_staff_home.php';</script>";
         }
        }

      }else {
        //fetch new control id
        $fetchControl = mysqli_query($dbcon,"SELECT * FROM report_date_control order by rd_control_id desc limit 1")or die(mysqli_error($dbcon));
        foreach ($fetchControl as $key => $dControlId);
        //insert report master
        $insertReportMaster = mysqli_query($dbcon,"INSERT INTO reports (rd_control_id,storage_id,report_date,reported_by,report_type_id)
        VALUES('".$dControlId['rd_control_id']."','$storageID',NOW(),'$user',3)");

        $fetchReport = mysqli_query($dbcon,"SELECT * FROM reports order by report_id desc limit 1")or die(mysqli_error($dbcon));
        foreach ($fetchReport as $key => $reportId);
        // insert report details
        foreach (array_filter($_SESSION['incident_report']) as $key => $rContents) {
          $insertIncidentDetails = mysqli_query($dbcon,"INSERT INTO incident_report_details (report_id,utensils_id,storage_id,lost_qty,damaged_qty,comment)
          VALUES('".$reportId['report_id']."','".$rContents['utensils_id']."','$storageID','".$rContents['lost_qty']."','".$rContents['damaged_qty']."','".$rContents['comment']."')");
        }
        // execute reported utensils
        foreach (array_filter($_SESSION['incident_report']) as $key => $values) {
          $combined1 = (int)$values['lost_qty'] + (int)$values['damaged_qty'];
         $checkUtensilsx = mysqli_query($dbcon,"SELECT * FROM storage_stocks where  utensils_id = '".$values['utensils_id']."' and storage_id = $storageID");
         foreach ($checkUtensilsx as $key => $storage) {
           $newStQty = $storage['storage_qty'] - $combined1;
           $newLost = $storage['lost_qty'] + $values['lost_qty'];
           $newDamaged= $storage['damaged_qty'] + $values['damaged_qty'];
           $updateStorage = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStQty,lost_qty = $newLost,damaged_qty = $newDamaged
           where utensils_id = '".$values['utensils_id']."'and storage_id = $storageID ");
           echo "<script>alert('Report successfully saved!');window.location.href='kitchen_staff_home.php';</script>";
         }
        }
      }



    }

  } ?>
<?php
if (isset($_GET['verify'])) {
  $reportIdx = $_GET['verify'];
  $user = $_SESSION['user']['user_id'];
  $updateReport = mysqli_query($dbcon,"UPDATE reports set date_verified = NOW(),verified_by = $user,status = 1 where report_id = $reportIdx");
}
if (isset($_GET['update_report'])) {
  $_SESSION['report_update_ID']=$_GET['update_report'];
  $_SESSION['report_modal_control']=1;
}
if (isset($_GET['close_modal_report'])) {
  $_SESSION['report_modal_control']=0;
} ?>
<?php
if (isset($_GET['add_found'])) {
  $trapAddFound = mysqli_query($dbcon,"SELECT * FROM incident_report_details where found = lost_qty
  and report_id = '".$_SESSION['report_update_ID']."' and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
  if (mysqli_num_rows($trapAddFound)>=1) {
    array_push($errors,"Limit reached!");
  }else {
    $AddFound = mysqli_query($dbcon,"SELECT * FROM incident_report_details where
     report_id = '".$_SESSION['report_update_ID']."' and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
     foreach ($AddFound as $key => $found);

    $newFound = $found['found'] + 1;
    $updateFound = mysqli_query($dbcon,"UPDATE incident_report_details set found = $newFound where report_id = '".$_SESSION['report_update_ID']."'
    and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");

    $checkAddFound = mysqli_query($dbcon,"SELECT * FROM incident_report_details where
     report_id = '".$_SESSION['report_update_ID']."' and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
     foreach ($checkAddFound as $key => $found2);
    $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage_stocks where  utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
    foreach ($fetchStorage as $key => $newStqty);
    $newStorage_QTY = $newStqty['storage_qty'] + 1;
    $newStorage_lost_qty = $newStqty['lost_qty'] - 1;
    $updateStorage = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStorage_QTY,lost_qty = $newStorage_lost_qty
    where utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
  }
}
//minus FOUND
if (isset($_GET['minus_found'])) {
  $trapMinusFound = mysqli_query($dbcon,"SELECT * FROM incident_report_details where found = 0
  and report_id = '".$_SESSION['report_update_ID']."' and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
  if (mysqli_num_rows($trapMinusFound)>=1) {
    array_push($errors,"Limit reached!");
  }else {
    $MinusFound = mysqli_query($dbcon,"SELECT * FROM incident_report_details where
     report_id = '".$_SESSION['report_update_ID']."' and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
     foreach ($MinusFound as $key => $minfound);

    $newFound = $minfound['found'] - 1;
    $updateFound = mysqli_query($dbcon,"UPDATE incident_report_details set found = $newFound where report_id = '".$_SESSION['report_update_ID']."'
    and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");

    $checkMinusFound = mysqli_query($dbcon,"SELECT * FROM incident_report_details where
     report_id = '".$_SESSION['report_update_ID']."' and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
     foreach ($checkMinusFound as $key => $minfound2);
    $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage_stocks where  utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
    foreach ($fetchStorage as $key => $newStqty);
    //no changes

      $newStorage_QTY = $newStqty['storage_qty'] - 1;
      $newStorage_lost_qty = $newStqty['lost_qty'] + 1;
      $updateStorage = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStorage_QTY,lost_qty = $newStorage_lost_qty
      where utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");


  }
} ?>

<?php // replacement
if (isset($_GET['add_replaced'])) {
  $trapAddReplaced = mysqli_query($dbcon,"SELECT * FROM incident_report_details where damaged_qty = replaced
  and report_id = '".$_SESSION['report_update_ID']."' and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
  if (mysqli_num_rows($trapAddReplaced)>=1) {
    array_push($errors,"Limit reached!");
  }else {
    $AddReplaced = mysqli_query($dbcon,"SELECT * FROM incident_report_details where
     report_id = '".$_SESSION['report_update_ID']."' and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
     foreach ($AddReplaced as $key => $replaced);

    $newReplaced = $replaced['replaced'] + 1;
    $updateReplaced = mysqli_query($dbcon,"UPDATE incident_report_details set replaced = $newReplaced where report_id = '".$_SESSION['report_update_ID']."'
    and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");

    $checkAddReplaced = mysqli_query($dbcon,"SELECT * FROM incident_report_details where
     report_id = '".$_SESSION['report_update_ID']."' and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
     foreach ($checkAddReplaced as $key => $replaced2);
    $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage_stocks where  utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
    foreach ($fetchStorage as $key => $newStqty);

    $newStorage_QTY = $newStqty['storage_qty'] + 1;
    $newStorage_damaged_qty = $newStqty['damaged_qty'] - 1;
    $updateStorage = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStorage_QTY,damaged_qty = $newStorage_damaged_qty
    where utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
  }
}
//minus replaced
if (isset($_GET['minus_replaced'])) {
  $trapMinusReplaced = mysqli_query($dbcon,"SELECT * FROM incident_report_details where replaced = 0
  and report_id = '".$_SESSION['report_update_ID']."' and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
  if (mysqli_num_rows($trapMinusReplaced)>=1) {
    array_push($errors,"Limit reached!");
  }else {
    $MinusReplaced = mysqli_query($dbcon,"SELECT * FROM incident_report_details where
     report_id = '".$_SESSION['report_update_ID']."' and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
     foreach ($MinusReplaced as $key => $minReplaced);

    $newReplaced  = $minReplaced['replaced'] - 1;
    $updateReplaced = mysqli_query($dbcon,"UPDATE incident_report_details set replaced = $newReplaced where report_id = '".$_SESSION['report_update_ID']."'
    and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");

    $checkMinusFound = mysqli_query($dbcon,"SELECT * FROM incident_report_details where
     report_id = '".$_SESSION['report_update_ID']."' and utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
     foreach ($checkMinusFound as $key => $minReplaced2);
    $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage_stocks where  utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");
    foreach ($fetchStorage as $key => $newStqty);
    //no changes
      $newStorage_QTY = $newStqty['storage_qty'] - 1;
      $newStorage_damaged_qty = $newStqty['damaged_qty'] + 1;
      $updateStorage = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStorage_QTY,damaged_qty = $newStorage_damaged_qty
      where utensils_id = '".$_GET['uID']."' and storage_id = '".$_GET['sID']."'");


  }
} ?>
<?php
if (isset($_POST['update_report_confirm'])) {
  $user = $_SESSION['user']['user_id'];
  $comment = mysqli_real_escape_string($dbcon,$_POST['comment']);
  $updateReports = mysqli_query($dbcon,"UPDATE reports set date_updated = NOW(),updated_by = $user,comment = '$comment' where report_id = '".$_SESSION['report_update_ID']."'");
  echo "<script>alert('Successfully saved!');</script>";
  $_SESSION['report_modal_control']=0;
} ?>
<br><br>
<div class="content">
    <div class="container-fluid">

              <div class="card ">
                        <div class="header">
                          <?php
                          $stID = $_SESSION['user']['storage_id'];
                          $inventoryName = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $stID");
                          foreach ($inventoryName as $key => $value); ?>
                        <h4 class="title"><?php echo $value['storage_name']; ?> Inventory Report</h4>
                        <p class="category"> </p>
                          </div>
                    <div class="content">
                      <div class="row">
                        <div class="col-md-3">
                          <br>
                          <span><select class="form-control" name="inventory_ses_storage"id="inventory_ses_storage"onchange="session_inventory_storage(this.value)">
                            <option value="<?php echo $all_storage; ?>"<?php if ($_SESSION['default_report_inventory'] == 1) {  echo $selected;  } ?>>  Storage Inventory</option>
                            <option value="<?php echo $create_incident_report; ?>"<?php if ($_SESSION['default_report_inventory'] == 2) {  echo $selected;  } ?>>Create Incident Report</option>
                            <option value="<?php echo $all_storage_report; ?>"<?php if ($_SESSION['default_report_inventory'] == 3) {  echo $selected;  } ?>>  Incident Report</option>
                          </select></span>
                          </div>
                         <?php if ($_SESSION['default_report_inventory']==1 || $_SESSION['default_report_inventory']==3) {
                         ?>
                        <div class="col-md-3">
                          <br>
                          <select class="form-control"  name="inventory_ses_date"id="inventory_ses_date"onchange="session_inventory_date(this.value)">
                            <option value="<?php echo  $inventory_current_date; ?>"<?php if ($_SESSION['default_inventory_date'] == 1) {  echo $selected;  } ?>>  Today</option>
                            <option value="<?php echo $all_inventory_date; ?>"<?php if ($_SESSION['default_inventory_date'] == 2) { echo $selected; } ?>>  All Date</option>
                            <option value="<?php echo $custom_inventory_date; ?>"<?php if ($_SESSION['default_inventory_date'] == 3) { echo $selected; } ?> data-toggle="modal" data-target="#myModal1">  Custom Date</option>
                          </select>
                        </div>
                          <div class="col-md-3">
                            <br>
                            <!-- <button class="btn btn-fill btn-success"data-toggle="modal" data-target="#myModal2">Generate Report</button> -->
                            <a href="staff_inventory_generate_report.php"class="btn btn-fill btn-success">Generate Report</a>
                          </div>
                        <?php }?>
                      </div>
                    </div>
          <div class="content">
            <?php
              if ($_SESSION['default_report_inventory']==1) {  //if by storage
                if ($_SESSION['default_inventory_date']==1) {      /// by storage current date
                  ?>
                  <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Orig-qty</th>
                        <th>cur-qty</th>
                        <th>Items</th>
                        <th>Category</th>
                        <th>Model</th>
                        <th>Serial</th>
                        <th>Date purchased</th>
                        <th>Unit cost</th>
                        <th>Remarks</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $fetchInventoryMasterS = mysqli_query($dbcon,"SELECT * FROM inventory where date_added = CURRENT_DATE()");
                      foreach ($fetchInventoryMasterS as $key => $value);
                       $queryString = "SELECT *
                                    from utensils a
                                    left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                                    left join umsr c on a.umsr = c.id
                                    left join inventory_storage d on a.utensils_id = d.utensils_id
                                    where d.storage_id = $stID and a.status !=0 and d.original_stock != 0 and d.inventory_control_id = '".$value['inventory_control_id']."'
                                    group by d.utensils_id
                                    ";
                        $result = mysqli_query($dbcon,$queryString);
                        foreach ($result as $key => $invS) {
                      ?>
                      <tr>
                        <td class="bg bg-info"><?php echo $invS['utensils_id'] ?></td>
                        <?php
                          $currentStock = $invS['stock_remain'] + $invS['reserved_qty'] ;
                        ?>
                        <td class="bg bg-primary"><?php echo $invS['original_stock']; ?></td>
                        <td class="bg bg-primary"><?php echo $currentStock; ?></td>
                        <td class="bg bg-info"><?php echo $invS['utensils_name'] ?></td>
                        <td class="bg bg-info"><?php echo $invS['category'] ?></td>
                        <td class="bg bg-info"><?php echo $invS['model'] ?></td>
                        <td class="bg bg-info"><?php echo $invS['serial_no'] ?></td>
                        <td class="bg bg-info"><?php echo $invS['date_purchased'] ?></td>
                        <td class="bg bg-info">₱ <?php echo number_format($invS['cost'],2); ?></td>
                        <?php if ($invS['original_stock']== $currentStock){
                         ?> <td class="bg bg-success"><i class="fa fa-check text-success"></i> Complete </td> <?php
                       }else{
                         ?>
                         <td class="bg bg-danger">
                           <?php if ($invS['lost_qty']>0) {?>(<?php echo $invS['lost_qty'];?>) - Missing <br> <?php } ?>
                           <?php if ($invS['damaged_qty']>0) {?>(<?php echo $invS['damaged_qty'];?>) - Damaged <br><?php } ?>
                           <?php if ($invS['on_use']>0) {?> <b class="bg text-info"> (<?php echo $invS['on_use'];?>) - On use </b><br><?php } ?>
                         </td>
                         <?php
                       }
                         ?>
                      </tr>
                    <?php
                  }?>
                    </tbody>
                  </table>

                  <?php
                } // end of by storage current date
                if ($_SESSION['default_inventory_date']==2) {
                  ?>
                  <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Orig-qty</th>
                        <th>cur-qty</th>
                        <th>Items</th>
                        <th>Category</th>
                        <th>Model</th>
                        <th>Serial</th>
                        <th>Date purchased</th>
                        <th>Unit cost</th>
                        <th>Remarks</th>
                      </tr>
                    </thead>
                    <?php $fetchInventoryMasterS = mysqli_query($dbcon,"SELECT * FROM inventory order by inventory_control_id desc");
                    foreach ($fetchInventoryMasterS as $key => $value){  ?>
                      <thead>
                        <tr>
                          <th colspan="10"class="text-center"><?php echo $value['date_added']; ?></th>
                        </tr>
                      </thead>
                    <tbody>
                      <?php
                       $queryString = "SELECT *
                                    from utensils a
                                    left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                                    left join umsr c on a.umsr = c.id
                                    left join inventory_storage d on a.utensils_id = d.utensils_id
                                    where d.storage_id = $stID and a.status !=0
                                    and d.original_stock != 0 and d.inventory_control_id = '".$value['inventory_control_id']."'
                                    order by d.utensils_id
                                    ";
                        $result = mysqli_query($dbcon,$queryString);
                        foreach ($result as $key => $invS) {
                          $currentStock = $invS['stock_remain'] + $invS['reserved_qty'];
                      ?>
                      <tr>
                        <td class="bg bg-info"><?php echo $invS['utensils_id'] ?></td>
                        <td class="bg bg-primary"><?php echo $invS['original_stock']; ?></td>
                        <td class="bg bg-primary"><?php echo $currentStock; ?></td>
                        <td class="bg bg-info"><?php echo $invS['utensils_name'] ?></td>
                        <td class="bg bg-info"><?php echo $invS['category'] ?></td>
                        <td class="bg bg-info"><?php echo $invS['model'] ?></td>
                        <td class="bg bg-info"><?php echo $invS['serial_no'] ?></td>
                        <td class="bg bg-info"><?php echo $invS['date_purchased'] ?></td>
                        <td class="bg bg-info">₱ <?php echo number_format($invS['cost'],2); ?></td>
                        <?php if ($invS['original_stock']== $currentStock){
                         ?> <td class="bg bg-success"><i class="fa fa-check text-success"></i> Complete </td> <?php
                       }else{
                         ?>
                         <td class="bg bg-danger">
                           <?php if ($invS['lost_qty']>0) {?>(<?php echo $invS['lost_qty'];?>) - Missing <br> <?php } ?>
                           <?php if ($invS['damaged_qty']>0) {?>(<?php echo $invS['damaged_qty'];?>) - Damaged <br><?php } ?>
                           <?php if ($invS['on_use']>0) {?> <b class="bg text-info"> (<?php echo $invS['on_use'];?>) - On use </b><br><?php } ?>
                         </td>
                         <?php
                       }
                         ?>
                      </tr>
                    <?php
                  }?>
                    </tbody>
                  <?php } ?>
                  </table>
                  <?php
                }
                if ($_SESSION['default_inventory_date']==3) {
                  ?>
                  <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Orig-qty</th>
                        <th>cur-qty</th>
                        <th>Items</th>
                        <th>Category</th>
                        <th>Model</th>
                        <th>Serial</th>
                        <th>Date purchased</th>
                        <th>Unit cost</th>
                        <th>Remarks</th>
                      </tr>
                    </thead>
                    <?php
                    $date1 = $_SESSION['in_report_date1'];
                    $date2 = $_SESSION['in_report_date2'];
                    $fetchInventoryMasterS = mysqli_query($dbcon,"SELECT * FROM inventory where date_added >= '$date1' and date_added <= '$date2'");
                    foreach ($fetchInventoryMasterS as $key => $value){  ?>
                      <thead>
                        <tr>
                          <th colspan="10"class="text-center"><?php echo $value['date_added']; ?></th>
                        </tr>
                      </thead>
                    <tbody>
                      <?php
                       $queryString = "SELECT *
                                    from utensils a
                                    left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                                    left join umsr c on a.umsr = c.id
                                    left join inventory_storage d on a.utensils_id = d.utensils_id
                                    where d.storage_id = $stID and a.status !=0
                                    and d.original_stock != 0 and d.inventory_control_id = '".$value['inventory_control_id']."'
                                    order by d.utensils_id
                                    ";
                        $result = mysqli_query($dbcon,$queryString);
                        foreach ($result as $key => $invS) {
                          $currentStock = $invS['stock_remain'] + $invS['reserved_qty'];
                      ?>
                      <tr>
                        <td class="bg bg-info"><?php echo $invS['utensils_id'] ?></td>
                        <td class="bg bg-primary"><?php echo $invS['original_stock']; ?></td>
                        <td class="bg bg-primary"><?php echo $currentStock; ?></td>
                        <td class="bg bg-info"><?php echo $invS['utensils_name'] ?></td>
                        <td class="bg bg-info"><?php echo $invS['category'] ?></td>
                        <td class="bg bg-info"><?php echo $invS['model'] ?></td>
                        <td class="bg bg-info"><?php echo $invS['serial_no'] ?></td>
                        <td class="bg bg-info"><?php echo $invS['date_purchased'] ?></td>
                        <td class="bg bg-info">₱ <?php echo number_format($invS['cost'],2); ?></td>
                        <?php if ($invS['original_stock']== $currentStock){
                         ?> <td class="bg bg-success"><i class="fa fa-check text-success"></i> Complete </td> <?php
                       }else{
                         ?>
                         <td class="bg bg-danger">
                           <?php if ($invS['lost_qty']>0) {?>(<?php echo $invS['lost_qty'];?>) - Missing <br> <?php } ?>
                           <?php if ($invS['damaged_qty']>0) {?>(<?php echo $invS['damaged_qty'];?>) - Damaged <br><?php } ?>
                           <?php if ($invS['on_use']>0) {?> <b class="bg text-info"> (<?php echo $invS['on_use'];?>) - On use </b><br><?php } ?>
                         </td>
                         <?php
                       }
                         ?>
                      </tr>
                    <?php
                  }?>
                    </tbody>
                  <?php } ?>
                  </table>
                  <?php
                }
              } // end of by storage by storage
              if ($_SESSION['default_report_inventory']==2) {
                ?>
              <div class="row">
                <div class="col-md-6">
                <table class="table table-bordered table-striped table-hover" id="report_table">
                  <thead>
                    <tr>
                      <th></th>
                      <th>ID</th>
                      <th>Orig-qty</th>
                      <th>cur-qty</th>
                      <th>Items</th>
                      <th>Category</th>
                      <th>Model</th>
                      <th>Serial</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $fetchInventoryMasterS = mysqli_query($dbcon,"SELECT * FROM inventory where date_added = CURRENT_DATE()");
                    foreach ($fetchInventoryMasterS as $key => $value);
                     $queryString = "SELECT *
                                  from utensils a
                                  left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                                  left join umsr c on a.umsr = c.id
                                  left join inventory_storage d on a.utensils_id = d.utensils_id
                                  where d.storage_id = $stID and a.status !=0 and d.original_stock != 0 and d.inventory_control_id = '".$value['inventory_control_id']."'
                                  group by d.utensils_id
                                  ";
                      $result = mysqli_query($dbcon,$queryString);
                      foreach ($result as $key => $invS) {
                    ?>
                    <tr>
                      <td><a href="?add_incident_report&id=<?php echo $invS['utensils_id'] ?>">Report</a></td>
                      <td class="bg bg-info"><?php echo $invS['utensils_id'] ?></td>
                      <?php
                        $currentStock = $invS['stock_remain'] + $invS['reserved_qty'];
                      ?>
                      <td class="bg bg-primary"><?php echo $invS['original_stock']; ?></td>
                      <td class="bg bg-primary"><?php echo $currentStock; ?></td>
                      <td class="bg bg-info"><?php echo $invS['utensils_name'] ?></td>
                      <td class="bg bg-info"><?php echo $invS['category'] ?></td>
                      <td class="bg bg-info"><?php echo $invS['model'] ?></td>
                      <td class="bg bg-info"><?php echo $invS['serial_no'] ?></td>
                    </tr>
                  <?php
                }?>
                  </tbody>
                </table>
                </div>
                <div class="col-md-6">
                  <?php if (isset($_SESSION['incident_report'])) {
                    ?>
                    <div class="card">
                      <div class="content">
                        <div class="row">
                          <div class="col-md-6">
                            <span>
                            <a href="?save_incident_report"onclick="return confirm('Confirm save!')"class="btn btn-sm btn-fill btn-success">Save report <i class="fa fa-check"></i></a>
                            </span>
                            <span>
                             <a href="?clear_report"class="btn btn-sm btn-fill btn-warning">Clear report <i class="fa fa-trash"></i></a>
                             </span>
                          </div>
                        </div>
                        <h5>Items to report :</h5>
                        <table class="table">
                           <thead>
                             <tr>
                               <th></th>
                               <th>ID</th>
                               <th>ITEMS</th>
                               <th>LOST QTY</th>
                               <th>DAMAGE QTY</th>
                               <th>COMMENT</th>
                             </tr>
                           </thead>
                           <tbody>
                             <?php foreach (array_filter($_SESSION['incident_report']) as $key => $value) {
                               $query = mysqli_query($dbcon,"Select * FROM utensils where utensils_id = ".$value['utensils_id']);
                               foreach ($query as $key => $values) {

                             ?>
                             <tr>
                               <td><a href="?action=remove_report&id=<?php echo $value['utensils_id']; ?>"><i class="fa fa-times text-danger"></i></a></td>
                               <td><?php echo $value['utensils_id']; ?></td>
                               <td><?php echo $values['utensils_name']; ?></td>
                               <td class="bg bg-success"><?php echo $value['lost_qty']; ?></td>
                               <td class="bg bg-success"><?php echo $value['damaged_qty']; ?></td>
                               <td><?php echo $value['comment']; ?></td>
                             </tr>
                           <?php }
                              } ?>
                           </tbody>
                        </table>
                      </div>
                    </div>
                    <?php
                  }else {
                    ?>
                    <div class="card">
                      <div class="content">
                        <br><br><br><br>
                        <center>
                          <h4>(Empty Report)</h4>
                        </center>
                        <br><br><br><br>
                      </div>
                    </div>
                    <?php
                  } ?>

                </div>
              </div>
                <?php
              }if ($_SESSION['default_report_inventory']==3) {
                if ($_SESSION['default_inventory_date']==1) {
                ?>
                <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                  <thead>
                    <tr>
                      <th class="bg bg-info">Report NO.</th>
                      <th class="bg bg-danger">Lost</th>
                      <th class="bg bg-danger">Damaged</th>
                      <th class="bg bg-warning">Comment</th>
                      <th class="bg bg-success">Date reported</th>
                      <th class="bg bg-success">Reported by</th>
                      <?php if ($_SESSION['account_type']==3) {
                        ?>
                        <th class="bg bg-info">Storage</th>
                        <?php
                      } ?>
                      <th class="bg bg-success">Date verified</th>
                      <th class="bg bg-success">Verified by</th>
                      <th class="bg bg-info">Remarks</th>
                      <?php if ($_SESSION['account_type']==3) {
                        ?>
                        <th class="bg bg-warning">Action</th>
                        <?php
                      } ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if ($_SESSION['account_type']==3) {
                      $fetchReportMasterS = "SELECT *
                                FROM report_date_control a
                                left join reports b on a.rd_control_id = b.rd_control_id
                                where   b.report_type_id = 3 and a.date_range = CURRENT_DATE()
                                order by a.rd_control_id desc";
                       $queryString = mysqli_query($dbcon,$fetchReportMasterS);
                    }else {
                      $fetchReportMasterS = "SELECT *
                                FROM report_date_control a
                                left join reports b on a.rd_control_id = b.rd_control_id
                                where b.storage_id = $storageID and b.report_type_id = 3 and a.date_range = CURRENT_DATE()
                                order by a.rd_control_id desc";
                       $queryString = mysqli_query($dbcon,$fetchReportMasterS);
                    }
                      foreach ($queryString as $key => $invS) {
                    ?>
                    <tr>
                      <td ><?php echo $invS['report_id'] ?></td>
                      <td>
                      <?php $queryRdetails = "SELECT *
                                       FROM utensils a
                                       left join incident_report_details b on a.utensils_id = b.utensils_id
                                       left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                       where b.report_id = '".$invS['report_id']."'
                                       ";
                        $incidentD = mysqli_query($dbcon,$queryRdetails);
                    foreach ($incidentD as $key => $incD) {
                      if ($incD['lost_qty']==0) {
                      }else {
                      ?> <span><?php echo $incD['lost_qty'] ?> - <?php echo $incD['utensils_name'] ?></span> <br> <?php
                    }
                  }?>
                    </td>
                    <td>
                    <?php $queryRdetails = "SELECT *
                                     FROM utensils a
                                     left join incident_report_details b on a.utensils_id = b.utensils_id
                                     left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                     where b.report_id = '".$invS['report_id']."'
                                     ";
                      $incidentD = mysqli_query($dbcon,$queryRdetails);
                  foreach ($incidentD as $key => $incD) {
                    if ($incD['damaged_qty']==0) {
                    }else {
                    ?> <span><?php echo $incD['damaged_qty'] ?> - <?php echo $incD['utensils_name'] ?></span> <br> <?php
                  }
                }?>
                  </td>
                  <td>
                  <?php $queryRdetails = "SELECT *
                                   FROM utensils a
                                   left join incident_report_details b on a.utensils_id = b.utensils_id
                                   left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                   where b.report_id = '".$invS['report_id']."'
                                   ";
                    $incidentD = mysqli_query($dbcon,$queryRdetails);
                foreach ($incidentD as $key => $incD) {
                  ?> <span><?php echo $incD['comment'] ?> </span> <br> <?php
                } ?>
                </td>
                      <td><?php echo $invS['report_date'] ?></td>
                     <?php $fetchUser = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['reported_by']."'");
                     foreach ($fetchUser as $key => $userR) {
                       ?><td><?php echo $userR['lname'] ?></td><?php
                     } ?>
                     <?php if ($_SESSION['account_type']==3) {
                       $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = '".$invS['storage_id']."'");
                       foreach ($fetchStorage as $key => $stor) {
                      ?>
                      <td> <?php echo $stor['initials']; ?> </td>
                      <?php
                     }
                   }?>
                     <?php if ($invS['date_verified']==0) {
                       ?> <td></td> <?php
                     }else {
                       ?>
                       <td><?php echo $invS['date_verified'] ?></td>
                       <?php
                     } ?>
                      <?php
                      if ($invS['verified_by']==0) {
                        ?> <td></td> <?php
                      }else {

                       $fetchUserV = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['verified_by']."'");
                      foreach ($fetchUserV as $key => $userV) {
                        ?><td><?php  echo $userV['lname'] ?></td><?php
                      }
                    } ?>
                    <?php $queryRdetails = "SELECT *
                                      FROM utensils a
                                      left join incident_report_details b on a.utensils_id = b.utensils_id
                                      left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                      where b.report_id = '".$invS['report_id']."'
                                      ";
                       $incidentD = mysqli_query($dbcon,$queryRdetails);
                   foreach ($incidentD as $key => $incD);
                     ?> <span><?php if($incD['lost_qty']==$incD['found']&&$incD['damaged_qty']==$incD['replaced']){
                       ?> <td class="bg bg-success">Complete</td> <?php
                     }else { ?> <td class="bg bg-danger">Incomplete</td> <?php }  ?> </span>

                    <?php if ($_SESSION['account_type']==3) {
                      if ($invS['status']== 0) {
                        ?> <td><a href="?verify=<?php echo $invS['report_id']; ?>"onclick="return confirm('Confirm verify!');">Verify</a></td> <?php
                      }else {
                        ?> <td><a href="?update_report=<?php echo $invS['report_id']; ?>"class="text-success">Update</a></td> <?php
                      }
                    } ?>
                    </tr>
                  <?php

                }?>
                  </tbody>
                </table>
                <?php
                 }
                 if ($_SESSION['default_inventory_date']==2) {
                   ?>
                   <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                     <thead>
                       <tr>
                         <th class="bg bg-info">Report NO.</th>
                         <th class="bg bg-danger">Lost</th>
                         <th class="bg bg-danger">Damaged</th>
                         <th class="bg bg-warning">Comment</th>
                         <th class="bg bg-success">Date reported</th>
                         <th class="bg bg-success">Reported by</th>
                         <?php if ($_SESSION['account_type']==3) {
                           ?>
                           <th class="bg bg-info">Storage</th>
                           <?php
                         } ?>
                         <th class="bg bg-success">Date verified</th>
                         <th class="bg bg-success">Verified by</th>
                         <th class="bg bg-info">Remarks</th>
                         <?php if ($_SESSION['account_type']==3) {
                           ?>
                           <th class="bg bg-warning">Action</th>
                           <?php
                         } ?>
                       </tr>
                     </thead>
                     <tbody>
                       <?php
                       if ($_SESSION['account_type']==3) {
                         $fetchReportMasterS = "SELECT *
                                   FROM report_date_control a
                                   left join reports b on a.rd_control_id = b.rd_control_id
                                   where   b.report_type_id = 3 order by b.report_id desc";
                          $queryString = mysqli_query($dbcon,$fetchReportMasterS);
                       }else {
                         $fetchReportMasterS = "SELECT *
                                   FROM report_date_control a
                                   left join reports b on a.rd_control_id = b.rd_control_id
                                   where b.storage_id = $storageID and b.report_type_id = 3 order by b.report_id desc";
                          $queryString = mysqli_query($dbcon,$fetchReportMasterS);
                       }
                         foreach ($queryString as $key => $invS) {
                       ?>
                       <tr>
                         <td ><?php echo $invS['report_id'] ?></td>
                         <td>
                         <?php $queryRdetails = "SELECT *
                                          FROM utensils a
                                          left join incident_report_details b on a.utensils_id = b.utensils_id
                                          left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                          where b.report_id = '".$invS['report_id']."'
                                          ";
                           $incidentD = mysqli_query($dbcon,$queryRdetails);
                       foreach ($incidentD as $key => $incD) {
                         if ($incD['lost_qty']==0) {
                         }else {
                         ?> <span><?php echo $incD['lost_qty'] ?> - <?php echo $incD['utensils_name'] ?></span> <br> <?php
                       }
                     }?>
                       </td>
                       <td>
                       <?php $queryRdetails = "SELECT *
                                        FROM utensils a
                                        left join incident_report_details b on a.utensils_id = b.utensils_id
                                        left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                        where b.report_id = '".$invS['report_id']."'
                                        ";
                         $incidentD = mysqli_query($dbcon,$queryRdetails);
                     foreach ($incidentD as $key => $incD) {
                       if ($incD['damaged_qty']==0) {
                       }else {
                       ?> <span><?php echo $incD['damaged_qty'] ?> - <?php echo $incD['utensils_name'] ?></span> <br> <?php
                     }
                   }?>
                     </td>
                     <td>
                     <?php $queryRdetails = "SELECT *
                                      FROM utensils a
                                      left join incident_report_details b on a.utensils_id = b.utensils_id
                                      left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                      where b.report_id = '".$invS['report_id']."'
                                      ";
                       $incidentD = mysqli_query($dbcon,$queryRdetails);
                   foreach ($incidentD as $key => $incD) {
                     ?> <span><?php echo $incD['comment'] ?> </span> <br> <?php
                   } ?>
                   </td>
                         <td><?php echo $invS['report_date'] ?></td>
                        <?php $fetchUser = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['reported_by']."'");
                        foreach ($fetchUser as $key => $userR) {
                          ?><td><?php echo $userR['lname'] ?></td><?php
                        } ?>
                        <?php if ($_SESSION['account_type']==3) {
                          $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = '".$invS['storage_id']."'");
                          foreach ($fetchStorage as $key => $stor) {
                         ?>
                         <td> <?php echo $stor['initials']; ?> </td>
                         <?php
                        }
                      }?>
                        <?php if ($invS['date_verified']==0) {
                          ?> <td></td> <?php
                        }else {
                          ?>
                          <td><?php echo $invS['date_verified'] ?></td>
                          <?php
                        } ?>
                         <?php
                         if ($invS['verified_by']==0) {
                           ?> <td></td> <?php
                         }else {

                          $fetchUserV = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['verified_by']."'");
                         foreach ($fetchUserV as $key => $userV) {
                           ?><td><?php  echo $userV['lname'] ?></td><?php
                         }
                       } ?>
                         <?php $queryRdetails = "SELECT *
                                           FROM utensils a
                                           left join incident_report_details b on a.utensils_id = b.utensils_id
                                           left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                           where b.report_id = '".$invS['report_id']."'
                                           ";
                            $incidentD = mysqli_query($dbcon,$queryRdetails);
                        foreach ($incidentD as $key => $incD);
                          ?> <span><?php if($incD['lost_qty']==$incD['found']&&$incD['damaged_qty']==$incD['replaced']){
                            ?> <td class="bg bg-success">Complete</td> <?php
                          }else { ?> <td class="bg bg-danger">Incomplete</td> <?php }  ?> </span>

                         <?php if ($_SESSION['account_type']==3) {
                           if ($invS['status']== 0) {
                             ?> <td><a href="?verify=<?php echo $invS['report_id']; ?>"onclick="return confirm('Confirm verify!');">Verify</a></td> <?php
                           }else {
                             ?> <td><a href="?update_report=<?php echo $invS['report_id']; ?>"class="text-success">Update</a></td> <?php
                           }
                         } ?>
                       </tr>
                     <?php

                   }?>
                     </tbody>
                   </table>
                   <?php
                 }
                 if ($_SESSION['default_inventory_date']==3) {
                   ?>
                   <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                     <thead>
                       <tr>
                         <th class="bg bg-info">Report NO.</th>
                         <th class="bg bg-danger">Lost</th>
                         <th class="bg bg-danger">Damaged</th>
                         <th class="bg bg-warning">Comment</th>
                         <th class="bg bg-success">Date reported</th>
                         <th class="bg bg-success">Reported by</th>
                         <?php if ($_SESSION['account_type']==3) {
                           ?>
                           <th class="bg bg-info">Storage</th>
                           <?php
                         } ?>
                         <th class="bg bg-success">Date verified</th>
                         <th class="bg bg-success">Verified by</th>
                         <th class="bg bg-info">Remarks</th>
                         <?php if ($_SESSION['account_type']==3) {
                           ?>
                           <th class="bg bg-warning">Action</th>
                           <?php
                         } ?>
                       </tr>
                     </thead>
                     <tbody>
                       <?php
                       $dat1 = $_SESSION['in_report_date1'];
                       $dat2 = $_SESSION['in_report_date2'];
                       if ($_SESSION['account_type']==3) {
                         $fetchReportMasterS = "SELECT *
                                   FROM report_date_control a
                                   left join reports b on a.rd_control_id = b.rd_control_id
                                   where   b.report_type_id = 3 and a.date_range  >= '$dat1' and a.date_range <= '$dat2'
                                   order by a.rd_control_id desc";
                          $queryString = mysqli_query($dbcon,$fetchReportMasterS);
                       }else {
                         $fetchReportMasterS = "SELECT *
                                   FROM report_date_control a
                                   left join reports b on a.rd_control_id = b.rd_control_id
                                   where b.storage_id = $storageID and b.report_type_id = 3 and a.date_range  >= '$dat1' and a.date_range <= '$dat2'
                                   order by a.rd_control_id desc";
                          $queryString = mysqli_query($dbcon,$fetchReportMasterS);
                       }
                         foreach ($queryString as $key => $invS) {
                       ?>
                       <tr>
                         <td ><?php echo $invS['report_id'] ?></td>
                         <td>
                         <?php $queryRdetails = "SELECT *
                                          FROM utensils a
                                          left join incident_report_details b on a.utensils_id = b.utensils_id
                                          left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                          where b.report_id = '".$invS['report_id']."'
                                          ";
                           $incidentD = mysqli_query($dbcon,$queryRdetails);
                       foreach ($incidentD as $key => $incD) {
                         if ($incD['lost_qty']==0) {
                         }else {
                         ?> <span><?php echo $incD['lost_qty'] ?> - <?php echo $incD['utensils_name'] ?></span> <br> <?php
                       }
                     }?>
                       </td>
                       <td>
                       <?php $queryRdetails = "SELECT *
                                        FROM utensils a
                                        left join incident_report_details b on a.utensils_id = b.utensils_id
                                        left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                        where b.report_id = '".$invS['report_id']."'
                                        ";
                         $incidentD = mysqli_query($dbcon,$queryRdetails);
                     foreach ($incidentD as $key => $incD) {
                       if ($incD['damaged_qty']==0) {
                       }else {
                       ?> <span><?php echo $incD['damaged_qty'] ?> - <?php echo $incD['utensils_name'] ?></span> <br> <?php
                     }
                   }?>
                     </td>
                     <td>
                     <?php $queryRdetails = "SELECT *
                                      FROM utensils a
                                      left join incident_report_details b on a.utensils_id = b.utensils_id
                                      left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                      where b.report_id = '".$invS['report_id']."'
                                      ";
                       $incidentD = mysqli_query($dbcon,$queryRdetails);
                   foreach ($incidentD as $key => $incD) {
                     ?> <span><?php echo $incD['comment'] ?> </span> <br> <?php
                   } ?>
                   </td>
                         <td><?php echo $invS['report_date'] ?></td>
                        <?php $fetchUser = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['reported_by']."'");
                        foreach ($fetchUser as $key => $userR) {
                          ?><td><?php echo $userR['lname'] ?></td><?php
                        } ?>
                        <?php if ($_SESSION['account_type']==3) {
                          $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = '".$invS['storage_id']."'");
                          foreach ($fetchStorage as $key => $stor) {
                         ?>
                         <td> <?php echo $stor['initials']; ?> </td>
                         <?php
                        }
                      }?>
                        <?php if ($invS['date_verified']==0) {
                          ?> <td></td> <?php
                        }else {
                          ?>
                          <td><?php echo $invS['date_verified'] ?></td>
                          <?php
                        } ?>
                         <?php
                         if ($invS['verified_by']==0) {
                           ?> <td></td> <?php
                         }else {

                          $fetchUserV = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['verified_by']."'");
                         foreach ($fetchUserV as $key => $userV) {
                           ?><td><?php  echo $userV['lname'] ?></td><?php
                         }
                       } ?>
                       <?php $queryRdetails = "SELECT *
                                         FROM utensils a
                                         left join incident_report_details b on a.utensils_id = b.utensils_id
                                         left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                         where b.report_id = '".$invS['report_id']."'
                                         ";
                          $incidentD = mysqli_query($dbcon,$queryRdetails);
                      foreach ($incidentD as $key => $incD);
                        ?> <span><?php if($incD['lost_qty']==$incD['found']&&$incD['damaged_qty']==$incD['replaced']){
                          ?> <td class="bg bg-success">Complete</td> <?php
                        }else { ?> <td class="bg bg-danger">Incomplete</td> <?php }  ?> </span>

                       <?php if ($_SESSION['account_type']==3) {
                         if ($invS['status']== 0) {
                           ?> <td><a href="?verify=<?php echo $invS['report_id']; ?>"onclick="return confirm('Confirm verify!');">Verify</a></td> <?php
                         }else {
                           ?> <td><a href="?update_report=<?php echo $invS['report_id']; ?>"class="text-success">Update</a></td> <?php
                         }
                       } ?>
                       </tr>
                     <?php

                   }?>
                     </tbody>
                   </table>
                   <?php
                 }
              }
              ?>
          </div>

         </div>
     </div>
</div>

<!-- Mini Modal -->
<div class="modal <?php if ($_SESSION['default_inventory_date']==3&&$_SESSION['default_control_inventory']== 0) {
  echo "show";
 }if($_SESSION['default_inventory_date']==3&&$_SESSION['default_control_inventory']== 1) {
  echo "fade";
 } ?>  modal-primary" id="myModal1" data-backdrop="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
              <span >Select custom dates</span>
              <a href="staff_inventory_report.php?close_modal_inventory
    " class="close" data-dismiss="modal">&times;</a>
            </div>
            <form class="" action="staff_inventory_report.php" method="post">
            <div class="modal-body ">
              <div class="content">
                  <div class="pull-center">
                  <label for="">From :</label>
                  <input type="date"class="form-control" name="in_report_date1" value=""required>
                  <br>
                  <label for="">To :</label>
                  <input type="date"class="form-control" name="in_report_date2" value=""required>
                </div>
            </div>
       </div>
            <div class="modal-footer">
                <button type="submit" name="confirm_report_date"class="btn btn-sm btn-info btn-fill">Confirm</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!--  End Modal -->

<!-- Mini Modal -->
<div class="modal <?php if ($_SESSION['incident_modal_control']==1) {
  echo "show";
 }if($_SESSION['incident_modal_control']==0) {
  echo "fade";
 } ?>  modal-primary" id="myModal2" data-backdrop="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
              <span >Create report on this item</span>
              <a href="staff_inventory_report.php?close_modal_incident
              " class="close" data-dismiss="modal">&times;</a>
            </div>
            <form class="" action="staff_inventory_report.php" method="post">
            <div class="modal-body ">
              <div class="content">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>CUR-QTY</th>
                        <th>ITEM</th>
                        <th>Category</th>
                        <th>MODEL</th>
                        <th>SERIAL NO.</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $query = "SELECT *
                                      FROM utensils a
                                      LEFT join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                                      left join storage_stocks c on a.utensils_id = c.utensils_id
                                      where a.utensils_id = '".$_SESSION['report_id']."' and c.storage_id = $storageID";
                            $result = mysqli_query($dbcon,$query);
                        foreach ($result as $key => $value) {
                    ?>
                      <tr>
                        <td><?php echo $value['utensils_id']; ?></td>
                        <td><?php echo $value['storage_qty']; ?></td>
                        <td><?php echo $value['utensils_name']; ?></td>
                        <td><?php echo $value['category']; ?></td>
                        <td><?php echo $value['model']; ?></td>
                        <td><?php echo $value['serial_no']; ?></td>
                      </tr>
                    <?php } ?>
                    </tbody>
                  </table>
                  <hr>
                  <div class="row">
                    <div class="col-md-6">
                      <label for="">Report lost</label>
                      <input type="number"class="form-control" name="lost_qty" value=""placeholder="Enter qty">
                    </div>
                    <div class="col-md-6">
                      <label for="">Report damaged</label>
                      <input type="number" class="form-control"name="damaged_qty" value=""placeholder="Enter qty">
                    </div>
                    <div class="col-md-12">
                      <br>
                      <label for="">Comment</label>
                      <textarea name="comment"class="form-control" rows="8" cols="80">
                      </textarea required>
                      <input type="hidden" name="utensils_id" value="<?php echo $_SESSION['report_id']; ?>">
                    </div>
                  </div>
            </div>
       </div>
            <div class="modal-footer">
              <div class="row">
                <div class="col-md-6">
                  <?php include('errors.php'); ?>
                </div>
                <div class="col-md-6">
                  <button type="submit" name="add_report_item"class="btn btn-sm btn-info btn-fill">Add report</button>
                </div>
              </div>
            </div>
            </form>
        </div>
    </div>
</div>
<!--  End Modal -->

<!-- Mini Modal -->
<div class="modal <?php if ($_SESSION['report_modal_control']==1) {
  echo "show";
 }if($_SESSION['report_modal_control']==0) {
  echo "fade";
 } ?>  modal-primary" id="myModal3" data-backdrop="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
              <span >Update report</span>
              <a href="staff_inventory_report.php?close_modal_report
              " class="close" data-dismiss="modal">&times;</a>
            </div>
            <form class="" action="staff_inventory_report.php" method="post">
            <div class="modal-body ">
              <div class="content">
                <h5>Report No. : <?php echo $_SESSION['report_update_ID']; ?></h5>
                  <table class="table">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>ITEMS</th>
                        <th>Lost</th>
                        <th>damaged</th>
                        <th class="bg bg-info text-center">found</th>
                        <th class="bg bg-info text-center">replaced</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $query = "SELECT *
                                      FROM utensils a
                                      LEFT join incident_report_details b on a.utensils_id = b.utensils_id
                                      left join reports c on b.report_id = c.report_id
                                      where c.report_id = '".$_SESSION['report_update_ID']."' ";
                            $result = mysqli_query($dbcon,$query);
                        foreach ($result as $key => $value) {
                    ?>
                      <tr>
                        <td><?php echo $value['utensils_id']; ?></td>
                        <td><?php echo $value['utensils_name']; ?></td>
                        <td><?php echo $value['lost_qty']; ?></td>
                        <td><?php echo $value['damaged_qty']; ?></td>
                        <td class="bg bg-warning text-center">
                         <?php if ($value['lost_qty']==0) {
                           echo '';
                         }else {
                           ?>
                             <a href="?add_found&uID=<?php echo $value['utensils_id']; ?>
                             &sID=<?php echo $value['storage_id']; ?>"><i class="fa fa-plus"></i></a>
                             <?php echo $value['found']; ?>
                             <a href="?minus_found&uID=<?php echo $value['utensils_id']; ?>
                             &sID=<?php echo $value['storage_id']; ?>"><i class="fa fa-minus"></i></a>
                           <?php
                         } ?>
                         </td>
                          <td class="bg bg-warning text-center">
                         <?php if ($value['damaged_qty']==0) {
                           echo '';
                         }else {
                           ?>

                            <a href="?add_replaced&uID=<?php echo $value['utensils_id']; ?>
                            &sID=<?php echo $value['storage_id']; ?>"><i class="fa fa-plus"></i></a>
                             <?php echo $value['replaced']; ?>
                              <a href="?minus_replaced&uID=<?php echo $value['utensils_id']; ?>
                            &sID=<?php echo $value['storage_id']; ?>"><i class="fa fa-minus"></i></a>
                          <?php
                        } ?>
                        </td>
                      </tr>
                    <?php } ?>
                    </tbody>
                  </table>
                  <hr>
                  <div class="row">
                    <div class="col-md-12">
                      <br>
                      <label for="">Comment</label>
                      <textarea name="comment"class="form-control" rows="8" cols="80">
                      </textarea required>
                    </div>
                  </div>
            </div>
       </div>
            <div class="modal-footer">
              <div class="row">
                <div class="col-md-6">
                  <?php include('errors.php'); ?>
                </div>
                <div class="col-md-6">
                  <button type="submit" name="update_report_confirm"class="btn btn-sm btn-info btn-fill">Save changes</button>
                </div>
              </div>
            </div>
            </form>
        </div>
    </div>
</div>
<!--  End Modal -->


<script src='dist/jspdf.min.js'></script>
<script src="html2pdf.bundle.min.js"></script>

<?php include('dataTables2.php'); ?>
<script type="text/javascript">
$('#report_table').DataTable( {
 "pageLength": 50,
 "scrollX": true
 } );
 $('#ALL_UTENSILS').DataTable( {
  "pageLength": 50
  } );

 var doc = new jsPDF();
 var specialElementHandlers = {
     '#editor': function (element, renderer) {
         return true;
     }
 };

 $('#cmd').click(function () {
     doc.fromHTML($('#smdiv').html(), 15, 15, {
         'width': 170,
             'elementHandlers': specialElementHandlers
     });
     doc.save('sample-file.pdf');
 });
</script>
<?php include('footer.php') ?>
