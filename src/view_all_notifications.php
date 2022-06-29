<?php include('header.php');
?>
<?php
if (isset($_GET['remove_notif'])) {
  $id = $_GET['remove_notif'];
  $removeNotif = mysqli_query($dbcon,"DELETE FROM notification where id = $id");
} ?>
<?php
if (isset($_GET['clear_notif'])) {
  $user = $_SESSION['user']['user_id'];
  $removeNotifs = mysqli_query($dbcon,"DELETE FROM notification where user_id = $user");
} ?>
<br><br>
<div class="content">
    <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
              <div class="card ">
                <div class="row">
                  <div class="col-md-6">
                    <div class="header">
                        <h4 class="title">Your Notifications</h4>
                        <p class="category"> </p>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="content pull-right">
                      <a href="?clear_notif">Clear notifications</a>
                    </div>
                  </div>
                </div>
                  <div class="content">
                      <div class="table-full-width">
                          <table class="table table-hover">
                              <tbody>
                                <?php
                                  $userID = $_SESSION['user']['user_id'];
                                //if admin
                                   if ($_SESSION['account_type']==1 ||$_SESSION['account_type']==2 ) {
                                 $query = "SELECT
                                        a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
                                        b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_admin,b.notif_count
                                        from notification_control a
                                        LEFT join notification b on a.notif_control_id = b.notif_control_id
                                        where  b.user_id = $userID group by b.notif_control_id order by b.id desc";
                                  $results = mysqli_query($dbcon,$query);
                                 // $notification = mysqli_query($dbcon,"SELECT * FROM notification where storage_id = $storageID group by trans_id desc");
                           while ($row = mysqli_fetch_array($results)) {
                             $fetchPurpose = mysqli_query($dbcon,"SELECT * FROM borrower_slip where borrower_slip_id = ".$row['trans_id']);
                             foreach ($fetchPurpose as $key => $value) {
                                  ?>
                                  <?php if ($row['notif_type_id'] == 7) {
                                    if ($row['seen_admin']==0 ) {
                                      ?>
                                      <tr class="active">
                                          <td>
                                          <i class="fa fa-arrow-up text-success"></i>
                                          </td>
                                          <td> <a href="get_notification.php?id=<?php echo $row['id']; ?>"style="color:black;"> <strong> Request for <?php echo $value['purpose']; ?> (Request # <?php echo $row['trans_id'] ?>) on <?php echo  date('M d, Y',strtotime($row['notif_date']));
                                          echo " | ".date('h:i:s A',strtotime($row['notif_date'])); ?></a></strong></td>
                                          <td class="td-actions text-right">
                                          <td class="td-actions text-right">
                                              <!-- <button type="button" rel="tooltip" title="Hide" class="btn btn-info btn-simple btn-xs">
                                                  <i class="fa fa-eye-slash"></i>
                                              </button> -->
                                              <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                                  <i class="fa fa-times"></i>
                                              </a>
                                          </td>
                                      </tr>
                                      <?php
                                    }else {
                                      ?>
                                      <tr >
                                          <td>
                                          <i class="fa fa-arrow-up text-success"></i>
                                          </td>
                                            <td> <a href="get_notification.php?staff_seen_approve=<?php echo $row['id']; ?>"style="color:gray;"> Request for <?php echo $value['purpose']; ?> (Request # <?php echo $row['trans_id'] ?>) on <?php echo  date('M d, Y',strtotime($row['notif_date']));
                                            echo " | ".date('h:i:s A',strtotime($row['notif_date'])); ?></a></td>
                                          <td class="td-actions text-right">
                                          <td class="td-actions text-right">
                                            <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                                <i class="fa fa-times"></i>
                                            </a>
                                          </td>
                                      </tr>
                                      <?php
                                    }
                                  ?>

                                <?php }?>

                            <?php }
                            }
                          }?>
                                <?php
                                  $storageID = $_SESSION['user']['storage_id'];
                                  $userID = $_SESSION['user']['user_id'];

                                //if staff
                                   if ($_SESSION['account_type']==3 ||$_SESSION['account_type']==4 || $_SESSION['account_type']==5) {
                                 $query = "SELECT
                                        a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
                                        b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_staff,b.notif_count
                                        from notification_control a
                                        LEFT join notification b on a.notif_control_id = b.notif_control_id
                                        where a.storage_id = $storageID and b.user_id = $userID group by b.notif_control_id order by b.id desc";
                                  $results = mysqli_query($dbcon,$query);
                                 // $notification = mysqli_query($dbcon,"SELECT * FROM notification where storage_id = $storageID group by trans_id desc");
                           while ($row = mysqli_fetch_array($results)) {
                                  ?>
                                  <?php if ($row['notif_type_id'] == 1) {
                                    if ($row['seen_staff']==0 ) {
                                      ?>
                                      <tr class="active">
                                          <td>
                                          <i class="fa fa-arrow-up text-success"></i>
                                          </td>
                                          <td> <a href="get_notification.php?id=<?php echo $row['id']; ?>"style="color:black;"> <strong> Request for releasing (Request # <?php echo $row['trans_id'] ?>) on <?php echo  date('M d, Y',strtotime($row['notif_date']));
                                          echo " | ".date('h:i:s A',strtotime($row['notif_date'])); ?></a></strong></td>
                                          <td class="td-actions text-right">
                                          <td class="td-actions text-right">
                                              <!-- <button type="button" rel="tooltip" title="Hide" class="btn btn-info btn-simple btn-xs">
                                                  <i class="fa fa-eye-slash"></i>
                                              </button> -->
                                              <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                                  <i class="fa fa-times"></i>
                                              </a>
                                          </td>
                                      </tr>
                                      <?php
                                    }else {
                                      ?>
                                      <tr >
                                          <td>
                                          <i class="fa fa-arrow-up text-success"></i>
                                          </td>
                                            <td> <a href="get_notification.php?staff_seen_approve=<?php echo $row['id']; ?>"style="color:gray;"> Request for releasing (Request # <?php echo $row['trans_id'] ?>) on <?php echo  date('M d, Y',strtotime($row['notif_date']));
                                            echo " | ".date('h:i:s A',strtotime($row['notif_date'])); ?></a></td>
                                          <td class="td-actions text-right">
                                          <td class="td-actions text-right">
                                            <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                                <i class="fa fa-times"></i>
                                            </a>
                                          </td>
                                      </tr>
                                      <?php
                                    }
                                  ?>

                                <?php }else {

                                  if ($row['seen_staff']==0) {
                                    ?>
                                    <tr class="active">
                                        <td>
                                        <i class="fa fa-arrow-down text-info"></i>
                                        </td>
                                        <td> <a href="get_notification.php?unseen_receive_staff=<?php echo $row['id'] ?>"style="color:black;"> <strong> Request for receiving (Request # <?php echo $row['trans_id'] ?>) on <?php echo  date('M d, Y',strtotime($row['notif_date']));
                                        echo " | ".date('h:i:s A',strtotime($row['notif_date'])); ?></a></strong></td>
                                        <td class="td-actions text-right">
                                        <td class="td-actions text-right">
                                          <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                              <i class="fa fa-times"></i>
                                          </a>
                                        </td>
                                    </tr>
                                    <?php
                                  }else {
                                  ?>
                                  <tr>
                                      <td>
                                      <i class="fa fa-arrow-down text-info"></i>
                                      </td>
                                      <td> <a href="get_notification.php?seen_received_staff=<?php echo $row['id'] ?>"style="color:gray;"> Request for receiving (Request # <?php echo $row['trans_id'] ?>) on <?php echo  date('M d, Y',strtotime($row['notif_date']));
                                      echo " | ".date('h:i:s A',strtotime($row['notif_date'])); ?></a></td>
                                      <td class="td-actions text-right">
                                      <td class="td-actions text-right">
                                        <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                            <i class="fa fa-times"></i>
                                        </a>
                                      </td>
                                  </tr>
                                  <?php
                                }
                              } ?>

                            <?php }
                          }?>
                          <?php
                          //if user notification ////////////////////////////////////////////////////////////////////////
                             if ($_SESSION['account_type']==6 || $_SESSION['account_type']==7) {

                           $userID = $_SESSION['user']['user_id'];
                           $query = "SELECT
                                  a.notif_control_id,a.trans_id,a.notif_type_id,a.storage_id,
                                  b.id,b.notif_control_id,b.user_id,b.notif_date,b.notif_approved_date,b.seen_user,b.notif_count
                                  from notification_control a
                                  LEFT join notification b on a.notif_control_id = b.notif_control_id
                                  where b.user_id = $userID group by b.notif_control_id order by b.id desc";
                            $notification = mysqli_query($dbcon,$query);
                     while ($row = mysqli_fetch_array($notification)) {
                            ?>
                            <?php if (!empty($row['notif_approved_date']) && $row['notif_type_id'] == 1)  {
                              if ($row['seen_user']==1) {
                                ?>
                                <tr class="active">
                                    <td>
                                    <i class="fa fa-arrow-up text-success"></i>
                                    </td>
                                    <td> <a href="get_notification.php?userApproveId=<?php echo $row['id']; ?>"style="color:black;"> <strong> Your request (Request # <?php echo $row['trans_id'] ?>)  has been approved ! on <?php echo  date('M d, Y',strtotime($row['notif_date']));
                                    echo " | ".date('h:i:s A',strtotime($row['notif_date'])); ?></a></strong></td>
                                    <td class="td-actions text-right">
                                    <td class="td-actions text-right">
                                      <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                          <i class="fa fa-times"></i>
                                      </a>
                                    </td>
                                </tr>
                                <?php
                              }if ($row['seen_user']== 2  || $row['seen_user'] ==3 ||  $row['seen_user'] == 4) {
                                ?>
                                <tr >
                                    <td>
                                    <i class="fa fa-arrow-up text-success"></i>
                                    </td>
                                      <td> <a href="get_notification.php?seenUser=<?php echo $row['id']; ?>"style="color:gray;">Your request (Request # <?php echo $row['trans_id'] ?>) has been approved ! on <?php echo  date('M d, Y',strtotime($row['notif_date']));
                                      echo " | ".date('h:i:s A',strtotime($row['notif_date'])); ?></a></td>
                                    <td class="td-actions text-right">
                                    <td class="td-actions text-right">
                                      <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                          <i class="fa fa-times"></i>
                                      </a>
                                    </td>
                                </tr>
                                <?php
                              }
                            ?>

                          <?php }if (!empty($row['notif_approved_date']) && $row['notif_type_id'] == 2) {

                           if ($row['seen_user']== 1) {
                              ?>
                              <tr class="active">
                                  <td>
                                  <i class="fa fa-arrow-down text-info"></i>
                                  </td>
                                  <td> <a href="get_notification.php?unseen_return_user=<?php echo $row['id'] ?>"style="color:black;"> <strong> Items from Request # <?php echo $row['trans_id'] ?> has been returned! on <?php echo  date('M d, Y',strtotime($row['notif_date']));
                                  echo " | ".date('h:i:s A',strtotime($row['notif_date'])); ?></a></strong></td>
                                  <td class="td-actions text-right">
                                  <td class="td-actions text-right">
                                    <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                        <i class="fa fa-times"></i>
                                    </a>
                                  </td>
                              </tr>
                              <?php
                            }if ($row['seen_user']== 2) {
                            ?>
                            <tr>
                                <td>
                                <i class="fa fa-arrow-down text-info"></i>
                                </td>
                                <td> <a href="get_notification.php?seen_return_user=<?php echo $row['id'] ?>"style="color:gray;"> Items from Request # <?php echo $row['trans_id'] ?> has been returned! on <?php echo  date('M d, Y',strtotime($row['notif_date']));
                                echo " | ".date('h:i:s A',strtotime($row['notif_date'])); ?></a></td>
                                <td class="td-actions text-right">
                                <td class="td-actions text-right">
                                  <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                      <i class="fa fa-times"></i>
                                  </a>
                                </td>
                            </tr>
                            <?php
                          }
                        }if (!empty($row['notif_date']) && $row['notif_type_id'] == 3)  {
                          if ($row['seen_user']==1) {
                            ?>
                            <tr class="active">
                                <td>
                                <i class="fa fa-exclamation text-danger"></i>
                                </td>
                                <td> <a href="get_notification.php?userSettleId=<?php echo $row['id']; ?>"style="color:black;"> <strong> Please settle your request (Request # <?php echo $row['trans_id'] ?>) - on hold for having discrepancies ! on <?php echo  date('M d, Y',strtotime($row['notif_date']));
                                echo " | ".date('h:i:s A',strtotime($row['notif_date'])); ?></a></strong></td>
                                <td class="td-actions text-right">
                                <td class="td-actions text-right">
                                  <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                      <i class="fa fa-times"></i>
                                  </a>
                                </td>
                            </tr>
                            <?php
                          }if ($row['seen_user']== 2  || $row['seen_user'] ==3 ||  $row['seen_user'] == 4) {
                            ?>
                            <tr >
                                <td>
                                <i class="fa fa-exclamation text-danger"></i>
                                </td>
                                  <td> <a href="get_notification.php?seenuserSettleId=<?php echo $row['id']; ?>"style="color:gray;">Please settle your request (Request # <?php echo $row['trans_id'] ?>) - on hold for having discrepancies ! on <?php echo  date('M d, Y',strtotime($row['notif_date']));
                                  echo " | ".date('h:i:s A',strtotime($row['notif_date'])); ?></a></td>
                                <td class="td-actions text-right">
                                <td class="td-actions text-right">
                                  <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                      <i class="fa fa-times"></i>
                                  </a>
                                </td>
                            </tr>
                            <?php
                          }
                        ?>

                      <?php }if ($row['notif_type_id'] == 4) {
                        if ($row['seen_user']==1) {
                          ?>
                          <tr class="active">
                              <td>
                              <i class="fa fa-check text-success"></i>
                              </td>
                              <td> <a href="get_notification.php?userSettleId=<?php echo $row['id']; ?>"style="color:black;"> <strong> Your request (Request # <?php echo $row['trans_id'] ?>) has been settled ! on <?php echo  date('M d, Y',strtotime($row['notif_approved_date']));
                              echo " | ".date('h:i:s A',strtotime($row['notif_approved_date'])); ?></a></strong></td>
                              <td class="td-actions text-right">
                              <td class="td-actions text-right">
                                <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                    <i class="fa fa-times"></i>
                                </a>
                              </td>
                          </tr>
                          <?php
                        }if ($row['seen_user']== 2  || $row['seen_user'] ==3 ||  $row['seen_user'] == 4) {
                          ?>
                          <tr >
                              <td>
                              <i class="fa fa-check text-success"></i>
                              </td>
                                <td> <a href="get_notification.php?seenuserSettleId=<?php echo $row['id']; ?>"style="color:gray;">Your request (Request # <?php echo $row['trans_id'] ?>) has been settled ! on <?php echo  date('M d, Y',strtotime($row['notif_approved_date']));
                                echo " | ".date('h:i:s A',strtotime($row['notif_approved_date'])); ?></a></td>
                              <td class="td-actions text-right">
                              <td class="td-actions text-right">
                                <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                    <i class="fa fa-times"></i>
                                </a>
                              </td>
                          </tr>
                        <?php }
                      } ?>

                    <?php if ($row['notif_type_id'] == 8) {
                      if ($row['seen_user']==1) {
                        ?>
                        <tr class="active">
                            <td>
                            <i class="fa fa-ban text-danger"></i>
                            </td>
                            <td> <a href="get_notification.php?userSettleId=<?php echo $row['id']; ?>"style="color:black;"> <strong> Your request (Request # <?php echo $row['trans_id'] ?>) has been denied ! on <?php echo  date('M d, Y',strtotime($row['notif_date']));
                            echo " | ".date('h:i:s A',strtotime($row['notif_date'])); ?></a></strong></td>
                            <td class="td-actions text-right">
                            <td class="td-actions text-right">
                              <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                  <i class="fa fa-times"></i>
                              </a>
                            </td>
                        </tr>
                        <?php
                      }if ($row['seen_user']== 2  || $row['seen_user'] ==3 ||  $row['seen_user'] == 4) {
                        ?>
                        <tr >
                            <td>
                            <i class="fa fa-ban text-danger"></i>
                            </td>
                              <td> <a href="get_notification.php?seenuserSettleId=<?php echo $row['id']; ?>"style="color:gray;">Your request (Request # <?php echo $row['trans_id'] ?>) has been denied ! on <?php echo  date('M d, Y',strtotime($row['notif_date']));
                              echo " | ".date('h:i:s A',strtotime($row['notif_date'])); ?></a></td>
                            <td class="td-actions text-right">
                            <td class="td-actions text-right">
                              <a href="?remove_notif=<?php echo $row['id']; ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                  <i class="fa fa-times"></i>
                              </a>
                            </td>
                        </tr>
                      <?php }
                    } ?>

                      <?php }
                    }?>
                              </tbody>
                          </table>
                      </div>

                      <div class="footer">
                          <hr>
                          <div class="stats">
                              <i class="fa fa-history"></i>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
    </div>
</div>

<?php include('footer.php') ?>
