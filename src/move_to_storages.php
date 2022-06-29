<?php include('header.php'); ?>
            <br><br>
            <div class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="header">
                                            <h4 class="title">Move the new item/s to prefered storage</h4>

                                            <div class="category">
                                             <p>(Set the quantity you want to move and select a storage where to put the item/s.)</p>
                                            </div>
                                            <hr>
                                        </div>
                                           <div class="content">
                                             <div class="card">
                                               <div class="content">

                                             <?php

                                             if (isset($_GET['move'])) {
                                             $_SESSION['id'] = $_GET['move'];
                                               }
                                             $queryString = "SELECT
                                             a.utensils_id , a.utensils_name, a.umsr,
                                             a.model, a.serial_no, a.date_purchased,a.utensils_cat_id,a.cost,
                                             b.utensils_cat_id , b.category,
                                             c.date_received,c.new_arvl_qty,c.utensils_id,c.received_by,c.checked_by,c.approved_by,c.date_approved,
                                             d.id,d.umsr_name

                                             FROM utensils a
                                             LEFT JOIN utensils_category b ON a.utensils_cat_id = b.utensils_cat_id
                                             LEFT JOIN new_arrival_utensils c ON a.utensils_id = c.utensils_id
                                             LEFT JOIN umsr d on a.umsr = d.id

                                             where a.utensils_id = $id or a.utensils_id = '".$_SESSION['id']."'";
                                             $query = mysqli_query($dbcon,$queryString);
                                             $show = mysqli_fetch_array($query);
                                             $storage = mysqli_query($dbcon, "SELECT * FROM storage");

                                             ?>

                                             <form method="post"name="myForm"onsubmit="return validateForm()" action="move_to_storages.php">
                                             <div class="row">
                                               <div class="col-md-12">
                                               <?php include('success.php'); ?>
                                               <?php include('errors.php'); ?>
                                               </div>
                                             <div class="col-md-12">
                                             <label for="inputAddress">Name and description of the item</label>
                                             <input type="text" class="form-control"name="item_name_desc" value="<?php echo $show['utensils_name'] ?>" readonly>
                                             </div>

                                             <div class="col-md-6">
                                             <label for="inputAddress">Category</label>
                                             <input type="text" class="form-control" name="category" value="<?php echo $show['category'] ?>"readonly>
                                             </div>

                                             <div class="col-md-3">
                                             <label for="">Quantity</label>
                                             <input type="hidden"id="origNum" name="new_arrival" value="<?php echo $show['new_arvl_qty'] ?>">
                                             <input type="number" id="numInput"class="form-control"min="1"name="qty"style="border-color: blue;" placeholder="<?php echo $show['new_arvl_qty'] ?>"required>
                                             </div>
                                             <div class="col-md-3">
                                             <label for="inputAddress">UMSR</label>
                                             <input type="text" class="form-control" name="umsr" value="<?php echo $show['umsr_name'] ?>"readonly>
                                             </div>
                                             <div class="col-md-6">
                                             <label for="inputAddress">Model</label>
                                             <input type="text" class="form-control" name="model" value="<?php echo $show['model'] ?>"readonly>
                                             </div>
                                             <div class="col-md-6">
                                             <label for="inputAddress">Serial Number</label>
                                             <input type="text" class="form-control"name="serial" value="<?php echo $show['serial_no'] ?>"readonly>
                                             </div>


                                             <div class="col-md-2">
                                             <label for="inputAddress">Date Received</label>
                                             <input type="text" class="form-control"name="date_received" value="<?php echo $show['date_received'] ?>"readonly>
                                             </div>
                                             <div class="col-md-2">
                                             <label for="inputAddress">Date Purchased</label>
                                             <input type="text" class="form-control"name="date_purchased" value="<?php echo $show['date_purchased'] ?>"readonly>
                                             </div>
                                             <div class="col-md-2">
                                             <label for="inputAddress">Unit Cost</label>
                                             <input type="hidden" name="utensils_id" value="<?php echo $show['utensils_id'] ?>">
                                             <input type="text" class="form-control"name="cost" value="₱ <?php echo number_format($show['cost'],2) ?>"readonly>
                                             </div>


                                             <div class="col-md-6">
                                             <label for="inputState">Storage</label>
                                             <select class="form-control"name="storage"style="border-color: blue;">
                                             <?php if($storage) {
                                             while($rows = mysqli_fetch_array($storage)) {
                                             ?>
                                             <option value="<?php echo $rows['storage_id']; ?>"><?php echo $rows['storage_name']; ?></option>
                                             <?php
                                             }
                                             } ?>
                                             </select>
                                             </div>

                                             <div class="col-md-4">
                                             <br>

                                             <input class="btn btn-info btn-fill" id="btn" type="submit" name="move_to" value="Confirm Move">
                                             <a href="newarrival.php" class="btn btn-danger btn-fill">Cancel</a>
                                             </div>
                                             <br>
                                             <!-- <div class="form-group">
                                             <div class="form-check">
                                             <input class="form-check-input" type="hidden" id="gridCheck">
                                             <label class="form-check-label" for="gridCheck">

                                             </label>
                                             </div>
                                             </div> -->
                                             </div>
                                             </form>

                                             </div>
                                             </div>
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

function validateForm() {
  var x = document.forms["myForm"]["new_arrival"].value;
  var y = document.forms["myForm"]["qty"].value;
  if (x  y) {
    alert("Invalid quantity!");
    return false;
  }


}

</script>
<?php include('footer.php') ?>
