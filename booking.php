<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $stmt = $db->prepare("SELECT * from users where id = ?");
	$stmt->bind_param('s', $user);
	$stmt->execute();
	$result = $stmt->get_result();
  $role = 'NORMAL';
  
  if(($row = $result->fetch_assoc()) !== null){
    $role = $row['role_code'];
  }

  $customers = $db->query("SELECT * FROM customers WHERE deleted = '0'");
  $customers2 = $db->query("SELECT * FROM customers WHERE deleted = '0'");
  $supplies = $db->query("SELECT * FROM supplies WHERE deleted = '0'");
  $supplies2 = $db->query("SELECT * FROM supplies WHERE deleted = '0'");
  $users = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $transporters = $db->query("SELECT * FROM transporters WHERE deleted = '0'");
  $vehicles = $db->query("SELECT * FROM vehicles WHERE deleted = '0'");
}
?>

<style>
  @media screen and (min-width: 676px) {
    .modal-dialog {
      max-width: 1800px; /* New width for default modal */
    }
  }
</style>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Booking</h1>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="form-group col-3">
                <label>From Date:</label>
                <div class="input-group date" id="fromDatePicker" data-target-input="nearest">
                  <input type="text" class="form-control datetimepicker-input" data-target="#fromDatePicker" id="fromDate"/>
                  <div class="input-group-append" data-target="#fromDatePicker" data-toggle="datetimepicker">
                  <div class="input-group-text"><i class="fa fa-calendar"></i></div></div>
                </div>
              </div>

              <div class="form-group col-3">
                <label>To Date:</label>
                <div class="input-group date" id="toDatePicker" data-target-input="nearest">
                  <input type="text" class="form-control datetimepicker-input" data-target="#toDatePicker" id="toDate"/>
                  <div class="input-group-append" data-target="#toDatePicker" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>

              <div class="col-3">
                <div class="form-group">
                  <label>Customer</label>
                  <select class="form-control" id="customerNoFilter" name="customerNoFilter">
                    <option value="" selected disabled hidden>Please Select</option>
                    <?php while($rowCustomer2=mysqli_fetch_assoc($customers2)){ ?>
                      <option value="<?=$rowCustomer2['id'] ?>"><?=$rowCustomer2['customer_name'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="col-3">
                <div class="form-group">
                  <label>Partner</label>
                  <select class="form-control" id="suppliesNoFilter" name="suppliesNoFilter">
                    <option value="" selected disabled hidden>Please Select</option>
                    <?php while($rowSupplies22=mysqli_fetch_assoc($supplies2)){ ?>
                      <option value="<?=$rowSupplies22['id'] ?>"><?=$rowSupplies22['supplier_name'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-9"></div>
              <div class="col-3">
                <button type="button" class="btn btn-block bg-gradient-warning btn-sm"  id="filterSearch">
                  <i class="fas fa-search"></i>
                  Search
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header">
            <div class="row">
              <div class="col-9">Booking</div>
              <div class="col-3">
                <button type="button" class="btn btn-block bg-gradient-success btn-sm" id="newBooking">
                  <i class="fas fa-plus"></i>
                  New Booking
                </button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <table id="weightTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
                  <th>Customer</th>
                  <th>From &<br>To</th>
                  <th>Booking <br>Datetime</th>
                  <th>Contact</th>
                  <th>Partner</th>
                  <th>Amount <br>(RM)</th>
                  <th>Driver &<br>Vehicles No.</th>
                  <th></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="extendModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">

      <form role="form" id="extendForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Add New Booking</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
              <div class="form-group">
                <label class="labelStatus">Customer *</label>
                <select class="form-control" id="customerNo" name="customerNo" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowCustomer=mysqli_fetch_assoc($customers)){ ?>
                    <option value="<?=$rowCustomer['id'] ?>"><?=$rowCustomer['customer_name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
              <div class="form-group">
                <label class="labelStatus">Booking Date *</label>
                <div class="input-group date" id="bookingDatePicker" data-target-input="nearest">
                  <input type="text" class="form-control datetimepicker-input" data-target="#bookingDatePicker" id="bookingDate" name="bookingDate" required/>
                  <div class="input-group-append" data-target="#bookingDatePicker" data-toggle="datetimepicker">
                  <div class="input-group-text"><i class="fa fa-calendar"></i></div></div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
              <div class="form-group">
                <label class="labelStatus">Booking Time *</label>
                <div class="input-group date" id="bookingTimePicker" data-target-input="nearest">
                  <input type="text" class="form-control datetimepicker-input" data-target="#bookingTimePicker" id="bookingTime" name="bookingTime" required/>
                  <div class="input-group-append" data-target="#bookingTimePicker" data-toggle="datetimepicker">
                  <div class="input-group-text"><i class="fa fa-clock"></i></div></div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
              <div class="form-group">
                <label>From *</label>
                <textarea class="form-control" id="fromAddress" name="fromAddress" placeholder="Enter your origin address" required></textarea>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
              <div class="form-group">
                <label>To *</label>
                <textarea class="form-control" id="toAddress" name="toAddress" placeholder="Enter your destination address" required></textarea>
              </div>
            </div>
            <div class="form-group col-lg-4 col-md-6 col-sm-12">
              <label>Number of People *</label>
              <input class="form-control" type="number" placeholder="Number of people" id="numberOfPeople" name="numberOfPeople" min="0" step="1" required/>                        
            </div>
          </div>
          <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
              <div class="form-group">
                <label>Passenger Name</label>
                <input class="form-control" type="text" placeholder="Contact Person" id="contactPerson" name="contactPerson"/>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
              <div class="form-group">
                <label>Passenger Contact</label>
                <input class="form-control" type="text" placeholder="Contact Number" id="contactNumber" name="contactNumber"/>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
              <div class="form-group">
                <label class="labelStatus">Partner</label>
                <select class="form-control" id="supplierNo" name="supplierNo">
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowSupplies=mysqli_fetch_assoc($supplies)){ ?>
                    <option value="<?=$rowSupplies['id'] ?>"><?=$rowSupplies['supplier_name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
              <div class="form-group">
                <label>Driver</label>
                <select class="form-control" id="driverNo" name="driverNo">
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowDriver=mysqli_fetch_assoc($transporters)){ ?>
                    <option value="<?=$rowDriver['id'] ?>"><?=$rowDriver['transporter_name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
              <div class="form-group">
                <label>Vehicle</label>
                <select class="form-control" id="vehicleNo" name="vehicleNo">
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowVeh=mysqli_fetch_assoc($vehicles)){ ?>
                    <option value="<?=$rowVeh['id'] ?>"><?=$rowVeh['veh_number'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
              <div class="form-group">
                <label>Amount (RM) * </label>
                <input class="form-control" type="number" placeholder="Amount" id="amount" name="amount" required/>
              </div>
            </div>
          </div>
          <div>
            <div class="col-12">
              <div class="form-group">
                <label>Remark</label>
                <textarea class="form-control" id="remark" name="remark" placeholder="Remark"></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(function () {
  var table = $("#weightTable").DataTable({
    "responsive": true,
    "autoWidth": false,
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'order': [[ 0, 'asc' ]],
    'columnDefs': [ { orderable: false, targets: [0] }],
    'ajax': {
      'url':'php/loadBooking.php'
    },
    'columns': [
      { data: 'customer_name' },
      {
        data: null,
        render: function(data, type, row) {
          if (type === 'display') {
            return 'From: ' + row.from_place + '<br>To: ' + row.to_place;
          }
          
          return row.from_place + ' ' + row.to_place;
        }
      },
      {
        data: null,
        render: function(data, type, row) {
          if (type === 'display') {
            return row.booking_date + '<br>' + row.booking_time;
          }
          
          return row.booking_date + ' ' + row.booking_time;
        }
      },
      {
        data: null,
        render: function(data, type, row) {
          if (type === 'display') {
            return row.contact_person + '<br>' + row.contact_number;
          }

          return row.contact_person + ' ' + row.contact_number;
        }
      },
      { data: 'suplier_name' },
      { data: 'amount' },
      {
        data: null,
        render: function(data, type, row) {
          if (type === 'display') {
            return row.drivers_name + '<br>' + row.vehicles_no;
          }

          return row.drivers_name + ' ' + row.vehicles_no;
        }
      },
      {
        data: 'id',
        render: function (data, type, row) {
          var buttonsHtml = '<div class="row">';
          
          if (row['pickup_datetime'] == null && row['completed_datetime'] == null) {
            buttonsHtml += '<div class="col-3"><button type="button" id="edit' + data + '" onclick="edit(' + data + ')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div>';
            buttonsHtml += '<div class="col-3"><button type="button" id="pickup' + data + '" onclick="picked(' + data + ')" class="btn btn-info btn-sm"><i class="fas fa-car"></i></button></div>';
            buttonsHtml += '<div class="col-3"><button type="button" id="deactivate' + data + '" onclick="deactivate(' + data + ')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div>';
          }
          else if (row['completed_datetime'] == null) {
            buttonsHtml += '<div class="col-3"><button type="button" id="complete' + data + '" onclick="invoice(' + data + ')" class="btn btn-success btn-sm"><i class="fas fa-check"></i></button></div>';
          }

          buttonsHtml += '</div>';
          return buttonsHtml;
        }
      }
    ],
    "rowCallback": function( row, data, index ) {
      //$('td', row).css('background-color', '#E6E6FA');
    },        
  });

  // Add event listener for opening and closing details
  $('#weightTable tbody').on('click', 'td.dt-control', function () {
    var tr = $(this).closest('tr');
    var row = table.row( tr );

    if ( row.child.isShown() ) {
      row.child.hide();
      tr.removeClass('shown');
    }
    else {
      row.child( format(row.data()) ).show();tr.addClass("shown");
    }
  });

  //Date picker
  $('#fromDatePicker').datetimepicker({
      icons: { time: 'far fa-calendar' },
      format: 'DD/MM/YYYY HH:mm:ss A',
      defaultDate: new Date
  });

  $('#toDatePicker').datetimepicker({
      icons: { time: 'far fa-calendar' },
      format: 'DD/MM/YYYY HH:mm:ss A',
      defaultDate: new Date
  });

  $('#bookingDatePicker').datetimepicker({
      icons: { time: 'far fa-calendar' },
      format: 'DD/MM/YYYY',
      defaultDate: new Date
  });

  $('#bookingTimePicker').datetimepicker({
      icons: { time: 'far fa-clock' },
      format: 'HH:mm A',
      defaultDate: new Date
  });

  $.validator.setDefaults({
    submitHandler: function () {
      if($('#extendModal').hasClass('show')){
        $('#spinnerLoading').show();
        $.post('php/booking.php', $('#extendForm').serialize(), function(data){
          var obj = JSON.parse(data); 
          if(obj.status === 'success'){
            $('#extendModal').modal('hide');
            toastr["success"](obj.message, "Success:");
            $('#weightTable').DataTable().ajax.reload();
          }
          else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
          }
          else{
            toastr["error"]("Something wrong when edit", "Failed:");
          }

          $('#spinnerLoading').hide();
        });
      }
    }
  });

  $('#newBooking').on('click', function(){
    $('#extendModal').find('#id').val("");
    $('#extendModal').find('#bookingDate').val(formatDate(new Date));
    $('#extendModal').find('#customerNo').val("");
    $('#extendModal').find('#bookingTime').val(formatTime(new Date));
    $('#extendModal').find('#fromAddress').val("");
    $('#extendModal').find('#toAddress').val("");
    $('#extendModal').find('#numberOfPeople').val("1");
    $('#extendModal').find('#contactPerson').val("");
    $('#extendModal').find('#contactNumber').val("");
    $('#extendModal').find('#supplierNo').val("");
    $('#extendModal').find('#driverNo').val("");
    $('#extendModal').find('#vehicleNo').val("");
    $('#extendModal').find('#amount').val("");
    $('#extendModal').find('#remark').val("");

    $('#extendModal').modal('show');
    
    $('#extendForm').validate({
      errorElement: 'span',
      errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
      }
    });
  });

  $('#filterSearch').on('click', function(){
    //$('#spinnerLoading').show();

    var fromDateValue = '';
    var toDateValue = '';

    if($('#fromDate').val()){
      var convert1 = $('#fromDate').val().replace(", ", " ");
      convert1 = convert1.replace(":", "/");
      convert1 = convert1.replace(":", "/");
      convert1 = convert1.replace(" ", "/");
      convert1 = convert1.replace(" pm", "");
      convert1 = convert1.replace(" am", "");
      convert1 = convert1.replace(" PM", "");
      convert1 = convert1.replace(" AM", "");
      var convert2 = convert1.split("/");
      var date  = new Date(convert2[2], convert2[1] - 1, convert2[0], convert2[3], convert2[4], convert2[5]);
      fromDateValue = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
    }
    
    if($('#toDate').val()){
      var convert3 = $('#toDate').val().replace(", ", " ");
      convert3 = convert3.replace(":", "/");
      convert3 = convert3.replace(":", "/");
      convert3 = convert3.replace(" ", "/");
      convert3 = convert3.replace(" pm", "");
      convert3 = convert3.replace(" am", "");
      convert3 = convert3.replace(" PM", "");
      convert3 = convert3.replace(" AM", "");
      var convert4 = convert3.split("/");
      var date2  = new Date(convert4[2], convert4[1] - 1, convert4[0], convert4[3], convert4[4], convert4[5]);
      toDateValue = date2.getFullYear() + "-" + (date2.getMonth() + 1) + "-" + date2.getDate() + " " + date2.getHours() + ":" + date2.getMinutes() + ":" + date2.getSeconds();
    }

    var suppliesNoFilter = $('#suppliesNoFilter').val() ? $('#suppliesNoFilter').val() : '';
    var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';

    //Destroy the old Datatable
    $("#weightTable").DataTable().clear().destroy();

    //Create new Datatable
    table = $("#weightTable").DataTable({
      "responsive": true,
      "autoWidth": false,
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',
      'searching': false,
      'order': [[ 1, 'asc' ]],
      'columnDefs': [ { orderable: false, targets: [0] }],
      'ajax': {
        'type': 'POST',
        'url':'php/filterBooking.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          supplier: suppliesNoFilter,
          customer: customerNoFilter,
        } 
      },
      'columns': [
        { data: 'customer_name' },
        {
          data: null,
          render: function(data, type, row) {
            if (type === 'display') {
              return 'From: ' + row.from_place + '<br>To: ' + row.to_place;
            }
            
            return row.from_place + ' ' + row.to_place;
          }
        },
        {
          data: null,
          render: function(data, type, row) {
            if (type === 'display') {
              return row.booking_date + '<br>' + row.booking_time;
            }
            
            return row.booking_date + ' ' + row.booking_time;
          }
        },
        {
          data: null,
          render: function(data, type, row) {
            if (type === 'display') {
              return row.contact_person + '<br>' + row.contact_number;
            }

            return row.contact_person + ' ' + row.contact_number;
          }
        },
        { data: 'suplier_name' },
        { data: 'amount' },
        {
          data: null,
          render: function(data, type, row) {
            if (type === 'display') {
              return row.drivers_name + '<br>' + row.vehicles_no;
            }

            return row.drivers_name + ' ' + row.vehicles_no;
          }
        },
        { 
          data: 'id',
          render: function ( data, type, row ) {
            return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data
            +')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="pickup'+data
            +'" onclick="picked('+data+')" class="btn btn-info btn-sm"><i class="fas fa-car"></i></button></div><div class="col-3"><button type="button" id="complete'+data
            +'" onclick="invoice('+data+')" class="btn btn-success btn-sm"><i class="fas fa-check"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data
            +'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
          }
        }
      ],
      "rowCallback": function( row, data, index ) {
        //$('td', row).css('background-color', '#E6E6FA');
        //$('#spinnerLoading').hide();
      },
    });
  });
});

function format (row) {
  var returnString = '<div class="row"><div class="col-md-3"><p>Pickup Methode: '+row.pickup_method+
  '</p></div><div class="col-md-3"><p>Customer Name: '+row.customer_name+
  '</p></div><div class="col-md-3"><p>Pickup Location: '+row.pickup_location+
  '</p></div><div class="col-md-3"><p>Description: '+row.description+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Estimated Ctn: '+row.estimated_ctn+
  '</p></div><div class="col-md-3"><p>Actual Ctn: '+row.actual_ctn+
  '</p></div><div class="col-md-3"><p>Vehicle No: '+row.vehicle_no+
  '</p></div><div class="col-md-3"><p>Col Goods: '+row.col_goods+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Col Chq: '+row.col_chq+
  '</p></div><div class="col-md-3"><p>Form No: '+row.form_no+
  '</p></div><div class="col-md-3"><p>Gate: '+row.gate+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Checker: '+row.name+
  '</p></div><div class="col-md-3"><p>Status: '+row.status+
  '</p></div><div class="col-md-3">';
  
  if(row.status == 'Created'){
    returnString += '<div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" onclick="edit('+row.id+
  ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-danger btn-sm" onclick="deactivate('+row.id+
  ')"><i class="fas fa-trash"></i></button></div><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="picked('+row.id+
  ')"><i class="fas fa-truck"></i></button></div></div></div></div>';
  }
  else if(row.status == 'Picked'){
    returnString +='<div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="invoice('+row.id+
  ')"><i class="fas fa-receipt"></i></button></div></div></div></div>';
  }
  
  
  return returnString;
}

function formatNormal (row) {
  return '<div class="row"><div class="col-md-3"><p>Customer Name: '+row.customer_name+
  '</p></div><div class="col-md-3"><p>Unit Weight: '+row.unit+
  '</p></div><div class="col-md-3"><p>Weight Status: '+row.status+
  '</p></div><div class="col-md-3"><p>MOQ: '+row.moq+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Address: '+row.customer_address+
  '</p></div><div class="col-md-3"><p>Batch No: '+row.batchNo+
  '</p></div><div class="col-md-3"><p>Weight By: '+row.userName+
  '</p></div><div class="col-md-3"><p>Package: '+row.packages+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Lot No: '+row.lots_no+
  '</p></div><div class="col-md-3"><p>Invoice No: '+row.invoiceNo+
  '</p></div><div class="col-md-3"><p>Unit Price: '+row.unitPrice+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Order Weight: '+row.supplyWeight+
  '</p></div><div class="col-md-3"><p>Delivery No: '+row.deliveryNo+
  '</p></div><div class="col-md-3"><p>Total Weight: '+row.totalPrice+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Contact No: '+row.customer_phone+
  '</p></div><div class="col-md-3"><p>Variance Weight: '+row.varianceWeight+
  '</p></div><div class="col-md-3"><p>Purchase No: '+row.purchaseNo+
  '</p></div><div class="col-md-3"><div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="print('+row.id+
  ')"><i class="fas fa-print"></i></button></div><div class="col-3"><button type="button" class="btn btn-success btn-sm" onclick="portrait('+row.id+
  ')"><i class="fas fa-receipt"></i></button></div></div></div></div>'+
  '</div><div class="row"><div class="col-md-3"><p>Remark: '+row.remark+
  '</p></div><div class="col-md-3"><p>% Variance: '+row.variancePerc+
  '</p></div><div class="col-md-3"><p>Transporter: '+row.transporter_name+
  '</p></div></div>';
  ;
}

function edit(id) {
  $('#spinnerLoading').show();
  $.post('php/getBooking.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#extendModal').find('#id').val(obj.message.id);
      $('#extendModal').find('#bookingDate').val(obj.message.booking_date);
      $('#extendModal').find('#customerNo').val(obj.message.customer);
      $('#extendModal').find('#bookingTime').val(obj.message.booking_time);
      $('#extendModal').find('#fromAddress').val(obj.message.from_place);
      $('#extendModal').find('#toAddress').val(obj.message.to_place);
      $('#extendModal').find('#numberOfPeople').val(obj.message.number_of_person);
      $('#extendModal').find('#contactPerson').val(obj.message.contact_person);
      $('#extendModal').find('#contactNumber').val(obj.message.contact_number);
      $('#extendModal').find('#supplierNo').val(obj.message.supplier);
      $('#extendModal').find('#driverNo').val(obj.message.driver);
      $('#extendModal').find('#vehicleNo').val(obj.message.vehicles);
      $('#extendModal').find('#amount').val(obj.message.amount);
      $('#extendModal').find('#remark').val(obj.message.remark);

      $('#extendModal').modal('show');
      $('#extendForm').validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
      });
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when pull data", "Failed:");
    }
    $('#spinnerLoading').hide();
  });
}

function deactivate(id) {
  if (confirm('Are you sure you want to delete this items?')) {
    $('#spinnerLoading').show();
    $.post('php/deleteBooking.php', {userID: id}, function(data){
      var obj = JSON.parse(data);

      if(obj.status === 'success'){
        toastr["success"](obj.message, "Success:");
        $('#weightTable').DataTable().ajax.reload();
      }
      else if(obj.status === 'failed'){
        toastr["error"](obj.message, "Failed:");
      }
      else{
        toastr["error"]("Something wrong when activate", "Failed:");
      }
      $('#spinnerLoading').hide();
    });
  }
}

function picked(id) {
  $('#spinnerLoading').show();
  $.post('php/pickedBooking.php', {userID: id}, function(data){
    var obj = JSON.parse(data);

    if(obj.status === 'success'){
      toastr["success"](obj.message, "Success:");
      $('#weightTable').DataTable().ajax.reload();
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when activate", "Failed:");
    }
    $('#spinnerLoading').hide();
  });
}

function invoice(id) {
  $.post('php/invoiceBooking.php', {userID: id}, function(data){
    var obj = JSON.parse(data);

    if(obj.status === 'success'){
      toastr["success"](obj.message, "Success:");
      $('#weightTable').DataTable().ajax.reload();
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when activate", "Failed:");
    }
    $('#spinnerLoading').hide();
  });
}

function formatDate(originalDate){
  const day = originalDate.getDate().toString().padStart(2, '0');
  const month = (originalDate.getMonth() + 1).toString().padStart(2, '0'); // Months are zero-based
  const year = originalDate.getFullYear();

  const formattedDate = `${day}/${month}/${year}`;

  return formattedDate;
}

function formatTime(originalDate){
  const hours = originalDate.getHours().toString().padStart(2, '0');
  const minutes = originalDate.getMinutes().toString().padStart(2, '0');

  const formattedTime = `${hours}:${minutes} ${originalDate.getHours() < 12 ? 'AM' : 'PM'}`;
  return formattedTime;
}
</script>