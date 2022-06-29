<?php
  include 'config.php';
  $array = array();

  function getItemForUpdate($trxID, $itmID) {
    global $dbcon;
    global $array;

   $query = mysqli_query($dbcon,"SELECT * FROM borrower_slip_details where borrower_id = $trxID AND utensils_id = $itemID");
   $check = mysqli_fetch_array($query);
   $uID = $check['utensils_id'];
   $sID = $check['storage_id'];
   $array["req_qty"][] = $check;
   $queryString = mysqli_query($dbcon,"SELECT * FROM storage_stocks where utensils_id = $uID and storage_id = $sID");


    $row = mysqli_fetch_array($queryString);
    $array["stock_qty"][] = $row;


    return $array;

}
  // function getAllRequestedItems() {
  //   global $dbcon;
  //   global $array;
  //   $_SESSION['user']['user_id'];
  //   $user = $_SESSION['user']['user_id'];
  //   $queryString = "SELECT
  //                     a.borrower_slip_id as transID,a.group_id as groupID,a.date_requested,a.status,a.storage_id as storageID,
  //                     b.borrower_slip_id,b.utensils_id,b.storage_id,b.qty as itemQty,
  //                     c.utensils_id,c.utensils_name as itemName,c.utensils_cat_id,
  //                     d.utensils_cat_id,d.category,
  //                     e.group_id,e.faculty_id,
  //                     f.group_id,f.user_id,
  //                     g.user_id,g.school_id,g.lname,
  //                     h.storage_id,h.storage_name,h.initials
  //
  //
  //                     from borrower_slip a
  //                     left join borrower_slip_details b on a.borrower_slip_id = b.borrower_slip_id
  //                     left join utensils c on b.utensils_id = c.utensils_id
  //                     left join utensils_category d on c.utensils_cat_id = d.utensils_cat_id
  //                     left join group_table e on a.group_id = e.group_id
  //                     left join group_members f on e.group_id = f.group_id
  //                     left join users g on f.user_id = g.user_id
  //                     left join storage h on b.storage_id = h.storage_id
  //
  //                     WHERE f.user_id = $user AND a.status = 1
  //                     GROUP BY a.borrower_slip_id";
  //
  //   $query = mysqli_query($dbcon, $queryString);
  //
  //   if($query) {
  //     while ($row = mysqli_fetch_array($query)) {
  //       $array[] = $row;
  //     }
  //   }
  //
  //   return $array;
  // }


//   function getBorrowedItemsDetails($trxID, $borrowerType) {
//     global $dbcon;
//     global $array;
//
//     if($borrowerType == 1) {
//       $queryString = "SELECT c.*
//                       FROM borrow a
//                       LEFT JOIN borrower_group b ON a.group_id = b.id
//                       LEFT JOIN students c ON b.borrower_id = c.student_id
//                       WHERE a.id='$trxID'";
//     } elseif($borrowerType == 2) {
//       $queryString = "SELECT c.*
//                       FROM borrow a
//                       LEFT JOIN borrower_group b ON a.group_id = b.id
//                       LEFT JOIN teacher c ON b.borrower_id = c.id
//                       WHERE a.id='$trxID'";
//     }elseif($borrowerType == 3) {
//       $queryString = "SELECT c.*
//                       FROM borrow a
//                       LEFT JOIN borrower_group b ON a.group_id = b.id
//                       LEFT JOIN borrower c ON b.borrower_id = c.borrower_id
//                       WHERE a.id='$trxID'";
//     }
//
//     $query = mysqli_query($dbcon, $queryString);
//
//     if($query) {
//       while($row = mysqli_fetch_array($query)) {
//         $array["borrower"][] = $row;
//       }
//
//       //get Teacher Details
//       if($borrowerType == 1) {
//         $array["teacher"][] = getTeacherForBorrowedTransaction($trxID);
//       }
//
//       //get Borrow Transaction details
//       $array["items"][] = getBorrowedItems($trxID);
//     } else {
//       die(mysqli_error($dbcon));
//     }
//
//     return $array;
//   }
//
//   function getTeacherForBorrowedTransaction($trxID) {
//     global $dbcon;
//     $array = array();
//     $queryString = "SELECT c.*
//                     FROM borrow a
//                     LEFT JOIN edp b ON a.edpID = b.edp_id
//                     LEFT JOIN teacher c ON b.teacher_id = c.id
//                     WHERE a.id='$trxID'";
//
//     $query = mysqli_query($dbcon, $queryString);
//
//     if($query) {
//       $array = mysqli_fetch_array($query);
//     } else {
//       die(mysqli_error($dbcon));
//     }
//
//     return $array;
//   }
//
//   function getBorrowedItems($trxID) {
//     global $dbcon;
//     $array = array();
//
//     $queryString = "SELECT c.id, c.item_name, c.item_desc, b.qty, b.status
//                     FROM borrow a
//                     LEFT JOIN borrow_details b ON a.id = b.borrow_id
//                     LEFT JOIN utensils c ON b.item_id = c.id
//                     WHERE a.id='$trxID'";
//     $query = mysqli_query($dbcon, $queryString);
//
//     if($query) {
//       while($row = mysqli_fetch_array($query)) {
//         $array[] = $row;
//       }
//     } else {
//       $array = die(mysqli_error($dbcon));
//     }
//
//     return $array;
//   }
//
//   function approveBorrowedItems($borrowedItem, $unCheckedItem, $trxID) {
//     global $dbcon;
//     global $array;
//     $itemID = 0;
//     session_start();
//     $staff = $_SESSION['user']['user_id'];
//
//     $query = mysqli_query($dbcon, "UPDATE borrow SET date_approved=NOW(),staff_id='$staff',status=2 WHERE id='$trxID'");
//
//     for ($i=0; $i < count($unCheckedItem); $i++) {
//       $itemID = $unCheckedItem[$i]["id"];
//       $borroweditemQty = $unCheckedItem[$i]["qty"];
//
//       $queryString = "UPDATE borrow_details
//                       SET status = 3
//                       WHERE borrow_id='$trxID' AND item_id='$itemID'";
//       $query = mysqli_query($dbcon, $queryString);
//     }
//
//     for ($i=0; $i < count($borrowedItem); $i++) {
//
//       $itemID = $borrowedItem[$i]["id"];
//       $borroweditemQty = $borrowedItem[$i]["qty"];
//
//       $queryString = "UPDATE borrow_details
//                       SET status = 1
//                       WHERE borrow_id='$trxID' AND item_id='$itemID'";
//       $query = mysqli_query($dbcon, $queryString);
//
//       if($query) {
//         $queryString = "SELECT * FROM utensils WHERE id='$itemID'";
//         $query = mysqli_query($dbcon, $queryString);
//         $row = mysqli_fetch_array($query);
//         if($row['item_qty'] > 0 && $row['item_qty'] >= $borroweditemQty) {
//           $newQty = $row['item_qty'] - $borroweditemQty;
//
//           $queryString = "UPDATE utensils SET item_qty = ".$newQty." WHERE id = ".$itemID;
//           $query = mysqli_query($dbcon, $queryString);
//
//           if($queryString) {
//             $array = 1; //deducted successfully
//           } else {
//             $array = 2; //error on deducting
//           }
//         } else {
//           $array = 3; //insufficient stock
//         }
//       } else{
//         $array = 0;
//       }
//     }
//     return $array;
//   }
//
//   function getAllApprovedTransactions() {
//     global $dbcon;
//     $array = array();
//
//     $queryString = "SELECT * FROM borrow a
//                     LEFT JOIN borrow_details b ON a.id = b.borrow_id
//                     WHERE b.status = 1
//                     GROUP by a.id";
//     $query = mysqli_query($dbcon, $queryString);
//
//     if($query) {
//       while ($row = mysqli_fetch_array($query)) {
//         $array[] = $row;
//       }
//     } else {
//       $array = die(mysqli_error($dbcon));
//     }
//
//     return $array;
//   }
//
//   function getAllApprovedTransactionsItems($trxID) {
//     global $dbcon;
//     $array = array();
//
//     $queryString = "SELECT * FROM borrow_details a
//                     LEFT JOIN utensils b ON a.item_id = b.id
//                     WHERE borrow_id = '$trxID' AND status = 1";
//     $query = mysqli_query($dbcon, $queryString);
//
//     if($query) {
//       while ($row = mysqli_fetch_array($query)) {
//         $array[] = $row;
//       }
//     } else {
//       $array = die(mysqli_error($dbcon));
//     }
//
//     return $array;
//   }
//
//   function returnItems($itemsWithDescrepancy, $itemsWithOutDescrepancy) {
//     global $dbcon;
//     $array = array();
//
//     // Update details and utensils for without descrepancy
//     if(count($itemsWithOutDescrepancy) > 0) {
//       for ($i=0; $i < count($itemsWithOutDescrepancy); $i++) {
//         $queryString = "UPDATE borrow_details
//                         SET status = 2,
//                             date_returned = NOW()
//                         WHERE borrow_details_id='".$itemsWithOutDescrepancy[$i]["id"]."'";
//         $query = mysqli_query($dbcon, $queryString);
//
//         if($query) {
//           $queryString = "SELECT item_qty FROM utensils WHERE id='".$itemsWithOutDescrepancy[$i]["item_id"]."'";
//           $query = mysqli_query($dbcon, $queryString);
//           $row = mysqli_fetch_array($query);
//           $qty = $row['item_qty'] + $itemsWithOutDescrepancy[$i]['returnQty'];
//
//           $queryString = "UPDATE utensils
//                           SET item_qty = '$qty'
//                           WHERE id='".$itemsWithOutDescrepancy[$i]["item_id"]."'";
//           $query = mysqli_query($dbcon, $queryString);
//
//           if($query)
//             $array = 1; //returned successfully
//           else
//             $array = die(mysqli_error($dbcon));
//         }
//       }
//     }
//
//     // Update details and utensils for with descrepancy
//     if(count($itemsWithDescrepancy) > 0) {
//
//         for ($i=0; $i < count($itemsWithDescrepancy); $i++) {
//           $queryString = "UPDATE borrow_details
//                           SET unreturnedqty = '".$itemsWithDescrepancy[$i]["damagedItemQty"]."',
//                               status = 2,
//                               date_returned = NOW(),
//                               item_remarks = '".$itemsWithDescrepancy[$i]["remarks"]."'
//                           WHERE borrow_details_id='".$itemsWithDescrepancy[$i]["id"]."'";
//           $query = mysqli_query($dbcon, $queryString);
//
//           if($query) {
//
//             if($itemsWithDescrepancy[$i]["remarks"] == 1)
//               //$remarks = "Kulang ug ".$itemsWithDescrepancy[$i]['damagedItemQty']." kay nawagtang";
//               $remarks = "Missing";
//             else
//               //$remarks = "Kulang ug ".$itemsWithDescrepancy[$i]['damagedItemQty']." kay naguba";
//               $remarks = "Damaged";
//             $statusdefault ='1';
//             $queryString = "INSERT INTO returneddescrepancies_logs(borrow_details_id, remarks, date_added,status)
//                             VALUES('".$itemsWithDescrepancy[$i]["id"]."','$remarks',NOW(),'$statusdefault')";
//             $query = mysqli_query($dbcon,$queryString);
//
//             if($query) {
//
//               $queryString = "SELECT item_qty FROM utensils WHERE id='".$itemsWithDescrepancy[$i]["item_id"]."'";
//               $query = mysqli_query($dbcon, $queryString);
//               $row = mysqli_fetch_array($query);
//               $qty = $row['item_qty'] + ($itemsWithDescrepancy[$i]['returnQty'] - $itemsWithDescrepancy[$i]['damagedItemQty']);
//
//               $queryString = "UPDATE utensils
//                               SET item_qty = '$qty'
//                               WHERE id='".$itemsWithDescrepancy[$i]["item_id"]."'";
//               $query = mysqli_query($dbcon, $queryString);
//
//               if($query)
//                 $array = 1; //returned successfully
//               else
//                 $array = die(mysqli_error($dbcon));
//
//             } else {
//               $array = die(mysqli_error($dbcon));
//             }
//
//           }
//         }
//
//     }
//     // $array = count($itemsWithOutDescrepancy);
//
//     return $array;
//   }
//
//   function getAllitemsWithDescrepancies() {
//     global $dbcon;
//     $array = array();
//
//     $queryString = "SELECT
//                       a.descrepancy_id, a.date_added,
//                       b.borrow_id, b.item_id,
//                       c.item_name, c.item_desc, b.unreturnedqty
//                     FROM returneddescrepancies_logs a
//                     LEFT JOIN borrow_details b ON a.borrow_details_id = b.borrow_details_id
//                     LEFT JOIN utensils c ON b.item_id = c.id
//                     WHERE a.date_replaced IS NULL and b.date_returned is not null and b.unreturnedqty is not null";
//
//     $query = mysqli_query($dbcon, $queryString);
//
//     if($query) {
//       while($row = mysqli_fetch_array($query)) {
//         $array[] = $row;
//       }
//     } else {
//       $array = die(mysqli_error($dbcon));
//     }
//
//     return $array;
//   }
//
//   function getAllTransactions() {
//     global $dbcon;
//     $array = array();
//
//     $queryString = "SELECT
//                       b.id,
//                       c.student_id, c.firstname as studFName, c.lastname as studLName,
//                       d.firstname as staffFname, d.lastname as staffLname
//                     FROM borrower_group a
//                     LEFT JOIN borrow b ON a.id = b.id
//                     LEFT JOIN students c ON a.borrower_id = c.student_id
//                     LEFT JOIN users d ON b.staff_id = d.user_id";
//     $query = mysqli_query($dbcon, $queryString);
//
//     if($query) {
//       while($row = mysqli_fetch_array($query)) {
//         $array[] = $row;
//       }
//     } else {
//       $array = die(mysqli_error($dbcon));
//     }
//
//     return $array;
//   }
//
//   function getDescrepancyDetails($descrepancyID) {
//     global $dbcon;
//     $array = array();
//
//     $queryString = "SELECT
//                     	a.descrepancy_id, a.remarks, a.date_added,a.borrow_details_id as detail_id,
//                       b.unreturnedqty, b.item_id,
//                       c.borrower_id,
//                       d.firstname as sfname, d.lastname as slname,
//                       e.firstname as tfname, e.lastname as tlname,
//                       f.firstname as oname, f.lastname as olname,
//                       g.borrower_type
//                     FROM returneddescrepancies_logs a
//                     LEFT JOIN borrow_details b ON a.borrow_details_id = b.borrow_details_id
//                     LEFT JOIN borrower_group c ON b.borrow_id = c.id
//                     LEFT JOIN students d ON c.borrower_id = d.student_id
//                     LEFT JOIN teacher e on c.borrower_id = e.id
//                     left join borrower f on c.borrower_id = f.borrower_id
//                     left join borrow g on b.borrow_id = g.id
//                     WHERE a.descrepancy_id = '$descrepancyID'";
//     $query = mysqli_query($dbcon, $queryString);
//
//     if($query) {
//       while ($row = mysqli_fetch_array($query)) {
//         $array[] = $row;
//       }
//     } else {
//       $array = die(mysqli_error($dbcon));
//     }
//
//     return $array;
//   }
//
//   function replaceDescrepancyItem($descrepancyID, $unreturnedQty, $itemID) {
//     global $dbcon;
//     $array = array();
//
//     $queryString = "UPDATE returneddescrepancies_logs SET date_replaced=NOW(),status=2 WHERE descrepancy_id='$descrepancyID'";
//     $query = mysqli_query($dbcon, $queryString);
// //change borrow detail status
//     $samp = mysqli_query($dbcon,"SELECT * FROM returneddescrepancies_logs where descrepancy_id='$descrepancyID'");
//     $samp1 = mysqli_fetch_array($samp);
//
//     $show = $samp1['borrow_details_id'];
//     $detail = "UPDATE borrow_details SET status=3 WHERE borrow_details_id='$show'";
//     $d = mysqli_query($dbcon, $detail);
// //////////////////////////////////////////
//
//     if($query) {
//       $queryString = "SELECT * FROM utensils WHERE id='$itemID'";
//       $query = mysqli_query($dbcon, $queryString);
//
//       if($query) {
//
//         $row = mysqli_fetch_array($query);
//         $qty = $row['item_qty'] + $unreturnedQty;
//
//
//         $queryString = "UPDATE utensils SET item_qty='$qty' WHERE id='$itemID'";
//         $query = mysqli_query($dbcon, $queryString);
//
//         if($query)
//           $array = 1;
//         else
//           $array = die(mysqli_error($dbcon));
//       } else {
//         $array = die(mysqli_error($dbcon));
//       }
//     }
//
//     return $array;
//   }
//
// ?>
