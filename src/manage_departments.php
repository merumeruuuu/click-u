<?php include('header.php') ?>
<br><br>
<?php
$errors = array();
if (isset($_GET['manage_dept'])) {
  $_SESSION['department_control'] = 0;
  $_SESSION['department_control2'] = 0;
}
if (isset($_GET['department_controlx'])) {
  $_SESSION['department_control'] = 1;
}
if (isset($_GET['close_modal'])) {
  $_SESSION['department_control'] = 0;
  $_SESSION['department_control2'] = 0;
}
if (isset($_POST['add_department'])) {
  $stname = mysqli_real_escape_string($dbcon,$_POST['description']);
  $inls = mysqli_real_escape_string($dbcon,$_POST['dept_name']);
  $department = trim($stname);
  $dept_name = trim($inls);
  $trapString = mysqli_query($dbcon,"SELECT * FROM department where description = '$department'or dept_name = '$dept_name'");
  if (mysqli_num_rows($trapString)>=1) {
    array_push($errors,"Department already exists!");
  }else {
    if (empty($department)) {
      array_push($errors,"Please fill in fields!");
    }else {
      $insertdepartment = mysqli_query($dbcon,"INSERT INTO department (description,dept_name) VALUES ('$stname','$inls')");
      array_push($success,"New department added!");
      $_SESSION['department_control'] = 0;
    }
}
}
if (isset($_GET['remove_department'])) {
  $deptID = $_GET['remove_department'];
  // $removeDept = mysqli_query($dbcon,"DELETE FROM department where dept_id = $deptID");
  // $removeDeptS = mysqli_query($dbcon,"DELETE FROM department_stocks where dept_id = $deptID");
  $fetchStID = mysqli_query($dbcon,"SELECT * FROM users where dept_id = $deptID");
  if (mysqli_num_rows($fetchStID)>=1) {
    array_push($errors,"Cannot delete active department!");
  }else {
  $removeDept = mysqli_query($dbcon,"DELETE FROM department where dept_id = $deptID");
  }
}
if (isset($_GET['update_dept'])) {
  $deptID = $_GET['update_dept'];
  $fetchStID = mysqli_query($dbcon,"SELECT * FROM department where dept_id = $deptID");
  foreach ($fetchStID as $key => $value);
  $_SESSION['update_name'] = $value['description'];
  $_SESSION['update_dept_name'] = $value['dept_name'];
  $_SESSION['dept_id'] = $value['dept_id'];
  $_SESSION['department_control2'] = 1;
}
if (isset($_POST['update_department'])) {
  $str1 = $_POST['description'];
  $str2 = $_POST['dept_name'];
  $trapString = mysqli_query($dbcon,"SELECT * FROM department where description like '%$str1%' or dept_name like '%$str2%'");
  if (mysqli_num_rows($trapString)>=1) {
    array_push($errors,"department already exists!");
  }else {
    $updateStor = mysqli_query($dbcon,"UPDATE department set description = '$str1',dept_name = '$str2' where dept_id = '".$_SESSION['dept_id']."'");
    array_push($success,"Department name successfully updated!");
      $_SESSION['department_control2'] = 0;
  }

}?>
<div class="content" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Departments</h4>
                        <!-- <p class="department">(General department)</p> -->
                    </div>
                    <div class="content" style="overflow-x:auto;">
                      <div class="row">
                        <div class="col-md-7">
                          <?php include('success.php'); ?>
                          <?php
                          if ($_SESSION['department_control2'] == 1||$_SESSION['department_control'] == 1) {
                            // code...
                          }else {
                              include('errors.php');
                          }
                         ?>
                        </div>
                        <div class="col-md-5">
                            <a href="?department_controlx" class="btn btn-info btn-fill pull-right"><i class="fa fa-plus"></i> Add new Department</a>
                        </div>
                      </div>
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>Status</th>
                          <th>Deoartment/Strand Description</th>
                          <th>Abbreviation</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $fetchdepartment = "SELECT *
                                    FROM department
                                    order by dept_id  ";
                        $result = mysqli_query($dbcon,$fetchdepartment);
                        foreach ($result as $key => $value) {
                          ?>
                          <tr>
                            <td> <?php $check = mysqli_query($dbcon,"SELECT * FROM users where dept_id = ".$value['dept_id']);
                            if (mysqli_num_rows($check)>0) {
                              ?>
                              <i class="fa fa-circle text-success"></i> Active
                              <?php
                            }else {
                              ?>
                              <i class="fa fa-circle text-warning"></i> Inactive
                              <?php
                            } ?> </td>
                            <td><?php echo $value['description'] ?></td>
                            <td><?php echo $value['dept_name'] ?></td>
                            <td>
                              <span>
                            <a href="?update_dept=<?php echo $value['dept_id']; ?>"class=""><i class="fa fa-pencil text-info"></i></a>
                            </span>
                            <span>
                          <a href="?remove_department=<?php echo $value['dept_id']; ?>"class=""><i class="fa fa-trash text-danger"></i></a>
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
<div class="modal <?php if ($_SESSION['department_control']==1) {
      echo "show";
    }if($_SESSION['department_control']==0) {
      echo "fade";
     } ?>  modal-primary" id="myModal1" data-backdrop="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                  <span >Add new department</span>
                  <a href="manage_departments.php?close_modal" class="close" data-dismiss="modal">&times;</a>
                </div>
                <form class="" action="manage_departments.php" method="post">
                <div class="modal-body ">
                  <div class="content">
                    <div class="row">
                      <div class="col-md-8">
                        <div class="pull-center">
                        <label for="">Department/Strand Description :</label>
                        <input type="text"class="form-control" name="description" value=""required>
                        <br>
                      </div>
                      </div>
                      <div class="col-md-4">
                        <div class="pull-center">
                        <label for="">Abbreviation :</label>
                        <input type="text"class="form-control" name="dept_name" value=""required>
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
                   <button type="submit" name="add_department"class="btn btn-sm btn-info btn-fill">Add</button>
               </div>
             </div>
           </div>
                </form>
            </div>
        </div>
    </div>
    <!--  End Modal -->

            <!-- Mini Modal -->
  <div class="modal <?php if ($_SESSION['department_control2']==1) {
              echo "show";
            }if($_SESSION['department_control2']==0) {
              echo "fade";
             } ?>  modal-primary" id="myModal1" data-backdrop="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header justify-content-center">
                          <span ></span>
                          <a href="manage_departments.php?close_modal" class="close" data-dismiss="modal">&times;</a>
                        </div>
                        <form class="" action="manage_departments.php" method="post">
                        <div class="modal-body ">
                          <div class="content">
                            <div class="row">
                              <div class="col-md-8">
                                <div class="pull-center">
                                <label for="">Department/Strand Description :</label>
                                <input type="text"class="form-control" name="description" value="<?php echo $_SESSION['update_name'] ?>"required>
                                <br>
                              </div>
                              </div>
                              <div class="col-md-4">
                                <div class="pull-center">
                                <label for="">Abbreviation :</label>
                                <input type="text"class="form-control" name="dept_name" value="<?php echo $_SESSION['update_dept_name'] ?>"required>
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
                           <button type="submit" name="update_department"class="btn btn-sm btn-info btn-fill">Update</button>
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
