<?php include('header.php') ?>
<br><br>
<?php
if (isset($_GET['show_empty'])) {
  $_SESSION['show_control'] = 1;
}
if (isset($_GET['hide_empty'])) {
  unset($_SESSION['show_control']);
} ?>
<div class="content" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="header">
                          <h4 class="title"> Utensils Quantity Monitoring :</h4>
                          <!-- <p class="category">(General Storage)</p> -->
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="pull-right">
                        <div class="content">
                          <?php if (isset($_SESSION['show_control'])==1) {
                            ?>
                           <a href="?hide_empty">Hide empty storages</a>
                            <?php
                          }else {
                            ?>
                            <a href="?show_empty">Show empty storages</a><?php
                          } ?>

                        </div>
                      </div>
                    </div>
                  </div>

                    <div class="content" style="overflow-x:auto;">
                      <?php
 $queryString = "SELECT
                     a.utensils_id , a.utensils_name, a.stock_on_hand, a.umsr,a.original_stock,a.status
                    ,a.model, a.serial_no, a.date_purchased,a.utensils_cat_id,a.cost,
                     b.utensils_cat_id as catID, b.category,
                     c.id,c.umsr_name

                     FROM utensils a
                     LEFT JOIN utensils_category b ON a.utensils_cat_id = b.utensils_cat_id
                     LEFT JOIN umsr c ON a.umsr = c.id

                     where  a.status !=0
                     ORDER BY a.utensils_id ";
                     $query = mysqli_query($dbcon,$queryString);
?>
 <?php
 $query3 = mysqli_query($dbcon,"SELECT * FROM storage  where status = 1  order by storage_id");
  ?>


     <table id="UtensilTable"class="table table-striped table-bordered dataTable table-hover "cellpadding="0" cellspacing="0" border="0">
        <thead>
          <tr>
            <th>CTL NO.</th>
            <th>ITEMS W/ DESCRIPTION</th>
            <th>CATEGORY</th>
            <th>TOT-QTY</th>
            <th>CUR-QTY</th>
             <?php
             if (isset($_SESSION['show_control'])==1) {
                 foreach ($query3 as $key => $rows) {
              ?>
             <th><?php echo $rows['initials']; ?></th>
             <?php }
           }else {

             while ($rows = mysqli_fetch_array($query3)){
               $fetchEmpty = mysqli_query($dbcon,"SELECT * FROM storage_stocks where original_stock > 0 and storage_id = ".$rows['storage_id']);
               if (mysqli_num_rows($fetchEmpty)<=0) {
               }else {
               $fetchStorID = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_id = ".$rows['storage_id']);
               if (mysqli_num_rows($fetchStorID)>=1) {

              ?>
             <th><?php echo $rows['initials']; ?></th>
           <?php }
              }
           }
         }?>
            <th>On use </th>
            <th>Resereved </th>
            <th>Lost </th>
            <th>Damaged </th>
          </tr>
        </thead>
        <tbody>
          <?php while ($rows = mysqli_fetch_array($query)) {
           ?>
          <tr>
            <td><?php echo $rows['utensils_id'] ?></td>
            <td><?php echo $rows['utensils_name'] ?></td>
            <td><?php echo $rows['category'] ?></td>
            <td class="bg bg-warning"><?php echo $rows['original_stock'] ?></td>
            <?php
            $fetchEmpty = mysqli_query($dbcon,"SELECT SUM(storage_qty) as current_qty FROM storage_stocks where utensils_id = ".$rows['utensils_id']);
            foreach ($fetchEmpty as $key => $curr) {
            ?>
             <td class="bg bg-warning"><?php echo $curr['current_qty'] ?></td>
            <?php
            }
             ?>
            <?php
               $uID = $rows['utensils_id'];
               $query5 = "SELECT
                          a.utensils_id,a.storage_id,a.storage_qty,a.original_stock,
                          b.storage_id

                          from storage_stocks a
                          left join storage b on a.storage_id = b.storage_id
                          where a.utensils_id = $uID
                          order by b.storage_id";
                $check = mysqli_query($dbcon,$query5);
                if (isset($_SESSION['show_control'])==1) {
                  foreach ($check as $key => $show) {
                  ?>
                  <td ><a href="server.php?by_storage=<?php echo $show['storage_id']; ?>" ><?php echo $show['storage_qty']; ?></a></td>
                  <?php
                   }
                }else {

                while ($show = mysqli_fetch_array($check)) {
                  $fetchEmpty = mysqli_query($dbcon,"SELECT * FROM storage_stocks where original_stock > 0 and storage_id = ".$show['storage_id']);
                  if (mysqli_num_rows($fetchEmpty)<=0) {
                  }else {
                  ?>
                   <td ><a href="server.php?by_storage=<?php echo $show['storage_id']; ?>" ><?php echo $show['storage_qty']; ?></a></td>
                  <?php
                }
              }
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
              $('#UtensilTable').DataTable( {
               "pageLength": 50
               } );
            </script>
<?php include('footer.php') ?>
