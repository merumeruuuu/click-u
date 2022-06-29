<?php require('config.php');
session_start();
if (isset($_POST['LOGIN_SUBMIT']))
 {

    $username = mysqli_real_escape_string($dbcon,$_POST['username']);
    $password = mysqli_real_escape_string($dbcon,$_POST['password']);
  	// $password = md5($password);

    $result = "SELECT
                   a.user_id,a.fname,a.storage_id,
                   b.user_id,b.username,b.password,b.account_type_id

                  FROM users a
                  LEFT JOIN user_settings b on a.user_id = b.user_id

                  where b.username = '$username' and b.password = '$password' ";
     $check = mysqli_query($dbcon,$result);

     if(mysqli_num_rows($check)==1)
     {
       while($res=mysqli_fetch_array($check))
       {
         if($res['account_type_id']=="1")
         {
           $_SESSION['user']=$res;
           $_SESSION['account_type']=$res['account_type_id'];
           $_SESSION['username']=$res['fname'];
           header("location: dean_home.php");
         }
         else if($res['account_type_id']=="2")
         {
           $_SESSION['user']=$res;
           $_SESSION['account_type']=$res['account_type_id'];
          $_SESSION['username']=$res['fname'];
           header("location: dean_home.php");
         }

         else if($res['account_type_id']=="3")
         {
           $_SESSION['user']=$res;
           $_SESSION['account_type']=$res['account_type_id'];
           $_SESSION['username']="$username";
           header("location: kitchen_staff_home.php");
         }
         else if($res['account_type_id']=="4")
         {
           $_SESSION['user']=$res;
           $_SESSION['account_type']=$res['account_type_id'];
           $_SESSION['username']="$username";
           header("location: kitchen_staff_home.php");
         }
         else if($res['account_type_id']=="5")
         {
           $_SESSION['user']= $res;
           $_SESSION['account_type']=$res['account_type_id'];
           $_SESSION['username']="$username";
           header("location: kitchen_staff_home.php");
         }
         else if($res['account_type_id']=="6")
         {
           $_SESSION['user']=$res;
           $_SESSION['account_type']=$res['account_type_id'];
           $_SESSION['username']="$username";
           header("location: storages.php");
         }
         else if($res['account_type_id']=="7")
         {
           $_SESSION['user']=$res;

           $_SESSION['account_type']=$res['account_type_id'];
           $_SESSION['username']="$username";
           header("location: storages.php");
         }

       }
     }
     else
     {
       echo "<script>alert('Invalid username or password');window.location.href='index.php';</script>";
     }
    }
 ?>
