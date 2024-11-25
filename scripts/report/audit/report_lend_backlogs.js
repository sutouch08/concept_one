var HOME = BASE_URL + 'report/audit/lend_backlogs/';

function toggleAllEmp(option) {
	$('#allEmp').val(option);
	if(option == 1) {
		$('#btn-emp-use').removeClass('btn-primary');
		$('#btn-emp-all').addClass('btn-primary');
		$('#empName').val('').attr('disabled', 'disabled');
		return;
	}

	if(option == 0) {
		$('#btn-emp-all').removeClass('btn-primary');
		$('#btn-emp-use').addClass('btn-primary');
		$('#empName').val('').removeAttr('disabled');
		$('#empName').focus();
		return;
	}
}


$('#empName').autocomplete({
	source: BASE_URL + 'auto_complete/get_employee',
	autoFocus:true,
	close:function() {
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length == 2) {
			var empId = arr[1];
			var name = arr[0];
			$(this).val(name);
			$('#empId').val(empId);
		}
		else {
			$(this).val('');
			$('#empId').val('');
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
    $('#pdTo').val().attr('disabled', 'disabled');
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
	var allEmp = $('#allEmp').val();
	var empId = $('#empId').val();
	var allPd = $('#allProduct').val();
	var pdFrom = $("#pdFrom").val();
	var pdTo = $('#pdTo').val();
	var fromDate = $('#fromDate').val();
	var toDate = $('#toDate').val();

	if(!isDate(fromDate) || !isDate(toDate)) {
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	if(allEmp == 0 && empId == "") {
		swal("กรุณาระบุผู้ยืม");
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
			'allEmp' : allEmp,
			'empId' : empId,
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
	var allEmp = $('#allEmp').val();
	var empId = $('#empId').val();
	var allPd = $('#allProduct').val();
	var pdFrom = $("#pdFrom").val();
	var pdTo = $('#pdTo').val();
	var fromDate = $('#fromDate').val();
	var toDate = $('#toDate').val();

	if(!isDate(fromDate) || !isDate(toDate)) {
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	if(allEmp == 0 && empId == "") {
		swal("กรุณาระบุผู้ยืม");
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
