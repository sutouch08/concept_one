var HOME = BASE_URL + 'report/sales/sales_analyze/';

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



function doExport() {
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
