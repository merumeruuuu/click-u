<?php include('header.php') ?>
<br><br>
<?php //update Profile
$username = "";
$email = "";
$contact = "";

if (isset($_POST['update_profile'])) {
  $success = array();
  $errors = array();
  $username = mysqli_real_escape_string($dbcon,$_POST['username']);
  $email = mysqli_real_escape_string($dbcon,$_POST['email']);
  $contact = mysqli_real_escape_string($dbcon,$_POST['contact']);
  $_SESSION['user']['user_id'];
  $id = $_SESSION['user']['user_id'];
  if (ctype_space($username)||ctype_space($email)||ctype_space($contact)||empty($username)||empty($email)||empty($contact)) {
  array_push($errors, "Please fill in the blank fields!");
   }else {

  $contactlength = strlen((string)$contact);

  if ($contactlength != 11) {
  array_push($errors, "Invalid Contact Number!" );
  }
  $email_check_query = "SELECT
                             a.user_id as userID,a.email,a.contact,a.school_id,
                             b.user_id,b.username

                             FROM users a
                             LEFT JOIN user_settings b ON a.user_id = b.user_id
                             where a.user_id != '$id' and a.email = '$email'";
  $email_q = mysqli_query($dbcon, $email_check_query);
  $username_check_query = "SELECT
                             a.user_id as userID,a.email,a.contact,a.school_id,
                             b.user_id,b.username

                             FROM users a
                             LEFT JOIN user_settings b ON a.user_id = b.user_id
                             where  b.username = '$username'and a.user_id != '$id' ";
  $username_q = mysqli_query($dbcon, $username_check_query);
  $number_check_query = "SELECT
                             a.user_id as userID,a.email,a.contact,a.school_id,
                             b.user_id,b.username

                             FROM users a
                             LEFT JOIN user_settings b ON a.user_id = b.user_id
                             where  a.contact ='$contact' and a.user_id != '$id' ";
  $number = mysqli_query($dbcon, $number_check_query);

  if (mysqli_num_rows($email_q)>0) {
    array_push($errors, "Email already exists!");
  }
  if (mysqli_num_rows($username_q)>0) {
    array_push($errors, "Username already exists!");
  }
  if (mysqli_num_rows($number)>0) {
    array_push($errors, "Contact number already exists!");
  }


if (count($errors) == 0) {
  $update1 = mysqli_query($dbcon,"UPDATE `users`set `email`='$email',`contact`='$contact' where `users`.`user_id` = $id");
  $update2 = mysqli_query($dbcon,"UPDATE `user_settings`set `username`='$username' where `user_settings`.`user_id` = $id");

  array_push($success, "Profile successfully updated!");
// echo "<script>alert('Profile successfully updated!');window.location.href='userprofile.php';</script>";
}

}
}
 ?>
 <?php
 if (isset($_POST['change_pass']))
  {
     $_SESSION['modal_control'] = "show";
     $success = array();
     $oldpasserr = array();
     $missMatchPassErrs = array();

     $oldpass = mysqli_real_escape_string($dbcon,$_POST['old_pass']);
     $password1 = mysqli_real_escape_string($dbcon,$_POST['password1']);
     $password2 = mysqli_real_escape_string($dbcon,$_POST['password2']);
     $_SESSION['old_pass'] = $oldpass;
     $_SESSION['password1'] = $password1;
     $_SESSION['password2'] = $password2;
     // $password = md5($password1);
 	   // $oldpassN = md5($oldpass);
     $_SESSION['user']['user_id'];
     $id = $_SESSION['user']['user_id'];
        $result = "SELECT
                       a.user_id,
                       b.user_id,b.password,b.account_type_id

                      FROM users a
                      LEFT JOIN user_settings b on a.user_id = b.user_id

                      where a.user_id = '$id' and b.password = '$oldpass'";
         $check = mysqli_query($dbcon,$result);
         if(mysqli_num_rows($check)>=1){
              if ($password1 == $password2) {
                $update = mysqli_query($dbcon,"UPDATE `user_settings` SET `password`='$password1' WHERE `user_settings`.`user_id`= '$id'");
                 // array_push($success,"Password successfully changed!");
                 echo "<script>alert('Password successfully changed!');window.location.href='userprofile.php';</script>";
                 $_SESSION['modal_control'] = "fade";
                 unset($_SESSION['missMatchPass']);
                 unset($_SESSION['oldpasserr']);
                 unset($_SESSION['old_pass']);
                 unset($_SESSION['password1']);
                 unset($_SESSION['password2']);
              }else {
                $_SESSION['missMatchPass'] = "Passwords don't match!";
              }
              unset($_SESSION['oldpasserr']);
         }else {
           $_SESSION['oldpasserr']="Wrong old password!";
         }
 } ?>
<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Edit Profile</h4>
                                <hr>
                                <?php include('errors.php') ?>
                                <?php include('success.php') ?>
                            </div>
                            <div class="content">
                                <form action="userprofile.php"method="post">
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
                                        $username = $rows['username'];
                                        $email = $rows['email'];
                                        $contact = $rows['contact'];

                                  ?>
                                    <div class="row">
                                        <div class="col-md-5 pr-1">
                                            <div class="form-group">
                                                <label>ID Number</label>
                                                <input type="text" class="form-control" readonly value="<?php echo $rows['school_id'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3 px-1">
                                            <div class="form-group">
                                                <label>Username</label>
                                                <input type="text"id="input" class="form-control"name="username"   value="<?php echo $username; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4 pl-1">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Email address</label>
                                                <input type="email" class="form-control" name="email"  value="<?php echo $email; ?>">
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
                                                <input type="number"id="numInput" class="form-control"onKeyPress="if(this.value.length==11) return false;" name="contact"  value="<?php echo $contact; ?>">
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

                                  <center><input type="submit" class="btn btn-success btn-fill pull-right"name="update_profile"value="Update Profile"></center>
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

                                      <br>
                                        <img class="avatar " src="img/logo4.png" >
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
<?php if (isset($_GET['modal_control'])) {
  $_SESSION['modal_control'] = "fade";
  unset($_SESSION['oldpasserr']);
  unset($_SESSION['missMatchPass']);
  unset($_SESSION['old_pass']);
  unset($_SESSION['password1']);
  unset($_SESSION['password2']);
} ?>
        <div class="modal <?php echo $_SESSION['modal_control']; ?> modal-mini modal-primary" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"data-backdrop="false">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header justify-content-center">
                                <div class="modal-profile">
                                    <i class="nc-icon nc-bulb-63"></i>
                                </div>
                            </div>
                            <div class="modal-body text-center">
                                <p>Always have an access to your profile!</p>
                            <div class="text-left">
                              <form method="post" action="userprofile.php" class="form-login ">
                                  <div class="content">
                                  <br><br>
                                  <span for="">Enter old password</span><span id=""class="pull-right text-danger"><?php if (isset($_SESSION['oldpasserr'])) { echo $_SESSION['oldpasserr'];  } ?>
                                </span> <?php $green = "green";$red = "red"; ?>
                                  <input type="text" class="form-control" style="border-color: <?php if (isset($_SESSION['oldpasserr'])) { echo $red; }else{ echo $green; }?>"
                                 placeholder="" name="old_pass"value="<?php if (isset($_SESSION['old_pass'])) {
                                    echo $_SESSION['old_pass'];
                                  } ?>"required>
                                  <br>
                                  <span for="">Enter new password</span><span id=""class="pull-right text-danger"><?php if (isset($_SESSION['missMatchPass'])) { echo $_SESSION['missMatchPass'];  } ?>
                                  </span>
                                  <input type="password" class="form-control"style="border-color: <?php if (isset($_SESSION['missMatchPass'])) { echo $red; }else{ echo $green; }?>" placeholder="" name="password1"value="<?php if (isset($_SESSION['password1'])) {
                                    echo $_SESSION['password1'];
                                  } ?>"required>
                                  <br>
                                  <span for="">Confirm new password</span><span id=""class="pull-right text-danger"><?php if (isset($_SESSION['missMatchPass'])) { echo $_SESSION['missMatchPass'];  } ?>
                                  </span>
                                  <input type="password" class="form-control"style="border-color: <?php if (isset($_SESSION['missMatchPass'])) { echo $red; }else{ echo $green; }?>" placeholder="" name="password2"value="<?php if (isset($_SESSION['password2'])) {
                                    echo $_SESSION['password2'];
                                  } ?>"required>
                                  <br>  <br>
                                 </div>
                              </div>
                            <div class="modal-footer">
                              <span>
                                <input type="submit" class="btn btn-fill btn-success btn-sm" name="change_pass" value="Submit">
                              </span>
                              <span>
                                <a href="?modal_control" class="btn btn-fill btn-sm btn-danger">Cancel</a >
                              </span>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
</div>

<script type="text/javascript">
var trapNumber = document.getElementById("numInput")

trapNumber.addEventListener("keydown", function(e) {

// prevent: "e", "=", ",", "-", "."
if ([69, 187, 188, 189, 190].includes(e.keyCode)) {
e.preventDefault();
}

})
function addWords() {
    var value = $('#input').val();

    if ((/^[a-zA-Z\s]*$/.test(value)) && (value !== '')) {
        value = value.trimLeft().trimRight();
        $('#output').append($('<span></span>').text(value + ' '));
    } else {
        alert('Please use characters only.');
    }
}
setTimeout(function() {
  $('#myErrs').fadeOut('fast');
}, 3000);
setTimeout(function() {
  $('#myErrs1').fadeOut('fast');
}, 3000);
setTimeout(function() {
  $('#myErrs2').fadeOut('fast');
}, 3000);
</script>
<?php include('footer.php') ?>
