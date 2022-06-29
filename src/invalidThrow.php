<?php
session_start();
	if ($_SESSION['account_type']==1||$_SESSION['account_type']==2) {
    header("Location:dean_home.php");
  }if ($_SESSION['account_type']==3||$_SESSION['account_type']==4||$_SESSION['account_type']==5) {
    header("Location:kitchen_staff_home.php");
  }if ($_SESSION['account_type']==6||$_SESSION['account_type']==7) {
    header("Location:storages.php");
  }
  ?>
