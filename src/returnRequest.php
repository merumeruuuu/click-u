<?php include('header.php'); ?>
<br><br>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                         <div class="card">
                    <div class="content">
                      <?php
                             $stID = $_SESSION['user']['storage_id'];
                             $query = "SELECT
                                             a.borrower_slip_id as receiveID,a.group_id as grpID,a.date_requested,a.date_approved,a.added_by,a.purpose,
                                             a.received_by,a.aprvd_n_rlsd_by,a.storage_id,a.status,a.date_use,a.time_use,
                                             b.group_id,b.group_name,b.instructor,b.group_leader_id,
                                             c.group_id,c.user_id,c.added_by,
                                             d.user_id,d.school_id,d.fname,d.lname,
                                             e.user_id,e.account_type_id,
                                             f.storage_id,f.storage_name


                                             from borrower_slip a
                                             left join group_table b on a.group_id = b.group_id
                                             left join group_members c on b.group_id = c.group_id
                                             left join users d on c.user_id = d.user_id
                                             left join user_settings e on d.user_id = e.user_id
                                             left join storage f on a.storage_id = f.storage_id


                                             where  a.status = 2 and a.received_by = 'To receive..' and a.storage_id = $stID
                                             group by b.group_id desc
                                             order by receiveID desc";
                              $result = mysqli_query($dbcon,$query);
                             ?>

                               <table class="table DataTable"id="requestTable">
                                   <thead>
                                     <tr>
                                     <th class="card">
                                        <div class="title ">
                                        <h5 class="info">Receiving Borrowed Items :</h5>
                                        </div>
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
                                                 <h5 class=" ">Request # <strong style="color:#07bfea"> <?php echo $rows['receiveID']; ?></strong></h5>
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
                                                         <label for="">Borrowed Items</label>
                                                         <?php
                                                         $reqID = $rows['receiveID'];
                                                             $queryString = "SELECT
                                                               a.bsd_id,a.borrower_slip_id,a.utensils_id,a.qty,a.storage_id,a.on_use,
                                                               b.utensils_id as itemID,b.utensils_name,b.utensils_cat_id,
                                                               c.utensils_cat_id,c.category

                                                               from borrower_slip_details a
                                                               left JOIN utensils b on a.utensils_id = b.utensils_id
                                                               left join utensils_category c on b.utensils_cat_id = c.utensils_cat_id

                                                               where a.borrower_slip_id = $reqID";
                                                               $itemQuery = mysqli_query($dbcon,$queryString);

                                                          ?>
                                                          <table class="table">
                                                            <thead>
                                                              <col span="4" style="background-color:auto;">
                                                              <col style="background-color:#e9e7e0;">
                                                              <tr>
                                                                <th>Item ID</th>
                                                                <th>Item Name</th>
                                                                <th>Item Category</th>
                                                                <th>Borrowed Quantity</th>
                                                              </tr>
                                                            </thead>
                                                            <tbody>
                                                              <?php while ($items = mysqli_fetch_array($itemQuery)) {
                                                                ?>
                                                              <tr>
                                                                <td><?php echo  $items['itemID']?></td>
                                                                <td><?php echo  $items['utensils_name']?></td>
                                                                <td><?php echo  $items['category']?></td>
                                                                <td><?php echo  $items['on_use']?></td>

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
                                                                           <i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($rows['date_approved']));
                                                                                echo " | ".date('h:i:s A',strtotime($rows['date_approved'])); ?></span>
                                                                      </div>
                                                                      </div>
                                                                        <div class="col-md-5">
                                                                       <div class="stats">
                                                                         <?php

                                                                           $staff = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$rows['aprvd_n_rlsd_by']."'");
                                                                           $row = mysqli_fetch_assoc($staff);
                                                                          ?>
                                                                           <span>Approved by : <?php echo $row['lname'] ?> ,<?php echo $row['fname'] ?></span>
                                                                         </div>
                                                                      </div>
                                                                      <div class="col-md-2">
                                                                          <div class="stats">
                                                                         <span>
                                                                        <!-- <a href="server.php?action=receiveItems&id=<?php echo $rows['receiveID'];?>"  onclick="return confirm('Confirm Receive!')">Receive </a> -->
                                                                        <a href="#" data-toggle="modal"data-id="<?php echo $rows['receiveID']; ?>" class="click_pin">
                                                                          Receive
                                                                        </a>
                                                                         </span>
                                                                         |
                                                                         <span>
                                                                       <a href="server.php?action=manageRequest&id=<?php echo $rows['receiveID'];?>"> Report </a>
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
      if (isset($_POST['receive_requestss'])) {
        $receiveID = mysqli_real_escape_string($dbcon,$_POST['reqIDs']);
        $pin = mysqli_real_escape_string($dbcon,$_POST['pin']);

        $query_pin = mysqli_query($dbcon,"SELECT * FROM pin where borrower_slip_id = $receiveID");
        $check_pin = mysqli_fetch_array($query_pin);

        if ($check_pin['pin_id']==$pin) {
          $query = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $receiveID");
          while ($check = mysqli_fetch_array($query)) {
            $utensilID1 = $check['utensils_id'];
            $storageID1 = $check['storage_id'];
            $requestQty1 = $check['qty'];
            $onUseQty = $check['on_use'];
            $update = mysqli_query($dbcon,"UPDATE borrower_slip_details SET returned = $requestQty1 where borrower_slip_id = $receiveID and utensils_id = $utensilID1  ");

            $query3 = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = $utensilID1 and storage_id = $storageID1");
            while ($rows = mysqli_fetch_array($query3)) {
              $storageQty1 = $rows['storage_qty'];

              $addStorageQty = $storageQty1 + $requestQty1;
              $storageOnUse = $rows['on_use'] - $requestQty1;
              $updateOnUse =  $onUseQty - $requestQty1;
              $staff1 = $_SESSION['user']['user_id'];
              $updateStocks1 = mysqli_query($dbcon,"UPDATE storage_stocks SET storage_qty = $addStorageQty,on_use = $storageOnUse where utensils_id = $utensilID1 and storage_id = $storageID1");
              $updateStatus = mysqli_query($dbcon,"UPDATE borrower_slip SET date_received = NOW(), received_by = $staff1, status = 5 where borrower_slip_id = $receiveID");
              $updateStatus1 = mysqli_query($dbcon,"UPDATE borrower_slip_details SET on_use =  $updateOnUse where borrower_slip_id = $receiveID and utensils_id = $utensilID1 and storage_id = $storageID1 ");

              $update_query = "UPDATE  notification
                              LEFT JOIN notification_control
                              ON      notification.notif_control_id = notification_control.notif_control_id
                              SET     notification.seen_user = 1,notification.notif_count = 0,notification.notif_approved_date = NOW()
                              WHERE  notification.user_notif_type = 1 and notification_control.trans_id = $receiveID and notification_control.notif_type_id = 2";
             mysqli_query($dbcon, $update_query);
            echo "<script>alert('Received successfully !');window.location.href='returnRequest.php';</script>";
            }
         }
         //inser history
         $historyType = 3;
         $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
         values (NOW(),'$staff1','$receiveID','$storageID1','$historyType')");
            }
        else {
          echo "<script>alert('Wrong PIN !');window.location.href='returnRequest.php';</script>";
        }
      }
       ?>
              <!-- Mini Modal -->
              <div class="modal fade modal-mini modal-primary" id="enterPin" data-backdrop="false">
                  <div class="modal-dialog">
                      <form class="" action="returnRequest.php" method="post">
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
                            <input type="submit" name="receive_requestss"id="submit_btn" class="btn btn-success btn-fill btn-sm" value="Submit">
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

  </script>
<?php include('footer.php') ?>
