<?php include('header.php');
?>
<br><br>
<div class="content" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                  <div class="content">
                 <a href="reports.php"><i class="fa fa-chevron-left"></i> Back</a>
                  </div>

 <div class="content">
   <div class="card">
     <div class="row">
       <div class="col-md-12">
         <div class="content">
           <center>
        <img src="img/form_header.jpg"style='width:100%;' border="0" alt="Null">
         </center>
         <?php if ($_SESSION['default_report']==1) {
           ?>
       <div class="content">
        <table class="table table-bordered table-striped table-hover" align="center">
          <thead>
            <tr>
              <th colspan="2"></th>
              <th colspan="3"class="bg bg-info text-center">Releasing/Receiving</th>
              <th colspan="3"class="bg bg-success text-center">Discrepancies</th>
              <th></th>
           </tr>
            <tr>
              <th>Req#</th>
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
            <?php $storID = $_SESSION['user']['storage_id'];
                 if ($_SESSION['default_report_date']==1) {
                   $queryString = "SELECT * from borrower_slip a
                    where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.date_requested >= CURRENT_DATE()
                    order by a.borrower_slip_id desc";
                 }if ($_SESSION['default_report_date']==2) {
                   $queryString = "SELECT * from borrower_slip a
                    where a.storage_id = $storID and a.status != 1 and a.status !=3
                    order by a.borrower_slip_id desc";
                 }if ($_SESSION['default_report_date']==3) {
                   $time = '00:00:00';
                   $dat1 = $_SESSION['date1'];
                   $date1 = date('d-m-Y', strtotime("$dat1"));
                   $dat2 = $_SESSION['date2'];
                   $date1 = date('d-m-Y', strtotime("$dat2"));
                   $queryString = "SELECT * from borrower_slip a
                    where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.date_requested >= '$dat1' and a.date_requested <= '$dat2'
                    order by a.borrower_slip_id desc";
                 }
                  $result = mysqli_query($dbcon,$queryString);
                  foreach ($result as $key => $value) {
                 ?>
            <tr>
              <td><?php echo $value['borrower_slip_id']; ?></td>
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
                     ?>Cleared<?php
                   }else {
                     ?>Lacking<?php
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
          <table class="table table-bordered table-striped table-hover" align="center">
            <thead>
              <tr>
                <th>Req#</th>
                <th >Borrowed by</th>
                <th class="bg bg-warning text-center">Borrowed Items</th>
                <th class="bg bg-warning text-center">Released by</th>
                <th class="bg bg-info text-center">Returned Items</th>
                <th class="bg bg-info text-center">Received by</th>
                <th>Status</th>
                </tr>
            </thead>
            <tbody>
              <?php $storID = $_SESSION['user']['storage_id'];
                    if ($_SESSION['default_report_date']==1) {
                      $queryString = "SELECT * from borrower_slip a
                       where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.date_requested >= CURRENT_DATE()
                       order by a.borrower_slip_id desc";
                    }if ($_SESSION['default_report_date']==2) {
                      $queryString = "SELECT * from borrower_slip a
                       where a.storage_id = $storID and a.status != 1 and a.status !=3
                       order by a.borrower_slip_id desc";
                    }if ($_SESSION['default_report_date']==3) {
                      $dat1 = $_SESSION['date1'];
                      $dat2 = $_SESSION['date2'];
                      $queryString = "SELECT * from borrower_slip a
                       where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.date_requested >= '$dat1' and a.date_requested <= '$dat2'
                       order by a.borrower_slip_id desc";
                    }
                    $result = mysqli_query($dbcon,$queryString);
                    foreach ($result as $key => $value) {
                   ?>
              <tr >
                <td ><?php echo $value['borrower_slip_id']; ?></td>
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
                   <td><?php if ($value['status']==5) {
                     ?><i class="fa fa-check text-success"></i><?php
                   }else {
                     ?><i class="fa fa-exclamation text-danger"></i><?php
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
        <table class="table table-bordered table-striped table-hover" align="center">
          <thead>
            <tr>
              <th>Req#</th>
              <th >Borrowed by</th>
              <th class="bg bg-danger text-center">Lost Items</th>
              <th class="bg bg-danger text-center">Damaged Items</th>
              <th class="bg bg-danger text-center">Checked by</th>
              <th class="bg bg-success text-center">Note</th>
              <th class="bg bg-success text-center">Approved by</th>
              <th>Status</th>
              </tr>
          </thead>
          <tbody>
            <?php $storID = $_SESSION['user']['storage_id'];
                  if ($_SESSION['default_report_date']==1) {
                    $queryString = "SELECT * from borrower_slip a
                     where a.storage_id = $storID and a.status > 5 and a.date_requested >= CURRENT_DATE()
                     order by a.borrower_slip_id desc";
                  }if ($_SESSION['default_report_date']==2) {
                    $queryString = "SELECT * from borrower_slip a
                     where a.storage_id = $storID and a.status > 5
                     order by a.borrower_slip_id desc";
                  }if ($_SESSION['default_report_date']==3) {
                    $dat1 = $_SESSION['date1'];
                    $dat2 = $_SESSION['date2'];
                    $queryString = "SELECT * from borrower_slip a
                     where a.storage_id = $storID and a.status > 5 and a.date_requested >= '$dat1' and a.date_requested <= '$dat2'
                     order by a.borrower_slip_id desc";
                  }
                  $result = mysqli_query($dbcon,$queryString);
                  foreach ($result as $key => $value) {
                 ?>
            <tr >
              <td ><?php echo $value['borrower_slip_id']; ?></td>
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
                   ?>Cleared<?php
                 }else {
                   ?>Lacking<?php
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




        </div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php') ?>
