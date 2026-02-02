var HOME = BASE_URL + 'report/audit/products_backlogs_summary/';

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
  clearErrorByClass('r');

  let h = {
    'fromDate' : $('#fromDate').val(),
    'toDate' : $('#toDate').val(),
    'allChannels' : $('#allChannels').val(),
    'allRole' : $('#allRole').val(),
    'warehouse_code' : $('#warehouse').val(),
    'channels' : [],
    'role' : []
  }

  if( ! isDate(h.fromDate) || ! isDate(h.toDate)) {
    $('#fromDate').hasError();
    $('#toDate').hasError();
    swal("กรุณาระบุวันที่");
    return false;
  }

  if(h.allRole == '0' && $('.role-chk:checked').length == 0) {
    $('#role-modal').modal('show');
    return false;
  }

  if(h.allChannels === '0' && $('.ch-chk:checked').length == 0) {
    $('#channels-modal').modal('show');
    return false;
  }

  if(h.warehouse_code.length == 0) {
    $('#warehouse').hasError();
    return false;
  }

  if(h.allRole == '0' && $('.role-chk:checked').length) {
    $('.role-chk:checked').each(function() {
      h.role.push($(this).val());
    });
  }

  if(h.allChannels == '0' && $('.ch-chk:checked').length) {
    $('.ch-chk:checked').each(function() {
      h.channels.push($(this).val());
    });
  }


  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'POST',
    cache:false,
    data: {
      "data" : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let source = $('#template').html();
          let output = $('#result');

          render(source, ds.data, output);
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  });
}



function doExport(){
  clearErrorByClass('r');

  let h = {
    'fromDate' : $('#fromDate').val(),
    'toDate' : $('#toDate').val(),
    'allChannels' : $('#allChannels').val(),
    'allRole' : $('#allRole').val(),
    'warehouse_code' : $('#warehouse').val(),
    'channels' : [],
    'role' : []
  }

  if( ! isDate(h.fromDate) || ! isDate(h.toDate)) {
    $('#fromDate').hasError();
    $('#toDate').hasError();
    swal("กรุณาระบุวันที่");
    return false;
  }

  if(h.allRole == '0' && $('.role-chk:checked').length == 0) {
    $('#role-modal').modal('show');
    return false;
  }

  if(h.allChannels === '0' && $('.ch-chk:checked').length == 0) {
    $('#channels-modal').modal('show');
    return false;
  }

  if(h.warehouse_code.length == 0) {
    $('#warehouse').hasError();
    return false;
  }

  if(h.allRole == '0' && $('.role-chk:checked').length) {
    $('.role-chk:checked').each(function() {
      h.role.push($(this).val());
    });
  }

  if(h.allChannels == '0' && $('.ch-chk:checked').length) {
    $('.ch-chk:checked').each(function() {
      h.channels.push($(this).val());
    });
  }

  let token = generateUID();
  $('#token').val(token);
  $('#data').val(JSON.stringify(h));
  get_download(token);
  $('#exportForm').submit();
}


function printReport() {
  clearErrorByClass('r');

  let h = {
    'fromDate' : $('#fromDate').val(),
    'toDate' : $('#toDate').val(),
    'allChannels' : $('#allChannels').val(),
    'allRole' : $('#allRole').val(),
    'warehouse_code' : $('#warehouse').val(),
    'channels' : [],
    'role' : []
  }

  if( ! isDate(h.fromDate) || ! isDate(h.toDate)) {
    $('#fromDate').hasError();
    $('#toDate').hasError();
    swal("กรุณาระบุวันที่");
    return false;
  }

  if(h.allRole == '0' && $('.role-chk:checked').length == 0) {
    $('#role-modal').modal('show');
    return false;
  }

  if(h.allChannels === '0' && $('.ch-chk:checked').length == 0) {
    $('#channels-modal').modal('show');
    return false;
  }

  if(h.warehouse_code.length == 0) {
    $('#warehouse').hasError();
    return false;
  }

  if(h.allRole == '0' && $('.role-chk:checked').length) {
    $('.role-chk:checked').each(function() {
      h.role.push($(this).val());
    });
  }

  if(h.allChannels == '0' && $('.ch-chk:checked').length) {
    $('.ch-chk:checked').each(function() {
      h.channels.push($(this).val());
    });
  }

  let width = 800;
  let height = 900;
  let center = ($(document).width() - width) /2;
  let target = HOME + 'print_report';
  let windowProp = `width=${width}, height=${height}, left=${center}, scrollbars=yes`;


  let postForm = document.createElement('form');
  postForm.target = "report";
  postForm.method = "POST";
  postForm.action = target;

  var input = document.createElement("input");
  input.type = "hidden"
  input.name = "data";
  input.value = JSON.stringify(h);
  postForm.appendChild(input);
  document.body.appendChild(postForm);

  window.open(target, "report", windowProp);
  postForm.submit();
  document.body.removeChild(postForm);
}
