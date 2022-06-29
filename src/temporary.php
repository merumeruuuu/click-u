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
    #content {
    display: table;
}

#pageFooter {
    display: table-footer-group;
}

#pageFooter:after {
    counter-increment: page;
    content: counter(page);
}
#pageFooter:after {
    counter-increment: page;
    content:"Page " counter(page);
    left: 0;
    top: 100%;
    white-space: nowrap;
    z-index: 20;
    -moz-border-radius: 5px;
    -moz-box-shadow: 0px 0px 4px #222;
    background-image: -moz-linear-gradient(top, #eeeeee, #cccccc);
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
                  <a href="inventory_admin.php"id="printPageButton1"class="btn btn-info btn-fill bt-sm" name="button">Back</a>
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
                      <?php if ($_SESSION['default_inventory']==1) {
                        ?>
                        <h5>Inventory Report</h5>
                        <?php
                      }else {
                        ?>
                        <h5>Incident Report</h5>
                        <?php
                      } ?>

                      <h5> Date retrieved : <?php echo date('m-d-Y'); ?> </h5>
                    </div>

                  </div>
              </div>
              <?php if ($_SESSION['default_inventory']==1) { // if all utensils and current date) {
                     if ($_SESSION['default_inventory_storage']==10102) {
                       if ($_SESSION['default_inventory_date']==1) {
                     ?>
                      <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                        <thead>
                          <tr>
                            <th>ID#</th>
                            <th>Orig-qty</th>
                            <th >Cur-qty</th>
                            <th > Items</th>
                            <th>Category</th>
                            <th >Model</th>
                            <th >Serial#</th>
                            <th>Date purchased</th>
                            <th >Unit Cost</th>
                            <th >Remarks</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $queryString = "SELECT
                                              a.utensils_id,a.utensils_name,a.utensils_cat_id,a.cost,a.umsr,a.model,a.serial_no,a.date_purchased,a.status,
                                              b.utensils_cat_id,b.category,
                                              c.id,c.umsr_name,
                                              d.inventory_control_id,d.utensils_id,d.original_stock,d.remain_stock,
                                              e.inventory_control_id,e.date_added

                                              from utensils a
                                              left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                                              left join umsr c on a.umsr = c.id
                                              left join inventory_all_record d on a.utensils_id = d.utensils_id
                                              left join inventory e on d.inventory_control_id = e.inventory_control_id
                                              where e.date_added = CURRENT_DATE() and a.status != 0
                                              order by a.utensils_id
                                              ";
                            $result = mysqli_query($dbcon,$queryString);
                            ?>
                            <?php
                             foreach ($result as $key => $value) {
                               ?>
                                 <tr>
                                   <td class="bg bg-info"><?php echo $value['utensils_id']; ?></td>
                                   <td class="bg bg-warning"><?php echo $value['original_stock']; ?></td>
                                   <?php $inventoryD = mysqli_query($dbcon,"SELECT sum(storage_qty)as remain_s,
                                    sum(lost_qty)as lost_qty,sum(damaged_qty)as damaged_qty,sum(reserved_qty)as reserved_qty,sum(on_use)as on_use
                                    FROM storage_stocks where utensils_id = '".$value['utensils_id']."'");
                                    foreach ($inventoryD as $key => $invD) {
                                      $currentStock = $value['remain_stock'] + $invD['remain_s']  + $invD['reserved_qty'];
                                      $lacking = $value['original_stock'] - $currentStock;
                                    ?>
                                   <td class="bg bg-warning"><?php echo $currentStock; ?></td>
                                   <td class="bg bg-info"><?php echo $value['utensils_name']; ?></td>
                                   <td class="bg bg-info"><?php echo $value['category']; ?></td>
                                   <td class="bg bg-info"><?php echo $value['model']; ?></td>
                                   <td class="bg bg-info"><?php echo $value['serial_no']; ?></td>
                                   <td class="bg bg-info"><?php echo date('M d,Y',strtotime($value['date_purchased'])); ?></td>
                                   <td class="bg bg-info">₱ <?php echo number_format($value['cost'],2); ?></td>
                                   <?php if ($value['original_stock']==$currentStock) {
                                    ?> <td class="bg bg-success"><i class="fa fa-check text-success"></i> Complete </td> <?php
                                  }else{
                                    ?>
                                    <td class="bg bg-danger">
                                      <?php if ($invD['lost_qty']>0) {?>(<?php echo $invD['lost_qty'];?>) - Missing <br> <?php } ?>
                                      <?php if ($invD['damaged_qty']>0) {?>(<?php echo $invD['damaged_qty'];?>) - Damaged <br><?php } ?>
                                      <?php if ($invD['on_use']>0) {?> <b class="bg text-info"> (<?php echo $invD['on_use'];?>) - On use </b><br><?php } ?>
                                    </td>
                                    <?php
                                  }
                                    ?>

                                    <?php
                                  } ?>
                                 </tr>

                        <?php } ?>
                        </tbody>
                      </table>
                     <?php
                   } //end of default date
                      if ($_SESSION['default_inventory_date']==2) { //if all date all utensils
                     ?>
                     <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                     <thead>
                      <tr>
                        <th>ID#</th>
                        <th>Orig-qty</th>
                        <th >Cur-qty</th>
                        <th > Items</th>
                        <th>Category</th>
                        <th >Model</th>
                        <th >Serial#</th>
                        <th>Date purchased</th>
                        <th >Unit Cost</th>
                        <th >Remarks</th>
                        </tr>
                    </thead>
                    <?php $checkInventory = mysqli_query($dbcon,"SELECT * FROM inventory order by inventory_control_id desc");
                    foreach ($checkInventory as $key => $inventoryM) {
                     ?>
                    <thead>
                      <tr>
                       <th colspan="10"class="text-center"> <?php echo $inventoryM['date_added'] ?></th>
                     </tr>
                    </thead>
                    <?php $fetchInventoryDetails = mysqli_query($dbcon,"SELECT * FROM inventory_all_record where inventory_control_id = '".$inventoryM['inventory_control_id']."'");
                    foreach ($fetchInventoryDetails as $key => $value) {
                      $fetchUtensiDetails = "SELECT *
                        FROM  `utensils`
                        left join `utensils_category`
                        on `utensils`.`utensils_cat_id` = `utensils_category`.`utensils_cat_id`
                        left join `umsr`  on `utensils`.`umsr` = `umsr`.`id`
                        where `utensils`.`utensils_id` = '".$value['utensils_id']."' and `utensils`.`status` != 0";
                        $utensilsQ = mysqli_query($dbcon,$fetchUtensiDetails);
                      foreach ($utensilsQ as $key => $utensilsD) {
                        $fetchInventoryS = mysqli_query($dbcon,"SELECT sum(lost_qty)as lost_qty,sum(damaged_qty)
                        as damaged_qty,sum(reserved_qty)as reserved_qty,sum(stock_remain)as remain_s,sum(on_use)as on_use
                          FROM inventory_storage where inventory_control_id = '".$value['inventory_control_id']."'and utensils_id = '".$value['utensils_id']."'");
                        foreach ($fetchInventoryS as $key => $inStor) {
                        $currentStock = $value['remain_stock'] + $inStor['remain_s']  + $inStor['reserved_qty'];
                        $lacking = $value['original_stock'] - $currentStock;
                      ?>
                      <tbody>
                       <tr>
                         <td class="bg bg-info"> <?php echo $value['utensils_id'] ?></td>
                         <td class="bg bg-warning"><?php echo $value['original_stock'] ?></td>
                         <td class="bg bg-warning"><?php echo $currentStock; ?></td>
                         <td class="bg bg-info"><?php echo $utensilsD['utensils_name'] ?></td>
                         <td class="bg bg-info"><?php echo $utensilsD['category'] ?></td>
                         <td class="bg bg-info"><?php echo $utensilsD['model'] ?></td>
                         <td class="bg bg-info"><?php echo $utensilsD['serial_no'] ?></td>
                         <td class="bg bg-info"><?php echo $utensilsD['date_purchased'] ?></td>
                         <td class="bg bg-info"><?php echo $utensilsD['cost'] ?></td>
                         <?php if ($value['original_stock']==$currentStock) {
                          ?> <td class="bg bg-success"><i class="fa fa-check text-success"></i> Complete </td> <?php
                        }else {
                          ?>
                          <td class="bg bg-danger">
                            <?php if ($inStor['lost_qty']>0) {?>(<?php echo $inStor['lost_qty'];?>) - Missing <br> <?php } ?>
                            <?php if ($inStor['damaged_qty']>0) {?>(<?php echo $inStor['damaged_qty'];?>) - Damaged <br><?php } ?>
                            <?php if ($inStor['on_use']>0) {?> <b class="bg text-info"> (<?php echo $inStor['on_use'];?>) - On use </b><br><?php } ?>
                          </td>
                          <?php
                        }
                          ?>
                       </tr>
                          </tbody>
                      <?php }
                           }
                        }
                    }
                    ?>
                    </table>
                  <?php
                } // end of default date 2
                 if ($_SESSION['default_inventory_date']==3) { // if custom date all utensils
                   ?>
                   <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                     <thead>
                       <tr>
                         <th>ID#</th>
                         <th>Orig-qty</th>
                         <th >Cur-qty</th>
                         <th > Items</th>
                         <th>Category</th>
                         <th >Model</th>
                         <th >Serial#</th>
                         <th>Date purchased</th>
                         <th >Unit Cost</th>
                         <th >Remarks</th>
                         </tr>
                     </thead>
                     <?php
                     $dat1 = $_SESSION['inventory_date1'];
                     $dat2 = $_SESSION['inventory_date2'];
                     $checkInventory = mysqli_query($dbcon,"SELECT * FROM inventory where date_added >= '$dat1' and date_added <= '$dat2'");
                     foreach ($checkInventory as $key => $inventoryM) {
                      ?>
                     <thead>
                       <tr>
                        <th colspan="10"class="text-center"> <?php echo $inventoryM['date_added'] ?></th>
                      </tr>
                     </thead>
                    <?php $fetchInventoryDetails = mysqli_query($dbcon,"SELECT * FROM inventory_all_record where inventory_control_id = '".$inventoryM['inventory_control_id']."'");
                    foreach ($fetchInventoryDetails as $key => $value) {
                      $fetchUtensiDetails = "SELECT *
                        FROM  `utensils`
                        left join `utensils_category`
                        on `utensils`.`utensils_cat_id` = `utensils_category`.`utensils_cat_id`
                        left join `umsr`  on `utensils`.`umsr` = `umsr`.`id`
                        where `utensils`.`utensils_id` = '".$value['utensils_id']."'and `utensils`.`status` != 0";
                        $utensilsQ = mysqli_query($dbcon,$fetchUtensiDetails);
                      foreach ($utensilsQ as $key => $utensilsD) {
                        $fetchInventoryS = mysqli_query($dbcon,"SELECT sum(lost_qty)as lost_qty,sum(damaged_qty)
                        as damaged_qty,sum(reserved_qty)as reserved_qty,sum(stock_remain)as remain_s,sum(on_use)as on_use
                          FROM inventory_storage where inventory_control_id = '".$value['inventory_control_id']."'and utensils_id = '".$value['utensils_id']."'");
                        foreach ($fetchInventoryS as $key => $inStor) {
                        $currentStock = $value['remain_stock'] + $inStor['remain_s']  + $inStor['reserved_qty'];
                        $lacking = $value['original_stock'] - $currentStock;
                      ?>
                      <tbody>
                       <tr>
                         <td class="bg bg-info"> <?php echo $value['utensils_id'] ?></td>
                         <td class="bg bg-warning"><?php echo $value['original_stock'] ?></td>
                         <td class="bg bg-warning"><?php echo $currentStock; ?></td>
                         <td class="bg bg-info"><?php echo $utensilsD['utensils_name'] ?></td>
                         <td class="bg bg-info"><?php echo $utensilsD['category'] ?></td>
                         <td class="bg bg-info"><?php echo $utensilsD['model'] ?></td>
                         <td class="bg bg-info"><?php echo $utensilsD['serial_no'] ?></td>
                         <td class="bg bg-info"><?php echo $utensilsD['date_purchased'] ?></td>
                         <td class="bg bg-info"><?php echo $utensilsD['cost'] ?></td>
                         <?php if ($value['original_stock']==$currentStock) {
                          ?> <td class="bg bg-success"><i class="fa fa-check text-success"></i> Complete </td> <?php
                        }else {
                          ?>
                          <td class="bg bg-danger">
                            <?php if ($inStor['lost_qty']>0) {?>(<?php echo $inStor['lost_qty'];?>) - Missing <br> <?php } ?>
                            <?php if ($inStor['damaged_qty']>0) {?>(<?php echo $inStor['damaged_qty'];?>) - Damaged <br><?php } ?>
                            <?php if ($inStor['on_use']>0) {?> <b class="bg text-info"> (<?php echo $inStor['on_use'];?>) - On use </b><br><?php } ?>
                          </td>
                          <?php
                        }
                          ?>
                       </tr>
                        </tbody>
                      <?php }
                           }
                       }
                    }
                     ?>
                     </table>
                   <?php
                 } //end of custom date ?>
                     <?php
                   } //end of default storage option
                   if ($_SESSION['default_inventory_storage']==101001) { //if all storages
                     if ($_SESSION['default_inventory_date']==1) {
                    ?>
                    <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                   <thead>
                     <tr>
                       <th>ID#</th>
                       <th > Items</th>
                       <th>Category</th>
                       <th >Model</th>
                       <th >Serial#</th>
                       <th>Date purchased</th>
                       <th >Unit Cost</th>
                       <th>Orig-qty</th>
                       <th >Cur-qty</th>
                       <?php $fetchInventoryMaster = mysqli_query($dbcon,"SELECT * FROM inventory where date_added = CURRENT_DATE()");
                       foreach ($fetchInventoryMaster as $key => $value);
                        $fetchStorages = "SELECT *
                                          FROM inventory_storage a
                                          left join storage b on a.storage_id = b.storage_id
                                          where a.inventory_control_id = '".$value['inventory_control_id']."'
                                           group by a.storage_id ";
                         $storageRes = mysqli_query($dbcon,$fetchStorages);
                         foreach ($storageRes as $key => $value) {
                         ?>
                        <th><?php echo $value['initials']; ?></th>
                         <?php
                         }
                       ?>
                       <th >Remarks</th>
                       </tr>
                   </thead>
                   <tbody>
                   <?php
                   $queryString = "SELECT
                                       a.utensils_id,a.utensils_name,a.utensils_cat_id,a.cost,a.umsr,a.model,a.serial_no,a.date_purchased,a.status,
                                       b.utensils_cat_id,b.category,
                                       c.id,c.umsr_name,
                                       d.inventory_control_id,d.utensils_id,d.original_stock,d.remain_stock,
                                       e.inventory_control_id,e.date_added

                                       from utensils a
                                       left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                                       left join umsr c on a.umsr = c.id
                                       left join inventory_all_record d on a.utensils_id = d.utensils_id
                                       left join inventory e on d.inventory_control_id = e.inventory_control_id
                                       where e.date_added = CURRENT_DATE() and a.status != 0
                                       order by a.utensils_id
                                       ";
                     $result = mysqli_query($dbcon,$queryString);
                                        foreach ($result as $key => $value) {
                                       ?>
                                  <tr>
                                    <td class="bg bg-info"><?php echo $value['utensils_id']; ?></td>

                                    <?php $inventoryD = mysqli_query($dbcon,"SELECT sum(stock_remain)as remain_s,
                                     sum(lost_qty)as lost_qty,sum(damaged_qty)as damaged_qty,sum(reserved_qty)as reserved_qty,sum(on_use)as on_use
                                     FROM inventory_storage where utensils_id = '".$value['utensils_id']."' and inventory_control_id = '".$value['inventory_control_id']."'");
                                     foreach ($inventoryD as $key => $invD) {
                                       $currentStock = $value['remain_stock'] + $invD['remain_s']  + $invD['reserved_qty'];
                                       $lacking = $value['original_stock'] - $currentStock;

                                     ?>
                                    <td class="bg bg-info"><?php echo $value['utensils_name']; ?></td>
                                    <td class="bg bg-info"><?php echo $value['category']; ?></td>
                                    <td class="bg bg-info"><?php echo $value['model']; ?></td>
                                    <td class="bg bg-info"><?php echo $value['serial_no']; ?></td>
                                    <td class="bg bg-info"><?php echo date('M d,Y',strtotime($value['date_purchased'])); ?></td>
                                    <td class="bg bg-info">₱ <?php echo number_format($value['cost'],2); ?></td>

                                    <td class="bg bg-warning"><?php echo $value['original_stock']; ?></td>
                                    <td class="bg bg-warning"><?php echo $value['remain_stock']; ?></td>
                                    <?php
                                       $query5 = "SELECT
                                                  a.utensils_id,a.storage_id,a.stock_remain,a.inventory_control_id,a.reserved_qty,
                                                  b.storage_id

                                                  from inventory_storage a
                                                  left join storage b on a.storage_id = b.storage_id
                                                  where a.utensils_id = '".$value['utensils_id']."' and a.inventory_control_id = '".$value['inventory_control_id']."'
                                                  order by b.storage_id";
                                        $check = mysqli_query($dbcon,$query5);
                                        while ($show = mysqli_fetch_array($check)) {
                                          $remainPlusResrvd = $show['stock_remain'] + $show['reserved_qty'];
                                          ?>
                                           <td class="bg bg-primary"><?php echo $remainPlusResrvd; ?></td>
                                          <?php
                                        }
                                     ?>
                                    <?php if ($value['original_stock']==$currentStock) {
                                     ?> <td class="bg bg-success"><i class="fa fa-check text-success"></i> Complete </td> <?php
                                   }else{
                                     ?>
                                     <td class="bg bg-danger">
                                       <?php if ($invD['lost_qty']>0) {?>(<?php echo $invD['lost_qty'];?>) - Missing <br> <?php } ?>
                                       <?php if ($invD['damaged_qty']>0) {?>(<?php echo $invD['damaged_qty'];?>) - Damaged <br><?php } ?>
                                       <?php if ($invD['on_use']>0) {?> <b class="bg text-info"> (<?php echo $invD['on_use'];?>) - On use </b><br><?php } ?>
                                     </td>
                                     <?php
                                   }
                                     ?>

                                     <?php
                                   } ?>
                                  </tr>

                         <?php }
                         ?>
                       </tbody>
                       </table>
                  <?php } //end of date 1 storage option 2
                  if ($_SESSION['default_inventory_date']==2) { // if all storage and all date
                    ?>
                  <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                   <thead>
                     <tr>
                       <th>ID#</th>
                       <th > Items</th>
                       <th>Category</th>
                       <th >Model</th>
                       <th >Serial#</th>
                       <th>Date purchased</th>
                       <th >Unit Cost</th>
                       <th>Orig-qty</th>
                       <th >Cur-qty</th>
                       <?php $fetchInventoryMaster = mysqli_query($dbcon,"SELECT * FROM inventory order by inventory_control_id ");
                       foreach ($fetchInventoryMaster as $key => $value);
                        $fetchStorages = "SELECT *
                                          FROM inventory_storage a
                                          left join storage b on a.storage_id = b.storage_id
                                          where a.inventory_control_id = '".$value['inventory_control_id']."'
                                           group by a.storage_id ";
                         $storageRes = mysqli_query($dbcon,$fetchStorages);
                         foreach ($storageRes as $key => $value) {
                         ?>
                        <th><?php echo $value['initials']; ?></th>
                         <?php
                         }
                       ?>
                       <th >Remarks</th>
                       </tr>
                       </thead>
                        <?php $fetchInventoryMaster2 = mysqli_query($dbcon,"SELECT * FROM inventory order by inventory_control_id desc");
                        foreach ($fetchInventoryMaster2 as $key => $values){
                        ?>
                        <thead>
                          <tr>
                            <th colspan="16" class="text-center"><?php echo $values['date_added']; ?></th>
                          </tr>
                        </thead>
                   <tbody>
                   <?php
                   $queryString = "SELECT
                                       a.utensils_id,a.utensils_name,a.utensils_cat_id,a.cost,a.umsr,a.model,a.serial_no,a.date_purchased,a.status,
                                       b.utensils_cat_id,b.category,
                                       c.id,c.umsr_name,
                                       d.inventory_control_id,d.utensils_id,d.original_stock,d.remain_stock,
                                       e.inventory_control_id,e.date_added

                                       from utensils a
                                       left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                                       left join umsr c on a.umsr = c.id
                                       left join inventory_all_record d on a.utensils_id = d.utensils_id
                                       left join inventory e on d.inventory_control_id = e.inventory_control_id
                                       where e.inventory_control_id = '".$values['inventory_control_id']."' and a.status != 0
                                       order by a.utensils_id
                                       ";
                     $result = mysqli_query($dbcon,$queryString);
                                        foreach ($result as $key => $value) {
                                       ?>
                                       <tr>
                                         <td class="bg bg-info"><?php echo $value['utensils_id']; ?></td>

                                         <?php $inventoryD = mysqli_query($dbcon,"SELECT sum(stock_remain)as remain_s,
                                          sum(lost_qty)as lost_qty,sum(damaged_qty)as damaged_qty,sum(reserved_qty)as reserved_qty,sum(on_use)as on_use
                                          FROM inventory_storage where utensils_id = '".$value['utensils_id']."' and inventory_control_id = '".$value['inventory_control_id']."'");
                                          foreach ($inventoryD as $key => $invD) {
                                            $currentStock = $value['remain_stock'] + $invD['remain_s']  + $invD['reserved_qty'];
                                            $lacking = $value['original_stock'] - $currentStock;

                                          ?>
                                         <td class="bg bg-info"><?php echo $value['utensils_name']; ?></td>
                                         <td class="bg bg-info"><?php echo $value['category']; ?></td>
                                         <td class="bg bg-info"><?php echo $value['model']; ?></td>
                                         <td class="bg bg-info"><?php echo $value['serial_no']; ?></td>
                                         <td class="bg bg-info"><?php echo date('M d,Y',strtotime($value['date_purchased'])); ?></td>
                                         <td class="bg bg-info">₱ <?php echo number_format($value['cost'],2); ?></td>

                                         <td class="bg bg-warning"><?php echo $value['original_stock']; ?></td>
                                         <td class="bg bg-warning"><?php echo $value['remain_stock']; ?></td>
                                         <?php
                                            $query5 = "SELECT
                                                       a.utensils_id,a.storage_id,a.stock_remain,a.inventory_control_id,a.reserved_qty,
                                                       b.storage_id

                                                       from inventory_storage a
                                                       left join storage b on a.storage_id = b.storage_id
                                                       where a.utensils_id = '".$value['utensils_id']."' and a.inventory_control_id = '".$values['inventory_control_id']."'
                                                       order by b.storage_id";
                                             $check = mysqli_query($dbcon,$query5);
                                             while ($show = mysqli_fetch_array($check)) {
                                               $remainPlusResrvd = $show['stock_remain'] + $show['reserved_qty'];
                                               ?>
                                                <td class="bg bg-primary"><?php echo $remainPlusResrvd; ?></td>
                                               <?php
                                             }
                                          ?>
                                         <?php if ($value['original_stock']==$currentStock) {
                                          ?> <td class="bg bg-success"><i class="fa fa-check text-success"></i> Complete </td> <?php
                                        }else{
                                          ?>
                                          <td class="bg bg-danger">
                                            <?php if ($invD['lost_qty']>0) {?>(<?php echo $invD['lost_qty'];?>) - Missing <br> <?php } ?>
                                            <?php if ($invD['damaged_qty']>0) {?>(<?php echo $invD['damaged_qty'];?>) - Damaged <br><?php } ?>
                                            <?php if ($invD['on_use']>0) {?> <b class="bg text-info"> (<?php echo $invD['on_use'];?>) - On use </b><br><?php } ?>
                                          </td>
                                          <?php
                                        }
                                          ?>

                                          <?php
                                        } ?>
                                       </tr>

                         <?php }
                         ?>
                       </tbody>
                     <?php } ?>
                       </table>
                    <?php
                  }
                  if ($_SESSION['default_inventory_date']==3) {  // if all storages custom date
                    ?>
                    <table class="table table-bordered table-striped table-hover" id="ALL_UTENSILS">
                     <thead>
                       <tr>
                         <th>ID#</th>
                         <th > Items</th>
                         <th>Category</th>
                         <th >Model</th>
                         <th >Serial#</th>
                         <th>Date purchased</th>
                         <th >Unit Cost</th>
                         <th>Orig-qty</th>
                         <th >Cur-qty</th>
                         <?php
                         $date1 = $_SESSION['inventory_date1'];
                         $date2 = $_SESSION['inventory_date1'];
                         $fetchInventoryMaster = mysqli_query($dbcon,"SELECT * FROM inventory order by inventory_control_id");
                         foreach ($fetchInventoryMaster as $key => $value);
                          $fetchStorages = "SELECT *
                                            FROM inventory_storage a
                                            left join storage b on a.storage_id = b.storage_id
                                            where a.inventory_control_id = '".$value['inventory_control_id']."'
                                             group by a.storage_id ";
                           $storageRes = mysqli_query($dbcon,$fetchStorages);
                           foreach ($storageRes as $key => $value) {
                           ?>
                          <th><?php echo $value['initials']; ?></th>
                           <?php
                           }
                         ?>
                         <th >Remarks</th>
                         </tr>
                         </thead>
                          <?php $fetchInventoryMaster2 = mysqli_query($dbcon,"SELECT * FROM inventory where date_added >= '$date1' and date_added <= '$date2'");
                          foreach ($fetchInventoryMaster2 as $key => $values){
                          ?>
                          <thead>
                            <tr>
                              <th colspan="16" class="text-center"><?php echo $values['date_added']; ?></th>
                            </tr>
                          </thead>
                     <tbody>
                     <?php
                     $queryString = "SELECT
                                         a.utensils_id,a.utensils_name,a.utensils_cat_id,a.cost,a.umsr,a.model,a.serial_no,a.date_purchased,a.status,
                                         b.utensils_cat_id,b.category,
                                         c.id,c.umsr_name,
                                         d.inventory_control_id,d.utensils_id,d.original_stock,d.remain_stock,
                                         e.inventory_control_id,e.date_added

                                         from utensils a
                                         left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                                         left join umsr c on a.umsr = c.id
                                         left join inventory_all_record d on a.utensils_id = d.utensils_id
                                         left join inventory e on d.inventory_control_id = e.inventory_control_id
                                         where e.inventory_control_id = '".$values['inventory_control_id']."' and a.status != 0
                                         order by a.utensils_id
                                         ";
                       $result = mysqli_query($dbcon,$queryString);
                                          foreach ($result as $key => $value) {
                                         ?>
                                         <tr>
                                           <td class="bg bg-info"><?php echo $value['utensils_id']; ?></td>

                                           <?php $inventoryD = mysqli_query($dbcon,"SELECT sum(stock_remain)as remain_s,
                                            sum(lost_qty)as lost_qty,sum(damaged_qty)as damaged_qty,sum(reserved_qty)as reserved_qty,sum(on_use)as on_use
                                            FROM inventory_storage where utensils_id = '".$value['utensils_id']."' and inventory_control_id = '".$value['inventory_control_id']."'");
                                            foreach ($inventoryD as $key => $invD) {
                                              $currentStock = $value['remain_stock'] + $invD['remain_s']  + $invD['reserved_qty'];
                                              $lacking = $value['original_stock'] - $currentStock;

                                            ?>
                                           <td class="bg bg-info"><?php echo $value['utensils_name']; ?></td>
                                           <td class="bg bg-info"><?php echo $value['category']; ?></td>
                                           <td class="bg bg-info"><?php echo $value['model']; ?></td>
                                           <td class="bg bg-info"><?php echo $value['serial_no']; ?></td>
                                           <td class="bg bg-info"><?php echo date('M d,Y',strtotime($value['date_purchased'])); ?></td>
                                           <td class="bg bg-info">₱ <?php echo number_format($value['cost'],2); ?></td>

                                           <td class="bg bg-warning"><?php echo $value['original_stock']; ?></td>
                                           <td class="bg bg-warning"><?php echo $value['remain_stock']; ?></td>
                                           <?php
                                              $query5 = "SELECT
                                                         a.utensils_id,a.storage_id,a.stock_remain,a.inventory_control_id,a.reserved_qty,
                                                         b.storage_id

                                                         from inventory_storage a
                                                         left join storage b on a.storage_id = b.storage_id
                                                         where a.utensils_id = '".$value['utensils_id']."' and a.inventory_control_id = '".$values['inventory_control_id']."'
                                                         order by b.storage_id";
                                               $check = mysqli_query($dbcon,$query5);
                                               while ($show = mysqli_fetch_array($check)) {
                                                 $remainPlusResrvd = $show['stock_remain'] + $show['reserved_qty'];
                                                 ?>
                                                  <td class="bg bg-primary"><?php echo $remainPlusResrvd; ?></td>
                                                 <?php
                                               }
                                            ?>
                                           <?php if ($value['original_stock']==$currentStock) {
                                            ?> <td class="bg bg-success"><i class="fa fa-check text-success"></i> Complete </td> <?php
                                          }else{
                                            ?>
                                            <td class="bg bg-danger">
                                              <?php if ($invD['lost_qty']>0) {?>(<?php echo $invD['lost_qty'];?>) - Missing <br> <?php } ?>
                                              <?php if ($invD['damaged_qty']>0) {?>(<?php echo $invD['damaged_qty'];?>) - Damaged <br><?php } ?>
                                              <?php if ($invD['on_use']>0) {?> <b class="bg text-info"> (<?php echo $invD['on_use'];?>) - On use </b><br><?php } ?>
                                            </td>
                                            <?php
                                          }
                                            ?>
                                            <?php
                                          } ?>
                                         </tr>

                           <?php }
                           ?>
                         </tbody>
                       <?php } ?>
                         </table>
                    <?php
                  }
                } // end storage option 2
                if ($_SESSION['default_inventory_storage']!=10102&&$_SESSION['default_inventory_storage']!=101001) {  //if by storage
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
                                      where d.storage_id = '".$_SESSION['default_inventory_storage']."' and a.status !=0 and d.original_stock != 0
                                      group by d.utensils_id
                                      ";
                          $result = mysqli_query($dbcon,$queryString);
                          foreach ($result as $key => $invS) {
                        ?>
                        <tr>
                          <td class="bg bg-info"><?php echo $invS['utensils_id'] ?></td>
                          <?php $inventoryStorage = mysqli_query($dbcon,"SELECT count(utensils_id)as numID,sum(original_stock)as original_stock,
                          sum(stock_remain)as stock_remain,sum(lost_qty)as lost_qty,sum(damaged_qty)as damaged_qty,sum(reserved_qty)as reserved_qty,
                          sum(on_use)as on_use from inventory_storage where utensils_id = '".$invS['utensils_id']."' and storage_id = '".$invS['storage_id']."' ");
                          foreach ($inventoryStorage as $key => $value) {
                            $currentStock = $value['stock_remain']/$value['numID'];
                          ?>

                          <td class="bg bg-primary"><?php echo $invS['original_stock']; ?></td>
                          <td class="bg bg-primary"><?php echo (round($currentStock) . "<br>"); ?></td>
                          <td class="bg bg-info"><?php echo $invS['utensils_name'] ?></td>
                          <td class="bg bg-info"><?php echo $invS['category'] ?></td>
                          <td class="bg bg-info"><?php echo $invS['model'] ?></td>
                          <td class="bg bg-info"><?php echo $invS['serial_no'] ?></td>
                          <td class="bg bg-info"><?php echo $invS['date_purchased'] ?></td>
                          <td class="bg bg-info">₱ <?php echo number_format($invS['cost'],2); ?></td>
                          <?php if ($invS['original_stock']== round($currentStock)){
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
                      <?php }
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
                                      where d.storage_id = '".$_SESSION['default_inventory_storage']."' and a.status !=0
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
                      $date1 = $_SESSION['inventory_date1'];
                      $date2 = $_SESSION['inventory_date1'];
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
                                      where d.storage_id = '".$_SESSION['default_inventory_storage']."' and a.status !=0
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
                ?>
                <?php
              } // end of default inventory all Utensils?>
              <?php if ($_SESSION['default_inventory']==2) { // if Incident reports
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
                      <th class="bg bg-info">Storage</th>
                      <th class="bg bg-success">Date verified</th>
                      <th class="bg bg-success">Verified by</th>
                      <th class="bg bg-info">Remarks</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $fetchReportMasterS = "SELECT *
                                FROM report_date_control a
                                left join reports b on a.rd_control_id = b.rd_control_id
                                where   b.report_type_id = 3 and a.date_range = CURRENT_DATE()
                                order by a.rd_control_id desc";
                       $queryString = mysqli_query($dbcon,$fetchReportMasterS);
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
                      <td ><?php echo date('M d,Y | h:i:s A',strtotime($invS['report_date'])); ?></td>
                     <?php $fetchUser = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['reported_by']."'");
                     foreach ($fetchUser as $key => $userR) {
                       ?><td><?php echo $userR['lname'] ?></td><?php
                     } ?>
                     <?php
                       $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = '".$invS['storage_id']."'");
                       foreach ($fetchStorage as $key => $stor) {
                      ?>
                      <td> <?php echo $stor['initials']; ?> </td>
                      <?php
                   }?>
                     <?php if ($invS['date_verified']==0) {
                       ?> <td></td> <?php
                     }else {
                       ?>
                       <td ><?php echo date('M d,Y | h:i:s A',strtotime($invS['date_verified'])); ?></td>

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
                         <th class="bg bg-info">Storage</th>
                         <th class="bg bg-success">Date verified</th>
                         <th class="bg bg-success">Verified by</th>
                         <th class="bg bg-info">Remarks</th>
                         <?php if ($_SESSION['account_type']==3) {
                           ?>
                           <th class="bg bg-warning">Action</th>
                           <?php
                         } ?>
                       </tr>
                     </thead>
                     <tbody>
                       <?php
                         $fetchReportMasterS = "SELECT *
                                   FROM report_date_control a
                                   left join reports b on a.rd_control_id = b.rd_control_id
                                   where   b.report_type_id = 3 order by b.report_id desc";
                          $queryString = mysqli_query($dbcon,$fetchReportMasterS);

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
                         <td ><?php echo date('M d,Y | h:i:s A',strtotime($invS['report_date'])); ?></td>
                        <?php $fetchUser = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['reported_by']."'");
                        foreach ($fetchUser as $key => $userR) {
                          ?><td><?php echo $userR['lname'] ?></td><?php
                        } ?>
                        <?php
                          $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = '".$invS['storage_id']."'");
                          foreach ($fetchStorage as $key => $stor) {
                         ?>
                         <td> <?php echo $stor['initials']; ?> </td>
                         <?php
                      }?>
                        <?php if ($invS['date_verified']==0) {
                          ?> <td></td> <?php
                        }else {
                          ?>
                          <td ><?php echo date('M d,Y | h:i:s A',strtotime($invS['date_verified'])); ?></td>
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
                         <th class="bg bg-info">Storage</th>
                         <th class="bg bg-success">Date verified</th>
                         <th class="bg bg-success">Verified by</th>
                         <th class="bg bg-info">Remarks</th>
                       </tr>
                     </thead>
                     <tbody>
                       <?php
                       $dat1 = $_SESSION['inventory_date1'];
                       $dat2 = $_SESSION['inventory_date2'];
                         $fetchReportMasterS = "SELECT *
                                   FROM report_date_control a
                                   left join reports b on a.rd_control_id = b.rd_control_id
                                   where   b.report_type_id = 3 and a.date_range  >= '$dat1' and a.date_range <= '$dat2'
                                   order by a.rd_control_id desc";
                          $queryString = mysqli_query($dbcon,$fetchReportMasterS);

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
                         <td ><?php echo date('M d,Y | h:i:s A',strtotime($invS['report_date'])); ?></td>
                        <?php $fetchUser = mysqli_query($dbcon,"SELECT * FROM users where user_id = '".$invS['reported_by']."'");
                        foreach ($fetchUser as $key => $userR) {
                          ?><td><?php echo $userR['lname'] ?></td><?php
                        } ?>
                        <?php
                          $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = '".$invS['storage_id']."'");
                          foreach ($fetchStorage as $key => $stor) {
                         ?>
                         <td> <?php echo $stor['initials']; ?> </td>
                         <?php
                      }?>
                        <?php if ($invS['date_verified']==0) {
                          ?> <td></td> <?php
                        }else {
                          ?>
                          <td ><?php echo date('M d,Y | h:i:s A',strtotime($invS['date_verified'])); ?></td>
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
                 <div id="content">
                  <div id="pageFooter">Page </div>
                </div>
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
