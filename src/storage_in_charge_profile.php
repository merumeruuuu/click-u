<?php include('header.php') ?>
<br><br>
<?php //update Profile
$username = "";
$email = "";
$contact = "";
if (isset($_POST['update_storage_profile'])) {

  $username = mysqli_real_escape_string($dbcon,$_POST['username']);
  $email = mysqli_real_escape_string($dbcon,$_POST['email']);
  $contact = mysqli_real_escape_string($dbcon,$_POST['contact']);
  $_SESSION['user']['user_id'];
  $id = $_SESSION['user']['user_id'];

  $contactlength = strlen((string)$contact);

  if ($contactlength != 11) {
  array_push($errors, "Invalid Contact Number!" );
  }
  $user_check_query = "SELECT
                             a.user_id as userID,a.email,a.contact,a.school_id,
                             b.user_id,b.username

                             FROM users a
                             LEFT JOIN user_settings b ON a.user_id = b.user_id
                             where  a.email = '$email' or a.contact ='$contact' or b.username = '$username'order by a.user_id desc limit 1";
  $result = mysqli_query($dbcon, $user_check_query);
  $user = mysqli_fetch_assoc($result);

  if ($user) { // if user exists
  if ($user['email'] === $email) {
    array_push($errors, "Email already exists!");
  }
  if ($user['username'] === $username) {
    array_push($errors, "Username already exists!");
  }
  if ($user['contact'] === $contact) {
    array_push($errors, "Contact number already exists!");
  }
}

if (count($errors) == 0) {
  $update1 = mysqli_query($dbcon,"UPDATE `users`set `email`='$email',`contact`='$contact' where `users`.`user_id` = $id");
  $update2 = mysqli_query($dbcon,"UPDATE `user_settings`set `username`='$username' where `user_settings`.`user_id` = $id");

  echo "<script>alert('Profile successfully updated!');window.location.href='storage_in_charge_profile.php';</script>";
}
}
 ?>
<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Edit Profile</h4>
                                <hr>
                                <?php include('errors.php') ?>
                            </div>
                            <div class="content">
                                <form action="storage_in_charge_profile.php"method="post">
                                  <?php
                                      $_SESSION['user']['user_id'];
          														 $id = $_SESSION['user']['user_id'];
          														 $result = "SELECT
          																						a.user_id,a.fname,a.lname,a.school_id,a.email,a.contact,a.dept_id,
          																						b.user_id,b.username,b.password,b.account_type_id,
                                                      c.id,c.account_type_name,
                                                      d.dept_id,d.dept_name

          																					 FROM users a
          																					 LEFT JOIN user_settings b on a.user_id = b.user_id
                                                     LEFT JOIN account_type c on b.account_type_id = c.id
                                                     left join department d on a.dept_id = d.dept_id

          																					 where b.user_id = '$id' ";
          															$check = mysqli_query($dbcon,$result);
          															$rows = mysqli_fetch_array($check);


                                  ?>
                                    <div class="row">
                                        <div class="col-md-5 pr-1">
                                            <div class="form-group">
                                                <label>ID Number</label>
                                                <input type="text" class="form-control" disabled value="<?php echo $rows['school_id'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3 px-1">
                                            <div class="form-group">
                                                <label>Username</label>
                                                <input type="text" class="form-control"name="username"  placeholder="<?php echo $rows['username'] ?>" value="<?php echo $username; ?>"required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 pl-1">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Email address</label>
                                                <input type="email" class="form-control" name="email" placeholder="<?php echo $rows['email'] ?>" value="<?php echo $email; ?>"required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 pr-1">
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" class="form-control" disabled placeholder="" value="<?php echo $rows['fname'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6 pl-1">
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" class="form-control" disabled placeholder="" value="<?php echo $rows['lname'] ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 pr-1">
                                            <div class="form-group">
                                                <label>CONTACT NUMBER</label>
                                                <input type="number" class="form-control"onKeyPress="if(this.value.length==11) return false;" name="contact" placeholder="<?php echo $rows['contact'] ?>" value="<?php echo $contact; ?>"required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 px-1">
                                            <div class="form-group">
                                                <label>DEPARTMENT/COURSE</label>
                                                <input type="text" class="form-control"disabled placeholder="" value="<?php echo $rows['dept_name'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4 pl-1">
                                            <div class="form-group">
                                                <label>USER TYPE</label>
                                                <input type="text" class="form-control" disabled placeholder="" value="<?php echo $rows['account_type_name'] ?>">
                                            </div>
                                        </div>

                                    </div>

                                  <center><input type="submit" class="btn btn-success btn-fill pull-right"name="update_storage_profile"value="Update Profile"></center>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                <br><br><br>
                    <div class="col-md-4" style="margin-top:1%;">
                        <div class="card card-user">
                            <div class="card-image">

                            </div>
                            <div class="card-body">
                                <div class="author">
                                    <a href="#">
                                      <br>
                                        <img class="avatar " src="img/logo4.png" >

                                    </a>
                                    <p class="description">
                                      username :  <?php echo $rows['username'] ?>
                                    </p>
                                </div>
                                <p class="description text-center">

                                  ***********
                                </p>
                            </div>
                            <hr>
                            <div class="">
                            <center>  <button type="submit" class="btn btn-success btn-fill"data-toggle="modal" data-target="#myModal1" href="#pablo">Change Password</button></center>
                              <div class="clearfix"></div>
                              <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-mini modal-primary" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"data-backdrop="false">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header justify-content-center">
                                <div class="modal-profile">
                                    <i class="nc-icon nc-bulb-63"></i>
                                </div>
                            </div>
                            <div class="modal-body text-center">
                                <p>Always have an access to your profile!</p>

                            <div class="">
                              <form method="post" action="storage_in_charge_profile.php" class="form-login">

                                  <br><br>
                                  <label for="">Enter old password</label>
                                  <input type="text" class="form-control"style="border-color: green;" placeholder="" name="old_pass"required>
                                  <br>
                                  <label for="">Enter new password</label>
                                  <input type="password" class="form-control"style="border-color: green;" placeholder="" name="password1"required>
                                  <br>
                                  <label for="">Confirm new password</label>
                                  <input type="password" class="form-control"style="border-color: green;" placeholder="" name="password2"required>
                                  <br>  <br>



                              </div>
                            <div class="modal-footer">

                                <button type="button" class="btn btn-link btn-simple pull-left" data-dismiss="modal">Close</button>
                                <input type="submit" class="btn btn-fill pull-right btn-success" name="change_pass" value="Submit">

                            </div>
                            </form>
                        </div>
                    </div>
                </div>
              </div>
<?php include('footer.php') ?>
