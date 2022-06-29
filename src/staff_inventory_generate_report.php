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
        <?php $storageID = $_SESSION['user']['storage_id'];
        $stID = $_SESSION['user']['storage_id'];?>
        <body>
          <section class="container">
            <div class="">
              <div class="col-md-4">
                <div class="">
                  <br>
                    <a href="staff_inventory_report.php"id="printPageButton1"class="btn btn-info btn-fill bt-sm" name="button">Back</a>
                   <button onclick="printpage()"id="printPageButton"class="btn btn-warning btn-fill bt-sm" name="button">Print Report</button>
                </div>
              </div>
            </div>
            <div class="content"id="nodeToRenderAsPDF">
              <div class="card">
                  <img src="img/report_logo.jpg"style='width:100%;' border="0" alt="Null">
                  <br>
                  <div class="row">
                    <div class="col-md-8">
                      <h5>Incident Report</h5>
                      <h5> Date retrieved : <?php echo date('m-d-Y'); ?> </h5>
                    </div>
                  </div>
              </div>
              <div class="content">
                <?php
                  if ($_SESSION['default_report_inventory']==1) {  //if by storage
                    if ($_SESSION['default_inventory_date']==1) {      /// by storage current date
                      ?>
                      <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Orig-qty</th>
                            <th>cur-qty</th>
                            <th>Items</th>
                            <th>Category</th>
                            <th>Model</th>
                            <th>Serial</th>
                            <th>Date purchased</th>
                            <th>Unit cost</th>
                            <th>Remarks</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $fetchInventoryMasterS = mysqli_query($dbcon,"SELECT * FROM inventory where date_added = CURRENT_DATE()");
                          foreach ($fetchInventoryMasterS as $key => $value);
                           $queryString = "SELECT *
                                        from utensils a
                                        left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                                        left join umsr c on a.umsr = c.id
                                        left join inventory_storage d on a.utensils_id = d.utensils_id
                                        where d.storage_id = $stID and a.status !=0 and d.original_stock != 0 and d.inventory_control_id = '".$value['inventory_control_id']."'
                                        group by d.utensils_id
                                        ";
                            $result = mysqli_query($dbcon,$queryString);
                            foreach ($result as $key => $invS) {
                          ?>
                          <tr>
                            <td class="bg bg-info"><?php echo $invS['utensils_id'] ?></td>
                            <?php
                              $currentStock = $invS['stock_remain'] + $invS['reserved_qty'] ;
                            ?>
                            <td class="bg bg-primary"><?php echo $invS['original_stock']; ?></td>
                            <td class="bg bg-primary"><?php echo $currentStock; ?></td>
                            <td class="bg bg-info"><?php echo $invS['utensils_name'] ?></td>
                            <td class="bg bg-info"><?php echo $invS['category'] ?></td>
                            <td class="bg bg-info"><?php echo $invS['model'] ?></td>
                            <td class="bg bg-info"><?php echo $invS['serial_no'] ?></td>
                            <td class="bg bg-info"><?php echo $invS['date_purchased'] ?></td>
                            <td class="bg bg-info">₱ <?php echo number_format($invS['cost'],2); ?></td>
                            <?php if ($invS['original_stock']== $currentStock){
                             ?> <td class="bg bg-success"><i class="fa fa-check text-success"></i> Complete </td> <?php
                           }else{
                             ?>
                             <td class="bg bg-danger">
                               <?php if ($invS['lost_qty']>0) {?>(<?php echo $invS['lost_qty'];?>) - Missing <br> <?php } ?>
                               <?php if ($invS['damaged_qty']>0) {?>(<?php echo $invS['damaged_qty'];?>) - Damaged <br><?php } ?>
                               <?php if ($invS['on_use']>0) {?> <b class="bg text-info"> (<?php echo $invS['on_use'];?>) - On use </b><br><?php } ?>
                             </td>
                             <?php
                           }
                             ?>
                          </tr>
                        <?php
                      }?>
                        </tbody>
                      </table>

                      <?php
                    } // end of by storage current date
                    if ($_SESSION['default_inventory_date']==2) {
                      ?>
                      <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Orig-qty</th>
                            <th>cur-qty</th>
                            <th>Items</th>
                            <th>Category</th>
                            <th>Model</th>
                            <th>Serial</th>
                            <th>Date purchased</th>
                            <th>Unit cost</th>
                            <th>Remarks</th>
                          </tr>
                        </thead>
                        <?php $fetchInventoryMasterS = mysqli_query($dbcon,"SELECT * FROM inventory order by inventory_control_id desc");
                        foreach ($fetchInventoryMasterS as $key => $value){  ?>
                          <thead>
                            <tr>
                              <th colspan="10"class="text-center"><?php echo $value['date_added']; ?></th>
                            </tr>
                          </thead>
                        <tbody>
                          <?php
                           $queryString = "SELECT *
                                        from utensils a
                                        left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                                        left join umsr c on a.umsr = c.id
                                        left join inventory_storage d on a.utensils_id = d.utensils_id
                                        where d.storage_id = $stID and a.status !=0
                                        and d.original_stock != 0 and d.inventory_control_id = '".$value['inventory_control_id']."'
                                        order by d.utensils_id
                                        ";
                            $result = mysqli_query($dbcon,$queryString);
                            foreach ($result as $key => $invS) {
                              $currentStock = $invS['stock_remain'] + $invS['reserved_qty'];
                          ?>
                          <tr>
                            <td class="bg bg-info"><?php echo $invS['utensils_id'] ?></td>
                            <td class="bg bg-primary"><?php echo $invS['original_stock']; ?></td>
                            <td class="bg bg-primary"><?php echo $currentStock; ?></td>
                            <td class="bg bg-info"><?php echo $invS['utensils_name'] ?></td>
                            <td class="bg bg-info"><?php echo $invS['category'] ?></td>
                            <td class="bg bg-info"><?php echo $invS['model'] ?></td>
                            <td class="bg bg-info"><?php echo $invS['serial_no'] ?></td>
                            <td class="bg bg-info"><?php echo $invS['date_purchased'] ?></td>
                            <td class="bg bg-info">₱ <?php echo number_format($invS['cost'],2); ?></td>
                            <?php if ($invS['original_stock']== $currentStock){
                             ?> <td class="bg bg-success"><i class="fa fa-check text-success"></i> Complete </td> <?php
                           }else{
                             ?>
                             <td class="bg bg-danger">
                               <?php if ($invS['lost_qty']>0) {?>(<?php echo $invS['lost_qty'];?>) - Missing <br> <?php } ?>
                               <?php if ($invS['damaged_qty']>0) {?>(<?php echo $invS['damaged_qty'];?>) - Damaged <br><?php } ?>
                               <?php if ($invS['on_use']>0) {?> <b class="bg text-info"> (<?php echo $invS['on_use'];?>) - On use </b><br><?php } ?>
                             </td>
                             <?php
                           }
                             ?>
                          </tr>
                        <?php
                      }?>
                        </tbody>
                      <?php } ?>
                      </table>
                      <?php
                    }
                    if ($_SESSION['default_inventory_date']==3) {
                      ?>
                      <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Orig-qty</th>
                            <th>cur-qty</th>
                            <th>Items</th>
                            <th>Category</th>
                            <th>Model</th>
                            <th>Serial</th>
                            <th>Date purchased</th>
                            <th>Unit cost</th>
                            <th>Remarks</th>
                          </tr>
                        </thead>
                        <?php
                        $date1 = $_SESSION['in_report_date1'];
                        $date2 = $_SESSION['in_report_date2'];
                        $fetchInventoryMasterS = mysqli_query($dbcon,"SELECT * FROM inventory where date_added >= '$date1' and date_added <= '$date2'");
                        foreach ($fetchInventoryMasterS as $key => $value){  ?>
                          <thead>
                            <tr>
                              <th colspan="10"class="text-center"><?php echo $value['date_added']; ?></th>
                            </tr>
                          </thead>
                        <tbody>
                          <?php
                           $queryString = "SELECT *
                                        from utensils a
                                        left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                                        left join umsr c on a.umsr = c.id
                                        left join inventory_storage d on a.utensils_id = d.utensils_id
                                        where d.storage_id = $stID and a.status !=0
                                        and d.original_stock != 0 and d.inventory_control_id = '".$value['inventory_control_id']."'
                                        order by d.utensils_id
                                        ";
                            $result = mysqli_query($dbcon,$queryString);
                            foreach ($result as $key => $invS) {
                              $currentStock = $invS['stock_remain'] + $invS['reserved_qty'];
                          ?>
                          <tr>
                            <td class="bg bg-info"><?php echo $invS['utensils_id'] ?></td>
                            <td class="bg bg-primary"><?php echo $invS['original_stock']; ?></td>
                            <td class="bg bg-primary"><?php echo $currentStock; ?></td>
                            <td class="bg bg-info"><?php echo $invS['utensils_name'] ?></td>
                            <td class="bg bg-info"><?php echo $invS['category'] ?></td>
                            <td class="bg bg-info"><?php echo $invS['model'] ?></td>
                            <td class="bg bg-info"><?php echo $invS['serial_no'] ?></td>
                            <td class="bg bg-info"><?php echo $invS['date_purchased'] ?></td>
                            <td class="bg bg-info">₱ <?php echo number_format($invS['cost'],2); ?></td>
                            <?php if ($invS['original_stock']== $currentStock){
                             ?> <td class="bg bg-success"><i class="fa fa-check text-success"></i> Complete </td> <?php
                           }else{
                             ?>
                             <td class="bg bg-danger">
                               <?php if ($invS['lost_qty']>0) {?>(<?php echo $invS['lost_qty'];?>) - Missing <br> <?php } ?>
                               <?php if ($invS['damaged_qty']>0) {?>(<?php echo $invS['damaged_qty'];?>) - Damaged <br><?php } ?>
                               <?php if ($invS['on_use']>0) {?> <b class="bg text-info"> (<?php echo $invS['on_use'];?>) - On use </b><br><?php } ?>
                             </td>
                             <?php
                           }
                             ?>
                          </tr>
                        <?php
                      }?>
                        </tbody>
                      <?php } ?>
                      </table>
                      <?php
                    }
                  } // end of by storage by storage
                  if ($_SESSION['default_report_inventory']==2) {
                    ?>
                  <div class="row">
                    <div class="col-md-6">
                    <table class="table table-bordered table-striped table-hover" id="report_table">
                      <thead>
                        <tr>
                          <th></th>
                          <th>ID</th>
                          <th>Orig-qty</th>
                          <th>cur-qty</th>
                          <th>Items</th>
                          <th>Category</th>
                          <th>Model</th>
                          <th>Serial</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $fetchInventoryMasterS = mysqli_query($dbcon,"SELECT * FROM inventory where date_added = CURRENT_DATE()");
                        foreach ($fetchInventoryMasterS as $key => $value);
                         $queryString = "SELECT *
                                      from utensils a
                                      left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                                      left join umsr c on a.umsr = c.id
                                      left join inventory_storage d on a.utensils_id = d.utensils_id
                                      where d.storage_id = $stID and a.status !=0 and d.original_stock != 0 and d.inventory_control_id = '".$value['inventory_control_id']."'
                                      group by d.utensils_id
                                      ";
                          $result = mysqli_query($dbcon,$queryString);
                          foreach ($result as $key => $invS) {
                        ?>
                        <tr>
                          <td><a href="?add_incident_report&id=<?php echo $invS['utensils_id'] ?>">Report</a></td>
                          <td class="bg bg-info"><?php echo $invS['utensils_id'] ?></td>
                          <?php
                            $currentStock = $invS['stock_remain'] + $invS['reserved_qty'];
                          ?>
                          <td class="bg bg-primary"><?php echo $invS['original_stock']; ?></td>
                          <td class="bg bg-primary"><?php echo $currentStock; ?></td>
                          <td class="bg bg-info"><?php echo $invS['utensils_name'] ?></td>
                          <td class="bg bg-info"><?php echo $invS['category'] ?></td>
                          <td class="bg bg-info"><?php echo $invS['model'] ?></td>
                          <td class="bg bg-info"><?php echo $invS['serial_no'] ?></td>
                        </tr>
                      <?php
                    }?>
                      </tbody>
                    </table>
                    </div>
                    <div class="col-md-6">
                      <?php if (isset($_SESSION['incident_report'])) {
                        ?>
                        <div class="card">
                          <div class="content">
                            <div class="row">
                              <div class="col-md-6">
                                <span>
                                <a href="?save_incident_report"onclick="return confirm('Confirm save!')"class="btn btn-sm btn-fill btn-success">Save report <i class="fa fa-check"></i></a>
                                </span>
                                <span>
                                 <a href="?clear_report"class="btn btn-sm btn-fill btn-warning">Clear report <i class="fa fa-trash"></i></a>
                                 </span>
                              </div>
                            </div>
                            <h5>Items to report :</h5>
                            <table class="table">
                               <thead>
                                 <tr>
                                   <th></th>
                                   <th>ID</th>
                                   <th>ITEMS</th>
                                   <th>LOST QTY</th>
                                   <th>DAMAGE QTY</th>
                                   <th>COMMENT</th>
                                 </tr>
                               </thead>
                               <tbody>
                                 <?php foreach (array_filter($_SESSION['incident_report']) as $key => $value) {
                                   $query = mysqli_query($dbcon,"Select * FROM utensils where utensils_id = ".$value['utensils_id']);
                                   foreach ($query as $key => $values) {

                                 ?>
                                 <tr>
                                   <td><a href="?action=remove_report&id=<?php echo $value['utensils_id']; ?>"><i class="fa fa-times text-danger"></i></a></td>
                                   <td><?php echo $value['utensils_id']; ?></td>
                                   <td><?php echo $values['utensils_name']; ?></td>
                                   <td class="bg bg-success"><?php echo $value['lost_qty']; ?></td>
                                   <td class="bg bg-success"><?php echo $value['damaged_qty']; ?></td>
                                   <td><?php echo $value['comment']; ?></td>
                                 </tr>
                               <?php }
                                  } ?>
                               </tbody>
                            </table>
                          </div>
                        </div>
                        <?php
                      }else {
                        ?>
                        <div class="card">
                          <div class="content">
                            <br><br><br><br>
                            <center>
                              <h4>(Empty Report)</h4>
                            </center>
                            <br><br><br><br>
                          </div>
                        </div>
                        <?php
                      } ?>

                    </div>
                  </div>
                    <?php
                  }if ($_SESSION['default_report_inventory']==3) {
                    if ($_SESSION['default_inventory_date']==1) {
                    ?>
                    <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                      <thead>
                        <tr>
                          <th class="bg bg-info">Report NO.</th>
                          <th class="bg bg-danger">Lost</th>
                          <th class="bg bg-danger">Damaged</th>
                          <th class="bg bg-warning">Comment</th>
                          <th class="bg bg-success">Date reported</th>
                          <th class="bg bg-success">Reported by</th>
                          <?php if ($_SESSION['account_type']==3) {
                            ?>
                            <th class="bg bg-info">Storage</th>
                            <?php
                          } ?>
                          <th class="bg bg-success">Date verified</th>
                          <th class="bg bg-success">Verified by</th>
                          <th class="bg bg-info">Remarks</th>

                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        if ($_SESSION['account_type']==3) {
                          $fetchReportMasterS = "SELECT *
                                    FROM report_date_control a
                                    left join reports b on a.rd_control_id = b.rd_control_id
                                    where   b.report_type_id = 3 and a.date_range = CURRENT_DATE()
                                    order by a.rd_control_id desc";
                           $queryString = mysqli_query($dbcon,$fetchReportMasterS);
                        }else {
                          $fetchReportMasterS = "SELECT *
                                    FROM report_date_control a
                                    left join reports b on a.rd_control_id = b.rd_control_id
                                    where b.storage_id = $storageID and b.report_type_id = 3 and a.date_range = CURRENT_DATE()
                                    order by a.rd_control_id desc";
                           $queryString = mysqli_query($dbcon,$fetchReportMasterS);
                        }
                          foreach ($queryString as $key => $invS) {
                        ?>
                        <tr>
                          <td ><?php echo $invS['report_id'] ?></td>
                          <td>
                          <?php $queryRdetails = "SELECT *
                                           FROM utensils a
                                           left join incident_report_details b on a.utensils_id = b.utensils_id
                                           left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                           where b.report_id = '".$invS['report_id']."'
                                           ";
                            $incidentD = mysqli_query($dbcon,$queryRdetails);
                        foreach ($incidentD as $key => $incD) {
                          if ($incD['lost_qty']==0) {
                          }else {
                          ?> <span><?php echo $incD['lost_qty'] ?> - <?php echo $incD['utensils_name'] ?></span> <br> <?php
                        }
                      }?>
                        </td>
                        <td>
                        <?php $queryRdetails = "SELECT *
                                         FROM utensils a
                                         left join incident_report_details b on a.utensils_id = b.utensils_id
                                         left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                         where b.report_id = '".$invS['report_id']."'
                                         ";
                          $incidentD = mysqli_query($dbcon,$queryRdetails);
                      foreach ($incidentD as $key => $incD) {
                        if ($incD['damaged_qty']==0) {
                        }else {
                        ?> <span><?php echo $incD['damaged_qty'] ?> - <?php echo $incD['utensils_name'] ?></span> <br> <?php
                      }
                    }?>
                      </td>
                      <td>
                      <?php $queryRdetails = "SELECT *
                                       FROM utensils a
                                       left join incident_report_details b on a.utensils_id = b.utensils_id
                                       left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                       where b.report_id = '".$invS['report_id']."'
                                       ";
                        $incidentD = mysqli_query($dbcon,$queryRdetails);
                    foreach ($incidentD as $key => $incD) {
                      ?> <span><?php echo $incD['comment'] ?> </span> <br> <?php
                    } ?>
                    </td>
                          <td><?php echo $invS['report_date'] ?></td>
                         <?php $fetchUser = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['reported_by']."'");
                         foreach ($fetchUser as $key => $userR) {
                           ?><td><?php echo $userR['lname'] ?></td><?php
                         } ?>
                         <?php if ($_SESSION['account_type']==3) {
                           $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = '".$invS['storage_id']."'");
                           foreach ($fetchStorage as $key => $stor) {
                          ?>
                          <td> <?php echo $stor['initials']; ?> </td>
                          <?php
                         }
                       }?>
                         <?php if ($invS['date_verified']==0) {
                           ?> <td></td> <?php
                         }else {
                           ?>
                           <td><?php echo $invS['date_verified'] ?></td>
                           <?php
                         } ?>
                          <?php
                          if ($invS['verified_by']==0) {
                            ?> <td></td> <?php
                          }else {

                           $fetchUserV = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['verified_by']."'");
                          foreach ($fetchUserV as $key => $userV) {
                            ?><td><?php  echo $userV['lname'] ?></td><?php
                          }
                        } ?>
                        <?php $queryRdetails = "SELECT *
                                          FROM utensils a
                                          left join incident_report_details b on a.utensils_id = b.utensils_id
                                          left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                          where b.report_id = '".$invS['report_id']."'
                                          ";
                           $incidentD = mysqli_query($dbcon,$queryRdetails);
                       foreach ($incidentD as $key => $incD);
                         ?> <span><?php if($incD['lost_qty']==$incD['found']&&$incD['damaged_qty']==$incD['replaced']){
                           ?> <td class="bg bg-success">Complete</td> <?php
                         }else { ?> <td class="bg bg-danger">Incomplete</td> <?php }  ?> </span>

                        </tr>
                      <?php

                    }?>
                      </tbody>
                    </table>
                    <?php
                     }
                     if ($_SESSION['default_inventory_date']==2) {
                       ?>
                       <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                         <thead>
                           <tr>
                             <th class="bg bg-info">Report NO.</th>
                             <th class="bg bg-danger">Lost</th>
                             <th class="bg bg-danger">Damaged</th>
                             <th class="bg bg-warning">Comment</th>
                             <th class="bg bg-success">Date reported</th>
                             <th class="bg bg-success">Reported by</th>
                             <?php if ($_SESSION['account_type']==3) {
                               ?>
                               <th class="bg bg-info">Storage</th>
                               <?php
                             } ?>
                             <th class="bg bg-success">Date verified</th>
                             <th class="bg bg-success">Verified by</th>
                             <th class="bg bg-info">Remarks</th>
                           </tr>
                         </thead>
                         <tbody>
                           <?php
                           if ($_SESSION['account_type']==3) {
                             $fetchReportMasterS = "SELECT *
                                       FROM report_date_control a
                                       left join reports b on a.rd_control_id = b.rd_control_id
                                       where   b.report_type_id = 3 order by b.report_id desc";
                              $queryString = mysqli_query($dbcon,$fetchReportMasterS);
                           }else {
                             $fetchReportMasterS = "SELECT *
                                       FROM report_date_control a
                                       left join reports b on a.rd_control_id = b.rd_control_id
                                       where b.storage_id = $storageID and b.report_type_id = 3 order by b.report_id desc";
                              $queryString = mysqli_query($dbcon,$fetchReportMasterS);
                           }
                             foreach ($queryString as $key => $invS) {
                           ?>
                           <tr>
                             <td ><?php echo $invS['report_id'] ?></td>
                             <td>
                             <?php $queryRdetails = "SELECT *
                                              FROM utensils a
                                              left join incident_report_details b on a.utensils_id = b.utensils_id
                                              left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                              where b.report_id = '".$invS['report_id']."'
                                              ";
                               $incidentD = mysqli_query($dbcon,$queryRdetails);
                           foreach ($incidentD as $key => $incD) {
                             if ($incD['lost_qty']==0) {
                             }else {
                             ?> <span><?php echo $incD['lost_qty'] ?> - <?php echo $incD['utensils_name'] ?></span> <br> <?php
                           }
                         }?>
                           </td>
                           <td>
                           <?php $queryRdetails = "SELECT *
                                            FROM utensils a
                                            left join incident_report_details b on a.utensils_id = b.utensils_id
                                            left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                            where b.report_id = '".$invS['report_id']."'
                                            ";
                             $incidentD = mysqli_query($dbcon,$queryRdetails);
                         foreach ($incidentD as $key => $incD) {
                           if ($incD['damaged_qty']==0) {
                           }else {
                           ?> <span><?php echo $incD['damaged_qty'] ?> - <?php echo $incD['utensils_name'] ?></span> <br> <?php
                         }
                       }?>
                         </td>
                         <td>
                         <?php $queryRdetails = "SELECT *
                                          FROM utensils a
                                          left join incident_report_details b on a.utensils_id = b.utensils_id
                                          left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                          where b.report_id = '".$invS['report_id']."'
                                          ";
                           $incidentD = mysqli_query($dbcon,$queryRdetails);
                       foreach ($incidentD as $key => $incD) {
                         ?> <span><?php echo $incD['comment'] ?> </span> <br> <?php
                       } ?>
                       </td>
                             <td><?php echo $invS['report_date'] ?></td>
                            <?php $fetchUser = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['reported_by']."'");
                            foreach ($fetchUser as $key => $userR) {
                              ?><td><?php echo $userR['lname'] ?></td><?php
                            } ?>
                            <?php if ($_SESSION['account_type']==3) {
                              $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = '".$invS['storage_id']."'");
                              foreach ($fetchStorage as $key => $stor) {
                             ?>
                             <td> <?php echo $stor['initials']; ?> </td>
                             <?php
                            }
                          }?>
                            <?php if ($invS['date_verified']==0) {
                              ?> <td></td> <?php
                            }else {
                              ?>
                              <td><?php echo $invS['date_verified'] ?></td>
                              <?php
                            } ?>
                             <?php
                             if ($invS['verified_by']==0) {
                               ?> <td></td> <?php
                             }else {

                              $fetchUserV = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['verified_by']."'");
                             foreach ($fetchUserV as $key => $userV) {
                               ?><td><?php  echo $userV['lname'] ?></td><?php
                             }
                           } ?>
                             <?php $queryRdetails = "SELECT *
                                               FROM utensils a
                                               left join incident_report_details b on a.utensils_id = b.utensils_id
                                               left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                               where b.report_id = '".$invS['report_id']."'
                                               ";
                                $incidentD = mysqli_query($dbcon,$queryRdetails);
                            foreach ($incidentD as $key => $incD);
                              ?> <span><?php if($incD['lost_qty']==$incD['found']&&$incD['damaged_qty']==$incD['replaced']){
                                ?> <td class="bg bg-success">Complete</td> <?php
                              }else { ?> <td class="bg bg-danger">Incomplete</td> <?php }  ?> </span>

                           </tr>
                         <?php

                       }?>
                         </tbody>
                       </table>
                       <?php
                     }
                     if ($_SESSION['default_inventory_date']==3) {
                       ?>
                       <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                         <thead>
                           <tr>
                             <th class="bg bg-info">Report NO.</th>
                             <th class="bg bg-danger">Lost</th>
                             <th class="bg bg-danger">Damaged</th>
                             <th class="bg bg-warning">Comment</th>
                             <th class="bg bg-success">Date reported</th>
                             <th class="bg bg-success">Reported by</th>
                             <?php if ($_SESSION['account_type']==3) {
                               ?>
                               <th class="bg bg-info">Storage</th>
                               <?php
                             } ?>
                             <th class="bg bg-success">Date verified</th>
                             <th class="bg bg-success">Verified by</th>
                             <th class="bg bg-info">Remarks</th>
                           </tr>
                         </thead>
                         <tbody>
                           <?php
                           $dat1 = $_SESSION['in_report_date1'];
                           $dat2 = $_SESSION['in_report_date2'];
                           if ($_SESSION['account_type']==3) {
                             $fetchReportMasterS = "SELECT *
                                       FROM report_date_control a
                                       left join reports b on a.rd_control_id = b.rd_control_id
                                       where   b.report_type_id = 3 and a.date_range  >= '$dat1' and a.date_range <= '$dat2'
                                       order by a.rd_control_id desc";
                              $queryString = mysqli_query($dbcon,$fetchReportMasterS);
                           }else {
                             $fetchReportMasterS = "SELECT *
                                       FROM report_date_control a
                                       left join reports b on a.rd_control_id = b.rd_control_id
                                       where b.storage_id = $storageID and b.report_type_id = 3 and a.date_range  >= '$dat1' and a.date_range <= '$dat2'
                                       order by a.rd_control_id desc";
                              $queryString = mysqli_query($dbcon,$fetchReportMasterS);
                           }
                             foreach ($queryString as $key => $invS) {
                           ?>
                           <tr>
                             <td ><?php echo $invS['report_id'] ?></td>
                             <td>
                             <?php $queryRdetails = "SELECT *
                                              FROM utensils a
                                              left join incident_report_details b on a.utensils_id = b.utensils_id
                                              left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                              where b.report_id = '".$invS['report_id']."'
                                              ";
                               $incidentD = mysqli_query($dbcon,$queryRdetails);
                           foreach ($incidentD as $key => $incD) {
                             if ($incD['lost_qty']==0) {
                             }else {
                             ?> <span><?php echo $incD['lost_qty'] ?> - <?php echo $incD['utensils_name'] ?></span> <br> <?php
                           }
                         }?>
                           </td>
                           <td>
                           <?php $queryRdetails = "SELECT *
                                            FROM utensils a
                                            left join incident_report_details b on a.utensils_id = b.utensils_id
                                            left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                            where b.report_id = '".$invS['report_id']."'
                                            ";
                             $incidentD = mysqli_query($dbcon,$queryRdetails);
                         foreach ($incidentD as $key => $incD) {
                           if ($incD['damaged_qty']==0) {
                           }else {
                           ?> <span><?php echo $incD['damaged_qty'] ?> - <?php echo $incD['utensils_name'] ?></span> <br> <?php
                         }
                       }?>
                         </td>
                         <td>
                         <?php $queryRdetails = "SELECT *
                                          FROM utensils a
                                          left join incident_report_details b on a.utensils_id = b.utensils_id
                                          left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                          where b.report_id = '".$invS['report_id']."'
                                          ";
                           $incidentD = mysqli_query($dbcon,$queryRdetails);
                       foreach ($incidentD as $key => $incD) {
                         ?> <span><?php echo $incD['comment'] ?> </span> <br> <?php
                       } ?>
                       </td>
                             <td><?php echo $invS['report_date'] ?></td>
                            <?php $fetchUser = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['reported_by']."'");
                            foreach ($fetchUser as $key => $userR) {
                              ?><td><?php echo $userR['lname'] ?></td><?php
                            } ?>
                            <?php if ($_SESSION['account_type']==3) {
                              $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = '".$invS['storage_id']."'");
                              foreach ($fetchStorage as $key => $stor) {
                             ?>
                             <td> <?php echo $stor['initials']; ?> </td>
                             <?php
                            }
                          }?>
                            <?php if ($invS['date_verified']==0) {
                              ?> <td></td> <?php
                            }else {
                              ?>
                              <td><?php echo $invS['date_verified'] ?></td>
                              <?php
                            } ?>
                             <?php
                             if ($invS['verified_by']==0) {
                               ?> <td></td> <?php
                             }else {

                              $fetchUserV = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['verified_by']."'");
                             foreach ($fetchUserV as $key => $userV) {
                               ?><td><?php  echo $userV['lname'] ?></td><?php
                             }
                           } ?>
                           <?php $queryRdetails = "SELECT *
                                             FROM utensils a
                                             left join incident_report_details b on a.utensils_id = b.utensils_id
                                             left join utensils_category c on a.utensils_cat_id = c.utensils_cat_id
                                             where b.report_id = '".$invS['report_id']."'
                                             ";
                              $incidentD = mysqli_query($dbcon,$queryRdetails);
                          foreach ($incidentD as $key => $incD);
                            ?> <span><?php if($incD['lost_qty']==$incD['found']&&$incD['damaged_qty']==$incD['replaced']){
                              ?> <td class="bg bg-success">Complete</td> <?php
                            }else { ?> <td class="bg bg-danger">Incomplete</td> <?php }  ?> </span>
                           </tr>
                         <?php

                       }?>
                         </tbody>
                       </table>
                       <?php
                     }
                  }
                  ?>
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
