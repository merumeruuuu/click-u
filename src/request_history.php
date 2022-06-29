<?php include('header.php') ?>
<br><br>
<div class="content" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                      <div class="col-md-3">
                        <h4 class="title">My Requests History:</h4>
                         </div>
                          <br><br>
                           </div>

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
											$masterQuery = "SELECT
																					 a.borrower_slip_id,a.group_id,a.date_requested,a.date_approved,a.date_modified,a.modified_by,a.added_by,
																					 a.aprvd_n_rlsd_by,a.status,a.storage_id,
																					 b.user_id,b.fname,b.lname

																					 from borrower_slip a
																					 left join users b on a.modified_by = b.user_id

																					 where a.group_id = $grpID
																					 order by borrower_slip_id desc";
										$reqIdQuerry = mysqli_query($dbcon,$masterQuery);
									 while ($requestID = mysqli_fetch_array($reqIdQuerry)) {
							 ?>
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
																					 <?php $query_pin = mysqli_query($dbcon,"SELECT * FROM pin where borrower_slip_id = ".$requestID['borrower_slip_id']);
																								 $pin = mysqli_fetch_array($query_pin); ?>
																					 <label class=" ">PIN : <?php echo $pin['pin_id']; ?></label>
																							<h4 class=" ">Request # <strong style="color:gray;"> <?php echo $requestID['borrower_slip_id']; ?></strong></h4>
																					</div>

                                            <div class="col-md-5">
                                           <div class="">
                                             <h6 style="color:gray;">Borrower/s :</h6>

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
                        <!-- <div class="">
                          <h4 style="color:gray;">Cancelled</h43>
                        </div> -->
                      </div>
                      <div class="col-md-12">
                        <div class="content">
                        <h6 style="color:gray;">Requested items : </h6>
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

                                      <!-- <div class="col-md-5">
                                     <div class="category">
                                      <span>Cancelled by : <?php echo $requestID['lname']; ?> , <?php echo $requestID['fname']; ?></span>
                                     </div>
                                     </div> -->
                            </div>
                         </div>
                       </div>
                      </div>
                    </div>
                    <?php
                  }?>
                    </td>
                    </tr>
                  </tbody>
                </table>
							<?php } ?>
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
