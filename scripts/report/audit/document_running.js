var HOME = BASE_URL + 'report/audit/document_running/';


function toggleAllRole(option) {
	$('#allRole').val(option);
	if(option == 1) {
		$('#btn-role-all').addClass('btn-primary');
		$('#btn-role-range').removeClass('btn-primary');
		$('.chk').prop('checked', false);
		return;
	}

	if(option == 0) {
		$('#btn-role-all').removeClass('btn-primary');
		$('#btn-role-range').addClass('btn-primary');
		$('#role-modal').modal('show');
		return;
	}
}



//--- document date
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



function doExport(){
	var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

	if(!isDate(fromDate) && !isDate(toDate)) {
		swal({
			title: "วันที่ไม่ถูกต้อง",
			text:"กรุณาระบุ วันที่เอกสาร",
			type:'warning'
		});

		return false;
	}


  var allRole = $('#allRole').val();

	if(allRole == 0) {
		var count = $('.chk:checked').length;
		if(count == 0) {
			$('#role-modal').modal('show');
			return false;
		}
	}

  var token = $('#token').val();
  get_download(token);

  $('#reportForm').submit();

}
