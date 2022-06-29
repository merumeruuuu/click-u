<?php include('header.php');
?>
<?php
//delete history
 if (isset($_GET['remove'])) {
        $id = $_GET['id'];
        $delete = mysqli_query($dbcon,"DELETE FROM history where id = $id");
} ?>
<br><br>
<div class="content">
    <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
              <div class="card ">
                        <div class="header">
                        <h4 class="title">Your Reports </h4>
                        <p class="category"></p>
                          </div>
                    <!-- <div class="row">
                    <div class="col-md-5">
                      </div>
                      <div class="col-md-4">
                        <br>
                        <input type="text"class="form-control"style="border: 1px solid; opacity: 60%;" name=""placeholder="Search history" value="">
                      </div>
                      <div class="col-md-3">
                        <div class="pu-left">
                          <br>
                          <span><select class="form-control" name="" >
                            <option value="">All Date</option>
                          </select></span>
                        </div>
                      </div>
                    </div> -->
                    <?php $user = $_SESSION['user']['user_id'];
                     if (isset($_GET['clear'])) {
                       $id = $_GET['id'];
                       $clear = mysqli_query($dbcon,"DELETE FROM history where user_id = $id ");
                     }
                    ?>
      <div class="content">
                      <div class="table-full-width">
                        <div class="content">
                          <table class="table table-hover" id="history">
                            <thead>
                              <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th></th>
                              </tr>
                            </thead>
                              <tbody>
                                <?php //if staff
                                   if ($_SESSION['account_type']==3 ||$_SESSION['account_type']==4 || $_SESSION['account_type']==5) {

                                 $storageID = $_SESSION['user']['storage_id'];

                                 $history = mysqli_query($dbcon,"SELECT * FROM history where storage_id = $storageID and user_id = $user order by id desc");
                           while ($row = mysqli_fetch_array($history)) {
                                  ?>
                                  <?php if (mysqli_num_rows($history)>0) {
                                  ?>
                                      <tr class="">
                                        <td><a href="view_history.php?id=<?php echo $row['id']; ?>">
                                          <i class="fa fa-clock-o"></i> <span><?php echo  date('M d, Y',strtotime($row['date_added']));
                                          echo " | ".date('h:i:s A',strtotime($row['date_added'])); ?></span></a>
                                        </td>
                                        <?php if ($row['history_type_id']==1) {
                                          ?>
                                            <td><a href="view_history.php?id=<?php echo $row['id']; ?>"style="color:gray;">You have released items to (Request # <?php echo $row['trans_id']; ?>) </a></td>
                                          <?php
                                        }if ($row['history_type_id']==2) {
                                          ?>
                                          <td><a href="view_history.php?id=<?php echo $row['id']; ?>"style="color:gray;">You have made changes on the (Request # <?php echo $row['trans_id']; ?>) </a></td>
                                          <?php
                                        }if ($row['history_type_id']==3) {
                                          ?>
                                          <td><a href="view_history.php?id=<?php echo $row['id']; ?>"style="color:gray;">You have received the items from (Request # <?php echo $row['trans_id']; ?>) </a></td>
                                          <?php
                                        }if ($row['history_type_id']==4) {
                                          ?>
                                           <td><a href="view_history.php?id=<?php echo $row['id']; ?>"style="color:gray;">You have made a report from (Request # <?php echo $row['trans_id']; ?>) </a></td>
                                          <?php
                                        }if ($row['history_type_id']==5) {
                                          ?>
                                          <td><a href="view_history.php?id=<?php echo $row['id']; ?>"style="color:gray;">You have approved the replacement of items from (Request # <?php echo $row['trans_id']; ?>) </a></td>
                                          <?php
                                        } ?>

                                          <td class="td-actions text-right">
                                              <a href="history.php?remove&id=<?php echo $row['id'];  ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                                  <i class="fa fa-times"></i>
                                              </a>
                                          </td>
                                      </tr>
                                    <?php }
                                      ?>

                            <?php
                            }
                          }
if ($_SESSION['account_type']==6 || $_SESSION['account_type']==7){

          $user = $_SESSION['user']['user_id'];
          $history = mysqli_query($dbcon,"SELECT * FROM history where user_id = $user order by id desc");
          while ($row = mysqli_fetch_array($history)) {
              if (mysqli_num_rows($history)>0) {

                     ?>
                        <tr class="">
                         <td><a href="view_history.php?id=<?php echo $row['id']; ?>">
                          <i class="fa fa-clock-o"></i>
                            <span><?php echo  date('M d, Y',strtotime($row['date_added']));
                              echo " | ".date('h:i:s A',strtotime($row['date_added'])); ?></span></a>
                          </td>
                          <?php
                          if ($row['history_type_id']==1) {
                             ?>
                           <td><a href="view_history.php?id=<?php echo $row['id']; ?>"style="color:gray;">You have made a request (Request # <?php echo $row['trans_id']; ?>) </a></td>
                           <?php
                         }if ($row['history_type_id']==2){
                           ?>
                           <td><a href="view_history.php?id=<?php echo $row['id']; ?>"style="color:gray;">You have cancelled your request (Request # <?php echo $row['trans_id']; ?>) </a></td>
                           <?php
                         } ?>
                         <?php
                       if ($row['history_type_id']==3){
                         ?>
                         <td><a href="view_history.php?id=<?php echo $row['id']; ?>"style="color:gray;">You have made changes on your request (Request # <?php echo $row['trans_id']; ?>) </a></td>
                         <?php
                       } ?>
                            <td class="td-actions text-right">
                              <a href="history.php?remove&id=<?php echo $row['id'];  ?>" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                               <i class="fa fa-times"></i>
                              </a>
                            </td>
                          </tr>

                                    <?php }else {
                                 ?>
                                 <tr>
                                   <td> <h5>No history..</h5> </td>
                                 </tr>
                <?php
            }
      }
}?>

                              </tbody>
                          </table>
                          </div>
                      </div>

                      <div class="footer">
                          <hr>
                          <div class="stats">
                           <a href="history.php?clear=&id=<?php echo $user; ?>"onclick="return confirm('Are you sure you want to clear history?');" style="color:gray;">  <i class="fa fa-trash"></i> Clear history</a>
                          </div>
                      </div>
   </div>
              </div>
          </div>
        </div>
    </div>
</div>
<?php include('dataTables2.php') ?>
<script type="text/javascript">
  $('#history').DataTable( {
   "pageLength": 50
   } );
</script>
<?php include('footer.php') ?>
