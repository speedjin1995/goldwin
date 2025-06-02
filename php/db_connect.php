<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
$db = mysqli_connect("srv909.hstgr.io", "u782565293_goldwin", "Aa@111222333", "u782565293_goldwin");

if(mysqli_connect_errno()){
    echo 'Database connection failed with following errors: ' . mysqli_connect_error();
    die();
}
?>