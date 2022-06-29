<?php include('header.php');
?>
<?php
if (isset($_GET['approve_request'])) {
  $borID = $_GET['approve_request'];
  $user = $_SESSION['user']['user_id'];
  $checkStats = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $borID");
  foreach ($checkStats as $key => $value);
  if ($value['status']==3) {
    echo "<script>alert('Failed! request has been cancelled!');</script>";
  }else {
    if ($value['status']==8) {
        $updateRequest = mysqli_query($dbcon,"UPDATE borrower_slip set date_verified = NOW(),verified_by = $user,status = 0 where borrower_slip_id = $borID");
    }
  }
}
if (isset($_GET['deny_request'])) {
  $borID2 = $_GET['deny_request'];
  $user = $_SESSION['user']['user_id'];
  $checkStats = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $borID2");
  foreach ($checkStats as $key => $value);
  if ($value['status']==3) {
    echo "<script>alert('Failed! request has been cancelled!');</script>";
  }else {
    $getItems = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $borID2");
    foreach ($getItems as $key => $value) {
      $getStocks = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = '".$value['utensils_id']."'and storage_id = '".$value['storage_id']."'");
      foreach ($getStocks as $key => $stocks) {
        $newStock = $stocks['storage_qty'] + $value['qty'];
        $newReserved = $stocks['reserved_qty'] - $value['qty'];
        $updateStocks = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStock,reserved_qty = $newReserved where utensils_id = '".$stocks['utensils_id']."'and storage_id = '".$stocks['storage_id']."'");
      }
    }
    $updateRequestD = mysqli_query($dbcon,"UPDATE borrower_slip_details set reserved_qty = 0 where borrower_slip_id = $borID2");
    $updateRequest = mysqli_query($dbcon,"UPDATE borrower_slip set date_denied = NOW(),denied_by = $user,status = 9 where borrower_slip_id = $borID2");

    $fetchGroupID = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $borID2");
    foreach ($fetchGroupID as $key => $groupID);
    $getMembers = mysqli_query($dbcon,"SELECT * FROM group_members where group_id = ".$groupID['group_id']);
    foreach ($getMembers as $key => $members) {
      $insertNotifControl = mysqli_query($dbcon,"INSERT INTO notification_control (trans_id,notif_type_id,storage_id)
      values('$borID2','8','".$groupID['storage_id']."')");
      $fetchControl = mysqli_query($dbcon,"SELECT notif_control_id from notification_control order by notif_control_id desc limit 1");
      foreach ($fetchControl as $key => $control) {

      $insertNotif = mysqli_query($dbcon,"INSERT INTO notification (notif_control_id,user_id,user_notif_type,notif_date,seen_user)
      values('".$control['notif_control_id']."','".$members['user_id']."','1',NOW(),'1')");
      }
    }
  }
}?>
<br><br>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                         <div class="card">
                    <div class="content">
                      <?php
                             $query = "SELECT
                                             a.borrower_slip_id as requestID,a.group_id as grpID,a.date_requested,a.added_by,a.date_requested,a.storage_id,a.status,a.purpose,a.date_use,a.time_use,
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

                                             where a.status = 8 and g.bsd_id >0
                                             group by b.group_id";
                              $result = mysqli_query($dbcon,$query);
                             ?>

                               <table class="table "id="requestTable">
                                   <thead>
                                     <tr>
                                     <th class="card">
                                        <div class="title ">
                                        <h5 class="info">Borrow requests (for dean's approval)</h5>
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
                                                                     <div class="col-md-9">
                                                                    <div class="stats">
                                                                           <i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($rows['date_requested']));
                                                                                echo " | ".date('H:i:s A',strtotime($rows['date_requested'])); ?></span>
                                                                      </div>
                                                                      </div>
                                                                      <div class="col-md-3">
                                                                        <span>
                                                                          <a href="?approve_request=<?php echo $rows['requestID']; ?>"onclick="return confirm('Confirm approval!');">
                                                                            Approve
                                                                          </a>
                                                                        </span>|
                                                                       <span>
                                                                       <a href="?deny_request=<?php echo $rows['requestID']; ?>"onclick="return confirm('Confirm denial!');">
                                                                         Deny
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
  <?php include('dataTables2.php') ?>
  <script type="text/javascript">
    $('#requestTable').DataTable();
  </script>
<?php include('footer.php') ?>
