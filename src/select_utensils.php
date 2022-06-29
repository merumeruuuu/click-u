<?php include('header.php');
include 'requests.php';
// session_start();
$requests = new Utensils;
?>
<br><br>
<div class="content" id="main">
    <div class="container-fluid">
        <div class="row">
          <div class="col-md-6">
              <div class="card">
                  <div class="header">
                      <h4 class="title">My Request</h4>
                      <!-- <p class="category"></p> -->
                  </div>
                  <div class="content">
                        <!-- <h2>My Utensil Requests</h2> -->
                        <table class="table table-hover "id="RequestTable"style='width:100%;' border="0" alt="Null">
                          <thead>
                            <!-- <th>ID</th> -->
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Category</th>
                            <th>Action</th>

                          </thead>
                          <tbody >
                            <?php

                      $error = '';
                      if($requests->total_items() > 0){
                        //get cart items from session
                        $requestItems = $requests->contents();
                        foreach($requestItems as $item){
                          $checkStor = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = '".$item['id']."' and storage_id = '".$item['storageID']."' and storage_qty <'".$item['qty']."'");
                        if (mysqli_num_rows($checkStor)>0) {
                          $error = 1;
                        }
                          $checkStorageStock = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_id = '".$item['storageID']."' and utensils_id = '".$item['id']."'");
                          foreach ($checkStorageStock as $key => $value) {
                            if ($item['qty'] > $value['storage_qty']) {
                              ?>
                              <tr >
                                <!-- <td><?php echo $item["id"]; ?></td> -->
                                <td class="text-danger"><?php echo $item["name"]; ?></td>
                                <?php if ($value['storage_qty']<=0) {
                                  ?>
                                  <td ><input type="text" class="form-control text-center text-danger"min="1" id="numInput"value=" (unavailable)" placeholder="(unavailable)" disabled></td>
                                  <?php
                                }else {
                                  ?>
                                  <td ><input type="number" class="form-control text-center text-danger"min="1" id="numInput"value="<?php echo $item["qty"]; ?>" onchange="updateRequestItem(this, '<?php echo $item["rowid"]; ?>')"></td>
                                  <?php
                                } ?>

                                <td class="text-danger"><?php echo $item["category"]; ?></td>
                                <td>
                              <a href="utensil_request_action.php?action=removeRequestItem&id=<?php echo $item["rowid"]; ?>"class="btn btn-danger"> <i class="fa fa-trash"> </i></a>
                             </td>
                              </tr>
                              <?php
                            }else {

                      ?>
                            <tr >
                              <!-- <td><?php echo $item["id"]; ?></td> -->
                              <td><?php echo $item["name"]; ?></td>
                              <td><input type="number" class="form-control text-center"min="1" id="numInput" value="<?php echo $item["qty"]; ?>" onchange="updateRequestItem(this, '<?php echo $item["rowid"]; ?>')"></td>
                              <td><?php echo $item["category"]; ?></td>
                              <td>
                            <a href="utensil_request_action.php?action=removeRequestItem&id=<?php echo $item["rowid"]; ?>"> <i class="fa fa-trash text-danger"> </i></a>
                           </td>
                            </tr>
                          <?php }
                            }
                        } }else{ ?>
                      <tr><td colspan="5"><p>Your request is empty.....</p></td></tr>
                      <?php } ?>
                          </tbody>
                        </table>
                          <hr>
                            <?php if(!empty($requests->total_items())){
                                //get cart items from session
                                // $requestItems = $requests->contents();
                                // foreach($requestItems as $item){
                                   ?>


                                     <!-- <div class="btn-group btn-group-justified">
                                       <div class="btn-group">
                                       <a href="utensil_request_action.php?action=clearItems&id=clear" class="btn btn-lg btn-deafault  btn-fill">Clear Requests</a>
                                       </div>
                                       <div class="btn-group">
                                        <a href="#"class=" btn btn-lg btn-info  btn-fill" data-toggle="modal" data-target="#modal-ajax">Submit Request</a>
                                       </div>
                                     </div> -->
                                     <div class="stats">
                                     <div class="row">
                                       <div class="col-md-12">
                                         <div class="col-md-6">
                                            <br>
                                            <?php if (isset($_SESSION['group_leader'])||isset($_SESSION['group_name'])) {
                                              ?><span>
                                              <a href="creategroup.php"class=" btn btn-sm btn-warning  btn-fill" ><i class="fa fa-chevron-left"></i> Back to form</a>
                                              </span><?php
                                            }else {
                                              ?><span>
                                              <a href="userSelectStorage.php"class=" btn btn-sm btn-warning  btn-fill" ><i class="fa fa-chevron-left"></i> Back to form</a>
                                              </span><?php
                                            } ?>

                                         </div>
                                           <!-- <div class="col-md-3 ">
                                           <span>
                                          <a href="utensil_request_action.php?action=clearItems&id=clear" class="btn btn-sm btn-deafault  btn-fill">Clear Requests</a>
                                          <br>
                                          </span>
                                           </div> -->
                                           <div class="col-md-6">
                                             <br>
                                               <?php if (empty($error)) {
                                                 ?>
                                                 <span class="pull-right">
                                                <a href="#"class=" btn btn-sm btn-info  btn-fill" data-toggle="modal" data-target="#modal-ajax">Submit Request <i class="fa fa-chevron-right"></i> </a>
                                                </span>
                                                 <?php
                                               } ?>
                                        </div>
                                           </div>
                                       </div>
                               </div>
                               <?php
                                   }  ?>



                  </div>
              </div>
            </div>
            <div class="col-md-6">
                <div class="card">

                    <div class="header">
                        <h4 class="title">Utensil Table </h4>
                        <!-- <p class="category"></p> -->
                    </div>
<div class="content" >
   <?php
     $id = $_SESSION['group_storage'];
     $query1 = "SELECT
                   a.storage_id,a.storage_name,a.initials,
                   b.storage_id as storageID,b.utensils_id,b.storage_qty,
                   c.utensils_id,c.utensils_name,c.utensils_cat_id,
                   d.utensils_cat_id,d.category

                   FROM storage a
                   LEFT JOIN storage_stocks b On a.storage_id = b.storage_id
                   LEFT JOIN utensils c on b.utensils_id = c.utensils_id
                   LEFT JOIN utensils_category d on c.utensils_cat_id = d.utensils_cat_id

                  where a.storage_id ='$id'AND b.storage_qty >0 ";

     $utensil = mysqli_query($dbcon,$query1);
     $storage_name = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = '$id'");
     $row=mysqli_fetch_array($storage_name);
   ?>
    <h5><?php echo $row['storage_name'] ?> </h5>
    <?php include('errors.php'); ?>

    <div class="app-table-responsive">


    <table class="table table-bordered table-hover" id="utensil_table"style="overflow: :auto;">
      <thead>
        <tr>
          <!-- <th>ID</th> -->
          <th>Action</th>
          <th>Item Name</th>
          <th>Available</th>
          <th>Category</th>

        </tr>
      </thead>
      <tbody>
        <?php

        while ($rows=mysqli_fetch_array($utensil)) {
        ?>
        <tr>
          <td>
            <a href="utensil_request_action.php?action=addToRequests&id=<?php echo $rows['utensils_id']; ?>&s_id=<?php echo $rows['storage_id']; ?>" class="btn btn-sm btn-success btn-fill" ><i class="fa fa-plus"></i></a>
          </td>
          <!-- <td><?php echo $rows['utensils_id'] ?></td> -->
          <td><?php echo $rows['utensils_name'] ?></td>
          <td><?php echo $rows['storage_qty'] ?></td>
          <td><?php echo $rows['category'] ?></td>


        </tr>
      <?php  }?>
      </tbody>
    </table>
    </div>
  </div>
</div>
</div>



</div>

</div>
</div>






<!-- The Modal -->
<div class="modal fade" id="modal-ajax" data-backdrop="false">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<!-- Modal Header -->
<div class="modal-header">
<h5 class="modal-title">Borrow Details<button type="button" class="close" data-dismiss="modal">&times;</button></h5>
</div>
<div class="card">
<div class="content">
 <div class="row">


            <?php
              //by group borrowing
             if (!empty($_SESSION['group_members'])&& !empty($_SESSION['group_instructor'])) { ?>
               <div class="col-md-12">
                 <span>Purpose :  </span> <span><?php echo $_SESSION['group_purpose']; ?></span>
               </div>
              <div class="col-md-3">
                <div class=""style="color:gray;">
                <h5> Borrowers </h5>
               </div>
            <?php
            if(!empty($_SESSION["group_members"]))
            {    $filtered = array_filter($_SESSION["group_members"]);
                 foreach($filtered as $keys => $values)
                 {
            ?>
            <tbody>
              <tr>
                <td> <?php echo $values["member_school_ID"];  ?> - </td>
                <td> <?php echo $values["member_lname"]; ?> </td>
              </tr>
                <br>
            </tbody>
          <?php }

        }
        ?>
        </div>
        <div class="col-md-3">
          <?php
          $instructor = $_SESSION['group_instructor'];?>
           <div class=""style="color:gray;">
           <h5> Instructor </h5>
          </div>
          <?php echo $instructor ?>
        </div>
        <div class="col-md-3">

          <div class=""style="color:gray;">
          <h5> Group Name </h5>
         </div>
          <?php $groupName = $_SESSION['group_name']; ?>
            <?php echo $groupName;?>
         </div>
        <div class="col-md-3">
            <div class=""style="color:gray;">
            <h5>Items From </h5>
           </div>
          <?php
              $storage = $_SESSION['group_storage'];
              $query  = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $storage");
              $rows = mysqli_fetch_array($query);
           ?>
            <?php echo $rows['storage_name'] ?> (<?php echo $rows['initials'] ?>)
        </div>
         <?php
         //Individual borrower
      }if (empty($_SESSION['group_members'])&& !empty($_SESSION['group_instructor'])&&empty($_SESSION['group_name'])) {
          ?>
          <div class="col-md-12">
            <span>Purpose :  </span> <span><?php echo $_SESSION['group_purpose']; ?></span>
          </div>
          <div class="col-md-3">
              <div class=""style="color:gray;">
              <h5> Borrower </h5>
             </div>
          <?php $_SESSION['user']['user_id'];
                $id = $_SESSION['user']['user_id'];
                $query  = mysqli_query($dbcon,"SELECT * FROM users where user_id = $id");
                $rows = mysqli_fetch_array($query);
                ?>
                <?php echo $rows['lname'] ?> , <?php echo $rows['fname'] ?>
                <?php
            ?>
          </div>
          <div class="col-md-3">
            <?php
            $instructor = $_SESSION['group_instructor'];?>
             <div class=""style="color:gray;">
             <h5> Instructor </h5>
            </div>
            <?php echo $instructor; ?>
          </div>

          <div class="col-md-3">
              <div class=""style="color:gray;">
              <h5> Items From </h5>
             </div>
            <?php
                $storage = $_SESSION['group_storage'];
                $query  = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $storage");
                $rows = mysqli_fetch_array($query);
             ?>
              <?php echo $rows['storage_name'] ?> (<?php echo $rows['initials'] ?>)
          </div>

          <?php
                //teacher borrower
        }if (empty($_SESSION['group_members'])&& empty($_SESSION['group_instructor'])&&empty($_SESSION['group_name'])) {
          ?>
          <div class="col-md-12">
            <span>Purpose :  </span> <span><?php echo $_SESSION['group_purpose']; ?></span>
          </div>
          <div class="col-md-3">
              <div class=""style="color:gray;">
              <h5> Borrower</h5>
             </div>
          <?php $_SESSION['user']['user_id'];
                $id = $_SESSION['user']['user_id'];
                $query  = mysqli_query($dbcon,"SELECT * FROM users where user_id = $id");
                $rows = mysqli_fetch_array($query);
                ?>
                <?php echo $rows['lname'] ?> , <?php echo $rows['fname'] ?>
          </div>

          <div class="col-md-3">
              <div class=""style="color:gray;">
              <h5> Items From </h5>
             </div>
           <?php
               $storage = $_SESSION['group_storage'];
               $query  = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = $storage");
               $rows = mysqli_fetch_array($query);
            ?>
           <?php echo $rows['storage_name'] ?> (<?php echo $rows['initials'] ?>)
          </div>

          <?php
        }
          ?>
        </div>
        </div>
      </div>



<!-- Modal body -->
<div class="modal-body">
<div>
  <div class=""style="color:gray;">
  <h5> Items Request </h5>
 </div>
          <table class="table table-bordered"id="RequestTable">
              <thead >
               <tr >
                    <th>Item Name</th>
                    <th >Category</th>
                    <th >Quantity</th>
               </tr>
             </thead>
         <tbody>
<?php
//Request items session
$error = '';
if($requests->total_items() > 0){
 $requestsItems = $requests->contents();
 foreach($requestsItems as $item){
   $checkStor = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = '".$item['id']."' and storage_id = '".$item['storageID']."' and storage_qty <'".$item['qty']."'");
 if (mysqli_num_rows($checkStor)>0) {
   $error = 1;
 }
?>
<tr>
 <td><?php echo $item["name"]; ?></td>
 <td><?php echo $item["category"]; ?></td>
 <td><?php echo $item["qty"]; ?></td>
</tr>
<?php }} ?>
</tbody>
</table>
<div>
</div>
</div>
<!-- Modal footer -->
<div class="modal-footer">
<?php if(empty($error)){ ?>
<!-- <td ><a href="checkout.php" class="btn btn-primary btn-lg btn-info">Submit</a></td> -->
<td><a href="utensil_request_action.php?action=submitRequest" class="btn btn-info btn-fill btn-sm">Confirm <i class="fa fa-send"></i></a></td>
<?php } ?> </div>
</div>
</div>
</div>

</div>
<?php include('dataTables.php') ?>
<script type="text/javascript">

var trapNumber = document.getElementById("numInput")

trapNumber.addEventListener("keydown", function(e) {
  // prevent: "e", "=", ",", "-", "."
  if ([69, 187, 188, 189, 190].includes(e.keyCode)) {
    e.preventDefault();
  }
})

          $('#utensil_table').DataTable( {
           "pageLength": 50,
           "scrollX": true
           } );
          $('#RequestTable').DataTable( {
           "pageLength": 50,
           "scrollX": true
           } );

          function updateRequestItem(obj,id){
  $.get("utensil_request_action.php", {action:"updateRequestItem", id:id, qty:obj.value}, function(data){
    //location.reload();

      if(data == 'ok'){
       alert('Confirm Update!');
        location.reload();
        setInterval(refreshButton, 20000);

      } else{
        // alert(' Update!');
        // location.reload();
        location.reload();
        setInterval(refreshButton, 20000);

       }
  });
}


        </script>

<?php include('footer.php') ?>
