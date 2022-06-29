<?php include('header.php');
?>
<br><br>
<div class="content" >
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                  <div class="content">
                    <?php if ($_SESSION['account_type']==3||$_SESSION['account_type']==4||$_SESSION['account_type']==5) {
                      ?>
                  <a href="discrepancyReport.php"><i class="fa fa-chevron-left"></i> Back</a>
                      <?php
                    }else {
                      ?>
                 <a href="userRequestsMenu2.php"><i class="fa fa-chevron-left"></i> Back</a>
                      <?php
                    } ?>

                  </div>

      <?php if (isset($_GET['id'])) {
        $_SESSION['form_id'] = $_GET['id'];
      } ?>
      <?php  $form_id = $_SESSION['form_id'];
              ?>
              <?php
                     $query = "SELECT
                                     a.borrower_slip_id as requestID,a.group_id as grpID,a.date_requested,a.added_by,a.date_approved,a.date_requested,a.aprvd_n_rlsd_by,a.storage_id,a.status,
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


                                     where a.borrower_slip_id = $form_id";
                      $result = mysqli_query($dbcon,$query);
                      $rows = mysqli_fetch_array($result);
                      $check = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$rows['faculty_id']);
                      $faculty = mysqli_fetch_array($check);

                     ?>


 <div class="content">
   <div class="card">
     <div class="row">
       <div class="col-md-12">
         <div class="content">
        <img src="img/form_header.jpg"style='width:100%;' border="0" alt="Null">
         </div>
        <br>
        <center>
        <h4>Damage/Lost form</h4>
      </center>
       </div>
      <div class="col-md-12">
         <div class="content">
           <div class="header">
               <h5 class=" ">Request # <strong> <?php echo $rows['requestID']; ?></strong></h5>
           </div>
           <div class="col-md-4">
          <div class="">
            <label for="">Borrower/s :</label>
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
               <label for="">Group Name :</label>
               <span>
               <?php if (!empty($rows['group_name'])){ ?>

                 <?php echo $rows['group_name']; ?>

               <?php }else {
                 echo "N/A";
               }?>
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
         <div class="col-md-4">
           <label for="">Date Borrowed :</label>
           <span>
           <?php echo  date('M d, Y',strtotime($rows['date_requested'])); ?>
         </span>
         <br>
        <label for="">Borrowed From :</label>
        <span>
        <?php echo $rows['storage_name']; ?>
      </span>
         </div>
      <div class="col-md-12">
       <hr>
      </div>
                   <div class="col-md-12">
                     <label for="">Reported Items :</label>
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
                          <tr>
                            <th>Item ID</th>
                            <th>Item Name</th>
                            <th>Item Category</th>
                           <th>Lost </th>
                           <th>Damaged </th>
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

                          <div class="row">
                            <div class="col-md-12">
                            <br>
                            </div>
                            <div class="col-md-4">
                            <div > <hr> </div>
                             <div class="text-center">STUDENT'S NAME AND SIGNITURE</div>
                             <div class="clearfix"></div>
                            </div>
                            <div class="col-md-4">
                              <div class="text-center">

                           </div>
                            </div>
                            <div class="col-md-4">
                            <div > <hr> </div>
                             <div class="text-center">WORKING SCHOLAR IN-CHARGE</div>
                             <div class="clearfix"></div>
                            </div>
                            </div>
                            <div class="row">
                              <div class="col-md-12">
                              <br>
                              </div>
                              <div class="col-md-4">
                              <div > <hr> </div>
                               <div class="text-center">INSTRUCTOR</div>
                               <div class="clearfix"></div>
                              </div>
                              <div class="col-md-4">
                                <div class="text-center">
                              <br>
                             <h6>VERIFIED BY:</h6>
                             </div>
                              </div>
                              <div class="col-md-4">
                              <div > <hr> </div>
                               <div class="text-center">LABORATORY IN-CHARGE</div>
                               <div class="clearfix"></div>
                              </div>
                              </div>
                              <div class="col-md-12">
                                <div class="text-center">
                              <br><br><br><br><br>
                             <h6>NOTED BY:</h6>
                             <br>
                             <p>DR. GRAYFIELD T. BAJAO</p>
                             <p>Dean, College of HRM/SEACAST Director</p>
                             <br>
                             </div>
                              </div>
                              <div class="col-md-12">
                                <div class="content">
                                  <div class="pull-right">
                                    <a href="#">Print</a>
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
