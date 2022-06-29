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
        <div class="row">
          <div class="col-md-12">
              <div class="card ">
                        <div class="header">
                        <h4 class="title"> Inventory </h4>
                        <p class="category"> </p>
                          </div>
                    <div class="content">
                      <div class="row">
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
                      <div class="col-md-3">
                        <br>
                        <span><select class="form-control" name="inventory_ses"id="inventory_ses"onchange="session_inventory(this.value)">
                          <option value="<?php echo $all_inventory; ?>"<?php if ($_SESSION['default_inventory'] == 1) {  echo $selected;  } ?>> Inventory</option>
                          <option value="<?php echo $new_arrival; ?>"<?php if ($_SESSION['default_inventory'] == 2) { echo $selected; } ?>>  New arrival</option>
                        </select></span>
                        </div>
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
                            <a href="#" class="btn btn-fill btn-success">Generate Report</a>
                          </div>
                      </div>
                    </div>
                  <?php if ($_SESSION['default_inventory']==1) { // if all utensils and current date
                    ?>
                     <?php $storID = $_SESSION['default_inventory_storage'];
                     if ($_SESSION['default_inventory_storage']==10102) {  //if all utensils
                    if ($_SESSION['default_inventory_date']==1) { //if current date
                         ?>
                         <div class="content">
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
                           </div>
                               <?php
                    }
                    if ($_SESSION['default_inventory_date']==2) { //if all utensils and all date
                         ?>
                         <div class="content">
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
                            where `utensils`.`utensils_id` = '".$value['utensils_id']."'";
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
                        </div>
                        <?php
                    }
                    if ($_SESSION['default_inventory_date']==3) { //if all utensils and custom date
                      ?>
                      <div class="content">
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
                         where `utensils`.`utensils_id` = '".$value['utensils_id']."'";
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
                  </div>
                      <?php
                    }?>

                  <?php
                }if ($_SESSION['default_inventory_storage']==101001) { // if all storages and current date
                  if ($_SESSION['default_inventory_date']==1) { //if current date
                    ?>
                    <div class="content">
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

                                     <?php $inventoryD = mysqli_query($dbcon,"SELECT sum(storage_qty)as remain_s,
                                      sum(lost_qty)as lost_qty,sum(damaged_qty)as damaged_qty,sum(reserved_qty)as reserved_qty,sum(on_use)as on_use
                                      FROM storage_stocks where utensils_id = '".$value['utensils_id']."'");
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
                                                   a.utensils_id,a.storage_id,a.stock_remain,a.inventory_control_id,
                                                   b.storage_id

                                                   from inventory_storage a
                                                   left join storage b on a.storage_id = b.storage_id
                                                   where a.utensils_id = '".$value['utensils_id']."' and a.inventory_control_id = '".$value['inventory_control_id']."'
                                                   order by b.storage_id";
                                         $check = mysqli_query($dbcon,$query5);
                                         while ($show = mysqli_fetch_array($check)) {
                                           ?>
                                            <td class="bg bg-primary"><?php echo $show['stock_remain']; ?></td>
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
                    <?php
                  }
                  ?>

                  <?php
                }if ($_SESSION['default_inventory_storage']!=101001 && $_SESSION['default_inventory_storage']!=10102) {
                  if ($_SESSION['default_inventory_date']==1) {
                  ?>
                  <div class="content">
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
                                      d.inventory_control_id,d.utensils_id,d.original_stock,d.stock_remain,d.lost_qty,d.damaged_qty,d.reserved_qty,
                                      d.on_use,d.storage_id,
                                      e.inventory_control_id,e.date_added

                                      from utensils a
                                      left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                                      left join umsr c on a.umsr = c.id
                                      left join inventory_storage d on a.utensils_id = d.utensils_id
                                      left join inventory e on d.inventory_control_id = e.inventory_control_id
                                      where e.date_added = CURRENT_DATE() and a.status != 0 and d.storage_id = '".$_SESSION['default_inventory_storage']."'
                                      order by a.utensils_id
                                      ";
                    $result = mysqli_query($dbcon,$queryString);
                                       foreach ($result as $key => $value) {
                                      ?>
                                 <tr>
                                   <td class="bg bg-info"><?php echo $value['utensils_id']; ?></td>
                                   <td class="bg bg-warning"><?php echo $value['original_stock']; ?></td>
                                   <?php $inventoryD = mysqli_query($dbcon,"SELECT sum(storage_qty)as remain_s,
                                    sum(lost_qty)as lost_qty,sum(damaged_qty)as damaged_qty,sum(reserved_qty)as reserved_qty,sum(on_use)as on_use
                                    FROM storage_stocks where utensils_id = '".$value['utensils_id']."'");
                                    foreach ($inventoryD as $key => $invD) {
                                      $currentStock = $value['stock_remain'] + $invD['remain_s']  + $invD['reserved_qty'];
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

                        <?php }
                        ?>
                      </tbody>
                    </table>
                    </div>
                  <?php
                  }
                }
                }if ($_SESSION['default_inventory']==2) {
                  ?>
                  <div class="content">
                   <table class="table table-bordered table-striped table-hover" align="center">
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
                       <?php $storID = $_SESSION['default_inventory_storage'];
                             if ($_SESSION['default_inventory_date']==1) {
                               if ($_SESSION['default_inventory_storage']==10102) {
                                 $queryString = "SELECT * from borrower_slip a
                                  where a.storage_id = $storID and a.status != 1 and a.status !=3 and a.date_requested >= CURRENT_DATE()
                                  order by a.borrower_slip_id desc";
                               }else {
                                 $queryString = "SELECT * from borrower_slip a
                                  where  a.status != 1 and a.status !=3 and a.date_requested >= CURRENT_DATE()
                                  order by a.borrower_slip_id desc";
                               }
                             }if ($_SESSION['default_inventory_date']==2) {
                               if ($_SESSION['default_inventory_storage']==10102) {
                                 $queryString = "SELECT * from borrower_slip a
                                  where   a.status != 1 and a.status !=3
                                  order by a.borrower_slip_id desc";
                               }else {
                                 $queryString = "SELECT * from borrower_slip a
                                  where  a.storage_id = $storID and a.status != 1 and a.status !=3
                                  order by a.borrower_slip_id desc";
                               }
                             }if ($_SESSION['default_inventory_date']==3) {
                               if ($_SESSION['default_inventory_storage']==10102) {
                                 $dat1 = $_SESSION['inventory_date1'];
                                 $dat2 = $_SESSION['inventory_date2'];
                                 $queryString = "SELECT * from borrower_slip a
                                  where   a.status != 1 and a.status !=3 and a.date_requested >= '$dat1' and a.date_requested <= '$dat2'
                                  order by a.borrower_slip_id desc";
                               }else {
                                 $dat1 = $_SESSION['inventory_date1'];
                                 $dat2 = $_SESSION['inventory_date2'];
                                 $queryString = "SELECT * from borrower_slip a
                                  where  a.storage_id = $storID and a.status != 1 and a.status !=3 and a.date_requested >= '$dat1' and a.date_requested <= '$dat2'
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
                }
                ?>

              </div>
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

<?php include('dataTables2.php'); ?>
<script type="text/javascript">
<?php if ($_SESSION['default_inventory_date']==3) {
  ?>
  $('#ALL_UTENSILS').DataTable( {
   "pageLength": 50
   } );
  <?php
} ?>
<?php if ($_SESSION['default_inventory_date']==1) {
  ?>
  $('#ALL_UTENSILS').DataTable( {
   "pageLength": 50
   } );
  <?php
}if ($_SESSION['default_inventory_date']==2) {
  ?>
  $('#ALL_UTENSILS').dataTable({
   paging: false
  });
  <?php
} ?>


</script>
<?php include('footer.php') ?>
