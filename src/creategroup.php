<?php include('header.php'); ?>
<script type="text/javascript">
function session_group_name(value) {
        $.ajax({
            type: "POST",
            url: 'ajaxrequest/selectSession.php',
            data: 'group_nameS=' + value,
            dataType: 'json',
            success: function (data) {
              if (data==0) {
                // location.href = 'creategroup.php';
                // setInterval( 1000);
              }
            }
        });
    }
function session_Instructor(value) {
        $.ajax({
            type: "POST",
            url: 'ajaxrequest/selectSession.php',
            data: 'group_instructor=' + value,
            dataType: 'json',
            success: function (data) {
              if (data==0) {
                // location.href = 'creategroup.php';
                // setInterval( 1000);
              }
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
                  if (data==0) {
                    // location.reload();
                    // location.href = 'creategroup.php';
                    // setInterval( 1000);
                  }
                }
            });
        }
function session_groupLeader(value) {
        $.ajax({
            type: "POST",
            url: 'ajaxrequest/session_group_leader.php',
            data: 'group_leader=' + value,
            dataType: 'json',
            success: function (data) {
              if (data==0) {
                // location.reload();
                location.href = 'creategroup.php';
                setInterval( 1000);
              }
            }
        });
    }
    function session_Storage(value) {
            $.ajax({
                type: "POST",
                url: 'ajaxrequest/selectSession.php',
                data: 'group_storage=' + value,
                dataType: 'json',
                success: function (data) {
                  if (data==0) {
                    alert('Confirm Update!');
                  }

                }
            });
        }
        function session_Date_use(value) {
                $.ajax({
                    type: "POST",
                    url: 'ajaxrequest/session_group_leader.php',
                    data: 'group_date=' + value,
                    dataType: 'json',
                    success: function (data) {
                      if (data==1) {
                        // location.href = 'creategroup.php';
                        // setInterval( 1000);
                      }

                    }
                });
            }
            function session_Time_use(value) {
                    $.ajax({
                        type: "POST",
                        url: 'ajaxrequest/session_group_leader.php',
                        data: 'group_time=' + value,
                        dataType: 'json',
                        success: function (data) {
                          if (data==1) {
                            // location.href = 'creategroup.php';
                            // setInterval( 1000);
                          }

                        }
                    });
                }
</script>
<br><br>
<?php
if (isset($_POST['validate_group'])) {

      $date = $_SESSION['date_use'];
      $time = $_SESSION['time_use'];
      $now = date('d/m/Y');
      $nowT = date('d.m.Y',strtotime("-1 days"));
      if ($date >= $nowT) {
      if(empty($_SESSION["group_members"])&&empty($_SESSION["group_leader"])) {
        array_push($errors,"No members added yet!");
      }if(!empty($_SESSION["group_members"]) && count($_SESSION["group_members"])<2) {
        array_push($errors,"Invalid Grouping, contains only 1 member!");
      }if(!empty($_SESSION["group_members"])&& count($_SESSION["group_members"])>1 && empty($_SESSION["group_leader"])) {
        array_push($errors,"Please select a group leader!");
      }if(!empty($_SESSION["group_members"]) && count($_SESSION["group_members"])>1 && !empty($_SESSION["group_leader"])){
      include 'requests.php';
      $requests = new Utensils;
      $clear = $requests->clear();
      echo "<script>window.location.href='select_utensils.php';</script>";
    }
 }else {
   array_push($errors,"Invalid date/time!");
 }

}
if (isset($_GET['blank_fields'])) {
  array_push($errors,"Please fill empty fields!");
}
if (isset($_GET['invalid_date'])) {

    array_push($errors,"Invalid date/time!");
}
if (isset($_GET['create_group'])) {
  unset($_SESSION['group_purpose']);
  unset($_SESSION['group_name']);
  unset($_SESSION['group_instructor']);
  unset($_SESSION['group_storage']);
  unset($_SESSION['date_use']);
  unset($_SESSION['time_use']);
  unset($_SESSION['group_leader']);
  unset($_SESSION['group_members']);
} ?>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <h4 class="title bol">Create your group :</h4>
                        <p class="category">(Please provide the necessary information bellow)</p>
                    </div>
                    <div class="content">

          <?php
          if(isset($_POST['add_member']))
          {
           $str = mysqli_real_escape_string($dbcon,$_POST['member']);

           $query = "SELECT
                            a.user_id as userID,a.fname,a.lname,a.school_id,
                            b.user_id,b.account_type_id

                            from users a
                            left join user_settings b on a.user_id = b.user_id
                            where a.school_id = $str and b.account_type_id =7";
           $result = mysqli_query($dbcon,$query);
           $check = mysqli_num_rows($result);
           if($check==0){
          // echo "<script>alert('Student not found!');window.location.href='creategroup.php';</script>";
           array_push($errors, "Student not found!! " );
        }
        else {

          if(isset($_POST["add_member"]))
          {
            $str1 = $_POST['member'];
            $user = $_SESSION['user']['user_id'];
            $query1 = "SELECT
                             a.user_id as userID,a.fname,a.lname,a.school_id,
                             b.user_id,b.account_type_id

                             from users a
                             left join user_settings b on a.user_id = b.user_id
                             where a.school_id = $str1 ";
            $result1 = mysqli_query($dbcon,$query1);
             $rows = mysqli_fetch_array($result1);

              if(isset($_SESSION["group_members"]))
              {
                   $item_array_id = array_column($_SESSION["group_members"], "member_school_ID");
                   if(!in_array($_POST['member'], $item_array_id))
                   {
                        $count = count($_SESSION["group_members"]);
                        $item_array = array(
                             // 'member_ID'             =>     $_POST['add_member'],
                             'member_school_ID'      =>     $_POST['member'],
                             'member_ID'             =>     $rows["user_id"],
                             'member_fname'          =>     $rows["fname"],
                             'member_lname'          =>     $rows["lname"]
                        );
                        $_SESSION["group_members"][$count] = $item_array;
                   }
                   else
                   {
                         array_push($errors, "Member already added! " );
                        // echo '<script>alert("Member Already Added")</script>';
                        // echo '<script>window.location="creategroup.php"</script>';
                   }
              }
              else
              {
                   $item_array = array(
                        // 'member_ID'             =>     $_POST['school_id'],
                        'member_school_ID'      =>     $_POST['member'],
                        'member_ID'             =>     $rows["user_id"],
                        'member_fname'          =>     $rows["fname"],
                        'member_lname'          =>     $rows["lname"]
                   );
                   $_SESSION["group_members"][0] = $item_array;
              }

          }

        }

      }
      if(isset($_GET["action"]))
      {
           if($_GET["action"] == "delete")
           {  $checkMember = $_SESSION["group_members"];
             $count = count(array_filter($checkMember));
             if ($count > 2) {
                foreach($_SESSION["group_members"] as $keys => $values)
                {

                     if($values["member_ID"] == $_GET["school_id"])
                     {
                          // unset($_SESSION["group_members"][$keys]);
                             $_SESSION["group_members"][$keys] = Null;
                          if ($_SESSION['group_leader']==$_GET["school_id"]) {
                            $unset = "0";
                            $_SESSION['group_leader'] = $unset ;
                          }
                          // echo '<script>window.location="creategroup.php"</script>';
                     }
                }
              }else {
                array_push($errors,"Remove Failed! At least two members is required!");
              }
           }
      }

     ?>

        <div class="content">
          <div class="row">
            <div class="col-md-6">
              <h4 style="color:gray;">Group borrowing form</h4>
            </div>
            <div class="col-md-6">
              <br>
              <?php include('errors.php') ?>
            </div>
          </div>
          <div class="card">
            <div class="content">
            <div class="row">
              <div class="col-md-6">

                <div class="">
                  <div class=""style="color:gray;">
                  <h5>Search members :</h5>
                 </div>
                </div>
                <form class="form-inline my-2 my-lg-0" action="creategroup.php"method="post"style="position:relative;">
                <input class="form-control mr-sm-2"style="border-color: green;" type="number" name="member" placeholder="ID number" aria-label="Search" required>
                <button type="submit" name="add_member"  class="btn btn-success btn-fill"><i class="fa fa-search"></i>
              </button>
                 </form>
                 <div class="header">
                   <div class=""style="color:gray;">
                   <h5><i class="fa fa-users"></i> Group Members </h5>
                  </div>
                 </div>
                 <table class="table  table-hover app-table-responsive">
                   <thead>
                     <th>Action</th>
                     <th>ID Number</th>
                     <th>First Name</th>
                     <th>Last Name</th>
                   </thead>
                   <tbody >

                   <?php
                   if(!empty($_SESSION["group_members"]))
                   {
                        $filtered = array_filter($_SESSION["group_members"]);
                        foreach($filtered as $keys => $values)
                        {

                   ?>
                   <tr>
                        <td><a href="creategroup.php?action=delete&school_id=<?php echo $values["member_ID"]; ?>"><span class="text-danger">Remove</span></a></td>
                        <td><?php echo $values["member_school_ID"]; ?></td>
                        <td><?php echo $values["member_fname"]; ?></td>
                        <td> <?php echo $values["member_lname"]; ?></td>
                   </tr>

               <?php }
             }else{ ?>
             <tr><td colspan="5"><p>Empty members...</p></td></tr>
             <?php } ?>
                   </tbody>
                   </table>
                 <br>
                 <div class=""style="color:gray;">
                 <h5><i class="fa fa-pencil"></i> Select A Group Leader </h5>
                </div>
            <select class="form-control"name="group_leader"id="group_leader"onchange="session_groupLeader(this.value)"style="border-color: green;" required>
            <?php if (isset($_SESSION['group_leader'])) {
              $fetchLeader = mysqli_query($dbcon,"SELECT * FROM users where user_id = ".$_SESSION['group_leader']);
              foreach ($fetchLeader as $key => $value);
              ?>
               <option value="<?php echo $value['user_id'] ?>" selected disabled hidden ><?php echo $value['lname'] ?> , <?php echo $value['fname'] ?></option>
              <?php
            }else {
              ?>
             <option value="" selected disabled hidden >Choose here..</option>
              <?php
            } ?>

            <?php
            if(count($_SESSION['group_members'])>1 && !empty($_SESSION["group_members"]))
            {
                 $filtered = array_filter($_SESSION["group_members"]);
                 foreach($filtered as $keys => $values)
                 {
            ?>
              <option value="<?php echo $values['member_ID'] ?>"><?php echo $values['member_lname'] ?> , <?php echo $values['member_fname'] ?></option>
            <?php } }?>

            </select>

            <form class="" action="creategroup.php" method="post">
            <span>
            <div class=""style="color:gray;">
            <h5>Group Name :</h5>
           </div>
              <input type="text"class="form-control" name="group_name"id="group_name" value="<?php if (isset($_SESSION['group_name'])) {
                echo $_SESSION['group_name'];
              } ?>"placeholder="Complete Name"onchange="session_group_name(this.value)"style="border-color: green;"required>
              </span>
              </div>
              <div class="col-md-6">
                  <span>
                  <div class=""style="color:gray;">
                  <h5>Instructor Name :</h5>
                 </div>
                    <input type="text"class="form-control" name="group_instructor"id="group_instructor" value="<?php if (isset($_SESSION['group_instructor'])) {
                      echo $_SESSION['group_instructor'];
                    } ?>"placeholder="Complete Name"onchange="session_Instructor(this.value)"style="border-color: green;"required>
                    </span>
                    <span>

                      <div class=""style="color:gray;">
                      <h5>Select storage :</h5>
                     </div>
                    <select name="storage"class="form-control"name="group_storage"id="group_storage"onchange="session_Storage(this.value)"style="border-color: green;"required>
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
                      <span>
                        <div class=""style="color:gray;">
                        <h5>Purpose :</h5>
                       </div>
                      <textarea name="purpose"id="purpose"class="form-control" rows="5"
                      cols="50"placeholder="(eg. Cooking,Baking, etc.)"onchange="session_purpose(this.value)"
                      style="border-color: green;"required><?php if (isset($_SESSION['group_purpose'])){echo $_SESSION['group_purpose'];}?></textarea>
                      </span>
                      <span>
                        <div class=""style="color:gray;">
                        <h5>Date of use :</h5>
                       </div>
                        <input type="date" name="group_date"id="group_date"class="form-control"onchange="session_Date_use(this.value)" value="<?php if (isset($_SESSION['date_use'])) {
                            echo $_SESSION['date_use'];
                          } ?>"style="border-color: green;"required>
                      </span>
                     <span>
                       <div class=""style="color:gray;">
                       <h5>Time of use :</h5>
                      </div>
                       <input type="time" name="group_time"id="group_time"class="form-control"onchange="session_Time_use(this.value)" value="<?php if (isset($_SESSION['time_use'])) {
                           echo $_SESSION['time_use'];
                         } ?>"style="border-color: green;"required>
                     </span>
              </div>

            </div>


              </div>
              <br>
                </div>
                  <br>
              <div class="row">
                <div class="col-md-12 ">
                  <div class="pull-right">
                  <span>
                      <a href="userSelectStorage.php"onclick="return confirm('Confirm cancel!')" class="btn btn-danger btn-fill btn-sm" >Cancel</a>
                </span>
                 <button name="validate_group" class="btn btn-info btn-fill btn-sm">Save and Proceed</button>

                 </form>
                  </div>
                </div>
          </div>
      </div>
                          <br>
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
  </div>
<!-- </form> -->




    <!-- Bootstrap core JavaScript -->
<?php include('footer.php') ?>
