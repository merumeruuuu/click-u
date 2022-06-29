<?php require('server.php');  ?>
        <!doctype html>
        <html lang="en">
        <head>
        	<meta charset="utf-8" />
        	<link rel="icon" type="image/png" href="assets/img/favicon.ico">
        	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

        	<title>ClickU</title>
        	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
            <meta name="viewport" content="width=device-width" />

            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
          <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
          <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>


          <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
         <script src="http://code.jquery.com/jquery-1.8.2.js"></script>
          <script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>

          <script src="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"></script>
        </head>
        <style media="screen">
        @media print {
      #printPageButton {
        display: none;
      }
    }
        </style>
        <script type="text/javascript">
      function printpage() {
     //Get the print button and put it into a variable
     var printButton1 = document.getElementById("printPageButton1");
     var printButton = document.getElementById("printPageButton");
     //Set the print button visibility to 'hidden'
     printButton1.style.visibility = 'hidden';
     printButton.style.visibility = 'hidden';
     //Print the page content
     window.print()
     printButton1.style.visibility = 'visible';
     printButton.style.visibility = 'visible';
 }
        </script>
        <body>
          <section class="container">
            <div class="">
              <div class="col-md-4">
                <div class="">
                  <br>
                  <?php if ($_SESSION['account_type']==6||$_SESSION['account_type']==7) {
                    ?>
                    <a href="userRequestsMenu2.php"id="printPageButton1"class="btn btn-info btn-fill bt-sm" name="button">Back</a>
                    <?php
                  }else {
                    ?>
                    <a href="discrepancyReport.php"id="printPageButton1"class="btn btn-info btn-fill bt-sm" name="button">Back</a>
                    <?php
                  } ?>

                   <button onclick="printpage()"id="printPageButton"class="btn btn-warning btn-fill bt-sm" name="button">Print Report</button>
                </div>
              </div>
            </div>
            <div class="content"id="nodeToRenderAsPDF">
              <div class="card">
                  <img src="img/report_logo.jpg"style='width:100%;' border="0" alt="Null">
                  <br>
                  <div class="row">
                    <div class="col-md-8">
                      <!-- <h5>Inventory Report</h5> -->
                      <h5> Date retrieved : <?php echo date('m-d-Y'); ?> </h5>
                    </div>
                  </div>
              </div>
              <div class="content" >
                  <div class="container-fluid">
                      <div class="row">

                          <div class="col-md-12">
                              <div class="card">
                    <?php if (isset($_GET['id'])) {
                      $_SESSION['form_id'] = $_GET['id'];
                    } ?>
                    <?php  $form_id = $_SESSION['form_id'];
                            ?>
                            <?php
                                   $query = "SELECT
                                                   a.borrower_slip_id as requestID,a.group_id as grpID,a.date_requested,a.added_by,a.date_approved,a.purpose,
                                                   a.date_requested,a.aprvd_n_rlsd_by,a.storage_id,a.status,
                                                   b.group_id,b.group_name,b.instructor,b.group_leader_id,
                                                   c.group_id,c.user_id,c.added_by,
                                                   d.user_id,d.school_id,d.fname,d.lname,
                                                   e.user_id,e.account_type_id,
                                                   f.storage_id,f.storage_name,
                                                   g.borrower_slip_id,count(g.bsd_id)


                                                   from borrower_slip a
                                                   left join group_table b on a.group_id = b.group_id
                                                   left join group_members c on b.group_id = c.group_id
                                                   left join users d on c.user_id = d.user_id
                                                   left join user_settings e on d.user_id = e.user_id
                                                   left join storage f on a.storage_id = f.storage_id
                                                   left join borrower_slip_details g on a.borrower_slip_id = g.borrower_slip_id


                                                   where a.borrower_slip_id = $form_id";
                                    $result = mysqli_query($dbcon,$query);
                                    $rows = mysqli_fetch_array($result);

                                   ?>


               <div class="content">
                 <div class="card">
                   <div class="row">
                     <div class="col-md-12">
                       <div class="content">
                       </div>
                      <br>
                      <center>
                      <h4>Damage/Lost form</h4>
                    </center>
                     </div>
                    <div class="col-md-12">

                       <div class="content">
                         <table class="table">
                           <tr>

                           <td>
                         <div class="header">
                             <h5 class=" ">Request # <strong> <?php echo $rows['requestID']; ?></strong></h5>
                         </div>
                        <div class="">
                          <label for="">Borrower/s :</label>
                          <?php
                          $grpID = $rows['grpID'];
                          $borrowers = "SELECT
                                    a.group_id,a.user_id,
                                    b.user_id,b.school_id,b.lname,b.fname

                                    from group_members a
                                    left join users b on a.user_id = b.user_id

                                    where a.group_id = $grpID";
                           $check = mysqli_query($dbcon,$borrowers);
                           while ($borrower = mysqli_fetch_array($check)) { ?>
                             <p>
                             <?php echo $borrower['school_id']; ?> -
                             <?php echo $borrower['lname'];  ?>,
                             <?php echo $borrower['fname'];  ?>
                             </p>
                           <?php } ?>

                        </div>
                         </td>
                         <td>
                           <br>
                             <label for="">Group Name :</label>
                             <span>
                             <?php if (!empty($rows['group_name'])){ ?>

                               <?php echo $rows['group_name']; ?>

                             <?php }else {
                               echo "N/A";
                             }?>
                           </span>

                         <br>
                           <label for="">Instructor :</label>
                           <span>
                             <?php if ($rows['account_type_id']=="7") {
                               ?>
                              <?php echo$rows['instructor']; ?>
                               <?php
                             }else {
                               echo "N/A";
                             }
                               ?>
                           </span>
                       </td>
                       <td>
                         <br>
                         <label for="">Date Borrowed :</label>
                         <span>
                         <?php echo  date('M d, Y',strtotime($rows['date_requested'])); ?>
                       </span>
                       <br>
                      <label for="">Borrowed From :</label>
                      <span>
                      <?php echo $rows['storage_name']; ?>
                    </span>
                    <br>
                    <label for="">Purpose :</label>
                    <span>
                    <?php echo $rows['purpose']; ?>
                  </span>
                       </td>
                          </tr>
                           </table>
                    <div class="col-md-12">
                     <hr>
                    </div>
                                 <div class="col-md-12">
                                   <label for="">Reported Items :</label>
                                   <?php
                                   $reqID = $rows['requestID'];
                                       $queryString = "SELECT
                                         a.borrower_slip_id,a.utensils_id,a.lost_qty,a.damaged_qty,
                                         b.utensils_id,b.utensils_name,b.utensils_cat_id,
                                         c.utensils_cat_id,c.category

                                         from breakages_and_damages a
                                         left join utensils b on a.utensils_id = b.utensils_id
                                         left join utensils_category c on b.utensils_cat_id = c.utensils_cat_id
                                         where a.borrower_slip_id = $reqID";
                                         $itemQuery = mysqli_query($dbcon,$queryString);

                                    ?>
                                    <table class="table">
                                      <thead>
                                        <tr>
                                          <th>Item ID</th>
                                          <th>Item Name</th>
                                          <th>Item Category</th>
                                         <th>Lost </th>
                                         <th>Damaged </th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php while ($items = mysqli_fetch_array($itemQuery)) {

                                          ?>
                                        <tr>
                                          <td><?php echo  $items['utensils_id']?></td>
                                          <td><?php echo  $items['utensils_name']?></td>
                                          <td><?php echo  $items['category']?></td>
                                          <td><?php echo  $items['lost_qty']?></td>
                                          <td><?php echo  $items['damaged_qty']?></td>

                                       <?php
                                      }?>
                                        </tr>

                                      </tbody>
                                    </table>
                                        </div>
                                        <div class="row">
                                          <table class="table">
                                            <tr>
                                              <td>
                                                <br><br>
                                              <div > <hr> </div>
                                               <div class="text-center">STUDENT'S NAME AND SIGNITURE</div>
                                               </td>
                                               <td>
                                                 <div class="text-center">

                                              </div></td>
                                              <td>
                                                <br><br>
                                                <div > <hr> </div>
                                                 <div class="text-center">WORKING SCHOLAR IN-CHARGE</div>
                                              </td>
                                            </tr>
                                            <tr>
                                              <td>
                                                <br><br>
                                              <div > <hr> </div>
                                               <div class="text-center">INSTRUCTOR</div>
                                               </td>
                                               <td>  <div class="text-center">
                                                <h6>VERIFIED BY:</h6>
                                              </div></td>
                                              <td>
                                                <br><br>
                                                <div > <hr> </div>
                                                 <div class="text-center">LABORATORY IN-CHARGE</div>
                                              </td>
                                            </tr>
                                          </table>
                                            <div class="col-md-12">
                                              <div class="text-center">
                                           <h6>NOTED BY:</h6>
                                           <br>
                                           <b>DR. GRAYFIELD T. BAJAO</b>
                                           <p>Dean, College of HRM/SEACAST Director</p>
                                           <br>
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
              <!-- <table class="table ">
                <tr>
                  <div class="text-center">
                   <p >Any deformation, loses or damages of the above item/s are my sole responsibility as Head/In-charge of this department.</p>
                    </div>
                </tr>
                <tr class="text-center">
                 <td>
                 <h5>Checked by :</h5>
                  <h4>John Carlo M. Arellano</h4>
                 </td>
                  <td>
                   <h5>Checked by :</h5>
                   <h4>Jessua Jugalbot</h4>
                   </td>
                </tr>
              </table>
                 <table class="table table-bordered">
                   <tr class="text-center">
                      <td>
                        <h4>Ms. Sheila Mae C. Pogoy</h4>
                        <h5>Inventory In-charge</h5>
                       </td>
                      <td>
                      <h4>MS. DARYL F. LEGARDE</h4>
                      <h5>Property Custodian</h5>
                    </td>
                      <td>
                        <h4>DR. GRAYFIELD BAJAO</h4>
                        <h5>Department Head/In-charge</h5>
                      </td>
                   </tr>
                 </table> -->
            </div>
          </section>
          <script type="text/javascript">
          </script>


          </div>
          </div>


          </body>
          <!--   Core JS Files   -->
          <script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
          <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>

          <!--  Charts Plugin -->
          <script src="assets/js/chartist.min.js"></script>

          <!--  Notifications Plugin    -->
          <script src="assets/js/bootstrap-notify.js"></script>


          <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
          <script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>

          <!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
          <script src="assets/js/demo.js"></script>

          <!-- <script type="text/javascript">
          // $(document).ready(function(){
          //
          //   demo.initChartist();
          //
          //   $.notify({
          //       icon: 'pe-7s-satisfied',
          //       message: "Welcome to <b>ClickU</b> - UCLM Kitchen Utensil Online Borrowing."
          //
          //     },{
          //         type: 'info',
          //         timer: 4000
          //     });
          //
          // });
          </script> -->

          </html>
