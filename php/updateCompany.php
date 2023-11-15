<?php
require_once 'db_connect.php';

if(isset($_POST['name'], $_POST['name_ch'], $_POST['address'])){
	$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
	$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
	$name_ch = $_POST['name_ch'];
	$phone = null;
	$phone2 = null;
	$phone3 = null;
	$email = null;
	$id = '1';

	if($_POST['phone'] != null && $_POST['phone'] != ""){
		$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
	}

	if($_POST['phone2'] != null && $_POST['phone2'] != ""){
		$phone2 = filter_input(INPUT_POST, 'phone2', FILTER_SANITIZE_STRING);
	}

	if($_POST['phone3'] != null && $_POST['phone3'] != ""){
		$phone3 = filter_input(INPUT_POST, 'phone3', FILTER_SANITIZE_STRING);
	}
	
	if($_POST['email'] != null && $_POST['email'] != ""){
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
	}

	if ($stmt2 = $db->prepare("UPDATE companies SET name=?, name_ch=?, address=?, phone=?, phone2=?, phone3=?, email=? WHERE id=?")) {
		$stmt2->bind_param('ssssssss', $name, $name_ch, $address, $phone, $phone3, $phone2, $email, $id);
		
		if($stmt2->execute()){
			$stmt2->close();
			$db->close();
			
			echo json_encode(
				array(
					"status"=> "success", 
					"message"=> "Your company profile is updated successfully!" 
				)
			);
		} else{
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> $stmt->error
				)
			);
		}
	} 
	else{
		echo json_encode(
			array(
				"status"=> "failed", 
				"message"=> "Something went wrong!"
			)
		);
	}
} 
else{
	echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all fields"
        )
    ); 
}
?>
