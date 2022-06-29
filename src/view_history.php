<?php include('header.php');
?>
<br><br>
<div class="content" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                      <div class="col-md-3">
                        <h4 class="title">History</h4>
                         </div>
                          <br><br>
                           </div>
      <?php if (isset($_GET['id'])) {
        $_SESSION['history_id'] = $_GET['id'];
      } ?>
      <?php  $hist_id = $_SESSION['history_id'];
             $historyQuery = mysqli_query($dbcon,"SELECT * FROM history where id = $hist_id");
             $hist = mysqli_fetch_array($historyQuery); ?>
             <?php
                    $query = "SELECT
                                    a.borrower_slip_id as requestID,a.group_id as grpID,a.date_requested,a.added_by,a.date_modified,a.modified_by,a.purpose,
                                    a.date_requested,a.date_approved,a.date_received,a.received_by,a.storage_id,a.status,a.aprvd_n_rlsd_by,
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

                                    where a.borrower_slip_id = ".$hist['trans_id'];
                     $result = mysqli_query($dbcon,$query);
                     $rows = mysqli_fetch_array($result);
                    ?>
     <div class="content">
       <div class="card">
           <div class="row">
        <!-- staff history -->
      <?php if ($_SESSION['account_type'] == 3 ||$_SESSION['account_type'] == 4 || $_SESSION['account_type'] == 5) {
        ?>
                           <div class="col-md-12">
                              <div class="header">
                                <?php if ($_SESSION['account_type'] == 3 ||$_SESSION['account_type']==4 ||$_SESSION['account_type']==5) {
                                  ?>
                                <h4 class=" ">Request # <strong style="color:gray;"> <?php echo  $rows['borrower_slip_id'];?></strong></h4>
                                  <?php
                                }else {
                                  ?>
                               <?php $query_pin = mysqli_query($dbcon,"SELECT * FROM pin where borrower_slip_id = ".$rows['borrower_slip_id']);
                                   $pin = mysqli_fetch_array($query_pin); ?>
                               <label class=" ">PIN : <?php echo $pin['pin_id']; ?></label>
                               <h4 class=" ">Request # <strong style="color:green;"> <?php echo $rows['borrower_slip_id']; ?></strong></h4>

                                  <?php
                                } ?>

                              </div>

                                <div class="col-md-5">
                               <div class="">
                                 <h6 style="color:gray;">Borrower/s :</h6>
                 <?php
                 $grpID = $rows['grpID'];
                 $borrowers = "SELECT
                           a.group_id,a.user_id,
                           b.user_id,b.school_id,b.lname,b.fname

                           from group_members a
                           left join users b on a.user_id = b.user_id

                           where a.group_id = $grpID";
                  $check = mysqli_query($dbcon,$borrowers);
                 while ($member = mysqli_fetch_array($check)) {
                 ?>
                 <div class="category">
               <?php echo $member['school_id']; ?> - <?php echo $member['lname']; ?>,<?php echo $member['fname']; ?>
                  </div>
                 <?php
                 } ?>
              </div>
           </div>
           <div class="col-md-5">
             <div class="">
                 <h6 style="color:gray;">Purpose : <span class="category"><?php echo $rows['purpose']; ?></span></h6>
                <h6 style="color:gray;">Date Requested : <span class="category"><?php echo  date('M d, Y',strtotime($rows['date_requested'])); ?></span></h6>
                <?php
                 $groupQuery = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = ".$rows['grpID']);
                 $group = mysqli_fetch_array($groupQuery);
                 if (!empty($group['group_name'])) {
                  ?>
                   <h6 style="color:gray;">Group Name : <span class="category"><?php echo $group['group_name']; ?></span></h6>
                   <h6 style="color:gray;">Instructor : <span class="category"><?php echo $group['instructor']; ?></span></h6>
                    <?php
                 }elseif (empty($group['group_name'])&& $rows['account_type_id']=="7") {
                  ?>
                  <h6 style="color:gray;">Group Name : <span class="category"> n/a</span></h6>
                  <h6 style="color:gray;">Instructor : <span class="category"><?php echo $group['instructor']; ?></span></h6>
                  <?php
                }else {
                  ?>
                  <h6 style="color:gray;">Group Name :<span class="category"> n/a</span></h6>
                  <h6 style="color:gray;">Instructor :<span class="category"> n/a</span></h6>
                  <?php
                }
                 ?>
                <?php $torageID = $rows['storage_id'];
                       $stor = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $torageID");
                       $show = mysqli_fetch_array($stor);
                         ?>
                 <h6 style="color:gray;">Requested From : <span class="category"><?php echo $show['storage_name']; ?></span></h6>
            </div>
           </div>
          <div class="col-md-12">
          <div class="content">
            <h6 style="color:gray;">Borrowed items : </h6>
            <table class="table">
              <thead>
                <tr>
                  <th>Item Name w/ Description</th>
                  <th>Category</th>
                  <th>Requested Quantity</th>
                </tr>
              </thead>
              <tbody>
                  <?php
                  $rIDS = $rows['borrower_slip_id'];
                  $reqDetails = "SELECT
                                      a.borrower_slip_id as requestID,a.group_id,a.date_requested,a.storage_id,a.status,a.received_by,
                                      b.borrower_slip_id,b.utensils_id,b.qty,b.on_use,
                                      c.utensils_id,c.utensils_name,c.utensils_cat_id,
                                      d.utensils_cat_id,d.category

                                      from borrower_slip a
                                      left join borrower_slip_details b on a.borrower_slip_id = b.borrower_slip_id
                                      left join utensils c on b.utensils_id = c.utensils_id
                                      left join utensils_category d on c.utensils_cat_id = d.utensils_cat_id

                                      where a.borrower_slip_id = $rIDS ";

                                      $result = mysqli_query($dbcon,$reqDetails);

                   foreach ($result as $key => $value) {
                    ?>
                  <tr>
                  <td><?php echo $value['utensils_name']; ?></td>
                  <td><?php echo $value['category']; ?></td>
                  <?php if ($value['status']==2 && $value['received_by']!=0) {
                    ?>
                 <td><?php echo $value['on_use']; ?></td>
                    <?php
                  } ?>
                  <td><?php echo $value['qty']; ?></td>
                </tr>
                <?php
              } ?>
              </tbody>
            </table>
            </div>
          </div>
          <?php
          if ($_SESSION['account_type'] == 3 ||$_SESSION['account_type']==4 || $_SESSION['account_type']==5) {
            //if staff
            ?>
            <div class="col-md-12">
              <div class="footer">
                  <hr>
                  <div class="col-md-5">
                  <div class="stats">
                        <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($hist['date_added']));
                            echo " | ".date('h:i:s A',strtotime($hist['date_added'])); ?> </span>
                   </div>
                   </div>
                  <?php
                   if ($rows['status']==2) {
                    ?>
                     <div class="col-md-5">
                             <div class="category">
                                <?php
                                  $staff2 = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$rows['aprvd_n_rlsd_by']."'");
                                  $rowz = mysqli_fetch_assoc($staff2);
                                 ?>
                                  <span>Released by : <?php echo $rowz['lname'] ?> ,<?php echo $rowz['fname'] ?></span>
                             </div>
                     </div>
                    <?php
                  }if ($rows['status']==5) {
                    ?>
                    <div class="col-md-5">
                            <div class="category">
                               <?php
                                 $staff2 = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$rows['received_by']."'");
                                 $rowz = mysqli_fetch_assoc($staff2);
                                ?>
                                 <span>Received by : <?php echo $rowz['lname'] ?> ,<?php echo $rowz['fname'] ?></span>
                            </div>
                    </div>
                    <?php
                  }if ($rows['status']==6) {
                    ?>
                    <div class="col-md-5">
                            <div class="category">
                               <?php
                                $breakgs = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = '".$rows['requestID']."'");
                                foreach ($breakgs as $key => $value);
                                 $staff2 = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$value['reported_by']."'");
                                 $rowz = mysqli_fetch_assoc($staff2);
                                ?>
                                 <span>Checked by : <?php echo $rowz['lname'] ?> ,<?php echo $rowz['fname'] ?></span>
                            </div>
                    </div>
                    <?php
                  }if ($rows['status']==7) {
                    ?>
                    <div class="col-md-5">
                            <div class="category">
                               <?php
                               $breakgs = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = '".$rows['requestID']."'");
                               foreach ($breakgs as $key => $value);
                                 $staff2 = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$value['approved_by']."'");
                                 $rowz = mysqli_fetch_assoc($staff2);
                                ?>
                                 <span>Approved by : <?php echo $rowz['lname'] ?> ,<?php echo $rowz['fname'] ?></span>
                            </div>
                    </div>
                    <?php
                  }
                    ?>
              </div>
            </div>
            <?php
          }if ($_SESSION['account_type']==6 || $_SESSION['account_type']==7) {
            // if user
            ?>
            <div class="col-md-12">
              <div class="footer">
                  <hr>
                  <?php
                   if (!empty($rows['date_received']&&!empty($rows['received_by'])&& $notif['notif_type_id']!=1)) {
                    ?>
                    <div class="col-md-5">
                    <div class="stats">
                          <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($rows['date_received']));
                              echo " | ".date('h:i:s A',strtotime($rows['date_received'])); ?> </span>
                     </div>
                     </div>
                     <div class="col-md-5">
                             <div class="category">
                                <?php
                                  $staff2 = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$rows['received_by']."'");
                                  $rowz = mysqli_fetch_assoc($staff2);
                                 ?>
                                  <span>Received by : <?php echo $rowz['lname'] ?> ,<?php echo $rowz['fname'] ?></span>
                             </div>
                     </div>
                    <?php
                  }else {
                    ?>
                    <div class="col-md-5">
                    <div class="stats">
                          <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($rows['date_approved']));
                              echo " | ".date('h:i:s A',strtotime($rows['date_approved'])); ?> </span>
                     </div>
                     </div>
                     <div class="col-md-5">
                             <div class="category">
                               <?php

                                 $staff = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$rows['aprvd_n_rlsd_by']."'");
                                 $row = mysqli_fetch_assoc($staff);
                                ?>
                                 <span>Approved by : <?php echo $row['lname'] ?> ,<?php echo $row['fname'] ?></span>
                             </div>
                     </div>
                    <?php
                  }
                   ?>
                    <div class="col-md-2">
                      <span>
                        <?php

                         if ($notif['seen_user']==2) {
                          ?>
                         <a href="server.php?return=<?php echo $rows['borrower_slip_id'];?>" type="button"onclick="return confirm('Confirm return!')" name="button">Return</a>
                          <?php
                        } ?>
                     </span>
                    </div>
              </div>
            </div>
            <?php
          } ?>

                </div>

        <?php
      }
      ?>
      <!-- user history -->
      <?php if ($_SESSION['account_type'] == 6 || $_SESSION['account_type'] == 7) {
        ?>
                           <div class="col-md-12">
                              <div class="header">
                               <?php $query_pin = mysqli_query($dbcon,"SELECT * FROM pin where borrower_slip_id = ".$rows['borrower_slip_id']);
                                   $pin = mysqli_fetch_array($query_pin); ?>
                               <label class=" ">PIN : <?php echo $pin['pin_id']; ?></label>
                               <h4 class=" ">Request # <strong style="color:green;"> <?php echo $rows['borrower_slip_id']; ?></strong></h4>
                              </div>
                                <div class="col-md-5">
                               <div class="">
                                 <h6 style="color:gray;">Borrower/s :</h6>
                 <?php
                 $grpID = $rows['grpID'];
                 $borrowers = "SELECT
                           a.group_id,a.user_id,
                           b.user_id,b.school_id,b.lname,b.fname

                           from group_members a
                           left join users b on a.user_id = b.user_id

                           where a.group_id = $grpID";
                  $check = mysqli_query($dbcon,$borrowers);
                 while ($member = mysqli_fetch_array($check)) {
                 ?>
                 <div class="category">
               <?php echo $member['school_id']; ?> - <?php echo $member['lname']; ?>,<?php echo $member['fname']; ?>
                  </div>
                 <?php
                 } ?>
              </div>
           </div>
           <div class="col-md-5">
             <div class="">
                 <h6 style="color:gray;">Purpose : <span class="category"><?php echo $rows['purpose']; ?></span></h6>
                <h6 style="color:gray;">Date Requested : <span class="category"><?php echo  date('M d, Y',strtotime($rows['date_requested'])); ?></span></h6>
                <?php
                 $groupQuery = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = ".$rows['grpID']);
                 $group = mysqli_fetch_array($groupQuery);
                 if (!empty($group['group_name'])) {
                  ?>
                   <h6 style="color:gray;">Group Name : <span class="category"><?php echo $group['group_name']; ?></span></h6>
                   <h6 style="color:gray;">Instructor : <span class="category"><?php echo $group['instructor']; ?></span></h6>
                    <?php
                 }elseif (empty($group['group_name'])&& $rows['account_type_id']=="7") {
                  ?>
                  <h6 style="color:gray;">Group Name : <span class="category"> n/a</span></h6>
                  <h6 style="color:gray;">Instructor : <span class="category"><?php echo $group['instructor']; ?></span></h6>
                  <?php
                }else {
                  ?>
                  <h6 style="color:gray;">Group Name :<span class="category"> n/a</span></h6>
                  <h6 style="color:gray;">Instructor :<span class="category"> n/a</span></h6>
                  <?php
                }
                 ?>
                <?php $torageID = $rows['storage_id'];
                       $stor = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $torageID");
                       $show = mysqli_fetch_array($stor);
                         ?>
                 <h6 style="color:gray;">Requested From : <span class="category"><?php echo $show['storage_name']; ?></span></h6>
            </div>
           </div>
          <div class="col-md-12">
          <div class="content">
            <h6 style="color:gray;">Borrowed items : </h6>
            <table class="table">
              <thead>
                <tr>
                  <th>Item Name w/ Description</th>
                  <th>Category</th>
                  <th>Requested Quantity</th>
                </tr>
              </thead>
              <tbody>
                  <?php
                  $rIDS = $rows['borrower_slip_id'];
                  $reqDetails = "SELECT
                                      a.borrower_slip_id as requestID,a.group_id,a.date_requested,a.storage_id,a.status,a.received_by,
                                      b.borrower_slip_id,b.utensils_id,b.qty,b.on_use,
                                      c.utensils_id,c.utensils_name,c.utensils_cat_id,
                                      d.utensils_cat_id,d.category

                                      from borrower_slip a
                                      left join borrower_slip_details b on a.borrower_slip_id = b.borrower_slip_id
                                      left join utensils c on b.utensils_id = c.utensils_id
                                      left join utensils_category d on c.utensils_cat_id = d.utensils_cat_id

                                      where a.borrower_slip_id = $rIDS ";

                                      $result = mysqli_query($dbcon,$reqDetails);

                   foreach ($result as $key => $value) {
                    ?>
                  <tr>
                  <td><?php echo $value['utensils_name']; ?></td>
                  <td><?php echo $value['category']; ?></td>
                  <?php if ($value['status']==2 && $value['received_by']!=0) {
                    ?>
                 <td><?php echo $value['on_use']; ?></td>
                    <?php
                  } ?>
                  <td><?php echo $value['qty']; ?></td>
                </tr>
                <?php
              } ?>
              </tbody>
            </table>
            </div>
          </div>
            <div class="col-md-12">
              <div class="footer">
                  <hr>
                    <div class="col-md-5">
                    <div class="stats">
                          <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($hist['date_added']));
                              echo " | ".date('h:i:s A',strtotime($hist['date_added'])); ?> </span>
                     </div>
                     </div>
                     <div class="col-md-5">
                             <div class="category">
                                <?php
                                  $staff2 = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$rows['added_by']."'");
                                  $rowz = mysqli_fetch_assoc($staff2);
                                 ?>
                                  <span>Requested by : <?php echo $rowz['lname'] ?> ,<?php echo $rowz['fname'] ?></span>
                             </div>
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
		</div>
	</div>
</div>
<?php include('footer.php') ?>
