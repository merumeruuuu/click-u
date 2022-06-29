<?php require('server.php');  ?>
        <!doctype html>
        <html lang="en">
        <head>
        	<meta charset="utf-8" />
        	<link rel="icon" type="image/png" href="assets/img/favicon.ico">
        	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

        	<title>ClickU</title>
        	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
            <meta name="viewport" content="width=device-width" />

            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
          <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
          <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>


          <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
         <script src="http://code.jquery.com/jquery-1.8.2.js"></script>
          <script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>

          <script src="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"></script>
        </head>
        <style media="screen">
        @media print {
      #printPageButton {
        display: none;
      }
    }
        </style>
        <script type="text/javascript">
      function printpage() {
     //Get the print button and put it into a variable
     var printButton1 = document.getElementById("printPageButton1");
     var printButton = document.getElementById("printPageButton");
     //Set the print button visibility to 'hidden'
     printButton1.style.visibility = 'hidden';
     printButton.style.visibility = 'hidden';
     //Print the page content
     window.print()
     printButton1.style.visibility = 'visible';
     printButton.style.visibility = 'visible';
 }
        </script>
        <body>
          <section class="container">
            <div class="">
              <div class="col-md-4">
                <div class="">
                  <br>
                  <a href="reports.php"id="printPageButton1"class="btn btn-info btn-fill bt-sm" name="button">Back</a>
                   <button onclick="printpage()"id="printPageButton"class="btn btn-warning btn-fill bt-sm" name="button">Print Report</button>
                </div>
              </div>
            </div>
            <div class="content"id="nodeToRenderAsPDF">
              <div class="">
                  <img src="img/report_logo.jpg"style='width:100%;' border="0" alt="Null">
                  <br>
                  <div class="row">
                    <div class="col-md-8">
                      <h5>Borrow Report</h5>
                      <h5> Date retrieved : <?php echo date('m-d-Y'); ?> </h5>
                    </div>
                    <?php if ($_SESSION['default_report']==1) {
                      ?>
                  <div class="content">
                   <table class="table table-bordered table-striped table-hover" align="center"id="ALL_UTENSILS">
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
                               where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9 and a.date_requested >= CURRENT_DATE()
                               order by a.borrower_slip_id desc";
                            }if ($_SESSION['default_report_date']==2) {
                              $queryString = "SELECT * from borrower_slip a
                               where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9
                               order by a.borrower_slip_id desc";
                            }if ($_SESSION['default_report_date']==3) {
                              $time = '00:00:00';
                              $dat1 = $_SESSION['date1'];
                              $dat2 = $_SESSION['date2'];
                              $queryString = "SELECT * from borrower_slip a
                               where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9 and a.date_requested >= '$dat1' and a.date_requested <= '$dat2'
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
                     <table class="table table-bordered table-striped table-hover" align="center"id="ALL_UTENSILS">
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
                                  where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9 and a.date_requested >= CURRENT_DATE()
                                  order by a.borrower_slip_id desc";
                               }if ($_SESSION['default_report_date']==2) {
                                 $queryString = "SELECT * from borrower_slip a
                                  where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9
                                  order by a.borrower_slip_id desc";
                               }if ($_SESSION['default_report_date']==3) {
                                 $dat1 = $_SESSION['date1'];
                                 $dat2 = $_SESSION['date2'];
                                 $queryString = "SELECT * from borrower_slip a
                                  where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.status != 0 and a.status != 9 and a.date_requested >= '$dat1' and a.date_requested <= '$dat2'
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
                              <td><?php if ($value['status']==5||$value['status']==7) {
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
                   <table class="table table-bordered table-striped table-hover" align="center" id="ALL_UTENSILS">
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
                                where a.storage_id = $storID and a.status > 5 and a.status != 9 and a.date_requested >= CURRENT_DATE()
                                order by a.borrower_slip_id desc";
                             }if ($_SESSION['default_report_date']==2) {
                               $queryString = "SELECT * from borrower_slip a
                                where a.storage_id = $storID and a.status > 5 and a.status != 9
                                order by a.borrower_slip_id desc";
                             }if ($_SESSION['default_report_date']==3) {
                               $dat1 = $_SESSION['date1'];
                               $dat2 = $_SESSION['date2'];
                               $queryString = "SELECT * from borrower_slip a
                                where a.storage_id = $storID and a.status > 5 and a.status != 9 and a.date_requested >= '$dat1' and a.date_requested <= '$dat2'
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
              <table class="table ">
                <tr>
                  <div class="text-center">
                   <p >Any deformation, loses or damages of the above item/s are my sole responsibility as Head/In-charge of this department.</p>
                  </div>
                </tr>
                <tr class="text-center">
                 <td>
                 <h5>Checked by :</h5>
                  <h4>John Carlo M. Arellano</h4>
                 </td>
                  <td>
                   <h5>Checked by :</h5>
                   <h4>Jessua Jugalbot</h4>
                   </td>
                </tr>
              </table>
                 <table class="table table-bordered">
                   <tr class="text-center">
                      <td>
                        <h4>Ms. Sheila Mae C. Pogoy</h4>
                        <h5>Inventory In-charge</h5>
                       </td>
                      <td>
                      <h4>MS. DARYL F. LEGARDE</h4>
                      <h5>Property Custodian</h5>
                    </td>
                      <td>
                        <h4>DR. GRAYFIELD BAJAO</h4>
                        <h5>Department Head/In-charge</h5>
                      </td>
                   </tr>
                 </table>
            </div>
          </section>
          <script type="text/javascript">
          </script>


          </div>
          </div>


          </body>
          <!--   Core JS Files   -->
          <script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
          <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>

          <!--  Charts Plugin -->
          <script src="assets/js/chartist.min.js"></script>

          <!--  Notifications Plugin    -->
          <script src="assets/js/bootstrap-notify.js"></script>


          <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
          <script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>

          <!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
          <script src="assets/js/demo.js"></script>

          <!-- <script type="text/javascript">
          // $(document).ready(function(){
          //
          //   demo.initChartist();
          //
          //   $.notify({
          //       icon: 'pe-7s-satisfied',
          //       message: "Welcome to <b>ClickU</b> - UCLM Kitchen Utensil Online Borrowing."
          //
          //     },{
          //         type: 'info',
          //         timer: 4000
          //     });
          //
          // });
          </script> -->

          </html>
