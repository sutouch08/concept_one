function addNew(){
  window.location.href = BASE_URL + 'orders/orders/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'orders/orders';
}



function editDetail(){
  var code = $('#order_code').val();
  window.location.href = BASE_URL + 'orders/orders/edit_detail/'+ code;
}


function editOrder(code){
  window.location.href = BASE_URL + 'orders/orders/edit_order/'+ code;
}


function clearFilter(){
  var url = BASE_URL + 'orders/orders/clear_filter';
  $.get(url, function(rs){ goBack(); });
}



function getSearch(){
  $('#searchForm').submit();
}


function toggleState(state){
  var current = $('#state_'+state).val();
  if(current == 'Y'){
    $('#state_'+state).val('N');
    $('#btn-state-'+state).removeClass('btn-info');
  }else{
    $('#state_'+state).val('Y');
    $('#btn-state-'+state).addClass('btn-info');
  }

  getSearch();
}


function toggleNotSave(){
  var current = $('#notSave').val();
  if(current == ''){
    $('#notSave').val(1);
    $('#btn-not-save').addClass('btn-info');
  }else{
    $('#notSave').val('');
    $('#btn-not-save').removeClass('btn-info');
  }

  getSearch();
}


function toggleOnlyMe(){
  var current = $('#onlyMe').val();
  if(current == ''){
    $('#onlyMe').val(1);
    $('#btn-only-me').addClass('btn-info');
  }else{
    $('#onlyMe').val('');
    $('#btn-only-me').removeClass('btn-info');
  }

  getSearch();
}


function toggleIsExpire(){
  var current = $('#isExpire').val();
  if(current == ''){
    $('#isExpire').val(1);
    $('#btn-expire').addClass('btn-info');
  }else{
    $('#isExpire').val('');
    $('#btn-expire').removeClass('btn-info');
  }

  getSearch();
}


$('.search').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#toDate").datepicker("option", "minDate", ds);
	}
});

$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#fromDate").datepicker("option", "maxDate", ds);
	}
});


function test_cancle(code)
{
  $.ajax({
    url:BASE_URL + 'inventory/delivery_order/cancle_order/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          text:'Success',
          type:'success',
          timer:1000
        });
      }else{
        swal({
          title:'Error',
          text:rs,
          type:'error'
        })
      }
    }
  });
}
