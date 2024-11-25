var HOME = BASE_URL + 'report/audit/receive_details/';

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

  if(allWarehouse === '0' && countWarehouse === 0){
    swal("กรุณาเลือกคลังสินค้า");
    $('#warehouse-modal').modal('show');
    return false;
  }

  var role = {};

  if(countRole > 0) {
    $('.role-chk').each(function() {
      if($(this).is(':checked')) {
        let name = $(this).val();
        role[name] = name;
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

  if(allWarehouse === '0' && countWarehouse === 0){
    swal("กรุณาเลือกคลังสินค้า");
    $('#warehouse-modal').modal('show');
    return false;
  }

  var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();
}
