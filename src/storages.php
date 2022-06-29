<?php include('header.php'); ?>
<br><br>
<style media="screen">

.button {
  padding: 15px 25px;
  font-size: 24px;
  text-align: center;
  cursor: pointer;
  outline: none;
  color: #fff;
  background-color: #0396ff;
  border: none;
  border-radius: 15px;
  box-shadow: 0 9px #999;
}

.button:hover {background-color: #3681bb}

.button:active {
  background-color: #3466ed;
  box-shadow: 0 5px #666;
  transform: translateY(4px);
}
</style>
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
                      <h4 class="title bol">Select Laboratory : </h4>
                      <!-- <p class="category">(Select from laboratories bellow)</p> -->
                      <hr>
                  </div>

                  <div class="content">
                    <div class="content">
                      <div class="row">
                        <?php
                        $query = "SELECT

                                        a.storage_id,a.storage_qty,
                                        b.storage_id,b.storage_name,b.initials

                                        from storage_stocks a
                                        left join storage b on a.storage_id = b.storage_id
                                        where a.storage_qty >0
                                        group by a.storage_id
                                         ";
                            $storage_disp = mysqli_query($dbcon,$query);

                            while ($row = mysqli_fetch_array($storage_disp)) {
                         ?>
               <a href="utensil_request_action.php?action=selectStorage&id=<?php echo $row['storage_id']; ?>"  style="color:white;">
                <div class="col-md-6">
                  <div class="button">
                <?php echo $row['storage_name']; ?> (<?php echo $row['initials']; ?>)
                <br>
              </div><br>
            </div>
            </a>
    <?php } ?>
  </div>
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
            <!-- <div class="col-md-12">
                <div class="card">

                    <div class="header">
                        <h4 class="title">Select Storage :</h4>
                        <p class="category">(Laboratory)</p>
                    </div>
                    <div class="content">
                          <hr>
                      <form method="post" action="storages.php">
                      <div class="content">

                        <br>
                        <div class="">
                          <?php
                          $query = "SELECT

                                          a.storage_id,a.storage_qty,
                                          b.storage_id,b.storage_name,b.initials

                                          from storage_stocks a
                                          left join storage b on a.storage_id = b.storage_id
                                          where a.storage_qty >0
                                          group by a.storage_id
                                           ";
                              $storage_disp = mysqli_query($dbcon,$query);

                              while ($row = mysqli_fetch_array($storage_disp)) {
                           ?>
                        <ul>

                          <button type="submit" formaction="utensil_request_action.php?action=selectStorage&id=<?php echo $row['storage_id']; ?>"class="btn btn-lg btn-block btn-default btn-fill"><?php echo $row['storage_name']; ?> (<?php echo $row['initials']; ?>)</button>
                        </ul>
                        <?php
                      }
                       ?>
                      </div>
                    </div>
                    </form>
                        <div class="footer">

                            <hr>
                            <div class="stats">
                                <i class="fa fa-clock-o"></i> All Transactions will be monitored timely.
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
          </div>
      </div>
    <?php
     //for students
   }else {
      ?>
      <div class="container-fluid">
          <div class="row">
              <div class="col-md-6">
                  <div class="card">

                      <div class="header">
                          <h4 class="title">Create Group :</h4>
                          <p class="category">(For Group Borrowing)</p>
                      </div>
                      <div class="content">
                            <hr>

                        <form method="post" action="storages.php">
                        <div class="content">
                         <div class="">
                           <input class="form-control "style="border-color: green;" type="txt" name="group_name" placeholder="Group Name" required>
                         </div>
                          <br>
                          <div class="">
                            <!-- onchange="session_Storage(this.value)" -->
                            <select class="form-control"name="group_storage"id="group_storage"style="border-color: green;" required>
                              <option value="" selected disabled hidden >Select Storage</option>
                              <?php
                              $query = "SELECT

                                              a.storage_id,a.storage_qty,
                                              b.storage_id,b.storage_name,b.initials

                                              from storage_stocks a
                                              left join storage b on a.storage_id = b.storage_id
                                              where a.storage_qty >0
                                              group by a.storage_id
                                               ";
                                  $storage_disp = mysqli_query($dbcon,$query);

                                  while ($rows = mysqli_fetch_array($storage_disp)) {
                               ?>
                                <option value="<?php echo $rows['storage_id'] ?>"><?php echo $rows['storage_name'] ?> (<?php echo $rows['initials'] ?>)</option>
                              <?php } ?>

                              </select>
                              <br>
                          </div>

                          <div class="">
                            <!-- onchange="session_Instructor(this.value)" -->
                            <select class="form-control"name="group_instructor"id="group_instructor"style="border-color: green;"required>
                                <option value="" selected disabled hidden >Select Instructor</option>
                                <?php
                                    $find = "SELECT
                                               a.user_id as userID,a.school_id,a.fname,a.lname,
                                               b.user_id,b.account_type_id


                                               FROM users a
                                               Left join user_settings b on a.user_id = b.user_id

                                                where b.account_type_id = 6 ";
                                    $query  = mysqli_query($dbcon,$find);
                                    while ($rows = mysqli_fetch_array($query)) {
                                 ?>
                                  <option value="<?php echo $rows['userID'] ?>"><?php echo $rows['fname'] ?> <?php echo $rows['lname'] ?></option>
                              <?php } ?>

                              </select>

                          </div>
                          <br>
                          <div class="">
                            <input type="submit"class="btn btn-lg btn-block btn-info btn-fill" name="create_group" value="Create Group">
                        </div>
                      </div>
                      </form>



                          <div class="footer">

                              <hr>
                              <div class="stats">
                                  <i class="fa fa-clock-o"></i>All Transactions will be monitored timely.
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

              <div class="col-md-6">
                  <div class="card">

                      <div class="header">
                          <h4 class="title">Please Select Instructor and Preferred Laboratory :</h4>
                          <p class="category">(For Individual Borrowing)</p>
                      </div>
                      <div class="content">
                            <hr>
                        <form method="post" action="storages.php">
                        <div class="content">
                          <div class="">

                            <select class="form-control"name="group_instructor"id="group_instructor"onchange="session_Instructor(this.value)"style="border-color: green;"required>
                                <option value="" selected disabled hidden >Select Instructor</option>
                                <?php
                                    $find = "SELECT
                                               a.user_id as userID,a.school_id,a.fname,a.lname,
                                               b.user_id,b.account_type_id


                                               FROM users a
                                               Left join user_settings b on a.user_id = b.user_id

                                                where b.account_type_id = 6 ";
                                    $query  = mysqli_query($dbcon,$find);
                                    while ($rows = mysqli_fetch_array($query)) {
                                 ?>
                                  <option value="<?php echo $rows['userID'] ?>"><?php echo $rows['fname'] ?> <?php echo $rows['lname'] ?></option>
                              <?php } ?>

                              </select>

                          </div>
                          <br>
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

                            <!-- <a class="btn btn-lg btn-block btn-primary" href="select_utensils.php?get_id=<?php echo $row['storage_id']; ?>"> <?php echo $row['storage_name']; ?> (<?php echo $row['initials']; ?>)</a> -->
                            <!-- <a href="utensil_request_action.php?action=selectStorage&id=<?php echo $row['storage_id']; ?>" class="btn btn-lg btn-block btn-default btn-fill" ><?php echo $row['storage_name']; ?> (<?php echo $row['initials']; ?>)</a> -->
                            <button type="submit" formaction="utensil_request_action.php?action=selectStorage&id=<?php echo $row['storage_id']; ?>"class="btn btn-lg btn-block btn-default btn-fill"><?php echo $row['storage_name']; ?> (<?php echo $row['initials']; ?>)</button>
                           <br>
                          </div>
                          <?php
                        }
                         ?>
                        </div>
                      </div>
                      </form>
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
</script>

    <!-- Bootstrap core JavaScript -->
<?php include('footer.php') ?>
