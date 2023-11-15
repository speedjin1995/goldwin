<?php

require_once 'db_connect.php';

$compids = '1';
$compchinese = '雙 赢 載 送 服 務';
$compname = 'GOLD WIN TRANSPORTATION SERVICE';
$compaddress = 'No.44,Jalan Nusa Bestari 3/1,Taman Nusa Bestari,81300 Skudai,Johor,Malaysia.';
$compphone = '07-232 1825';
$comphp = '(+6) 016-787 2226';
$compphone2 = '(+65) 9085 2226';
$compiemail = 'goldwintransport@hotmail.com';

$message2 = array();
$checker = array();
$searchQuery = " ";
$currentDate = date('j F Y'); 

if($_POST['fromDate'] != null && $_POST['fromDate'] != ''){
    $searchQuery .= " and invoice.created_date >= '".$_POST['fromDate']."'";
}

if($_POST['toDate'] != null && $_POST['toDate'] != ''){
    $searchQuery .= " and invoice.created_date <= '".$_POST['toDate']."'";
}

if(isset($_POST['supplier']) && $_POST['supplier'] != null && $_POST['supplier'] != '' && $_POST['supplier'] != '-'){
    $searchQuery .= " and invoice.supplier = '".$_POST['supplier']."'";
}

if(isset($_POST['customer']) && $_POST['customer'] != null && $_POST['customer'] != '' && $_POST['customer'] != '-'){
    $searchQuery .= " and invoice.customer = '".$_POST['customer']."'";
}


$stmt = $db->prepare("SELECT * FROM companies WHERE id=?");
$stmt->bind_param('s', $compids);
$stmt->execute();
$result1 = $stmt->get_result();
$id = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_STRING);
        
if ($row = $result1->fetch_assoc()) {
    $compname = $row['name'];
    $compchinese = $row['name_ch'];
    $compaddress = $row['address'];
    $compphone = $row['phone'];
    $comphp = $row['phone2'];
    $compphone2 = $row['phone3'];
    $compiemail = $row['email'];
}

if ($select_stmt = $db->prepare("SELECT invoice.*, customers.customer_name FROM invoice, customers WHERE invoice.deleted = '0' AND invoice.customer = customers.id".$searchQuery)) {
    if (! $select_stmt->execute()) {
        echo json_encode(
            array(
                "status" => "failed",
                "message" => $select_stmt->error
            )); 
    }
    else{
        $result = $select_stmt->get_result();
            
        while($row = $result->fetch_assoc()) {
            if(!in_array($row['customer_name'], $checker)){
                $message2[] = array( 
                    'customer_name' => $row['customer_name'],
                    'invoices' => array()
                );

                array_push($checker, $row['customer_name']);
            }

            $key = array_search($row['customer_name'], $checker);
            $message2[$key]['invoices'][] = array( 
                'customer_name' => $row['customer_name'],
                'customer' => $row['customer'],
                'supplier' => $row['supplier'],
                'amount' => $row['amount'],
                'created_time' => $row['created_time'],
                'created_date' => $row['created_date'],
                'passenger' => $row['passenger'],
                'from_place' => $row['from_place'],
                'to_place' => $row['to_place'],
                'remark' => $row['remark'],
            );
        }

        $message = '<html>
                        <head>
                            <style>
                                @media print {
                                    @page {
                                        margin-left: 0.5in;
                                        margin-right: 0.5in;
                                        margin-top: 0.1in;
                                        margin-bottom: 0.1in;
                                    }
                                    
                                    thead {
                                        -webkit-print-color-adjust: exact; /* For Chrome and Safari */
                                        color-adjust: exact; /* Standard property for other browsers */
                                    }
                                }

                                .container {
                                    border: 2px solid #010101; /* Border color */
                                    border-radius: 20px; /* Adjust the radius as needed */
                                    padding: 20px;
                                    width: 93%;
                                    margin: auto;
                                    margin-top: 20px;
                                    height: 90vh;
                                }
                                        
                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    
                                } 
                                
                                .table th, .table td {
                                    padding: 0.70rem;
                                    vertical-align: top;
                                    border-top: 1px solid #dee2e6;
                                    
                                } 
                                
                                .table-bordered {
                                    border: 1px solid #000000;
                                    
                                } 
                                
                                .table-bordered th, .table-bordered td {
                                    border: 1px solid #000000;
                                    font-family: sans-serif;
                                    font-size: 12px;
                                    
                                } 
                                
                                .row {
                                    display: flex;
                                    flex-wrap: wrap;
                                    margin-top: 20px;
                                    margin-right: -15px;
                                    margin-left: -15px;
                                    
                                } 
                                
                                .col-md-4{
                                    position: relative;
                                    width: 33.333333%;
                                }

                                .center {
                                    text-align: center;
                                }

                                .custom-margin p {
                                    margin: 5px 0;
                                }

                                .bank-info {
                                    margin-top: 20px;
                                }
                            </style>
                        </head><body>';
                        
                            

        for($i=0; $i<count($message2); $i++){
            $message .= '<div class="container">
                <div class="center custom-margin">
                    <p>'.$compchinese.'</p>
                    <p><span>'.$compname.' &nbsp;</span><span style="font-size: 10px;"> &nbsp;(JM 0571025-T)</span></p>
                    <p>'.$compaddress.'</p>
                    <p>Tel: '.$compphone.' | H/p: '.$comphp.' / '.$compphone2.' | Email: '.$compiemail.'</p>
                </div><br><br><table>
                <tbody>
                    <tr>
                        <td style="width:50%;">DATE OF INVOICE : '.$currentDate.'</td>
                        <td style="width:50%;float: right;">INVOICE No : 2655</td>
                    </tr>
                    <tr>
                        <td style="width:50%;"><span>BILL TO :</span><span style="font-weight: bold;"> '.$message2[$i]['customer_name'].'</span></td>
                        <td style="width:50%;float: right;"></td>
                    </tr>
                </tbody>
            </table><br><br><table class="table-bordered">
            <thead style="background-color: #c3c0c0;">
                <tr>
                    <th>NO.</th>
                    <th>DATE</th>
                    <th>TIME</th>
                    <th>PASSENGER NAME</th>
                    <th>FROM</th>
                    <th>TO</th>
                    <th>AMOUNT</th>
                </tr>
            </thead><tbody>';

            $total = 0;
            
            foreach ($message2[$i]['invoices'] as $index => $invoice) {
                $dateString = $invoice['created_date'];
                $dateTime = new DateTime($dateString);
                $formattedDate = $dateTime->format('d/m/Y');

                $timeString = $invoice['created_time'];
                $dateTime2 = DateTime::createFromFormat('H:i:s', $timeString);
                $formattedTime = $dateTime2->format('h:i a');
                $total += (float)$invoice['amount'];

                $message .= '<tr>
                    <td style="text-align: center;background-color: #c3c0c0;">'.($index + 1).'</td>
                    <td style="text-align: center;">'.$formattedDate.'</td>
                    <td style="text-align: center;">'.$formattedTime.'</td>
                    <td style="text-align: center;">'.$invoice['passenger'].'</td>
                    <td style="text-align: center;">'.$invoice['from_place'].'</td>
                    <td style="text-align: center;">'.$invoice['to_place'].'</td>
                    <td style="text-align: center;">RM '.(string)number_format($invoice['amount'], 2, '.', '').'</td>
                </tr>';
            }

            $message .= '<tfoot>
                        <tr>
                            <th colspan="6" style="text-align: right;">TOTAL AMOUNT &nbsp;&nbsp;</th>
                            <th>RM '.(string)number_format($total, 2, '.', '').'</th>$total
                        </tr>
                    </tfoot>
                </table><br><br><br>
                <div class="bank-info custom-margin">
                    <p>BANK NAME: PUBLIC BANK</p>
                    <p>ACC NAME: GOLD WIN TRANSPORTATION</p>
                    <p>ACC NUMBER: 321-354-6930</p>
                </div>
            </div>';

            if($i < (count($message2)-1)){
                $message .= '<div style="page-break-before: always;"></div>';
            }
        }

        $message .= '</body></html>';

        echo json_encode(
            array(
                "status" => "success",
                "message" => $message
            )
        );
    }
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Something Goes Wrong"
        )
    );
}

?>