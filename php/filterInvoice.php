<?php
## Database configuration
require_once 'db_connect.php';

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
//$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = "created_date"; // Column name
$columnSortOrder = "desc"; // asc or desc
$searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $searchQuery .= " and created_date >= '".$_POST['fromDate']."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $searchQuery .= " and created_date <= '".$_POST['toDate']."'";
}

if(isset($_POST['supplier']) && $_POST['supplier'] != null && $_POST['supplier'] != '' && $_POST['supplier'] != '-'){
	$searchQuery .= " and supplier = '".$_POST['supplier']."'";
}

if(isset($_POST['customer']) && $_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
	$searchQuery .= " and customer = '".$_POST['customer']."'";
}

if($searchValue != ''){
  $searchQuery = " AND (passenger like '%".$searchValue."%' 
  OR to_place like '%".$searchValue."%')
  OR from_place like '%".$searchValue."%')";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from invoice WHERE deleted='0'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from invoice WHERE deleted='0'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT * FROM invoice WHERE deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
  $suplier_name = "";

  if($row['supplier']!=null && $row['supplier']!=''){
    $id = $row['supplier'];

    if ($update_stmt = $db->prepare("SELECT supplier_name FROM supplies WHERE id=?")) {
      $update_stmt->bind_param('s', $id);
      
      // Execute the prepared query.
      if ($update_stmt->execute()) {
        $result1 = $update_stmt->get_result();
        
        if ($row1 = $result1->fetch_assoc()) {
          $suplier_name = $row1['supplier_name'];
        }
      }
    }
  }

  $data[] = array( 
    "id"=>$row['id'],
    "from_place"=>$row['from_place'],
    "to_place"=>$row['to_place'],
    "passenger"=>$row['passenger'],
    "supplier"=>$row['supplier'],
    "suplier_name"=>$suplier_name,
    "amount"=>$row['amount'],
    "remark"=>$row['remark'],
    "created_time"=>$row['created_time'],
    "created_date"=>$row['created_date']
  );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
  "query" => $empQuery
);

echo json_encode($response);

?>