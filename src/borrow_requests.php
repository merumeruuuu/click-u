<?php include('header.php');
?>
<br><br>
<?php
if (isset($_GET['forward_to_dean'])) {
  $reqID = $_GET['forward_to_dean'];
  $checkStats = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $reqID");
  foreach ($checkStats as $key => $value);
  if ($value['status']==3) {
    echo "<script>alert('Failed! request has been cancelled!');</script>";
  }else {
    $checkStats2 = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $reqID");
    foreach ($checkStats2 as $key => $value2) {
      if ($value2['status']!=8) {
        $updateRequest = mysqli_query($dbcon,"UPDATE borrower_slip set status = 8 where borrower_slip_id = $reqID");
        //insert admin notification
        $insertNotifControl = mysqli_query($dbcon,"INSERT INTO notification_control (trans_id,notif_type_id)
        values('$reqID','7')");
        $fetchControl = mysqli_query($dbcon,"SELECT notif_control_id from notification_control order by notif_control_id desc limit 1");
        foreach ($fetchControl as $key => $control) {
          $fetchAdmin = mysqli_query($dbcon,"SELECT * from user_settings where account_type_id <=2 ");
          foreach ($fetchAdmin as $key => $user) {

        $insertNotif = mysqli_query($dbcon,"INSERT INTO notification (notif_control_id,user_id,user_notif_type,notif_date)
        values('".$control['notif_control_id']."','".$user['user_id']."','3',NOW())");
           }
        }
      }
    }
}
} ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <?php
              $_SESSION['user']['storage_id'];
              $storageID = $_SESSION['user']['storage_id'];

              $select = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $storageID");
              $show = mysqli_fetch_array($select);
               ?>
                         <div class="card">
                    <div class="content">
                      <?php
                             $query = "SELECT
                                             a.borrower_slip_id as requestID,a.group_id as grpID,a.date_requested,a.added_by,a.date_requested,a.storage_id,a.status,
                                             a.purpose,a.date_use,a.time_use,a.verified_by,a.date_verified,
                                             b.group_id,b.group_name,b.instructor,b.group_leader_id,b.instructor,
                                             c.group_id,c.user_id,c.added_by,
                                             d.user_id,d.school_id,d.fname,d.lname,
                                             e.user_id,e.account_type_id,
                                             f.storage_id,f.storage_name,
                                             g.borrower_slip_id,count(g.bsd_id)


                                             from borrower_slip a
                                             left join group_table b on a.group_id = b.group_id
                                             left join group_members c on b.group_id = c.group_id
                                             left join users d on c.user_id = d.user_id
                                             left join user_settings e on d.user_id = e.user_id
                                             left join storage f on a.storage_id = f.storage_id
                                             left join borrower_slip_details g on a.borrower_slip_id = g.borrower_slip_id


                                             where  a.status != 2 and a.status != 3 and a.status != 4 and a.status != 5
                                             and a.status != 6 and a.status != 7 and a.status != 8  and a.status != 9 and a.storage_id = $storageID and  g.bsd_id >0
                                             group by b.group_id";
                              $result = mysqli_query($dbcon,$query);
                             ?>

                               <table class="table "id="requestTable">
                                   <thead>
                                     <tr>
                                     <th class="card">
                                        <div class="title ">
                                        <h5 class="info"><?php echo $show['storage_name'] ?> (<?php echo $show['initials'] ?>)</h5>
                                    </tr>
                                   </thead>
                                 <tbody>
                                   <?php

                                    while ($rows = mysqli_fetch_array($result)) {

                                     $group = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$rows['group_leader_id']);
                                     $groupLeader = mysqli_fetch_array($group);
                                  ?>
                                   <tr>
                                     <td>
                                       <div class="card">
                                         <div class="row">
                                          <div class="col-md-12">
                                             <div class="header">
                                                 <h5 class=" ">Request # <strong style="color:#07bfea"> <?php echo $rows['requestID']; ?></strong></h5>
                                             </div>
                                             <div class="content">
                                               <div class="col-md-4">
                                              <div class="">
                                                <label for="">Borrower/s</label>
                                                <?php
                                                $grpID = $rows['grpID'];
                                                $borrowers = "SELECT
                                                          a.group_id,a.user_id,
                                                          b.user_id,b.school_id,b.lname,b.fname

                                                          from group_members a
                                                          left join users b on a.user_id = b.user_id

                                                          where a.group_id = $grpID";
                                                 $check = mysqli_query($dbcon,$borrowers);
                                                 while ($borrower = mysqli_fetch_array($check)) { ?>
                                                   <p>
                                                   <?php echo $borrower['school_id']; ?> -
                                                   <?php echo $borrower['lname'];  ?>,
                                                   <?php echo $borrower['fname'];  ?>
                                                   </p>
                                                 <?php } ?>

                                              </div>
                                               </div>
                                               <div class="col-md-5">
                                                 <label for="">Date of use :</label>
                                                 <span>
                                                   <?php echo date('M d, Y',strtotime($rows['date_use']));?> | <?php echo date('h:i:s A',strtotime($rows['date_use']));?>
                                               </span>
                                               <br>
                                                 <label for="">Purpose :</label>
                                                 <span>
                                                   <?php echo $rows['purpose']; ?>
                                               </span>
                                               <br>
                                                   <label for="">Group Name :</label>
                                                   <span>
                                                   <?php if (!empty($rows['group_name'])){ ?>

                                                     <?php echo $rows['group_name']; ?>

                                                   <?php }else {
                                                     echo "N/A";
                                                   }?>
                                                 </span>
                                                  <br>
                                                 <label for="">Requested From :</label>
                                                 <span>
                                                 <?php echo $rows['storage_name']; ?>
                                               </span>
                                               <br>
                                               <label for="">Date Requested :</label>
                                               <span>
                                               <?php echo  date('M d, Y',strtotime($rows['date_requested'])); ?>
                                             </span>
                                               <br>

                                                 <label for="">Instructor :</label>
                                                 <span>
                                                   <?php if ($rows['account_type_id']=="7") {
                                                     ?>
                                                    <?php echo $rows['instructor']; ?>
                                                     <?php
                                                   }else {
                                                     echo "N/A";
                                                   }
                                                     ?>
                                                 </span>
                                             </div>
                                             <br>

                                                       <div class="col-md-12">
                                                         <label for="">Requested Items</label>
                                                         <?php
                                                         $reqID = $rows['requestID'];
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
                                                          <table class="table"style='width:100%;' border="0" alt="Null">
                                                            <thead>
                                                              <col span="4" style="background-color:auto;">
                                                              <col style="background-color:#e9e7e0;">
                                                              <tr>
                                                                <th>Item ID</th>
                                                                <th>Item Name</th>
                                                                <th>Item Category</th>
                                                                <th>Requested Quantity</th>
                                                              </tr>
                                                            </thead>
                                                            <tbody>
                                                              <?php while ($items = mysqli_fetch_array($itemQuery)) {
                                                                ?>
                                                              <tr>
                                                                <td><?php echo  $items['itemID']?></td>
                                                                <td><?php echo  $items['utensils_name']?></td>
                                                                <td><?php echo  $items['category']?></td>
                                                                <td><?php echo  $items['qty']?></td>


                                                             <?php
                                                            }?>
                                                              </tr>

                                                            </tbody>
                                                          </table>
                                                              </div>

                                                              <div class="col-md-12">
                                                                <div class="footer">
                                                                    <hr>
                                                                    <?php if (empty($rows['verified_by'])) {
                                                                      ?> <div class="col-md-9">
                                                                       <div class="stats">
                                                                             <i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($rows['date_requested']));
                                                                                  echo " | ".date('H:i:s A',strtotime($rows['date_requested'])); ?></span>
                                                                        </div>
                                                                        </div>
                                                                        <?php
                                                                    }else {
                                                                      ?>
                                                                      <div class="col-md-6">
                                                                      <div class="stats">
                                                                            <i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($rows['date_requested']));
                                                                                 echo " | ".date('H:i:s A',strtotime($rows['date_requested'])); ?></span>
                                                                       </div>
                                                                       </div>
                                                                       <div class="col-md-3">
                                                                         <div class="stats">
                                                                           <?php $fetchUser = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$rows['verified_by']);
                                                                           foreach ($fetchUser as $key => $value) {
                                                                             ?>
                                                                             Verified by: <span><?php  echo $value['lname'];?></span>
                                                                             <?php
                                                                           } ?>
                                                                          </div>
                                                                       </div>
                                                                      <?php
                                                                    } ?>

                                                                      <div class="col-md-3">
                                                                        <?php
                                                                        if (empty($rows['verified_by'])) {
                                                                          ?>  <span>
                                                                              <a href="?forward_to_dean=<?php echo $rows['requestID']; ?>"onclick="return confirm('Are you sure you want to forward this request to the dean?');">
                                                                                Send to dean
                                                                              </a>
                                                                            </span>|<?php
                                                                        } ?>
                                                                       <span>
                                                                      <!-- <a href="server.php?action=approveRequest&id=<?php echo $rows['requestID']; ?>" class="btn btn-success btn-fill btn_requestDetails btn-sm" onclick="return confirm('Confirm approve!')">Approve<i class="fa fa-check"></i></a> -->
                                                                       <a href="#" data-toggle="modal"data-id="<?php echo $rows['requestID']; ?>" class="click_pin">
                                                                         Release
                                                                       </a>
                                                                       </span>
                                                                      </div>
                                                                      </div>
                                                                </div>
                                                              </div>

                                                       <?php

                                                   }
                                                       ?>
                                      </div>
                                    </div>
                                    </div>
                                     </div>
                                     </div>

                                   </td>
                                   </tr>

                                 </tbody>
                               </table>
                             </div>
                                </div>
</div>
  </div>
    </div>
        </div>

<?php
if (isset($_POST['approve_from_modal'])) {
  $request = mysqli_real_escape_string($dbcon,$_POST['reqIDs']);
  $pin = mysqli_real_escape_string($dbcon,$_POST['pin']);

  $query_pin = mysqli_query($dbcon,"SELECT * FROM pin where borrower_slip_id = $request");
  $check_pin = mysqli_fetch_array($query_pin);

  if ($check_pin['pin_id']==$pin) {
    $query = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $request");
    $checkx = mysqli_fetch_array($query);
    if ($checkx['status']==2) {
      echo "<script>alert('Failed! The request has been approved by the other staff!');window.location.href='borrow_requests.php';</script>";
    }else {
    if ($checkx['status']<2) {

      $query1 = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $request");
      while ($check = mysqli_fetch_array($query1)) {
        $utensilID = $check['utensils_id'];
        $storageID = $check['storage_id'];
        $requestQty = $check['qty'];
        $reservedtQty = $check['reserved_qty'];

        $query2 = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = $utensilID and storage_id = $storageID");
        while ($rows = mysqli_fetch_array($query2)) {
          $storageQty = $rows['storage_qty'];

          $deduct = $reservedtQty - $requestQty;
          $storageOnuse = $rows['on_use'] + $requestQty;
          $storageRsrv = $rows['reserved_qty'] - $requestQty;
          $staff = $_SESSION['user']['user_id'];
          // $updateStocks = mysqli_query($dbcon,"UPDATE storage_stocks SET storage_qty = $deductStorageQty where utensils_id = $utensilID and storage_id = $storageID");
          $updateStatus = mysqli_query($dbcon,"UPDATE borrower_slip SET date_approved = NOW(),aprvd_n_rlsd_by = $staff, status = 2 where borrower_slip_id = $request");
          $updateItemOnUse = mysqli_query($dbcon,"UPDATE borrower_slip_details SET on_use = $requestQty,reserved_qty = $deduct where borrower_slip_id = $request and utensils_id = $utensilID and storage_id = $storageID");
          $updateStorage = mysqli_query($dbcon,"UPDATE storage_stocks set reserved_qty = $storageRsrv,on_use = $storageOnuse where utensils_id = $utensilID and storage_id = $storageID");
        echo "<script>alert('Released successfully !');window.location.href='borrow_requests.php';</script>";
         }
         //update notification
         $checkMembers = mysqli_query($dbcon,"SELECT * FROM group_members where group_id = ".$checkx['group_id']);
         foreach ($checkMembers as $key => $members) {
         $checkControl = mysqli_query($dbcon,"SELECT * FROM notification_control where trans_id = $request and notif_type_id = 1 and storage_id = $storageID");
         foreach ($checkControl as $key => $control);
         $updateUserNotif = mysqli_query($dbcon,"UPDATE notification set notif_date = NOW(),seen_user = 1,notif_count = 0 where notif_control_id = '".$control['notif_control_id']."' and user_id = '".$members['user_id']."'");
        }

      }
      //insertion of inventory master
      $checkInventoryControl = mysqli_query($dbcon,"SELECT * FROM inventory where date_added = CURRENT_DATE() order by inventory_control_id desc limit 1")or die(mysqli_error($dbcon));
      foreach ($checkInventoryControl as $key => $inventoryControl);
      if (mysqli_num_rows($checkInventoryControl)<=0) {
       $insertInventory = mysqli_query($dbcon, "INSERT INTO inventory (date_added)
       VALUES(NOW())") or die(mysqli_error($dbcon));
       $selectFromInventory = mysqli_query($dbcon,"SELECT * FROM inventory order by inventory_control_id desc limit 1");
       foreach ($selectFromInventory as $key => $inventory);
       $utensils = mysqli_query($dbcon,"SELECT * from utensils where  stock_on_hand >0");
        foreach ($utensils as $key => $utensilz) {
        $insertToInventoryDaily = mysqli_query($dbcon,"INSERT INTO inventory_all_record (inventory_control_id,utensils_id,original_stock,remain_stock)
        VALUES ('".$inventory['inventory_control_id']."','".$utensilz['utensils_id']."','".$utensilz['original_stock']."','".$utensilz['stock_on_hand']."' )");
      }

        //inser history
        $historyType = 1;
        $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
        values (NOW(),'$staff','$request','$storageID','$historyType')");

     }
   }else {
         echo "<script>alert('Failed! The request was cancelled by the user!');window.location.href='borrow_requests.php';</script>";
       }
     }

  }else {
    echo "<script>alert('Wrong PIN !');window.location.href='borrow_requests.php';</script>";
  }
}

 ?>

        <!-- Mini Modal -->
        <div class="modal fade modal-mini modal-primary" id="enterPin" data-backdrop="false">
            <div class="modal-dialog">
                <form class="" action="borrow_requests.php" method="post">
                <div class="modal-content">
                    <div class="modal-header justify-content-center">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body text-center">

                        <div class="row">
                          <div class="col-md-12">
                             <?php include('errors.php'); ?>
                          </div>
                            <div class="col-md-5">
                              <div class="author">
                                  <a >
                                      <img class="avatar " src="img/logo4.png" style="width: 150px;">
                                  </a>
                              </div>

                            </div>
                            <div class="col-md-6">
                              <label>Request No : </label>
                               <input type="text"class="form-control"id="reqIDs" name="" disabled  style="text-align:center;"/>
                               <input type="hidden" name="reqIDs"id="reqIDsx"  value="">
                               <br>
                            <label>Enter PIN : </label>
                               <input class="form-control"type="number" name="pin"required style="text-align:center;">
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                      <input type="submit" name="approve_from_modal"id="submit_btn" class="btn btn-success btn-fill btn-sm" value="Submit">
                    </div>
                </div>
                </form>
            </div>
        </div>
        <!--  End Modal -->

  <?php include('dataTables2.php') ?>
  <script type="text/javascript">
    $('#requestTable').DataTable();

    $(".click_pin").click(function () {
        var ids = $(this).attr('data-id');
        $("#reqIDs").val( ids );
        $("#reqIDsx").val( ids );
        $('#enterPin').modal('show');

    });
  //   $("#submit_btn").on("click", function(e) {
  //     e.preventDefault();
  //
  //     // the rest of your code ...
  // });
  </script>
<?php include('footer.php') ?>
