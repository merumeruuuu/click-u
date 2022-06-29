<?php include('header.php') ?>
<br><br>
<?php
$errors = array();
if (isset($_GET['manage_stor'])) {
  $_SESSION['storage_control'] = 0;
  $_SESSION['storage_control2'] = 0;
}
if (isset($_GET['storage_controlx'])) {
  $_SESSION['storage_control'] = 1;
}
if (isset($_GET['close_modal'])) {
  $_SESSION['storage_control'] = 0;
  $_SESSION['storage_control2'] = 0;
}
if (isset($_POST['add_storage'])) {
  $stname = mysqli_real_escape_string($dbcon,$_POST['storage_name']);
  $inls = mysqli_real_escape_string($dbcon,$_POST['initials']);
  $storage = trim($stname);
  $initials = trim($inls);
  $trapString = mysqli_query($dbcon,"SELECT * FROM storage where storage_name = '$storage'or initials = '$initials'");
  if (mysqli_num_rows($trapString)>=1) {
    array_push($errors,"Storage already exists!");
  }else {
    if (empty($storage)) {
      array_push($errors,"Please fill in fields!");
    }else {
      $insertStorage = mysqli_query($dbcon,"INSERT INTO storage (storage_name,initials) VALUES ('$stname','$inls')");
      array_push($success,"New storage added!");
      $_SESSION['storage_control'] = 0;
    }
}
}
if (isset($_GET['remove_storage'])) {
  $storID = $_GET['remove_storage'];
  // $removeCat = mysqli_query($dbcon,"DELETE FROM storage where storage_id = $storID");
  // $removeCatS = mysqli_query($dbcon,"DELETE FROM storage_stocks where storage_id = $storID");
  $fetchStID = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_id = $storID");
  if (mysqli_num_rows($fetchStID)>=1) {
    array_push($errors,"Cannot delete active storage!");
  }else {
  $removeCat = mysqli_query($dbcon,"DELETE FROM storage where storage_id = $storID");
  }
}
if (isset($_GET['update_stor'])) {
  $storID = $_GET['update_stor'];
  $fetchStID = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $storID");
  foreach ($fetchStID as $key => $value);
  $_SESSION['update_name'] = $value['storage_name'];
  $_SESSION['update_initials'] = $value['initials'];
  $_SESSION['storage_id'] = $value['storage_id'];
  $_SESSION['storage_control2'] = 1;
}
if (isset($_POST['update_storage'])) {
  $str1 = $_POST['storage_name'];
  $str2 = $_POST['initials'];
  $trapString = mysqli_query($dbcon,"SELECT * FROM storage where storage_name like '%$str1%' or initials like '%$str2%'");
  if (mysqli_num_rows($trapString)>=1) {
    array_push($errors,"Storage already exists!");
  }else {
    $updateStor = mysqli_query($dbcon,"UPDATE storage set storage_name = '$str1',initials = '$str2' where storage_id = '".$_SESSION['storage_id']."'");
    array_push($success,"Storage name successfully updated!");
      $_SESSION['storage_control2'] = 0;
  }

}?>
<?php
if (isset($_GET['activate_storage'])) {
  $storID = $_GET['activate_storage'];
  $fetchAllUtensils = mysqli_query($dbcon,"SELECT * FROM utensils ");
  foreach ($fetchAllUtensils as $key => $value) {
    $insertToStorage = mysqli_query($dbcon,"INSERT INTO storage_stocks (utensils_id,storage_id,storage_qty,original_stock,reserved_qty,lost_qty,damaged_qty,on_use)
    values('".$value['utensils_id']."','$storID',0,0,0,0,0,0)")or die("details: ".mysqli_error($dbcon));
  }
  $updateStats = mysqli_query($dbcon,"UPDATE storage set status = 1 where storage_id = $storID");
  array_push($success,"Successfully activated!");
}
 ?>
<div class="content" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Storages</h4>
                        <!-- <p class="storage">(General Storage)</p> -->
                    </div>
                    <div class="content" style="overflow-x:auto;">
                      <div class="row">
                        <div class="col-md-7">
                          <?php include('success.php'); ?>
                          <?php
                          if ($_SESSION['storage_control2'] == 1||$_SESSION['storage_control'] == 1) {
                            // code...
                          }else {
                              include('errors.php');
                          }
                         ?>
                        </div>
                        <div class="col-md-5">
                            <a href="?storage_controlx" class="btn btn-success btn-fill pull-right"><i class="fa fa-plus"></i> Add new Storage</a>
                        </div>
                      </div>
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>Status</th>
                          <th>Storage Name</th>
                          <th>Initials</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $fetchstorage = "SELECT *
                                    FROM storage
                                    order by storage_id  ";
                        $result = mysqli_query($dbcon,$fetchstorage);
                        foreach ($result as $key => $value) {
                          ?>
                          <tr>
                            <td> <?php $check = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_id = ".$value['storage_id']);
                            if (mysqli_num_rows($check)>0) {
                              ?>
                              <i class="fa fa-circle text-success"></i> Active
                              <?php
                            }else {
                              ?>
                              <i class="fa fa-circle text-warning"></i> Inactive
                              <?php
                            } ?> </td>
                            <td><?php echo $value['storage_name'] ?></td>
                            <td><?php echo $value['initials'] ?></td>
                            <td>
                              <span>
                            <a href="?update_stor=<?php echo $value['storage_id']; ?>"class=""><i class="fa fa-pencil text-info"></i></a>
                            </span>
                          </td>
                          <td>
                            <span>
                              <?php $check = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_id = ".$value['storage_id']);
                              if (mysqli_num_rows($check)<=0) {
                                ?>
                                <span>
                              <a href="?activate_storage=<?php echo $value['storage_id']; ?>"onclick="return confirm('Are you sure you want to activate?')">   activate </a>
                              </span>
                              |
                              <span>
                              <a href="?remove_storage=<?php echo $value['storage_id']; ?>"class=""> remove </a>
                              </span>
                                <?php
                              }
                                ?>
                            </span>
                          </td>
                          </tr>
                          <?php
                        } ?>

                      </tbody>
                    </table>
                   </div>
              </div>
              </div>
            </div>
          </div>
    </div>
    <!-- Mini Modal -->
<div class="modal <?php if ($_SESSION['storage_control']==1) {
      echo "show";
    }if($_SESSION['storage_control']==0) {
      echo "fade";
     } ?>  modal-primary" id="myModal1" data-backdrop="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                  <span >Add new storage</span>
                  <a href="manage_storages.php?close_modal" class="close" data-dismiss="modal">&times;</a>
                </div>
                <form class="" action="manage_storages.php" method="post">
                <div class="modal-body ">
                  <div class="content">
                    <div class="row">
                      <div class="col-md-8">
                        <div class="pull-center">
                        <label for="">Storage name :</label>
                        <input type="text"class="form-control" name="storage_name" value=""required>
                        <br>
                      </div>
                      </div>
                      <div class="col-md-4">
                        <div class="pull-center">
                        <label for="">Initials :</label>
                        <input type="text"class="form-control" name="initials" value=""required>
                        <br>
                      </div>
                      </div>
                    </div>
                </div>
           </div>
           <div class="row">
             <div class="col-md-6">
               <?php include 'errors.php'; ?>
             </div>
             <div class="col-md-6">
               <div class="modal-footer">
                   <button type="submit" name="add_storage"class="btn btn-sm btn-info btn-fill">Add</button>
               </div>
             </div>
           </div>
                </form>
            </div>
        </div>
    </div>
    <!--  End Modal -->

            <!-- Mini Modal -->
  <div class="modal <?php if ($_SESSION['storage_control2']==1) {
              echo "show";
            }if($_SESSION['storage_control2']==0) {
              echo "fade";
             } ?>  modal-primary" id="myModal1" data-backdrop="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header justify-content-center">
                          <span ></span>
                          <a href="manage_storages.php?close_modal" class="close" data-dismiss="modal">&times;</a>
                        </div>
                        <form class="" action="manage_storages.php" method="post">
                        <div class="modal-body ">
                          <div class="content">
                            <div class="row">
                              <div class="col-md-8">
                                <div class="pull-center">
                                <label for="">Update Storage name :</label>
                                <input type="text"class="form-control" name="storage_name" value="<?php echo $_SESSION['update_name'] ?>"required>
                                <br>
                              </div>
                              </div>
                              <div class="col-md-4">
                                <div class="pull-center">
                                <label for="">Update Initials :</label>
                                <input type="text"class="form-control" name="initials" value="<?php echo $_SESSION['update_initials'] ?>"required>
                                <br>
                              </div>
                              </div>
                            </div>
                        </div>
                   </div>
                   <div class="row">
                     <div class="col-md-6">
                       <?php   include('errors.php'); ?>
                     </div>
                     <div class="col-md-6">
                       <div class="modal-footer">
                           <button type="submit" name="update_storage"class="btn btn-sm btn-info btn-fill">Update</button>
                       </div>
                     </div>
                   </div>

                        </form>
                    </div>
                </div>
            </div>
            <!--  End Modal -->
            <?php include('dataTables2.php') ?>
            <script type="text/javascript">
                      $('#UtensilTable').DataTable( {
                       "pageLength": 50
                       } );
                    </script>
<?php include('footer.php') ?>
