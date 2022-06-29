$(document).ready(function(){
  var currentLocation = window.location;
  console.log(currentLocation.pathname);
  var students = [];
  var ifFirstTimer = false;

  //Validate borrower
  $('#studCategory').on('change', function(){
    // alert($(this).val());
    clearStudentSession();
    var category = $(this).val();
    var options = '<option value="">Select EDP</option>';

    if(!category.trim())
      alert('Please select category!');
    else {
      $.ajax({
        url: 'ajaxrequest/validateBorrower.php',
        type: 'POSt',
        dataType: 'json',
        data: {
          edpCat : category
        },
        success: function(returnData) {

          if(returnData == 0) {
            alert("No EDP for this category!");
          } else {
            $.each(returnData, function(i,v){
              options += '<option value="'+v['edp_id']+'">'+v['edp_code']+ ' - ' +v['sub_name']+'</option>';
            });

            $('#edpEntries').html(options);
          }
          console.log(returnData);
        },
        error: function(xhr, status, error) {
          console.log(xhr.responseText);
         }
      });
    }
  });

  $('#edpEntries').on('change', function(){
    clearStudentSession();
    // alert($(this).val());
    var edpID = $(this).val();

    students = []; //empty the array

    $.ajax({
     url: "ajaxrequest/validateBorrower.php",
     type: "POST",
     dataType: "json",
     data: {
       edpID : edpID
     },
     success: function(returnData) {
       if(returnData == 0)
        alert("No Students enrolled to this EDP!");
      else {
        $.each(returnData, function(i,v){
          students.push(v['stud_id']);
        });
      }

      // console.log(students);
       // console.log(returnData);
     },
     error: function(xhr, status, error) {
        console.log(xhr.responseText);
      }
    });
  });

  $('#checkBorrower').on('click', function(){
    var studID = $('#borrower').val();

    if(students.includes(studID)) {
      var row = "<tr>";
      $.ajax({
       url: "ajaxrequest/validateBorrower.php",
       type: "POST",
       dataType: "json",
       data: {
         id : studID
       },
       success: function(returnData) {
         if(returnData == 1) {
          row += "<td>"+studID+"</td>";
          row += '<td><button class="removeBorrower btn btn-danger">Remove</button></td>';
          row += "</tr>";
          $('#borrowerTable').append(row);
          console.log('<a href="selectutensils.php" class="btn btn-success" id="selectbutton"><< Select Utensils >></a>');
          $('#btnSelectUtensils').html('<a href="selectutensils.php" class="btn btn-success" id="selectbutton"><< Select Utensils >></a>');
        } else if(returnData == 2) {
          alert("Student ID: "+studID+" is already listed");
        } else {
         alert("Student ID: "+studID+" is not registered");
        }
         console.log(returnData);
         $('#borrower').val('');
       },
       error: function(xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    } else {
     alert("Student ID: "+studID+" is not registered");
    }
  });

  //for clearing session
    function clearStudentSession() {
    $.ajax({
     url: "ajaxrequest/validateBorrower.php",
     type: "POST",
     dataType: "json",
     data: {
       clear : true
     },
     success: function(returnData) {
       $('#borrowerTable').html('');
     },
     error: function(xhr, status, error) {
        console.log(xhr.responseText);
      }
    });
  }
  //for dynamic elements
  $('#borrowerTable').on('click', ".removeBorrower", function() {
    var studID = $(this).parents("tr").find('td:nth-child(1)').text();
    var size = 0;
    $(this).parents("tr").remove();
// alert(studID);
    $.ajax({
      url: "ajaxrequest/validateBorrower.php",
      type: "POST",
      dataType: "json",
      data: {
        toRemoveid : studID
      },
      success: function(returnData) {
        console.log(Object.keys(returnData).length);
        size = Object.keys(returnData).length;
        if(size <= 0)
          $('#btnSelectUtensils').html('');
      },
      error: function(xhr, status, error) {
        console.log(xhr.responseText);
      }
    });
});

  //change borrower Type
  $("input[name='borrowerType']").on("change", function(){
    var borrowerType = $(this).val();

    $.ajax({
     url: "ajaxrequest/validateBorrower.php",
     type: "POST",
     dataType: "json",
     data: {
       borr_Type : borrowerType
     },
     success: function(returnData) {
       if(borrowerType == 1) {
         $("#forStudentField").css({
           "display":"block"
         });
         $("#forTeacherField").css({
           "display":"none"
         });
         $("#forOthersField").css({
           "display":"none"
          });
       }else if(borrowerType == 2) {
         $("#forStudentField").css({
           "display":"none"
         });

         $("#forTeacherField").css({
           "display":"block"
         });

         $("#forOthersField").css({
           "display":"none"
         });
         $("#borrowerTable").css({
           "display":"none"
         });
       }else if(borrowerType == 3) {
         $("#forStudentField").css({
           "display":"none"
         });

         $("#forTeacherField").css({
           "display":"none"
         });
         $("#borrowerTable").css({
           "display":"none"
         });
         $("#forOthersField").css({
           "display":"block"
         });
       }
       $('#borrowerTable').html('');
       $('#borrowedItems').html('<tr><td colspan="5"><p>Your borrower is empty.....</p></td></tr>');
     },
     error: function(xhr, status, error) {
        console.log(xhr.responseText);
      }
    });
  });

  //trigger first time borrower (teacher)
  // $('#ifFirstTimer').on("change", function(){
  //   if(this.checked) {
  //     $('#teacherField_frequent').css({
  //       "display":"none"
  //     });
  //       $('#teacherField_firstTime').css({
  //         "display":"block"
  //       });
  //       ifFirstTimer = true;
  //   } else {
  //     $('#teacherField_firstTime').css({
  //       "display":"none"
  //     });
  //       $('#teacherField_frequent').css({
  //         "display":"block"
  //       });
  //       ifFirstTimer = false;
  //   }
  //   // alert(this.checked);
  // });

  //Validate / Insert teacher
  $('#validateTeacher').on("click", function(){
    var data = [];

    // if(ifFirstTimer) {
    //   // alert("First Timer");
    //   var fname = $('#t_fname').val();
    //   var lname = $('#t_lname').val();
    //
    //   data.push(fname);
    //   data.push(lname);
    //
    //   console.log(fname+ " " +lname);
    // } else {
    //   // alert("Frequent");
      var id = $('#t_borrower').val();

      data.push(id);



    $.ajax({
      url: "ajaxrequest/validateBorrower.php",
      type: "POST",
      dataType: "json",
      data: {
        data : data
      },
      success: function(returnData) {
        if(returnData != null && returnData != "null" && returnData != 0) {
          // alert("Confirmed!");
          window.location.href = "selectutensils.php";
        } else {
          alert("Teacher is not Registered Yet!");
        }
        console.log(returnData);
      },
      error: function(xhr, status, error) {
        console.log(xhr.responseText);
       }
    });
  });

  // / Insert OTHER
  $('#insertOther').on("click", function(){
    var other = [];


      // alert("First Timer");
      var fname = $('#t_fname').val();
      var lname = $('#t_lname').val();
      var org = $('#t_org').val();

      other.push(fname);
      other.push(lname);
      other.push(org);

      console.log(fname+ " " +lname+ " " +org);



    $.ajax({
      url: "ajaxrequest/validateBorrower.php",
      type: "POST",
      dataType: "json",
      data: {
        other : other
      },
      success: function(returnData) {
        if(returnData != null && returnData != "null" && returnData != 0) {
          alert("Registered");
          window.location.href = "selectutensils.php";
        } else {
          alert("Borrower is not Registered Yet!");
        }
        console.log(returnData);
      },
      error: function(xhr, status, error) {
        console.log(xhr.responseText);
       }
    });
  });

  //Search Student -> Edp encoding
  $('#edpBtb_search').on('click', function(){
    var studid = $('input[name=studID]').val();

    if(!studid.trim())
      alert('Student ID is empty!');
    else {
      $.ajax({
       url: "ajaxrequest/searchStud_encoding.php",
       type: "POST",
       dataType: "json",
       data: {
         id : studid
       },
       success: function(returnData) {
        if(returnData == 0) {
          alert("Student ID: "+studid+" is not registered");
          $('input[type=text]').val('');
        } else {
          $('input[name=firstname]').val(returnData['firstname']);
          $('input[name=lastname]').val(returnData['lastname']);
          $('input[name=category]').val(returnData['category_desc']);
          $('input[name=course]').val(returnData['course']);
        }
         console.log(returnData);
         setEDPCodeList(returnData['stud_category']);
       },
       error: function(xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    }
  });

  function setEDPCodeList(category) {
    var options = "<option>Select EDP Code</option>";
    $.ajax({
      url: "ajaxrequest/searchStud_encoding.php",
      type: "POST",
      dataType: "json",
      data: {
        'category' : category
      },
      success: function(returnData) {
        console.log(returnData);
        $.each(returnData, function(i,v){
          options += '<option value="'+v['edp_id']+'">'+v['edp_code'].toUpperCase()+'</options>';
        });

        $('#edpCode_select').html(options);
      },
      error: function(xhr, status, error) {
        console.log(xhr.responseText);
      }
    });
  }

  //viewrequest.php

    $('#borrowedTable').DataTable();

    if($('.checkItem').prop("disabled"))
      $('#checkAllItems').prop("disabled","disabled");

    $('#checkAllItems').on('click', function(){

      if($(this).prop("checked")) {
          console.log($(this).prop("checked"));
          $('.checkItem').prop("checked",true);
      }
      else {
          console.log($(this).prop("checked"));
        $('.checkItem').prop("checked",false);
      }
    });

    $('#approveBorrow').on('click', function(){

      var itemChecked = [], qtyChecked = [], itemUnChecked = [], qtyUnChecked = [];
      var trxID = $('#transactionID').val();

      $('.checkItem').each(function () {
        if($(this).prop("checked")) {
          //list of checked items
          itemChecked.push($(this).val());
          qtyChecked.push($(this).attr('data-qty'));
        } else {
          // list of unchecked items
          itemUnChecked.push($(this).val());
          qtyUnChecked.push($(this).attr('data-qty'));
        }
      });

      $.ajax({
        url: "ajaxrequest/borrowApprove.php",
        type: "post",
        dataType: "json",
        data: {
          "itemID":itemChecked,
          "itemQty":qtyChecked,
          "itemIDUnchecked":itemUnChecked,
          "itemQtyUnchecked":qtyUnChecked,
          "trxID": trxID
        },
        success: function(returnData) {
          console.log(returnData)
          if(returnData==1){
         alert("Approved Successfully");
         window.location.href = 'viewrequests.php';
       }else {
         alert("No item selected!");
         window.location.href = 'viewrequests.php';
       }

      },
        error: function(xhr, status, error) {
           console.log(xhr.responseText);
         }
      })


    });

    // View Request details
    if(currentLocation.pathname == "/kitchen/viewrequests.php") {
      $('.btn_requestDetails').on('click', function(){
        var id = $(this).attr('data-rowID');
        var borrowerType = $(this).attr('data-borrowerType');
        console.log($(this).attr('data-rowID'));

        $.ajax({
          url: "ajaxrequest/requestDetails.php",
          type: "post",
          dataType: "json",
          data: {
            "id":id,
            'borrowerType':borrowerType
          },
          success: function(returnData) {
            var names = "", i=1, items = "";
            console.log(returnData);

            //populate data here for modal body
            /* Borrower Data */
            $.each(returnData['borrower'], function(index, value){
              names += i + ") "+value['firstname']+" "+value['lastname']+"<br>";
              i++;
            });
            $('#borrowerList').html(names);
            names = "";

            /* Teacher Data */
            // if(returnData['teacher']) {
            //   $.each(returnData['teacher'], function(index, value){
            //     names += value['firstname']+" "+value['lastname']+"<br>";
            //   });
            //   $('#classTeacher').html("Teacher: " + names).removeClass('d-none');
            // }

            /* Items Data */
            $.each(returnData['items'], function(index, value){
              $.each(value, function(index, v){
                items += '<tr>';
                  items += '<td style="display: none;">'+v['id']+'</td>';
                  items += '<td>';
                    items += '<input type="checkbox" name="checkItem" class="checkItem" value="'+v['id']+'" data-qty="'+v['qty']+'" '+(v['status'] == 0 ? "" : "disabled")+'>';
                  items += '</td>';
                  items += '<td>'+v['item_name']+'</td>';
                  items += '<td>'+v['item_desc']+'</td>';
                  items += '<td>'+v['qty']+'</td>';

                  var status = "";
                  if(v['status'] == 0)
                    status = "Pending";
                  else if(v['status'] == 1)
                    status = "Borrowed";
                  else if(v['status'] == 2)
                    status = "Returned";
                  else if(v['status'] == 3)
                    status = "Disapproved";

                  items += '<td>'+status+'</td>';
                items += '</tr>';
              });

            });

            items += '<input type="hidden" name="transactionID" value="'+id+'" id="transactionID">';
            // console.log(items)
            $('#borrowedDetailsTable tbody').html(items);


          },
          error: function(xhr, status, error) {
             console.log(xhr.responseText);
           }
        });
      });
    }

    // Approved request

    if(currentLocation.pathname == "/kitchen/approvedrequests.php") {
      var itemsWithDescrepancyID = [], itemObject = [];
      $('#tbl_approvedRequests').DataTable();

      $('.btn_approveDetails').on('click', function(){
        var items = "", approvedItems = [];
        itemsWithDescrepancyID = [];

        $.ajax({
          url: "ajaxrequest/approveDetails.php",
          type: "post",
          dataType: "json",
          data: {
            "id":$(this).attr('data-rowID')
          },
          success: function(returnData) {
            $.each(returnData, function(index, val){
              approvedItems.push(val['borrow_details_id']);
              itemObject[val['borrow_details_id']] = val;
              items += '<div id="itemDetails_'+val['borrow_details_id']+'">';
                items += 'Item Name: '+val['item_name']+'<br>';
                items += 'Quantity (Borrowed): '+val['qty']+'<br>';
                items += '<input type="checkbox" name="checkbox" class="cb_descrepancy" data-detailsID="'+val['borrow_details_id']+'"> Has Discrepancies ? <br>';
                items += '<div class="selectToggle d-none">';
                  items += '<select name="options" id="descrepancySelect_'+val['borrow_details_id']+'">';
                    items += '<option value="">-- Select --</option>';
                    items += '<option value="1">Lost</option>';
                    items += '<option value="2">Damaged</option>';
                  items += '</select><br>';
                  items += '<input type="number" name="newQty" id="damagedItemQty_'+val['borrow_details_id']+'">';
                items += '</div>';
              items += '</div>';
              items += '<hr>';
            });
            $('#approveDetails .modal-body').html(items);

            console.log(itemObject);

          },
          error: function(xhr, status, error) {
             console.log(xhr.responseText);
           }
        });
      });

      $('#approveDetails .modal-body').on('change', '.cb_descrepancy', function(){
        var element = $('#approveDetails .modal-body #itemDetails_'+$(this).attr('data-detailsID')+' .selectToggle');

        if(this.checked) {
          element.removeClass("d-none");
          itemsWithDescrepancyID.push($(this).attr('data-detailsID'));
        } else {
          element.addClass("d-none");
          var index = itemsWithDescrepancyID.indexOf($(this).attr('data-detailsID'));
          index > -1 ? itemsWithDescrepancyID.splice(index, 1) : '';
          $('#descrepancySelect_'+$(this).attr('data-detailsID')).val('');
          $('#damagedItemQty_'+$(this).attr('data-detailsID')).val('');
        }

        console.log(itemsWithDescrepancyID);

      });

      $('#returnItems').on('click', function(){
        var size = Object.keys(itemsWithDescrepancyID).length;
        var ele_Select = "", ele_damageQty = "";
        var actualQty = 0, booleanFlag = false;
        var itemsWithDescrepancy = [], itemsWithOutDescrepancy = [], itemsWithDescrepancyHolder = [];

        //Check if has descrepancy
        if(size > 0) {
          for (var i = 0; i < itemsWithDescrepancyID.length; i++) {
            ele_Select = $('#descrepancySelect_'+itemsWithDescrepancyID[i]);
            ele_damageQty = $('#damagedItemQty_'+itemsWithDescrepancyID[i]);

            //check input if empty
            if(ele_Select.val().length <= 0 || (ele_damageQty.val().length <= 0 || ele_damageQty.val() == 0)) {
              if(ele_Select.val().length <= 0) {
                ele_Select.focus();
              }
              else {
                ele_damageQty.focus();
              }
              break;

            } else {

              //get the actual qty borrowed
              actualQty = itemObject[itemsWithDescrepancyID[i]][i]['qty'];

              if(ele_damageQty.val() > actualQty) {
                console.log("Input value exceeds the actual borrowed item qty");
                ele_damageQty.focus();
                break;

              } else {
                itemsWithDescrepancyHolder.push(ele_damageQty.val());
                itemsWithDescrepancyHolder.push(ele_Select.val());
                itemsWithDescrepancyHolder.push(itemObject[itemsWithDescrepancyID[i]]);
                itemsWithDescrepancy.push(itemsWithDescrepancyHolder);
                itemsWithDescrepancyHolder = [];
                booleanFlag = true;

              }
            }
          }

          //check if this item has descrepancy
          $('.cb_descrepancy').each(function(){
            if(!this.checked) {
              itemsWithOutDescrepancy.push(itemObject[$(this).attr('data-detailsID')]);
            }
          });

        } else {
          itemsWithOutDescrepancy = itemObject;
          booleanFlag = true;
          ///////char //////
          alert("Success!");
         window.location.href = "approvedrequests.php";

        }

        if(booleanFlag)
          returnItems(itemsWithDescrepancy, itemsWithOutDescrepancy);

        console.log("without: ");
        console.log(itemsWithOutDescrepancy);
        //

        console.log("with: ");
        console.log(itemsWithDescrepancy);

      });

    }

    function returnItems(itemsWithDescrepancy, itemsWithOutDescrepancy) {
      $.ajax({
        url: "ajaxrequest/approveDetails.php",
        type: "post",
        dataType: "json",
        data: {
          "itemsWithDescrepancy":itemsWithDescrepancy,
          "itemsWithOutDescrepancy":itemsWithOutDescrepancy
        },
        success: function(returnData) {

          console.log(returnData);
          // if(returnData == 1) {
          alert("Success!");
         window.location.href = "approvedrequests.php";


        },

        error: function(xhr, status, error) {
           console.log(xhr.responseText);
         }

      });

    }

    // reports.php
    // if(currentLocation.pathname == "/kitchen/reports.php") {
    // }

    // replace.php
    if(currentLocation.pathname == "/kitchen/replace.php") {
      var names = "", i = 1, unreturnedqty = 0, descrepancy_id=0, itemID = 0;
      $('#tbl_forReplacement').DataTable();

      $(".btn_unreturnedItems").on('click', function(){
        descrepancy_id = $(this).attr('data-rowID');
        unreturnedqty = $(this).attr('data-qty');
        itemID = $(this).attr('data-itemID');
        console.log(descrepancy_id);
        $.ajax({
          url: "ajaxrequest/unreturneditems.php",
          type: "post",
          dataType: "json",
          data: {
            "id":descrepancy_id
          },
          success: function(returnData) {
            console.log(returnData);

            //populate data here for modal body
            /* Borrower Data */
            $.each(returnData, function(index, value){
              names +=  value['sfname']+" "+value['slname']+"<br>";
              i++;
              $('#remarks').html(value['remarks']);
            });
            $('#borrowerList').html(names);
            names = "";
          },
          error: function(xhr, status, error) {
             console.log(xhr.responseText);
           }
        });
      });

      $('#returnItems').on('click', function(){
        $.ajax({
          url: "ajaxrequest/unreturneditems.php",
          type: "post",
          dataType: "json",
          data: {
            "rep_id":descrepancy_id,
            "rep_qty":unreturnedqty,
            "itemID":itemID
          },
          success: function(returnData) {
            console.log(returnData);

            if(returnData == 1) {
              alert("Item replaced successfully");
              window.location.href = "replace.php";
            }
          },
          error: function(xhr, status, error) {
             console.log(xhr.responseText);
           }
        });
      });
    }

});
