<?php include('header.php');
?>
<script type="text/javascript">


  function session_report_storage(value) {
          $.ajax({
              type: "POST",
              url: 'ajaxrequest/sessionReports_admin.php',
              data: 'reports_ses_storage=' + value,
              dataType: 'json',
              success: function (data) {
                if (data==1) {
                  // location.reload();
                  location.href = 'admin_view_reports.php';
                  setInterval( 1000);
                }
              }
          });
      }
//session reports
  function session_report(value) {
          $.ajax({
              type: "POST",
              url: 'ajaxrequest/sessionReports_admin.php',
              data: 'reports_ses=' + value,
              dataType: 'json',
              success: function (data) {
                if (data==1) {
                  // location.reload();
                  location.href = 'admin_view_reports.php';
                  setInterval( 1000);
                }
              }
          });
      }
  //report date
  function session_report_date(val) {
          $.ajax({
              type: "POST",
              url: 'ajaxrequest/sessionReports_admin.php',
              data: 'reports_ses_date=' + val,
              dataType: 'json',
              success: function (da) {
                if (da==1) {
                  // location.reload();
                  location.href = 'admin_view_reports.php';
                  setInterval( 1000);
                }
              }
          });
      }
</script>
<?php
if (isset($_GET['report'])) {
 $_SESSION['default_control'] = 0;
 $_SESSION['default_report_date'] = 1;
 $_SESSION['default_report'] = 1;
 $_SESSION['date1'] = '00/00/0000';
 $_SESSION['date2'] = '00/00/0000';
 $_SESSION['default_storage'] = 1010;
}
  if (isset($_GET['close_modal'])) {
   $_SESSION['default_control'] = 1;
   $_SESSION['default_report_date'] = 1;
  }
  $all_report = 1;
  $borrow_report = 2;
  $disc_report = 3;
  $all_report_date = 1;
  $borrow_report_date = 2;
  $disc_report_date = 3;
  $all_storage = 1010;
  $selected = "selected";
 ?>
<br><br>
<div class="content">
    <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
              <div class="card ">
                        <div class="header">
                        <h4 class="title"> Borrowing </h4>
                        <p class="category"> </p>
                          </div>
                    <div class="content">
                      <div class="row">
                        <div class="col-md-3">
                          <br>
                          <span><select class="form-control" name="reports_ses_storage"id="reports_ses_storage"onchange="session_report_storage(this.value)">
                            <option value="<?php echo $all_storage; ?>"<?php if ($_SESSION['default_storage'] == 1010) {  echo $selected;  } ?>>  All Storages</option>
                            <?php $storages = mysqli_query($dbcon,"SELECT * FROM storage ");
                                  foreach ($storages as $key => $stor) {
                                    ?>
                             <option value="<?php echo $stor['storage_id']; ?>"<?php if ($stor['storage_id'] == $_SESSION['default_storage'] ) { echo $selected; } ?>> <?php echo $stor['storage_name']; ?></option>
                                    <?php
                                  } ?>

                          </select></span>
                          </div>
                      <div class="col-md-3">
                        <br>
                        <span><select class="form-control" name="reports_ses"id="reports_ses"onchange="session_report(this.value)">
                          <option value="<?php echo $all_report; ?>"<?php if ($_SESSION['default_report'] == 1) {  echo $selected;  } ?>>  All Reports</option>
                          <option value="<?php echo $borrow_report; ?>"<?php if ($_SESSION['default_report'] == 2) { echo $selected; } ?>>  Borrowed Items</option>
                          <option value="<?php echo $disc_report; ?>"<?php if ($_SESSION['default_report'] == 3) { echo $selected; } ?>>  Damaged/Lost Items</option>
                        </select></span>
                        </div>
                          <div class="col-md-3">
                            <br>
                            <select class="form-control"  name="reports_ses_date"id="reports_ses_date"onchange="session_report_date(this.value)">
                              <option value="<?php echo $all_report_date; ?>"<?php if ($_SESSION['default_report_date'] == 1) {  echo $selected;  } ?>>  Today</option>
                              <option value="<?php echo $borrow_report_date; ?>"<?php if ($_SESSION['default_report_date'] == 2) { echo $selected; } ?>>  All Date</option>
                              <option value="<?php echo $disc_report_date; ?>"<?php if ($_SESSION['default_report_date'] == 3) { echo $selected; } ?> data-toggle="modal" data-target="#myModal1">  Custom Date</option>
                            </select>
                          </div>
                          <div class="col-md-3">
                            <br>
                            <a href="borrow_report.php" class="btn btn-fill btn-info">Generate Report</a>
                          </div>
                      </div>
                    </div>
                  <?php if ($_SESSION['default_report']==1) {
                    ?>
            <div class="content">
                 <table class="table table-bordered table-striped table-hover"id="ALL_REPORTS">
                   <thead>
                     <tr>
                       <th colspan="3"></th>
                       <th colspan="3"class="bg bg-info text-center">Releasing/Receiving</th>
                       <th colspan="3"class="bg bg-success text-center">Discrepancies</th>
                       <th colspan="1"></th>
                    </tr>
                     <tr>
                       <th>Req#</th>
                       <th>Loc</th>
                       <th >Borrowed by</th>
                       <th class="bg bg-warning text-center">Borrowed Items</th>
                       <th class="bg bg-warning text-center">Released by</th>
                       <th class="bg bg-warning text-center">Received by</th>
                       <th class="bg bg-danger text-center">Damaged/Lost Items</th>
                       <th class="bg bg-danger text-center">Checked by</th>
                       <th class="bg bg-danger text-center">Approved by</th>
                       <th>Status</th>
                       </tr>
                   </thead>
                   <tbody>
                     <?php $storID = $_SESSION['default_storage'];
                          if ($_SESSION['default_report_date']==1) {
                            if ($_SESSION['default_storage']==1010) {
                              $queryString = "SELECT * from borrower_slip a
                               where a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9 and a.date_requested >= CURRENT_DATE()
                               order by a.borrower_slip_id desc";
                            }else {
                              $queryString = "SELECT * from borrower_slip a
                               where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9 and a.date_requested >= CURRENT_DATE()
                               order by a.borrower_slip_id desc";
                            }
                          }if ($_SESSION['default_report_date']==2) {
                            if ($_SESSION['default_storage']==1010) {
                              $queryString = "SELECT * from borrower_slip a
                               where  a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9
                               order by a.borrower_slip_id desc";
                            }else {
                              $queryString = "SELECT * from borrower_slip a
                               where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9
                               order by a.borrower_slip_id desc";
                            }

                          }if ($_SESSION['default_report_date']==3) {
                            if ($_SESSION['default_storage']==1010) {
                              $dat1 = $_SESSION['date1'];
                              $dat2 = $_SESSION['date2'];
                              $queryString = "SELECT * from borrower_slip a
                               where  a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9 and a.date_requested >= '$dat1' and a.date_requested <= '$dat2'
                               order by a.borrower_slip_id desc";
                            }else {
                              $dat1 = $_SESSION['date1'];
                              $dat2 = $_SESSION['date2'];
                              $queryString = "SELECT * from borrower_slip a
                               where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.status != 9 and a.status != 0 and a.date_requested >= '$dat1' and a.date_requested <= '$dat2'
                               order by a.borrower_slip_id desc";
                            }
                          }
                           $result = mysqli_query($dbcon,$queryString);
                           foreach ($result as $key => $value) {
                          ?>
                     <tr>
                       <td><?php echo $value['borrower_slip_id']; ?></td>
                       <?php $location = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = '".$value['storage_id']."'");
                       foreach ($location as $key => $loc) {
                         ?>
                         <td><?php echo $loc['initials']; ?></td>
                         <?php
                       }?>
                       <td>
                       <?php $group = mysqli_query($dbcon,"SELECT * FROM group_members where group_id = ".$value['group_id']);
                              foreach ($group as $key => $uid) {
                                $names = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$uid['user_id']);
                                foreach ($names as $key => $name) {

                                ?>
                         <span>- <?php echo $name['lname']; ?> , <?php echo $name['fname']; ?> <br> </span>
                                <?php
                               }
                              } ?>
                        <span style="font-size:10px;color:gray;"><?php  echo  date('M d, Y',strtotime($value['date_requested']));
                             echo " | ".date('h:i:s A',strtotime($value['date_requested'])); ?></span>
                        </td>
                        <td>
                        <?php $itemQuery = "SELECT
                        a.borrower_slip_id,a.utensils_id,a.qty,
                        b.utensils_id,b.utensils_name

                        from borrower_slip_details a
                        left join utensils b on a.utensils_id = b.utensils_id
                        where a.borrower_slip_id = '".$value['borrower_slip_id']."'";
                        $resItem = mysqli_query($dbcon,$itemQuery);
                        foreach ($resItem as $key => $item) {
                      ?>
                      <span> <?php echo $item['qty']; ?> - <?php echo $item['utensils_name']; ?> <br> </span>
                      <?php
                        } ?>
                        </td>
                        <td>
                        <?php
                                 $rleased = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$value['aprvd_n_rlsd_by']);
                                 foreach ($rleased as $key => $rel) {

                                 ?>
                          <span> <?php echo $rel['lname']; ?><br> </span>
                                 <?php
                               } ?>
                         <span style="font-size:10px;color:gray;"><?php  echo  date('M d, Y',strtotime($value['date_approved']));
                              echo " | ".date('h:i:s A',strtotime($value['date_approved'])); ?></span>
                         </td>
                         <td>
                         <?php
                                  $received = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$value['received_by']."' and '".$value['received_by']."'!='To receive..'");
                                  foreach ($received as $key => $rel) {
                                  ?>
                           <span> <?php echo $rel['lname']; ?><br> </span>
                           <span style="font-size:10px;color:gray;"><?php  echo  date('M d, Y',strtotime($value['date_received']));
                                echo " | ".date('h:i:s A',strtotime($value['date_received'])); ?></span>
                                  <?php
                                } ?>

                          </td>
                          <td>
                          <?php $discQuery = "SELECT
                          a.borrower_slip_id,a.utensils_id,a.lost_qty,a.damaged_qty,
                          b.utensils_id,b.utensils_name

                          from breakages_and_damages a
                          left join utensils b on a.utensils_id = b.utensils_id
                          where a.borrower_slip_id = '".$value['borrower_slip_id']."'";
                          $resItems = mysqli_query($dbcon,$discQuery);
                          foreach ($resItems as $key => $items) {
                        ?>
                        <span> <?php if ($items['lost_qty']==0 && $items['damaged_qty']>0) {
                          echo $items['damaged_qty'];
                        }if ($items['damaged_qty']==0 && $items['lost_qty']>0) {
                          echo $items['lost_qty'];
                        } ?> - <?php echo $items['utensils_name']; ?> <br> </span>
                        <?php
                          } ?>
                          </td>
                          <td>
                          <?php
                                  $breakgs = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = '".$value['borrower_slip_id']."' group by reported_by");
                                  foreach ($breakgs as $key => $rep) {
                                   $checkedBy = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$rep['reported_by']."'");
                                   foreach ($checkedBy as $key => $chkd) {

                                   ?>
                            <span> <?php echo $chkd['lname']; ?>  <br> </span>
                            <span style="font-size:10px;color:gray;"><?php  echo  date('M d, Y',strtotime($rep['date_reported']));
                                 echo " | ".date('h:i:s A',strtotime($rep['date_reported'])); ?></span>
                                   <?php
                                 }
                                 } ?>

                           </td>
                           <td>
                           <?php
                                   $breakgsx = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = '".$value['borrower_slip_id']."'group by approved_by");
                                   foreach ($breakgsx as $key => $aprv) {
                                    $aprvdBy = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$aprv['approved_by']."'");
                                    foreach ($aprvdBy as $key => $apr) {

                                    ?>
                             <span> <?php echo $apr['lname']; ?> <br> </span>
                             <span style="font-size:10px;color:gray;"><?php  echo  date('M d, Y',strtotime($aprv['date_replaced']));
                                  echo " | ".date('h:i:s A',strtotime($aprv['date_replaced'])); ?></span>
                                    <?php
                                  }
                                  } ?>

                            </td>
                            <td><?php if ($value['status']==5||$value['status']==7) {
                              ?><i class="fa fa-check text-success"> </i> cleared<?php
                            }if ($value['status']==2) {
                              ?><i class="fa fa-arrow-right text-info"> </i> on use<?php
                            }if($value['status']==6) {
                              ?><i class="fa fa-question text-danger"> </i> lacking<?php
                            } ?></td>
                     </tr>
                   <?php } ?>
                   </tbody>
                 </table>
               </div>
                  <?php
                  }if ($_SESSION['default_report']==2) {
                  ?>
                  <div class="content">
                   <table class="table table-bordered table-striped table-hover" id="BORROWED_ITEMS">
                     <thead>
                       <tr>
                         <th>Req#</th>
                         <th>Loc</th>
                         <th >Borrowed by</th>
                         <th class="bg bg-warning text-center">Borrowed Items</th>
                         <th class="bg bg-warning text-center">Released by</th>
                         <th class="bg bg-info text-center">Returned Items</th>
                         <th class="bg bg-info text-center">Received by</th>
                         <th>Status</th>
                         </tr>
                     </thead>
                     <tbody>
                       <?php $storID = $_SESSION['default_storage'];
                             if ($_SESSION['default_report_date']==1) {
                               if ($_SESSION['default_storage']==1010) {
                                 $queryString = "SELECT * from borrower_slip a
                                  where  a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9 and a.date_requested >= CURRENT_DATE()
                                  order by a.borrower_slip_id desc";
                               }else {
                                 $queryString = "SELECT * from borrower_slip a
                                  where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9 and a.date_requested >= CURRENT_DATE()
                                  order by a.borrower_slip_id desc";
                               }
                             }if ($_SESSION['default_report_date']==2) {
                               if ($_SESSION['default_storage']==1010) {
                                 $queryString = "SELECT * from borrower_slip a
                                  where   a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9
                                  order by a.borrower_slip_id desc";
                               }else {
                                 $queryString = "SELECT * from borrower_slip a
                                  where  a.storage_id = $storID and a.status != 1  and a.status !=3 and a.status != 0 and a.status != 9
                                  order by a.borrower_slip_id desc";
                               }
                             }if ($_SESSION['default_report_date']==3) {
                               if ($_SESSION['default_storage']==1010) {
                                 $dat1 = $_SESSION['date1'];
                                 $dat2 = $_SESSION['date2'];
                                 $queryString = "SELECT * from borrower_slip a
                                  where   a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9 and a.date_requested >= '$dat1' and a.date_requested <= '$dat2'
                                  order by a.borrower_slip_id desc";
                               }else {
                                 $dat1 = $_SESSION['date1'];
                                 $dat2 = $_SESSION['date2'];
                                 $queryString = "SELECT * from borrower_slip a
                                  where  a.storage_id = $storID and a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9 and a.date_requested >= '$dat1' and a.date_requested <= '$dat2'
                                  order by a.borrower_slip_id desc";
                               }

                             }
                             $result = mysqli_query($dbcon,$queryString);
                             foreach ($result as $key => $value) {
                            ?>
                       <tr >
                         <td ><?php echo $value['borrower_slip_id']; ?></td>
                         <?php $location = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = '".$value['storage_id']."'");
                         foreach ($location as $key => $loc) {
                           ?>
                           <td><?php echo $loc['initials']; ?></td>
                         <?php } ?>
                         <td>
                         <?php $group = mysqli_query($dbcon,"SELECT * FROM group_members where group_id = ".$value['group_id']);
                                foreach ($group as $key => $uid) {
                                  $names = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$uid['user_id']);
                                  foreach ($names as $key => $name) {

                                  ?>
                           <span>- <?php echo $name['lname']; ?> , <?php echo $name['fname']; ?> <br> </span>
                                  <?php
                                 }
                                } ?>
                          <span style="font-size:10px;color:gray;"><?php  echo  date('M d, Y',strtotime($value['date_requested']));
                               echo " | ".date('h:i:s A',strtotime($value['date_requested'])); ?></span>
                          </td>
                          <td >
                          <?php $itemQuery = "SELECT
                          a.borrower_slip_id,a.utensils_id,a.qty,
                          b.utensils_id,b.utensils_name

                          from borrower_slip_details a
                          left join utensils b on a.utensils_id = b.utensils_id
                          where a.borrower_slip_id = '".$value['borrower_slip_id']."'";
                          $resItem = mysqli_query($dbcon,$itemQuery);
                          foreach ($resItem as $key => $item) {
                        ?>
                        <span> <?php echo $item['qty']; ?> - <?php echo $item['utensils_name']; ?> <br> </span>
                        <?php
                          } ?>
                          </td>
                          <td class="text-center">
                          <?php
                                   $rleased = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$value['aprvd_n_rlsd_by']);
                                   foreach ($rleased as $key => $rel) {

                                   ?>
                            <span> <?php echo $rel['lname']; ?><br> </span>
                                   <?php
                                 } ?>
                           <span style="font-size:10px;color:gray;"><?php  echo  date('M d, Y',strtotime($value['date_approved']));
                                echo " | ".date('h:i:s A',strtotime($value['date_approved'])); ?></span>
                           </td>
                           <td >
                           <?php $itemQuery = "SELECT
                           a.borrower_slip_id,a.utensils_id,a.qty,a.returned,
                           b.utensils_id,b.utensils_name

                           from borrower_slip_details a
                           left join utensils b on a.utensils_id = b.utensils_id
                           where a.borrower_slip_id = '".$value['borrower_slip_id']."' and returned > 0";
                           $resItem = mysqli_query($dbcon,$itemQuery);
                           foreach ($resItem as $key => $item) {
                         ?>
                         <span> <?php echo $item['returned']; ?> - <?php echo $item['utensils_name']; ?> <br> </span>
                         <?php
                           } ?>
                           </td>
                           <td class="text-center">
                           <?php
                                    $received = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$value['received_by']."' and '".$value['received_by']."'!='To receive..'");
                                    foreach ($received as $key => $rel) {
                                    ?>
                             <span> <?php echo $rel['lname']; ?><br> </span>
                             <span style="font-size:10px;color:gray;"><?php  echo  date('M d, Y',strtotime($value['date_received']));
                                  echo " | ".date('h:i:s A',strtotime($value['date_received'])); ?></span>
                                    <?php
                                  } ?>

                            </td>
                            <td><?php if ($value['status']==5||$value['status']==7) {
                              ?><i class="fa fa-check text-success"> </i> cleared<?php
                            }if ($value['status']==2) {
                            ?>
                              <i class="fa fa-arrow-right text-info"> </i> on use
                            <?php
                          }if($value['status']==6) {
                              ?><i class="fa fa-question text-danger"></i> incomplete<?php
                            } ?></td>
                       </tr>
                     <?php } ?>
                     </tbody>
                   </table>
                 </div>
                  <?php
                }if ($_SESSION['default_report']==3) {
                ?>
                <div class="content">
                 <table class="table table-bordered table-striped table-hover" align="center" id="DISCREPANCIES">
                   <thead>
                     <tr>
                       <th>Req#</th>
                       <th>Loc</th>
                       <th >Borrowed by</th>
                       <th class="bg bg-danger text-center">Lost Items</th>
                       <th class="bg bg-danger text-center">Damaged Items</th>
                       <th class="bg bg-danger text-center">Checked by</th>
                       <th class="bg bg-danger text-center">Note</th>
                       <th class="bg bg-success text-center">Approved by</th>
                       <th>Status</th>
                       </tr>
                   </thead>
                   <tbody>
                     <?php $storID = $_SESSION['default_storage'];
                           if ($_SESSION['default_report_date']==1) {
                             if ($_SESSION['default_storage']==1010) {
                               $queryString = "SELECT * from borrower_slip a
                                where  a.status > 5 and a.status != 9 and a.date_requested >= CURRENT_DATE()
                                order by a.borrower_slip_id desc";
                             }else {
                               $queryString = "SELECT * from borrower_slip a
                                where a.storage_id = $storID and a.status > 5 and a.status != 9 and a.date_requested >= CURRENT_DATE()
                                order by a.borrower_slip_id desc";
                             }
                           }if ($_SESSION['default_report_date']==2) {
                             if ($_SESSION['default_storage']==1010) {
                               $queryString = "SELECT * from borrower_slip a
                                where a.status > 5 and a.status != 9
                                order by a.borrower_slip_id desc";
                             }else {
                               $queryString = "SELECT * from borrower_slip a
                                where a.storage_id = $storID and a.status > 5 and a.status != 9
                                order by a.borrower_slip_id desc";
                             }

                           }if ($_SESSION['default_report_date']==3) {
                             if ($_SESSION['default_storage']==1010) {
                               $dat1 = $_SESSION['date1'];
                               $dat2 = $_SESSION['date2'];
                               $queryString = "SELECT * from borrower_slip a
                                where a.status > 5 and a.status != 9 and a.date_requested >= '$dat1' and a.date_requested <= '$dat2'
                                order by a.borrower_slip_id desc";
                             }else {
                               $dat1 = $_SESSION['date1'];
                               $dat2 = $_SESSION['date2'];
                               $queryString = "SELECT * from borrower_slip a
                                where a.storage_id = $storID and a.status > 5 and a.status != 9 and a.date_requested >= '$dat1' and a.date_requested <= '$dat2'
                                order by a.borrower_slip_id desc";
                             }

                           }
                           $result = mysqli_query($dbcon,$queryString);
                           foreach ($result as $key => $value) {
                          ?>
                     <tr >
                       <td ><?php echo $value['borrower_slip_id']; ?></td>
                       <?php $location = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = '".$value['storage_id']."'");
                       foreach ($location as $key => $loc) {
                         ?>
                         <td><?php echo $loc['initials']; ?></td>
                       <?php } ?>
                       <td>
                       <?php $group = mysqli_query($dbcon,"SELECT * FROM group_members where group_id = ".$value['group_id']);
                              foreach ($group as $key => $uid) {
                                $names = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$uid['user_id']);
                                foreach ($names as $key => $name) {

                                ?>
                         <span>- <?php echo $name['lname']; ?> , <?php echo $name['fname']; ?> <br> </span>
                                <?php
                               }
                              } ?>
                        <span style="font-size:10px;color:gray;"><?php  echo  date('M d, Y',strtotime($value['date_requested']));
                             echo " | ".date('h:i:s A',strtotime($value['date_requested'])); ?></span>
                        </td>
                        <td>
                        <?php $discQuery = "SELECT
                        a.borrower_slip_id,a.utensils_id,a.lost_qty,a.damaged_qty,
                        b.utensils_id,b.utensils_name

                        from breakages_and_damages a
                        left join utensils b on a.utensils_id = b.utensils_id
                        where a.borrower_slip_id = '".$value['borrower_slip_id']."' and a.damaged_qty = 0";
                        $resItems = mysqli_query($dbcon,$discQuery);
                        foreach ($resItems as $key => $items) {
                      ?>
                      <span><?php  echo $items['lost_qty']; ?> - <?php  echo $items['utensils_name']; ?> <br> </span>
                      <?php
                        } ?>
                        </td>
                        <td>
                        <?php $discQuery = "SELECT
                        a.borrower_slip_id,a.utensils_id,a.lost_qty,a.damaged_qty,
                        b.utensils_id,b.utensils_name

                        from breakages_and_damages a
                        left join utensils b on a.utensils_id = b.utensils_id
                        where a.borrower_slip_id = '".$value['borrower_slip_id']."' and a.lost_qty = 0";
                        $resItems = mysqli_query($dbcon,$discQuery);
                        foreach ($resItems as $key => $items) {
                      ?>
                      <span><?php  echo $items['damaged_qty']; ?> - <?php  echo $items['utensils_name']; ?> <br> </span>
                      <?php
                        } ?>
                        </td>
                        <td>
                        <?php
                                $breakgsx = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = '".$value['borrower_slip_id']."'group by reported_by");
                                foreach ($breakgsx as $key => $aprv) {
                                 $aprvdBy = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$aprv['reported_by']."'");
                                 foreach ($aprvdBy as $key => $apr) {

                                 ?>
                          <span> <?php echo $apr['lname']; ?> <br> </span>
                          <span style="font-size:10px;color:gray;"><?php  echo  date('M d, Y',strtotime($aprv['date_reported']));
                               echo " | ".date('h:i:s A',strtotime($aprv['date_reported'])); ?></span>
                                 <?php
                               }
                               } ?>
                         </td>
                         <td>
                         <?php $discQuery = "SELECT
                         a.borrower_slip_id,a.utensils_id,a.lost_qty,a.damaged_qty,a.note,
                         b.utensils_id,b.utensils_name

                         from breakages_and_damages a
                         left join utensils b on a.utensils_id = b.utensils_id
                         where a.borrower_slip_id = '".$value['borrower_slip_id']."' ";
                         $resItems = mysqli_query($dbcon,$discQuery);
                         foreach ($resItems as $key => $items) {
                       ?>
                       <span><?php  echo $items['note']; ?> <br> </span>
                       <?php
                         } ?>
                         </td>
                         <td>
                         <?php
                                 $breakgsx = mysqli_query($dbcon,"SELECT * FROM breakages_and_damages where borrower_slip_id = '".$value['borrower_slip_id']."'group by approved_by");
                                 foreach ($breakgsx as $key => $aprv) {
                                  $aprvdBy = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$aprv['approved_by']."'");
                                  foreach ($aprvdBy as $key => $apr) {
                                  ?>
                           <span> <?php echo $apr['lname']; ?> <br> </span>
                           <span style="font-size:10px;color:gray;"><?php  echo  date('M d, Y',strtotime($aprv['date_replaced']));
                                echo " | ".date('h:i:s A',strtotime($aprv['date_replaced'])); ?></span>
                                  <?php
                                }
                                } ?>
                          </td>
                          <td><?php if ($value['status']==7) {
                              ?><i class="fa fa-check text-success"> </i> cleared<?php
                          }if ($value['status']==6) {
                              ?><i class="fa fa-question text-danger"> </i> lacking<?php
                          } ?></td>
                     </tr>
                   <?php } ?>
                   </tbody>
                 </table>
               </div>
                <?php
                } ?>
              </div>
          </div>
        </div>
    </div>
</div>

<!-- Mini Modal -->
<div class="modal <?php if ($_SESSION['default_report_date']==3&&$_SESSION['default_control']== 0) {
  echo "show";
 }if($_SESSION['default_report_date']==3&&$_SESSION['default_control']== 1) {
  echo "fade";
 } ?>  modal-primary" id="myModal1" data-backdrop="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
              <span >Select custom dates</span>
              <a href="admin_view_reports.php?close_modal" class="close" data-dismiss="modal">&times;</a>
            </div>
            <form class="" action="admin_view_reports.php" method="post">
            <div class="modal-body ">
              <div class="content">
                  <div class="pull-center">
                  <label for="">From :</label>
                  <input type="date"class="form-control" name="date1" value=""required>
                  <br>
                  <label for="">To :</label>
                  <input type="date"class="form-control" name="date2" value=""required>
                </div>
            </div>
       </div>
            <div class="modal-footer">
                <button type="submit" name="confirm_date"class="btn btn-sm btn-info btn-fill">Confirm</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!--  End Modal -->

<?php include('dataTables2.php'); ?>
<script type="text/javascript">
$('#ALL_REPORTS').DataTable( {
 "pageLength": 50
 } );
 $('#BORROWED_ITEMS').DataTable( {
  "pageLength": 50
  } );
  $('#DISCREPANCIES').DataTable( {
   "pageLength": 50
   } );
</script>
<?php include('footer.php') ?>
