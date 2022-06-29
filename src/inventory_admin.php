<?php include('header.php');
?>

<script type="text/javascript">

// session_inventory_date
function session_inventory_date(value) {
        $.ajax({
            type: "POST",
            url: 'ajaxrequest/sessionInventory_admin.php',
            data: 'inventory_ses_date=' + value,
            dataType: 'json',
            success: function (data) {
              if (data==1) {
                // location.reload();
                location.href = 'inventory_admin.php';
                setInterval( 1000);
              }
            }
        });
    }
  function session_inventory_storage(value) {
          $.ajax({
              type: "POST",
              url: 'ajaxrequest/sessionInventory_admin.php',
              data: 'inventory_ses_storage=' + value,
              dataType: 'json',
              success: function (data) {
                if (data==1) {
                  // location.reload();
                  location.href = 'inventory_admin.php';
                  setInterval( 1000);
                }
              }
          });
      }
//session reports
  function session_inventory(value) {
          $.ajax({
              type: "POST",
              url: 'ajaxrequest/sessionInventory_admin.php',
              data: 'inventory_ses=' + value,
              dataType: 'json',
              success: function (data) {
                if (data==1) {
                  // location.reload();
                  location.href = 'inventory_admin.php';
                  setInterval( 1000);
                }
              }
          });
      }
</script>
<?php
if (isset($_GET['inventory_rep'])) {

 $_SESSION['default_inventory'] = 1;
 $_SESSION['default_inventory_storage'] = 10102;
 $_SESSION['default_control_inventory'] = 0;
 $_SESSION['default_inventory_date'] = 1;
 $_SESSION['inventory_date1'] = '00/00/0000';
 $_SESSION['inventory_date2'] = '00/00/0000';

}
if (isset($_GET['close_modal_inventory'])) {
 $_SESSION['default_control_inventory'] = 1;
 $_SESSION['default_inventory_date'] = 1;
}
  $all_inventory = 1;
  $new_arrival = 2;

  $inventory_current_date = 1;
  $all_inventory_date = 2;
  $custom_inventory_date = 3;
  $all_storage = 101001;
  $all_utensils = 10102;
  $selected = "selected";
 ?>
<br><br>
<div class="content">
    <div class="container-fluid">

              <div class="card ">
                        <div class="header">
                        <h4 class="title"> Inventory </h4>
                        <p class="category"> </p>
                          </div>
                    <div class="content">
                      <div class="row">
                        <div class="col-md-3">
                          <br>
                          <span><select class="form-control" name="inventory_ses"id="inventory_ses"onchange="session_inventory(this.value)">
                            <option value="<?php echo $all_inventory; ?>"<?php if ($_SESSION['default_inventory'] == 1) {  echo $selected;  } ?>> Inventory</option>
                            <option value="<?php echo $new_arrival; ?>"<?php if ($_SESSION['default_inventory'] == 2) { echo $selected; } ?>>  Incident Reports</option>
                          </select></span>
                          </div>
                          <?php if ($_SESSION['default_inventory'] == 1) {
                        ?>
                        <div class="col-md-3">
                          <br>
                          <span><select class="form-control" name="inventory_ses_storage"id="inventory_ses_storage"onchange="session_inventory_storage(this.value)">
                            <option value="<?php echo $all_utensils; ?>"<?php if ($_SESSION['default_inventory_storage'] == 10102) {  echo $selected;  } ?>>  All Utensils</option>
                            <option value="<?php echo $all_storage; ?>"<?php if ($_SESSION['default_inventory_storage'] == 101001) {  echo $selected;  } ?>>  All Storages</option>
                            <?php $storages = mysqli_query($dbcon,"SELECT * FROM storage ");
                                  foreach ($storages as $key => $stor) {
                                    ?>
                             <option value="<?php echo $stor['storage_id']; ?>"<?php if ($stor['storage_id'] == $_SESSION['default_inventory_storage'] ) { echo $selected; } ?>> <?php echo $stor['storage_name']; ?></option>
                                    <?php
                                  } ?>

                          </select></span>
                          </div>
                           <?php } ?>
                        <div class="col-md-3">
                          <br>
                          <select class="form-control"  name="inventory_ses_date"id="inventory_ses_date"onchange="session_inventory_date(this.value)">
                            <option value="<?php echo  $inventory_current_date; ?>"<?php if ($_SESSION['default_inventory_date'] == 1) {  echo $selected;  } ?>>  Today</option>
                            <option value="<?php echo $all_inventory_date; ?>"<?php if ($_SESSION['default_inventory_date'] == 2) { echo $selected; } ?>>  All Date</option>
                            <option value="<?php echo $custom_inventory_date; ?>"<?php if ($_SESSION['default_inventory_date'] == 3) { echo $selected; } ?> data-toggle="modal" data-target="#myModal1">  Custom Date</option>
                          </select>
                        </div>

                          <div class="col-md-3">
                            <br>
                            <!-- <button class="btn btn-fill btn-success"data-toggle="modal" data-target="#myModal2">Generate Report</button> -->
                            <a href="temporary.php"class="btn btn-fill btn-success">Generate Report</a>
                          </div>
                      </div>
                    </div>
          <div class="content">
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
                                  <?php
                                  $fetchEmpty = mysqli_query($dbcon,"SELECT SUM(storage_qty) as current_qty FROM storage_stocks where utensils_id = ".$value['utensils_id']);
                                  foreach ($fetchEmpty as $key => $curr) {
                                  ?>
                                   <td class="bg bg-warning"><?php echo $curr['current_qty'] ?></td>
                                  <?php
                                  }
                                   ?>
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
                                       <?php
                                       $fetchEmpty = mysqli_query($dbcon,"SELECT SUM(storage_qty) as current_qty FROM storage_stocks where utensils_id = ".$value['utensils_id']);
                                       foreach ($fetchEmpty as $key => $curr) {
                                       ?>
                                        <td class="bg bg-warning"><?php echo $curr['current_qty'] ?></td>
                                       <?php
                                       }
                                        ?>
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
                                         <?php
                                         $fetchEmpty = mysqli_query($dbcon,"SELECT SUM(storage_qty) as current_qty FROM storage_stocks where utensils_id = ".$value['utensils_id']);
                                         foreach ($fetchEmpty as $key => $curr) {
                                         ?>
                                          <td class="bg bg-warning"><?php echo $curr['current_qty'] ?></td>
                                         <?php
                                         }
                                          ?>
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
                                    where d.storage_id = '".$_SESSION['default_inventory_storage']."' and a.status !=0 and d.original_stock != 0 and d.inventory_control_id = '".$value['inventory_control_id']."'
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

          </div>

         </div>
     </div>
</div>

<!-- Mini Modal -->
<div class="modal <?php if ($_SESSION['default_inventory_date']==3&&$_SESSION['default_control_inventory']== 0) {
  echo "show";
 }if($_SESSION['default_inventory_date']==3&&$_SESSION['default_control_inventory']== 1) {
  echo "fade";
 } ?>  modal-primary" id="myModal1" data-backdrop="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
              <span >Select custom dates</span>
              <a href="inventory_admin.php?close_modal_inventory
    " class="close" data-dismiss="modal">&times;</a>
            </div>
            <form class="" action="inventory_admin.php" method="post">
            <div class="modal-body ">
              <div class="content">
                  <div class="pull-center">
                  <label for="">From :</label>
                  <input type="date"class="form-control" name="inventory_date1" value=""required>
                  <br>
                  <label for="">To :</label>
                  <input type="date"class="form-control" name="inventory_date2" value=""required>
                </div>
            </div>
       </div>
            <div class="modal-footer">
                <button type="submit" name="confirm_inventory_date"class="btn btn-sm btn-info btn-fill">Confirm</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!--  End Modal -->


<script src='dist/jspdf.min.js'></script>
<script src="html2pdf.bundle.min.js"></script>

<?php include('dataTables2.php'); ?>
<script type="text/javascript">
$('#ALL_UTENSILS').DataTable( {
 "pageLength": 50
 } );


 var doc = new jsPDF();
 var specialElementHandlers = {
     '#editor': function (element, renderer) {
         return true;
     }
 };

 $('#cmd').click(function () {
     doc.fromHTML($('#smdiv').html(), 15, 15, {
         'width': 170,
             'elementHandlers': specialElementHandlers
     });
     doc.save('sample-file.pdf');
 });
</script>
<?php include('footer.php') ?>
