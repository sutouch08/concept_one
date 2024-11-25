var HOME = BASE_URL + 'inventory/return_request/';

function goBack() {
	window.location.href = HOME;
}


function goAdd() {
	window.location.href = HOME + 'add_new';
}


function goEdit(code) {
	window.location.href = HOME + 'edit/'+code;
}



function addNew() {
	var date = $('#date_add').val();
	var remark = $.trim($('#remark').val());

	if(!isDate(date)) {
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'date_add' : date,
			'remark' : remark
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				goEdit(ds.code);
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error',
					html:true
				});
			}
		},
		error:function(xhr, status, error) {
			swal({
				title:'Error!',
				text:xhr.responseText,
				type:'error',
				html:true
			})
		}
	})
}



function getEdit() {
	$('#date_add').removeAttr('disabled');
	$('#remark').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}


function update() {
	var code = $('#code').val();
	var date_add = $('#date_add').val();
	var remark = $('#remark').val();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'date_add' : date_add,
			'remark' : remark
		},
		success:function(rs) {
			if(rs == 'success') {
				swal({
					title:'Updated',
					type:'success',
					timer:1000
				});
				
				$('#date_add').attr('disabled', 'disabled');
				$('#remark').attr('disabled', 'disabled');
				$('#btn-update').addClass('hide');
				$('#btn-edit').removeClass('hide');
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error',
					html:true
				});
			}
		}
	})
}


function getSearch() {
	$('#searchForm').submit();
}


$('.search').keyup(function(e) {
	if(e.keyCode == 13) {
		getSearch();
	}
})


$('#fromDate').datepicker({
	dateFormat:'dd-mm-yy',
	onClose:function(sd) {
		$('#toDate').datepicker('option', 'minDate', sd);
	}
});

$('#toDate').datepicker({
	dateFormat:'dd-mm-yy',
	onClose:function(sd) {
		$('#fromDate').datepicker('option', 'maxDate', sd);
	}
});


$('#date_add').datepicker({
	dateFormat:'dd-mm-yy'
});
