<?php include('header.php') ?>
<?php
if (isset($_POST['add_task'])) {
  $task = mysqli_real_escape_string($dbcon,$_POST['task']);
  $user = $_SESSION['user']['user_id'];
  $insert = mysqli_query($dbcon,"INSERT INTO tasks (description,user_id)values('$task','$user')");
    echo "<script>alert('New task added!');</script>";
} ?>
<?php
if (isset($_GET['remove_task'])) {
  $id = $_GET['remove_task'];
  $remove = mysqli_query($dbcon,"DELETE FROM tasks where id = $id");
  // echo "<script>alert('Task has been deleted!');</script>";
} ?>
<br><br>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                          <h4 class="title">Utensils </h4>
                          <p class="category">Most used items</p>


                        </div>
                        <div class="content">
                          <!-- <div id="top_x_div1" style="overflow-x:auto;" ></div> -->
                          <div id="piechart2" style="overflow-x:auto;"></div>

                            <div class="footer">

                                <hr>
                                <div class="stats">
                                    <i class="fa fa-clock-o"></i> <?php echo date('m-d,Y h:i:s A'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Users Behavior</h4>
                            <p class="category">24 Hours performance</p>
                        </div>
                        <div class="content" >

                            <div id="dual_x_div" style="overflow-x:auto;" ></div>
                            <div class="footer">

                                <hr>
                                <div class="stats">
                                    <i class="fa fa-history"></i> <?php echo date('m-d,Y h:i:s A'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card ">
                        <div class="header">
                          <h4 class="title">Inventory Statistics</h4>
                          <p class="category">All transactions</p>
                        </div>
                        <div class="content">
                          <div class="row">
                            <div class="col-md-12" >
                                <div id="piechart_3d" style="overflow-x:auto;" ></div>
                            </div>
                          </div>

                            <div  class="footer">

                                <hr>
                                <div class="stats">
                                    <i class="fa fa-check"></i> Data information certified
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card ">
                        <div class="header">
                          <div class="row">
                            <div class="col-md-6">
                              <h4 class="title">Tasks</h4>
                              <p class="category"></p>
                            </div>
                            <div class="col-md-6">
                               <span><a href="#"data-toggle="modal"data-target="#modal1"class="pull-right"> <i class="fa fa-plus"></i> Add task</a></span>
                            </div>
                          </div>

                        </div>
                        <div class="content">
                            <div class="table-full-width">
                                <table class="table">
                                    <tbody>
                                      <?php
                                      $user = $_SESSION['user']['user_id'];
                                      $fetchTask = mysqli_query($dbcon,"SELECT * FROM tasks where user_id = $user order by id desc");
                                      foreach ($fetchTask as $key => $value) {
                                       ?>
                                        <tr>
                                            <td>
                                               <i class="fa fa-circle text-info"></i>
                                            </td>
                                            <td><?php echo $value['description']; ?></td>
                                            <td class="td-actions text-right">
                                                <a href="?remove_task=<?php echo $value['id']; ?>"onclick="return confirm('Confirm delete!');" rel="tooltip" title="Remove" class="btn btn-danger btn-simple btn-xs">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </td>
                                        </tr>
                                      <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="footer">
                                <hr>
                                <div class="stats">
                                    <i class="fa fa-history"></i> <?php echo date('m-d,Y h:i:s A'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mini Modal -->
    <div class="modal fade modal-mini modal-primary" id="modal1" data-backdrop="false">
        <div class="modal-dialog">
            <form class="" action="dean_home.php" method="post">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-center">

                    <div class="row">
                      <div class="col-md-12 text-left">
                       <label for="">Add new task</label>
                       <input type="text" name="task" value=""class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                  <input type="submit" name="add_task"id="submit_btn" class="btn btn-success btn-fill btn-sm" value="Submit">
                </div>
            </div>
            </form>
        </div>
    </div>
    <!--  End Modal -->
<?php include('footer.php') ?>
