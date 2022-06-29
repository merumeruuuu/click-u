<?php include('header.php');
 ?>
<script>
$(document).ready(function(){
	$('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
		localStorage.setItem('activeTab', $(e.target).attr('href'));
	});
	var activeTab = localStorage.getItem('activeTab');
	if(activeTab){
		$('#myTab a[href="' + activeTab + '"]').tab('show');
	}
});
</script>
<style>

</style>
<br><br>
<div class="content" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                      <div class="col-md-2">
                        <h4 class="title">My Requests</h4>
                         </div>
                          <br><br>
                           </div>
                            <div class="content" >
                            <div class="bs-example">
                        <ul class="nav nav-tabs" id="myTab">
                           <li class="active"><a data-toggle="tab" href="#sectionA">On Queue</a></li>
                           <li><a data-toggle="tab" href="#sectionB">Approved</a></li>
                           <li><a data-toggle="tab" href="#sectionC">Cancelled</a></li>
                           <li><a data-toggle="tab" href="#sectionD">Returned</a></li>
                           <li><a data-toggle="tab" href="#sectionE">Damage/Lost</a></li>
                           <li><a data-toggle="tab" href="#sectionF">Denied</a></li>
                        </ul>
                        <div class="tab-content">
        <div id="sectionA" class="tab-pane fade in active">
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
										 $reqIdQuerry = mysqli_query($dbcon,"SELECT * FROM borrower_slip where status != 2 and status != 3 and status != 5 and status != 6 and status != 7 and status != 9  and group_id = $grpID   order by borrower_slip_id desc");
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
																					 <?php $query_pin = mysqli_query($dbcon,"SELECT * FROM pin where borrower_slip_id = ".$requestID['borrower_slip_id']);
																								 $pin = mysqli_fetch_array($query_pin); ?>
																					 <label class=" ">PIN : <?php echo $pin['pin_id']; ?></label>
																							<h4 class=" ">Request # <strong > <?php echo $requestID['borrower_slip_id']; ?></strong></h4>
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
                           <h6 style="color:gray;">When to use : <span class="category"><?php echo date('M d, Y',strtotime($requestID['date_use']));?> | <?php echo date('h:i:s A',strtotime($requestID['date_use']));?></span></h6>
                           <h6 style="color:gray;">Purpose : <span class="category"><?php echo $requestID['purpose'];?></span></h6>
                            <h6 style="color:gray;">Date Requested : <span class="category"><?php echo date('M d, Y',strtotime($requestID['date_requested']));?></span></h6>
                            <?php
                             $accountType = $_SESSION['account_type'];
                             $groupQuery = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = $id");
                             while ($group = mysqli_fetch_array($groupQuery)) {
                             if (!empty($group['group_name'])) {
                              ?>
                               <h6 style="color:gray;">Group Name : <span class="category"><?php echo $group['group_name']; ?></span></h6>
                               <h6 style="color:gray;">Instructor : <span class="category"><?php echo $group['instructor']; ?></span></h6>
                                <?php
                             }elseif (empty($group['group_name'])&& $accountType =="7") {
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
                        <h6 style="color:gray;">Requested items : </h6>
                        <table class="table">
                          <thead>
                            <tr >
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
														<?php if (!empty($requestID['date_modified'])&& $requestID['modified_by']>0) {
															?>
															<div class="col-md-5">
																 <div class="stats">
																			 <i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($requestID['date_modified']));
																						echo " | ".date('h:i:s A',strtotime($requestID['date_modified'])); ?></span>
																	</div>
																	</div>
																	<div class="col-md-5">
																		 <div class="stats">
																			 <?php $mod = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$requestID['modified_by']);
																			       while ($show = mysqli_fetch_array($mod)) {
																			      ?>
                                   <span>Modified by : <?php  echo $show['lname']; ?></span>
																						<?php
																			       } ?>

																			</div>
																			</div>
																 <div class="col-md-2">
                                   <?php
                                   if ($requestID['status']!=8) {
                                     ?>
                                     <span>
  																	 <a href="server.php?action=modify&id=<?php echo $requestID['borrower_slip_id']; ?>" type="button" name="button">Modify</a>
  																	</span>
  																    |
                                     <?php
                                   } ?>
																	<span>
																	<a href="server.php?cancel=<?php echo $requestID['borrower_slip_id']; ?>" onclick="return confirm('Are you sure you want to cancel?')" name="button">Cancel</a>
																 </span>
																 </div>
															<?php
														}else{
															?>
															<div class="col-md-10">
															  <div class="stats">
																			<i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($requestID['date_requested']));
																					 echo " | ".date('h:i:s A',strtotime($requestID['date_requested'])); ?></span>
																 </div>
																 </div>
	                              <div class="col-md-2">
                                  <?php
                                  if ($requestID['status']!=8) {
                                    ?>
                                    <span>
                                    <a href="server.php?action=modify&id=<?php echo $requestID['borrower_slip_id']; ?>" type="button" name="button">Modify</a>
                                   </span>
                                     |
                                    <?php
                                  } ?>
	                               <span>
	                               <a href="server.php?cancel=<?php echo $requestID['borrower_slip_id']; ?>" onclick="return confirm('Are you sure you want to cancel?')" name="button">Cancel</a>
	                              </span>
	                              </div>
															<?php
														} ?>

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
        <div id="sectionB" class="tab-pane fade">
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
																					 a.borrower_slip_id,a.group_id,a.date_requested,a.date_approved,a.date_modified,a.modified_by,a.added_by,a.received_by,a.purpose,
																					 a.aprvd_n_rlsd_by,a.status,a.storage_id,a.date_use,a.time_use,
																					 b.user_id,b.fname,b.lname

																					 from borrower_slip a
																					 left join users b on a.aprvd_n_rlsd_by = b.user_id

																					 where a.group_id = $grpID and a.status = 2
																					 order by a.borrower_slip_id desc";
										$reqIdQuerry = mysqli_query($dbcon,$masterQuery);
									 while ($requestID = mysqli_fetch_array($reqIdQuerry)) {
							 ?>
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
																					 <?php $query_pin = mysqli_query($dbcon,"SELECT * FROM pin where borrower_slip_id = ".$requestID['borrower_slip_id']);
																								 $pin = mysqli_fetch_array($query_pin); ?>
																					 <label class=" ">PIN : <?php echo $pin['pin_id']; ?></label>
																							<h4 class=" ">Request # <strong > <?php echo $requestID['borrower_slip_id']; ?></strong></h4>
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
                              <h6 style="color:gray;">When to use : <span class="category"><?php echo date('M d, Y',strtotime($requestID['date_use']));?> | <?php echo date('h:i:s A',strtotime($requestID['date_use']));?></span></h6>
                           <h6 style="color:gray;">Purpose : <span class="category"><?php echo $requestID['purpose'];?></span></h6>
                            <h6 style="color:gray;">Date Requested : <span class="category"><?php echo date('M d, Y',strtotime($requestID['date_requested']));?></span></h6>
                            <?php
                             $accountType = $_SESSION['account_type'];
                             $groupQuery = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = $id");
                             while ($group = mysqli_fetch_array($groupQuery)) {
                             if (!empty($group['group_name'])) {
                              ?>
                               <h6 style="color:gray;">Group Name : <span class="category"><?php echo $group['group_name']; ?></span></h6>
                               <h6 style="color:gray;">Instructor : <span class="category"><?php echo $group['instructor']; ?></span></h6>
                                <?php
                             }elseif (empty($group['group_name'])&& $accountType =="7") {
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
                                       echo " | ".date('h:i:s A',strtotime($requestID['date_approved'])); ?> </span>
                              </div>
                              </div>
                              <div class="col-md-5">
                                      <div class="category">
                                       <span>Approved by : <?php echo $requestID['lname']; ?> , <?php echo $requestID['fname']; ?></span>
                                      </div>
                              </div>
                              <div class="col-md-1">
                                <?php if ($requestID['received_by']!='To receive..') {
                                  ?>
                                  <span>
                                  <a href="server.php?return=<?php echo $requestID['borrower_slip_id'];?>" type="button"onclick="return confirm('Confirm return!')" name="button">Return</a>
                                 </span>
                                  <?php
                                } ?>
                              </div>

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
        <div id="sectionC" class="tab-pane fade">
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
																					 a.borrower_slip_id,a.group_id,a.date_requested,a.date_approved,a.date_modified,a.modified_by,a.added_by,a.purpose,
																					 a.aprvd_n_rlsd_by,a.status,a.storage_id,a.date_use,a.time_use,
																					 b.user_id,b.fname,b.lname

																					 from borrower_slip a
																					 left join users b on a.modified_by = b.user_id

																					 where a.group_id = $grpID and a.status = 3
																					 order by a.borrower_slip_id desc";
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
																							<h4 class=" ">Request # <strong > <?php echo $requestID['borrower_slip_id']; ?></strong></h4>
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
                          <h6 style="color:gray;">When to use : <span class="category"><?php echo date('M d, Y',strtotime($requestID['date_use']));?> | <?php echo date('h:i:s A',strtotime($requestID['date_use']));?></span></h6>
                           <h6 style="color:gray;">Purpose : <span class="category"><?php echo $requestID['purpose'];?></span></h6>
                            <h6 style="color:gray;">Date Requested : <span class="category"><?php echo date('M d, Y',strtotime($requestID['date_requested']));?></span></h6>
                            <?php
                             $accountType = $_SESSION['account_type'];
                             $groupQuery = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = $id");
                             while ($group = mysqli_fetch_array($groupQuery)) {
                             if (!empty($group['group_name'])) {
                              ?>
                               <h6 style="color:gray;">Group Name : <span class="category"><?php echo $group['group_name']; ?></span></h6>
                               <h6 style="color:gray;">Instructor : <span class="category"><?php echo $group['instructor']; ?></span></h6>
                                <?php
                             }elseif (empty($group['group_name'])&& $accountType =="7") {
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
                          <h4 style="color:gray;">Cancelled</h43>

                        </div>
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
                                   echo " | ".date('h:i:s A',strtotime($dateModified)); ?></span>
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
        <div id="sectionD" class="tab-pane fade">
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
																					 *
																					 from borrower_slip a
																					 where a.group_id = $grpID and a.status != 0 and a.status != 1 and a.status != 2 and a.status != 3 and a.status != 4
                                           and a.status != 8 and a.status != 9
																					 order by a.borrower_slip_id ASC";
										$reqIdQuerry = mysqli_query($dbcon,$masterQuery);
									 while ($requestID = mysqli_fetch_array($reqIdQuerry)) {
							 ?>
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
																					 <?php $query_pin = mysqli_query($dbcon,"SELECT * FROM pin where borrower_slip_id = ".$requestID['borrower_slip_id']);
																								 $pin = mysqli_fetch_array($query_pin); ?>
																					 <label class=" ">PIN : <?php echo $pin['pin_id']; ?></label>
																							<h4 class=" ">Request # <strong > <?php echo $requestID['borrower_slip_id']; ?></strong></h4>
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
                          <h6 style="color:gray;">Date used : <span class="category"><?php echo date('M d, Y',strtotime($requestID['date_use']));?> | <?php echo date('h:i:s A',strtotime($requestID['date_use']));?></span></h6>
                           <h6 style="color:gray;">Purpose : <span class="category"><?php echo $requestID['purpose'];?></span></h6>
                            <h6 style="color:gray;">Date Requested : <span class="category"><?php echo date('M d, Y',strtotime($requestID['date_requested']));?></span></h6>
                            <?php
                             $accountType = $_SESSION['account_type'];
                             $groupQuery = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = $id");
                             while ($group = mysqli_fetch_array($groupQuery)) {
                             if (!empty($group['group_name'])) {
                              ?>
                               <h6 style="color:gray;">Group Name : <span class="category"><?php echo $group['group_name']; ?></span></h6>
                               <h6 style="color:gray;">Instructor : <span class="category"><?php echo $group['instructor']; ?></span></h6>
                                <?php
                             }elseif (empty($group['group_name'])&& $accountType =="7") {
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
                          <?php if ($requestID['status']==5 ||$requestID['status']==7) {
                            ?>
                             <h4 style="color:orange;">Complete</h43>
                            <?php
                          }if ($requestID['status']==6) {
                            ?>
                            <h4 style="color:orange;">Incomplete</h43>
                            <?php
                          } ?>

                        </div>
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
                              <th>Remarks</th>
                            </tr>
                          </thead>
                          <tbody>

                              <?php
                              $rIDS = $requestID['borrower_slip_id'];
                              $reqDetails = "SELECT
                                                  a.borrower_slip_id as requestID,a.group_id,a.date_requested,a.storage_id,a.status,
                                                  b.borrower_slip_id,b.utensils_id,b.qty,b.remarks,
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
                              <?php
                                if ($value['remarks']==0) {
                                ?>
                                <td ><i class="fa fa-check text-success"></i> </td>
                                <?php
                                }if ($value['remarks']==1) {
                                ?>
                               <td><i class="fa fa-times text-danger"></i> </td>
                                <?php
                                }
                              ?>
                            </tr>
                            <?php
                          } ?>
                          </tbody>
                        </table>

                        </div>
                      </div>
                      <div class="col-md-12">
                        <?php if ($requestID['status']==5) {
                          ?>
                          <div class="footer">
                              <hr>
                               <div class="col-md-5">
                              <div class="stats">
                                     <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($requestID['date_received']));
                                     echo " | ".date('h:i:s A',strtotime($requestID['date_received'])); ?></span>
                                </div>
                                </div>
                                <div class="col-md-5">
                                        <div class="category">
                                          <?php
                                          $checkNames = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$requestID['received_by']."'");
                                          while ($receivdby = mysqli_fetch_array($checkNames)) {
                                            ?>
                                            <span>Received by : <?php echo $receivdby['lname']; ?> , <?php echo $receivdby['fname']; ?></span>
                                            <?php
                                          } ?>
                                       </div>
                                </div>
                          </div>
                          <?php
                        }if ($requestID['status']==6) {
                          ?>
                          <div class="footer">
                              <hr>
                               <div class="col-md-5">
                                 <?php $query_disc = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = '".$requestID['borrower_slip_id']."' order by bad_id limit 1");
                                 while ($disc = mysqli_fetch_array($query_disc)) {
                                   ?>
                              <div class="stats">
                                     <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($disc['date_reported']));
                                     echo " | ".date('h:i:s A',strtotime($disc['date_reported'])); ?></span>
                                </div>
                              <?php ?>
                                </div>
                                <div class="col-md-5">
                                  <?php
                                  $checkNames = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$disc['reported_by']."'");
                                  while ($receivdby = mysqli_fetch_array($checkNames)) {
                                    ?>
                                    <div class="category">
                                    <span>Checked by : <?php echo $receivdby['lname']; ?> , <?php echo $receivdby['fname']; ?></span>
                                    </div>
                                    <?php
                                  } } ?>
                                </div>
                          </div>
                          <?php
                        }if ($requestID['status']==7){
                          ?>
                          <div class="footer">
                              <hr>
                               <div class="col-md-5">
                                 <?php $query_disc = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = '".$requestID['borrower_slip_id']."' order by bad_id limit 1");
                                 while ($disc = mysqli_fetch_array($query_disc)) {
                                   ?>
                              <div class="stats">
                                     <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($disc['date_replaced']));
                                     echo " | ".date('h:i:s A',strtotime($disc['date_replaced'])); ?></span>
                                </div>
                              <?php ?>
                                </div>
                                <div class="col-md-5">
                                        <div class="category">
                                          <?php
                                          $checkNames = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$disc['approved_by']."'");
                                          while ($receivdby = mysqli_fetch_array($checkNames)) {
                                            ?>
                                            <span>Approved by : <?php echo $receivdby['lname']; ?> , <?php echo $receivdby['fname']; ?></span>
                                            <?php }
                                          } ?>
                                        </div>
                                </div>
                          </div>
                          <?php
                      }?>

                      </div>
                    </div>
                    </div>
                    </div>
                    <?php
                  }

                ?>
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
        <div id="sectionE" class="tab-pane fade">
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
                    $reqIdQuerry = mysqli_query($dbcon,"SELECT * FROM borrower_slip where  status = 6  or status = 7 and group_id = $grpID   order by borrower_slip_id desc");

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
																				 <div class="responsive">
																					 <div class="header">
																						<?php $query_pin = mysqli_query($dbcon,"SELECT * FROM pin where borrower_slip_id = ".$requestID['borrower_slip_id']);
																									$pin = mysqli_fetch_array($query_pin); ?>
																						<label class=" ">PIN : <?php echo $pin['pin_id']; ?></label>
																							 <h4 class=" ">Request # <strong > <?php echo $requestID['borrower_slip_id']; ?></strong></h4>
																					 </div>
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
                              <h6 style="color:gray;">Date used : <span class="category"><?php echo date('M d, Y',strtotime($requestID['date_use']));?> | <?php echo date('h:i:s A',strtotime($requestID['date_use']));?></span></h6>
                           <h6 style="color:gray;">Purpose : <span class="category"><?php echo $requestID['purpose'];?></span></h6>
                            <h6 style="color:gray;">Date Requested : <span class="category"><?php echo date('M d, Y',strtotime($requestID['date_requested']));?></span></h6>
                            <?php
                             $accountType = $_SESSION['account_type'];
                             $groupQuery = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = $id");
                             while ($group = mysqli_fetch_array($groupQuery)) {
                             if (!empty($group['group_name'])) {
                              ?>
                               <h6 style="color:gray;">Group Name : <span class="category"><?php echo $group['group_name']; ?></span></h6>
                               <h6 style="color:gray;">Instructor : <span class="category"><?php echo $group['instructor']; ?></span></h6>
                                <?php
                             }elseif (empty($group['group_name'])&& $accountType =="7") {
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
                          <?php if ($requestID['status']==6) {
                            ?>
                             <h4 style="color:red;">Discrepancies</h43>
                            <?php
                          }else {
                            ?>
                            <h4 style="color:green;">Cleared</h43>
                            <?php
                          } ?>


                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="content">
                        <h6 style="color:gray;">Requested items : </h6>
                        <table class="table">
                          <thead>
                            <tr>
                              <th>Item Name w/ Description</th>
                              <th>Category</th>
	                              <th>Requested QTY</th>
																<th>Lost QTY</th>
																<th>Damaged QTY</th>
                            </tr>
                          </thead>
                          <tbody>

                              <?php
                              $rIDS = $requestID['borrower_slip_id'];
                              $reqDetails = "SELECT
															a.bad_id,a.borrower_slip_id,a.utensils_id as utensilID,a.lost_qty,a.damaged_qty,a.note,
														 b.borrower_slip_id,b.utensils_id,b.qty,
														 c.utensils_id,c.utensils_name,c.utensils_cat_id,
														 d.utensils_cat_id,d.category

														 from breakages_and_damages a
														 left join borrower_slip_details b on a.utensils_id = b.utensils_id and a.borrower_slip_id = b.borrower_slip_id
														 left join utensils c on a.utensils_id = c.utensils_id
														 left join utensils_category d on c.utensils_cat_id = d.utensils_cat_id

														 where a.borrower_slip_id = $rIDS group by  a.bad_id";
                             $result = mysqli_query($dbcon,$reqDetails);
                               foreach ($result as $key => $value) {
                                ?>
                              <tr>
                              <td><?php echo $value['utensils_name']; ?></td>
                              <td><?php echo $value['category']; ?></td>
                              <td><?php echo $value['qty']; ?></td>
                              <td><?php echo $value['lost_qty']; ?></td>
                              <td><?php echo $value['damaged_qty']; ?></td>
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
                              <?php if ($requestID['status']==6) {
                                ?>
															<?php $date_reported = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = '".$requestID['borrower_slip_id']."'");
															      $report_row = mysqli_fetch_assoc($date_reported);
																		 ?>
																		 <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($report_row['date_reported']));
                                     echo " | ".date('h:i:s A',strtotime($report_row['date_reported'])); ?></span>
                                <?php }else {
                                  ?>
                                  <?php $date_reported = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = '".$requestID['borrower_slip_id']."'");
    															      $report_row = mysqli_fetch_assoc($date_reported);
    																		 ?>
    																		 <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($report_row['date_replaced']));
                                         echo " | ".date('h:i:s A',strtotime($report_row['date_replaced'])); ?></span>
                                  <?php
                                } ?>
                              </div>
														</div>
														<div class="col-md-5">
															<div class="category">
                                <?php if ($requestID['status']==6) {
                                  ?>
                                  <?php  $reported_by = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$report_row['reported_by']."'");
   																      $report = mysqli_fetch_assoc($reported_by);?>
   															 <span>Checked by : <?php echo $report['lname']; ?> , <?php echo $report['fname']; ?></span>
                                  <?php
                                }else {
                                  ?>
                                  <?php  $reported_by = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$report_row['approved_by']."'");
                                        $report = mysqli_fetch_assoc($reported_by);?>
                                 <span>Approved by : <?php echo $report['lname']; ?> , <?php echo $report['fname']; ?></span>
                                  <?php
                                } ?>

															</div>
														</div>
                              <!-- <div class="col-md-1">
                                <span>
                                <a href="#" type="button" name="button">Manage</a>
                               </span>
                              </div> -->
                              <div class="col-md-2">
                               <span>
                                 <?php if ($requestID['status']==6) {
                                   ?>
                                 <a href="temporary2.php?id=<?php echo $requestID['borrower_slip_id']; ?>" name="button">Print form</a>
                                   <?php
                                 }
                                   ?>


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
              ?>
               </div>
                </div>
          </div>
           </div>
        </div>
        <div id="sectionF" class="tab-pane fade">
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
																					 *
																					 from borrower_slip a
																					 where a.group_id = $grpID AND a.status = 9
																					 order by a.borrower_slip_id ASC";
										$reqIdQuerry = mysqli_query($dbcon,$masterQuery);
									 while ($requestID = mysqli_fetch_array($reqIdQuerry)) {
							 ?>
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
																					 <?php $query_pin = mysqli_query($dbcon,"SELECT * FROM pin where borrower_slip_id = ".$requestID['borrower_slip_id']);
																								 $pin = mysqli_fetch_array($query_pin); ?>
																					 <label class=" ">PIN : <?php echo $pin['pin_id']; ?></label>
																							<h4 class=" ">Request # <strong > <?php echo $requestID['borrower_slip_id']; ?></strong></h4>
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
                          <h6 style="color:gray;">Date used : <span class="category"><?php echo date('M d, Y',strtotime($requestID['date_use']));?> | <?php echo date('h:i:s A',strtotime($requestID['date_use']));?></span></h6>
                           <h6 style="color:gray;">Purpose : <span class="category"><?php echo $requestID['purpose'];?></span></h6>
                            <h6 style="color:gray;">Date Requested : <span class="category"><?php echo date('M d, Y',strtotime($requestID['date_requested']));?></span></h6>
                            <?php
                             $accountType = $_SESSION['account_type'];
                             $groupQuery = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = $id");
                             while ($group = mysqli_fetch_array($groupQuery)) {
                             if (!empty($group['group_name'])) {
                              ?>
                               <h6 style="color:gray;">Group Name : <span class="category"><?php echo $group['group_name']; ?></span></h6>
                               <h6 style="color:gray;">Instructor : <span class="category"><?php echo $group['instructor']; ?></span></h6>
                                <?php
                             }elseif (empty($group['group_name'])&& $accountType =="7") {
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
                            <h4 style="color:GRAY;">Denied</h43>
                        </div>
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
                              $rIDS = $requestID['borrower_slip_id'];
                              $reqDetails = "SELECT
                                                  a.borrower_slip_id as requestID,a.group_id,a.date_requested,a.storage_id,a.status,
                                                  b.borrower_slip_id,b.utensils_id,b.qty,b.remarks,
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
                                     <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($requestID['date_denied']));
                                     echo " | ".date('h:i:s A',strtotime($requestID['date_denied'])); ?></span>
                                </div>
                                </div>
                                <div class="col-md-5">
                                        <div class="category">
                                          <?php
                                          $checkNames = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$requestID['denied_by']."'");
                                          while ($receivdby = mysqli_fetch_array($checkNames)) {
                                            ?>
                                            <span>Denied by : <?php echo $receivdby['lname']; ?> , <?php echo $receivdby['fname']; ?></span>
                                            <?php
                                          } ?>
                                       </div>
                                </div>
                          </div>
                      </div>
                    </div>
                    </div>
                    </div>
                    <?php
                  }

                ?>
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
           </div>
        </div>
    </div>
</div>
<?php include('footer.php') ?>
