<?php include('header.php'); ?>
<br><br>
<?php
include 'requests.php';
$requests = new Utensils;
if (isset($_GET['borrow_form'])) {
  unset($_SESSION['group_instructor']);
  unset($_SESSION['group_purpose']);
  unset($_SESSION['group_storage']);
  unset($_SESSION['date_use']);
  unset($_SESSION['time_use']);
}
if (isset($_POST['individual_form'])) {
  $errors = array();
  $date = $_POST['date_use'];
  $time = $_POST['time_use'];
  $now = date('Y-m-d H:i:s');
  $nowT = date('Y-m-d H:i:s');
  if ($_SESSION['account_type']==7) {
    $_SESSION['group_instructor'] = $_POST['group_instructor'];
  }
  $_SESSION['group_purpose'] = $_POST['purpose'];
  $_SESSION['group_storage'] = $_POST['storage'];
  $_SESSION['date_use'] = $_POST['date_use'];
  $_SESSION['time_use'] = $_POST['time_use'];
  // if ($now < $date) {
    $clear = $requests->clear();
    echo "<script>window.location.href='select_utensils.php';</script>";
  // }else {
  //   array_push($errors,"Inavalid date!");
  // }

} ?>
<div class="content"id="main">
  <?php
     $_SESSION['user'];
     $user = $_SESSION['user']['user_id'];
     $result = "SELECT
                    a.user_id,a.fname,
                    b.user_id,b.account_type_id

                   FROM users a
                   LEFT JOIN user_settings b on a.user_id = b.user_id

                   where b.user_id = '$user' ";
      $check = mysqli_query($dbcon,$result);
      $rows = mysqli_fetch_array($check);
      //for teacher
     if($rows['account_type_id']=="6"){
   ?>
    <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
              <div class="card">
                  <div class="header">
                    <h4 class="title">Borrowing form</h4>
                    <p class="category">(Please provide the necessary information bellow)</p>
                      <hr>
                  </div>
                  <div class="content">
                    <form method="post" action="userSelectStorage.php">
                    <div class="content">
                      <div class="row">

                        <div class="col-md-6">
                          <span>

                            <div class="">
                              <div class=""style="color:gray;">
                              <h5>Date of use :</h5>
                             </div>
                            </div>
                            <input type="date" name="date_use"class="form-control" value="<?php if (isset($_SESSION['date_use'])) {
                                echo $_SESSION['date_use'];
                              } ?>"style="border-color: gray;"required>
                          </span>
                          <span>

                            <div class="">
                              <div class=""style="color:gray;">
                              <h5>Time of use :</h5>
                             </div>
                            </div>
                            <input type="time" name="time_use"class="form-control" value="<?php if (isset($_SESSION['time_use'])) {
                                echo $_SESSION['time_use'];
                              } ?>"style="border-color: gray;"required>
                          </span>
                          <span>
                            <div class="">
                              <div class=""style="color:gray;">
                              <h5>Select storage :</h5>
                             </div>
                            </div>
                          <select name="storage"class="form-control"style="border-color: gray;"required>
                            <?php if (isset($_SESSION['group_storage'])) {
                              $select = "SELECT * from storage
                                              where storage_id = '".$_SESSION['group_storage']."'
                                               ";
                            }else { ?>
                            <option value=""selected>Choose here</option>
                          <?php } ?>
                            <?php
                              $select = "SELECT

                                              a.storage_id,a.storage_qty,
                                              b.storage_id,b.storage_name,b.initials

                                              from storage_stocks a
                                              left join storage b on a.storage_id = b.storage_id
                                              where a.storage_qty >0
                                              group by a.storage_id
                                               ";
                           $storage_display = mysqli_query($dbcon,$select)
                             ?>
                             <?php
                           while($row = mysqli_fetch_array($storage_display)) {
                            ?>
                       <option value="<?php echo $row['storage_id']; ?>"><?php echo $row['storage_name']; ?></option>
                         <?php
                       }
                        ?>
                          </select>
                          </span>
                          </div>
                          <div class="col-md-6">
                            <span>
                            <div class="">
                              <div class=""style="color:gray;">
                              <h5>Purpose :</h5>
                             </div>
                            </div>
                            <textarea name="purpose"id="purpose"class="form-control"rows="9"cols="90"placeholder="(eg. Cooking,Baking, etc.)"onchange="session_purpose(this.value)"style="border-color:gray;"required><?php if(isset($_SESSION['group_purpose'])){echo $_SESSION['group_purpose'];}?></textarea>
                            </span>
                          </div>
                          <div class="col-md-6">
                           <?php include('errors.php') ?>
                          </div>
                    <div class="col-md-12">
                      <div class="pull-right">
                      <input type="submit" name="individual_form"class="btn btn-info btn-fill btn-sm" value="Save and proceed">
                      </div>
                    </div>
                      </div>
                      <!-- <br>
                      <div class="">
                         <?php
                        $select = "SELECT

                                        a.storage_id,a.storage_qty,
                                        b.storage_id,b.storage_name,b.initials

                                        from storage_stocks a
                                        left join storage b on a.storage_id = b.storage_id
                                        where a.storage_qty >0
                                        group by a.storage_id
                                         ";
                        $storage_display = mysqli_query($dbcon,$select)
                          ?>
                          <?php
                        while($row = mysqli_fetch_array($storage_display)) {
                         ?>
                      <div class="">
                        <button type="submit" formaction="utensil_request_action.php?action=selectStorage&id=<?php echo $row['storage_id']; ?>"class="btn btn-lg btn-block btn-default btn-fill"><?php echo $row['storage_name']; ?> (<?php echo $row['initials']; ?>)</button>
                       <br>
                      </div>
                      <?php
                    }
                     ?>
                    </div> -->

                  </div>
                  </form>
                      <div class="footer">
                          <br>
                          <hr>
                          <div class="stats">
                              <i class="fa fa-clock-o"></i> All Transactions will be monitored timely.
                          </div>
                      </div>
                  </div>
</div>
</div>
  </div>
  </div>
    <?php
     //for students
   } if($rows['account_type_id']=="7") {
      ?>
      <div class="container-fluid">
          <div class="row">
              <div class="col-md-12">
                  <div class="card">
                    <div class="row">
                      <div class="col-md-9">
                        <div class="header">
                            <h4 class="title">Borrowing form</h4>
                            <p class="category">(Please provide the necessary information bellow)</p>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="content">
                           <p class="category">For group borrowing</p>
                           <!-- <p class="category"><a href="#"data-toggle="modal"data-target="#by_group">Click here</a></p> -->
                           <p class="category"><a href="creategroup.php?create_group">Click here</a></p>
                        </div>
                      </div>
                    </div>
                      <div class="content">
                            <hr>
                            <h4 style="color:gray;">Individual borrowing :</h4>
                            <div class="card">
                              <div class="content">
                                  <form method="post" action="userSelectStorage.php">
                            <div class="row">
                              <div class="col-md-12">

                            <div class="col-md-6">
                              <span>
                              <div class="">
                                <div class=""style="color:gray;">
                                <h5>Instructor Name :</h5>
                               </div>
                              </div>
                                <input type="text"class="form-control" name="group_instructor"id="group_instructor" value="<?php if (isset($_SESSION['group_instructor'])) {
                                  echo $_SESSION['group_instructor'];
                                }?>"placeholder="Complete Name"onchange="session_Instructor(this.value)"style="border-color: gray;"required>
                                </span>
                                <span>
                                <div class="">
                                  <div class=""style="color:gray;">
                                  <h5>Purpose :</h5>
                                 </div>
                                </div>
                                <textarea name="purpose"id="purpose"class="form-control" rows="5" cols="50"placeholder=" (eg. Cooking,Baking, etc.)"onchange="session_purpose(this.value)"
                                style="border-color: gray;"required><?php if(isset($_SESSION['group_purpose'])){echo $_SESSION['group_purpose'];}?></textarea>

                                </span>
                            </div>

                     <div class="col-md-6">
                       <span>
                         <div class="">
                           <div class=""style="color:gray;">
                           <h5>Date of use :</h5>
                          </div>
                         </div>
                         <input type="date" name="date_use"class="form-control" value="<?php if (isset($_SESSION['date_use'])) {
                             echo $_SESSION['date_use'];
                           } ?>"style="border-color: gray;"required>
                       </span>
                       <span>
                         <div class="">
                           <div class=""style="color:gray;">
                           <h5>Time of use :</h5>
                          </div>
                         </div>
                         <input type="time" name="time_use"class="form-control" value="<?php if (isset($_SESSION['time_use'])) {
                             echo $_SESSION['time_use'];
                           } ?>"style="border-color: gray;"required>
                       </span>
                       <span>

                         <div class="">
                           <div class=""style="color:gray;">
                           <h5>Select storage :</h5>
                          </div>
                         </div>
                       <select name="storage"class="form-control"style="border-color: gray;"required>
                         <?php if (isset($_SESSION['group_storage'])) {
                           $select = "SELECT * from storage
                                           where storage_id = '".$_SESSION['group_storage']."'
                                            ";
                         }else { ?>
                         <option value=""selected>Choose here</option>
                       <?php } ?>
                         <?php
                           $select = "SELECT

                                           a.storage_id,a.storage_qty,
                                           b.storage_id,b.storage_name,b.initials

                                           from storage_stocks a
                                           left join storage b on a.storage_id = b.storage_id
                                           where a.storage_qty >0
                                           group by a.storage_id
                                            ";
                        $storage_display = mysqli_query($dbcon,$select)
                          ?>
                          <?php
                        while($row = mysqli_fetch_array($storage_display)) {
                         ?>
                    <option value="<?php echo $row['storage_id']; ?>"><?php echo $row['storage_name']; ?></option>
                      <?php
                    }
                     ?>
                       </select>
                       </span>
                     </div>
                     <div class="col-md-6">
                      <?php include('errors.php') ?>
                     </div>

                   </div>
                   </div>
                 </div>
               </div>
               <div class="col-md-12">
                 <div class="pull-right">
                 <input type="submit" name="individual_form"class="btn btn-info btn-fill btn-sm" value="Save and proceed">
                 </div>
               </div>
               </form>
               <br><br><br>

                            <!-- <div class="col-md-6">
                                 <center>
                                 <h4 class="text-info">Select a laboratory</h4>
                               </center>
                                 <div class="">
                                    <?php
                                   $select = "SELECT

                                                   a.storage_id,a.storage_qty,
                                                   b.storage_id,b.storage_name,b.initials

                                                   from storage_stocks a
                                                   left join storage b on a.storage_id = b.storage_id
                                                   where a.storage_qty >0
                                                   group by a.storage_id
                                                    ";
                                   $storage_display = mysqli_query($dbcon,$select)
                                     ?>
                                     <?php
                                   while($row = mysqli_fetch_array($storage_display)) {
                                    ?>
                                 <div class="">
                                   <button type="submit" formaction="utensil_request_action.php?action=selectStorage&id=<?php echo $row['storage_id']; ?>"class="btn btn-lg btn-block btn-default btn-fill"><?php echo $row['storage_name']; ?> (<?php echo $row['initials']; ?>)</button>
                                  <br>
                                 </div>
                                 <?php
                               }
                                ?>
                               </div>
                            </div> -->


                          <div class="footer">

                              <hr>
                              <div class="stats">
                                  <i class="fa fa-clock-o"></i> All Transactions will be monitored timely.
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
        </div>
      <?php
    } ?>
</div>

<script type="text/javascript">
         function session_Instructor(value) {
                 $.ajax({
                     type: "POST",
                     url: 'ajaxrequest/selectSession.php',
                     data: 'group_instructor=' + value,
                     dataType: 'json',
                     success: function (data) {
                     }
                 });
             }
             function session_purpose(value) {
                     $.ajax({
                         type: "POST",
                         url: 'ajaxrequest/selectSession.php',
                         data: 'purpose=' + value,
                         dataType: 'json',
                         success: function (data) {

                         }
                     });
                 }
</script>

    <!-- Bootstrap core JavaScript -->
<?php include('footer.php') ?>
