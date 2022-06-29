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
                                             a.date_requested,a.received_by,a.aprvd_n_rlsd_by,a.storage_id,a.status,a.date_use,a.time_use,
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


                                             where a.storage_id = $storageID and a.status =2 and g.bsd_id > 0 and a.received_by != 'To receive..'
                                             group by b.group_id";
                              $result = mysqli_query($dbcon,$query);
                             ?>

                               <table class="table DataTable"id="requestTable">
                                   <thead>
                                     <tr>
                                     <th class="card">
                                        <div class="title ">
                                        <h5 class="info">Active Requests</h5>
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
                                                                     <div class="col-md-5">
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
                                                                      <div class="col-md-2">
                                                                       <span>
                                                                      <!-- <a href="server.php?action=approveRequest&id=<?php echo $rows['requestID']; ?>" class="btn btn-success btn-fill btn_requestDetails btn-sm" onclick="return confirm('Confirm approve!')">Approve<i class="fa fa-check"></i></a> -->
                                                                       <a href="#" data-toggle="modal"data-id="<?php echo $rows['requestID']; ?>" class="click_pin">
                                                                         Manage
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
if (isset($_POST['manage_from_modal'])) {
  $request = mysqli_real_escape_string($dbcon,$_POST['reqIDs']);
  $pin = mysqli_real_escape_string($dbcon,$_POST['pin']);

  $query_pin = mysqli_query($dbcon,"SELECT * FROM pin where borrower_slip_id = $request");
  $check_pin = mysqli_fetch_array($query_pin);

  if ($check_pin['pin_id']==$pin) {
    $checkStatus = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $request and received_by = 'To receive..' and status = 2");
    if (mysqli_num_rows($checkStatus)>0) {
      echo "<script>alert('Failed !');window.location.href='activeRequests.php';</script>";
    }else {

    $_SESSION['modify_id'] = $request;
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

                   where a.borrower_slip_id = $request";
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
    echo "<script>alert('Confirmed !');window.location.href='modifyUserRequests.php';</script>";
  }

  }else {
    echo "<script>alert('Wrong PIN !');window.location.href='activeRequests.php';</script>";
  }
}

 ?>

        <!-- Mini Modal -->
        <div class="modal fade modal-mini modal-primary" id="enterPin" data-backdrop="false">
            <div class="modal-dialog">
                <form class="" action="activeRequests.php" method="post">
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
                      <input type="submit" name="manage_from_modal"id="submit_btn" class="btn btn-success btn-fill btn-sm" value="Submit">
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
