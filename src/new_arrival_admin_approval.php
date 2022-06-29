<?php include('header.php'); ?>
<?php
  if(isset($_GET['verify_all']))
  {
    $_SESSION['user']['user_id'];
    $dean = $_SESSION['user']['user_id'];
    $query = mysqli_query($dbcon,"SELECT * FROM new_arrival_utensils where status = 0");
    foreach ($query as $key => $check) {
    $stock = $check['new_arvl_qty'];
    $uID = $check['utensils_id'];
    $ids = $check['new_arvl_id'];

   // $res = mysqli_query($dbcon,"UPDATE `utensils` SET `stock_on_hand`='$stock' WHERE `utensils`.`utensils_id`= $uID");
   $fetchStorage = mysqli_query($dbcon,"SELECT * FROM storage order by storage_id ");
   foreach ($fetchStorage as $key => $value) {
     $insertToStorage = mysqli_query($dbcon,"INSERT INTO storage_stocks (utensils_id,storage_id,storage_qty,original_stock,reserved_qty,lost_qty,damaged_qty,on_use)
     values('$uID','".$value['storage_id']."',0,0,0,0,0,0)")or die("details: ".mysqli_error($dbcon));
   }
   $fetchNewStock = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = $uID and storage_id = 1");
   foreach ($fetchNewStock as $key => $newStock){
   $original_stock = $newStock['original_stock'] + $stock;
   $storageQty = $newStock['storage_qty'] + $stock;
   $updateStats = mysqli_query($dbcon,"UPDATE utensils set status = 1 where utensils_id = $uID");
   $updateQTY = mysqli_query($dbcon,"UPDATE storage_stocks set original_stock = $original_stock,storage_qty = $storageQty where utensils_id = $uID and storage_id = 1");
   $result = mysqli_query($dbcon,"UPDATE `new_arrival_utensils` SET `approved_by`='$dean',`date_approved`= NOW(),`status` = '1' WHERE `new_arrival_utensils`.`new_arvl_id`= $ids");
    }
   }
       echo "<script>alert('Successfully verified!');window.location.href='new_arrival_admin_approval.php';</script>";
       // header("Location:new_arrival_admin_approval.php");
  }
 ?>
<br><br>
<div class="content">
    <div class="container-fluid">
      <div class="row">
          <div class="col-md-12">
              <div class="card">
                <div class="row">
                  <div class="col-md-6">
                    <div class="header">
                        <h4 class="title bol">New Items : </h4>
                        <p class="category">(For approval)</p>
                        <hr>
                    </div>
                  </div>
                  <div class="col-md-6 ">
                    <div class="header pull-right">
                      <?php
                       $query = mysqli_query($dbcon,"SELECT * FROM new_arrival_utensils where status = 0");
                      if (mysqli_num_rows($query)>0) {
                        ?><a href="?verify_all" class="btn btn-info btn-sm btn-fill"onclick="return confirm('Confirm verify all!')"><i class="fa fa-check"></i> Verify all</a><?php
                      } ?>
                        <hr>
                    </div>
                  </div>
                </div>

                  <div class="content" style="overflow-x:auto;">
<?php
$queryString = "SELECT
                    a.utensils_id , a.utensils_name, a.umsr
                    , a.model, a.serial_no, a.date_purchased,a.utensils_cat_id,a.cost,
                    b.utensils_cat_id as catID, b.category,
                    c.new_arvl_id,c.date_received,c.new_arvl_qty,c.utensils_id,c.received_by,c.checked_by,c.approved_by,
                    d.id,d.umsr_name

                    FROM utensils a
                    LEFT JOIN utensils_category b ON a.utensils_cat_id = b.utensils_cat_id
                    LEFT JOIN new_arrival_utensils c ON a.utensils_id = c.utensils_id
                    LEFT JOIN umsr d ON a.umsr = d.id
                    where c.status = 0
                    ORDER BY a.utensils_id ";
                    $query = mysqli_query($dbcon,$queryString);
                    ?><?php
 ?>
    <table id="UtensilTable"class="table table-bordered dataTable">
       <thead>
         <tr>
           <th>CTL NO.</th>
           <th>UMSR</th>
           <th>ITEMS W/ DESCRIPTION</th>
           <th>CATEGORY</th>
           <th>QTY</th>
           <th>MODEL</th>
           <th>SERIAL NO.</th>
           <th>DATE PURCHASED</th>
           <th>DATE RECEIVED</th>
           <th>UNIT COST</th>
           <th>RECEIVED BY</th>
           <th>CHECKED BY</th>
           <th>ACTION</th>
         </tr>
       </thead>
       <tbody>
         <?php while ($rows = mysqli_fetch_array($query)) {
          ?>
         <tr>
           <td><?php echo $rows['utensils_id'] ?></td>
           <td><?php echo $rows['umsr_name'] ?></td>
           <td><?php echo $rows['utensils_name'] ?></td>
           <td><?php echo $rows['category'] ?></td>
           <td><?php echo $rows['new_arvl_qty'] ?></td>
           <td><?php echo $rows['model'] ?></td>
           <td><?php echo $rows['serial_no'] ?></td>
           <td><?php echo $rows['date_purchased'] ?></td>
           <td><?php echo $rows['date_received'] ?></td>
           <td>₱ <?php echo number_format($rows['cost'],2) ?></td>
           <?php
           $fetchUser = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$rows['received_by']);
           foreach ($fetchUser as $key => $value) {
            ?>
           <td><?php echo $value['lname'] ?></td>
            <?php
           } ?>
           <?php
           $fetchUser = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$rows['checked_by']);
           foreach ($fetchUser as $key => $value) {
            ?>
           <td><?php echo $value['lname'] ?></td>
            <?php
           } ?>
           <td><a href="server.php?confirm=<?php echo $rows['new_arvl_id'];?>"class="btn btn-success btn-sm btn-fill"><i class="fa fa-check"></i> Verify </a>
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

          $('#UtensilTable').DataTable( {
           "pageLength": 50
           } );

        </script>
<?php include 'footer.php' ?>
