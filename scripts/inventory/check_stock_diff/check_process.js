$('#barcode').keyup(function(e){
  if(e.keyCode == 13 && $(this).val() !== ''){
    check_barcode();
  }
});




function check_barcode(){
  var zone = $('#zone-code').val();
  var barcode = $('#barcode').val();
  var qty = parseInt($('#qty').val());
  var isExists = $('[data-barcode="'+barcode+'"]').length;
  var topRow = parseInt($('#topRow').val());

  if(zone.length == 0){
    swal('กรุณาระบุโซน');
    return false;
  }

  if(barcode.length == 0){
    return false;
  }

  if(qty <= 0){
    swal('จำนวนไม่ถูกต้อง');
    return false;
  }

  $('#barcode').attr('disabled', 'disabled');

  if(!isExists){
    load_in();
    $.ajax({
      url:HOME + 'get_item_by_barcode',
      type:'GET',
      cache:false,
      data:{
        'barcode' : barcode,
        'zone_code' : zone,
        'qty' : qty,
        'topRow' : topRow
      },
      success:function(rs){
        load_out();
        if(isJson(rs)){
          var data = $.parseJSON(rs);
          var source = $('#row-template').html();
          var output = $('#stock-table');
          render_append(source, data, output);
          $('#topRow').val(topRow+1);
          cal_diff(data.no);
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })
  }else{
    var input = $('[data-barcode="'+barcode+'"]');
    var id = input.attr('id');
    var arr = id.split('_');
    var no = arr[1];
    var c_qty = parseInt(input.val());
    var new_qty = c_qty + qty;
    input.val(new_qty);
    $('#check_'+no).attr('checked', true);
    cal_diff(no);
  }

  $('#barcode').removeAttr('disabled');
  $('#barcode').val('');
  $('#qty').val(1);
  $('#barcode').focus();

}
