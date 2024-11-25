var HOME = BASE_URL + 'orders/orders/';

function addNew(){
  window.location.href = BASE_URL + 'orders/orders/add_new';
}

function leave(){
	swal({
		title: 'ออกจากหน้านี้',
    text:'การเปลี่ยนแปลงจะไม่ถูกบันทึก ต้องการออกจากหน้านี้หรือไม่ ?',
		type: 'warning',
    html:true,
		showCancelButton: true,
		cancelButtonText: 'No',
		confirmButtonText: 'Yes',
		closeOnConfirm: false
	}, function(){
		goBack();
	});
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

$('#sub-district').autocomplete({
  source:BASE_URL + 'auto_complete/sub_district',
  autoFocus:true,
  open:function(event){
    var $ul = $(this).autocomplete('widget');
    $ul.css('width', 'auto');
  },
  close:function(){
    var rs = $.trim($(this).val());
    var adr = rs.split('>>');
    if(adr.length == 4){
      $('#sub-district').val(adr[0]);
      $('#district').val(adr[1]);
      $('#province').val(adr[2]);
      $('#postcode').val(adr[3]);
    }
  }
});


$('#district').autocomplete({
  source:BASE_URL + 'auto_complete/district',
  autoFocus:true,
  open:function(event){
    var $ul = $(this).autocomplete('widget');
    $ul.css('width', 'auto');
  },
  close:function(){
    var rs = $.trim($(this).val());
    var adr = rs.split('>>');
    if(adr.length == 3){
      $('#district').val(adr[0]);
      $('#province').val(adr[1]);
      $('#postcode').val(adr[2]);
    }
  }
});


$('#province').autocomplete({
  source:BASE_URL + 'auto_complete/province',
  autoFocus:true,
  open:function(event){
    var $ul = $(this).autocomplete('widget');
    $ul.css('width', 'auto');
  }
})



$('#postcode').autocomplete({
  source:BASE_URL + 'auto_complete/postcode',
  autoFocus:true,
  open:function(event){
    var $ul = $(this).autocomplete('widget');
    $ul.css('width', 'auto');
  },
  close:function(){
    var rs = $.trim($(this).val());
    var adr = rs.split('>>');
    if(adr.length == 4){
      $('#sub-district').val(adr[0]);
      $('#district').val(adr[1]);
      $('#province').val(adr[2]);
      $('#postcode').val(adr[3]);
      $('#postcode').focus();
    }
  }
})
