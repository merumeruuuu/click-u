<?php include('header.php') ?>
<script type="text/javascript">
$(document).ready(function(){
	$('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
		localStorage.setItem('activeTab', $(e.target).attr('href'));
	});
	var activeTab = localStorage.getItem('activeTab');
	if(activeTab){
		$('#myTab a[href="' + activeTab + '"]').tab('show');
	}
});
</script>
<?php if (isset($_GET['clear_updates'])) {
	unset($_SESSION['add_qty']);
	unset($_SESSION['deduct_qty']);
} ?>
<?php //Add utensils
 $success = array();
 $errors = array();
if (isset($_POST['add_new_item'])) {
  $item_name_desc = mysqli_real_escape_string($dbcon, $_POST['item_name_desc']);
  $category = mysqli_real_escape_string($dbcon, $_POST['category']);
  $qty = mysqli_real_escape_string($dbcon, $_POST['qty']);
  $umsr = mysqli_real_escape_string($dbcon, $_POST['umsr']);
  $model = mysqli_real_escape_string($dbcon, $_POST['model']);
  $serial = mysqli_real_escape_string($dbcon, $_POST['serial']);
  $date_received = mysqli_real_escape_string($dbcon, $_POST['date_received']);
  $cost = mysqli_real_escape_string($dbcon, $_POST['cost']);
  // $storage = mysqli_real_escape_string($dbcon, $_POST['storage']);
  $date_purchased = mysqli_real_escape_string($dbcon, $_POST['date_purchased']);
	$_SESSION['item_name_desc'] = $item_name_desc;
	$_SESSION['category'] = $category;
	$_SESSION['qty'] = $qty;
	$_SESSION['umsr'] = $umsr;
	$_SESSION['model'] = $model;
	$_SESSION['serial'] = $serial;
	$_SESSION['date_received'] = $date_received;
	$_SESSION['cost'] = $cost;
	$_SESSION['date_purchased'] = $date_purchased;
  $status = "0";
  $_SESSION['user']['user_id'];
  $id = $_SESSION['user']['user_id'];
  $currentDate =  date('Y-m-d H:i:s');

	$exName = trim($item_name_desc);
	$checkExisting = mysqli_query($dbcon,"SELECT * FROM utensils where utensils_name = '$exName' and utensils_cat_id = '$category'");
	if (mysqli_num_rows($checkExisting)>0) {
		array_push($errors,"Item exists! Update the quantity instead!");
	}else {
		if ($date_received <= $currentDate && $date_purchased <= $currentDate && $date_purchased <= $date_received) {
			array_push($success,"New item added!");
		}else {
			array_push($errors,"Invalid date!");
		}
	}
   if (count($errors) == 0) {
      $queryx = mysqli_query($dbcon,"INSERT INTO utensils (utensils_name,original_stock, utensils_cat_id, added_by, date_added, cost, umsr, model, serial_no,date_purchased)
           VALUES('$item_name_desc', '$qty', '$category','$id',NOW(),'$cost','$umsr','$model','$serial','$date_purchased')");

       $query = mysqli_query($dbcon, "SELECT utensils_id FROM utensils ORDER BY utensils_id DESC LIMIT 1") or die(mysqli_error($dbcon));
       $utensilID = mysqli_fetch_array($query);
       $insertnewitems = mysqli_query($dbcon, "INSERT INTO new_arrival_utensils (date_received, new_arvl_qty, utensils_id,received_by,checked_by,status)
        VALUES('".$date_received."','".$qty."','".$utensilID['utensils_id']."','".$id."','".$id."','".$status."')") or die("details: ".mysqli_error($dbcon));
   // echo "<script>alert('New item added successfully!');window.location.href='addutensils.php';</script>";
   // array_push($success,"New item added!");
 }
}
 ?>

 <?php
 if(isset($_POST["qty_to_add"]))
{
      $uID = mysqli_real_escape_string($dbcon,$_POST['utensils_id']);
      $qty = mysqli_real_escape_string($dbcon,$_POST['qty']);

			if(isset($_SESSION["deduct_qty"])||isset($_SESSION["add_qty"]))
		  {
				if (isset($_SESSION["deduct_qty"])) {

		 			$item_array_id = array_column($_SESSION["deduct_qty"], "utensils_id");
		 			if(!in_array($_POST['utensils_id'], $item_array_id))
		 			{
						if(isset($_SESSION["add_qty"]))
						{
								 $item_array_id = array_column($_SESSION["add_qty"], "utensils_id");
								 if(!in_array($_POST['utensils_id'], $item_array_id))
								 {
											$count = count($_SESSION["add_qty"]);
											$item_array = array(
												 'utensils_id'    =>     $_POST['utensils_id'],
												 'qty'        =>     $_POST["qty"]

											);
											$_SESSION["add_qty"][$count] = $item_array;

								 }
								 else
								 {
									 array_push($errors,"Item Already Added!");
								 }

						}
						else
						{
								 $item_array = array(
										'utensils_id'    =>    $_POST['utensils_id'],
										'qty'        =>     $_POST["qty"]
								 );

								 $_SESSION["add_qty"][0] = $item_array;
						}
		 			}
		 		 else
		 			{
		 				array_push($errors,"Error cannot select two items simultaneously!");
		 			}
				}else {
					if(isset($_SESSION["add_qty"]))
					{
							 $item_array_id = array_column($_SESSION["add_qty"], "utensils_id");
							 if(!in_array($_POST['utensils_id'], $item_array_id))
							 {
										$count = count($_SESSION["add_qty"]);
										$item_array = array(
											 'utensils_id'    =>     $_POST['utensils_id'],
											 'qty'        =>     $_POST["qty"]

										);
										$_SESSION["add_qty"][$count] = $item_array;

							 }
							 else
							 {
								 array_push($errors,"Item Already Added!");
							 }
					}
					else
					{
							 $item_array = array(
									'utensils_id'    =>    $_POST['utensils_id'],
									'qty'        =>     $_POST["qty"]
							 );

							 $_SESSION["add_qty"][0] = $item_array;
					}
				}
		  }else {
				$item_array = array(
					 'utensils_id'    =>    $_POST['utensils_id'],
					 'qty'        =>     $_POST["qty"]
				);

				$_SESSION["add_qty"][0] = $item_array;
		  }
}
  if(isset($_GET["action"]))
  {
      if($_GET["action"] == "remove_add")
      {
           foreach(array_filter($_SESSION["add_qty"]) as $keys => $values)
           {
                if($values["utensils_id"] == $_GET["ids"])
                {
                     // unset($_SESSION["item_tray"][$keys]);
                      $_SESSION["add_qty"][$keys] = Null;
                     // echo '<script>alert("Item cancelled!")</script>';
                     // echo '<script>window.location="modifyUserRequests.php"</script>';

                }
           }
      }
  } ?>

  <?php //deduct qty
  if(isset($_POST["qty_to_deduct"]))
{
       $uID = mysqli_real_escape_string($dbcon,$_POST['utensils_id']);
       $qty = mysqli_real_escape_string($dbcon,$_POST['qty']);
  $checkCurQTY = mysqli_query($dbcon,"SELECT * FROM utensils where stock_on_hand < $qty and utensils_id = $uID");
	if (mysqli_num_rows($checkCurQTY)>0) {
		array_push($errors,"Invalid quantity!");
	}else {

  // trap if in add session
	if(isset($_SESSION["add_qty"])||isset($_SESSION["deduct_qty"]))
		{
			if (isset($_SESSION["add_qty"])) {

				 $item_array_id = array_column($_SESSION["add_qty"], "utensils_id");
				 if(!in_array($_POST['utensils_id'], $item_array_id))
				 {
					 // insertion for deduction
					 if(isset($_SESSION["deduct_qty"]))
					 {
								$item_array_id = array_column($_SESSION["deduct_qty"], "utensils_id");
								if(!in_array($_POST['utensils_id'], $item_array_id))
								{
										 $count = count($_SESSION["deduct_qty"]);
										 $item_array = array(
											 'utensils_id'    =>     $_POST['utensils_id'],
											 'reason'    =>     $_POST['reason'],
											 'qty'        =>     $_POST["qty"]

										 );
										 $_SESSION["deduct_qty"][$count] = $item_array;
								}
							 else
								{
									array_push($errors,"Item Already Added!");
								}
					 }
					 else
					 {
								$item_array = array(
									'utensils_id'    =>    $_POST['utensils_id'],
									'reason'    =>     $_POST['reason'],
									'qty'        =>     $_POST["qty"]
								);

								$_SESSION["deduct_qty"][0] = $item_array;
					 }
				 } // end of insertion
				 else
				 {
					 array_push($errors,"Error cannot select two items simultaneously!");
				 }

			 }else {
				 if(isset($_SESSION["deduct_qty"]))
				 {
							$item_array_id = array_column($_SESSION["deduct_qty"], "utensils_id");
							if(!in_array($_POST['utensils_id'], $item_array_id))
							{
									 $count = count($_SESSION["deduct_qty"]);
									 $item_array = array(
										 'utensils_id'    =>     $_POST['utensils_id'],
										 'reason'    =>     $_POST['reason'],
										 'qty'        =>     $_POST["qty"]

									 );
									 $_SESSION["deduct_qty"][$count] = $item_array;
							}
						 else
							{
								array_push($errors,"Item Already Added!");
							}
				 }
				 else
				 {
							$item_array = array(
								'utensils_id'    =>    $_POST['utensils_id'],
								'reason'    =>     $_POST['reason'],
								'qty'        =>     $_POST["qty"]
							);

							$_SESSION["deduct_qty"][0] = $item_array;
				 }
			 }

		}else {
			$item_array = array(
				'utensils_id'    =>    $_POST['utensils_id'],
				'reason'    =>     $_POST['reason'],
				'qty'        =>     $_POST["qty"]
			);

			$_SESSION["deduct_qty"][0] = $item_array;
		}
	}
}
   if(isset($_GET["action"]))
   {
       if($_GET["action"] == "remove_deduct")
       {
            foreach(array_filter($_SESSION["deduct_qty"]) as $keys => $values)
            {
                 if($values["utensils_id"] == $_GET["ids"])
                 {
                      // unset($_SESSION["item_tray"][$keys]);
                       $_SESSION["deduct_qty"][$keys] = Null;
                      // echo '<script>alert("Item cancelled!")</script>';
                      // echo '<script>window.location="modifyUserRequests.php"</script>';

                 }
            }
       }
   } ?>
<?php
if (isset($_GET['apply_updates'])) {

  if (empty($_SESSION['add_qty'])&&empty($_SESSION['deduct_qty'])) {
  	array_push($errors,"Please select items!");
  }else {
		if (isset($_SESSION['add_qty'])) {
		foreach (array_filter($_SESSION['add_qty']) as $key => $value) {
	$queryUtensils = mysqli_query($dbcon,"SELECT * FROM utensils where utensils_id = '".$value['utensils_id']."'");
	foreach ($queryUtensils as $key => $curUtensil) {
		$newOrigQty = $curUtensil['original_stock'] + $value['qty'];
		$newCurQty = $curUtensil['stock_on_hand'] + $value['qty'];
		$updateAddUtensils = mysqli_query($dbcon,"UPDATE utensils set original_stock = $newOrigQty,stock_on_hand = $newCurQty where utensils_id = '".$curUtensil['utensils_id']."'");
	    }
    }
	}
	if (isset($_SESSION['deduct_qty'])) {
		foreach (array_filter($_SESSION['deduct_qty']) as $key => $values) {
	$queryUtensils2 = mysqli_query($dbcon,"SELECT * FROM utensils where utensils_id = '".$values['utensils_id']."'");
	foreach ($queryUtensils2 as $key => $curUtensil2) {
		$newOrigQty1 = $curUtensil2['original_stock'] - $values['qty'];
		$newCurQty1 = $curUtensil2['stock_on_hand'] - $values['qty'];
		$updateDeductUtensils = mysqli_query($dbcon,"UPDATE utensils set original_stock = $newOrigQty1,stock_on_hand = $newCurQty1 where utensils_id = '".$curUtensil2['utensils_id']."'");
	    }
    }
	 }
	 echo "<script>alert('Updated successfully!');window.location.href='kitchen_staff_home.php';</script>";
	}
} ?>
<br><br>
<div class="content" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                  <div class="row">
                    <div class="col-md-5">
                      <div class="header">
                          <h4 class="title">Add new item / Restocking </h4>
                      </div>
                    </div>
                    <div class="col-md-5">
                      <div class="">
												<?php include('success.php'); ?>
                       <?php include('errors.php') ?>
                      </div>
                    </div>
                  </div>
                    <div class="content" style="overflow-x:auto;">
                      <div class="row">
                        <div class="col-md-12">
                          <ul class="nav nav-tabs" id="myTab">
                             <li class="active"><a  data-toggle="tab" href="#sectionA">Add new items</a></li>
                             <li><a  data-toggle="tab" href="#sectionB">Update qty</a></li>
                          </ul>
                        </div>
                      </div>
                        <br>
                     <div class="row">
                         <div class="tab-content">
<div id="sectionA" class="tab-pane fade in active">
    <div class="content">
                      <?php
											 	$category2 = mysqli_query($dbcon, "SELECT * FROM utensils_category where utensils_cat_id = ".$_SESSION['category']);
												$category = mysqli_query($dbcon, "SELECT * FROM utensils_category");
                        $storage = mysqli_query($dbcon, "SELECT * FROM storage");
												$umsr2 = mysqli_query($dbcon, "SELECT * FROM umsr where id = ".$_SESSION['umsr']);
                        $umsr = mysqli_query($dbcon, "SELECT * FROM umsr");

                      ?>
                    <form method="post" action="restocking.php"name="actorInsert"oninput="return ValidateActInsert">
                     <div class="col-md-12">
                       <!-- <?php include('success.php'); ?> -->
                       <!-- <?php include('errors.php'); ?> -->
                     </div>
                     <div class="col-md-12">
                     <label for="title"> <b>Complete name and description of the item </b>  <b class="text-danger"style="font-size:16px;">*</b></label>
                     <input type="text"value="<?php if (isset($_SESSION['item_name_desc'])) {
                     	echo $_SESSION['item_name_desc'];
                     } ?>" class="form-control"name="item_name_desc" placeholder="" required>
                     </div>
                     <div class="col-md-6">
                     <label for="inputState"> <b>Category</b>  <b class="text-danger"style="font-size:16px;">*</b></label>
                     <select class="form-control"name="category"required>
											 <?php
											 if (isset($_SESSION['category'])) {
												 foreach ($category2 as $key => $value)
												 ?><option value="<?php echo $_SESSION['category']; ?>" selected><?php echo $value['category']; ?></option><?php
											 	foreach ($category as $key => $row) {
											 		?><option value="<?php echo $row['utensils_cat_id']; ?>"><?php echo $row['category']; ?></option><?php
											 	}
											}else {
											?>
											 <option value=""hidden disabled selected>Choose here</option>
                    <?php
										if($category) {
                    while($row = mysqli_fetch_array($category)) {
                    ?>
                    <option value="<?php echo $row['utensils_cat_id']; ?>"><?php echo $row['category']; ?></option>
                    <?php
                    }
                    }
									}?>
                    </select>
                    </div>

                   <div class="col-md-3">
                   <label for="inputAddress"> <b>Quantity</b> <b class="text-danger"style="font-size:16px;">*</b></label>
                   <input type="number"id="numInput" class="form-control"value="<?php if (isset($_SESSION['qty'])) {
										echo $_SESSION['qty'];
									 } ?>" min="1"name="qty" placeholder=""required>
                   </div>
                   <div class="col-md-3">
                   <label for="inputAddress">UMSR</label>
                   <select class="form-control"name="umsr">
                   <!-- <?php if($umsr) {
                   while($row = mysqli_fetch_array($umsr)) {
                   ?>
                   <option value="<?php echo $row['id']; ?>"><?php echo $row['umsr_name']; ?></option>
                  <?php
                   }
                   } ?> -->

									 <?php
									 if (isset($_SESSION['umsr'])) {
										 foreach ($umsr2 as $key => $value)
										 ?><option value="<?php echo $_SESSION['umsr']; ?>" selected><?php echo $value['umsr_name']; ?></option><?php
										foreach ($umsr as $key => $row) {
											?><option value="<?php echo $row['id']; ?>"><?php echo $row['umsr_name']; ?></option><?php
										}
									}else {
									?>
									 <option value=""hidden disabled selected>Choose here</option>
								 <?php
								if($umsr) {
								 while($row = mysqli_fetch_array($umsr)) {
								 ?>
								 <option value="<?php echo $row['id']; ?>"><?php echo $row['umsr_name']; ?></option>
								 <?php
								 }
								 }
							}?>
                   </select>
                   </div>
                   <div class="col-md-6">
                   <label for="inputAddress">Model</label>
                   <input type="text" id="textbox" class="form-control"value="<?php if (isset($_SESSION['model'])) {
										echo $_SESSION['model'];
									 } ?>" name="model" placeholder="">
                   </div>
                   <div class="col-md-6">
                   <label for="inputAddress">Serial Number</label>
                   <input type="text" id="textbox1"class="form-control"value="<?php if (isset($_SESSION['serial'])) {
										echo $_SESSION['serial'];
									 } ?>"name="serial" placeholder="">
                   </div>


                   <div class="col-md-2">
                   <label for="inputAddress"> <b>Date Purchased</b>  <b class="text-danger"style="font-size:16px;">*</b></label>
                   <input type="date" data-date-format='yy-mm-dd'id=""value="<?php if (isset($_SESSION['date_purchased'])) {
										echo $_SESSION['date_purchased'];
									 } ?>" class="form-control"name="date_purchased" placeholder="yy/mm/dd"required>
                   </div>
                   <div class="col-md-2">
                   <label for="inputAddress"> <b> Date Received </b><b class="text-danger"style="font-size:16px;">*</b></label>
                   <input type="date"data-date-format='yy-mm-dd' id=""value="<?php if (isset($_SESSION['date_received'])) {
										echo $_SESSION['date_received'];
									 } ?>"class="form-control"name="date_received" placeholder="yy/mm/dd"required>
                   </div>

                   <div class="col-md-2">
                   <label for="inputAddress"> <b></b> Unit Cost <b class="text-danger"style="font-size:16px;">*</b></label>
                   <input type="number" id="numInput"value="<?php if (isset($_SESSION['cost'])) {
										echo $_SESSION['cost'];
									 } ?>"class="form-control"min="1"name="cost" placeholder="₱ 00.00"required>
                   </div>

                   <div class="row">
                   <div class="col-md-4">
                   <br>
                   <input class="btn btn-info btn-fill" type="submit" name="add_new_item" value="Add">
                   <a href="kitchen_staff_home.php" class="btn btn-danger btn-fill">Cancel</a>
                   </div>
                   </div>
                   </form>
       </div>
</div>
<div id="sectionB" class="tab-pane fade in ">
  <div class="content">
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="content">

        <table class="table"id="update_qty">
          <thead>
            <tr>
              <th>ACTION</th>
              <th>ID</th>
              <th>ORIG-QTY</th>
              <th>CUR-QTY</th>
              <th>ITEMS</th>
              <th>CATEGORY</th>
              <th>MODEL</th>
              <th>SERIAL NO.</th>
            </tr>
          </thead>
          <tbody>
            <?php $queryString = "SELECT *
                                 FROM utensils a
                                 left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
																 where a.status  = 1
                                 order by a.utensils_id desc";
                  $result = mysqli_query($dbcon,$queryString);
            foreach ($result as $key => $value) {
            ?>
            <tr>
              <td>
                <a href="#"data-toggle="modal"data-id="<?php echo $value['utensils_id']; ?>" class="click_pin"><i class="fa fa-plus text-success"></i></a> |
                <a href="#"data-toggle="modal"data-id="<?php echo $value['utensils_id']; ?>"class="click_pins"><i class="fa fa-minus text-warning"></i></a>
              </td>
              <td class="bg bg-info"><?php echo $value['utensils_id'] ?></td>
              <td class="bg bg-warning"><?php echo $value['original_stock'] ?></td>
							<?php
							$fetchEmpty = mysqli_query($dbcon,"SELECT SUM(storage_qty) as current_qty FROM storage_stocks where utensils_id = ".$value['utensils_id']);
							foreach ($fetchEmpty as $key => $curr) {
							?>
							 <td class="bg bg-warning"><?php echo $curr['current_qty'] ?></td>
							<?php
							}
							 ?>
              <td class="bg bg-info"><?php echo $value['utensils_name'] ?></td>
              <td class="bg bg-info"><?php echo $value['category'] ?></td>
              <td class="bg bg-info"><?php echo $value['model'] ?></td>
              <td class="bg bg-info"><?php echo $value['serial_no'] ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="content">
          <?php if (isset($_SESSION['add_qty'])||isset($_SESSION['deduct_qty'])) {
            ?>
             <div class="card">
               <div class="content">
                 <div class="row">
                   <div class="col-md-2">
                     <a href="?apply_updates"onclick="return confirm('Confirm update!');"class="btn btn-sm btn-fill btn-success">Apply updates</a>
                   </div>
                   <div class="col-md-6">
                     <a href="?clear_updates"class="btn btn-sm btn-fill btn-warning">Clear updates</a>
                   </div>
                 </div>
                 <?php if (isset($_SESSION['add_qty'])) { ?>
               <h4>Items to increase quantity :</h4>
               <table class="table"id="">
                 <thead>
                   <tr>
                     <th></th>
                     <th>ID</th>
                     <th>PLUS-QTY</th>
                     <th>ITEMS</th>
                   </tr>
                 </thead>
                 <tbody>
                   <?php
                     foreach (array_filter($_SESSION['add_qty']) as $key => $value) {
                       $query = mysqli_query($dbcon,"SELECT * FROM utensils where utensils_id = ".$value['utensils_id']);
                       foreach ($query as $key => $values) {
                       ?>
                       <tr>
                         <td><a href="?action=remove_add&ids=<?php echo $value["utensils_id"]; ?>"><i class="fa fa-times text-danger"></i></a></td>
                         <td class="bg bg-success"><?php echo $value['utensils_id']; ?></td>
                         <td class="bg bg-info"><?php echo $value['qty']; ?></td>
                         <td class="bg bg-success"><?php echo $values['utensils_name']; ?></td>
                       </tr>
                       <?php
                       }
                     }
                   ?>

                 </tbody>
               </table>
             <?php } ?>
               <?php if (isset($_SESSION['deduct_qty'])) { ?>
               <h4>Items to decrease quantity:</h4>
               <table class="table"id="">
                 <thead>
                   <tr>
                     <th></th>
                     <th>ID</th>
                     <th>MIN-QTY</th>
                     <th>ITEMS</th>
                     <th>REASON</th>
                   </tr>
                 </thead>
                 <tbody>
                   <?php
                     foreach (array_filter($_SESSION['deduct_qty']) as $key => $value) {
                       $query = mysqli_query($dbcon,"SELECT * FROM utensils where utensils_id = ".$value['utensils_id']);
                       foreach ($query as $key => $values) {
                       ?>
                       <tr>
                         <td><a href="?action=remove_deduct&ids=<?php echo $value["utensils_id"]; ?>"><i class="fa fa-times text-danger"></i></a></td>
                         <td class="bg bg-danger"><?php echo $value['utensils_id']; ?></td>
                         <td class="bg bg-info"><?php echo $value['qty']; ?></td>
                         <td class="bg bg-danger"><?php echo $values['utensils_name']; ?></td>
                         <td class="bg bg-danger"><?php echo $value['reason']; ?></td>
                       </tr>
                       <?php
                       }
                     }
                    ?>

                 </tbody>
               </table>
             <?php } ?>
                </div>
             </div>
            <?php
          }else {
            ?>
            <div class="card">
              <br><br><br><br><br><br>
              <center>
                <h4>(No items selected)</h4>
              </center>
              <br><br><br><br><br><br>
            </div>
            <?php
          } ?>

        </div>
      </div>
    </div>
  </div>
</div>
                        </div>
                     </div>
                  </div>
              </div>
              </div>
            </div>
         </div>
    </div>

    <!-- Mini Modal -->
    <div class="modal fade modal-primary" id="add_qty" data-backdrop="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                  <a href="#"class="close" data-dismiss="modal">&times;</a>
                </div>
                <div class="modal-body ">
                  <div class="content">
                   <div class="row">
                     <div class="col-md-12">
                         <span >Item ID : <input type="text"class="form-control"id="reqIDs" name="" disabled  style="text-align:center;"/> </span>
                         <br>
                     </div>
                     <div class="col-md-12">
                       <span> Additional qty :
                       <form class="" action="restocking.php" method="post">
                         <input type="hidden" name="utensils_id"id="reqIDsx"  value="">
                        <input type="number" class="form-control text-center"name="qty" value=""required>
                        <br>
                        <input type="submit"class="btn btn-info btn-block btn-fill" name="qty_to_add" value="Confirm">
                       </form>
                       </span>
                     </div>
                   </div>
                </div>
           </div>
            </div>
        </div>
    </div>
    <!--  End Modal -->
    <!-- Mini Modal -->
    <div class="modal fade modal-primary" id="deduct_qty" data-backdrop="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header justify-content-center">

                  <a href="#"class="close" data-dismiss="modal">&times;</a>
                </div>
                <div class="modal-body ">
                  <div class="content">
										<div class="row">
											<div class="col-md-12">
													<span >Item ID : <input type="text"class="form-control"id="reqIDs2" name="" disabled  style="text-align:center;"/> </span>
													<br>
											</div>
											<div class="col-md-12">

												<form class="" action="restocking.php" method="post">
													<span> Deduction qty :
													<input type="hidden" name="utensils_id"id="reqIDsx2"  value="">
												 <input type="number" class="form-control text-center"name="qty" value=""required>
												 </span>
												 <br>
												 <span> Reason :
												<textarea name="reason"class="form-control text-left" required>
	                      </textarea>
												 </span>
												 <br>
												 <input type="submit"class="btn btn-info btn-block btn-fill" name="qty_to_deduct" value="Confirm">
												</form>

											</div>
										</div>
                </div>
           </div>
            </div>
        </div>
    </div>
    <!--  End Modal -->
<?php include('dataTables2.php') ?>
<script type="text/javascript">
$('#update_qty').DataTable( {
 "pageLength": 50,
 "scrollX": true
 } );
 $(".click_pin").click(function () {
     var ids = $(this).attr('data-id');
     $("#reqIDs").val( ids );
     $("#reqIDsx").val( ids );
     $('#add_qty').modal('show');

 });
 $(".click_pins").click(function () {
		 var ids2 = $(this).attr('data-id');
		 $("#reqIDs2").val( ids2 );
		 $("#reqIDsx2").val( ids2 );
		 $('#deduct_qty').modal('show');

 });
</script>
<?php include('footer.php') ?>
