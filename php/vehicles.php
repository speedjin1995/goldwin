<?php
require_once "db_connect.php";

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "../login.html";</script>';
}

if(isset($_POST['vehicleNumber'], $_POST['driver'])){
    $vehicleNumber = filter_input(INPUT_POST, 'vehicleNumber', FILTER_SANITIZE_STRING);
    $driver = filter_input(INPUT_POST, 'driver', FILTER_SANITIZE_STRING);
    $attendance1 = null;
    $attendance2 = null;

    if($_POST['attendance1'] != null && $_POST['attendance1'] != ''){
        $attendance1 = filter_input(INPUT_POST, 'attendance1', FILTER_SANITIZE_STRING);
    }

    if($_POST['attendance2'] != null && $_POST['attendance2'] != ''){
        $attendance2 = filter_input(INPUT_POST, 'attendance2', FILTER_SANITIZE_STRING);
    }
    

    if($_POST['id'] != null && $_POST['id'] != ''){
        if ($update_stmt = $db->prepare("UPDATE vehicles SET veh_number=?, driver=?, attandence_1=?, attandence_2=? WHERE id=?")) {
            $update_stmt->bind_param('sssss', $vehicleNumber, $driver, $attendance1, $attendance2, $_POST['id']);
            
            // Execute the prepared query.
            if (! $update_stmt->execute()) {
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
    }
    else{
        if ($insert_stmt = $db->prepare("INSERT INTO vehicles (veh_number, driver, attandence_1, attandence_2) VALUES (?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssss', $vehicleNumber, $driver, $attendance1, $attendance2);
            
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
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