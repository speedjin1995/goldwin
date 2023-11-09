<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if(isset($_POST['bookingDate'], $_POST['customerNo'], $_POST['bookingTime'], $_POST['fromAddress'], $_POST['toAddress'], $_POST['numberOfPeople'])){
	$userId = $_SESSION['userID'];
	$bookingDate = filter_input(INPUT_POST, 'bookingDate', FILTER_SANITIZE_STRING);
	$customerNo = filter_input(INPUT_POST, 'customerNo', FILTER_SANITIZE_STRING);
	$bookingTime = filter_input(INPUT_POST, 'bookingTime', FILTER_SANITIZE_STRING);
	$fromAddress = filter_input(INPUT_POST, 'fromAddress', FILTER_SANITIZE_STRING);
	$toAddress = filter_input(INPUT_POST, 'toAddress', FILTER_SANITIZE_STRING);
	$numberOfPeople = filter_input(INPUT_POST, 'numberOfPeople', FILTER_SANITIZE_STRING);

	$contactPerson = null;
	$contactNumber = null;
	$supplierNo = null;
	$driverNo = null;
	$vehicleNo = null;
	$dateTime2 = DateTime::createFromFormat('H:i A', $bookingTime);
	$formattedTime = $dateTime2->format('H:i:s');
	$dateTime = DateTime::createFromFormat('d/m/Y', $bookingDate);
	$formattedDate = $dateTime->format('Y-m-d');

	if(isset($_POST['contactPerson']) && $_POST['contactPerson'] != null && $_POST['contactPerson'] != ''){
		$contactPerson = filter_input(INPUT_POST, 'contactPerson', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['contactNumber']) && $_POST['contactNumber'] != null && $_POST['contactNumber'] != ''){
		$contactNumber = filter_input(INPUT_POST, 'contactNumber', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['supplierNo']) && $_POST['supplierNo'] != null && $_POST['supplierNo'] != ''){
		$supplierNo = filter_input(INPUT_POST, 'supplierNo', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['driverNo']) && $_POST['driverNo'] != null && $_POST['driverNo'] != ''){
		$driverNo = filter_input(INPUT_POST, 'driverNo', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['vehicleNo']) && $_POST['vehicleNo'] != null && $_POST['vehicleNo'] != ''){
		$vehicleNo = filter_input(INPUT_POST, 'vehicleNo', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		if ($update_stmt = $db->prepare("UPDATE booking SET from_place=?, to_place=?, customer=?, booking_date=?, booking_time=?
		, contact_person=?, contact_number=?, number_of_person=?, supplier=?, driver=?, vehicles=? WHERE id=?")){
			$update_stmt->bind_param('ssssssssssss', $fromAddress, $toAddress, $customerNo, $formattedDate, $formattedTime, 
			$contactPerson, $contactNumber, $numberOfPeople, $supplierNo, $driverNo, $vehicleNo, $_POST['id']);
		
			// Execute the prepared query.
			if (! $update_stmt->execute()){
				echo json_encode(
					array(
						"status"=> "failed", 
						"message"=> $update_stmt->error
					)
				);
			} 
			else{
				$update_stmt->close();
				$db->close();
				
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Updated Successfully!!" 
					)
				);
			}
		}
		else{
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> $insert_stmt->error
				)
			);
		}
	}
	else{
		if ($insert_stmt = $db->prepare("INSERT INTO booking (from_place, to_place, customer, booking_date, booking_time
		, contact_person, contact_number, number_of_person, supplier, driver, vehicles) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
			$insert_stmt->bind_param('sssssssssss', $fromAddress, $toAddress, $customerNo, $formattedDate, $formattedTime, 
			$contactPerson, $contactNumber, $numberOfPeople, $supplierNo, $driverNo, $vehicleNo);
			
			// Execute the prepared query.
			if (! $insert_stmt->execute()){
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
						"message"=> "Added Successfully!!" 
					)
				);
			}
		}
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