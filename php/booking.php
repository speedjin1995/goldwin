<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if(isset($_POST['bookingDate'], $_POST['bookingTime'], $_POST['fromAddress'], $_POST['toAddress'], $_POST['numberOfPeople'], $_POST['amount'])){
	$userId = $_SESSION['userID'];
	$bookingDate = filter_input(INPUT_POST, 'bookingDate', FILTER_SANITIZE_STRING);
	$bookingTime = filter_input(INPUT_POST, 'bookingTime', FILTER_SANITIZE_STRING);
	$fromAddress = filter_input(INPUT_POST, 'fromAddress', FILTER_SANITIZE_STRING);
	$toAddress = filter_input(INPUT_POST, 'toAddress', FILTER_SANITIZE_STRING);
	$numberOfPeople = filter_input(INPUT_POST, 'numberOfPeople', FILTER_SANITIZE_STRING);
	$amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_STRING);
	$manualCustomer = filter_input(INPUT_POST, 'manualCustomer', FILTER_SANITIZE_STRING);
	$manualDriver = filter_input(INPUT_POST, 'manualDriver', FILTER_SANITIZE_STRING);
	$manualVehicle = filter_input(INPUT_POST, 'manualVehicle', FILTER_SANITIZE_STRING);

	$contactPerson = null;
	$contactNumber = null;
	$supplierNo = null;
	$driverNo = null;
	$vehicleNo = null;
	$remark = null;
	$dateTime2 = DateTime::createFromFormat('H:i A', $bookingTime);
	$formattedTime = $dateTime2->format('H:i:s');
	$dateTime = DateTime::createFromFormat('d/m/Y', $bookingDate);
	$formattedDate = $dateTime->format('Y-m-d');

	if(isset($_POST['customerNo']) && $_POST['customerNo'] != null && $_POST['customerNo'] != ''){
		$customerNo = filter_input(INPUT_POST, 'customerNo', FILTER_SANITIZE_STRING);
	}
	else{
		if ($insert_stmt = $db->prepare("INSERT INTO customers (customer_name, customer_address, customer_phone, pic) VALUES (?, ?, ?, ?)")) {
            $name = $_POST['customerNoTxt'];
			$address = '-';
			$phone = '-';
			$pic = '-';

			$insert_stmt->bind_param('ssss', $name, $address, $phone, $pic);
            $insert_stmt->execute();
			$customerNo = $insert_stmt->insert_id;;
			$insert_stmt->close();
        }
	}

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
	else{
		if($manualDriver != null && $manualDriver != '' && $manualDriver == "1" && $_POST['driverNoTxt'] != null &&  $_POST['driverNoTxt'] != ''){
        	if ($insert_stmtD = $db->prepare("INSERT INTO transporters (transporter_name) VALUES (?)")) {
				$driverName = $_POST['driverNoTxt']; // assign to a variable
				$insert_stmtD->bind_param('s', $driverName);
				$insert_stmtD->execute();
				$driverNo = $insert_stmtD->insert_id;
				$insert_stmtD->close();
			}
		}
	}

	if(isset($_POST['vehicleNo']) && $_POST['vehicleNo'] != null && $_POST['vehicleNo'] != ''){
		$vehicleNo = filter_input(INPUT_POST, 'vehicleNo', FILTER_SANITIZE_STRING);
	}
	else{
		if($manualVehicle != null && $manualVehicle != '' && $manualVehicle == "1" && $_POST['vehicleNoTxt'] != null &&  $_POST['vehicleNoTxt'] != ''){
			if ($insert_stmtV = $db->prepare("INSERT INTO vehicles (veh_number) VALUES (?)")) {
				$vehicleNumber = $_POST['vehicleNoTxt']; // assign to a variable
				$insert_stmtV->bind_param('s', $vehicleNumber);
				$insert_stmtV->execute();
				$vehicleNo = $insert_stmtV->insert_id;
				$insert_stmtV->close();
			}
		}
	}

	if(isset($_POST['remark']) && $_POST['remark'] != null && $_POST['remark'] != ''){
		$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);
	}

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		if ($update_stmt = $db->prepare("UPDATE booking SET from_place=?, to_place=?, customer=?, booking_date=?, booking_time=?
		, contact_person=?, contact_number=?, number_of_person=?, supplier=?, driver=?, vehicles=?, amount=?, remark=? WHERE id=?")){
			$update_stmt->bind_param('ssssssssssssss', $fromAddress, $toAddress, $customerNo, $formattedDate, $formattedTime, 
			$contactPerson, $contactNumber, $numberOfPeople, $supplierNo, $driverNo, $vehicleNo, $amount, $remark, $_POST['id']);
		
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
		, contact_person, contact_number, number_of_person, supplier, driver, vehicles, amount, remark) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
			$insert_stmt->bind_param('sssssssssssss', $fromAddress, $toAddress, $customerNo, $formattedDate, $formattedTime, 
			$contactPerson, $contactNumber, $numberOfPeople, $supplierNo, $driverNo, $vehicleNo, $amount, $remark);
			
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