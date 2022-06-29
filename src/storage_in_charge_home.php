<?php include('header.php') ?>

<style media="screen">
td.details-control {
  background: url('../resources/details_open.png') no-repeat center center;
  cursor: pointer;
}
tr.shown td.details-control {
  background: url('../resources/details_close.png') no-repeat center center;
}
</style>
<br><br><br>
<div class="content"id="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="header">
                        <h4 class="title">General Utensils Monitoring :</h4>
                        <p class="category">(General Storage)</p>
                    </div>
                    <div class="content">
                      <table id="UtensilTable" class="display" style="width:100%">
          <thead>
              <tr>
                  <th></th>
                  <th>Name</th>
                  <th>Position</th>
                  <th>Office</th>
                  <th>Salary</th>
              </tr>
          </thead>
          <tfoot>
              <tr>
                  <th></th>
                  <th>Name</th>
                  <th>Position</th>
                  <th>Office</th>
                  <th>Salary</th>
              </tr>
          </tfoot>
      </table>


                   </div>
              </div>
              </div>
            </div>
          </div>
    </div>
    <?php include('dataTables2.php') ?>
    <script type="text/javascript">
              // $('#UtensilTable').DataTable();


                            /* Formatting function for row details - modify as you need */
              function format ( d ) {
                  // `d` is the original data object for the row
                  return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
                      '<tr>'+
                          '<td>Full name:</td>'+
                          '<td>'+d.name+'</td>'+
                      '</tr>'+
                      '<tr>'+
                          '<td>Extension number:</td>'+
                          '<td>'+d.extn+'</td>'+
                      '</tr>'+
                      '<tr>'+
                          '<td>Extra info:</td>'+
                          '<td>And any further details here (images etc)...</td>'+
                      '</tr>'+
                  '</table>';
              }

              $(document).ready(function() {
                  var table = $('#UtensilTable').DataTable( {
                      "ajax": "ajax.txt",
                      "columns": [
                          {
                              "className":      'details-control',
                              "orderable":      false,
                              "data":           null,
                              "defaultContent": ''
                          },
                          { "data": "name" },
                          { "data": "position" },
                          { "data": "office" },
                          { "data": "salary" }
                      ],
                      "order": [[1, 'asc']]
                  } );

                  // Add event listener for opening and closing details
                  $('#UtensilTable tbody').on('click', 'td.details-control', function () {
                      var tr = $(this).closest('tr');
                      var row = table.row( tr );

                      if ( row.child.isShown() ) {
                          // This row is already open - close it
                          row.child.hide();
                          tr.removeClass('shown');
                      }
                      else {
                          // Open this row
                          row.child( format(row.data()) ).show();
                          tr.addClass('shown');
                      }
                  } );
              } );
            </script>
<?php include('footer.php') ?>
