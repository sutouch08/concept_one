var HOME = BASE_URL + 'report/sales/sales_channesl_details/';


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
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  if(!isDate(fromDate) || !isDate(toDate)){
    swal("กรุณาระบุวันที่");
    return false;
  }

  var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();

}
