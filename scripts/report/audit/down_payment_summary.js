var HOME = BASE_URL + 'report/audit/down_payment_summary/';

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
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let shop_id = $('#shop_id').val();
  let pos_id = $('#pos_id').val();
  let group_by = $('#group_by').val();


  if(!isDate(fromDate) || !isDate(toDate)){
    swal("กรุณาระบุวันที่");
    return false;
  }

  var data = {
    "fromDate" : fromDate,
    "toDate" : toDate,
    "shop_id" : shop_id,
    "pos_id" : pos_id,
    "group_by" : group_by
  };

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'GET',
    cache:false,
    data: {
      "filter" : JSON.stringify(data)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let source = group_by == 'doc' ? $('#doc-template').html() : $('#date-template').html();
          let output = $('#result');

          render(source, ds, output);
        }
        else {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error'
          });
        }
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
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();

  if(!isDate(fromDate) || !isDate(toDate)){
    swal("กรุณาระบุวันที่");
    return false;
  }
  
  var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();
}
