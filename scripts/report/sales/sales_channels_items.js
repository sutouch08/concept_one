var HOME = BASE_URL + 'report/audit/order_channesl_items/';


$('#pdFrom').autocomplete({
  source : BASE_URL + 'auto_complete/get_item_code',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    var pdFrom = arr[0];
    $(this).val(pdFrom);
    var pdTo = $('#pdTo').val();
    if(pdTo.length > 0 && pdFrom.length > 0){
      if(pdFrom > pdTo){
        $('#pdTo').val(pdFrom);
        $('#pdFrom').val(pdTo);
      }
    }
  }
});


$('#pdTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_item_code',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    var pdTo = arr[0];
    $(this).val(pdTo);
    var pdFrom = $('#pdFrom').val();
    if(pdTo.length > 0 && pdFrom.length > 0){
      if(pdFrom > pdTo){
        $('#pdTo').val(pdFrom);
        $('#pdFrom').val(pdTo);
      }
    }
  }
})

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


function toggleAllProduct(option){
  $('#allProduct').val(option);
  if(option == 1){
    $('#btn-pd-all').addClass('btn-primary');
    $('#btn-pd-range').removeClass('btn-primary');
    $('#pdFrom').attr('disabled', 'disabled');
    $('#pdTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-pd-all').removeClass('btn-primary');
    $('#btn-pd-range').addClass('btn-primary');
    $('#pdFrom').removeAttr('disabled');
    $('#pdTo').removeAttr('disabled');
    $('#pdFrom').focus();
  }
}



function toggleState(id){
  var state = $('#state-'+id);
  if(state.val() === '0'){
    state.val(1);
    $('#btn-state-'+id).addClass('btn-primary');
    return
  }

  if(state.val() === '1'){
    state.val(0);
    $('#btn-state-'+id).removeClass('btn-primary');
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



function doExport(){
  var allChannels = $('#allChannels').val();
  var countChannels = $('.chk:checked').length;

  var allPayments = $('#allPayments').val();
  var countPayments = $('.pay-chk:checked').length;

  var allWarehouse = $('#allWarehouse').val();
  var countWarehouse = $('.wh-chk:checked').length;

  var allProduct = $('#allProducts').val();
  var pdFrom = $('#pdFrom').val();
  var pdTo = $('#pdTo').val();

  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  if(allChannels === '0' && countChannels === 0){
    swal("กรุณาเลือกช่องทางขาย");
    return false;
  }


  if(allPayments === '0' && countPayments === 0){
    swal("กรุณาเลือกการชำระเงิน");
    return false;
  }


  if(allWarehouse === '0' && countWarehouse === 0){
    swal("กรุณาเลือกคลังสินค้า");
    return false;
  }


  if(allProduct === '0' && (pdFrom.length === 0 || pdTo.length === 0)){
    swal("รายการสินค้าไม่ถูกต้อง");
    return false;
  }

  if(!isDate(fromDate) || !isDate(toDate)){
    swal("กรุณาระบุวันที่");
    return false;
  }

  var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();
}
