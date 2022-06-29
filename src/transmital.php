<?php include('header.php') ?>
<script type="text/javascript">
function session_storage_from(value) {
      $.ajax({
          type: "POST",
          url: 'ajaxrequest/sessionTransmital.php',
          data: 'storage_from_ses=' + value,
          dataType: 'json',
          success: function (data) {
            if (data==1) {
              // location.reload();
              location.href = 'transmital.php';
              setInterval( 1000);
            }
          }
      });
  }
  function session_storage_to(value) {
        $.ajax({
            type: "POST",
            url: 'ajaxrequest/sessionTransmital.php',
            data: 'storage_to_ses=' + value,
            dataType: 'json',
            success: function (data) {
              if (data==1) {
                // location.reload();
                location.href = 'transmital.php';
                setInterval( 1000);
              }
            }
        });
    }
// seconde tab
function session_storage_from2(value) {
      $.ajax({
          type: "POST",
          url: 'ajaxrequest/sessionTransmital.php',
          data: 'storage_from_ses2=' + value,
          dataType: 'json',
          success: function (data) {
            if (data==1) {
              // location.reload();
              location.href = 'transmital.php';
              setInterval( 1000);
            }
          }
      });
  }
  function session_storage_to2(value) {
        $.ajax({
            type: "POST",
            url: 'ajaxrequest/sessionTransmital.php',
            data: 'storage_to_ses2=' + value,
            dataType: 'json',
            success: function (data) {
              if (data==1) {
                // location.reload();
                location.href = 'transmital.php';
                setInterval( 1000);
              }
            }
        });
    }
</script>
<?php
if (isset($_GET['transmital'])) {
  $_SESSION['storage_from'] = 0;
  $_SESSION['tab_control'] = 0;
   unset($_SESSION['error_msg']);
  unset($_SESSION['error_msg1']);
}
if (isset($_GET['from'])) {
  $_SESSION['modal_control'] = 0;
}
if (isset($_GET['to'])) {
  $_SESSION['modal_control'] = 1;
}
if (isset($_GET['transfer_all'])) {
  if ($_SESSION['storage_from']==0||$_SESSION['storage_to']==0) {
    $_SESSION['error_msg'] = 'Please select storage/s!';
    $_SESSION['error_msg1'] = 1;
  }else {

    $checkStorageItems = mysqli_query($dbcon,"SELECT count(utensils_id)as utensils_id  FROM storage_stocks where  storage_id = ".$_SESSION['storage_from']);
    $checkStorageItemsZeros = mysqli_query($dbcon,"SELECT count(utensils_id)as utensils_id  FROM storage_stocks where original_stock = 0 and storage_id = ".$_SESSION['storage_from']);
    foreach ($checkStorageItems as $key => $value1) {
      foreach ($checkStorageItemsZeros as $key => $value2) {
        if ($value1['utensils_id']==$value2['utensils_id']) {
          $error = true;

        }else {
          $error = false;
        }
      }
    }
     if ($error == false) {
       $checkStorageTransactions = mysqli_query($dbcon,"SELECT *  FROM storage_stocks where original_stock > storage_qty and storage_id = ".$_SESSION['storage_from']);
    if (mysqli_num_rows($checkStorageTransactions)<=0) {
      //create reference
        $fetchAllItemsFromStorageTo = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_id = '".$_SESSION['storage_to']."'");
        foreach ($fetchAllItemsFromStorageTo as $key => $itemsToCompare) {
          //chek again the storage from
          $storageFromDiffItems = mysqli_query($dbcon,"SELECT * From storage_stocks where utensils_id = '".$itemsToCompare['utensils_id']."'and storage_id = '".$_SESSION['storage_from']."'");
              //now compare items if all same
            foreach ($storageFromDiffItems as $key => $updateNew) {
            $newStorQTY = $itemsToCompare['storage_qty'] + $updateNew['storage_qty'];
            $newOrgStck = $itemsToCompare['original_stock'] + $updateNew['original_stock'];
            $updateDifference = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = $newStorQTY, original_stock = $newOrgStck where utensils_id = '".$updateNew['utensils_id']."' and storage_id ='".$_SESSION['storage_to']."'");
            $removeDifference = mysqli_query($dbcon,"UPDATE storage_stocks set storage_qty = 0, original_stock = 0 where utensils_id = '".$updateNew['utensils_id']."' and storage_id ='".$_SESSION['storage_from']."'");
          }
        }
           unset($_SESSION['storage_to']);
           $_SESSION['storage_from'] = 0;
            unset($_SESSION['error_msg']);
           unset($_SESSION['error_msg1']);
           echo "<script>alert('Transfer success!');window.location.href='transmital.php';</script>";
          }else {
            $_SESSION['error_msg'] = 'Error! There are pending transactions in this Storage/Lab!';
             array_push($errors,"Error! There are pending transactions in this Storage/Lab!");
          }

        }else {
           $_SESSION['error_msg'] = '(NO ITEMS FOUND!)';
        }
      }

    }
?>
<?php
if(isset($_POST["add_new_item"]))
 {
     $storID = mysqli_real_escape_string($dbcon,$_POST['storage_id']);
     $qty = mysqli_real_escape_string($dbcon,$_POST['qty']);
     $uID = $_GET["id"];
     if ($qty < 0) {
       array_push($errors,"Invalid quantity!");
     }else {

     $storageQuery = mysqli_query($dbcon,"SELECT * FROM storage_stocks where storage_id = $storID and utensils_id = $uID");
     $checkqty = mysqli_fetch_array($storageQuery);
     if ($qty > $checkqty['storage_qty']) {
       array_push($errors,"Failed! invalid quantity!");
     }else {

  			if(isset($_SESSION["transfer_item_tray"]))
  			{
  					 $item_array_id = array_column($_SESSION["transfer_item_tray"], "utensils_id");
  					 if(!in_array($_GET["id"], $item_array_id))
  					 {
  								$count = count($_SESSION["transfer_item_tray"]);
  								$item_array = array(
                    'utensils_id'    =>     $_GET["id"],
                    'storage_id' =>     $_POST["storage_id"],
                    'utensils_name'  =>      $_POST["utensils_name"],
                    'category'   =>     $_POST["category"],
                    'model'   =>     $_POST["model"],
                    'serial_no'   =>     $_POST["serial_no"],
                    'qty'        =>     $_POST["qty"]

  								);
  								$_SESSION["transfer_item_tray"][$count] = $item_array;

  					 }
            else
  					 {
  						 array_push($errors,"Item Already Added!");
  					 }

  			}
  			else
  			{
  					 $item_array = array(
               'utensils_id'    =>     $_GET["id"],
               'storage_id' =>     $_POST["storage_id"],
               'utensils_name'  =>      $_POST["utensils_name"],
               'category'   =>     $_POST["category"],
               'model'   =>     $_POST["model"],
               'serial_no'   =>     $_POST["serial_no"],
               'qty'        =>     $_POST["qty"]
  					 );

  					 $_SESSION["transfer_item_tray"][0] = $item_array;


  			}

     }
     // code...
   }

 }
 if(isset($_GET["action"]))
 {
     if($_GET["action"] == "delete")
     {
          foreach(array_filter($_SESSION["transfer_item_tray"]) as $keys => $values)
          {
               if($values["utensils_id"] == $_GET["ids"])
               {
                    // unset($_SESSION["item_tray"][$keys]);
                     $_SESSION["transfer_item_tray"][$keys] = Null;
                    // echo '<script>alert("Item cancelled!")</script>';
                    // echo '<script>window.location="modifyUserRequests.php"</script>';

               }
          }
     }
 } ?>
 <?php
 if (isset($_GET['transfer_by_item'])) {
   if ($_SESSION['storage_to2']!=0) {
   foreach (array_filter($_SESSION['transfer_item_tray']) as $key => $checkqty) {
     $checkStorageTransactions = mysqli_query($dbcon,"SELECT *  FROM storage_stocks where original_stock < '".$checkqty['qty']."'and utensils_id = '".$checkqty['utensils_id']."' and storage_id = '".$_SESSION['storage_from2']."'");
       if (mysqli_num_rows($checkStorageTransactions)>0) {
         $qtyErr = true;
       }else {
         $qtyErr = false;
       }
   }
   if ($qtyErr == true) {
     array_push($errors,"Some items not available! Please check quantity!");
   }else {


       foreach (array_filter($_SESSION['transfer_item_tray']) as $key => $value) {
         $fetchStorageFrom = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = '".$value['utensils_id']."'and storage_id = '".$_SESSION['storage_from2']."'");
         foreach ($fetchStorageFrom as $key => $valueFrom) {
           $newOrgStockFrom = $valueFrom['original_stock'] - $value['qty'];
           $newCurStockFrom = $valueFrom['storage_qty'] - $value['qty'];
           $updateStocksFROM = mysqli_query($dbcon,"UPDATE storage_stocks set original_stock = $newOrgStockFrom,storage_qty = $newCurStockFrom  where utensils_id = '".$value['utensils_id']."'and storage_id = '".$_SESSION['storage_from2']."'");
         }
         }
         foreach (array_filter($_SESSION['transfer_item_tray']) as $key => $value) {
         $fetchStorageTo = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = '".$value['utensils_id']."'and storage_id = '".$_SESSION['storage_to2']."'");
         foreach ($fetchStorageTo as $key => $valueTo) {
           $newOrgStockTo = $valueTo['original_stock'] + $value['qty'];
           $newCurStockTo = $valueTo['storage_qty'] + $value['qty'];
           $updateStocksTo = mysqli_query($dbcon,"UPDATE storage_stocks set original_stock = $newOrgStockTo,storage_qty = $newCurStockTo  where utensils_id = '".$value['utensils_id']."'and storage_id =  '".$_SESSION['storage_to2']."'");
         }
       }

       unset($_SESSION['transfer_item_tray']);
        unset($_SESSION['storage_to2']);
        $_SESSION['storage_from2'] = 0;
         unset($_SESSION['error_msg2']);
        unset($_SESSION['error_msg12']);
        echo "<script>alert('Transfer success!');window.location.href='transmital.php';</script>";
      }
    }else {
      array_push($errors,"Please select storage!");
    }
 }
 if (isset($_GET['clear_transfer'])) {
    unset($_SESSION['transfer_item_tray']);
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
                          <h4 class="title"> Transfer items</h4>
                      </div>
                    </div>
                    <div class="col-md-5">
                    <?php include('errors.php') ?>
                    </div>
                  </div>
                    <div class="content" style="overflow-x:auto;">
                       <?php
                       if (isset($_GET['sectionA'])) {
                         $_SESSION['tab_control']=0;
                       }if (isset($_GET['sectionB'])) {
                          $_SESSION['storage_from2'] = 0;
                         unset($_SESSION['storage_to']);
                         $_SESSION['storage_from'] = 0;
                          unset($_SESSION['error_msg']);
                         unset($_SESSION['error_msg1']);
                         $_SESSION['tab_control']=1;
                         $_SESSION['hide_panel'] = 0;
                         unset($_SESSION['transfer_item_tray']);
                       }  ?>
                      <div class="row">
                        <div class="col-md-12">
                          <ul class="nav nav-tabs" >
                             <li class="<?php if($_SESSION['tab_control']==0){echo 'active';} ?>"><a  href="?sectionA">Transfer all items</a></li>
                             <li class="<?php if($_SESSION['tab_control']==1){echo 'active';} ?>"><a  href="?sectionB">Transfer by item/qty</a></li>
                          </ul>
                        </div>
                      </div>
                        <br>
                        <?php if ($_SESSION['tab_control']==0) {
                          ?>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="">
                                <label>From : </label>
                                <select class="form-control" name="storage_from"id="storage_from_ses"onchange="session_storage_from(this.value);"style="border-color:<?php if($_SESSION['storage_from']==0 && $_SESSION['storage_to']==0&& isset($_SESSION['error_msg1'])){echo 'red';} ?>">
                                  <?php $defaultStorageFrom = 0; ?>
                                  <option value="<?php $defaultStorageFrom; ?>" <?php if ($_SESSION['storage_from']==0){ echo 'selected';} ?> >Select a storage</option>
                                 <?php $fetchStorageFrom = mysqli_query($dbcon,"SELECT * FROM storage order by storage_id");
                                 foreach ($fetchStorageFrom as $key => $value) {
                                   ?>
                                   <option value="<?php echo $value['storage_id'] ?>" <?php if ($value['storage_id']==$_SESSION['storage_from']){ echo 'selected';} ?>><?php echo $value['storage_name'] ?></option>
                                   <?php
                                 } ?>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="">
                                <label>To : </label>
                                <select class="form-control" name="storage_to"id="storage_to_ses"onchange="session_storage_to(this.value);"
                                <?php if ($_SESSION['storage_from']==0){echo 'disabled';} ?> style="border-color: <?php if($_SESSION['storage_from']!=0 && $_SESSION['storage_to']==0){echo 'red';}if($_SESSION['storage_from']==0){$_SESSION['storage_to']=0;} ?>">
                                  <?php $defaultStorageTo = 0; ?>
                                  <option value="<?php $defaultStorageTo; ?>" <?php if ($_SESSION['storage_from']==0){ echo 'selected';} ?> >Select a storage</option>
                                 <?php $fetchStorageTo = mysqli_query($dbcon,"SELECT * FROM storage where storage_id != '".$_SESSION['storage_from']."'");
                                 foreach ($fetchStorageTo as $key => $value2) {
                                   ?>
                                   <option value="<?php echo $value2['storage_id'] ?>" <?php if ($value2['storage_id']==$_SESSION['storage_to']){ echo 'selected';} ?>><?php echo $value2['storage_name'] ?></option>
                                   <?php
                                 } ?>
                                </select>
                              </div>
                            </div>
                         </div>
                          <?php
                        }if ($_SESSION['tab_control']==1) {
                          ?>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="">
                            <label>From : </label>
                            <select class="form-control" name="storage_from2"id="storage_from_ses2"onchange="session_storage_from2(this.value);"style="border-color:<?php if($_SESSION['storage_from2']==0 && $_SESSION['storage_to2']==0&& isset($_SESSION['error_msg2'])){echo 'red';} ?>">
                              <?php $defaultStorageFrom = 0; ?>
                              <option value="<?php $defaultStorageFrom; ?>" <?php if ($_SESSION['storage_from2']==0){ echo 'selected';} ?> >Select a storage</option>
                             <?php $fetchStorageFrom = mysqli_query($dbcon,"SELECT * FROM storage order by storage_id");
                             foreach ($fetchStorageFrom as $key => $value) {
                               ?>
                               <option value="<?php echo $value['storage_id'] ?>" <?php if ($value['storage_id']==$_SESSION['storage_from2']){ echo 'selected';} ?>><?php echo $value['storage_name'] ?></option>
                               <?php
                             } ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="">
                            <label>To : </label>
                            <select class="form-control" name="storage_to2"id="storage_to_ses2"onchange="session_storage_to2(this.value);"
                            <?php if ($_SESSION['storage_from2']==0){echo 'disabled';} ?> style="border-color: <?php if($_SESSION['storage_from2']!=0 && $_SESSION['storage_to2']==0){echo 'red';}if($_SESSION['storage_from2']==0){$_SESSION['storage_to2']=0;} ?>">
                              <?php $defaultStorageTo = 0; ?>
                              <option value="<?php $defaultStorageTo; ?>" <?php if ($_SESSION['storage_from2']==0){ echo 'selected';} ?> >Select a storage</option>
                             <?php $fetchStorageTo = mysqli_query($dbcon,"SELECT * FROM storage where storage_id != '".$_SESSION['storage_from2']."'");
                             foreach ($fetchStorageTo as $key => $value2) {
                               ?>
                               <option value="<?php echo $value2['storage_id'] ?>" <?php if ($value2['storage_id']==$_SESSION['storage_to2']){ echo 'selected';} ?>><?php echo $value2['storage_name'] ?></option>
                               <?php
                             } ?>
                            </select>
                          </div>
                        </div>
                     </div>
                     <?php
                   } ?>
                     <br>
                     <div class="row">
                         <div class="tab-content">
                   <div id="sectionA" class="tab-pane fade in <?php if($_SESSION['tab_control']==0){echo 'active';} ?>">
                             <div class="col-md-6">
                               <div class="card">
                               <div class="content">
                             <br><br><br><br>
                             <center>
                               <?php
                               if ($_SESSION['storage_from']==0) {
                                   ?>
                                   <h4>Transfer all items from </h4>
                                   <h3 style="color:gray;">(No storage selected)</h3>
                                   <?php
                               }else {
                               $checkStorageItems = mysqli_query($dbcon,"SELECT count(utensils_id)as utensils_id  FROM storage_stocks where  storage_id = ".$_SESSION['storage_from']);
                               $checkStorageItemsZeros = mysqli_query($dbcon,"SELECT count(utensils_id)as utensils_id  FROM storage_stocks where original_stock = 0 and storage_id = ".$_SESSION['storage_from']);
                               foreach ($checkStorageItems as $key => $value1) {
                                 foreach ($checkStorageItemsZeros as $key => $value2) {
                                   if ($value1['utensils_id']==$value2['utensils_id']) {
                                     $error = true;
                                   }else {
                                     $error = false;
                                   }
                                 }
                               }
                               // code...

                               if ($error == false) { ?>
                               <h4>Transfer all items from </h4>
                               <?php if ($_SESSION['storage_from']!=0) {
                                 $storageNameF = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = ".$_SESSION['storage_from']);
                                 foreach ($storageNameF as $key => $valueF);
                                $from= $valueF['storage_name'];
                                 ?>
                                 <h3><a href="#"data-toggle="modal" data-target="#myModal_transmital1"><?php echo $from; ?></a></h3>
                                 <br>
                               <div class="center">
                                <a href="?transfer_all"class="btn btn-sm btn-fill btn-warning" name="button">Transfer <i class="fa fa-arrow-right"></i></a>
                               </div>
                                 <?php
                               } ?>
                             <?php }else {
                                ?>
                                <?php if (isset($_SESSION['error_msg'])) {
                                ?><h4 class="text-danger"><?php  echo $_SESSION['error_msg']; ?></h4><?php
                                }else {
                                  ?><h4 class="text-danger">(NO ITEMS FOUND!)</h4><?php
                                } ?>
                                <?php    }
                             } ?>
                              </center>
                             <br><br><br><br>
                              </div>
                              </div>
                              </div>
                              <div class="col-md-6">
                                <div class="card">
                              <div class="content">
                               <br><br><br><br>
                               <center>
                              <h4>To  </h4>
                              <?php if ($_SESSION['storage_to']!=0) {
                                $storageNameF = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = ".$_SESSION['storage_to']);
                                foreach ($storageNameF as $key => $valueF);
                               $to= $valueF['storage_name'];
                                ?>
                                <h3><a href="#"data-toggle="modal" data-target="#myModal_transmital2"><?php echo $to; ?></a></h3>
                                <?php
                              }else {
                                ?>
                                <h3 style="color:gray;">(No storage selected)</h3>
                                 <?php
                              } ?>
                              <div class="center">
                               </div>
                              </center>
                              <br><br><br><br>
                             </div>
                              </div>
                           </div>
                        </div>
                   <div id="sectionB" class="tab-pane fade in <?php if($_SESSION['tab_control']==1){echo 'active';} ?>">

                     <div class="col-md-6">
                       <div class="card">
                       <div class="content">
                        <?php if ($_SESSION['storage_from2']==0) {
                          ?>
                          <br><br><br><br>
                          <center>
                            <h4>Transfer by item/qty from </h4>
                            <h3 style="color:gray;">(No storage selected)</h3>
                            <br>
                           </center>
                          <br><br><br><br>
                          <?php
                        }else {
                       ?>
                       <?php if ($_SESSION['hide_panel']==0) {
                        ?>
                        <br><br><br><br>
                        <center>
                          <h4>Transfer by item/qty from </h4>
                          <?php if ($_SESSION['storage_from2']!=0) {
                            $storageNameF = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = ".$_SESSION['storage_from2']);
                            foreach ($storageNameF as $key => $valueF);
                           $from= $valueF['storage_name'];
                            ?>
                            <h3><a href="#"data-toggle="modal" data-target="#myModal_transmital1"><?php echo $from; ?></a></h3>
                            <?php

                          } ?>

                          <br>
                         </center>
                        <br><br><br><br>
                        <?php
                      }else {
                        $checkStorageItems = mysqli_query($dbcon,"SELECT count(utensils_id)as utensils_id  FROM storage_stocks where  storage_id = ".$_SESSION['storage_from2']);
                        $checkStorageItemsZeros = mysqli_query($dbcon,"SELECT count(utensils_id)as utensils_id  FROM storage_stocks where original_stock = 0 and storage_id = ".$_SESSION['storage_from2']);
                        foreach ($checkStorageItems as $key => $value1) {
                          foreach ($checkStorageItemsZeros as $key => $value2) {
                            if ($value1['utensils_id']==$value2['utensils_id']) {
                              $error = true;
                            }else {
                              $error = false;
                            }
                          }
                        }
                        if ($error == false) {
                        $queryString = "SELECT *
                        FROM utensils a
                        left join utensils_category b on a.utensils_cat_id = b.utensils_cat_id
                        left join storage_stocks c on a.utensils_id = c.utensils_id
                        where c.storage_id = '".$_SESSION['storage_from2']."' and c.original_stock !=0";
                         $getItemsFromStorage = mysqli_query($dbcon,$queryString);
                        ?>
                        <table class="table "id="by_qty">
                          <thead>

                            <tr>
                              <th>ID</th>
                              <!-- <th>ORIG-QTY</th> -->
                              <th>CUR-QTY</th>
                              <th>Transfer qty</th>
                              <th>ITEMS</th>
                              <th>CATEGORY</th>
                              <th>MODEL</th>
                              <th>SERIAL</th>

                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            foreach ($getItemsFromStorage as $key => $value) {
                           ?>
                            <tr>
                              <td><?php echo $value['utensils_id'] ?></td>
                              <!-- <td><?php echo $value['original_stock'] ?></td> -->
                              <td><?php echo $value['storage_qty'] ?></td>
                              <style media="screen">
                              .buttonInside{
                                 position:relative;
                                 margin-bottom:0px;
                               }
                               button{
                                 position:absolute;
                                 left: 0px;
                                 top: 0px;
                               }
                              </style>
                              <td><form class="form-inline" action="transmital.php?action=add&id=<?php echo $value["utensils_id"]; ?>" method="post">
                                <input type="hidden" name="storage_id" value="<?php echo $value["storage_id"]; ?>">
                                <input type="hidden" name="utensils_name" value="<?php echo $value["utensils_name"]; ?>">
                                <input type="hidden" name="category" value="<?php echo $value["category"]; ?>">
                                <input type="hidden" name="model" value="<?php echo $value["model"]; ?>">
                                <input type="hidden" name="serial_no" value="<?php echo $value["serial_no"]; ?>">
                                <div class="buttonInside">
                                  <input type="number"class="form-control text-center" name="qty"id="numInput2" value=""placeholder="move"required >
                                    <button type="submit"name="add_new_item" class="btn btn-fill   btn-info ">
                                    <i class="fa fa-share"></i>
                                    </button>
                                </div>
                              </form></td>
                              <td><?php echo $value['utensils_name'] ?></td>
                              <td><?php echo $value['category'] ?></td>
                              <td><?php echo $value['model'] ?></td>
                              <td><?php echo $value['serial_no'] ?></td>
                            </tr>
                          <?php } ?>
                          </tbody>
                        </table>
                        <?php   // end of false
                      }else {
                        ?>
                        <br><br><br><br>
                        <center>
                          <h4 class="text-danger">(NO ITEMS FOUND!) </h4>
                          <br>
                         </center>
                        <br><br><br><br>
                        <?php
                      } ?>
                        <?php
                      }
                    } ?>
                      </div>
                      </div>
                      </div>
                      <div class="col-md-6">
                        <div class="card">
                      <div class="content">
                        <?php if (isset($_SESSION['transfer_item_tray'])) {
                          ?>
                        <div class="content">
                          <div class="row">
                            <div class="col-md-3">
                              <a href="?transfer_by_item"onclick="return confirm('Confirm Transfer!');"class="btn btn-fill btn-warning btn-sm">Transfer now <i class="fa fa-upload"></i></a>
                            </div>
                            <div class="col-md-3">
                              <a href="?clear_transfer"class="btn btn-fill btn-success btn-sm">Clear <i class="fa fa-eraser"></i></a>
                            </div>
                          </div>
                         </div>
                              <table class="table"id="tray">
                                <thead>
                                  <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Transfered qty</th>
                                    <th>ITEMS</th>
                                    <th>CATEGORY</th>
                                    <th>MODEL</th>
                                    <th>SERIAL</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                  foreach (array_filter($_SESSION['transfer_item_tray']) as $key => $value) {
                                 ?>
                                  <tr>
                                    <td><a href="?action=delete&ids=<?php echo $value["utensils_id"]; ?>"><i class="fa fa-times text-danger"></i></a></td>
                                    <td><?php echo $value['utensils_id'] ?></td>
                                    <td class="text-center bg bg-info"><?php echo $value['qty'] ?></td>
                                    <td><?php echo $value['utensils_name'] ?></td>
                                    <td><?php echo $value['category'] ?></td>
                                    <td><?php echo $value['model'] ?></td>
                                    <td><?php echo $value['serial_no'] ?></td>
                                  </tr>
                                <?php } ?>
                                </tbody>
                              </table>
                          <?php
                        }else {
                         ?>
                       <br><br><br><br>
                       <center>
                      <h4>To  </h4>
                      <?php if ($_SESSION['storage_to2']!=0) {
                        $storageNameF = mysqli_query($dbcon,"SELECT * FROM storage where storage_id = ".$_SESSION['storage_to2']);
                        foreach ($storageNameF as $key => $valueF);
                       $to= $valueF['storage_name'];
                        ?>
                        <h3><a href="#"data-toggle="modal" data-target="#myModal_transmital2"><?php echo $to; ?></a></h3>
                        <?php
                      }else {
                        ?>
                        <h3 style="color:gray;">(No storage selected)</h3>
                         <?php
                      } ?>
                      <div class="center">
                       </div>
                       <br>
                      </center>
                      <br><br><br><br>
                    <?php } ?>
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
    <div class="modal fade modal-primary"id="myModal_transmital1" data-backdrop="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                  <span >Items from : <?php echo $from; ?></span>
                  <a href="#"class="close" data-dismiss="modal">&times;</a>
                </div>
                <div class="modal-body ">
                  <div class="content">
                    <?php $queryString = "SELECT
                                        a.utensils_id , a.utensils_name, a.stock_on_hand, a.umsr,a.original_stock
                                       ,a.model, a.serial_no, a.date_purchased,a.utensils_cat_id,a.cost,
                                        b.utensils_cat_id as catID, b.category,
                                        c.storage_id,sum(c.original_stock) as origStock,c.storage_qty,c.utensils_id,
                                        d.id,d.umsr_name

                                        FROM utensils a
                                        LEFT JOIN utensils_category b ON a.utensils_cat_id = b.utensils_cat_id
                                        LEFT JOIN storage_stocks c ON a.utensils_id = c.utensils_id
                                        LEFT JOIN umsr d ON a.umsr = d.id
                                        where c.original_stock > 0 and c.storage_id = '".$_SESSION['storage_from']."'
                                        group BY c.utensils_id ";
                                        $query = mysqli_query($dbcon,$queryString); ?>
                  <table class="table table-hovered">
                    <thead>
                      <tr>
                        <th class="bg bg-success">ID</th>
                        <th class="bg bg-info">ITEMS </th>
                        <th class="bg bg-info">CATEGORY</th>
                        <th class="bg bg-info">QTY</th>
                        <th class="bg bg-info">MODEL</th>
                        <th class="bg bg-info">SERIAL NO. </th>
                        <tr>
                    </thead>
                        <tbody>
                          <?php while ($rows = mysqli_fetch_array($query)) {
                           ?>
                          <tr>
                            <td><?php echo $rows['utensils_id'] ?></td>
                            <td><?php echo $rows['utensils_name'] ?></td>
                            <td><?php echo $rows['category'] ?></td>
                            <td><?php echo $rows['origStock'] ?></td>
                            <td><?php echo $rows['model'] ?></td>
                            <td><?php echo $rows['serial_no'] ?></td>
                          </tr>
                        <?php
                             }?>
                        </tbody>
                  </table>
                </div>
           </div>
            </div>
        </div>
    </div>
    <!--  End Modal -->
    <!-- Mini Modal -->
    <div class="modal fade modal-primary" id="myModal_transmital2" data-backdrop="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header justify-content-center">
                  <span >Items from : <?php echo $to; ?></span>
                  <a href="#"class="close" data-dismiss="modal">&times;</a>
                </div>
                <div class="modal-body ">
                  <div class="content">
                    <?php
                    if (isset($_SESSION['storage_to'])) {
                      $storageTo = $_SESSION['storage_to'];
                    }
                    if (isset($_SESSION['storage_to2'])) {
                      $storageTo = $_SESSION['storage_to2'];
                    }
                    $queryString = "SELECT
                                        a.utensils_id , a.utensils_name, a.stock_on_hand, a.umsr,a.original_stock
                                       ,a.model, a.serial_no, a.date_purchased,a.utensils_cat_id,a.cost,
                                        b.utensils_cat_id as catID, b.category,
                                        c.storage_id,sum(c.original_stock) as origStock,c.storage_qty,c.utensils_id,
                                        d.id,d.umsr_name

                                        FROM utensils a
                                        LEFT JOIN utensils_category b ON a.utensils_cat_id = b.utensils_cat_id
                                        LEFT JOIN storage_stocks c ON a.utensils_id = c.utensils_id
                                        LEFT JOIN umsr d ON a.umsr = d.id
                                        where c.original_stock > 0 and c.storage_id = $storageTo
                                        group BY c.utensils_id ";
                                        $query = mysqli_query($dbcon,$queryString); ?>
                  <table class="table table-hovered">
                    <thead>
                      <tr>
                        <th class="bg bg-success">ID</th>
                        <th class="bg bg-info">ITEMS </th>
                        <th class="bg bg-info">CATEGORY</th>
                        <th class="bg bg-info">QTY</th>
                        <th class="bg bg-info">MODEL</th>
                        <th class="bg bg-info">SERIAL NO. </th>
                        <tr>
                    </thead>
                        <tbody>
                          <?php while ($rows = mysqli_fetch_array($query)) {
                           ?>
                          <tr>
                            <td><?php echo $rows['utensils_id'] ?></td>
                            <td><?php echo $rows['utensils_name'] ?></td>
                            <td><?php echo $rows['category'] ?></td>
                            <td><?php echo $rows['origStock'] ?></td>
                            <td><?php echo $rows['model'] ?></td>
                            <td><?php echo $rows['serial_no'] ?></td>
                          </tr>
                        <?php
                             }?>
                        </tbody>
                  </table>
                </div>
           </div>
            </div>
        </div>
    </div>
    <!--  End Modal -->
    <?php include('dataTables2.php') ?>
    <script type="text/javascript">
              $('#by_qty').DataTable( {
               "pageLength": 50,
               "scrollX": true
               } );
               $('#tray').DataTable( {
                "pageLength": 50,
                "scrollX": true,
                "bPaginate": false,
                "bLengthChange": false,
                "bFilter": true,
                 "bInfo": false

                } );
            </script>
<?php include('footer.php') ?>
