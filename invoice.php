<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $role = $_SESSION['role'];
  $users = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $customers = $db->query("SELECT * FROM customers WHERE deleted = '0'");
  $supplies2 = $db->query("SELECT * FROM supplies WHERE deleted = '0'");
}
?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Invoices</h1>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<section class="content">
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
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-9"></div>
              <div class="col-3">
                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="exportInvoices">Export Invoices</button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <table id="tableforPurchase" class="table table-bordered table-striped">
            <thead>
                <tr>
                  <th>Date &<br>Time</th>
                  <th>From &<br>To</th>
                  <th>Passenger</th>
                  <th>Company</th>
                  <th>Amount</th>
                  <th>Remark</th>
                </tr>
              </thead>
            </table>
          </div><!-- /.card-body -->
        </div>
      </div>
    </div>
  </div>
</section><!-- /.content -->

<div class="modal fade" id="purchaseModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form role="form" id="purchaseForm">
        <div class="modal-header">
          <h4 class="modal-title">Create Invoices</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="card card-primary">
              <div class="card-body">
                <!--<input type="hidden" class="form-control" id="id" name="id">
                <input type="hidden" class="form-control" id="purchaseId" name="purchaseId">--->
                <div class="row">
                  <h4>General Informations</h4>
                </div>
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label for="inputJobNo">Invoice Number</label>
                      <input type="text" class="form-control" id="inputInvNo" name="inputInvNo" placeholder="<new>" readonly>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="form-group">
                      <label for="inputJobNo">Customer *</label>
                      <select class="form-control" id="customerNo" name="customerNo" required>
                        <option value="" selected disabled hidden>Please Select</option>
                        <?php while($rowCustomer=mysqli_fetch_assoc($customers)){ ?>
                            <option value="<?=$rowCustomer['id'] ?>"><?=$rowCustomer['customer_name'] ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="form-group">
                      <label>Date</label>
                      <div class="input-group date" id="inputDate" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" id="inputDate" name="inputDate" data-target="#inputDate" />
                        <div class="input-group-append" data-target="#inputDate" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="card card-primary">
              <div class="card-body">
                <div class="row">
                  <h4>Details</h4>
                  <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-row">Add Item</button>
                </div>
                <table style="width: 100%;">
                  <thead>
                    <tr>
                      <th>No.</th>
                      <th>Item</th>
                      <th>Price</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody class="TableId" name="TableId" id="TableId"></tbody>
                  <tfoot><th colspan="2">Total</th><th><input type="text" class="form-control" id="totalAmount" name="totalAmount" placeholder="0.00" readonly></th></tfoot>
                </table>
              </div>
            </div>
          </div><!-- /.container-fluid -->
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" name="submit" id="submitPurchase">Save Change</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<script>
$(function () {
  var table = $("#tableforPurchase").DataTable({
    "responsive": true,
    "autoWidth": false,
    'processing': true,
    'serverSide': true,
    'searching': false,
    'serverMethod': 'post',
    'ordering': false,
    'ajax': {
      'url':'php/loadInvoices.php'
    },
    'columns': [
      {
        data: null,
        render: function(data, type, row) {
          if (type === 'display') {
            return row.created_date + '<br>' + row.created_time;
          }
          
          return row.created_date + ' ' + row.created_time;
        }
      },
      {
        data: null,
        render: function(data, type, row) {
          if (type === 'display') {
            return 'From: ' + row.from_place + '<br>To: ' + row.to_place;
          }
          
          return row.from_place + ' ' + row.to_place;
        }
      },
      { data: 'passenger' },
      { data: 'suplier_name' },
      { data: 'amount' },
      { data: 'remark' },
      /*{
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
      }*/
    ]       
  });

  $('[data-mask]').inputmask();

  $('#fromDatePicker').datetimepicker({
      icons: { time: 'far fa-calendar' },
      format: 'DD/MM/YYYY',
      defaultDate: new Date
  });

  $('#toDatePicker').datetimepicker({
      icons: { time: 'far fa-calendar' },
      format: 'DD/MM/YYYY',
      defaultDate: new Date
  });

  $.validator.setDefaults({
    submitHandler: function () {
      if($('#purchaseModal').hasClass('show')){
        $('#spinnerLoading').show();
        $.post('php/invoice.php', $('#purchaseForm').serialize(), function(data){
          var obj = JSON.parse(data); 
          if(obj.status === 'success'){
            $('#purchaseModal').modal('hide');
            toastr["success"](obj.message, "Success:");
            $('#tableforPurchase').DataTable().ajax.reload();
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

  $('#filterSearch').on('click', function(){
    var fromDateValue = '';
    var toDateValue = '';

    if($('#fromDate').val()){
      var convert1 = $('#fromDate').val();
      var convert2 = convert1.split("/");
      fromDateValue = convert2[2] + "-" + convert2[1] + "-" + convert2[0];
    }
    
    if($('#toDate').val()){
      var convert3 = $('#toDate').val();
      var convert4 = convert3.split("/");
      toDateValue = convert4[2] + "-" + convert4[1] + "-" + convert4[0];
    }

    var suppliesNoFilter = $('#suppliesNoFilter').val() ? $('#suppliesNoFilter').val() : '';

    //Destroy the old Datatable
    $("#tableforPurchase").DataTable().clear().destroy();

    //Create new Datatable
    table = $("#tableforPurchase").DataTable({
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
        'url':'php/filterInvoice.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          supplier: suppliesNoFilter,
        } 
      },
      'columns': [
        {
          data: null,
          render: function(data, type, row) {
            if (type === 'display') {
              return row.created_date + '<br>' + row.created_time;
            }
            
            return row.created_date + ' ' + row.created_time;
          }
        },
        {
          data: null,
          render: function(data, type, row) {
            if (type === 'display') {
              return 'From: ' + row.from_place + '<br>To: ' + row.to_place;
            }
            
            return row.from_place + ' ' + row.to_place;
          }
        },
        { data: 'passenger' },
        { data: 'suplier_name' },
        { data: 'amount' },
        { data: 'remark' },
        /*{
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
        }*/
      ],
      "rowCallback": function( row, data, index ) {
        //$('td', row).css('background-color', '#E6E6FA');
        //$('#spinnerLoading').hide();
      },
    });
  });
});

function simplyShowId(row) {
  //var weightData = JSON.parse(row.route);
  var returnString = '<div class="row"><div class="col-12">' + row.invoice_no + '</div></div><br>';
  returnString += '<div class="row"><div class="col-12">Customer Name: ' + row.customer_name 
  + '</div></div><div class="row"><div class="col-12">Created By: '+ row.name 
  + '</div></div><div class="row"><div class="col-12">Created Datetime: ' + row.created_datetime 
  + '</div></div>';

  return returnString;
}

function simplyShowCreatedDatetime(row) {
  //var weightData = JSON.parse(row.route);
  var returnString = '<div class="row"><div class="col-12">'+row.created_datetime+'</div></div><br>';

  returnString += '<p><small>Action:</small></p>';

  returnString += '<div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="printQuote('+row.id+
  ')"><i class="fas fa-print"></i></button></div><div class="col-3"><button type="button" onclick="cancel('+
  row.id+')" class="btn btn-danger btn-sm"><i class="fas fa fa-times"></i></button></div></div>';

  return returnString;
}

function simplyShowCreatedDatetime2(row) {
  //var weightData = JSON.parse(row.route);
  var returnString = '<div class="row"><div class="col-12">'+row.created_datetime+'</div></div><br>';

  returnString += '<p><small>Action:</small></p>';

  returnString += '<div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="printQuote('+row.id+
  ')"><i class="fas fa-print"></i></button></div></div>';

  return returnString;
}

function details(row) {
  var returnString = "";
  returnString += '<div class="row"><div class="col-8">Items</div><div class="col-4">Amount</div></div><hr>';

  //var itemsData = JSON.parse();

  for(var i=0; i<row.cart.length; i++){
    returnString += '<div class="row"><div class="col-8">' + row.cart[i].items + '</div><div class="col-4">' + parseFloat(row.cart[i].amount).toFixed(2)  + '</div></div>';
  }

  returnString += '<hr><div class="row"><div class="col-8">Total Amount</div><div class="col-4">' + parseFloat(row.total_amount).toFixed(2) + '</div></div><hr>';

  return returnString;
}

function cancel(id) {
  $('#spinnerLoading').show();
  $.post('php/cancelPurchases.php', {purchasesID: id}, function(data){
    var obj = JSON.parse(data); 
    
    if(obj.status === 'success'){
      toastr["success"](obj.message, "Success:");
      $('#tableforPurchase').DataTable().ajax.reload();
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

function printQuote(id) {
  $('#spinnerLoading').show();
  $.post('php/generateReportPurchases.php', {purchasesID: id}, function(data){
    var obj = JSON.parse(data); 
    
    if(obj.status === 'success'){
      var printWindow = window.open('', '', 'height=400,width=800');
      printWindow.document.write(obj.message);
      printWindow.document.close();
      setTimeout(function(){
          printWindow.print();
          printWindow.close();
      }, 1000);
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
</script>