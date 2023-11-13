<?php
## Database configuration
require_once 'db_connect.php';

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = " ";

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
  $fromDate = new DateTime($_POST['fromDate']);
  $fromDateTime = date_format($fromDate,"Y-m-d H:i:s");
  $searchQuery = " and booking.created_datetime >= '".$fromDateTime."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
  $toDate = new DateTime($_POST['toDate']);
  $toDateTime = date_format($toDate,"Y-m-d H:i:s");
	$searchQuery .= " and booking.created_datetime <= '".$toDateTime."'";
}

if($_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
	$searchQuery .= " and booking.customer = '".$_POST['customer']."'";
}

if($_POST['supplier'] != null && $_POST['supplier'] != '' && $_POST['supplier'] != '-'){
	$searchQuery .= " and booking.supplier = '".$_POST['supplier']."'";
}

if($searchValue != ''){
  $searchQuery = " AND (customers.customer_name like '%".$searchValue."%' 
  OR supplies.supplier_name like '%".$searchValue."%'
  OR booking.to_place like '%".$searchValue."%')
  OR booking.from_place like '%".$searchValue."%')";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from booking, customers WHERE booking.deleted='0' AND booking.customer=customers.id");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from booking, customers WHERE booking.deleted='0' AND booking.customer=customers.id".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select booking.*, customers.customer_name from booking, customers WHERE booking.deleted='0' AND booking.customer=customers.id".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
  $suplier_name = "";
  $vehicles_no = "";
  $drivers_name = "";

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

  if($row['driver']!=null && $row['driver']!=''){
    $id = $row['driver'];

    if ($update_stmt2 = $db->prepare("SELECT transporter_name FROM transporters WHERE id=?")) {
      $update_stmt2->bind_param('s', $id);
      
      // Execute the prepared query.
      if ($update_stmt2->execute()) {
        $result2 = $update_stmt2->get_result();
        
        if ($row2 = $result2->fetch_assoc()) {
          $drivers_name = $row2['transporter_name'];
        }
      }
    }
  }

  if($row['vehicles']!=null && $row['vehicles']!=''){
    $id = $row['vehicles'];

    if ($update_stmt3 = $db->prepare("SELECT veh_number FROM vehicles WHERE id=?")) {
      $update_stmt3->bind_param('s', $id);
      
      // Execute the prepared query.
      if ($update_stmt3->execute()) {
        $result3 = $update_stmt3->get_result();
        
        if ($row3 = $result3->fetch_assoc()) {
          $vehicles_no = $row3['veh_number'];
        }
      }
    }
  }

  $data[] = array( 
    "id"=>$row['id'],
    "from_place"=>$row['from_place'],
    "to_place"=>$row['to_place'],
    "customer"=>$row['customer'],
    "customer_name"=>$row['customer_name'],
    "booking_date"=>$row['booking_date'],
    "booking_time"=>$row['booking_time'],
    "contact_person"=>$row['contact_person'],
    "contact_number"=>$row['contact_number'],
    "number_of_person"=>$row['number_of_person'],
    "supplier"=>$row['supplier'],
    "suplier_name"=>$suplier_name,
    "driver"=>$row['driver'],
    "drivers_name"=>$drivers_name,
    "vehicles"=>$row['vehicles'],
    "vehicles_no"=>$vehicles_no,
    "amount"=>$row['amount'],
    "created_datetime"=>$row['created_datetime'],
    "pickup_datetime"=>$row['pickup_datetime'],
    "completed_datetime"=>$row['completed_datetime']
  );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);

?>