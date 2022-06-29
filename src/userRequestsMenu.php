<?php include('header.php') ?>
<style>
section {
  display: none;
  padding: 20px 0 0;
  border-top: 1px solid #ddd;
}

.tabs {
  display: none;
  width: 100%;
  margin: 0 auto;
}

label {
  display: inline-block;
  margin: 0 auto;
  padding: 15px 25px;
  text-align: center;
  color: #bbb;
}

label[for*='1']:before {
  content: ;
}

label[for*='2']:before {
  content: ;
}

label[for*='3']:before {
  content: ;
}
label[for*='4']:before {
  content: ;
}
label[for*='5']:before {
  content: ;
}

label:hover {
  color: #888;
  cursor: pointer;
}

.tabs:checked+label {
  color: #555;
  border-bottom: 2px solid orange;
}

#tab1:checked~#content1,
#tab2:checked~#content2,
#tab3:checked~#content3,
#tab4:checked~#content4,
#tab5:checked~#content5 {
  display: block;
}
</style>
<br><br><br>

<div class="content" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <h4 class="title">My Requests :</h4>
                    </div>

                    <div class="content" >


     <input class="tabs" id="tab1" type="radio" name="tabs" >
     <label for="tab1">On Queue</label>
     <input class="tabs" id="tab2" type="radio" name="tabs">
     <label for="tab2">Approved</label>
     <input class="tabs" id="tab3" type="radio" name="tabs">
     <label for="tab3">Cancelled</label>
     <input class="tabs" id="tab4" type="radio" name="tabs">
     <label for="tab4">Returned</label>
     <input class="tabs" id="tab5" type="radio" name="tabs">
     <label for="tab5">Damaged / Lost</label>

     <section id="content1">
       <div class="content">
         <div class="row">
           <div class="col-md-12">
             <div class="table-responsive">
               <?php
                 $_SESSION['user']['user_id'];
                 $userID = $_SESSION['user']['user_id'];

                 $query = mysqli_query($dbcon,"SELECT * FROM group_members where user_id = $userID order by group_id desc");
                 while ($rows = mysqli_fetch_array($query)) {
                   $grpID = $rows['group_id'];
                 $reqIdQuerry = mysqli_query($dbcon,"SELECT * FROM borrower_slip where group_id = $grpID and status <=1 order by borrower_slip_id desc");
                while ($requestID = mysqli_fetch_array($reqIdQuerry)) {
               ?>
             <table class="table"id="requestTable1">
               <thead>
                 <tr class="row">
                   <th class="col-md-12">Request Details :</th>

                 </tr>
               </thead>
               <tbody>
                 <tr class="row">
                   <td class="col-md-12">

                <?php
                $id = $requestID['group_id'];
                $members = "SELECT
                                  a.group_id,a.user_id,
                                  b.user_id,b.school_id,b.fname,b.lname
                                  from group_members a
                                  left join users b on a.user_id = b.user_id
                                  where a.group_id =$id";
                                 $res = mysqli_query($dbcon,$members); ?>
                                 <div class="card">
                                   <div class="row">
                                    <div class="col-md-12">
                                       <div class="header">
                                           <h4 class=" ">Request # <strong style="color:#07bfea"> <?php echo $requestID['borrower_slip_id']; ?></strong></h4>
                                       </div>

                                         <div class="col-md-5">
                                        <div class="">
                                          <h6 style="color:gray;">Borrower/s :<i class="fa fa-chevron-down"></i></h6>

                          <?php while ($member = mysqli_fetch_array($res)) {
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
                         <h6 style="color:gray;">Date Requested : <span class="category"><?php echo date('M d, Y',strtotime($requestID['date_requested']));?></span></h6>
                         <?php
                          $accountType = $_SESSION['account_type'];
                          $groupQuery = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = $id");
                          while ($group = mysqli_fetch_array($groupQuery)) {
                            $faculty = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$group['faculty_id']);
                             while ($fac = mysqli_fetch_array($faculty)) {
                          if (!empty($group['group_name'])) {
                           ?>
                            <h6 style="color:gray;">Group Name : <span class="category"><?php echo $group['group_name']; ?></span></h6>
                            <h6 style="color:gray;">Instructor : <span class="category"><?php echo $fac['lname']; ?> , <?php echo $fac['fname']; ?></span></h6>
                             <?php
                          }elseif (empty($group['group_name'])&& $accountType =="7") {
                           ?>
                           <h6 style="color:gray;">Group Name : <span class="category"> n/a</span></h6>
                           <h6 style="color:gray;">Instructor : <span class="category"><?php echo $fac['lname']; ?> , <?php echo $fac['fname']; ?></span></h6>
                           <?php
                         }else {
                           ?>
                           <h6 style="color:gray;">Group Name :<span class="category"> n/a</span></h6>
                           <h6 style="color:gray;">Instructor :<span class="category"> n/a</span></h6>
                           <?php
                         }
                          }
                          } ?>

                         <?php $torageID = $requestID['storage_id'];
                                $stor = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $torageID");
                                while ($show = mysqli_fetch_array($stor)) {
                                  ?>
                          <h6 style="color:gray;">Requested From : <span class="category"><?php echo $show['storage_name']; ?></span></h6>
                                  <?php
                                } ?>

                     </div>
                    </div>
                   <div class="col-md-2">
                     <div class="">
                       <h4 style="color:#07bfea;">Pending</h43>

                     </div>
                   </div>
                   <div class="col-md-12">
                     <div class="content">
                     <h6 style="color:gray;">Requested items : <i class="fa fa-chevron-down"></i></h6>
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
                           $rIDS = $requestID['borrower_slip_id'];
                           $reqDetails = "SELECT
                                               a.borrower_slip_id as requestID,a.group_id,a.date_requested,a.storage_id,a.status,
                                               b.borrower_slip_id,b.utensils_id,b.qty,
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
                          <div class="col-md-8">
                         <div class="stats">
                                <i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($requestID['date_requested']));
                                     echo " | ".date('H:i:s A',strtotime($requestID['date_requested'])); ?></span>
                           </div>
                           </div>
                           <div class="col-md-1">
                             <span>
                             <a href="#" type="button" name="button">Manage</a>
                            </span>
                           </div>
                           <div class="col-md-3">
                            <span>
                            <a href="server.php?cancel=<?php echo $requestID['borrower_slip_id']; ?>" onclick="return confirm('Are you sure you want to cancel?')" name="button">Cancel</a>
                           </span>
                           </div>
                     </div>
                   </div>
                 </div>
                 </div>
                 </div>

                 </td>
                 </tr>
               </tbody>
             </table>
             <?php
           }
           }if (empty($reqIdQuerry)) {
             ?>
             <div class="content">
                <h4>No Damaged / Lost Items...</h4>
             </div>
             <?php
           }?>
            </div>
             </div>
       </div>
        </div>
     </section>
     <section id="content2">
       <div class="content">
         <div class="row">
           <div class="col-md-12">
             <div class="table-responsive">
             <table class="table"id="requestTable2">
               <thead>
                 <tr class="row">
                   <th class="col-md-12">Request Details :</th>

                 </tr>
               </thead>
               <tbody>
                 <tr class="row">
                   <td class="col-md-12">
                   <?php
                     $_SESSION['user']['user_id'];
                     $userID = $_SESSION['user']['user_id'];

                     $query = mysqli_query($dbcon,"SELECT * FROM group_members where user_id = $userID order by group_id desc");
                     while ($rows = mysqli_fetch_array($query)) {
                       $grpID = $rows['group_id'];
                       $masterQuery = "SELECT
                                            a.borrower_slip_id,a.group_id,a.date_requested,a.date_approved,a.date_modified,a.modified_by,a.added_by,a.received_by,
                                            a.aprvd_n_rlsd_by,a.status,a.storage_id,
                                            b.user_id,b.fname,b.lname

                                            from borrower_slip a
                                            left join users b on a.aprvd_n_rlsd_by = b.user_id

                                            where a.group_id = $grpID and a.status = 2 and a.received_by = '0'
                                            order by a.borrower_slip_id desc";
                     $reqIdQuerry = mysqli_query($dbcon,$masterQuery);
                    while ($requestID = mysqli_fetch_array($reqIdQuerry)) {


                ?>
                <?php
                $id = $requestID['group_id'];
                $members = "SELECT
                                  a.group_id,a.user_id,
                                  b.user_id,b.school_id,b.fname,b.lname
                                  from group_members a
                                  left join users b on a.user_id = b.user_id
                                  where a.group_id =$id";
                                 $res = mysqli_query($dbcon,$members); ?>
                                 <div class="card">
                                   <div class="row">
                                    <div class="col-md-12">
                                       <div class="header">
                                           <h4 class=" ">Request # <strong style="color:green;"> <?php echo  $requestID['borrower_slip_id'];
                                          ?></strong></h4>
                                       </div>

                                         <div class="col-md-5">
                                        <div class="">
                                          <h6 style="color:gray;">Borrower/s :<i class="fa fa-chevron-down"></i></h6>

                          <?php while ($member = mysqli_fetch_array($res)) {
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
                         <h6 style="color:gray;">Date Requested : <span class="category"><?php echo  date('M d, Y',strtotime($requestID['date_requested'])); ?></span></h6>
                         <?php

                          $accountType = $_SESSION['account_type'];
                          $groupQuery = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = $id");
                          while ($group = mysqli_fetch_array($groupQuery)) {
                            $faculty = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$group['faculty_id']);
                             while ($fac = mysqli_fetch_array($faculty)) {
                          if (!empty($group['group_name'])) {
                           ?>
                            <h6 style="color:gray;">Group Name : <span class="category"><?php echo $group['group_name']; ?></span></h6>
                            <h6 style="color:gray;">Instructor : <span class="category"><?php echo $fac['lname']; ?> , <?php echo $fac['fname']; ?></span></h6>
                             <?php
                          }elseif (empty($group['group_name'])&& $accountType =="7") {
                           ?>
                           <h6 style="color:gray;">Group Name : <span class="category"> n/a</span></h6>
                           <h6 style="color:gray;">Instructor : <span class="category"><?php echo $fac['lname']; ?> , <?php echo $fac['fname']; ?></span></h6>
                           <?php
                         }else {
                           ?>
                           <h6 style="color:gray;">Group Name :<span class="category"> n/a</span></h6>
                           <h6 style="color:gray;">Instructor :<span class="category"> n/a</span></h6>
                           <?php
                         }
                          }
                          } ?>

                         <?php $torageID = $requestID['storage_id'];
                                $stor = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $torageID");
                                while ($show = mysqli_fetch_array($stor)) {
                                  ?>
                          <h6 style="color:gray;">Requested From : <span class="category"><?php echo $show['storage_name']; ?></span></h6>
                                  <?php
                                } ?>

                     </div>
                    </div>
                   <div class="col-md-2">
                     <div class="">
                       <h4 style="color:green;">Approved</h43>
                     </div>
                   </div>
                   <div class="col-md-12">
                     <div class="content">
                     <h6 style="color:gray;">Requested items : <i class="fa fa-chevron-down"></i></h6>
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
                           $rIDS = $requestID['borrower_slip_id'];
                           $reqDetails = "SELECT
                                               a.borrower_slip_id as requestID,a.group_id,a.date_requested,a.storage_id,a.status,
                                               b.borrower_slip_id,b.utensils_id,b.qty,
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
                                <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($requestID['date_approved']));
                                    echo " | ".date('H:i:s A',strtotime($requestID['date_approved'])); ?> </span>
                           </div>
                           </div>
                           <div class="col-md-5">
                                   <div class="category">
                                    <span>Approved by : <?php echo $requestID['lname']; ?> , <?php echo $requestID['fname']; ?></span>
                                   </div>
                           </div>
                           <div class="col-md-1">
                             <span>
                             <a href="server.php?return=<?php echo $requestID['borrower_slip_id'];?>" type="button"onclick="return confirm('Confirm return!')" name="button">Return</a>
                            </span>
                           </div>

                     </div>
                   </div>
                 </div>
                 </div>
                 </div>
                 <?php
               }
               }?>
                 </td>
                 </tr>
               </tbody>
             </table>
            </div>
             </div>
       </div>
        </div>
     </section>
     <section id="content3">
       <div class="content">
         <div class="row">
           <div class="col-md-12">
             <div class="table-responsive">
             <table class="table"id="requestTable3">
               <thead>
                 <tr class="row">
                   <th class="col-md-12">Request Details :</th>
                 </tr>
               </thead>
               <tbody>
                 <tr class="row">
                   <td class="col-md-12">
                   <?php
                     $_SESSION['user']['user_id'];
                     $userID = $_SESSION['user']['user_id'];
                     $query = mysqli_query($dbcon,"SELECT * FROM group_members where user_id = $userID order by group_id desc");
                     while ($rows = mysqli_fetch_array($query)) {
                       $grpID = $rows['group_id'];
                       $masterQuery = "SELECT
                                            a.borrower_slip_id,a.group_id,a.date_requested,a.date_approved,a.date_modified,a.modified_by,a.added_by,
                                            a.aprvd_n_rlsd_by,a.status,a.storage_id,
                                            b.user_id,b.fname,b.lname

                                            from borrower_slip a
                                            left join users b on a.modified_by = b.user_id

                                            where a.group_id = $grpID and a.status = 3
                                            order by a.borrower_slip_id desc";
                     $reqIdQuerry = mysqli_query($dbcon,$masterQuery);
                    while ($requestID = mysqli_fetch_array($reqIdQuerry)) {
                ?>
                <?php
                $id = $requestID['group_id'];
                $rIDS = $requestID['borrower_slip_id'];
                $torageID = $requestID['storage_id'];
                $approve = $requestID['modified_by'];
                $dateModified = $requestID['date_modified'];

                $members = "SELECT
                                  a.group_id,a.user_id,
                                  b.user_id,b.school_id,b.fname,b.lname
                                  from group_members a
                                  left join users b on a.user_id = b.user_id
                                  where a.group_id =$id";
                                 $res = mysqli_query($dbcon,$members); ?>
                                 <div class="card">
                                   <div class="row">
                                    <div class="col-md-12">
                                       <div class="header">
                                           <h4 class=" ">Request # <strong style="color:gray;"> <?php echo $requestID['borrower_slip_id']; ?></strong></h4>
                                       </div>

                                         <div class="col-md-5">
                                        <div class="">
                                          <h6 style="color:gray;">Borrower/s :<i class="fa fa-chevron-down"></i></h6>

                          <?php while ($member = mysqli_fetch_array($res)) {
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
                         <h6 style="color:gray;">Date Requested : <span class="category"><?php echo  date('M d, Y',strtotime($requestID['date_requested'])); ?></span></h6>
                         <?php

                          $accountType = $_SESSION['account_type'];
                          $groupQuery = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = $id");
                          while ($group = mysqli_fetch_array($groupQuery)) {
                            $faculty = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$group['faculty_id']);
                             while ($fac = mysqli_fetch_array($faculty)) {
                          if (!empty($group['group_name'])) {
                           ?>
                            <h6 style="color:gray;">Group Name : <span class="category"><?php echo $group['group_name']; ?></span></h6>
                            <h6 style="color:gray;">Instructor : <span class="category"><?php echo $fac['lname']; ?> , <?php echo $fac['fname']; ?></span></h6>
                             <?php
                          }elseif (empty($group['group_name'])&& $accountType =="7") {
                           ?>
                           <h6 style="color:gray;">Group Name : <span class="category"> n/a</span></h6>
                           <h6 style="color:gray;">Instructor : <span class="category"><?php echo $fac['lname']; ?> , <?php echo $fac['fname']; ?></span></h6>
                           <?php
                         }else {
                           ?>
                           <h6 style="color:gray;">Group Name :<span class="category"> n/a</span></h6>
                           <h6 style="color:gray;">Instructor :<span class="category"> n/a</span></h6>
                           <?php
                         }
                          }
                          } ?>

                         <?php
                                $stor = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $torageID");
                                while ($show = mysqli_fetch_array($stor)) {
                                  ?>
                          <h6 style="color:gray;">Requested From : <span class="category"><?php echo $show['storage_name']; ?></span></h6>
                                  <?php
                                } ?>

                     </div>
                    </div>
                   <div class="col-md-2">
                     <div class="">
                       <h4 style="color:gray;">Cancelled</h43>

                     </div>
                   </div>
                   <div class="col-md-12">
                     <div class="content">
                     <h6 style="color:gray;">Requested items : <i class="fa fa-chevron-down"></i></h6>
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

                           $reqDetails = "SELECT
                                               a.borrower_slip_id as requestID,a.group_id,a.date_requested,a.storage_id,a.status,
                                               b.borrower_slip_id,b.utensils_id,b.qty,
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
                                <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($dateModified));
                                echo " | ".date('H:i:s A',strtotime($dateModified)); ?></span>
                           </div>
                           </div>

                                   <div class="col-md-5">
                                  <div class="category">
                                   <span>Cancelled by : <?php echo $requestID['lname']; ?> , <?php echo $requestID['fname']; ?></span>
                                  </div>
                                  </div>
                         </div>
                      </div>
                    </div>
                   </div>
                 </div>
                 <?php

               }
               }?>
                 </td>
                 </tr>
               </tbody>
             </table>
            </div>
             </div>
       </div>
        </div>
     </section>
     <section id="content4">
       <div class="content">
         <div class="row">
           <div class="col-md-12">
             <div class="table-responsive">
             <table class="table"id="requestTable4">
               <thead>
                 <tr class="row">
                   <th class="col-md-12">Request Details :</th>

                 </tr>
               </thead>
               <tbody>
                 <tr class="row">
                   <td class="col-md-12">
                   <?php
                     $_SESSION['user']['user_id'];
                     $userID = $_SESSION['user']['user_id'];

                     $query = mysqli_query($dbcon,"SELECT * FROM group_members where user_id = $userID order by group_id desc");
                     while ($rows = mysqli_fetch_array($query)) {
                       $grpID = $rows['group_id'];
                       $masterQuery = "SELECT
                                            a.borrower_slip_id,a.group_id,a.date_requested,a.date_approved,a.date_modified,a.date_received,a.modified_by,a.added_by,
                                            a.aprvd_n_rlsd_by,a.status,a.storage_id,
                                            b.user_id,b.fname,b.lname

                                            from borrower_slip a
                                            left join users b on a.aprvd_n_rlsd_by = b.user_id

                                            where a.group_id = $grpID and a.status = 5
                                            order by a.borrower_slip_id desc";
                     $reqIdQuerry = mysqli_query($dbcon,$masterQuery);
                    while ($requestID = mysqli_fetch_array($reqIdQuerry)) {


                ?>
                <?php
                $id = $requestID['group_id'];
                $members = "SELECT
                                  a.group_id,a.user_id,
                                  b.user_id,b.school_id,b.fname,b.lname
                                  from group_members a
                                  left join users b on a.user_id = b.user_id
                                  where a.group_id =$id";
                                 $res = mysqli_query($dbcon,$members); ?>
                                 <div class="card">
                                   <div class="row">
                                    <div class="col-md-12">
                                       <div class="header">
                                           <h4 class=" ">Request # <strong style="color:orange"> <?php echo $requestID['borrower_slip_id']; ?></strong></h4>
                                       </div>

                                         <div class="col-md-5">
                                        <div class="">
                                          <h6 style="color:gray;">Borrower/s :<i class="fa fa-chevron-down"></i></h6>

                          <?php while ($member = mysqli_fetch_array($res)) {
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
                         <h6 style="color:gray;">Date Requested : <span class="category"><?php echo  date('M d, Y',strtotime($requestID['date_requested'])); ?></span></h6>
                         <?php

                          $accountType = $_SESSION['account_type'];
                          $groupQuery = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = $id");
                          while ($group = mysqli_fetch_array($groupQuery)) {
                            $faculty = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$group['faculty_id']);
                             while ($fac = mysqli_fetch_array($faculty)) {
                          if (!empty($group['group_name'])) {
                           ?>
                            <h6 style="color:gray;">Group Name : <span class="category"><?php echo $group['group_name']; ?></span></h6>
                            <h6 style="color:gray;">Instructor : <span class="category"><?php echo $fac['lname']; ?> , <?php echo $fac['fname']; ?></span></h6>
                             <?php
                          }elseif (empty($group['group_name'])&& $accountType =="7") {
                           ?>
                           <h6 style="color:gray;">Group Name : <span class="category"> n/a</span></h6>
                           <h6 style="color:gray;">Instructor : <span class="category"><?php echo $fac['lname']; ?> , <?php echo $fac['fname']; ?></span></h6>
                           <?php
                         }else {
                           ?>
                           <h6 style="color:gray;">Group Name :<span class="category"> n/a</span></h6>
                           <h6 style="color:gray;">Instructor :<span class="category"> n/a</span></h6>
                           <?php
                         }
                          }
                          } ?>

                         <?php $torageID = $requestID['storage_id'];
                                $stor = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $torageID");
                                while ($show = mysqli_fetch_array($stor)) {
                                  ?>
                          <h6 style="color:gray;">Requested From : <span class="category"><?php echo $show['storage_name']; ?></span></h6>
                                  <?php
                                } ?>

                     </div>
                    </div>
                   <div class="col-md-2">
                     <div class="">
                       <h4 style="color:orange;">Returned</h43>

                     </div>
                   </div>
                   <div class="col-md-12">
                     <div class="content">
                     <h6 style="color:gray;">Requested items : <i class="fa fa-chevron-down"></i></h6>
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
                           $rIDS = $requestID['borrower_slip_id'];
                           $reqDetails = "SELECT
                                               a.borrower_slip_id as requestID,a.group_id,a.date_requested,a.storage_id,a.status,
                                               b.borrower_slip_id,b.utensils_id,b.qty,
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
                                <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($requestID['date_received']));
                                echo " | ".date('H:i:s A',strtotime($requestID['date_received'])); ?></span>
                           </div>
                           </div>
                           <div class="col-md-5">
                                   <div class="category">
                                    <span>Received by : <?php echo $requestID['lname']; ?> , <?php echo $requestID['fname']; ?></span>
                                   </div>
                           </div>

                     </div>
                   </div>
                 </div>
                 </div>
                 </div>
                 <?php
               }
               }?>
                 </td>
                 </tr>
               </tbody>
             </table>
            </div>
             </div>
       </div>
        </div>
     </section>
     <section id="content5">
       <div class="content">
         <div class="row">
           <div class="col-md-12">
             <div class="table-responsive">
               <?php
                 $_SESSION['user']['user_id'];
                 $userID = $_SESSION['user']['user_id'];

                 $query = mysqli_query($dbcon,"SELECT * FROM group_members where user_id = $userID order by group_id desc");
                 while ($rows = mysqli_fetch_array($query)) {
                   $grpID = $rows['group_id'];
                 $reqIdQuerry = mysqli_query($dbcon,"SELECT * FROM borrower_slip where group_id = $grpID and status = 6 order by borrower_slip_id desc");

                while ($requestID = mysqli_fetch_array($reqIdQuerry)) {

            ?>
             <table class="table"id="requestTable5">
               <thead>
                 <tr class="row">
                   <th class="col-md-12">Request Details :</th>

                 </tr>
               </thead>
               <tbody>
                 <tr class="row">
                   <td class="col-md-12">
                 <?php
                $id = $requestID['group_id'];
                $members = "SELECT
                                  a.group_id,a.user_id,
                                  b.user_id,b.school_id,b.fname,b.lname
                                  from group_members a
                                  left join users b on a.user_id = b.user_id
                                  where a.group_id =$id";
                                 $res = mysqli_query($dbcon,$members); ?>
                                 <div class="card">
                                   <div class="row">
                                    <div class="col-md-12">
                                       <div class="header">
                                           <h4 class=" ">Request # <strong style="color:red"> <?php echo $requestID['borrower_slip_id']; ?></strong></h4>
                                       </div>

                                         <div class="col-md-5">
                                        <div class="">
                                          <h6 style="color:gray;">Borrower/s :<i class="fa fa-chevron-down"></i></h6>

                          <?php while ($member = mysqli_fetch_array($res)) {
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
                         <h6 style="color:gray;">Date Requested : <span class="category"><?php echo $requestID['date_requested']; ?></span></h6>
                         <?php

                          $accountType = $_SESSION['account_type'];
                          $groupQuery = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = $id");
                          while ($group = mysqli_fetch_array($groupQuery)) {
                            $faculty = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$group['faculty_id']);
                             while ($fac = mysqli_fetch_array($faculty)) {
                          if (!empty($group['group_name'])) {
                           ?>
                            <h6 style="color:gray;">Group Name : <span class="category"><?php echo $group['group_name']; ?></span></h6>
                            <h6 style="color:gray;">Instructor : <span class="category"><?php echo $fac['lname']; ?> , <?php echo $fac['fname']; ?></span></h6>
                             <?php
                          }elseif (empty($group['group_name'])&& $accountType =="7") {
                           ?>
                           <h6 style="color:gray;">Group Name : <span class="category"> n/a</span></h6>
                           <h6 style="color:gray;">Instructor : <span class="category"><?php echo $fac['lname']; ?> , <?php echo $fac['fname']; ?></span></h6>
                           <?php
                         }else {
                           ?>
                           <h6 style="color:gray;">Group Name :<span class="category"> n/a</span></h6>
                           <h6 style="color:gray;">Instructor :<span class="category"> n/a</span></h6>
                           <?php
                         }
                          }
                          } ?>

                         <?php $torageID = $requestID['storage_id'];
                                $stor = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $torageID");
                                while ($show = mysqli_fetch_array($stor)) {
                                  ?>
                          <h6 style="color:gray;">Requested From : <span class="category"><?php echo $show['storage_name']; ?></span></h6>
                                  <?php
                                } ?>

                     </div>
                    </div>
                   <div class="col-md-3">
                     <div class="">
                       <h4 style="color:red;">w/Discrepancy</h43>

                     </div>
                   </div>
                   <div class="col-md-12">
                     <div class="content">
                     <h6 style="color:gray;">Requested items : <i class="fa fa-chevron-down"></i></h6>
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
                           $rIDS = $requestID['borrower_slip_id'];
                           $reqDetails = "SELECT
                                               a.borrower_slip_id as requestID,a.group_id,a.date_requested,a.storage_id,a.status,
                                               b.borrower_slip_id,b.utensils_id,b.qty,
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
                          <div class="col-md-8">
                         <div class="stats">
                                <i class="fa fa-clock-o"></i> <span><?php echo $requestID['date_requested']; ?></span>
                           </div>
                           </div>
                           <div class="col-md-1">
                             <span>
                             <a href="#" type="button" name="button">Manage</a>
                            </span>
                           </div>
                           <div class="col-md-3">
                            <span>
                            <a href="server.php?cancel=<?php echo $requestID['borrower_slip_id']; ?>" onclick="return confirm('Are you sure you want to cancel?')" name="button">Cancel</a>
                           </span>
                           </div>
                     </div>
                   </div>
                 </div>
                 </div>
                 </div>

                 </td>
                 </tr>
               </tbody>
             </table>
             <?php
             }
           }
           if (mysqli_num_rows($reqIdQuerry)) {
             ?>
             <div class="content">
                <h4>No Damaged / Lost Items...</h4>
             </div>
             <?php
           }?>
            </div>
             </div>
       </div>
        </div>
     </section>
    </div>


                        </div>
                    </div>
                </div>
              </div>
          </div>






<?php include('dataTables2.php') ?>
<script type="text/javascript">
          $('#requestTable1').DataTable();
          $('#requestTable2').DataTable();
          $('#requestTable3').DataTable();
          $('#requestTable4').DataTable();
          $('#requestTable5').DataTable();


        </script>
        <!-- <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#requestTable').DataTable( {
                responsive: true
            } );

            new $.fn.dataTable.FixedHeader( table );
        } );
        </script> -->
<?php include('footer.php') ?>
