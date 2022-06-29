<?php include('header.php'); ?>
<br><br>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                         <div class="card">
                    <div class="content">
                      <?php
                             $query = "SELECT
                                             a.borrower_slip_id as requestID,a.group_id as grpID,a.date_requested,a.date_approved,
                                             a.date_cancelled,a.date_modified,a.added_by,a.storage_id,a.status,
                                             b.group_id,b.group_name,b.faculty_id,b.group_leader_id,
                                             c.group_id,c.user_id,c.added_by,
                                             d.user_id,d.school_id,d.fname,d.lname,
                                             e.user_id,e.account_type_id


                                             from borrower_slip a
                                             left join group_table b on a.group_id = b.group_id
                                             left join group_members c on b.group_id = c.group_id
                                             left join users d on c.user_id = d.user_id
                                             left join user_settings e on d.user_id = e.user_id
                                             where a.status > 1
                                             order by a.date_modified desc";
                              $result = mysqli_query($dbcon,$query);
                             ?>

                               <table class="table DataTable"id="requestTable">
                                   <thead>
                                     <tr>
                                     <th class="card">
                                        <div class="title ">
                                        <h5 class="info">Activity Logs :</h5>
                                    </tr>
                                   </thead>
                                 <tbody>
                                   <?php

                                    while ($rows = mysqli_fetch_array($result)) {
                                     $check = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$rows['faculty_id']);
                                     $faculty = mysqli_fetch_array($check);

                                     $group = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$rows['group_leader_id']);
                                     $groupLeader = mysqli_fetch_array($group);
                                  ?>
                                   <tr>
                                     <td>
                                       <div class="card">
                                         <div class="row">
                                          <div class="col-md-12">
                                             <div class="header">
                                                 <h5 class=" ">Request # <strong style="color:gray;"> <?php echo $rows['requestID']; ?></strong></h5>
                                             </div>
                                             <div class="content">
                                               <div class="col-md-4">
                                              <div class="">
                                                <label for=""><i class="fa fa-chevron-down"></i>Borrower/s</label>
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
                                               <div class="col-md-4">
                                                   <label for="">Group Name <i class="fa fa-chevron-right"></i></label>
                                                   <span>
                                                   <?php if (!empty($rows['group_name'])){ ?>

                                                     <?php echo $rows['group_name']; ?>

                                                   <?php }else {
                                                     echo "N/A";
                                                   }?>
                                                 </span>
                                                  <br>
                                                 <label for="">Date Requested <i class="fa fa-chevron-right"></i></label>
                                                 <span>
                                                 <?php echo $rows['date_requested']; ?>
                                               </span>
                                               <br>

                                                 <label for="">Instructor <i class="fa fa-chevron-right"></i></label>
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
                                             <div class="col-md-4">
                                                 <h3><?php echo $rows['status']; ?></h3>
                                             </div>
                                             <br>

                                                       <div class="col-md-12">
                                                         <label for=""><i class="fa fa-chevron-down"></i>Requested Items</label>
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


                                                       <div class="col-md-6">
                                                           <div class="content">

                                                       </div>
                                                       <div class="footer">

                                                           <hr>
                                                           <div class="stats">
                                                               <i class="fa fa-clock-o"></i> <span><?php echo $rows['date_modified']; ?></span>
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
