var HOME = BASE_URL + 'report/audit/return_cancle_qty_audit/';

function toggleAllDocument(option){
  $('#allDoc').val(option);
  if(option == 1){
    $('#btn-doc-all').addClass('btn-primary');
    $('#btn-doc-range').removeClass('btn-primary');
    $('#docFrom').val('');
    $('#docFrom').attr('disabled', 'disabled');
    $('#docTo').val('');
    $('#docTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-doc-all').removeClass('btn-primary');
    $('#btn-doc-range').addClass('btn-primary');
    $('#docFrom').removeAttr('disabled');
    $('#docTo').removeAttr('disabled');
    $('#docFrom').focus();
  }
}


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


//--- cancle date
$('#cancleFromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#cancleToDate').datepicker('option', 'minDate', sd);
  }
});


$('#cancleToDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#cancleFromDate').datepicker('option','maxDate', sd);
  }
});


function getReport(){
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();
	var cancleFromDate = $('#cancleFromDate').val();
	var cancleToDate = $('#cancleToDate').val();


	if(!isDate(fromDate) && !isDate(toDate) && !isDate(cancleFromDate) && !isDate(cancleToDate)) {
		swal({
			title: "วันที่ไม่ถูกต้อง",
			text:"กรุณาระบุ วันที่เอกสาร หรือวันที่ยกเลิก หรือ ทั้งคู่",
			type:'warning'
		});

		return false;
	}

	if((!isDate(fromDate) && isDate(toDate)) || (isDate(fromDate) && !isDate(toDate)) || (isDate(cancleFromDate) && !isDate(cancleToDate)) || (!isDate(cancleFromDate) && isDate(cancleToDate))) {
		swal({
			title: "วันที่ไม่ถูกต้อง",
			text:"กรุณาระบุ วันที่เอกสาร หรือวันที่ยกเลิก หรือ ทั้งคู่",
			type:'warning'
		});

		return false;
	}


  var allDoc = $('#allDoc').val();
  var docFrom = $('#docFrom').val();
  var docTo = $('#docTo').val();

	if(allDoc == 0 && (docFrom.length == 0 || docTo.length == 0)) {
		$('#docFrom').addClass('has-error');
		$('#docTo').addClass('has-error');
		return false;
	}
	else
	{
		$('#docFrom').removeClass('has-error');
		$('#docTo').removeClass('has-error');
	}

  var allRole = $('#allRole').val();
	if(allRole == 0) {
		var count = $('.chk:checked').length;
		if(count == 0) {
			$('#role-modal').modal('show');
			return false;
		}
	}

	var channels = $('#channels').val();

  var data = [
    {'name' : 'allDoc', 'value' : allDoc},
    {'name' : 'docFrom', 'value' : docFrom},
    {'name' : 'docTo', 'value' : docTo},
    {'name' : 'fromDate', 'value' : fromDate},
    {'name' : 'toDate', 'value' : toDate},
		{'name' : 'cancleFromDate', 'value' : cancleFromDate},
    {'name' : 'cancleToDate', 'value' : cancleToDate},
    {'name' : 'allRole', 'value' : allRole},
		{'name' : 'channels', 'value' : channels}
  ];

	if(allRole == 0){
    $('.chk').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'role['+index+']';
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
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(isJson(rs)){
        var source = $('#template').html();
        var data = $.parseJSON(rs);
        var output = $('#rs');
        render(source,  data, output);
      }
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
    }
  });

}


function doExport(){
	var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();
	var cancleFromDate = $('#cancleFromDate').val();
	var cancleToDate = $('#cancleToDate').val();


	if(!isDate(fromDate) && !isDate(toDate) && !isDate(cancleFromDate) && !isDate(cancleToDate)) {
		swal({
			title: "วันที่ไม่ถูกต้อง",
			text:"กรุณาระบุ วันที่เอกสาร หรือวันที่ยกเลิก หรือ ทั้งคู่",
			type:'warning'
		});

		return false;
	}

	if((!isDate(fromDate) && isDate(toDate)) || (isDate(fromDate) && !isDate(toDate)) || (isDate(cancleFromDate) && !isDate(cancleToDate)) || (!isDate(cancleFromDate) && isDate(cancleToDate))) {
		swal({
			title: "วันที่ไม่ถูกต้อง",
			text:"กรุณาระบุ วันที่เอกสาร หรือวันที่ยกเลิก หรือ ทั้งคู่",
			type:'warning'
		});

		return false;
	}


  var allDoc = $('#allDoc').val();
  var docFrom = $('#docFrom').val();
  var docTo = $('#docTo').val();

	if(allDoc == 0 && (docFrom.length == 0 || docTo.length == 0)) {
		$('#docFrom').addClass('has-error');
		$('#docTo').addClass('has-error');
		return false;
	}
	else
	{
		$('#docFrom').removeClass('has-error');
		$('#docTo').removeClass('has-error');
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
