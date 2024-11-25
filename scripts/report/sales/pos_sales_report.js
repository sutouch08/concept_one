var HOME = BASE_URL + 'report/sales/pos_sales_report/';

function toggleAllShop(option){
  $('#allShop').val(option);
  if(option == 1){
    $('#btn-shop-all').addClass('btn-primary');
    $('#btn-shop-select').removeClass('btn-primary');
    return
  }

  if(option == 0){
    $('#btn-shop-all').removeClass('btn-primary');
    $('#btn-shop-select').addClass('btn-primary');
    $('#shop-modal').modal('show');
  }
}


function toggleAllPos(option){
  $('#allPos').val(option);
  if(option == 1){
    $('#btn-pos-all').addClass('btn-primary');
    $('#btn-pos-select').removeClass('btn-primary');
    return
  }

  if(option == 0){
    $('#btn-pos-all').removeClass('btn-primary');
    $('#btn-pos-select').addClass('btn-primary');
    $('#pos-modal').modal('show');
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
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let billFrom = $('#billFrom').val();
  let billTo = $('#billTo').val();
  let allShop = $('#allShop').val();
  let allPos = $('#allPos').val();
  let uname = $('#uname').val();
  let shopCount = $('.shop-chk:checked').length;
  let posCount = $('.pos-chk:checked').length;
  let shopList = [];
  let posList = [];

  if(allShop == 0 && shopCount == 0) {
    swal("กรุณาระบุจุดขาย");
    return false;
  }

  if(allPos == 0 && posCount == 0) {
    swal("กรุณาระบุเครื่อง POS");
    return false;
  }

  if( ! isDate(fromDate) || ! isDate(toDate)) {
    swal("กรุณาระบุวันที่ให้ถูกต้อง");
    return false;
  }

  if(allShop == 0 && shopCount > 0) {
    $('.shop-chk:checked').each(function() {
      let id = $(this).val();
      shopList.push(id);
    })
  }

  if(allPos == 0 && posCount > 0) {
    $('.pos-chk:checked').each(function() {
      let id = $(this).val();
      posList.push(id);
    })
  }

  let data = {
    'from_date' : fromDate,
    'to_date' : toDate,
    'billFrom' : billFrom,
    'billTo' : billTo,
    'allShop' : allShop,
    'allPos' : allPos,
    'shopList' : shopList,
    'posList' : posList,
    'uname' : uname
  };

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(data)
    },
    success:function(rs) {
      load_out();

      if( isJson(rs)) {
        let ds = JSON.parse(rs);
        let source = $('#report-template').html();
        let output = $('#result');

        render(source, ds, output);
      }
    }
  })

}


function doExport(){
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let billFrom = $('#billFrom').val();
  let billTo = $('#billTo').val();
  let allShop = $('#allShop').val();
  let allPos = $('#allPos').val();
  let uname = $('#uname').val();
  let shopCount = $('.shop-chk:checked').length;
  let posCount = $('.pos-chk:checked').length;
  let shopList = [];
  let posList = [];

  if(allShop == 0 && shopCount == 0) {
    swal("กรุณาระบุจุดขาย");
    return false;
  }

  if(allPos == 0 && posCount == 0) {
    swal("กรุณาระบุเครื่อง POS");
    return false;
  }

  if( ! isDate(fromDate) || ! isDate(toDate)) {
    swal("กรุณาระบุวันที่ให้ถูกต้อง");
    return false;
  }

  var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();

}
