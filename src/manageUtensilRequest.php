<?php include('header.php'); ?>
<style media="screen">
.title{
	color: #555;
	border-bottom: 2px solid orange;
}
.dataTables_scrollBody{
    height: auto;
    overflow: scroll !important;
}
</style>
<?php $reqID = $_SESSION['manage_id'];

 if(isset($_POST["add_discrepancy"]))
 {
     $id = mysqli_real_escape_string($dbcon,$_POST['id']);
     $damaged_qty = mysqli_real_escape_string($dbcon,$_POST['damaged']);
     $lost_qty = mysqli_real_escape_string($dbcon,$_POST['lost']);
		 $note = mysqli_real_escape_string($dbcon,$_POST['note']);


		 if (empty($damaged_qty)) {
			$damaged_qty = 0;
		 }
		 if (empty($lost_qty)) {
			$lost_qty = 0;
		 }
     $compare = $damaged_qty + $lost_qty;




		if ($_SESSION['item']!=0) {
			if (!ctype_space($lost_qty)||!ctype_space($damaged_qty)) {

				$query1 = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_slip_id = $reqID and utensils_id = '".$_SESSION['item']."'");
	 		 $check = mysqli_fetch_array($query1);
        $req_qty = $check['qty'];

       if ($req_qty > $compare || $req_qty == $compare) {
     if ($compare >0) {

  			if(isset($_SESSION["discrepancy_tray"]))
  			{
					  // $_SESSION['item'] = 0;
  					 $item_array_id = array_column($_SESSION["discrepancy_tray"], "item_id");
  					 if(!in_array($_POST['id'], $item_array_id))
  					 {
  								$count = count($_SESSION["discrepancy_tray"]);
  								$item_array = array(
  										 'item_id'      =>     $_POST['id'],
                       'lost_qty'     =>     $lost_qty,
                       'damaged_qty'  =>     $damaged_qty,
											 'note'         =>     $note

  								);
  								$_SESSION["discrepancy_tray"][$count] = $item_array;

  					 }
            else
  					 {
  						 // array_push($errors,"Item Already Added!");
  						 // unset($_SESSION["discrepancy_tray"]);
  					 }
  			}
  			else
  			{

  					 $item_array = array(
							 'item_id'    =>     $_POST['id'],
							 'lost_qty' =>     $_POST["lost"],
							 'damaged_qty'  =>     $_POST["damaged"],
							 'note' =>     $_POST["note"]
  					 );
  					 $_SESSION["discrepancy_tray"][0] = $item_array;
  			}
			}else {
				array_push($errors,"Invalid quantity!");
			}
			}else {
				array_push($errors,"Invalid quantity!");
			}
			}else {
					array_push($errors,"Please input quantity!");
			}
			}else {
				array_push($errors,"Please select an item!");
			}


			////////////

 }
 if(isset($_GET["action"]))
 {
		 if($_GET["action"] == "delete")
		 {
					foreach(array_filter($_SESSION["discrepancy_tray"]) as $keys => $values)
					{
							 if($values["item_id"] == $_GET["ids"])
							 {
										// unset($_SESSION["item_tray"][$keys]);
										 $_SESSION["discrepancy_tray"][$keys] = Null;
										// echo '<script>alert("Item cancelled!")</script>';
										// echo '<script>window.location="modifyUserRequests.php"</script>';

							 }
					}
		 }
 } ?>
 <?php if (isset($_GET['clear'])) {
    unset($_SESSION['discrepancy_tray']);
 } ?>
<br><br>
<div class="content">
  <div class="container-fluid">
		<div class="card">
      <div class="content" >
<?php

		$queryString = "SELECT
			a.bsd_id,a.borrower_slip_id,a.utensils_id,a.qty,a.storage_id,
			b.utensils_id as itemID,b.utensils_name,b.utensils_cat_id,
			c.utensils_cat_id,c.category

			from borrower_slip_details a
			left JOIN utensils b on a.utensils_id = b.utensils_id
			left join utensils_category c on b.utensils_cat_id = c.utensils_cat_id

			where a.borrower_slip_id = $reqID";
			$itemQuery = mysqli_query($dbcon,$queryString);

 ?>
<div id="section1" class="tab-pane fade in active">
	<div class="card">
		<div class="content"style="background-color:#e9e7e0;">
			<div class="row">
				<div class="col-md-2">
				 <h5 class=" ">REQUEST # <strong style="color:#07bfea"> <?php echo $reqID; ?></strong></h5>
				 <br>
				</div>
				<div class="col-md-8">
          <?php include('errors.php'); ?>

				</div>

				</div>
				<div class="row">
				<?php
         $item =  $_SESSION['item'];
				$query = "SELECT
					a.borrower_slip_id,a.utensils_id,a.qty,
					b.utensils_id,b.utensils_name,b.utensils_cat_id,
					c.utensils_cat_id,c.category

					FROM borrower_slip_details a
					LEFT JOIN utensils b on a.utensils_id = b.utensils_id
					left join utensils_category c on b.utensils_cat_id = c.utensils_cat_id

					where a.borrower_slip_id = $reqID and b.utensils_id = $item";
         $result = mysqli_query($dbcon,$query);
				$row = mysqli_fetch_array($result); ?>
				<div class="col-md-2">
					<span for="">Item ID :</span>
					<input type="text"class="form-control" name="" value="<?php if ($item==0) {echo '';}else {echo $row['utensils_id'];} ?>"disabled>
				</div>
				<div class="col-md-5">
					<span for="">Item name w/ description :</span>
					<input type="text"class="form-control" name="" value="<?php if ($item==0) {echo '';}else {echo $row['utensils_name'];}?>"disabled>
				</div>
				<div class="col-md-4">
					<span for="">Category :</span>
					<input type="text"class="form-control" name="" value="<?php if ($item==0) {echo '';}else {echo $row['category'];}?>"disabled>
				</div>
				<div class="col-md-1">
					<span for="">Quantity :</span>
					<input type="text"class="form-control" name="" value="<?php if ($item==0) {echo '';}else {echo $row['qty'];}?>"disabled>
				</div>
				</div>
				<br>
        <!-- ?action=add&id=<?php echo $row["utensils_id"]; ?> -->
				<form class="" action="manageUtensilRequest.php" method="post">
					<div class="row">
						<input type="hidden" name="id" value="<?php echo $row['utensils_id']; ?>">
						<div class="col-md-2">
		 					<span for="">Lost items :</span>
		 				<input type="number"class="form-control" name="lost"placeholder="Enter qty">
		 				</div>
						<div class="col-md-2">
		 					<span for="">Damaged items :</span>
		 				<input type="number"class="form-control" name="damaged"placeholder="Enter qty">
		 				</div>
						<div class="col-md-5">
							<span for="">Note :</span>
						<input type="text"class="form-control" name="note" value=""placeholder="(Optional)">
						</div>
						<div class="col-md-1">
							<br>
						<button type="submit"class="form-control btn btn-fill btn-info" name="add_discrepancy" >Save</button>
						</div>
						<div class="col-md-1">
							<br>
							<?php
							if (isset($_SESSION['discrepancy_tray'])) {
						      	$new_items = $_SESSION['discrepancy_tray'];
										$count = count(array_filter($new_items));
								}
									 if (empty($_SESSION['discrepancy_tray'])||empty($count)) {
										?>
									<a href="#"class="btn btn-fill btn-default">view all</a>
										<?php
									}else {

										?>
										<a href="#"data-toggle="modal"data-target="#viewReport" class="btn btn-fill btn-default">view all
										<span class="label label-pill label-danger count blink" style="border-radius:8px;"><?php echo $count; ?></span></a>
										<?php
									}

								?>

						</div>
					</div>
				</form>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<a href="returnRequest.php"><i class="fa fa-chevron-left"></i> Back to list </a>
		</div>
		<div class="col-md-5">
			<div class="category">
      <span> <h5>Select an item bellow :</h5> </span>
			</div>

		</div>
	</div>
					<div class="row">
            <div class="col-md-12" >
					<div class="card">
                <div class="content"style="background-color:#e9e7e0;">
                      <!-- <strong><label for=""><i class="fa fa-table"></i> Requested Items :</label></strong> -->

               <table id="requestTable"class="table table-bordered table-sm table-hover" cellspacing="0"  width="100%">
                                <thead >
																		<!-- <col span="5" style="background-color:auto;">
																<col span="3"style="background-color:#e9e7e0;"> -->
                                         <tr >
                                            <th class="th-sm">Item ID</th>
                                            <th class="th-sm">Item Name</th>
                                            <th class="th-sm">Item Category</th>
                                            <th class="th-sm">Request Quantity</th>
																						<th class="th-sm">Action</th>

                                        </tr>
                                      </thead>
                                        <tbody>

                                <?php while ($items = mysqli_fetch_array($itemQuery)) {    ?>

                                  <tr>

                                    <td><?php echo  $items['itemID']?></td>
                                    <td><?php echo  $items['utensils_name']?></td>
                                   <td><?php echo  $items['category']?></td>
                                    <td> <strong><?php echo  $items['qty']?></strong> </td>
																		<td><a href="#"id="manage_item"class="link" data-artid="<?php echo $items['itemID']; ?>">select</a></td>
                                    </tr>

                               <?php } ?>

                                    </tbody>
                                </table>

										 </div>
           </div>
					 </div>
      </div>
  </div>
</div>
</div>
</div>
</div>


<!-- The Modal -->
<div class="modal fade" id="viewReport" data-backdrop="false">
 <div class="modal-dialog modal-lg">
   <div class="modal-content">

<!-- Modal Header -->
    <div class="modal-header">
   	<div class="category">
     <span>Report details :</span>
   	</div>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
<!-- Modal body -->
   <div class="modal-body">
      <table class="table table-hover"id="requestTable1">
      	<thead>
					<col span="4" style="background-color:auto;">
			<col span="2"style="background-color:#e9e7e0;">
      		<tr>
      			<th>Item ID</th>
						<th>Item Name</th>
						<th>Category</th>

						<th>Borrowed qty</th>
						<th>Lost qty</th>
						<th>Damaged qty</th>
						<th>Action</th>
      		</tr>
      	</thead>
				<tbody>
					<?php

         foreach (array_filter($_SESSION['discrepancy_tray']) as $key => $report) {

					 $query_item = "SELECT
					 a.borrower_slip_id,a.utensils_id,a.qty,
					 b.utensils_id,b.utensils_name,b.utensils_cat_id,
					 c.utensils_cat_id,c.category

					 from borrower_slip_details a
					 left join utensils b on a.utensils_id = b.utensils_id
					 left join utensils_category c on b.utensils_cat_id = c.utensils_cat_id

					 where a.utensils_id = '".$report['item_id']."'and a.borrower_slip_id = $reqID";
					 $display = mysqli_query($dbcon,$query_item);
					 foreach ($display as $key => $value) {
					 ?>
					<tr>
						<td><?php echo $report['item_id'] ?></td>
						<td><?php echo $value['utensils_name'] ?></td>
						<td><?php echo $value['category'] ?></td>
						<td><?php echo $value['qty'] ?></td>
						<td><?php echo $report['lost_qty'] ?></td>
						<td><?php echo $report['damaged_qty'] ?></td>
						<td><a href="manageUtensilRequest.php?action=delete&ids=<?php echo $report["item_id"]; ?>"<i class="fa fa-undo" ></i> undo</a></td>
					</tr>
					<?php
					 }
				} ?>
				</tbody>
      </table>
   </div>
<!-- Modal footer -->
	 <div class="modal-footer">
 <a href="server.php?add_report=<?php echo $reqID; ?>" class="btn btn-success btn-fill btn-sm"onclick="return confirm('Confirm submit!');">Submit report <i class="fa fa-send"></i></a>
 <a href="manageUtensilRequest.php?clear"class="btn btn-fill btn-sm btn-default">Clear</a>
	</div>
  </div>
 </div>
</div>


  <?php include('dataTables4.php') ?>
  <script type="text/javascript">
	$(document).ready(function() {
	    var table = $('#requestTable').DataTable( {
	        scrollY: 300,
	        paging: false
	    } );
	} );

		$('#requestTable').DataTable();

	$(function(){
		$('.link').click(function(){

            var elem = $(this);
						$.ajax({
								type: "GET",
								url: 'ajaxrequest/manage_request_item.php',
								data: "id="+elem.attr('data-artid'),
								dataType: 'json',
								success: function (data) {
									setTimeout(function(){
			           	 location.reload();
								 }, 1);
								}
							});
							return false;
				});

				$('.undo').click(function(){
								var undo = $(this);
								$.ajax({
										type: "GET",
										url: 'ajaxrequest/manage_request_item.php',
										data: "undo_id="+undo.attr('data-ID'),
										dataType: 'json',
										success: function (data) {
											setTimeout(function(){
											 location.reload();
										 }, 1);
										}
									});
									return false;
						});
	});


	</script>
<?php include('footer.php') ?>
