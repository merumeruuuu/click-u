<?php include('header.php') ?>
<br><br>
<?php
$errors = array();
if (isset($_GET['manage_cat'])) {
  $_SESSION['category_control'] = 0;
  $_SESSION['category_control2'] = 0;
}
if (isset($_GET['category_controlx'])) {
  $_SESSION['category_control'] = 1;
}
if (isset($_GET['close_modal'])) {
  $_SESSION['category_control'] = 0;
  $_SESSION['category_control2'] = 0;
}
if (isset($_POST['add_category'])) {
  $cate = mysqli_real_escape_string($dbcon,$_POST['category']);
  $category = trim($cate);
  $trapString = mysqli_query($dbcon,"SELECT * FROM utensils_category where category = '$category' ");
  if (mysqli_num_rows($trapString)>=1) {
    array_push($errors,"Category already exists!");
  }else {
    if (empty($category)) {
      array_push($errors,"Please fill in fields!");
    }else {
      $insertCategory = mysqli_query($dbcon,"INSERT INTO utensils_category (category) VALUES ('$category')");
      array_push($success,"New category added!");
      $_SESSION['category_control'] = 0;
    }
}
}
if (isset($_GET['remove_category'])) {
  $catID = $_GET['remove_category'];
  $fetchCatID = mysqli_query($dbcon,"SELECT * FROM utensils where utensils_cat_id = $catID");
  if (mysqli_num_rows($fetchCatID)>=1) {
    array_push($errors,"Cannot delete category with utensils!");
  }else {
  $removeCat = mysqli_query($dbcon,"DELETE FROM utensils_category where utensils_cat_id = $catID");
  }
}
if (isset($_GET['update_cat'])) {
  $catID = $_GET['update_cat'];
  $fetchCatID = mysqli_query($dbcon,"SELECT * FROM utensils_category where utensils_cat_id = $catID");
  foreach ($fetchCatID as $key => $value);
  $_SESSION['update_categoryx'] = $value['category'];
  $_SESSION['category_id'] = $value['utensils_cat_id'];
  $_SESSION['category_control2'] = 1;
}
if (isset($_POST['update_category'])) {
  $str = $_POST['category'];
  $trapString = mysqli_query($dbcon,"SELECT * FROM utensils_category where category like '%$str%' ");
  if (mysqli_num_rows($trapString)>=1) {
    array_push($errors,"Category already exists!");
  }else {
    $updateCat = mysqli_query($dbcon,"UPDATE utensils_category set category = '".$_POST['category']."' where utensils_cat_id = '".$_SESSION['category_id']."'");
    array_push($success,"Category successfully updated!");
      $_SESSION['category_control2'] = 0;
  }

}?>
<div class="content" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <h4 class="title"> Utensil categories</h4>
                        <!-- <p class="category">(General Storage)</p> -->
                    </div>
                    <div class="content" style="overflow-x:auto;">
                      <div class="row">
                        <div class="col-md-6">
                          <?php include('success.php'); ?>
                          <?php
                          if ($_SESSION['category_control2'] == 1||$_SESSION['category_control'] == 1) {
                            // code...
                          }else {
                              include('errors.php');
                          }
                         ?>
                        </div>
                        <div class="col-md-5">
                            <a href="?category_controlx" class="btn btn-warning btn-fill pull-right"><i class="fa fa-plus"></i> Add new category</a>
                        </div>
                      </div>
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Category</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $fetchCategory = "SELECT *
                                    FROM utensils_category
                                    order by utensils_cat_id  ";
                        $result = mysqli_query($dbcon,$fetchCategory);
                        foreach ($result as $key => $value) {
                          ?>
                          <tr>
                            <td><?php echo $value['category'] ?></td>
                            <td>
                              <span>
                            <a href="?update_cat=<?php echo $value['utensils_cat_id']; ?>"class=""><i class="fa fa-pencil text-info"></i></a>
                            </span>
                            <span>
                            <a href="?remove_category=<?php echo $value['utensils_cat_id']; ?>"class=""><i class="fa fa-trash text-danger"></i></a>
                            </span>
                          </td>
                          </tr>
                          <?php
                        } ?>

                      </tbody>
                    </table>
                   </div>
              </div>
              </div>
            </div>
          </div>
    </div>
    <!-- Mini Modal -->
<div class="modal <?php if ($_SESSION['category_control']==1) {
      echo "show";
    }if($_SESSION['category_control']==0) {
      echo "fade";
     } ?>  modal-primary" id="myModal1" data-backdrop="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                  <span >Add new category</span>
                  <a href="manage_categories.php?close_modal" class="close" data-dismiss="modal">&times;</a>
                </div>
                <form class="" action="manage_categories.php" method="post">
                <div class="modal-body ">
                  <div class="content">
                      <div class="pull-center">
                      <label for="">Enter new category :</label>
                      <input type="text"class="form-control" name="category" value=""required>
                      <br>
                    </div>
                </div>
           </div>
           <div class="row">
             <div class="col-md-6">
               <?php include 'errors.php'; ?>
             </div>
             <div class="col-md-6">
               <div class="modal-footer">
                   <button type="submit" name="add_category"class="btn btn-sm btn-info btn-fill">Add</button>
               </div>
             </div>
           </div>

                </form>
            </div>
        </div>
    </div>
    <!--  End Modal -->

            <!-- Mini Modal -->
  <div class="modal <?php if ($_SESSION['category_control2']==1) {
              echo "show";
            }if($_SESSION['category_control2']==0) {
              echo "fade";
             } ?>  modal-primary" id="myModal1" data-backdrop="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header justify-content-center">
                          <span ></span>
                          <a href="manage_categories.php?close_modal" class="close" data-dismiss="modal">&times;</a>
                        </div>
                        <form class="" action="manage_categories.php" method="post">
                        <div class="modal-body ">
                          <div class="content">
                              <div class="pull-center">
                              <label for="">Update category:</label>
                              <input type="text"class="form-control" name="category" value="<?php echo $_SESSION['update_categoryx'] ?>"required>
                              <br>
                            </div>
                        </div>
                   </div>
                   <div class="row">
                     <div class="col-md-6">
                       <?php   include('errors.php'); ?>
                     </div>
                     <div class="col-md-6">
                       <div class="modal-footer">
                           <button type="submit" name="update_category"class="btn btn-sm btn-info btn-fill">Update</button>
                       </div>
                     </div>
                   </div>

                        </form>
                    </div>
                </div>
            </div>
            <!--  End Modal -->
            <?php include('dataTables2.php') ?>
            <script type="text/javascript">
                      $('#UtensilTable').DataTable( {
                       "pageLength": 50
                       } );
                    </script>
<?php include('footer.php') ?>
