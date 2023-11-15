<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
	echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
}

if(isset($_POST['userID'])){
	$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
	$del = date("Y-m-d H:i:s");
	$createdDate = date("Y-m-d");
	$createdTime = date("H:i:s");

	$from = '';
	$to = '';
	$amount = '0.00';
	$passenger = '';
	$customer = null;
	$supplier = null;
	$remark = null;
	$uid = $_SESSION['userID'];

	if ($update_stmt = $db->prepare("SELECT * FROM booking WHERE id=?")) {
		$update_stmt->bind_param('s', $id);
        
        // Execute the prepared query.
        if ($update_stmt->execute()) {
            $result2 = $update_stmt->get_result();
            
            if ($row2 = $result2->fetch_assoc()) {
                $from = $row2['from_place'];
				$to = $row2['to_place'];
				$amount = $row2['amount'];
				$customer = $row2['customer'];
				$passenger = $row2['contact_person'];

				if($row2['supplier'] != null && $row2['supplier'] != ''){
					$supplier = $row2['supplier'];
				}

				if($row2['remark'] != null && $row2['remark'] != ''){
					$remark = $row2['remark'];
				}
            }
        }

		$update_stmt->close();
	}

	if ($stmt2 = $db->prepare("UPDATE booking SET completed_datetime=? WHERE id=?")) {
		$stmt2->bind_param('ss', $del, $id);
		
		if($stmt2->execute()){
			$stmt2->close();
			if ($insert_stmt = $db->prepare("INSERT INTO invoice (supplier, customer, amount, created_time, created_date, passenger, from_place, to_place, remark) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
				$insert_stmt->bind_param('sssssssss', $supplier, $customer, $amount, $createdTime, $createdDate, $passenger, $from, $to, $remark);
				
				if(!$insert_stmt->execute()){
					echo json_encode(
						array(
							"status"=> "failed", 
							"message"=> $insert_stmt->error
						)
					);
				}
				else{
					$insert_stmt->close();
					$db->close();

					echo json_encode(
						array(
							"status"=> "success", 
							"message"=> "Completed"
						)
					);
				}
			}
			else{
				echo json_encode(
					array(
						"status"=> "failed", 
						"message"=> "Failed to create invoice"
					)
				);
			}
		} else{
		    echo json_encode(
    	        array(
    	            "status"=> "failed", 
    	            "message"=> $stmt2->error
    	        )
    	    );
		}
	} 
	else{
	    echo json_encode(
	        array(
	            "status"=> "failed", 
	            "message"=> "Somthings wrong"
	        )
	    );
	}
} 
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    ); 
}
?>
