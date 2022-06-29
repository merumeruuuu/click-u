<?php include('header.php');
?>
<br><br>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <?php
              $_SESSION['user']['storage_id'];
              $storageID = $_SESSION['user']['storage_id'];

               ?>
                         <div class="card">
                    <div class="content">
                      <?php
                             $query = "SELECT
                                             a.borrower_slip_id as requestID,a.group_id as grpID,a.date_requested,a.added_by,a.date_approved,a.purpose,
                                             a.date_requested,a.aprvd_n_rlsd_by,a.storage_id,a.status,a.date_use,a.time_use,
                                             b.group_id,b.group_name,b.instructor,b.group_leader_id,
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


                                             where a.storage_id = $storageID and a.status = 6 and g.bsd_id > 0
                                             group by b.group_id";
                              $result = mysqli_query($dbcon,$query);
                             ?>

                               <table class="table DataTable"id="requestTable">
                                   <thead>
                                     <tr>
                                     <th class="card">
                                        <div class="title ">
                                        <h5 class="info">Items with discrepancies</h5>
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
                                               <div class="col-md-5">
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
                                                         <label for="">Reported Items</label>
                                                         <?php
                                                         $reqID = $rows['requestID'];
                                                             $queryString = "SELECT
                                                               a.borrower_slip_id,a.utensils_id,a.lost_qty,a.damaged_qty,
                                                               b.utensils_id,b.utensils_name,b.utensils_cat_id,
                                                               c.utensils_cat_id,c.category

                                                               from breakages_and_damages a
                                                               left join utensils b on a.utensils_id = b.utensils_id
                                                               left join utensils_category c on b.utensils_cat_id = c.utensils_cat_id
                                                               where a.borrower_slip_id = $reqID";
                                                               $itemQuery = mysqli_query($dbcon,$queryString);

                                                          ?>
                                                          <table class="table">
                                                            <thead>
                                                              <col span="3" style="background-color:auto;">
                                                              <col span="2"style="background-color:#e9e7e0;">
                                                              <tr>
                                                                <th>Item ID</th>
                                                                <th>Item Name</th>
                                                                <th>Item Category</th>
                                                               <th>Lost QTY</th>
                                                               <th>Damaged QTY</th>
                                                              </tr>
                                                            </thead>
                                                            <tbody>
                                                              <?php while ($items = mysqli_fetch_array($itemQuery)) {

                                                                ?>
                                                              <tr>
                                                                <td><?php echo  $items['utensils_id']?></td>
                                                                <td><?php echo  $items['utensils_name']?></td>
                                                                <td><?php echo  $items['category']?></td>
                                                                <td><?php echo  $items['lost_qty']?></td>
                                                                <td><?php echo  $items['damaged_qty']?></td>

                                                             <?php
                                                            }?>
                                                              </tr>

                                                            </tbody>
                                                          </table>
                                                              </div>

                                                              <div class="col-md-12">
                                                                <div class="footer">
                                                                    <hr>
                                                                    <div class="col-md-5">
                                                                   <div class="stats">
                                       															<?php $date_reported = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = '".$rows['requestID']."'");
                                       															      $report_row = mysqli_fetch_assoc($date_reported);
                                       																		 ?>
                                       																		 <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($report_row['date_reported']));
                                                                            echo " | ".date('H:i:s A',strtotime($report_row['date_reported'])); ?></span>
                                                                     </div>
                                       														</div>
                                       														<div class="col-md-5">
                                       															<div class="category">
                                       																<?php  $reported_by = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$report_row['reported_by']."'");
                                       																      $report = mysqli_fetch_assoc($reported_by);?>
                                       															 <span>Checked by : <?php echo $report['lname']; ?> , <?php echo $report['fname']; ?></span>
                                       															</div>
                                       														</div>
                                                                      <div class="col-md-2">
                                                                        <span>
                                                                        <a href="temporary2.php?id=<?php echo $rows['requestID']; ?>" >
                                                                          Form
                                                                        </a>
                                                                        </span>
                                                                        |
                                                                       <span>
                                                                      <!-- <a href="server.php?action=approveRequest&id=<?php echo $rows['requestID']; ?>" class="btn btn-success btn-fill btn_requestDetails btn-sm" onclick="return confirm('Confirm approve!')">Approve<i class="fa fa-check"></i></a> -->
                                                                       <a href="#" data-toggle="modal"data-id="<?php echo $rows['requestID']; ?>" class="click_pins">
                                                                         Replace
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
if (isset($_POST['replace_items'])) {
  $request = mysqli_real_escape_string($dbcon,$_POST['reqIDs']);
  $pin = mysqli_real_escape_string($dbcon,$_POST['pin']);

  $query_pin = mysqli_query($dbcon,"SELECT * FROM pin where borrower_slip_id = $request");
  $check_pin = mysqli_fetch_array($query_pin);

  if ($check_pin['pin_id']==$pin) {
    $query = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = $request");
    while ($check = mysqli_fetch_array($query)) {
      $utensilID1 = $check['utensils_id'];
     $combinedQty = $check['lost_qty'] + $check['damaged_qty'];
      $query3 = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = $utensilID1 and storage_id = $storageID");
      while ($rows = mysqli_fetch_array($query3)) {
        $storageQty = $rows['storage_qty'];

        $storageReplacedQTY = $storageQty + $combinedQty;
        $user =  $_SESSION['user']['user_id'];
        $lost_qty = $rows['lost_qty'] - $check['lost_qty'];
        $damaged_qty = $rows['damaged_qty'] - $check['damaged_qty'];
        $updateStocks1 = mysqli_query($dbcon,"UPDATE storage_stocks SET storage_qty = $storageReplacedQTY,lost_qty = $lost_qty,damaged_qty = $damaged_qty where utensils_id = $utensilID1 and storage_id = $storageID");
        $updateStatus = mysqli_query($dbcon,"UPDATE borrower_slip SET  status = 7 where borrower_slip_id = $request");
        $updateStatus1 = mysqli_query($dbcon,"UPDATE breakages_and_damages SET approved_by = $user,date_replaced = NOW() where borrower_slip_id = $request");
        $updateRemarks = mysqli_query($dbcon,"UPDATE borrower_slip_details SET  remarks = 0 where borrower_slip_id = $request");
      echo "<script>alert('Replaced successfully !');window.location.href='discrepancyReport.php';</script>";
      }
   }
   //insert notification
   $insertControl = mysqli_query($dbcon,"INSERT INTO notification_control (trans_id,notif_type_id,storage_id)
   values ('$request','4','$storageID')");
   $queryControl = mysqli_query($dbcon,"SELECT * FROM notification_control where trans_id = $request and notif_type_id = 4 ");
   $control = mysqli_fetch_array($queryControl);
   $notifQuery = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $request");
   $groupID = mysqli_fetch_array($notifQuery);
   $notifGroupQuery = mysqli_query($dbcon,"SELECT * FROM group_members where group_id = ".$groupID['group_id']);
   foreach ($notifGroupQuery as $key => $memberNotif) {
     $notif_members = $memberNotif['user_id'];
     $insertNotification = mysqli_query($dbcon,"INSERT INTO notification (notif_control_id,user_id,user_notif_type,notif_approved_date,seen_user)
     values ('".$control['notif_control_id']."','$notif_members','1',NOW(),'1')") or die(mysqli_error($dbcon));
   }
   //inser history replacement staff
   $historyType = 5;
   $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
   values (NOW(),'$user','$request','$storageID','$historyType')");
  }else {
    echo "<script>alert('Wrong PIN !');window.location.href='discrepancyReport.php';</script>";
  }
}

 ?>

        <!-- Mini Modal -->
        <div class="modal fade modal-mini modal-primary" id="enterPins" data-backdrop="false">
            <div class="modal-dialog">
                <form class="" action="discrepancyReport.php" method="post">
                <div class="modal-content">
                    <div class="modal-header justify-content-center">
                      <div class="category">
                         Confirm replacement..
                      </div>
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
                      <input type="submit" name="replace_items"id="submit_btn" class="btn btn-success btn-fill btn-sm" value="Submit">
                    </div>
                </div>
                </form>
            </div>
        </div>
        <!--  End Modal -->

  <?php include('dataTables2.php') ?>
  <script type="text/javascript">
    $('#requestTable').DataTable();

    $(".click_pins").click(function () {
        var ids = $(this).attr('data-id');
        $("#reqIDs").val( ids );
        $("#reqIDsx").val( ids );
        $('#enterPins').modal('show');
    });

  </script>
<?php include('footer.php') ?>
