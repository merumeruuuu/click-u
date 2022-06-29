  <?php include('header.php'); ?>
<br><br>

<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">New Arrival Items</h4>
                                <hr>
                            </div>
                            <div class="content" style="overflow-x:auto;">

 <?php
 $queryString = "SELECT
                     a.utensils_id , a.utensils_name, a.umsr,a.stock_on_hand,a.original_stock
                     , a.model, a.serial_no, a.date_purchased,a.utensils_cat_id,a.cost,
                     b.utensils_cat_id as catID, b.category,
                     c.date_received,c.new_arvl_qty,c.utensils_id,c.received_by,c.checked_by,c.approved_by,c.date_approved,
                     d.id,d.umsr_name

                     FROM utensils a
                     LEFT JOIN utensils_category b ON a.utensils_cat_id = b.utensils_cat_id
                     LEFT JOIN new_arrival_utensils c ON a.utensils_id = c.utensils_id
                     LEFT JOIN umsr d ON a.umsr = d.id

                     ORDER BY a.utensils_id ";
                     $query = mysqli_query($dbcon,$queryString);

                     ?><?php

 $query3 = mysqli_query($dbcon,"SELECT * FROM storage  order by storage_id");


  ?>

       <table id="UtensilTable"class="table table-striped table-bordered dataTable table-hover "cellpadding="0" cellspacing="0" border="0">
        <thead class="">
          <tr >
            <th>Action</th>
            <th>CTL NO.</th>
            <th>QTY</th>
            <th>UMSR</th>
            <th>ITEMS W/ DESCRIPTION</th>
            <th>CATEGORY</th>
            <th>MODEL</th>
            <th>SERIAL NO.</th>
            <th>DATE PURCHASED</th>
            <th>DATE RECEIVED</th>
            <th>DATE APPROVED</th>
            <th>UNIT COST</th>
            <th>RECEIVED BY</th>
            <th>APPROVED BY</th>
          </tr>
        </thead class="">
        <tbody>
          <?php while ($rows = mysqli_fetch_array($query)) {
            if (!empty($rows['approved_by'])&&$rows['new_arvl_qty']>=1) {
           ?>
          <tr >
            <td  class="bg bg-primary"><a href="move_to_storages.php?move=<?php echo $rows['utensils_id'];?>">Manage</a></td>
            <td class="bg bg-info"><?php echo $rows['utensils_id'] ?></td>
            <td class="bg bg-info"><?php echo $rows['new_arvl_qty'] ?></td>
            <td class="bg bg-info"><?php echo $rows['umsr_name'] ?></td>
            <td class="bg bg-info"><?php echo $rows['utensils_name'] ?></td>
            <td class="bg bg-info"><?php echo $rows['category'] ?></td>
            <td class="bg bg-info"><?php echo $rows['model'] ?></td>
            <td class="bg bg-info"><?php echo $rows['serial_no'] ?></td>
            <td><?php echo date('M d,y',strtotime($rows['date_purchased'])) ?></td>
            <td><?php echo date('M d,y',strtotime($rows['date_received'])) ?></td>
            <td><?php echo date('M d,y',strtotime($rows['date_approved'])) ?></td>
            <td class="bg bg-info">₱<?php echo $rows['cost'] ?></td>
            <?php $fetchUsers = mysqli_query($dbcon,"SELECT * FROM users where   user_id = '".$rows['received_by']."' ");
            foreach ($fetchUsers as $key => $value) {
               ?>
               <td class="bg bg-warning"><?php echo $value['lname'] ?> ,<?php echo $value['fname'] ?></td>
              <?php
            } ?>
            <?php $fetchUsers = mysqli_query($dbcon,"SELECT * FROM users where  user_id = '".$rows['approved_by']."'  ");
            foreach ($fetchUsers as $key => $value) {
               ?>
               <td class="bg bg-warning"><?php echo $value['lname'] ?> ,<?php echo $value['fname'] ?></td>
              <?php
            } ?>
          </tr>
        <?php
              }
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
            $('#UtensilTable').DataTable( {
             "pageLength": 50
             } );
          </script>
  <?php include('footer.php'); ?>
