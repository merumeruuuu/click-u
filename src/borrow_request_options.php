<?php include('header.php'); ?>
<style media="screen">


.button {
  padding: 15px 25px;
  font-size: 24px;
  text-align: center;
  cursor: pointer;
  outline: none;
  color: #fff;
  background-color: #ba40c7;
  border: none;
  border-radius: 15px;
  box-shadow: 0 9px #999;
}

.button:hover {background-color: #a122af}

.button:active {
  background-color: #c81dda;
  box-shadow: 0 5px #666;
  transform: translateY(4px);
}
</style>

<br><br>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="header">
                        <h4 class="title bol">Borrow Requests : </h4>
                        <p class="category">(Select from laboratories bellow)</p>
                        <hr>
                    </div>

                    <div class="content">
                      <div class="content">
                        <div class="row">
                      <?php
                     $storage_display = mysqli_query($dbcon,"SELECT * FROM storage ")
                       ?>
                       <?php
                     while($row = mysqli_fetch_array($storage_display)) {
                       $stID = $row['storage_id'];
                       $pending="SELECT count(borrower_slip_id) as pending From borrower_slip where storage_id = $stID AND status <= 1";
                          $res=mysqli_query($dbcon,$pending);
                          $val=mysqli_fetch_assoc($res);

                      ?>


                <?php if ($val['pending']>=1){
                   ?>
                   <a href="server.php?action=selectStorageForStaff&id=<?php echo $row['storage_id']; ?>"  style="color:white;">
                   <div class="col-md-6">
                     <div class="button">
                <?php echo $row['storage_name']; ?> (<?php echo $row['initials']; ?>)
                <br>
              <span>Requests</span>
                   <span class="label label-pill label-danger count blink"style="border-radius:20px;"> <i class="fa fa-bell fa-ring"></i>  <?php echo $val['pending']; ?> </span>
                 </div><br>
               </div>
               </a>
                   <?php
                } else {

                  ?>
                 <a href="server.php?action=selectStorageForStaff&id=<?php echo $row['storage_id']; ?>"  style="color:white;">
                  <div class="col-md-6">
                    <div class="button">
                  <?php echo $row['storage_name']; ?> (<?php echo $row['initials']; ?>)
                  <br>

                  <span> 0 Requests </span>

                </div><br>
              </div>
              </a>
                  <?php
                }?>







      <?php } ?>
    </div>
  </div>
</div>
  </div>
    </div>
      </div>
        </div>
          </div>

<?php include('footer.php') ?>
