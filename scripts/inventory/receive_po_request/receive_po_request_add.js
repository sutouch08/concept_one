// JavaScript Document

var data = [];
var poError = 0;
var invError = 0;
var zoneError = 0;


function editHeader() {
	$('.header-box').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}


function updateHeader() {
	var code = $('#receive_code').val();
	var date_add = $('#dateAdd').val();
	var remark = $('#remark').val();

	if(!isDate(date_add)){
		swal('วันที่ไม่ถูกต้อง');
		return false;
	}

	load_in();
	$.ajax({
		url:HOME + 'update_header',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'date_add' : date_add,
			'remark' : remark
		},
		success:function(rs){
			load_out();
			if(rs === 'success'){
				swal({
					title:'Updated',
					text:'Update successfully',
					type:'success',
					timer:1000
				});

				$('.header-box').attr('disabled', 'disabled');
				$('#btn-update').addClass('hide');
				$('#btn-edit').removeClass('hide');
			}else{
				swal({
					title:'Error!',
					text: rs,
					type:'error'
				});
			}
		}
	})
}



function save() {
	code = $('#receive_code').val();

	//--- Vendor code
	vendor_code = $('#vendor_code').val();
	vendor_name = $('#vendorName').val();

	//--- อ้างอิง PO Code
	po = $.trim($('#poCode').val());

	//--- เลขที่ใบส่งสินค้า
	invoice = $.trim($('#invoice').val());
	//--- approve key
	approver = $('#approver').val();

	//--- นับจำนวนรายการในใบสั่งซื้อ
	count = $(".receive-box").length;

	//--- Currency
	docCur = $('#DocCur').val();

	//--- Doc rate
	docRate = parseDefault(parseFloat($('#DocRate').val()), 0);

	//--- ตรวจสอบความถูกต้องของข้อมูล
	if(code == '' || code == undefined) {
		swal('ไม่พบเลขที่เอกสาร', 'หากคุณเห็นข้อผิดพลาดนี้มากกว่า 1 ครับ ให้ลองออกจากหน้านี้แล้วกลับเข้ามาทำรายการใหม่', 'error');
		return false;
	}

	if(vendor_code == '' || vendor_name == '') {
		swal('กรุณาระบุผู้จำหน่าย');
		return false;
	}


	//--- ใบสั่งซื้อถูกต้องหรือไม่
	if(po == '') {
		swal('กรุณาระบุใบสั่งซื้อ');
		return false;
	}

	//--- มีรายการในใบสั่งซื้อหรือไม่
	if(count = 0) {
		swal('Error!', 'ไม่พบรายการรับเข้า','error');
		return false;
	}

	var header = {
		"receive_code" : code,
		"vendor_code" : vendor_code,
		"vendorName" : vendor_name,
		"poCode" : po,
		"invoice" : invoice,
		"approver" : approver,
		"DocCur" : docCur,
		"DocRate" : docRate
	};

	var rows = [];

	$('.receive-box').each(function(index, el) {
		let qty = parseDefault(parseFloat($(this).val()), 0);
		if(qty > 0) {
			let uid = $(this).data('uid');
			let row = {
				'baseEntry' : $('#docEntry_'+uid).val(),
				'baseLine' : $('#lineNum_'+uid).val(),
				'product_code' : $('#pdCode_'+uid).val(),
				'product_name' : $('#pdName_'+uid).val(),
				'price' : $('#price_'+uid).val(),
				'qty' : qty,
				'currency' : $('#currency_'+uid).val(),
				'rate' : $('#rate_'+uid).val(),
				'vatGroup' : $('#vatGroup_'+uid).val(),
				'vatRate' : $('#vatRate_'+uid).val()
			}

			rows.push(row);
		}
	});

	if(rows.length < 1) {
		swal('ไม่พบรายการรับเข้า');
		return false;
	}

	load_in();

	$.ajax({
		url: HOME + 'save',
		type:"POST",
		cache:"false",
		data: {
			"receive_code" : code,
			"header" : JSON.stringify(header),
			"items" : JSON.stringify(rows)
		},
		success: function(rs) {
			load_out();

			rs = $.trim(rs);
			if(rs == 'success') {
				swal({
					title:'Success',
					text:'บันทึกรายการเรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					viewDetail(code);
				}, 1200);

			}
			else
			{
				swal("ข้อผิดพลาด !", rs, "error");
			}
		}
	});

}	//--- end save


function checkLimit() {
	var allow = $('#allow_over_po').val() == '1' ? true : false;
	var over = 0;

	$(".receive-box").each(function() {
    let uid = $(this).data('uid');
		var limit = parseDefault(parseFloat($("#limit_"+uid).val()), 0);
		var qty = parseDefault(parseFloat($(this).val()), 0);

		if(limit > 0 && qty > 0) {
			if(qty > limit) {
				over++;
				if(! allow) {
					$(this).addClass('has-error');
				}
			}
			else {
				$(this).removeClass('has-error');
			}
		}
	});

	if( over > 0 ) {
		if( ! allow) {
			swal({
				title:'Error!',
				text:'กรุณาระบุจำนวนไม่เกินยอดค้างรับ',
				type:'error'
			});
		}
		else {
			getApprove();			
		}
	}
	else {
		save();
	}
}






$("#sKey").keyup(function(e) {
    if( e.keyCode == 13 ){
		doApprove();
	}
});





function getApprove() {
	$("#approveModal").modal("show");
}





$("#approveModal").on('shown.bs.modal', function(){ $("#sKey").focus(); });



function validate_credentials(){
	var s_key = $("#s_key").val();
	var menu 	= $("#validateTab").val();
	var field = $("#validateField").val();
	if( s_key.length != 0 ){
		$.ajax({
			url:BASE_URL + 'users/validate_credentials/get_permission',
			type:"GET",
			cache:"false",
			data:{
				"menu" : menu,
				"s_key" : s_key,
				"field" : field
			},
			success: function(rs){
				var rs = $.trim(rs);
				if( isJson(rs) ){
					var data = $.parseJSON(rs);
					$("#approverName").val(data.approver);
					closeValidateBox();
					callback();
					return true;
				}else{
					showValidateError(rs);
					return false;
				}
			}
		});
	}else{
		showValidateError('Please enter your secure code');
	}
}


function doApprove(){
	var s_key = $("#sKey").val();
	var menu = 'ICPURC'; //-- อนุมัติรับสินค้าเกินใบสั่งซื้อ
	var field = 'approve';

	if( s_key.length > 0 )
	{
		$.ajax({
			url:BASE_URL + 'users/validate_credentials/get_permission',
			type:"GET",
			cache:"false",
			data:{
				"menu" : menu,
				"s_key" : s_key,
				"field" : field
			},
			success: function(rs){
				var rs = $.trim(rs);
				if( isJson(rs) ){
					var data = $.parseJSON(rs);
					$("#approver").val(data.approver);
					$("#approveModal").modal('hide');
					save();
				}else{
					$('#approvError').text(rs);
					return false;
				}
			}
		});
	}
}





function leave(){
	swal({
		title: 'ยกเลิกข้อมูลนี้ ?',
		type: 'warning',
		showCancelButton: true,
		cancelButtonText: 'No',
		confirmButtonText: 'Yes',
		closeOnConfirm: false
	}, function(){
		goBack();
	});

}


function changePo(){
	swal({
		title: 'ยกเลิกข้อมูลนี้ ?',
		type: 'warning',
		showCancelButton: true,
		cancelButtonText: 'No',
		confirmButtonText: 'Yes',
		closeOnConfirm: false
	}, function(){
		$("#receiveTable").html('');
		$('#btn-change-po').addClass('hide');
		$('#btn-get-po').removeClass('hide');
		$('#poCode').val('');
		$('#poCode').removeAttr('disabled');
		swal({
			title:'Success',
			text:'ยกเลิกข้อมูลเรียบร้อยแล้ว',
			type:'success',
			timer:1000
		});
		setTimeout(function(){
			$('#poCode').focus();
		}, 1200);
	});
}



function getPoCurrency(poCode) {
	$.ajax({
		url:HOME + 'get_po_currency',
		type:'GET',
		cache:false,
		data:{
			'po_code' : poCode
		},
		success:function(rs) {
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);
				$('#DocCur').val(ds.DocCur);
				$('#DocRate').val(ds.DocRate);

				if(ds.DocCur == 'THB') {
					$('#DocRate').val(1.00);
				}
			}
		}
	});
}



function getData(){
	var po = $("#poCode").val();
	getPoCurrency(po);
	load_in();
	$.ajax({
		url: HOME + 'get_po_detail',
		type:"GET",
		cache:"false",
		data:{
			"po_code" : po
		},
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( isJson(rs) ){
				data = $.parseJSON(rs);
				var source = $("#template").html();
				var output = $("#receiveTable");
				render(source, data, output);
				$("#poCode").attr('disabled', 'disabled');
				$(".receive-box").keyup(function(e){
    				sumReceive();
				});

				update_vender(po);

				$('#btn-get-po').addClass('hide');
				$('#btn-change-po').removeClass('hide');
				setTimeout(function(){
					$('#invoice').focus();
				},1000);

			}else{
				swal("ข้อผิดพลาด !", rs, "error");
				$("#receiveTable").html('');
			}
		}
	});
}



$("#vendorName").autocomplete({
	source: BASE_URL + 'auto_complete/get_vendor_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			$(this).val(arr[1]);
			$("#vendor_code").val(arr[0]);
			$('#poCode').focus();
		}else{
			$(this).val('');
			$("#vendor_code").val('');
		}
	}
});



$('#vendorName').focusout(function(event) {
	if($(this).val() == ''){
		$('#vendor_code').val('');
	}
	poInit();
});




$(document).ready(function() {
	poInit();
});


function poInit(){
	var vendor_code = $('#vendor_code').val();
	if(vendor_code == ''){
		$("#poCode").autocomplete({
			source: BASE_URL + 'auto_complete/get_po_code',
			autoFocus: true,
			close:function(){
				var code = $(this).val();
				var arr = code.split(' | ');
				if(arr.length == 2){
					$(this).val(arr[1]);
				}
			}
		});
	}else{
		$("#poCode").autocomplete({
			source: BASE_URL + 'auto_complete/get_po_code/'+vendor_code,
			autoFocus: true,
			close:function(){
				var code = $(this).val();
				var arr = code.split(' | ');
				if(arr.length == 2){
					$(this).val(arr[1]);
				}
			}
		});
	}
}



function update_vender(po_code){
	$.ajax({
		url: BASE_URL + 'inventory/receive_po/get_vender_by_po/'+po_code,
		type:'GET',
		cache:false,
		success:function(rs){
			if(isJson(rs)){
				var ds = $.parseJSON(rs);
				$('#vendor_code').val(ds.code);
				$('#vendorName').val(ds.name);
			}
		}
	});
}


$('#poCode').keyup(function(e) {
	if(e.keyCode == 13){
		if($(this).val().length > 0){
			getData();
		}
	}
});



$("#dateAdd").datepicker({ dateFormat: 'dd-mm-yy'});



function sumReceive(){

	var qty = 0;
	$(".receive-box").each(function(index, element) {
    	var cqty = isNaN( parseInt( $(this).val() ) ) ? 0 : parseInt( $(this).val() );
			qty += cqty;
    });
	$("#total-receive").text( addCommas(qty) );
}



function validateOrder(){
  var prefix = $('#prefix').val();
  var runNo = parseInt($('#runNo').val());
  let code = $('#code').val();

  if(code.length == 0){
    $('#addForm').submit();
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
      $.ajax({
        url: HOME + 'is_exists/'+code,
        type:'GET',
        cache:false,
        success:function(rs){
          if(rs == 'not_exists'){
            $('#addForm').submit();
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

  }else{
    swal('เลขที่เอกสารไม่ถูกต้อง');
    return false;
  }
}

$('#code').keyup(function(e){
	if(e.keyCode == 13){
		validateOrder();
	}
});
