<?php
session_start();
/////////////////////////////// user links ////////////////////////////////////
if (isset($_GET['storages'])) {
	if ($_SESSION['account_type']==1 || $_SESSION['account_type']==2|| $_SESSION['account_type']==3|| $_SESSION['account_type']==4|| $_SESSION['account_type']==5) {
    if ($_SESSION['account_type']==1 || $_SESSION['account_type']==2) {
    	header("location: dean_home.php");
    }
		if ($_SESSION['account_type']==3|| $_SESSION['account_type']==4|| $_SESSION['account_type']==5) {
    	header("location: kitchen_staff_home.php");
    }
	}else {
		header("location: userRequestsMenu2.php");
	}
}
 ?>
