var HOME = BASE_URL + 'inventory/check_stock_diff/';

function goBack(){
  window.location.href = HOME;
}


function getSearch(){
  $('#searchForm').submit();
}

function clearFilter()
{
  $.get(HOME + 'clear_filter', function(){
    goBack();
  });
}


function clearSearch(){
  $('.search').val('');
  getSearch();
}



function goToCheck(barcode){
  if(barcode !== undefined){
    window.location.href = HOME + 'check_barcode';
  }else{
    window.location.href = HOME + 'check';
  }
}


function goToAdjust(){
  var code = $('#adjust_code').val();
  window.location.href = BASE_URL + 'inventory/adjust/edit/'+code;
}


$('.search').keyup(function(e){
  if(e.keyCode == 13){
    var item = $('#product_code').val();
    var zone = $('#zone_code').val();
    if(item.length > 0 || zone.length > 0){
      getSearch();
    }
  }
})


$('#zone_code').keyup(function(e){
  if(e.keyCode == 13){
    set_zone();
  }
});


$('#zone_code').autocomplete({
  source:BASE_URL + 'auto_complete/get_zone_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2){
      $(this).val(arr[0]);
      $('#zone-code').val(arr[0]);
      $('#zone_name').val(arr[1]);
    }
  }
})

function set_zone()
{
  var zone_code = $('#zone_code').val();
  $.ajax({
    url:HOME + 'is_exists_zone',
    type:'GET',
    cache:false,
    data:{
      'zone_code' : zone_code
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(rs === 'ok'){
        $('#searchForm').submit();
      }
      else {
        swal({
          title:'Error!',
          text:'ไม่พบโซน',
          type:'error'
        });
      }
    }
  })
}



function change_zone(){
  $('#zone-code').val('');
  var is_barcode = $('#is_barcode').val();
  if(is_barcode == 1){
    goToCheck('barcode');
  }else{
    goToCheck();
  }
}


function cal_diff(no){
  var zone_qty = $('#stock_'+no).val();
  var count_qty = $('#qty_'+no).val();
  var diff_qty = count_qty - zone_qty;
  $('#diff_'+no).text(addCommas(diff_qty));
}


function save_checked(no){
  var zone_code = $('#zone-code').val();
  var stock = $('#stock_'+no).val();
  var count = $('#qty_'+no).val();
  var item = $('#item_'+no).val();

  $.ajax({
    url: HOME + 'save_checked',
    type: 'POST',
    cache: false,
    data:{
      'zone_code' : zone_code,
      'product_code' : item,
      'stock' : stock,
      'count' : count
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(rs === 'success'){
        var check = '<i class="fa fa-check green"></i>';
        $('#check-no-'+no).html(check);
        $('#qty_'+no).attr('disabled', 'disabled');
        $('#btn-'+no).attr('disabled','disabled');
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}


function save_all(){
  var uncheck = $('.check-no:not(:checked)').length;
  if(uncheck > 0){
    swal("กรุณาตรวจสอบ","พบรายการที่ไม่ได้ตรวจนับ "+ uncheck + " รายการ", "warning");
  }else{
    $('#checkForm').submit();
  }

}


function removeDiff(id){
  $.ajax({
    url:HOME + 'remove_diff/'+id,
    type:'POST',
    cache:false,
    success:function(rs){
      var rs = $.trim(rs);
      if(rs === 'success'){
        swal({
          title:'Deleted',
          text:'Diff qty has been removed',
          type:'success',
          timer:1000
        });

        $('#row-'+id).remove();
        reIndex();
      }
    }
  })
}


function toggleCheckAll(el){
  if(el.is(":checked")){
    $('.chk').prop("checked", true);
	}else{
    $('.chk').prop("checked", false);
	}
}


function loadDiff(){
  var adjust_code = $('#adjust_code').val();
  if(adjust_code.length == 0){
    swal('Error!','ไม่พบเลขที่เอกสารปรับยอด','error');
    return false;
  }

  var len = $('.chk:checked').length;
  if(len == 0){
    swal('กรุณาเลือกรายการที่ต้องการโหลด');
    return false;
  }

  load_in();

  $('#diffForm').submit();
}
