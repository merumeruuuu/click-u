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
<?php
if (isset($_GET['forward_to_dean'])) {
  $reqID = $_GET['forward_to_dean'];
  $checkStats = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $reqID");
  foreach ($checkStats as $key => $value);
  if ($value['status']==3) {
    echo "<script>alert('Failed! request has been cancelled!');</script>";
  }else {
    $checkStats2 = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $reqID");
    foreach ($checkStats2 as $key => $value2) {
      if ($value2['status']!=8) {
        $updateRequest = mysqli_query($dbcon,"UPDATE borrower_slip set status = 8 where borrower_slip_id = $reqID");
        //insert admin notification
        $insertNotifControl = mysqli_query($dbcon,"INSERT INTO notification_control (trans_id,notif_type_id)
        values('$reqID','7')");
        $fetchControl = mysqli_query($dbcon,"SELECT notif_control_id from notification_control order by notif_control_id desc limit 1");
        foreach ($fetchControl as $key => $control) {
          $fetchAdmin = mysqli_query($dbcon,"SELECT * from user_settings where account_type_id <=2 ");
          foreach ($fetchAdmin as $key => $user) {

        $insertNotif = mysqli_query($dbcon,"INSERT INTO notification (notif_control_id,user_id,user_notif_type,notif_date)
        values('".$control['notif_control_id']."','".$user['user_id']."','3',NOW())");
           }
        }
      }
    }
}
} ?>
<br><br>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <?php
              $_SESSION['user']['storage_id'];
              $storageID = $_SESSION['user']['storage_id'];
              $notif_transID = $_SESSION['notif_transId'];

              // $select = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $storageID");
              // $show = mysqli_fetch_array($select);

							$notif_ID = $_SESSION['notif_id'];

              $query = "SELECT
                     a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
                     b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_staff,b.seen_user,b.notif_count
                     from notification_control a
                     LEFT join notification b on a.notif_control_id = b.notif_control_id
                     where b.id = $notif_ID ";
               $results = mysqli_query($dbcon,$query);
							$notif = mysqli_fetch_array($results);
               ?>
             <div class="card">
                     <div class="header">
                       <div class="col-md-4">
                         <?php if ($_SESSION['account_type'] == 1 ||$_SESSION['account_type'] == 2) {
                           $fetchStatus = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = ".$notif['trans_id']);
                           foreach ($fetchStatus as $key => $value) {
                           if ($value['status']==8) {
                           ?><span> <a href="dean_borrow_requests.php"><i class="fa fa-chevron-left"></i> View list</a> </span><?php
                               }
                           }
                         } ?>
                         <?php if ($_SESSION['account_type'] == 3 ||$_SESSION['account_type'] == 4 || $_SESSION['account_type'] == 5) {

                         if ($notif['notif_type_id']==1) {
                         	?>
                         <span> <a href="borrow_requests.php"><i class="fa fa-chevron-left"></i> View list</a> </span>
													<?php
												}else {
													?>
                        <span> <a href="returnRequest.php"><i class="fa fa-chevron-left"></i> View list</a> </span>
													<?php
												}
                          ?>
                          <?php
                        }if ($_SESSION['account_type'] == 6 || $_SESSION['account_type'] == 7) {
													if ($notif['notif_type_id']==1 || $notif['notif_type_id']==8) {
													?>
                          <h4 class="title">My request for approval</h4>
													<?php
												}if($notif['notif_type_id']==2 ) {
													?>
                         <h4 class="title">My request for Returning</h4>
													<?php
												}if($notif['notif_type_id']==3 || $notif['notif_type_id']==4) {
                          ?>
                         <h4 class="title">My request with discrepancy</h4>
													<?php
                        }
                          ?>
                          </div>
                            </div>
														 <br><br>
                     <div class="content">
                       <div class="col-md-12">

                        <span> <a href="userRequestsMenu2.php"><i class="fa fa-chevron-left"></i> View list</a> </span>
                          <?php
                        } ?>
                       </div>
                     </div>

                    <div class="content">
                      <br>
                      <?php
                             $query = "SELECT
                                             a.borrower_slip_id as requestID,a.group_id as grpID,a.date_requested,a.added_by,a.date_modified,a.modified_by,a.purpose,a.date_denied,a.denied_by,
                                             a.date_requested,a.date_approved,a.date_received,a.received_by,a.storage_id,a.status,a.aprvd_n_rlsd_by,a.time_use,a.date_use,a.verified_by,a.date_verified,
                                             b.group_id,b.group_name,b.instructor,b.group_leader_id,
                                             c.group_id,c.user_id,c.added_by,
                                             d.user_id,d.school_id,d.fname,d.lname,
                                             e.user_id,e.account_type_id,
                                             f.storage_id,f.storage_name,
                                             g.borrower_slip_id,count(g.bsd_id),
                                             h.borrower_slip_id,h.reported_by,h.approved_by,h.date_reported,h.date_replaced


                                             from borrower_slip a
                                             left join group_table b on a.group_id = b.group_id
                                             left join group_members c on b.group_id = c.group_id
                                             left join users d on c.user_id = d.user_id
                                             left join user_settings e on d.user_id = e.user_id
                                             left join storage f on a.storage_id = f.storage_id
                                             left join borrower_slip_details g on a.borrower_slip_id = g.borrower_slip_id
                                             left join breakages_and_damages h on g.borrower_slip_id = h.borrower_slip_id

                                             where a.borrower_slip_id = $notif_transID";
                              $result = mysqli_query($dbcon,$query);
                              $rows = mysqli_fetch_array($result);
                             ?>

                                <div class="card">
                                    <div class="row">
                                       <div class="col-md-12">
                                          <div class="header">
																						<?php if ($_SESSION['account_type'] == 3 ||$_SESSION['account_type']==4 ||$_SESSION['account_type']==5 ||$_SESSION['account_type']==1||$_SESSION['account_type']==2) {
																							?>
                                            <h4 class=" ">Request # <strong style="color:gray;"> <?php echo  $rows['requestID'];?></strong></h4>
																							<?php
																						}if ($_SESSION['account_type'] == 6 ||$_SESSION['account_type']==7) {
																							?>
 																					 <?php $query_pin = mysqli_query($dbcon,"SELECT * FROM pin where borrower_slip_id = ".$rows['requestID']);
 																							 $pin = mysqli_fetch_array($query_pin); ?>
 																					 <label class=" ">PIN : <?php echo $pin['pin_id']; ?></label>
 																					 <h4 class=" ">Request # <strong > <?php echo $rows['requestID']; ?></strong></h4>

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
                            <h6 style="color:gray;">When to use : <span class="category"><?php echo date('M d, Y',strtotime($rows['date_use']));?> | <?php echo date('h:i:s A',strtotime($rows['date_use']));?></span></h6>
                            <h6 style="color:gray;">Purpose : <span class="category"><?php echo  $rows['purpose']; ?></span></h6>
                            <h6 style="color:gray;">Date Requested : <span class="category"><?php echo  date('M d, Y',strtotime($rows['date_requested'])); ?></span></h6>
                            <?php
                             $groupQuery = mysqli_query($dbcon,"SELECT * FROM group_table where group_id = ".$rows['grpID']);
                             $group = mysqli_fetch_array($groupQuery);
                             if (!empty($group['group_name'])) {
                              ?>
                               <h6 style="color:gray;">Group Name : <span class="category"><?php echo $group['group_name']; ?></span></h6>
                               <h6 style="color:gray;">Instructor : <span class="category"><?php echo $rows['instructor']; ?> </span></h6>
                                <?php
                             }elseif (empty($group['group_name'])&& $rows['account_type_id']=="7") {
                              ?>
                              <h6 style="color:gray;">Group Name : <span class="category"> n/a</span></h6>
                              <h6 style="color:gray;">Instructor : <span class="category"><?php echo $rows['instructor']; ?> </span></h6>
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
                      <div class="col-md-2">
                        <div class="">
                          <?php if ($_SESSION['account_type'] == 1 ||$_SESSION['account_type']== 2) {
                            if ($rows['status']==9) {
                              ?><h4 style="color:gray;">Denied</h43><?php
                            }if ($rows['status']==3) {
                              ?><h4 style="color:gray;">Cancelled</h43><?php
                            }if ($rows['status']==2||$rows['status']==5||$rows['status']==6||$rows['status']==7) {
                              ?><h4 style="color:gray;">Approved</h43><?php
                            }
                          } ?>
                          <?php
													if ($_SESSION['account_type'] == 3 ||$_SESSION['account_type']== 4 || $_SESSION['account_type']== 5) {

														if ($notif['notif_type_id']==1) {
															if ($notif['seen_staff']==1 && $rows['status']==0) {
																?>
                              <h4 style="color:gray;">Releasing..</h43>
																<?php
															}if ($rows['status']==2||$rows['status']==5||$rows['status']==6||$rows['status']==7) {
																?>
                                <h4 style="color:gray;">Released</h43>
																<?php
															}if ($rows['status']==3) {
																?>
                                  <h4 style="color:gray;">Cancelled</h43>
																<?php
															}
														?>
														<?php
													}if ($notif['notif_type_id']==2 ) {
														if ($rows['status']==5) {
															?>
                              <h4>Received</h4>
															<?php
														}if ($rows['status']==2)  {
															?>
                             <h4>To receive...</h4>
															<?php
														} if ($rows['status']==6) {
                              ?>
                             <h4>Incomplete</h4>
                              <?php
                            }
													?>
													<?php
													}
                            ?>
                            <?php
                          }if ($_SESSION['account_type']== 6 || $_SESSION['account_type']== 7) {
														if ($notif['notif_type_id']==1 && $notif['seen_user']!=0) {
															if ($notif['seen_user']==3 ) {
															?>
															<h4 style="color:green;">Approved</h43>
															<?php
														}else{
															?>
                               <h4 style="color:green;">Approved</h43>
															<?php
															}
														}if ($notif['notif_type_id']==2) {
															 if ($notif['seen_user']==2) {
															?>
                              <h4 style="color:green;">Returned</h43>
															<?php
														}
                          }if ($notif['notif_type_id']==3&&empty($notif['notif_approved_date'])) {
                            if ($notif['seen_user']==2) {
                           ?>
                           <h4 style="color:orange;">Incomplete</h43>
                           <?php
                         }
                       }if ($notif['notif_type_id']==4) {
                         ?>
                         <h4 style="color:green;">Settled</h43>
                         <?php
                       }if ($notif['notif_type_id']==8) {
                         ?>
                         <h4 style="color:gray;">Denied</h43>
                         <?php
                       }
                          } ?>

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
                              <th></th>
                            </tr>
                          </thead>
                          <tbody>
                              <?php
                              $rIDS = $rows['requestID'];
                              $reqDetails = "SELECT
                                                  a.borrower_slip_id as requestID,a.group_id,a.date_requested,a.storage_id,a.status,a.received_by,
                                                  b.borrower_slip_id,b.utensils_id,b.qty,b.on_use,b.remarks,
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
                              <?php
                              if ($notif['notif_type_id']==3) {

                               if ($value['remarks']==0) {
                                ?>
                                <td><i class="fa fa-check text-success"></i></td>
                                <?php
                              }else {
                                ?>
                                <td><i class="fa fa-times text-danger"></i></td>
                                <?php
                              }
                              }?>
                            </tr>
                            <?php
                          } ?>
                          </tbody>
                        </table>
                        </div>
                      </div>
                      <?php
                      if ($_SESSION['account_type'] == 1 ||$_SESSION['account_type']==2 ) {
                        ?>  <div class="col-md-12">
                            <div class="footer">
                                <hr>
                                 <div class="col-md-9">
                                <div class="stats">
                                       <i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($rows['date_requested']));
                                            echo " | ".date('H:i:s A',strtotime($rows['date_requested'])); ?></span>
                                  </div>
                                  </div>
                                  <?php if ($rows['status']==8) {
                                    ?>
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
                                    <?php
                                  } ?>
                                  </div>
                            </div><?php
                      } ?>
                      <?php
                      if ($_SESSION['account_type'] == 3 ||$_SESSION['account_type']==4 || $_SESSION['account_type']==5) {
                        //if staff
                        ?>
                        <div class="col-md-12">
                          <div class="footer">
														<?php if ($notif['notif_type_id']==1) {
														?>
														<hr>
														<?php if ($rows['status']<=1) {
														?>
                            <?php if (empty($rows['verified_by'])) {
                              ?> <div class="col-md-9">
                               <div class="stats">
                                     <i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($rows['date_requested']));
                                          echo " | ".date('H:i:s A',strtotime($rows['date_requested'])); ?></span>
                                </div>
                                </div>
                                <?php
                            }else {
                              ?>
                              <div class="col-md-6">
                              <div class="stats">
                                    <i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($rows['date_requested']));
                                         echo " | ".date('H:i:s A',strtotime($rows['date_requested'])); ?></span>
                               </div>
                               </div>
                               <div class="col-md-3">
                                 <div class="stats">
                                   <?php $fetchUser = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$rows['verified_by']);
                                   foreach ($fetchUser as $key => $value) {
                                     ?>
                                     Verified by: <span><?php  echo $value['lname'];?></span>
                                     <?php
                                   } ?>
                                  </div>
                               </div>
                              <?php
                            } ?>

                              <div class="col-md-3">
                                <?php
                                if (empty($rows['verified_by'])) {
                                  ?>  <span>
                                      <a href="?forward_to_dean=<?php echo $rows['requestID']; ?>"onclick="return confirm('Are you sure you want to forward this request to the dean?');">
                                        Send to dean
                                      </a>
                                    </span>|<?php
                                } ?>
                               <span>
                              <!-- <a href="server.php?action=approveRequest&id=<?php echo $rows['requestID']; ?>" class="btn btn-success btn-fill btn_requestDetails btn-sm" onclick="return confirm('Confirm approve!')">Approve<i class="fa fa-check"></i></a> -->
                               <a href="#" data-toggle="modal"data-id="<?php echo $rows['requestID']; ?>" class="click_pin">
                                 Release
                               </a>
                               </span>
                              </div>
															<?php
														}if ($rows['status']==2||$rows['status']==5||$rows['status']==6||$rows['status']==7) {
															?>
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
																	<span>Released by : <?php echo $row['lname'] ?> ,<?php echo $row['fname'] ?></span>
																</div>
																</div>
															<?php
														}if ($rows['status']==3) {
															?>
															<div class="col-md-5">
														 <div class="stats">
																		<i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($rows['date_modified']));
																				 echo " | ".date('H:i:s A',strtotime($rows['date_modified'])); ?></span>
															 </div>
															 </div>
															 <div class="col-md-5">
															<div class="stats">
																<?php
																	$staff = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$rows['modified_by']."'");
																	$row = mysqli_fetch_assoc($staff);
																 ?>
																	<span>Cancelled by : <?php echo $row['lname'] ?> ,<?php echo $row['fname'] ?></span>
																</div>
																</div>
															<?php
														} ?>
														<?php
													}if ($notif['notif_type_id']==2)  {
														?>
														<hr>
														<?php if ($rows['status']==2) {
														?>
														 <div class="col-md-5">
														<div class="stats">
																	 <i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($rows['date_approved']));
																				echo " | ".date('H:i:s A',strtotime($rows['date_approved'])); ?> (Approved)</span>
															</div>
															</div>

																<div class="col-md-5">
															 <div class="stats">
																 <?php

																	 $staff = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$rows['aprvd_n_rlsd_by']."'");
																	 $row = mysqli_fetch_assoc($staff);
																	?>
																	 <span>Released by : <?php echo $row['lname'] ?> ,<?php echo $row['fname'] ?></span>
																 </div>
															</div>
															<div class="col-md-2">
																	<div class="stats">
																 <span>
																<!-- <a href="server.php?action=receiveItems&id=<?php echo $rows['requestID'];?>"  onclick="return confirm('Confirm Receive!')">Receive </a> -->
																<a href="#" data-toggle="modal"data-id="<?php echo $rows['requestID']; ?>" class="click_pin_receiving">
 																 Receive
 															 </a>
																 </span>
																 |
																 <span>
															 <a href="server.php?action=manageRequest&id=<?php echo $rows['requestID'];?>"> Report </a>
																 </span>
															</div>
															</div>

															<?php
														}if ($rows['status']==5) {
															?>
																<div class="col-md-5">
															 <div class="stats">
																			<i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($rows['date_received']));
																					 echo " | ".date('h:i:s A',strtotime($rows['date_received'])); ?></span>
																 </div>
																 </div>
																 <div class="col-md-5">
																<div class="stats">
																	<?php
																		$staffd = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$rows['received_by']."'");
																		$rowd = mysqli_fetch_assoc($staffd);
																	 ?>
																		<span>Received by : <?php echo $rowd['lname'] ?> ,<?php echo $rowd['fname'] ?></span>
																	</div>
																	</div>
															<?php
														}if ($rows['status']==6) {
                              ?>
                              <div class="col-md-5">
                              <div class="stats">
                                     <i class="fa fa-clock-o"></i> <span><?php  echo  date('M d, Y',strtotime($rows['date_reported']));
                                          echo " | ".date('h:i:s A',strtotime($rows['date_reported'])); ?></span>
                                </div>
                                </div>
                                <div class="col-md-5">
                               <div class="stats">
                                 <?php
                                   $staffd = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$rows['reported_by']."'");
                                   $rowd = mysqli_fetch_assoc($staffd);
                                  ?>
                                   <span>Checked by : <?php echo $rowd['lname'] ?> ,<?php echo $rowd['fname'] ?></span>
                                 </div>
                                 </div>
                              <?php
                            } ?>
														<?php
													} ?>


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
                               if (!empty($rows['date_received']&&!empty($rows['received_by'])&& $notif['notif_type_id']==2)) {
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
															}if ($notif['notif_type_id']==1) {
																?>
																<div class="col-md-5">
																<div class="stats">
																			<i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($rows['date_approved']));
																					echo " | ".date('H:i:s A',strtotime($rows['date_approved'])); ?> </span>
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
                                <div class="col-md-2">
                                  <span>
																		<?php
                                    if (!empty($rows['date_received']&&!empty($rows['received_by'])&& $notif['notif_type_id']!=1)) {
                                    }else {

																		 if ($notif['seen_user']==2) {
																			?>
                                     <a href="server.php?return=<?php echo $rows['requestID'];?>" type="button"onclick="return confirm('Confirm return!')" name="button">Return</a>
																			<?php
                                        }
																		} ?>
                                 </span>
                                </div>
                                <?php
                              }if ($notif['notif_type_id']==3) {
                                $discQ = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id =".$rows['requestID']);
                                $dic = mysqli_fetch_array($discQ);
                                ?>
                                <div class="col-md-5">
                                <div class="stats">
                                      <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($dic['date_reported']));
                                          echo " | ".date('h:i:s A',strtotime($dic['date_reported'])); ?> </span>
                                 </div>
                                 </div>
                                 <div class="col-md-5">
                                         <div class="category">
                                           <?php

                                             $staff = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$dic['reported_by']."'");
                                             $row = mysqli_fetch_assoc($staff);
                                            ?>
                                             <span>Checked by : <?php echo $row['lname'] ?> ,<?php echo $row['fname'] ?></span>
                                         </div>
                                 </div>
                                <div class="col-md-2">
                                  <span>
                                    <?php
                                    if (!empty($dic['date_replaced'])) {
                                      ?>
                                     <a href="temporary2.php?id=<?php echo $rows['requestID'];?>" type="button" name="button">Print form</a>
                                      <?php
                                    } ?>
                                 </span>
                                </div>
                                <?php
                              }if ($notif['notif_type_id']==4) {
                                $discQ = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id =".$rows['requestID']);
                                $dic = mysqli_fetch_array($discQ);
                                ?>
                                <div class="col-md-5">
                                <div class="stats">
                                      <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($dic['date_replaced']));
                                          echo " | ".date('h:i:s A',strtotime($dic['date_replaced'])); ?> </span>
                                 </div>
                                 </div>
                                 <div class="col-md-5">
                                         <div class="category">
                                           <?php

                                             $staff = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$dic['approved_by']."'");
                                             $row = mysqli_fetch_assoc($staff);
                                            ?>
                                             <span>Approved by : <?php echo $row['lname'] ?> ,<?php echo $row['fname'] ?></span>
                                         </div>
                                 </div>
                                <div class="col-md-2">
                                  <span>

                                 </span>
                                </div>
                                <?php
                              }if ($notif['notif_type_id']==8) {
                                ?>
                                <div class="col-md-5">
                                <div class="stats">
                                      <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($rows['date_denied']));
                                          echo " | ".date('h:i:s A',strtotime($rows['date_denied'])); ?> </span>
                                 </div>
                                 </div>
                                 <div class="col-md-5">
                                         <div class="category">
                                           <?php

                                             $staff = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$rows['denied_by']."'");
                                             $row = mysqli_fetch_assoc($staff);
                                            ?>
                                             <span>Denied by : <?php echo $row['lname'] ?> ,<?php echo $row['fname'] ?></span>
                                         </div>
                                 </div>
                                <div class="col-md-2">
                                  <span>

                                 </span>
                                </div>
                                <?php
                              }
                               ?>
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


				<?php
				if (isset($_POST['approve_from_modal'])) {
				  $request = mysqli_real_escape_string($dbcon,$_POST['reqIDs']);
				  $pin = mysqli_real_escape_string($dbcon,$_POST['pin']);

				  $query_pin = mysqli_query($dbcon,"SELECT * FROM pin where borrower_slip_id = $request");
				  $check_pin = mysqli_fetch_array($query_pin);

				  if ($check_pin['pin_id']==$pin) {
				    $query = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = $request");
				    $checkx = mysqli_fetch_array($query);
            if ($checkx['status']==2) {
              echo "<script>alert('Failed! The request has been approved by the other staff!');window.location.href='borrow_requests.php';</script>";
            }else {
            if ($checkx['status']<2) {

				      $query1 = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $request");
				      while ($check = mysqli_fetch_array($query1)) {
				        $utensilID = $check['utensils_id'];
				        $storageID = $check['storage_id'];
				        $requestQty = $check['qty'];
				        $reservedtQty = $check['reserved_qty'];

				        $query2 = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = $utensilID and storage_id = $storageID");
				        while ($rows = mysqli_fetch_array($query2)) {
				          $storageQty = $rows['storage_qty'];

				          $deduct = $reservedtQty - $requestQty;
                  $storageOnuse = $rows['on_use'] + $requestQty;
                  $storageRsrv = $rows['reserved_qty'] - $requestQty;
				          $staff = $_SESSION['user']['user_id'];

				          $updateStatus = mysqli_query($dbcon,"UPDATE borrower_slip SET date_approved = NOW(),aprvd_n_rlsd_by = $staff, status = 2 where borrower_slip_id = $request");
				          $updateItemOnUse = mysqli_query($dbcon,"UPDATE borrower_slip_details SET on_use = $requestQty,reserved_qty = $deduct where borrower_slip_id = $request and utensils_id = $utensilID and storage_id = $storageID");
                  $updateStorage = mysqli_query($dbcon,"UPDATE storage_stocks set reserved_qty = $storageRsrv,on_use = $storageOnuse where utensils_id = $utensilID and storage_id = $storageID");

                echo "<script>alert('Released successfully !');window.location.href='view_notification.php';</script>";
				         }
                   //update notification
                   $checkMembers = mysqli_query($dbcon,"SELECT * FROM group_members where group_id = ".$checkx['group_id']);
                   foreach ($checkMembers as $key => $members) {
                   $checkControl = mysqli_query($dbcon,"SELECT * FROM notification_control where trans_id = $request and notif_type_id = 1 and storage_id = $storageID");
                   foreach ($checkControl as $key => $control);
                   $updateUserNotif = mysqli_query($dbcon,"UPDATE notification set notif_date = NOW(),seen_user = 1,notif_count = 0 where notif_control_id = '".$control['notif_control_id']."' and user_id = '".$members['user_id']."'");
                  }
               }

               //inser history
               $historyType = 1;
               $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
               values (NOW(),'$staff','$request','$storageID','$historyType')");
             }else {
                 echo "<script>alert('Failed! The request was cancelled by the user!');window.location.href='borrow_requests.php';</script>";
               }
             }

				  }else {
				    echo "<script>alert('Wrong PIN !');window.location.href='view_notification.php';</script>";
				  }
				}

				 ?>
	 <!-- Mini Modal -->
		<div class="modal fade modal-mini modal-primary" id="enterPin" data-backdrop="false">
				            <div class="modal-dialog">
				                <form class="" action="view_notification.php" method="post">
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
				                      <input type="submit" name="approve_from_modal"id="submit_btn" class="btn btn-success btn-fill btn-sm" value="Submit">
				                    </div>
				                </div>
				                </form>
				            </div>
				        </div>
	 <!--  End Modal -->

<!-- RECEIVING....... -->
		<?php
	  if (isset($_POST['receive_from_modal'])) {
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
					              $updateStatus1 = mysqli_query($dbcon,"UPDATE borrower_slip_details SET on_use =  $updateOnUse where utensils_id = $utensilID1 and storage_id = $storageID1 ");

                        $update_query = "UPDATE  notification
                                        LEFT JOIN notification_control
                                        ON      notification.notif_control_id = notification_control.notif_control_id
                                        SET     notification.seen_user = 1,notification.notif_count = 0,notification.notif_approved_date = NOW()
                                        WHERE  notification.user_notif_type = 1 and notification_control.trans_id = $receiveID and notification_control.notif_type_id = 2";
                       mysqli_query($dbcon, $update_query);
					            echo "<script>alert('Received successfully !');window.location.href='view_notification.php';</script>";
					            }
					         }
                   //inser history
                   $historyType = 3;
                   $insertHistory = mysqli_query($dbcon,"INSERT INTO history (date_added,user_id,trans_id,storage_id,history_type_id)
                   values (NOW(),'$staff1','$receiveID','$storageID1','$historyType')");
					            }
					        else {
					          echo "<script>alert('Wrong PIN !');window.location.href='view_notification.php';</script>";
					        }
					      }
		 ?>
			 <!-- Mini Modal -->
			<div class="modal fade modal-mini modal-primary" id="enterPinReceiving" data-backdrop="false">
					                  <div class="modal-dialog">
					                      <form class="" action="view_notification.php" method="post">
					                      <div class="modal-content">
					                          <div class="modal-header justify-content-center">
					                            <button type="button" class="close" data-dismiss="modal">&times;</button>
					                          </div>
					                          <div class="modal-body text-center">

					                              <div class="row">
					                                <div class="col-md-12">
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
					                                     <input type="text"class="form-control"id="reqIDs1" name="" disabled  style="text-align:center;"/>
					                                     <input type="hidden" name="reqIDs"id="reqIDsx1"  value="">
					                                     <br>
					                                  <label>Enter PIN : </label>
					                                     <input class="form-control"type="number" name="pin"required style="text-align:center;">
					                                  </div>
					                              </div>


					                          </div>
					                          <div class="modal-footer">
					                            <input type="submit" name="receive_from_modal"id="submit_btn" class="btn btn-success btn-fill btn-sm" value="Submit">
					                          </div>
					                      </div>
					                      </form>
					                  </div>
					              </div>
			<!--  End Modal -->

<script type="text/javascript">
$(".click_pin").click(function () {
		var ids = $(this).attr('data-id');
		$("#reqIDs").val( ids );
		$("#reqIDsx").val( ids );
		$('#enterPin').modal('show');
});

$(".click_pin_receiving").click(function () {
		var ids = $(this).attr('data-id');
		$("#reqIDs1").val( ids );
		$("#reqIDsx1").val( ids );
		$('#enterPinReceiving').modal('show');
});
</script>
<?php include('footer.php') ?>
