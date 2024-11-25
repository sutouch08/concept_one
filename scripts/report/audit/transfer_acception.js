var HOME = BASE_URL + 'report/audit/transfer_acception/';


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

  var allRole = $('#allRole').val();
  var countRole = $('.role-chk:checked').length;

  var isAccept = $('#is_accept').val();

  if(!isDate(fromDate) || !isDate(toDate)){
    swal("กรุณาระบุวันที่");
    return false;
  }

  if(allRole == '0' && countRole === 0) {
    swal("กรุณาเลือกประเภทเอกสาร");
    $('#role-modal').modal('show');
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


  var data = {
    "fromDate" : fromDate,
    "toDate" : toDate,
    "allRole" : allRole,
    "role" : role,
    "is_accept" : isAccept
  };

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'POST',
    cache:false,
    data: {
      "json" : JSON.stringify(data)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let source = $('#template').html();
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
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  var allRole = $('#allRole').val();
  var countRole = $('.role-chk:checked').length;

  var isAccept = $('#is_accept').val();

  if(!isDate(fromDate) || !isDate(toDate)){
    swal("กรุณาระบุวันที่");
    return false;
  }

  if(allRole == '0' && countRole === 0) {
    swal("กรุณาเลือกประเภทเอกสาร");
    $('#role-modal').modal('show');
    return false;
  }

  var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();
}
