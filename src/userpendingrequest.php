<?php include('header.php') ?>
<style>
body {font-family: Arial;}

/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
  background-color: #ccc;
}
</style>
<br><br><br>

<div class="content">


                        <div class="card">
                            <div class="header">
                                <h5 class="title">My Requests <i class="fa fa-chevron-down"></i></5>
                            </div>
                            <div class="content">

                              <div class="tab">
                                <button class="tablinks" onclick="openCity(event, 'pending')"><i class="fa fa-chevron-down"></i> On Queue</button>
                                <button class="tablinks" onclick="openCity(event, 'approved')"><i class="fa fa-chevron-down"></i> Approved</button>
                                <button class="tablinks" onclick="openCity(event, 'cancelled')"><i class="fa fa-chevron-down"></i> Cancelled</button>
                                <button class="tablinks" onclick="openCity(event, 'returned')"><i class="fa fa-chevron-down"></i> Returned</button>
                                <button class="tablinks" onclick="openCity(event, 'dl')"><i class="fa fa-chevron-down"></i> Damaged/Lost</button>
                              </div>

                              <div id="pending" class="tabcontent">
                                <div class="card">
                              <div class="content">
                                <table class="table table-bordered table-hover"id="UtensilTable">
                                  <thead class=".th th-default">
                                    <tr>
                                      <th>Borrow #</th>
                                      <th>Requested Item/s</th>
                                      <th>Requested Quantity</th>
                                      <th>Category</th>
                                      <th>Storage</th>
                                      <th>Date Requested</th>
                                      <th>Member/s</th>
                                      <th>Instrutor</th>
                                      <th>Action</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php
                                    foreach (getAllRequestedItems() as $borrowRequests) {

                                    ?>
                                    <tr>
                                      <td><?php echo $borrowRequests['transID'] ?></td>
                                      <td><?php echo $borrowRequests['itemName'] ?></td>
                                      <td><?php echo $borrowRequests['itemQty'] ?></td>
                                      <td><?php echo $borrowRequests['category'] ?></td>
                                      <td><?php echo $borrowRequests['storage_name'] ?></td>
                                      <td><?php echo $borrowRequests['date_added'] ?></td>
                                      <td><?php echo $borrowRequests['school_id'] ?> , <?php echo $borrowRequests['lname'] ?></td>
                                      <td><?php echo $borrowRequests['faculty_id'] ?></td>
                                      <td>
                                          <a href="#" class="btn btn-sm btn-success btn-fill" ><i class="fa fa-check"></i>Canel</a>
                                      </td>
                                    </tr>
                                  <?php  }?>
                                  </tbody>
                                </table>
                                </div>
                              </div>

                              </div>

                              <div id="approved" class="tabcontent">
                                <h3>approved</h3>
                                <p>approved is the capital of France.</p>
                              </div>

                              <div id="cancelled" class="tabcontent">
                                <h3>eeeee</h3>
                                <p>cancelled is the capital of Japan.</p>
                              </div>
                              <div id="returned" class="tabcontent">
                                <h3>ffffff</h3>
                                <p>cancelffffffffpan.</p>
                              </div>
                              <div id="dl" class="tabcontent">
                                <h3>caasdfsled</h3>
                                <p>cancasdfsdaJapan.</p>
                              </div>

                        </div>
                    </div>
                </div>





      <script>
      function openCity(evt, tab_content) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
          tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
          tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tab_content).style.display = "block";
        evt.currentTarget.className += " active";
      }
      </script>


<?php include('footer.php') ?>
