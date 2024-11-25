var HOME = BASE_URL + 'report/audit/order_details/';

function toggleAllChannels(option){
  $('#allChannels').val(option);
  if(option == 1){
    $('#btn-channels-all').addClass('btn-primary');
    $('#btn-channels-range').removeClass('btn-primary');
    return
  }

  if(option == 0){
    $('#btn-channels-all').removeClass('btn-primary');
    $('#btn-channels-range').addClass('btn-primary');
    $('#channels-modal').modal('show');
  }
}


function toggleAllPayment(option){
  $('#allPayments').val(option);
  if(option == 1){
    $('#btn-pay-all').addClass('btn-primary');
    $('#btn-pay-range').removeClass('btn-primary');
    return
  }

  if(option == 0){
    $('#btn-pay-all').removeClass('btn-primary');
    $('#btn-pay-range').addClass('btn-primary');
    $('#payment-modal').modal('show');
  }
}


function toggleAllWarehouse(option){
  $('#allWarehouse').val(option);
  if(option == 1){
    $('#btn-wh-all').addClass('btn-primary');
    $('#btn-wh-range').removeClass('btn-primary');
    return
  }

  if(option == 0){
    $('#btn-wh-all').removeClass('btn-primary');
    $('#btn-wh-range').addClass('btn-primary');
    $('#warehouse-modal').modal('show');
  }
}



function toggleAllState(option){
  $('#allState').val(option);

  if(option == 1) {
    $('#btn-st-all').addClass('btn-primary');
    $('#btn-st-range').removeClass('btn-primary');
    return
  }

  if(option == 0) {
    $('#btn-st-all').removeClass('btn-primary');
    $('#btn-st-range').addClass('btn-primary');
    $('#state-modal').modal('show');
  }
}


function toggleAllRole(option){
  $('#allRole').val(option);

  if(option == 1) {
    $('#btn-role-all').addClass('btn-primary');
    $('#btn-role-range').removeClass('btn-primary');
    return
  }

  if(option == 0) {
    $('#btn-role-all').removeClass('btn-primary');
    $('#btn-role-range').addClass('btn-primary');
    $('#role-modal').modal('show');
  }
}


//--- Date picker
$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#fromDate').datepicker('option','maxDate', sd);
  }
});



function getReport() {
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  var allChannels = $('#allChannels').val();
  var countChannels = $('.ch-chk:checked').length;

  var allPayments = $('#allPayments').val();
  var countPayments = $('.pay-chk:checked').length;

  var allWarehouse = $('#allWarehouse').val();
  var countWarehouse = $('.wh-chk:checked').length;

  var allState = $('#allState').val();
  var countState = $('.st-chk:checked').length;

  var allRole = $('#allRole').val();
  var countRole = $('.role-chk:checked').length;

  var isExpired = $('#is_expired').val();

  if(!isDate(fromDate) || !isDate(toDate)){
    swal("กรุณาระบุวันที่");
    return false;
  }

  if(allRole == '0' && countRole === 0) {
    swal("กรุณาเลือกประเภทเอกสาร");
    $('#role-modal').modal('show');
    return false;
  }

  if(allState == '0' && countState === 0) {
    swal("กรุณาเลือกสถานะเอกสาร");
    $('#state-modal').modal('show');
    return false;
  }

  if(allChannels === '0' && countChannels === 0){
    swal("กรุณาเลือกช่องทางขาย");
    $('#channels-modal').modal('show');
    return false;
  }


  if(allPayments === '0' && countPayments === 0){
    swal("กรุณาเลือกการชำระเงิน");
    $('#payment-modal').modal('show');
    return false;
  }


  if(allWarehouse === '0' && countWarehouse === 0){
    swal("กรุณาเลือกคลังสินค้า");
    $('#warehouse-modal').modal('show');
    return false;
  }

  var role = [];

  if(countRole > 0) {
    $('.role-chk').each(function() {
      if($(this).is(':checked')) {
        role.push($(this).val());
      }
    });
  }

  var state = [];

  if(countState > 0) {
    $('.st-chk').each(function() {
      if($(this).is(':checked')) {
        state.push($(this).val());
      }
    });
  }

  var channels = [];

  if(countChannels > 0) {
    $('.ch-chk').each(function() {
      if($(this).is(':checked')) {
        channels.push($(this).val());
      }
    });
  }

  var payment = [];

  if(countPayments > 0) {
    $('.pay-chk').each(function() {
      if($(this).is(':checked')) {
        payment.push($(this).val());
      }
    });
  }

  var warehouse = [];

  if(countWarehouse > 0) {
    $('.wh-chk').each(function() {
      if($(this).is(':checked')) {
        warehouse.push($(this).val());
      }
    });
  }

  var data = {
    "fromDate" : fromDate,
    "toDate" : toDate,
    "allRole" : allRole,
    "role" : role,
    "isExpired" : isExpired,
    "allState" : allState,
    "state" : state,
    "allChannels" : allChannels,
    "channels" : channels,
    "allPayments" : allPayments,
    "payment" : payment,
    "allWarehouse" : allWarehouse,
    "warehouse" : warehouse
  };

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'GET',
    cache:false,
    data: {
      "json" : JSON.stringify(data)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = $.parseJSON(rs);
        let text = "";
        if(ds.rows > 0) {
          text = "ผลลัพธ์ทั้งหมด "+addCommas(ds.rows)+" รายการ";
          if(ds.rows > ds.limit) {
            text += "  แสดง "+addCommas(ds.limit)+" รายการ หากต้องการดูผลลัพธ์ทั้งหมด กรุณาส่งออกเป็น excel";
          }
        }

        $('#row-result').text(text);

        let source = $('#template').html();
        let output = $('#result');

        render(source, ds, output);
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}



function doExport(){
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  var allChannels = $('#allChannels').val();
  var countChannels = $('.ch-chk:checked').length;

  var allPayments = $('#allPayments').val();
  var countPayments = $('.pay-chk:checked').length;

  var allWarehouse = $('#allWarehouse').val();
  var countWarehouse = $('.wh-chk:checked').length;

  var allState = $('#allState').val();
  var countState = $('.st-chk:checked').length;

  var allRole = $('#allRole').val();
  var countRole = $('.role-chk:checked').length;

  var isExpired = $('#is_expired').val();

  if(!isDate(fromDate) || !isDate(toDate)){
    swal("กรุณาระบุวันที่");
    return false;
  }

  if(allRole == '0' && countRole === 0) {
    swal("กรุณาเลือกประเภทเอกสาร");
    $('#role-modal').modal('show');
    return false;
  }

  if(allState == '0' && countState === 0) {
    swal("กรุณาเลือกสถานะเอกสาร");
    $('#state-modal').modal('show');
    return false;
  }

  if(allChannels === '0' && countChannels === 0){
    swal("กรุณาเลือกช่องทางขาย");
    $('#channels-modal').modal('show');
    return false;
  }


  if(allPayments === '0' && countPayments === 0){
    swal("กรุณาเลือกการชำระเงิน");
    $('#payment-modal').modal('show');
    return false;
  }


  if(allWarehouse === '0' && countWarehouse === 0){
    swal("กรุณาเลือกคลังสินค้า");
    $('#warehouse-modal').modal('show');
    return false;
  }

  var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();
}
