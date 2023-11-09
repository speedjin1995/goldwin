<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);

    if ($update_stmt = $db->prepare("SELECT * FROM booking WHERE id=?")) {
        $update_stmt->bind_param('s', $id);
        
        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            echo json_encode(
                array(
                    "status" => "failed",
                    "message" => "Something went wrong"
                )); 
        }
        else{
            $result = $update_stmt->get_result();
            $message = array();
            
            if ($row = $result->fetch_assoc()) {
                $message["id"]=$row['id'];
                $message["from_place"]=$row['from_place'];
                $message["to_place"]=$row['to_place'];
                $message["customer"]=$row['customer'];
                $message["booking_date"] = DateTime::createFromFormat('Y-m-d', $row['booking_date'])->format('d/m/Y');
                $message["booking_time"] = DateTime::createFromFormat('H:i:s', $row['booking_time'])->format('H:i A');
                $message["contact_person"]=$row['contact_person'];
                $message["contact_number"]=$row['contact_number'];
                $message["number_of_person"]=$row['number_of_person'];
                $message["supplier"]=$row['supplier'];
                $message["driver"]=$row['driver'];
                $message["vehicles"]=$row['vehicles'];
                $message["created_datetime"]=$row['created_datetime'];
                $message["pickup_datetime"]=$row['pickup_datetime'];
                $message["completed_datetime"]=$row['completed_datetime'];
            }
            
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => $message
                ));   
        }
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Attribute"
            )); 
}
?>