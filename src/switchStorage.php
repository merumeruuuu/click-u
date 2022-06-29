<?php include('header.php') ?>

<br><br>
<div class="content" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="header">
                    <?php $switchStorage = $_SESSION['switch_storage'];
                     $switch_query = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $switchStorage");
                     $showStorage = mysqli_fetch_array($switch_query);
                    ?>

                        <h4 class="title"><?php echo $showStorage['storage_name']; ?> Quantity Monitoring :</h4>
                        <!-- <p class="category">(General Storage)</p> -->
                        <br> <br>
                        <span> <a href="kitchen_staff_home.php"><i class="fa fa-chevron-left"></i> Back</a> </span>
                    </div>

                    <div class="content" style="overflow-x:auto;">

                                            <?php
                       $queryString = "SELECT
                                           a.utensils_id , a.utensils_name, a.stock_on_hand, a.umsr,a.original_stock
                                          ,a.model, a.serial_no, a.date_purchased,a.utensils_cat_id,a.cost,
                                           b.utensils_cat_id as catID, b.category,
                                           c.storage_id,sum(c.original_stock) as origStock,c.storage_qty,c.utensils_id,
                                           d.id,d.umsr_name

                                           FROM utensils a
                                           LEFT JOIN utensils_category b ON a.utensils_cat_id = b.utensils_cat_id
                                           LEFT JOIN storage_stocks c ON a.utensils_id = c.utensils_id
                                           LEFT JOIN umsr d ON a.umsr = d.id
                                           where c.original_stock > 0 and c.storage_id = '".$showStorage['storage_id']."'
                                           group BY c.utensils_id ";
                                           $query = mysqli_query($dbcon,$queryString);
                      ?>



                           <table id="UtensilTable"class="table table-striped table-bordered dataTable table-hover "cellpadding="0" cellspacing="0" border="0">
                              <thead>
                                <tr>
                                  <th>CTL NO.</th>
                                  <th class="bg bg-info">ITEMS W/ DESCRIPTION</th>
                                  <th class="bg bg-info">CATEGORY</th>
                                  <th class="bg bg-warning">ORIGINAL QTY</th>
                                  <th class="bg bg-success">AVAILABLE</th>
                                  <th class="bg bg-info">On use </th>
                                  <th class="bg bg-info">Resereved </th>
                                  <th class="bg bg-danger">Lost </th>
                                  <th class="bg bg-danger">Damaged </th>
                                  <!-- <th>UMSR</th>
                                  <th>MODEL</th>
                                  <th>SERIAL NO.</th>
                                  <th>DATE PURCHASED</th>
                                  <th>UNIT COST</th> -->
                                </tr>
                              </thead>
                              <tbody>
                                <?php while ($rows = mysqli_fetch_array($query)) {
                                 ?>
                                <tr>
                                  <td><?php echo $rows['utensils_id'] ?></td>
                                  <td><?php echo $rows['utensils_name'] ?></td>
                                  <td><?php echo $rows['category'] ?></td>
                                  <?php
                                     $uID = $rows['utensils_id'];
                                      $check = mysqli_query($dbcon,"SELECT * FROM  storage_stocks where utensils_id = $uID and storage_id = $switchStorage and original_stock > 0");
                                      while ($show = mysqli_fetch_array($check)) {
                                        ?>
                                        <td><?php echo $show['original_stock'] ?></td>
                                        <td><?php echo $show['storage_qty']; ?></td>
                                        <?php
                                      }
                                   ?>
                                   <?php
                                    $count = "SELECT
                                                   a.utensils_id,a.stock_on_hand,a.status,
                                                   b.utensils_id,sum(b.lost_qty)as lost_qty,sum(b.damaged_qty)as damaged_qty,sum(b.on_use)as on_use,sum(b.reserved_qty)as reserved_qty

                                                    from utensils a
                                                    left join storage_stocks b on a.utensils_id = b.utensils_id
                                                    where a.utensils_id = '".$rows['utensils_id']."'";
                                     $counts = mysqli_query($dbcon,$count);
                                         while ($row =mysqli_fetch_array($counts)) {
                                          ?>
                                      <td class="bg bg-info"><?php if($row['on_use']==0){ echo '';}else{ echo $row['on_use']; }?></td>
                                      <td class="bg bg-info"><?php if($row['reserved_qty']==0){ echo '';}else{ echo $row['reserved_qty'];} ?></td>
                                      <td class="bg bg-danger"><?php if($row['lost_qty']==0){ echo '';}else{ echo $row['lost_qty'];} ?></td>
                                      <td class="bg bg-danger"><?php if($row['damaged_qty']==0){ echo '';}else{ echo $row['damaged_qty'];} ?></td>
                                          <?php
                                         } ?>
                                </tr>
                              <?php
                                   }?>
                              </tbody>
                           </table>

                   </div>
              </div>
              </div>
            </div>
          </div>
    </div>
    <?php include('dataTables2.php') ?>
    <script type="text/javascript">
              $('#UtensilTable').DataTable();

            </script>
<?php include('footer.php') ?>
