$(document).ready(function(){
  var currentLocation = window.location;
  console.log(currentLocation.pathname);

    // View Request details
    if(currentLocation.pathname == "/trial/modifyUserRequests.php") {
      $('.btn_updateRequest').on('click', function(){
        var transID = $(this).attr('data-rowID');
        var itemID = $(this).attr('data-itemID');
        console.log($(this).attr('data-rowID'));

        $.ajax({
          url: "ajaxrequest/qtyUpdate.php",
          type: "post",
          dataType: "json",
          data: {
            "transID":transID,
            'itemID':itemID
          },
          success: function(returnData) {
            var request_qty = "",storage_qty = "";
            console.log(returnData);

            $.each(returnData['req_qty'], function(index, value){
              request_qty += '<input type="number"disabled  value="'+value['qty']+'">';
            });
            $('#currentQty').html(request_qty);
            $.each(returnData['stock_qty'], function(index, v){
              storage_qty += '<input type="number"disabled value="'+v['storage_qty']+'" >';
            });
            $('#storageQty').html(storage_qty);

          },
          error: function(xhr, status, error) {
           console.log(xhr.responseText);
         }

           }
        });
      });
    }

///
});
