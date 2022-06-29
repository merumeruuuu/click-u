<?php require('server.php');  $currURL = basename($_SERVER['REQUEST_URI']); ?>
<?php include('config.php'); ?>
<?php include('functions.php'); ?>
<?php if(!isset($_SESSION['user']))
{
  header("location: invalidPage.php");
}
?>
<?php
$fetchStorages = "SELECT
 a.storage_id,sum(a.lost_qty)as lost_qty,sum(a.damaged_qty)as damaged_qty,
 b.storage_id,b.initials
 From inventory_storage a
 left join storage b on a.storage_id = b.storage_id
 group by b.storage_id";
$result = mysqli_query($dbcon,$fetchStorages);

$fetchStorageRequests = "SELECT
 count(a.borrower_slip_id)as bor,a.storage_id,
 b.storage_id,b.initials
 From borrower_slip a
 left join storage b on a.storage_id = b.storage_id
 group by b.storage_id";
$res = mysqli_query($dbcon,$fetchStorageRequests);


$fetchStoragesInventory = "SELECT
 a.storage_id,sum(a.original_stock)as original_stock,sum(a.stock_remain)as stock_remain,a.inventory_control_id,
 b.storage_id,b.initials,b.storage_name,
 count(c.inventory_control_id)as id
 From inventory_storage a
 left join storage b on a.storage_id = b.storage_id
 left join inventory c on a.inventory_control_id = c.inventory_control_id
 where original_stock =  stock_remain
 group by b.storage_id desc";
$resultx = mysqli_query($dbcon,$fetchStoragesInventory);


$fetchYear = mysqli_query($dbcon,"SELECT * FROM inventory group by YEAR(date_added) desc");
$fetchYear1 = mysqli_query($dbcon,"SELECT * FROM inventory group by YEAR(date_added) desc");

foreach ($fetchYear1 as $key => $year) {

  $fetchStoragesInventory1 = "SELECT
   a.storage_id,sum(a.original_stock)as original_stock,sum(a.stock_remain)as stock_remain,a.inventory_control_id,
   b.storage_id,b.initials,b.storage_name,
   count(c.inventory_control_id)as id,c.date_added
   From inventory_storage a
   left join storage b on a.storage_id = b.storage_id
   left join inventory c on a.inventory_control_id = c.inventory_control_id
   where  YEAR(c.date_added) = '".$year['date_added']."' and original_stock = stock_remain
   group by b.storage_id desc";

$resultx1 = mysqli_query($dbcon,$fetchStoragesInventory1);
$_SESSION['query'] = $resultx1;

}
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

    <link href="assets/css/style.css" rel="stylesheet" />

    <!-- Bootstrap core CSS     -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Animation library for notifications   -->
    <link href="assets/css/animate.min.css" rel="stylesheet"/>

    <!--  Light Bootstrap Table core CSS    -->
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>


		<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> -->

    <!--     Fonts and icons     -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
  <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />

	<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
 <script src="http://code.jquery.com/jquery-1.8.2.js"></script>
	<script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>

	<script src="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"></script>

	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script> -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

          var data = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            ['Work',     11],
            ['Eat',      2],
            ['Commute',  2],
            ['Watch TV', 2],
            ['Sleep',    7]
          ]);

          var options = {
            title: 'My Daily Activities'
          };

          var chart = new google.visualization.PieChart(document.getElementById('piechart2'));

          chart.draw(data, options);
        }
      </script>

       <script type="text/javascript">
         google.charts.load('current', {'packages':['bar']});
         google.charts.setOnLoadCallback(drawStuff);

         function drawStuff() {
           var data = new google.visualization.arrayToDataTable([
             ['Storages', 'Complete'],
          // <?php
          //     foreach ($resultx as $key => $value) {
          //       echo "['".$value['storage_name']."','".$value['id']."'],";
          //     }
          //  ?>
           ]);

           var options = {
             title: 'Storages',
             width: 900,
             legend: { position: 'none' },
             bars: 'horizontal', // Required for Material Bar Charts.
             axes: {
               x: {
                 0: { side: 'top', label: 'Complete  Inventory'} // Top x-axis.
               }
             },
             bar: { groupWidth: "90%" }
           };

           var chart = new google.charts.Bar(document.getElementById('top_x_div'));
           chart.draw(data, options);
         };
       </script>

   <script type="text/javascript">
     google.charts.load("current", {packages:["corechart"]});
     google.charts.setOnLoadCallback(drawChart);
     function drawChart() {
       var data = google.visualization.arrayToDataTable([
   [
   <?php
       foreach ($resultx as $key => $value) {
         echo "'".$value['initials']."',";
       }
    ?>
    { role: 'annotation' } ],
    <?php

    foreach ($fetchYear as $key => $year) {
      echo "[";
      echo "'".date('Y',strtotime($year['date_added']))."',";
    //   $fetchStoragesInventory1 = "SELECT
    //    a.storage_id,sum(a.original_stock)as original_stock,sum(a.stock_remain)as stock_remain,a.inventory_control_id,
    //    b.storage_id,b.initials,b.storage_name,
    //    count(c.inventory_control_id)as id,c.date_added
    //    From inventory_storage a
    //    left join storage b on a.storage_id = b.storage_id
    //    left join inventory c on a.inventory_control_id = c.inventory_control_id
    //    where  YEAR(c.date_added) = '".$year['date_added']."'and original_stock = stock_remain
    //    group by b.storage_id desc";
    //
    // $resultx1 = mysqli_query($dbcon,$fetchStoragesInventory1);
      foreach ($_SESSION['query'] as $key => $values) {
        echo "'".$values['id']."',''],''";
      }

      // echo "10, 24, 20, 32, 18, 5, ''],";
    } ?>
    // ['2010', 10, 24, 20, 32, 18, 5, ''],
    // ['2020', 16, 22, 23, 30, 16, 9, ''],
    // ['2030', 28, 19, 29, 30, 12, 13, '']
 ]);

 var options = {
   width: 600,
   height: 400,
   legend: { position: 'top', maxLines: 4 },
   bar: { groupWidth: '75%' },
   isStacked: true,
 };

       var chart = new google.charts.Bar(document.getElementById('piechart_3d'));
       chart.draw(data, options);
     }
   </script>

   <script type="text/javascript">
         google.charts.load('current', {'packages':['bar']});
         google.charts.setOnLoadCallback(drawStuff);

         function drawStuff() {
           var data = new google.visualization.arrayToDataTable([
             ['Storage', 'Lost', 'Damaged'],
             <?php
                 foreach ($result as $key => $value) {
                   echo "['".$value['initials']."','".$value['lost_qty']."','".$value['damaged_qty']."'],";
                 }
              ?>
           ]);

           var options = {
             width: 800,
             chart: {
             },
             bars: 'horizontal', // Required for Material Bar Charts.
             series: {
               0: { axis: 'distance' }, // Bind series 0 to an axis named 'distance'.
               1: { axis: 'brightness' } // Bind series 1 to an axis named 'brightness'.
             },
             axes: {
               x: {
                 distance: {label: ''}, // Bottom x-axis.
                 brightness: {side: 'top', label: 'Discrepancies'} // Top x-axis.
               }
             }
           };

         var chart = new google.charts.Bar(document.getElementById('dual_x_div'));
         chart.draw(data, options);
       };
       </script>
</head>
<style media="screen">
.modal-body {
 overflow-x: auto;
     }
</style>
<style media="screen">
.app-table-responsive {
 display: block;
 width: 100%;
overflow-x: auto;
-ms-overflow-style: -ms-autohiding-scrollbar;
}
.app-table-responsive.table-bordered {
border: 0;
  }
</style>
<body onload = "setTimeout(checkCookie,1000);">
<?php
	$_SESSION['user']['user_id'];
	 $id = $_SESSION['user']['user_id'];
	 $result = "SELECT
									a.user_id,a.fname,
									b.user_id,b.account_type_id

								 FROM users a
								 LEFT JOIN user_settings b on a.user_id = b.user_id

								 where b.user_id = '$id'";
		$check = mysqli_query($dbcon,$result);
		$rows = mysqli_fetch_array($check);
 ?>
 <!--

		 Tip 1: you can change the color of the sidebar using: data-color="blue | azure | green | orange | red | purple"
		 Tip 2: you can also add an image using data-image tag

 -->
 <?php  //DEAN AND DEAN ASSISTANT HEADER

 if ($rows['account_type_id']<=2) { ?>
	 <div class="wrapper" class="active" >
	     <div class="sidebar" data-color="orange" data-image="assets/img/uc.jpg">
	     	<div class="sidebar-wrapper">
	             <div class="logo">
	                 <a href="#" class="simple-text">
	                     <img src="assets/img/click_U.png"style="width:80px;height:30px;">
	                 </a>
	             </div>
	             <ul class="nav">
	 							<li class="<?php echo $currURL =='dean_home.php' ? "active" : ""; ?>">
	 								 <a href="dean_home.php"class=" ">
	 											<i class="pe-7s-home"></i>
	 											<p>Home</p>
	 									</a>
	 							</li>
 	 							<li class="<?php echo $currURL =='kitchen_staff_home.php' ? "active" : ""; ?>">
 	 								 <a href="kitchen_staff_home.php"class=" ">
 	 											<i class="pe-7s-graph"></i>
 	 											<p>Stocks</p>
 	 									</a>
 	 							</li>

									 <li class="<?php echo $currURL =='new_arrival_admin_approval.php'||$currURL =='dean_borrow_requests.php' ? "active" :" "; ?>">
									 <a href="#submenu2" data-toggle="collapse" aria-expanded="false" >
									 <!-- <div class="d-flex w-100 justify-content-start align-items-center"> -->
									 <i class="pe-7s-paper-plane"></i>
									 <p class="menu-collapsed">Requests <span class="label label-pill label-danger count-admin blink"style="border-radius:10px;"></span>
									 <b class="caret"></b>
									 </p>
									<!-- </div> -->
									</a>
									 </li>
					 <!-- Submenu content -->
					 <div id='submenu2' class="collapse sidebar-submenu">
							 <a href="new_arrival_admin_approval.php" class="list-group-item list-group-item-action bg-light notif-count-admin  <?php echo $currURL =='new_arrival_admin_approval.php' ? "active" : ""; ?>">
									 <span class="menu-collapsed">New items to verify <span class="label label-pill label-danger count-admin "style="border-radius:10px;"></span></span>
							 </a>
							 <a href="dean_borrow_requests.php" class="list-group-item list-group-item-action bg-light <?php echo $currURL =='dean_borrow_requests.php' ? "active" : ""; ?>">
									 <span class="menu-collapsed">Borrow for approval</span>
							 </a>
					 </div>
					 <li class="<?php echo $currURL =='userprofile.php'|| $currURL =='create_account.php?session_account_dropdowns' ? "active" :" "; ?>">
					 <a href="#submenu1" data-toggle="collapse" aria-expanded="false" >
					 <!-- <div class="d-flex w-100 justify-content-start align-items-center"> -->
					 <i class="pe-7s-users"></i>
					 <p class="menu-collapsed">Users <span class="label label-pill label-danger"style="border-radius:10px;"></span>
					 <b class="caret"></b>
					 </p>
					<!-- </div> -->
					</a>
					 </li>
	 <!-- Submenu content -->
	 <div id='submenu1' class="collapse sidebar-submenu">
			 <a href="userprofile.php" class="list-group-item list-group-item-action bg-light  <?php echo $currURL =='userprofile.php' ? "active" : ""; ?> ">
					 <span class="menu-collapsed">My Account</span>
			 </a>
			 <a href="create_account.php?session_account_dropdowns" class="list-group-item list-group-item-action bg-light <?php echo $currURL =='create_account.php?session_account_dropdowns' ? "active" : ""; ?>">
					 <span class="menu-collapsed">Create Account</span>
			 </a>
	 </div>
	                 <!-- <li>
	                     <a href="#">
	                         <i class="pe-7s-news-paper"></i>
	                         <p>On Use Items</p>
	                     </a>
	                 </li> -->
									 <li class=" <?php echo $currURL =='admin_view_reports.php?report'|| $currURL =='inventory_admin.php?inventory_rep'? "active" : ""; ?>">
					 				 <a href="#submenu4" data-toggle="collapse" aria-expanded="false" >
					 				 <div class="d-flex w-100 justify-content-start align-items-center">
					 				 <i class="pe-7s-folder"></i>
					 				 <p class="menu-collapsed">Reports
					 				 <b class="caret"></b>
					 				 </p>
					 				</div>
					 				</a>
					 				 </li>
					 		 <div id='submenu4' class="collapse sidebar-submenu">
					 			<a href="admin_view_reports.php?report" class="list-group-item list-group-item-action bg-light">
					 				 <span class="menu-collapsed">Borrowing</span>
					 			</a>
					 			<a href="inventory_admin.php?inventory_rep" class="list-group-item list-group-item-action bg-light">
					 				 <span class="menu-collapsed">Inventory</span>
					 			</a>
					 		 </div>

               <li class=" <?php echo $currURL =='manage_storages.php?manage_stor'|| $currURL =='manage_departments.php'? "active" : ""; ?>">
                <a href="#submenu5" data-toggle="collapse" aria-expanded="false" >
                <div class="d-flex w-100 justify-content-start align-items-center">
                <i class="pe-7s-config"></i>
                <p class="menu-collapsed">Settings
                <b class="caret"></b>
                </p>
               </div>
               </a>
                </li>
            <div id='submenu5' class="collapse sidebar-submenu">
             <a href="manage_storages.php?manage_stor" class="list-group-item list-group-item-action bg-light">
                <span class="menu-collapsed">Manage Storages</span>
             </a>
             <a href="manage_departments.php?manage_dept" class="list-group-item list-group-item-action bg-light">
                <span class="menu-collapsed">Manage Departments</span>
             </a>
            </div>
								 <!-- <li class=" <?php echo $currURL =='sss.php'|| $currURL =='sss.php'? "active" : ""; ?>">
								 <a href="#submenu5" data-toggle="collapse" aria-expanded="false" >
								 <div class="d-flex w-100 justify-content-start align-items-center">
								 <i class="pe-7s-notebook"></i>
								 <p class="menu-collapsed">Staff Logs
								 <b class="caret"></b>
								 </p>
								</div>
								</a>
								 </li> -->
						 <!-- Submenu content -->
						 <!-- <div id='submenu5' class="collapse sidebar-submenu">
							<a href="#" class="list-group-item list-group-item-action bg-light">
								 <span class="menu-collapsed">Attendance</span>
							</a>
							<a href="#" class="list-group-item list-group-item-action bg-light">
								 <span class="menu-collapsed">Activity Logs</span>
							</a>
						 </div> -->
						 <!-- <li class=" <?php echo $currURL =='manage_storages.php?manage_stor'? "active" : ""; ?>">
							<a href="admin_view_reports.php?report">
									<i class="pe-7s-note2"></i>
									<p>Reports</p>
							</a>
							</li> -->
	 				<!-- <li class="active-pro">
	                     <a href="#">
	                         <i class="pe-7s-rocket"></i>
	                         <p>TRES N PEACE</p>
	                     </a>
	                 </li> -->
	             </ul>
	     	</div>
	     </div>

	     <div class="main-panel">
	         <nav class="navbar navbar-default navbar-fixed-top">
	             <div class="container-fluid">
	                 <div class="navbar-header">
	                     <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
	                         <span class="sr-only">Toggle navigation</span>
	                         <span class="icon-bar"></span>
	                         <span class="icon-bar"></span>
	                         <span class="icon-bar"></span>
	                     </button>
	                  <img src="assets/img/click_U.png"style="width:100px;height:50px;margin-left:35%;margin-top:1%;">

	                 </div>
	                 <div class="collapse navbar-collapse">
	                     <ul class="nav navbar-nav navbar-right">
                         <li class="dropdown">
                          <a href="#" class="dropdown-toggle notif-count" data-toggle="dropdown"><span class="fa fa-bell-o" style="font-size:18px;"></span><span class="label label-pill label-danger count blink" style="border-radius:10px;"></span> </a>
                          <ul class="dropdown-menu notif-count">
                           <hr>
                         </ul>
                          </li>
	                         <li>

	                            <a href="userprofile.php"id="toggle">
	                                    <p ><i class="fa fa-user "></i> <?php echo $_SESSION['username']; ?></p>
	                             </a>

	                         </li>
	                         <li>
	                             <a href="logout.php">
	                                 <p><i class="fa fa-sign-out "></i> Log out</p>
	                             </a>
	                         </li>
	 					            	<li class="separator hidden-lg"></li>
	                     </ul>
	                 </div>
	             </div>
	         </nav>
<?php } ?>
<?php  //GENERAL STORAGE-IN-CHARGE HEADER

if ($rows['account_type_id']==3) { ?>
	<div class="wrapper" class="active">
	    <div class="sidebar" data-color="green" data-image="assets/img/uc.jpg" id="side">
	    	<div class="sidebar-wrapper">
	            <div class="logo">
	                <a href="#" class="simple-text">
	                    <img src="assets/img/click_U.png"style="width:80px;height:30px;">
	                </a>
	            </div>
	            <ul class="nav">
								<li class=" <?php echo $currURL =='kitchen_staff_home.php' ? "active" : ""; ?>">
									 <a href="kitchen_staff_home.php">
												<i class="pe-7s-home"></i>
												<p>Home</p>
										</a>
								</li>
	                <li class=" <?php echo $currURL =='userprofile.php' ? "active" : ""; ?>">
	                    <a href="userprofile.php">
	                        <i class="pe-7s-user"></i>
	                        <p>User Profile</p>
	                    </a>
	                </li>
                  <li class=" <?php echo $currURL =='restocking.php?clear_updates'||  $currURL =='manage_categories.php'|| $currURL =='transmital.php?transmital'? "active" : ""; ?>">
									 <a href="#submenu2" data-toggle="collapse" aria-expanded="false" >
							     <div class="d-flex w-100 justify-content-start align-items-center">
									 <i class="pe-7s-note"></i>
									 <p class="menu-collapsed">Inventory
									 <b class="caret"></b>
									 </p>
							    </div>
					        </a>
									 </li>
					 <!-- Submenu content -->
					 <div id='submenu2' class="collapse sidebar-submenu">
							 <a href="restocking.php?clear_updates" class="list-group-item list-group-item-action bg-light">
									 <span class="menu-collapsed">Add New Item / Update qty</span>
							 </a>

               <a href="manage_categories.php?manage_cat" class="list-group-item list-group-item-action bg-light">
									 <span class="menu-collapsed">Manage categories</span>
							 </a>
							 <a href="transmital.php?transmital" class="list-group-item list-group-item-action bg-light">
									 <span class="menu-collapsed">Transfer items</span>
							 </a>
					 </div>
					 <li class=" <?php echo $currURL =='borrow_requests.php'|| $currURL =='returnRequest.php'|| $currURL =='activeRequests.php'|| $currURL =='discrepancyReport.php'? "active" : ""; ?>">
						<a href="#submenu3" data-toggle="collapse" aria-expanded="false" >
						<div class="d-flex w-100 justify-content-start align-items-center">
						<i class="pe-7s-paper-plane"></i>
						<p class="menu-collapsed">Borrow requests
						<b class="caret"></b>
						</p>
					 </div>
					 </a>
					 </li>
		<!-- Submenu content -->
		<div id='submenu3' class="collapse sidebar-submenu">
				<a href="borrow_requests.php" class="list-group-item list-group-item-action bg-light">
						<span class="menu-collapsed">Releasing</span>
				</a>
				<a href="returnRequest.php" class="list-group-item list-group-item-action bg-light">
						<span class="menu-collapsed">Receiving</span>
				</a>
				<a href="activeRequests.php" class="list-group-item list-group-item-action bg-light">
						<span class="menu-collapsed">Active</span>
				</a>
				<a href="discrepancyReport.php" class="list-group-item list-group-item-action bg-light">
					 <span class="menu-collapsed">Discrepancies</span>
			 </a>
		</div>
   <li class=" <?php echo $currURL =='reports.php?report'|| $currURL =='staff_inventory_report.php?inventory_rep'|| $currURL =='staff_inventory_report.php'? "active" : ""; ?>">
   <a href="#submenu4" data-toggle="collapse" aria-expanded="false" >
   <div class="d-flex w-100 justify-content-start align-items-center">
   <i class="pe-7s-folder"></i>
   <p class="menu-collapsed">Reports
   <b class="caret"></b>
   </p>
  </div>
  </a>
   </li>
 <div id='submenu4' class="collapse sidebar-submenu">
 <a href="reports.php?report" class="list-group-item list-group-item-action bg-light">
   <span class="menu-collapsed">Borrowing</span>
 </a>
 <a href="staff_inventory_report.php?inventory_rep" class="list-group-item list-group-item-action bg-light">
   <span class="menu-collapsed">Inventory</span>
 </a>
 </div>
		 <li class=" <?php echo $currURL =='history.php'? "active" : ""; ?>">
			<a href="history.php">
					<i class="pe-7s-clock"></i>
					<p>History</p>
			</a>
   	</li>
      </ul>
	   </div>
	    </div>
	    <div class="main-panel">
	        <nav class="navbar navbar-default navbar-fixed-top">
	            <div class="container-fluid">
	                <div class="navbar-header">
	                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
	                        <span class="sr-only">Toggle navigation</span>
	                        <span class="icon-bar"></span>
	                        <span class="icon-bar"></span>
	                        <span class="icon-bar"></span>
	                    </button>
	                 <img src="assets/img/click_U.png"style="width:100px;height:50px;margin-left:35%;margin-top:1%;">
	                </div>
	                <div class="collapse navbar-collapse">
	                    <ul class="nav navbar-nav navbar-right">
												<li class="dropdown">
                        <a href="#" class="dropdown-toggle notif-count" data-toggle="dropdown"><span class="fa fa-bell-o" style="font-size:18px;"></span><span class="label label-pill label-danger count blink" style="border-radius:10px;"></span> </a>
                        <ul class="dropdown-menu notif-count">
													<hr>
												</ul>
                        </li>
	                        <li>
														<?php
															$_SESSION['user']['user_id'];
															 $id = $_SESSION['user']['user_id'];
															 $result = "SELECT
																							a.user_id,a.fname,
																							b.user_id,b.account_type_id

																						 FROM users a
																						 LEFT JOIN user_settings b on a.user_id = b.user_id

																						 where b.user_id = '$id' ";
																$check = mysqli_query($dbcon,$result);
																$rows = mysqli_fetch_array($check);
															 if($rows['account_type_id']=="3"){
													?>

	                           <a href="userprofile.php"id="toggle">
	                                   <p ><i class="fa fa-user "></i> <?php echo $rows['fname']; ?></p>

	                            </a>
	                         <?php } ?>
	                        </li>
	                        <li>
	                            <a href="logout.php">
	                                <p><i class="fa fa-sign-out "></i> Log out</p>
	                            </a>
	                        </li>
						        	<li class="separator hidden-lg"></li>
	                    </ul>
	                </div>
	            </div>
	        </nav>
<?php } ?>

<?php  //KITCHEN STAFF AND WORKING STUDENT HEADER

if ($rows['account_type_id']==4 || $rows['account_type_id']==5) { ?>
	<div class="wrapper" class="active">
	    <div class="sidebar" data-color="purple" data-image="assets/img/uc.jpg" id="side">
	    	<div class="sidebar-wrapper">
	            <div class="logo">
	                <a href="#" class="simple-text">
	                  <img src="assets/img/click_U.png"style="width:80px;height:30px;">
	                </a>
	            </div>

	            <ul class="nav">
								<li class=" <?php echo $currURL =='kitchen_staff_home.php' ? "active" : ""; ?>">
									 <a href="kitchen_staff_home.php">
												<i class="pe-7s-home"></i>
												<p>Home</p>
										</a>
								</li>
	                <li class=" <?php echo $currURL =='userprofile.php' ? "active" : ""; ?>">
	                    <a href="userprofile.php">
	                        <i class="pe-7s-user"></i>
	                        <p>User Profile</p>
	                    </a>
	                </li>
									<li class=" <?php echo $currURL =='borrow_requests.php'|| $currURL =='returnRequest.php'|| $currURL =='activeRequests.php'|| $currURL =='discrepancyReport.php'? "active" : ""; ?>">
									 <a href="#submenu2" data-toggle="collapse" aria-expanded="false" >
									 <div class="d-flex w-100 justify-content-start align-items-center">
									 <i class="pe-7s-paper-plane"></i>
									 <p class="menu-collapsed">Requests
									 <b class="caret"></b>
									 </p>
									</div>
									</a>
									 </li>
					 <!-- Submenu content -->
					 <div id='submenu2' class="collapse sidebar-submenu">
							 <a href="borrow_requests.php" class="list-group-item list-group-item-action bg-light notif-count-admin">
									 <span class="menu-collapse ">Releasing </span>
							 </a>
							 <a href="returnRequest.php" class="list-group-item list-group-item-action bg-light">
									 <span class="menu-collapsed">Receiving</span>
							 </a>
							 <a href="activeRequests.php" class="list-group-item list-group-item-action bg-light">
									 <span class="menu-collapsed">Active</span>
							 </a>
							 <a href="discrepancyReport.php" class="list-group-item list-group-item-action bg-light">
									<span class="menu-collapsed">Discrepancies</span>
							</a>
					 </div>
           <li class=" <?php echo $currURL =='reports.php?report'|| $currURL =='staff_inventory_report.php?inventory_rep'? "active" : ""; ?>">
           <a href="#submenu4" data-toggle="collapse" aria-expanded="false" >
           <div class="d-flex w-100 justify-content-start align-items-center">
           <i class="pe-7s-folder"></i>
           <p class="menu-collapsed">Reports
           <b class="caret"></b>
           </p>
          </div>
          </a>
           </li>
        <div id='submenu4' class="collapse sidebar-submenu">
        <a href="reports.php?report" class="list-group-item list-group-item-action bg-light">
           <span class="menu-collapsed">Borrowing</span>
        </a>
        <a href="staff_inventory_report.php?inventory_rep" class="list-group-item list-group-item-action bg-light">
           <span class="menu-collapsed">Inventory</span>
        </a>
        </div>
		<!-- <li class=" <?php echo $currURL =='discrepancyReport.php'? "active" : ""; ?>">
		 <a href="#submenu4" data-toggle="collapse" aria-expanded="false" >
		 <div class="d-flex w-100 justify-content-start align-items-center">
		 <i class="pe-7s-folder"></i>
		 <p class="menu-collapsed">Reports
		 <b class="caret"></b>
		 </p>
		</div>
		</a>
		 </li> -->
 <!-- Submenu content -->
 <!-- <div id='submenu4' class="collapse sidebar-submenu">
	<a href="#" class="list-group-item list-group-item-action bg-light">
		 <span class="menu-collapsed">Borrowed items</span>
	</a>
	<a href="discrepancyReport.php" class="list-group-item list-group-item-action bg-light">
		 <span class="menu-collapsed">Discrepancies</span>
	</a>
 </div> -->
	 		<li class=" <?php echo $currURL =='history.php'? "active" : ""; ?>">
			 <a href="history.php">
					 <i class="pe-7s-clock"></i>
					 <p>History</p>
			 </a>
	 </li>

	            </ul>
	    	</div>
	    </div>

	    <div class="main-panel">
	        <nav class="navbar navbar-default navbar-fixed-top">
	            <div class="container-fluid">
	                <div class="navbar-header">
	                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
	                        <span class="sr-only">Toggle navigation</span>
	                        <span class="icon-bar"></span>
	                        <span class="icon-bar"></span>
	                        <span class="icon-bar"></span>
	                    </button>
	                 <img src="assets/img/click_U.png"style="width:100px;height:50px;margin-left:35%;margin-top:2%;">
	                </div>
	                <div class="collapse navbar-collapse">

	                    <ul class="nav navbar-nav navbar-right">
												<li class="dropdown">
                        <a href="#" class="dropdown-toggle notif-count" data-toggle="dropdown"><span class="fa fa-bell-o" style="font-size:18px;"></span><span class="label label-pill label-danger count blink" style="border-radius:10px;"></span> </a>
                        <ul class="dropdown-menu notif-count">
													<hr>
												</ul>
                        </li>
	                        <li>
														<?php
															$_SESSION['user']['user_id'];
															 $id = $_SESSION['user']['user_id'];
															 $result = "SELECT
																							a.user_id,a.fname,
																							b.user_id,b.account_type_id

																						 FROM users a
																						 LEFT JOIN user_settings b on a.user_id = b.user_id

																						 where b.user_id = '$id' ";
																$check = mysqli_query($dbcon,$result);
																$rows = mysqli_fetch_array($check);
															 if($rows['account_type_id']=="4"||$rows['account_type_id']=="5"){
													?>

	                           <a href="userprofile.php"id="toggle">
	                                   <p ><i class="fa fa-user "></i> <?php echo $rows['fname']; ?></p>

	                            </a>
	                         <?php } ?>
	                        </li>
	                        <li>
	                            <a href="logout.php">
	                                <p><i class="fa fa-sign-out "></i> Log out</p>
	                            </a>
	                        </li>
							          <li class="separator hidden-lg"></li>
	                    </ul>
	                </div>
	            </div>
	        </nav>
<?php } ?>


<?php  //STUDENT AND TEACHER HEADER

if ($rows['account_type_id']==6 || $rows['account_type_id']==7) { ?>
	<div class="wrapper" class="active">
	    <div class="sidebar" data-color="azure" data-image="assets/img/uc.jpg" id="side">

	    <!--

	        Tip 1: you can change the color of the sidebar using: data-color="blue | azure | green | orange | red | purple"
	        Tip 2: you can also add an image using data-image tag

	    -->

	    	<div class="sidebar-wrapper">
	            <div class="logo">
	                <a href="#" class="simple-text">
	                  <img src="assets/img/click_U.png"style="width:80px;height:30px;">
	                </a>
	            </div>

	            <ul class="nav">
								<li class="<?php echo $currURL =='userSelectStorage.php?borrow_form'  || $currURL =='creategroup.php?create_group' ? "active" : ""; ?>">
									 <a href="userSelectStorage.php?borrow_form"class=" ">
												<i class="pe-7s-home"></i>
												<p>Home</p>
										</a>
								</li>
	                <li class="<?php echo $currURL =='userprofile.php' ? "active" : ""; ?>">
	                    <a href="userprofile.php"class=" ">
	                        <i class="pe-7s-user"></i>
	                        <p>My Profile</p>
	                    </a>
	                </li>
	                <li class="<?php echo $currURL =='userRequestsMenu2.php' ? "active" : ""; ?>">
	                    <a href="userRequestsMenu2.php">
	                        <i class="pe-7s-paper-plane"></i>
	                        <p>My Requests

													</p>
	                    </a>
	                </li>
									<li class=" <?php echo $currURL =='history.php'? "active" : ""; ?>">
						 			 <a href="history.php">
						 					 <i class="pe-7s-clock"></i>
						 					 <p>History</p>
						 			 </a>
						 	 </li>

	                <!-- <li>
	                    <a href="#">
	                        <i class="pe-7s-folder"></i>
	                        <p>My Reports</p>
	                    </a>
	                </li> -->

					<!-- <li class="active-pro">
	                    <a href="#">
	                        <i class="pe-7s-rocket"></i>
	                        <p>TRES N PEACE</p>
	                    </a>
	                </li> -->
	            </ul>
	    	</div>
	    </div>

	    <div class="main-panel">
	        <nav class="navbar navbar-default navbar-fixed-top">
	            <div class="container-fluid">
	                <div class="navbar-header">
	                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
	                        <span class="sr-only">Toggle navigation</span>
	                        <span class="icon-bar"></span>
	                        <span class="icon-bar"></span>
	                        <span class="icon-bar"></span>
	                    </button>
	                 <img src="assets/img/click_U.png"style="width:100px;height:50px;margin-left:35%;margin-top:2%;">
	                </div>
	                <div class="collapse navbar-collapse">
										<!-- <ul class="nav navbar-nav navbar-left"style="margin-left:5%;">
												<li class="dropdown">
															<a href="#" class="dropdown-toggle" data-toggle="dropdown">
																		<i class="pe-7s-bell animate "></i><span class="label label-pill label-danger count blink" style="border-radius:10px;"></span>
																		<b class="caret hidden-lg hidden-md"></b>
											 <p class="hidden-lg hidden-md">
									    <b class="caret"></b>
							          	 </p>
															</a>
															<ul class="dropdown-menu notif-count">
															</ul>
												</li>
										</ul> -->
	                    <ul class="nav navbar-nav navbar-right">
												<li class="dropdown">
                        <a href="#" class="dropdown-toggle notif-count" data-toggle="dropdown"><span class="fa fa-bell-o" style="font-size:18px;"></span><span class="label label-pill label-danger count blink" style="border-radius:10px;"></span> </a>
                        <ul class="dropdown-menu notif-count">
													<hr>
												</ul>
                        </li>
	                        <li>
														<?php
															$_SESSION['user']['user_id'];
															 $id = $_SESSION['user']['user_id'];
															 $result = "SELECT
																							a.user_id,a.fname,
																							b.user_id,b.account_type_id

																						 FROM users a
																						 LEFT JOIN user_settings b on a.user_id = b.user_id

																						 where b.user_id = '$id' ";
																$check = mysqli_query($dbcon,$result);
																$rows = mysqli_fetch_array($check);
															 if($rows['account_type_id']=="6"){
												      	?>
	                           <a href="userprofile.php">
	                                   <p ><i class="fa fa-user "></i> <?php echo $rows['fname']; ?></p>
	                            </a>
	                         <?php } elseif ($rows['account_type_id']=="7") { ?>
														 <a href="userprofile.php">
		 																<p ><i class="fa fa-user "></i> <?php echo $rows['fname']; ?></p>
		 												 </a>
	                      <?php }?>
	                        </li>
	                        <li>
	                            <a href="logout.php">
	                                <p><i class="fa fa-sign-out "></i> Log out</p>
	                            </a>
	                        </li>
							         <li class="separator hidden-lg"></li>
	                    </ul>
	                </div>
	            </div>
	        </nav>
<?php } ?>
