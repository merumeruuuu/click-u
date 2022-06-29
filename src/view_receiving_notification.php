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
              $notif_transID = $_SESSION['receive_notif_transId'];

              $select = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $storageID");
              $show = mysqli_fetch_array($select);
               ?>
                   <div class="card">
                     <div class="content">
                       <div class="col-md-12">
                        <span> <a href="returnRequest.php"><i class="fa fa-chevron-left"></i> Go to list</a> </span>

                       </div>
                     </div>

                    <div class="content">
                      <br>
                      <?php
                             $query = "SELECT
                                             a.borrower_slip_id as receiveID,a.group_id as grpID,a.date_requested,a.added_by,a.date_requested,a.date_approved,a.storage_id,
                                             a.date_received,a.received_by,a.status,a.aprvd_n_rlsd_by,
                                             b.group_id,b.group_name,b.faculty_id,b.group_leader_id,
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

                                             where a.borrower_slip_id = $notif_transID";
                              $result = mysqli_query($dbcon,$query);
                             ?>

                                   <?php

                                    $rows = mysqli_fetch_array($result);
                                     $check = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$rows['faculty_id']);
                                     $faculty = mysqli_fetch_array($check);

                                     $group = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$rows['group_leader_id']);
                                     $groupLeader = mysqli_fetch_array($group);
                                  ?>

                                       <div class="card">
                                         <div class="row">
                                          <div class="col-md-12">
                                             <div class="header">
                                                 <h5 class=" ">Request # <strong style="color:#07bfea"> <?php echo $rows['receiveID']; ?></strong></h5>
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
                                                    <?php echo $faculty['lname']; ?> , <?php echo $faculty['fname']; ?>
                                                     <?php
                                                   }else {
                                                     echo "N/A";
                                                   }
                                                     ?>
                                                 </span>
                                             </div>
                                             <div class="col-md-2">
                                               <?php if ($rows['status']==5) {
                                                 ?>
                                                 <h4>Received</h4>
                                                 <?php
                                               }else {
                                                 ?>
                                                <h4>To receive...</h4>
                                                 <?php
                                               } ?>

                                             </div>
                                             <br>

                                                       <div class="col-md-12">
                                                         <label for="">Requested Items</label>
                                                         <?php
                                                         $reqID = $rows['receiveID'];
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
                                                          <table class="table">
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
                                                                    <?php if ($rows['status']==2) {
                                                                    ?>
                                                                     <div class="col-md-4">
                                                                    <div class="stats">
                                                                           <i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($rows['date_approved']));
                                                                                echo " | ".date('H:i:s A',strtotime($rows['date_approved'])); ?></span>
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
                                                                      <div class="col-md-3">
                                                                          <div class="stats">
                                                                         <span>
                                                                        <a href="server.php?action=receiveItems&id=<?php echo $rows['receiveID'];?>" class="btn btn-success btn-sm btn-fill btn_requestDetails" onclick="return confirm('Confirm Receive!')">Receive <i class="fa fa-check"></i></a>
                                                                         </span>
                                                                         <span>
                                                                       <button class="btn btn-info btn-sm btn-fill btn_requestDetails" > Manage <i class="fa fa-angle-right"></i></button>
                                                                         </span>
                                                                      </div>
                                                                      </div>

                                                                      <?php
                                                                    }else {
                                                                      ?>
                                                                      <div class="col-md-4">
                                                                     <div class="stats">
                                                                            <i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($rows['date_approved']));
                                                                                 echo " | ".date('H:i:s A',strtotime($rows['date_approved'])); ?></span>
                                                                       </div>
                                                                       </div>
                                                                       <div class="col-md-4">
                                                                      <div class="stats">
                                                                        <?php

                                                                          $staff = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$rows['aprvd_n_rlsd_by']."'");
                                                                          $row = mysqli_fetch_assoc($staff);
                                                                         ?>
                                                                          <span>Approved by : <?php echo $row['lname'] ?> ,<?php echo $row['fname'] ?></span>
                                                                        </div>
                                                                        </div>
                                                                        <br>
                                                                        <div class="col-md-4">
                                                                       <div class="stats">
                                                                              <i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($rows['date_received']));
                                                                                   echo " | ".date('H:i:s A',strtotime($rows['date_received'])); ?></span>
                                                                         </div>
                                                                         </div>
                                                                         <div class="col-md-4">
                                                                        <div class="stats">
                                                                          <?php

                                                                            $staff = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$rows['received_by']."'");
                                                                            $row = mysqli_fetch_assoc($staff);
                                                                           ?>
                                                                            <span>Received by : <?php echo $row['lname'] ?> ,<?php echo $row['fname'] ?></span>
                                                                          </div>
                                                                          </div>


                                                                      <?php
                                                                    } ?>

                                                                      </div>
                                                                </div>
                                                              </div>

                                      </div>
                                    </div>
                                    </div>
                                     </div>
                                     </div>

                             </div>
                                </div>
</div>
  </div>
    </div>
        </div>

<?php include('footer.php') ?>
