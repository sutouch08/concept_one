var HOME = BASE_URL + 'report/sales/sales_consignment_report/';

$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#toDate').datepicker("option", "minDate", sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#fromDate').datepicker("option", "maxDate", sd);
  }
});

function toggleAllProduct(option){
  $('#allProduct').val(option);
  if(option == 1){
    $('#btn-pd-all').addClass('btn-primary');
    $('#btn-pd-range').removeClass('btn-primary');
    $('#pdFrom').val('');
    $('#pdFrom').attr('disabled', 'disabled');
    $('#pdTo').val('');
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


$('#pdFrom').autocomplete({
  source : BASE_URL + 'auto_complete/get_style_code',
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
  source:BASE_URL + 'auto_complete/get_style_code',
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


function toggleAllCustomer(option){
  $('#allCustomer').val(option);
  if(option == 1){
    $('#btn-cus-all').addClass('btn-primary');
    $('#btn-cus-range').removeClass('btn-primary');
    $('#cusFrom').val('');
    $('#cusFrom').attr('disabled', 'disabled');
    $('#cusTo').val('');
    $('#cusTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-cus-all').removeClass('btn-primary');
    $('#btn-cus-range').addClass('btn-primary');
    $('#cusFrom').removeAttr('disabled');
    $('#cusTo').removeAttr('disabled');
    $('#cusFrom').focus();
  }
}


$('#cusFrom').autocomplete({
  source : BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    var cusFrom = arr[0];
    $(this).val(cusFrom);
    var cusTo = $('#cusTo').val();
    if(cusTo.length > 0 && cusFrom.length > 0){
      if(cusFrom > cusTo){
        $('#cusTo').val(cusFrom);
        $('#cusFrom').val(cusTo);
      }
    }
  }
});


$('#cusTo').autocomplete({
  source:BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    var cusTo = arr[0];
    $(this).val(cusTo);
    var cusFrom = $('#cusFrom').val();
    if(cusTo.length > 0 && cusFrom.length > 0){
      if(cusFrom > cusTo){
        $('#cusTo').val(cusFrom);
        $('#cusFrom').val(cusTo);
      }
    }
  }
})

function toggleAllWarehouse(option){
  $('#allWarehouse').val(option);
  if(option == 1){
    $('#btn-wh-all').addClass('btn-primary');
    $('#btn-wh-range').removeClass('btn-primary');
    $('.chk').removeAttr('checked');
    return
  }

  if(option == 0){
    $('#btn-wh-all').removeClass('btn-primary');
    $('#btn-wh-range').addClass('btn-primary');
    $('#wh-modal').modal('show');
  }

  zone_init();
}



function toggleAllZone(option){
  $('#allZone').val(option);
  if(option == 1){
    $('#btn-zone-all').addClass('btn-primary');
    $('#btn-zone-range').removeClass('btn-primary');
    $('#zoneCode').val('');
    $('#zoneName').val('');
    $('#zoneName').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-zone-all').removeClass('btn-primary');
    $('#btn-zone-range').addClass('btn-primary');
    $('#zoneName').removeAttr('disabled');
    $('#zoneName').focus();
  }
}


function zone_init(){
  var warehouse = "";
  let i = 0;
  $('.chk').each(function(index, el) {
    if($(this).is(':checked')){
      if(i == 0){
        warehouse = warehouse + $(this).val();
      }
      else{
        warehouse = warehouse + "|"+$(this).val();
      }

      i++;
    }
  });

  if(warehouse.length > 0){
    warehouse = "/"+warehouse;
  }

  $('#zoneName').autocomplete({
    source:BASE_URL + 'auto_complete/get_zone_code_and_name' + warehouse,
    autoFocus:true,
    close:function(){
      var rs = $(this).val();
      var rs = rs.split(' | ');
      if(rs.length == 2){
        $(this).val(rs[1]);
        $('#zoneCode').val(rs[0]);
      }
      else{
        $(this).val('');
        $('#zoneCode').val('');
      }
    }
  })
}


$('.chk').change(function(){
  zone_init();
})





function getReport(){
  var allProduct = $('#allProduct').val();
  var pdFrom = $('#pdFrom').val();
  var pdTo = $('#pdTo').val();
  var allCustomer = $('#allCustomer').val();
  var cusFrom = $('#cusFrom').val();
  var cusTo = $('#cusTo').val();
  var allWhouse = $('#allWarehouse').val();
  var allZone = $('#allZone').val();
  var zoneCode = $('#zoneCode').val();
  var zoneName = $('#zoneName').val();
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  if( ! isDate(fromDate) || ! isDate(toDate))
  {
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if(allProduct == 0) {
    if(pdFrom.length == 0) {
      $('#pdFrom').addClass('has-error');
      return false;
    }
    else {
      $('#pdFrom').removeClass('has-error');
    }

    if(pdTo.length == 0) {
      $('#pdTo').addClass('has-error');
      return false;
    }
    else {
      $('#pdTo').removeClass('has-error');
    }
  }
  else {
    $('#pdFrom').removeClass('has-error');
    $('#pdTo').removeClass('has-error');
  }


  if(allCustomer == 0) {
    if(cusFrom.length == 0) {
      $('#cusFrom').addClass('has-error');
      return false;
    }
    else {
      $('#cusFrom').removeClass('has-error');
    }

    if(cusTo.length == 0) {
      $('#cusTo').addClass('has-error');
      return false;
    }
    else {
      $('#cusTo').removeClass('has-error');
    }
  }
  else {
    $('#cusFrom').removeClass('has-error');
    $('#cusTo').removeClass('has-error');
  }


  if(allWhouse == 0) {
    var count = $('.chk:checked').length;
    console.log(count);
    if(count == 0) {
      $('#wh-modal').modal('show');
      return false;
    }
  }

  if(allZone == 0) {
    if(zoneCode == '' || zoneName == ''){
      $('#zoneName').addClass('has-error');
      return false;
    }
    else {
      $('#zoneName').removeClass('has-error');
    }
  }
  else {
    $('#zoneName').removeClass('has-error');
  }

  var data = [
    {'name' : 'allProduct', 'value' : allProduct},
    {'name' : 'allCustomer', 'value' : allCustomer},
    {'name' : 'allWhouse' , 'value' : allWhouse},
    {'name' : 'allZone' , 'value' : allZone},
    {'name' : 'pdFrom', 'value' : pdFrom},
    {'name' : 'pdTo', 'value' : pdTo},
    {'name' : 'cusFrom', 'value' : cusFrom},
    {'name' : 'cusTo', 'value' : cusTo},
    {'name' : 'zoneCode', 'value' : zoneCode},
    {'name' : 'zoneName', 'value' : zoneName},
    {'name' : 'fromDate', 'value' : fromDate},
    {'name' : 'toDate', 'value' : toDate}
  ];

  if(allWhouse == 0){
    $('.chk').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'warehouse['+$(this).val()+']';
        data.push({'name' : names, 'value' : $(this).val() });
      }
    });
  }

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'GET',
    cache:'false',
    data:data,
    success:function(rs) {
      load_out();
      var rs = $.trim(rs);
      if(isJson(rs)) {
        var source = $('#template').html();
        var data = $.parseJSON(rs);
        var output = $('#rs');
        render(source,  data, output);
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    }
  });

}


function doExport() {
  var allProduct = $('#allProduct').val();
  var pdFrom = $('#pdFrom').val();
  var pdTo = $('#pdTo').val();
  var allCustomer = $('#allCustomer').val();
  var cusFrom = $('#cusFrom').val();
  var cusTo = $('#cusTo').val();
  var allWhouse = $('#allWarehouse').val();
  var allZone = $('#allZone').val();
  var zoneCode = $('#zoneCode').val();
  var zoneName = $('#zoneName').val();
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  if( ! isDate(fromDate) || ! isDate(toDate))
  {
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if(allProduct == 0) {
    if(pdFrom.length == 0) {
      $('#pdFrom').addClass('has-error');
      return false;
    }
    else {
      $('#pdFrom').removeClass('has-error');
    }

    if(pdTo.length == 0) {
      $('#pdTo').addClass('has-error');
      return false;
    }
    else {
      $('#pdTo').removeClass('has-error');
    }
  }
  else {
    $('#pdFrom').removeClass('has-error');
    $('#pdTo').removeClass('has-error');
  }


  if(allCustomer == 0) {
    if(cusFrom.length == 0) {
      $('#cusFrom').addClass('has-error');
      return false;
    }
    else {
      $('#cusFrom').removeClass('has-error');
    }

    if(cusTo.length == 0) {
      $('#cusTo').addClass('has-error');
      return false;
    }
    else {
      $('#cusTo').removeClass('has-error');
    }
  }
  else {
    $('#cusFrom').removeClass('has-error');
    $('#cusTo').removeClass('has-error');
  }


  if(allWhouse == 0) {
    var count = $('.chk:checked').length;
    console.log(count);
    if(count == 0) {
      $('#wh-modal').modal('show');
      return false;
    }
  }

  if(allZone == 0) {
    if(zoneCode == '' || zoneName == ''){
      $('#zoneName').addClass('has-error');
      return false;
    }
    else {
      $('#zoneName').removeClass('has-error');
    }
  }
  else {
    $('#zoneName').removeClass('has-error');
  }


  var token = new Date().getTime();
  $('#token').val(token);

  get_download(token);

  $('#reportForm').submit();
}


$(document).ready(function(){
  zone_init();
})
