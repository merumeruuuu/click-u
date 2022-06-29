
<?php include('header.php'); ?>
<?php //Add utensils
 $success = array();
 $errors = array();
if (isset($_POST['Save'])) {
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
  $temporary = "MERU";
  $status = "0";
  $_SESSION['user']['user_id'];
  $id = $_SESSION['user']['user_id'];
  $currentDate =  date('Y-m-d H:i:s');
  if ($date_received <= $currentDate && $date_purchased <= $currentDate && $date_purchased <= $date_received) {
    array_push($success,"New item added!");
  }else {
    array_push($errors,"Invalid date!");
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
<br><br>
<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Add New Items</h4>
                                <hr>

                            </div>


                            <div class="content">
            <!-- <?php include('errors.php'); ?> -->
            <?php
               $category = mysqli_query($dbcon, "SELECT * FROM utensils_category");
               $storage = mysqli_query($dbcon, "SELECT * FROM storage");
               $umsr = mysqli_query($dbcon, "SELECT * FROM umsr");

             ?>
  <form method="post" action="addutensils.php"name="actorInsert"oninput="return ValidateActInsert">


      <div class="col-md-12">
        <?php include('success.php'); ?>
        <?php include('errors.php'); ?>
      </div>
      <div class="col-md-12">
        <label for="title">Complete name and description of the item</label>
        <input type="text" class="form-control"name="item_name_desc" placeholder="" required>
      </div>
    <div class="col-md-6">
      <label for="inputState">Category</label>
      <select class="form-control"name="category">
        <?php if($category) {
    while($row = mysqli_fetch_array($category)) {
  ?>
  <option value="<?php echo $row['utensils_cat_id']; ?>"><?php echo $row['category']; ?></option>
  <?php
    }
    } ?>
      </select>
    </div>

    <div class="col-md-3">
      <label for="inputAddress">Quantity</label>
      <input type="number"id="numInput" class="form-control" min="1"name="qty" placeholder=""required>
    </div>
    <div class="col-md-3">
      <label for="inputAddress">UMSR</label>
      <select class="form-control"name="umsr">
        <?php if($umsr) {
    while($row = mysqli_fetch_array($umsr)) {
  ?>
  <option value="<?php echo $row['id']; ?>"><?php echo $row['umsr_name']; ?></option>
  <?php
    }
    } ?>
      </select>
    </div>
    <div class="col-md-6">
      <label for="inputAddress">Model</label>
      <input type="text" id="textbox" class="form-control" name="model" placeholder="">
    </div>
    <div class="col-md-6">
      <label for="inputAddress">Serial Number</label>
      <input type="text" id="textbox1"class="form-control"name="serial" placeholder="">
    </div>


    <div class="col-md-2">
      <label for="inputAddress">Date Purchased</label>
      <input type="date" data-date-format='yy-mm-dd'id="" class="form-control"name="date_purchased" placeholder="yy/mm/dd"required>
    </div>
    <div class="col-md-2">
      <label for="inputAddress">Date Received</label>
      <input type="date"data-date-format='yy-mm-dd' id=""class="form-control"name="date_received" placeholder="yy/mm/dd"required>
    </div>

    <div class="col-md-2">
      <label for="inputAddress">Unit Cost</label>
      <input type="number" id="numInput"class="form-control"min="1"name="cost" placeholder="₱ 00.00"required>
    </div>

  <div class="row">
    <div class="col-md-4">
      <br>
      <input class="btn btn-info btn-fill" type="submit" name="Save" value="Add">
      <a href="kitchen_staff_home.php" class="btn btn-danger btn-fill">Cancel</a>
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
var trapNumber = document.getElementById("numInput")

trapNumber.addEventListener("keydown", function(e) {
  // prevent: "e", "=", ",", "-", "."
  if ([69, 187, 188, 189, 190].includes(e.keyCode)) {
    e.preventDefault();
  }
})

///specialChars
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

      $("#textbox1").keypress(function (e) {
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

//TRAP DATE
$(function() {

var date = new Date();

var currentYear = date.getFullYear();
var currentMonth = date.getMonth();
var currentDate = date.getDate();

$('#datepicker').datepicker({


maxDate: new Date(currentYear, currentMonth, currentDate),
dateFormat: 'dd-mm-yy',
   altField: '#datepicker',
   altFormat: 'yy-mm-dd'
});

});
$(function() {
var date = new Date();
var currentMonth = date.getMonth();
var currentDate = date.getDate();
var currentYear = date.getFullYear();
$('#datepicker2').datepicker({

maxDate: new Date(currentYear, currentMonth, currentDate),
dateFormat: 'dd-mm-yy',
   altField: '#datepicker2',
   altFormat: 'yy-mm-dd'
});

});

</script>
<?php include('footer.php') ?>
