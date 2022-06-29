<?php include('header.php') ?>
<script type="text/javascript">
  function session_userType(value) {
        $.ajax({
            type: "POST",
            url: 'ajaxrequest/sessionCreateAccountsDrop.php',
            data: 'userType_ses=' + value,
            dataType: 'json',
            success: function (data) {
              if (data==1) {
                // location.reload();
                location.href = 'create_account.php';
                setInterval( 1000);
              }
            }
        });
    }
    function session_deptStrand(value) {
            $.ajax({
                type: "POST",
                url: 'ajaxrequest/sessionCreateAccountsDrop.php',
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
        function session_assignLab(value) {
                $.ajax({
                    type: "POST",
                    url: 'ajaxrequest/sessionCreateAccountsDrop.php',
                    data: 'assignLab_ses=' + value,
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
            function session_id_number(value) {
                    $.ajax({
                        type: "POST",
                        url: 'ajaxrequest/sessionCreateAccountsDrop.php',
                        data: 'id_number_ses=' + value,
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
                function session_email(value) {
                        $.ajax({
                            type: "POST",
                            url: 'ajaxrequest/sessionCreateAccountsDrop.php',
                            data: 'email_ses=' + value,
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
                    function session_firstname(value) {
                            $.ajax({
                                type: "POST",
                                url: 'ajaxrequest/sessionCreateAccountsDrop.php',
                                data: 'firstname_ses=' + value,
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
                        function session_lastname(value) {
                                $.ajax({
                                    type: "POST",
                                    url: 'ajaxrequest/sessionCreateAccountsDrop.php',
                                    data: 'lastname_ses=' + value,
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
                            function session_contact(value) {
                                    $.ajax({
                                        type: "POST",
                                        url: 'ajaxrequest/sessionCreateAccountsDrop.php',
                                        data: 'contact_ses=' + value,
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
<?php if (isset($_GET['session_account_dropdowns'])) {
  $_SESSION['userType'] = 0;
  $_SESSION['assignLab'] = 0;
  $_SESSION['deptStrand'] = 0;
  $_SESSION['id_number'] = '';
  $_SESSION['email'] = '';
  $_SESSION['firstname'] = '';
  $_SESSION['lastname'] = '';
  $_SESSION['contact'] = '';
}
$default_assignLab = 0;
$default_UserType  = 0;
$default_DepStrand  = 0;
$selected = "selected";
?>
<?php
// create_account USER
if (isset($_POST['create_account'])) {

  $errors= array();
  $success= array();
  $schoolId = mysqli_real_escape_string($dbcon, $_POST['school_id']);
  $firstName = mysqli_real_escape_string($dbcon, $_POST['firstname']);
  $lastname = mysqli_real_escape_string($dbcon, $_POST['lastname']);
  $contact = mysqli_real_escape_string($dbcon, $_POST['contact']);
  $email = mysqli_real_escape_string($dbcon, $_POST['email']);
  $accountType = mysqli_real_escape_string($dbcon, $_POST['type']);
  if ($_SESSION['userType']==4|| $_SESSION['userType']==5) {
    $department = mysqli_real_escape_string($dbcon, $_POST['department']);
  }


  $numlength = strlen((string)$schoolId);
  $contactlength = strlen((string)$contact);
  if ($numlength != 8) {
  array_push($errors, "Invalid ID Number" );
  }
  if ($contactlength != 11) {
  array_push($errors, "Invalid Contact Number" );
  }
  if ($_SESSION['userType'] == 0) {
  array_push($errors, "Please select a user type" );
  }
  if ($_SESSION['userType']!=2 && $_SESSION['userType']!=3) {

    if ($_SESSION['userType']!=3) {
      if ($_SESSION['assignLab'] == 0) {
      array_push($errors, "Please select assigned Laboratory " );
      }
      if ($_SESSION['deptStrand'] == 0) {
      array_push($errors, "Please select a Department or Strand type" );
      }
    }

  }


  $user_check_query = "SELECT
                             a.user_id,a.email,a.contact,a.school_id,
                             b.user_id,b.username

                             FROM users a
                             LEFT JOIN user_settings b ON a.user_id = b.user_id
                             where a.school_id = '$schoolId' or a.email = '$email' or a.contact ='$contact' order by a.user_id desc limit 1";
  $result = mysqli_query($dbcon, $user_check_query);
  $user = mysqli_fetch_assoc($result);

  if ($user) { // if user exists
    if ($user['school_id'] === $schoolId) {
      array_push($errors, "School ID already exists!");
    }
    if ($user['contact'] === $contact) {
      array_push($errors, "Contact number already exists!");
    }
    if ($user['email'] === $email) {
      array_push($errors, "Email already exists!");
    }

  }


  if (count($errors) == 0) {
  	// $password = md5 ($password_1);
      if ($_SESSION['userType']== 2) {
        $query = "INSERT INTO users (school_id,fname, lname, email, contact)
      			  VALUES('$schoolId','$firstName', '$lastname', '$email','$contact')";
      	mysqli_query($dbcon, $query);
      }else {
        if ($_SESSION['userType']== 3) {
          $query = "INSERT INTO users (school_id,fname, lname, email, contact, storage_id)
                VALUES('$schoolId','$firstName', '$lastname', '$email','$contact','".$_POST['storage']."')";
          mysqli_query($dbcon, $query);
        }else {
          $query = "INSERT INTO users (school_id,fname, lname, email, contact, dept_id,storage_id)
                VALUES('$schoolId','$firstName', '$lastname', '$email','$contact','$department','".$_POST['storage']."')";
          mysqli_query($dbcon, $query);
        }
      }
      $query2 = mysqli_query($dbcon,"SELECT user_id from users order by user_id desc limit 1")or die(mysqli_error($dbcon));
      $row = mysqli_fetch_assoc($query2);
      $query3 = mysqli_query($dbcon,"INSERT INTO user_settings (user_id,username,password,account_type_id)
                values ('".$row['user_id']."','$schoolId','$schoolId','$accountType')");
                $_SESSION['userType'] = 0;
                $_SESSION['assignLab'] = 0;
                $_SESSION['deptStrand'] = 0;
                $_SESSION['id_number'] = '';
                $_SESSION['email'] = '';
                $_SESSION['firstname'] = '';
                $_SESSION['lastname'] = '';
                $_SESSION['contact'] = '';
    array_push($success, "New Account Created!");
  	// header('location: create_account.php');
  }
}
 ?>
<br><br>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Create Account :</h4>
                            <p class="category"></p>
                            <hr>
                        </div>
                        <div class="content">
                         <form class="" action="create_account.php" method="post">
                           <div class="form-row">
                             <div class="col-md-12">
                               <?php include('errors.php') ?>
                               <?php include('success.php') ?>
                             </div>
                             <div class="form-group col-md-6">
                               <label >School ID Number</label>
                               <input type="number"class="form-control "id="numInput1"name="school_id"onKeyPress="if(this.value.length==8) return false;"value="<?php echo $_SESSION['id_number'];?>"id="id_number_ses"onchange="session_id_number(this.value)"required>
                             </div>
                             <div class="form-group col-md-6">
                               <label >Email Address</label>
                               <input type="email" class="form-control"name="email"value="<?php echo $_SESSION['email'];?>"id="email_ses"onchange="session_email(this.value)"required>
                             </div>
                             <div class="form-group col-md-6">
                               <label >First Name</label>
                               <input type="text" class="form-control"  name="firstname"value="<?php echo $_SESSION['firstname'];?>"id="firstname_ses"onchange="session_firstname(this.value)"required>
                             </div>
                             <div class="form-group col-md-6">
                               <label >User Type</label>
                               <select  class="form-control"name="type"required id="userType_ses"onchange="session_userType(this.value)">
                               <?php echo $accountType; ?>
                                 <option value="<?php echo $default_UserType; ?>" <?php if ($_SESSION['userType']== 0) { echo $selected; } ?>>Choose here</option>
                              <?php
                                  $query  = mysqli_query($dbcon,"SELECT * FROM account_type where id < 6 and id != 1");
                                  while ($rows = mysqli_fetch_array($query)) {
                               ?>
                                <option value="<?php echo $rows['id']; ?>" <?php if ($rows['id'] == $_SESSION['userType']) { echo $selected; } ?> ><?php echo $rows['account_type_name'] ?></option>
                            <?php } ?>
                               </select>
                             </div>
                             <div class="form-group col-md-6">
                               <label >Last Name</label>
                               <input type="text" class="form-control" name="lastname"value="<?php echo $_SESSION['lastname'];?>"id="lastname_ses"onchange="session_lastname(this.value)"required>
                             </div>
                             <div class="form-group col-md-6" >
                               <label >Department / Strand</label>
                               <select  class="form-control"name="department"<?php if ($_SESSION['userType']== 2 || $_SESSION['userType']== 3){ echo 'disabled'; } ?> id="deptStrand_ses" onchange="session_deptStrand(this.value)">
                                 <option value="<?php echo $default_DepStrand; ?>" <?php if ($_SESSION['deptStrand']== 0) { echo $selected; } ?>  >Choose here</option>
                                 <?php
                                     $query  = mysqli_query($dbcon,"SELECT * FROM department order by dept_id");
                                     while ($rows = mysqli_fetch_array($query)) {
                                  ?>
                                   <option value="<?php echo $rows['dept_id'] ?>" <?php if ($rows['dept_id'] == $_SESSION['deptStrand']) { echo $selected; } ?>><?php echo $rows['dept_name'] ?></option>
                               <?php } ?>

                               </select>
                             </div>

                             <div class="form-group col-md-6">
                               <label for="inputAddress">Contact Number</label>
                               <input type="number"id="numInput" class="form-control" onKeyPress="if(this.value.length==11) return false;"name="contact"value="<?php echo $_SESSION['contact'];?>"id="contact_ses"onchange="session_contact(this.value)"required>
                             </div>

                             <div class="form-group col-md-6" >
                               <label >Assign Laboratory</label>
                               <select  class="form-control"name="storage"<?php if ($_SESSION['userType'] == 2){ echo 'disabled'; } if ($_SESSION['userType']!= 2){ echo 'required'; }?>  id="assignLab_ses" onchange="session_assignLab(this.value)">

                                  <?php if ($_SESSION['userType']==3) {
                                    ?>
                                    <option value="1" selected >General Storage (GS)</option>
                                    <?php
                                  }else {
                                    ?>
                                    <option value="<?php echo $default_assignLab; ?>" <?php if ($_SESSION['assignLab']== 0) { echo $selected; } ?> >Choose here</option>
                                    <?php
                                    if ($_SESSION['userType']==4||$_SESSION['userType']==5) {
                                      $query  = mysqli_query($dbcon,"SELECT * FROM storage where storage_id != 1 order by storage_id");
                                    }else {
                                      $query  = mysqli_query($dbcon,"SELECT * FROM storage order by storage_id");
                                    }
                                        while ($rows = mysqli_fetch_array($query)) {
                                     ?>
                                     <option value="<?php echo $rows['storage_id'] ?>" <?php if ($rows['storage_id'] == $_SESSION['assignLab']) { echo $selected; } ?>><?php echo $rows['storage_name'] ?> (<?php echo $rows['initials'] ?>)</option>
                                    <?php } ?>
                                    <?php
                                  } ?>


                               </select>
                              </div>
                              </div>

                               <div class="row">
                               <div class="col-md-12">
                                   <div class="footer">
                                   <div class="legend">
                                     <div class="category">
                                        User Legend :
                                     </div>
                                       <i class="fa fa-circle text-info"></i> Borrower
                                       <i class="fa fa-circle text-success"></i> GS-In-Charge
                                       <i class="fa fa-circle "style="color:purple;"></i> Laboratory-In-Charge
                                       <i class="fa fa-circle "style="color:orange;"></i> Admin/Assistant Admin
                                   </div>
                                   <hr>
                                   <div class="col-md-9">
                                     <div class="text-danger">
                                         <i class="fa fa-clock-o"></i> Default username and password = ( School ID )
                                     </div>
                                   </div>
                                   <div class="col-md-3">
                                    <span><input class="btn btn-info btn-fill" type="submit" name="create_account" value="Create"></span>
                                    <span><a href="dean_home.php" class="btn btn-danger btn-fill">Cancel</a></span>
                                   </div>
                                   </div>
                               </div>
                                </div>

                         </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

    </script>
<?php include('footer.php') ?>
