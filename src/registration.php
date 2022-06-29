<?php require('server.php');  ?>
<script type="text/javascript">
  function session_userType(value) {
        $.ajax({
            type: "POST",
            url: 'ajaxrequest/sessionRegistrationFields.php',
            data: 'userType_ses=' + value,
            dataType: 'json',
            success: function (data) {
              if (data==1) {
                // location.reload();
                // location.href = 'create_account.php';
                // setInterval( 1000);
              }
            }
        });
    }
    function session_deptStrand(value) {
            $.ajax({
                type: "POST",
                url: 'ajaxrequest/sessionRegistrationFields.php',
                data: 'deptStrand_ses=' + value,
                dataType: 'json',
                success: function (data) {
                  if (data==1) {
                    // location.reload();
                    // location.href = 'create_account.php';
                    // setInterval( 1000);
                  }
                }
            });
        }
</script>
<?php if (isset($_GET['session_registration'])) {
  $_SESSION['userType'] = 0;
  $_SESSION['deptStrand'] = 0;
}
$default_UserType  = 0;
$default_DepStrand  = 0;
$selected = "selected";
?>
        <!doctype html>
        <html lang="en">
        <head>
        	<meta charset="utf-8" />
        	<link rel="icon" type="image/png" href="assets/img/favicon.ico">
        	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

        	<title>ClickU</title>

        	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
            <meta name="viewport" content="width=device-width" />

            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
          <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
          <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>


          <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
         <script src="http://code.jquery.com/jquery-1.8.2.js"></script>
          <script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>

          <script src="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"></script>


        </head>
        <style media="screen">
        .error {
          width: 99%;
          margin: 0px auto;
          margin-left: auto%;
          padding: 10px;
          color: #a94442;
          text-align: left;
          position: inherit;


        }
        .title{
        	color: #F16A70;

        	display: inline-block;
        	font-size: 30;

        	text-decoration: none;
        	background-image: linear-gradient(to right, #F16A70, orange);
        	background-position: bottom left;
        	background-repeat: no-repeat;
        	background-size: 50% 2px;
        	transition: background-size .5s ease;
        }
        .title:hover {
        	background-size: 100% 2px;
        }

        </style>
        <body>
        	<br><br>
        	<br><br>
        <?php
        // REGISTER USER
        $schoolId ="";
        $firstName = "";
        $lastName = "";
        $username = "";
        $email = "";
        $contact ="";
        $password_1 ="";
        $password_2 ="";
        $accountType ="";
        $department ="";
        $value = "1";

        if (isset($_POST['REGISTER'])) {

          $errors= array();
          $accountType = mysqli_real_escape_string($dbcon, $_POST['type']);
          $schoolId = mysqli_real_escape_string($dbcon, $_POST['school_id']);
          $username = mysqli_real_escape_string($dbcon, $_POST['username']);
          $firstName = mysqli_real_escape_string($dbcon, $_POST['firstname']);
          $lastName = mysqli_real_escape_string($dbcon, $_POST['lastname']);
          $password_1 = mysqli_real_escape_string($dbcon, $_POST['password1']);
          $password_2 = mysqli_real_escape_string($dbcon, $_POST['password2']);
          $department = mysqli_real_escape_string($dbcon, $_POST['department']);
          $contact = mysqli_real_escape_string($dbcon, $_POST['contact']);
          $email = mysqli_real_escape_string($dbcon, $_POST['email']);

          $numlength = strlen((string)$schoolId);
          $contactlength = strlen((string)$contact);
          if ($numlength != 8) {
          array_push($errors, "Invalid ID Number" );
          $_SESSION['school_id'] = "red";
        }else {
          unset($_SESSION['school_id']);
        }


          if ($password_1 != $password_2) {
        	array_push($errors, "Passwords do not match!");
          $_SESSION['password1'] = "red";
          }else {
            unset($_SESSION['password1']);
          }
          if ($_SESSION['userType']==0) {
        	array_push($errors, "Please select a user type!");
          $_SESSION['user_Type'] = "red";
          }else {
            unset($_SESSION['user_Type']);
          }
          if ($_SESSION['deptStrand']==0) {
        	array_push($errors, "Please select a Department/Strand!");
          $_SESSION['dept_Strand'] = "red";
          }else {
            unset($_SESSION['dept_Strand']);
          }

          $user_check_query = "SELECT
                                     a.user_id,a.email,a.contact,a.school_id,
                                     b.user_id,b.username

                                     FROM users a
                                     LEFT JOIN user_settings b ON a.user_id = b.user_id
                                     where a.school_id = '$schoolId' or a.email = '$email' or a.contact ='$contact' or b.username = '$username'order by a.user_id desc limit 1";
          $result = mysqli_query($dbcon, $user_check_query);
          $user = mysqli_fetch_assoc($result);

          if ($user) { // if user exists
            if ($user['school_id'] === $schoolId) {
              array_push($errors, "School ID already exists!");
              $_SESSION['school_id'] = "red";
            }else {
              unset($_SESSION['school_id']);
            }
            if ($user['username'] === $username) {
              array_push($errors, "Username already exists!");
              $_SESSION['username'] = "red";
            }else {
              unset($_SESSION['username']);
            }
            if ($contactlength != 11) {
            array_push($errors, "Invalid Contact Number" );
            $_SESSION['contact'] = "red";
          }else {
            if ($user['contact'] === $contact) {

              array_push($errors, "Contact number already exists!");
              $_SESSION['contact'] = "red";
            }else {
              unset($_SESSION['contact']);
            }
          }

            if ($user['email'] === $email) {
              $_SESSION['email'] = "red";
              array_push($errors, "Email already exists!");
            }else {
              unset($_SESSION['email']);
            }

          }
          if (count($errors) == 0) {
          	// $password = md5 ($password_1);

            	$query = "INSERT INTO users (school_id,fname, lname, email, contact, dept_id)
            			  VALUES('$schoolId','$firstName', '$lastName', '$email','$contact','$department')";
            	mysqli_query($dbcon, $query);


              $query2 = mysqli_query($dbcon,"SELECT user_id from users order by user_id desc limit 1")or die(mysqli_error($dbcon));
              $row = mysqli_fetch_assoc($query2);
              $query3 = mysqli_query($dbcon,"INSERT INTO user_settings (user_id,username,password,account_type_id)
                        values ('".$row['user_id']."','$username','$password_1','$accountType')");

        		$_SESSION['user'] = $row;
        		$_SESSION['account_type'] = $accountType;
          	$_SESSION['success'] = "You are now logged in";
          	header('location: userSelectStorage.php');
          }
        }

         ?>

          <section class="container">
            <div class="row">
              <div class="col-md-2">
                <div class="header">
                  <div class="title">
                   <h2 style="color:gray;"><img src="assets/img/click_U.png"style="width:100px;height:50px;"></h2>

                  </div>
                </div>
                <br><br>
              </div>
              <div class="col-md-6">
                <br>
              <span><?php include('errors.php') ?></span>
              </div>
            </div>
          <form method="post" action="registration.php">
          <div class="form-row">
            <div class="form-group col-md-3">
              <label >User Type</label>
              <select  class="form-control"name="type"required id="userType_ses"onchange="session_userType(this.value)"style="border-color: <?php if (isset($_SESSION['user_Type'])) { echo 'red'; }else{ echo 'gray'; }?>">
                <option value="<?php echo $default_UserType; ?>" <?php if ($_SESSION['userType']== 0) { echo $selected; } ?>>Choose here</option>
             <?php
                 $query  = mysqli_query($dbcon,"SELECT * FROM account_type where id >=6");
                 while ($rows = mysqli_fetch_array($query)) {
              ?>
               <option value="<?php echo $rows['id'] ?>"<?php if ($rows['id'] == $_SESSION['userType']) { echo $selected; } ?>><?php echo $rows['account_type_name'] ?></option>
           <?php } ?>
              </select>
            </div>
            <div class="form-group col-md-3">
              <label >School ID Number</label>
              <input type="number" id="numInput"pattern="/^-?\d+\.?\d*$/"style="border-color: <?php if (isset($_SESSION['school_id'])) { echo 'red'; }else{ echo 'gray'; }?>"
               onKeyPress="if(this.value.length==8) return false;"class="form-control"name="school_id"value="<?php echo $schoolId;?>" placeholder=""required>
            </div>
            <div class="form-group col-md-6">
              <label >Username</label>
              <input type="text" class="form-control"style="border-color: <?php if (isset($_SESSION['username'])) { echo 'red'; }else{ echo 'gray'; }?>" name="username"value="<?php echo $username;?>" placeholder=""required>
            </div>
            <div class="form-group col-md-6">
              <label >First Name</label>
              <input type="text" class="form-control" id="" name="firstname"value="<?php echo $firstName;?>" placeholder=""required>
            </div>
            <div class="form-group col-md-6">
              <label >Last Name</label>
              <input type="text" class="form-control" id=""name="lastname"value="<?php echo $lastName;?>" placeholder=""required>
            </div>
            <div class="form-group col-md-6">
              <label >Password</label>
              <input type="password" class="form-control"style="border-color: <?php if (isset($_SESSION['password1'])) { echo 'red'; }else{ echo 'gray'; }?>"name="password1"value="<?php echo $password_1;?>" placeholder=""required>
            </div>
            <div class="form-group col-md-6">
              <label >Confirm Password</label>
              <input type="password" class="form-control" style="border-color: <?php if (isset($_SESSION['password2'])) { echo 'red'; }else{ echo 'gray'; }?>"name="password2"value="<?php echo $password_2;?>" placeholder=""required>
            </div>
            <div class="form-group col-md-3" >
              <label >Department / Strand</label>
              <select  class="form-control"name="department"required id="deptStrand_ses" onchange="session_deptStrand(this.value)"
              style="border-color: <?php if (isset($_SESSION['dept_Strand'])) { echo 'red'; }else{ echo 'gray'; }?>" >
                <option value="<?php echo $default_DepStrand; ?>" <?php if ($_SESSION['deptStrand']== 0) { echo $selected; } ?>  >Choose here</option>
                <?php
                    $query  = mysqli_query($dbcon,"SELECT * FROM department order by dept_id");
                    while ($rows = mysqli_fetch_array($query)) {
                 ?>
                  <option value="<?php echo $rows['dept_id'] ?>"<?php if ($rows['dept_id'] == $_SESSION['deptStrand']) { echo $selected; } ?>><?php echo $rows['dept_name'] ?></option>
              <?php } ?>

              </select>
            </div>
            <div class="form-group col-md-3">
              <label for="inputAddress">Contact Number</label>
              <input type="number"id="numInput1" class="form-control"style="border-color: <?php if (isset($_SESSION['contact'])) { echo 'red'; }else{ echo 'gray'; }?>" onKeyPress="if(this.value.length==11) return false;"name="contact"value="<?php echo $contact;?>" placeholder=""required>
            </div>
            <div class="form-group col-md-6">
              <label >Email Address</label>
              <input type="email" class="form-control" id="inputPassword4"name="email"value="<?php echo $email;?>"style="border-color: <?php if (isset($_SESSION['email'])) { echo 'red'; }else{ echo 'gray'; }?>" placeholder=""required>
            </div>
            <div class="form-group col-md-3">
              <input class="btn btn-info" type="submit" name="REGISTER" value="Register">
              <a href="logout.php" class="btn btn-danger">Cancel</a>

            </div>
            </div>

        </form>
                </section>
          <script type="text/javascript">
          var javaScriptVar = document.getElementById("origNum")
        var trapNumber = document.getElementById("numInput")

        trapNumber.addEventListener("keydown", function(e) {

        // prevent: "e", "=", ",", "-", "."
        if ([69, 187, 188, 189, 190].includes(e.keyCode)) {
          e.preventDefault();
        }

        })
        var javaScriptVar = document.getElementById("origNum")
      var trapNumber1 = document.getElementById("numInput1")

      trapNumber1.addEventListener("keydown", function(e) {

      // prevent: "e", "=", ",", "-", "."
      if ([69, 187, 188, 189, 190].includes(e.keyCode)) {
        e.preventDefault();
      }

      })
        $(document).ready(function(){
              $("#textbox").keypress(function (e) {
                var key = e.keyCode || e.which;
                $("#error_msg").html("");
                //Regular Expression
                var reg_exp = /^[A-Za-z0-9 ]+$/;
                //Validate Text Field value against the Regex.
                var is_valid = reg_exp.test(String.fromCharCode(key));
                if (!is_valid) {
                  $("#error_msg").html("No special characters Please!");
                }
                return is_valid;
              });

            });

            //dropdown

        // document.getElementById('SelectValue').onchange = function () {
        //
        //
        //     document.getElementById("depT_div").disabled = this.value == '2';
        // }

          </script>


          </div>
          </div>


          </body>

          <!--   Core JS Files   -->
          <script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
          <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>

          <!--  Charts Plugin -->
          <script src="assets/js/chartist.min.js"></script>

          <!--  Notifications Plugin    -->
          <script src="assets/js/bootstrap-notify.js"></script>


          <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
          <script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>

          <!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
          <script src="assets/js/demo.js"></script>

          <!-- <script type="text/javascript">
          // $(document).ready(function(){
          //
          //   demo.initChartist();
          //
          //   $.notify({
          //       icon: 'pe-7s-satisfied',
          //       message: "Welcome to <b>ClickU</b> - UCLM Kitchen Utensil Online Borrowing."
          //
          //     },{
          //         type: 'info',
          //         timer: 4000
          //     });
          //
          // });
          </script> -->

          </html>
