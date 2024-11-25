function getValidate() {
	var isManual = $('#manualCode').length;
	if(isManual === 1) {
		var prefix = $('#prefix').val();
	  var runNo = parseInt($('#runNo').val());
	}

	var code = $('#code').val();

	if(code.length == 0){
	   save();
	   return false;
	}

	let arr = code.split('-');

	if(arr.length == 2){
	  if(arr[0] !== prefix){
	    swal('Prefix ต้องเป็น '+prefix);
	    return false;
	  }else if(arr[1].length != (4 + runNo)){
	    swal('Run Number ไม่ถูกต้อง');
	    return false;
	  }else{
	    addOrder();
		}

	}else{
	  swal('เลขที่เอกสารไม่ถูกต้อง');
	  return false;
	}
}


function addOrder() {
	var code = $('#code').val();
	$.ajax({
		url: HOME + 'is_exists/'+code,
		type:'GET',
		cache:false,
		success:function(rs){
			if(rs == 'not_exists'){
				save();
			}else{
				swal({
					title:'Error!!',
					text: rs,
					type: 'error'
				});
			}
		}
	})
}


function save()
{
	var error = 0;
	let code = $('#code').val();
	let zone_code = $('#zone_code').val();
	let zoneName = $('#zone').val();
	let empName = $('#empName').val();
	let empID = $('#empID').val();
	let lendCode = $('#lend_code').val();
	let date_add = $('#dateAdd').val();
	let remark = $.trim($('#remark').val());
	let reqRemark = $('#required_remark').val();

	if(!isDate(date_add)){
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	if(zone_code.length == 0 || zoneName.length == 0){
		swal("กรุณาระบุโซนรับเข้า");
		return false;
	}

	if(empName.length == 0 || empID == ''){
		swal("กรุณาระบุผู้ยืม");
		return false;
	}

	if(lendCode.length == 0){
		swal("กรุณาระบุใบยืมสินค้า");
		return false;
	}

	if(reqRemark == 1 && remark.length < 10) {
		swal({
			title:'ข้อผิดพลาด',
			text:'กรุณาใส่หมายเหตุ (ความยาวอย่างน้อย 10 ตัวอักษร)',
			type:'warning'
		});

		return false;
	}

	let header = {
		"code" : code,
		"date_add" : date_add,
		"empID" : empID,
		"empName" : empName,
		"lendCode" : lendCode,
		"zone_code" : zone_code,
		"remark" : remark
	}

	let rows = [];

	$('.qty').each(function() {
		let no = $(this).data('no');
		let qty = parseDefault(parseFloat($(this).val()), 0);
		let limit = parseDefault(parseFloat($('#backlogs-'+no).val()), 0);
		let itemCode = $(this).data('product');

		if(qty > 0 && qty <= limit) {

			let row = {
				"product_code" : itemCode,
				"qty" : qty
			}

			rows.push(row);

			$(this).removeClass('has-error');
		}

		if(qty < 0 || qty > limit){
			error++;
			$(this).addClass('has-error');
		}
	});

	if(error > 0) {

		swal({
			title:'Error!',
			text:"จำนวนที่คืนต้องไม่มากกว่ายอดค้างรับ และ ต้องไม่น้อยกว่า 0",
			type:'error'
		});

		return false;
	}

	if(rows.length < 1) {
		swal({
			title:'Error!',
			text:"ต้องคืนอย่างน้อย 1 ตัว",
			type:'error'
		});

		return false
	}

	load_in();

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			"header" : JSON.stringify(header),
			"details" : JSON.stringify(rows)
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				ds = JSON.parse(rs);

				if(ds.status === 'success') {
					setTimeout(function() {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(function() {
							viewDetail(ds.code);
						}, 1200);
					}, 200);
				}
				else if(ds.status == 'warning') {
					setTimeout(function() {
						swal({
							title:'Warning',
							text:ds.message,
							type:'warning'
						}, () => {
							viewDetail(ds.code);
						});
					}, 200);
				}
				else {
					setTimeout(function() {
						swal({
							title:'Error!',
							text:ds.message,
							type:'error',
							html:true
						});
					}, 200);
				}
			}
		},
		error:function(xhr, status, error) {
			load_out();
			console.log(status);
			console.log(error);

			setTimeout(function() {
				swal({
					title:'Error!',
					text:xhr.responseText,
					type:'error',
					html:true
				});
			}, 200);
		}
	});
}





function doExport() {
	var code = $('#code').val();
	$.get(HOME + '/do_export/'+code, function(rs){
		if(rs === 'success'){
			swal({
				title:'Success',
				text:'ส่งข้อมูลไป SAP สำเร็จ',
				type:'success',
				timer:1000
			});
			setTimeout(function(){
				viewDetail(code);
			}, 1500);
		}
	});
}




$('#dateAdd').datepicker({
	dateFormat:'dd-mm-yy'
});


$("#empName").autocomplete({
	source: BASE_URL + 'auto_complete/get_employee',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var empName = arr[0];
			var empID = arr[1];
			$("#empName").val(empName);
			$("#empID").val(empID);
			lend_code_init();
		}
		else {
			$("#empID").val('');
			$(this).val('');
			lend_code_init();
		}
	}
});




$('#zone').autocomplete({
	source : BASE_URL + 'auto_complete/get_zone_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$("#zone").val(arr[1]);
			$('#zone_code').val(arr[0]);
		}else{
			$("#zone").val('');
			$('#zone_code').val('');
		}
	}
});

$('#zone_code').autocomplete({
	source : BASE_URL + 'auto_complete/get_zone_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$("#zone").val(arr[1]);
			$('#zone_code').val(arr[0]);
		}else{
			$(this).val('');
			$('#zone').val('');
			$('#zone_code').val('');
		}
	}
});



function recalTotal(){
	var totalQty = 0;
	$('.qty').each(function(){
		let qty = $(this).val();
		qty = parseDefault(parseFloat(qty),0);
		totalQty += qty;
	});

	$('#totalQty').text(addCommas(totalQty));
}


function sendToWms() {
	var code = $('#code').val();

	load_in();
	$.ajax({
		url:HOME + 'send_to_wms/'+code,
		type:'POST',
		cache:false,
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'succcess',
					timer:1000
				});
			}
			else
			{
				swal({
					title:'Error!',
					text:rs,
					type:'error',
					html:true
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


function accept() {
	$('#accept-modal').on('shown.bs.modal', () => $('#accept-note').focus());
	$('#accept-modal').modal('show');
}

function acceptConfirm() {
	let code = $('#code').val();
	let note = $.trim($('#accept-note').val());

	if(note.length < 10) {
		$('#accept-error').text('กรุณาระบุหมายเหตุอย่างนี้อย 10 ตัวอักษร');
		return false;
	}
	else {
		$('#accept-error').text('');
	}

	load_in();

	$.ajax({
		url:HOME + 'accept_confirm',
		type:'POST',
		cache:false,
		data:{
			"code" : code,
			"accept_remark" : note
		},
		success:function(rs) {
			load_out();
			if(isJson(rs))
			{
				let ds = JSON.parse(rs);
				if(ds.status === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(() => {
						window.location.reload();
					}, 1200);
				}
				else if(ds.status === 'warning') {

					swal({
						title:'Warning',
						text:ds.message,
						type:'warning'
					}, () => {
						setTimeout(() => {
							window.location.reload();
						}, 500);
					});
				}
				else {
					swal({
						title:'Error!',
						text: rs,
						type:'error'
					});
				}
			}
		}
	});

}
