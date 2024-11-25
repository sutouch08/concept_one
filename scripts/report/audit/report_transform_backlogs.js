var HOME = BASE_URL + 'report/audit/transform_backlogs/';

function toggleAllUser(option) {
	$('#allUser').val(option);
	if(option == 1) {
		$('#btn-user-use').removeClass('btn-primary');
		$('#btn-user-all').addClass('btn-primary');
		$('#u_name').val('').attr('disabled', 'disabled');
		return;
	}

	if(option == 0) {
		$('#btn-user-all').removeClass('btn-primary');
		$('#btn-user-use').addClass('btn-primary');
		$('#u_name').val('').removeAttr('disabled');
		$('#u_name').focus();
		return;
	}
}


$('#u_name').autocomplete({
	source: BASE_URL + 'auto_complete/get_user',
	autoFocus:true,
	close:function() {
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2) {
			var code = arr[0];
			var name = arr[1];
			$(this).val(name);
			$('#dname').val(name);
		}
		else {
			$(this).val('');
			$('#dname').val('');
		}
	}
})






$('#pdFrom').autocomplete({
  source : BASE_URL + 'auto_complete/get_item_code',
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
  source:BASE_URL + 'auto_complete/get_item_code',
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



function toggleAllProduct(option){
  $('#allProduct').val(option);

  if(option == 1){
    $('#btn-pd-all').addClass('btn-primary');
    $('#btn-pd-range').removeClass('btn-primary');
    $('#pdFrom').val('').attr('disabled', 'disabled');
    $('#pdTo').val('').attr('disabled', 'disabled');
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
	var allUser = $('#allUser').val();
	var dname = $('#dname').val();
	var allPd = $('#allProduct').val();
	var pdFrom = $("#pdFrom").val();
	var pdTo = $('#pdTo').val();
	var fromDate = $('#fromDate').val();
	var toDate = $('#toDate').val();

	if(allUser == 0 && dname == "") {
		swal("กรุณาระบุผู้เบิก");
		return false;
	}

	if(allPd == 0) {
		if(pdFrom == "" || pdTo == "") {
			swal("รหัสสินค้าไม่ถูกต้อง");
			return false;
		}
	}

	load_in();

	$.ajax({
		url:HOME + 'get_report',
		type:'GET',
		cache:false,
		data:{
			'allUser' : allUser,
			'dname' : dname,
			'allProduct' : allPd,
			'pdFrom' : pdFrom,
			'pdTo' : pdTo,
			'fromDate' : fromDate,
			'toDate' : toDate
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				var source = $('#template').html();
				var output = $('#result');

				render(source, ds, output);
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
		},
		error:function(xhr, status, error) {
			load_out();
			swal({
				title:'Error!',
				text:xhr.responseText,
				type:'error',
				html:true
			})
		}
	})
}



function doExport() {
	var allUser = $('#allUser').val();
	var dname = $('#dname').val();
	var allPd = $('#allProduct').val();
	var pdFrom = $("#pdFrom").val();
	var pdTo = $('#pdTo').val();
	var fromDate = $('#fromDate').val();
	var toDate = $('#toDate').val();

	if(allUser == 0 && dname == "") {
		swal("กรุณาระบุผู้เบิก");
		return false;
	}

	if(allPd == 0) {
		if(pdFrom == "" || pdTo == "") {
			swal("รหัสสินค้าไม่ถูกต้อง");
			return false;
		}
	}

	var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();

}
