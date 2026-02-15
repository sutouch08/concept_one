function viewImage(imageUrl) {
	var image = '<img src="'+imageUrl+'" width="100%" />';
	$("#imageBody").html(image);
	$("#imageModal").modal('show');
}


function viewPaymentDetail(id) {
	let order_code = $('#order_code').val();

	load_in();

	$.ajax({
		url: BASE_URL + 'orders/orders/view_payment_detail/'+id,
		type:"POST",
		cache:"false",
		data:{
			"order_code" : order_code
		},
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'fail' ){
				swal('ข้อผิดพลาด', 'ไม่พบข้อมูล', 'error');
			}else{
				var source 	= $("#detailTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#detailBody");
				render(source, data, output);
				$("#confirmModal").modal('show');
			}
		}
	});
}


$("#emsNo").keyup(function(e) {
	if( e.keyCode == 13 )
	{
		saveDeliveryNo();
	}
});


function inputDeliveryNo() {
	$("#deliveryModal").modal('show');
}


function saveDeliveryNo() {
	var deliveryNo 	= $("#emsNo").val();
	var order_code 	= $("#order_code").val();
	if( deliveryNo != '')	{
		$("#deliveryModal").modal('hide');
		$.ajax({
			url: BASE_URL + 'orders/orders/update_shipping_code/',
			type:"POST",
			cache:"false",
			data:{
				"shipping_code" : deliveryNo,
				"order_code" : order_code
			},
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success')
				{
					window.location.reload();
				}
			}
		});
	}
}


function submitPayment() {
		var order_code = $("#order_code").val();
		var id_account = $("#id_account").val();
		var acc_no = $('#acc_no').val();
		var image = $("#image")[0].files[0];
		var payAmount = parseDefault(parseFloat($("#payAmount").val()), 0);
		var orderAmount = parseDefault(parseFloat($("#orderAmount").val()), 0);
		var payDate = $("#payDate").val();
		var payHour = $("#payHour").val();
		var payMin = $("#payMin").val();

		if( order_code == '' ) {
			$('#payment-error').text('ไม่พบไอดีออเดอร์กรุณาออกจากหน้านี้แล้วเข้าใหม่อีกครั้ง');
			return false;
		}

		if( id_account == '' ){
			$('#payment-error').text('ไม่พบข้อมูลบัญชีธนาคาร กรุณาออกจากหน้านี้แล้วลองแจ้งชำระอีกครั้ง');
			return false;
		}

		if(acc_no == ''){
			$('#payment-error').text('ไม่พลเลขที่บัญชี กรุณาออกจากหน้านี้แล้วลองใหม่อีกครั้ง');
			return false;
		}

		if( image == '' ){
			$('#payment-error').text('ไม่สามารถอ่านข้อมูลรูปภาพที่แนบได้ กรุณาแนบไฟล์ใหม่อีกครั้ง');
			return false;
		}

		if( payAmount <= 0 ){
			$('#payment-error').text("ยอดชำระไม่ถูกต้อง");
			return false;
		}

		if( !isDate(payDate) ){
			$('#payment-error').text('วันที่ไม่ถูกต้อง');
			return false;
		}

		$("#paymentModal").modal('hide');

		var fd = new FormData();
		fd.append('image', $('input[type=file]')[0].files[0]);
		fd.append('order_code', order_code);
		fd.append('id_account', id_account);
		fd.append('acc_no', acc_no);
		fd.append('payAmount', payAmount);
		fd.append('orderAmount', orderAmount);
		fd.append('payDate', payDate);
		fd.append('payHour', payHour);
		fd.append('payMin', payMin);
		fd.append('type', 'OR');

		load_in();
		$.ajax({
			url: BASE_URL + 'orders/orders/confirm_payment',
			type:"POST",
			cache: "false",
			data: fd,
			processData:false,
			contentType: false,
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( rs == 'success')
				{
					swal({
						title : 'สำเร็จ',
						text : 'แจ้งชำระเงินเรียบร้อยแล้ว',
						type: 'success',
						timer: 1000
					});

					clearPaymentForm();
					setTimeout(function(){
						window.location.reload();
					}, 1200);

				}
				else if( rs == 'fail' )
				{
					swal("ข้อผิดพลาด", "ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง", "error");
				}
				else
				{
					swal("ข้อผิดพลาด", rs, "error");
				}
			}
		});
	}




















function readURL(input)
{
   if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $('#previewImg').html('<img id="previewImg" src="'+e.target.result+'" width="200px" alt="รูปสลิปของคุณ" />');
        }
        reader.readAsDataURL(input.files[0]);
    }
}






$("#image").change(function(){
	if($(this).val() != '')
	{
		var file 		= this.files[0];
		var name		= file.name;
		var type 		= file.type;
		var size		= file.size;
		if(file.type != 'image/png' && file.type != 'image/jpg' && file.type != 'image/gif' && file.type != 'image/jpeg' )
		{
			swal("รูปแบบไฟล์ไม่ถูกต้อง", "กรุณาเลือกไฟล์นามสกุล jpg, jpeg, png หรือ gif เท่านั้น", "error");
			$(this).val('');
			return false;
		}
		if( size > 2000000 )
		{
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 2 MB", "error");
			$(this).val('');
			return false;
		}
		readURL(this);
		$("#btn-select-file").css("display", "none");
		$("#block-image").animate({opacity:1}, 1000);
	}
});





function clearPaymentForm()
{
	$("#id_account").val('');
	$("#payAmount").val('');
	$("#payDate").val('');
	$("#payHour").val('00');
	$("#payMin").val('00');
	removeFile();
}






function removeFile()
{
	$("#previewImg").html('');
	$("#block-image").css("opacity","0");
	$("#btn-select-file").css('display', '');
	$("#image").val('');
}





$("#payAmount").focusout(function(e) {
	if( $(this).val() != '' && isNaN(parseFloat($(this).val())) )
	{
		swal('กรุณาระบุยอดเงินเป็นตัวเลขเท่านั้น');
	}
});





function dateClick()
{
	$("#payDate").focus();
}





$("#payDate").datepicker({ dateFormat: 'dd-mm-yy'});





function selectFile()
{
	$("#image").click();
}





function payOnThis(id, acc_no)
{
	$("#selectBankModal").modal('hide');
	$('#payment-error').text('');

	$.ajax({
		url:BASE_URL + 'orders/orders/get_account_detail/'+id,
		type:"POST",
		cache:"false",
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'fail' )
			{
				swal('ข้อผิดพลาด', 'ไม่พบข้อมูลที่ต้องการ กรุณาลองใหม่', 'error');
			}else{
				var ds = rs.split(' | ');
				var logo 	= '<img src="'+ ds[0] +'" width="50px" height="50px" />';
				var acc	= ds[1];
				$("#id_account").val(id);
				$('#acc_no').val(acc_no);
				$("#logo").html(logo)
				$("#detail").html(acc);
				$("#paymentModal").modal('show');
			}
		}
	});
}





function payOrder()
{
	var order_code = $("#order_code").val();

	$.ajax({
		url: BASE_URL + 'orders/orders/get_pay_amount',
		type:"GET",
		cache:"false",
		data: {
			"order_code" : order_code
		},
		success: function(rs){
			var rs = $.trim(rs);
			if(isJson(rs)) {
				var ds = $.parseJSON(rs);

				$("#orderAmount").val(ds.pay_amount);
				$("#payAmountLabel").text("ยอดชำระ "+ addCommas(ds.pay_amount) +" บาท");
				$("#selectBankModal").modal('show');
			}
			else {
				swal({
					title:"Error!",
					text:rs,
					type:'error'
				});
			}
		}
	});
}



function removeAddress(id)
{
	swal({
		title: 'ต้องการลบที่อยู่ ?',
		text: 'คุณแน่ใจว่าต้องการลบที่อยู่นี้ โปรดจำไว้ว่าการกระทำนี้ไม่สามารถกู้คืนได้',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ลบเลย',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url:BASE_URL + 'orders/orders/delete_shipping_address',
				type:"POST",
				cache:"false",
				data:{
					"id_address" : id
				},
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title : "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });
						reloadAddressTable();
					}else{
						swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
					}
				}
			});
		});
}





//----------  edit address  -----------//
function editAddress(id)
{
	$.ajax({
		url:BASE_URL + 'orders/orders/get_shipping_address',
		type:"POST",
		cache:"false",
		data:{
			"id_address" : id
		},
		success: function(rs){
			var rs = $.trim(rs);
			if( isJson(rs) ){
				var ds = $.parseJSON(rs);
				$("#id_address").val(ds.id);
				$("#Fname").val(ds.name);
				$("#address1").val(ds.address);
				$("#sub_district").val(ds.sub_district);
				$('#district').val(ds.district);
				$("#province").val(ds.province);
				$("#postcode").val(ds.postcode);
				$('#country').val(ds.country);
				$("#adr-phone").val(ds.phone);
				$("#email").val(ds.email);
				$("#alias").val(ds.alias);
				$("#addressModal").modal('show');
			}else{
				swal("ข้อผิดพลาด!", "ไม่พบข้อมูลที่อยู่", "error");
			}
		}
	});
}


function setSender()
{
	var order_code = $('#order_code').val();
	var id_sender = $('#id_sender').val();

	if(id_sender == "" ) {
		swal("กรุณาเลือกผู้จัดส่ง");
		return false;
	}

	if($('#sender option:selected').data('gen') == 1) {
		//--- gen tracking no
		//--- get prfix
		let prefix = $('#sender option:selected').data('prefix');
		prefix = prefix + order_code.replace('-', '');
		$('#tracking').val(prefix);
	}
	else {
		$('#tracking').val('');
	}


	$.ajax({
		url:BASE_URL + 'orders/orders/set_sender',
		type:'POST',
		cache:false,
		data:{
			'order_code' : order_code,
			'id_sender' : id_sender
		},
		success:function(rs) {
			if(rs == 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		}
	})
}


//---------   ------------------//
function setAddress(id)
{
	var order_code = $('#order_code').val();
	$.ajax({
		url:BASE_URL + 'orders/orders/set_address',
		type:"POST",
		cache:"false",
		data:{
			"id_address" : id,
			"order_code" : order_code
		},
		success: function(rs){
			$(".btn-address").removeClass('btn-success');
			$("#btn-"+id).addClass('btn-success');
			$('#address_id').val(id);
		}
	});
}

function update_tracking() {
	var trackingNo = $('#tracking').val();
	var order_code = $('#order_code').val();
	$.ajax({
		url: BASE_URL + 'orders/orders/update_shipping_code/',
		type:"POST",
		cache:"false",
		data:{
			"shipping_code" : trackingNo,
			"order_code" : order_code },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success')
			{
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				$('#trackingNo').val(trackingNo);
			}
			else {
				swal({
					title:'Error!',
					type:'error',
					text:rs
				});
			}
		}
	});
}



function reloadAddressTable()
{
	var customer_code = $("#customerCode").val();
	var customer_ref = $('#customer_ref').val();
	$.ajax({
		url:BASE_URL + 'orders/orders/get_address_table',
		type:"POST",
		cache:"false",
		data:{
			'customer_code' : customer_code,
			'customer_ref' : customer_ref
		},
		success: function(rs){
			var rs = $.trim(rs);
			if(isJson(rs)){
				var source 	= $("#addressTableTemplate").html();
				var data 		= $.parseJSON(rs);
				var output 	= $("#adrs");
				render(source, data, output);
			}else{
				$("#adrs").html('<tr><td colspan="7" align="center">ไม่พบที่อยู่</td></tr>');
			}
		}
	});
}


function saveAddress()
{
	clearErrorByClass('a');

	let country = $('#s-country').val().trim();

	let h = {
		'id_address' : $('#id_address').val(),
		'customer_code' : $('#customerCode').val().trim(),
		'customer_ref' : $('#customer_ref').val().trim(),
		'name' : $('#Fname').val().trim(),
		'address' : $('#s-address').val().trim(),
		'sub_district' : $('#s-sub-district').val().trim(),
		'district' : $('#s-district').val().trim(),
		'province' : $('#s-province').val().trim(),
		'postcode' : $('#s-postcode').val().trim(),
		'country' : country == "" ? "Thailand" : country,
		'phone' : $('#s-phone').val().trim(),
		'email' : $('#s-email').val().trim(),
		'alias' : $('#s-alias').val().trim()
	}

	if(h.name.length == 0) {
		$('#Fname').hasError();
		return false;
	}

	if(h.address.length == 0) {
		$('#s-address').hasError();
		return false;
	}

	if(h.sub_district.length == 0) {
		$('#s-sub-district').hasError();
		return false;
	}

	if(h.district.length == 0) {
		$('#s-district').hasError();
		return false;
	}

	if(h.province.length == 0) {
		$('#s-province').hasError();
		return false;
	}

	if(h.alias.length == 0) {
		$('#s-alias').hasError();
		return false;
	}

	$("#addressModal").modal('hide');

	load_in();

	$.ajax({
		url:BASE_URL + 'orders/orders/save_address',
		type:"POST",
		cache:"false",
		data: {
			'data' : JSON.stringify(h)
		},
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success'){
				reloadAddressTable();
				clearAddressField();
			}else{
				swal({
					title:'ข้อผิดพลาด',
					text:rs,
					type:'error'
				});
				$("#addressModal").modal('show');
			}
		}
	});
}


function addNewAddress()
{
	clearAddressField();
	$("#addressModal").modal('show');
}



$('#s-sub-district').autocomplete({
	source:BASE_URL + 'auto_complete/sub_district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		let adr = $(this).val().trim().split('>>');
		if(adr.length == 4) {
			$('#s-sub-district').val(adr[0]);
			$('#s-district').val(adr[1]);
			$('#s-province').val(adr[2]);
			$('#s-postcode').val(adr[3]);
			$('#s-phone').focus();
		}
	}
});


$('#s-district').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		let adr = $(this).val().trim().split('>>');
		if(adr.length == 3){
			$('#s-district').val(adr[0]);
			$('#s-province').val(adr[1]);
			$('#s-postcode').val(adr[2]);
			$('#s-phone').focus();
		}
	}
});


$('#s-province').autocomplete({
	source:BASE_URL + 'auto_complete/province',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function() {
		let adr = $(this).val().trim();
		if(adr.length) {
			$(this).val(adr);
			$('#s-phone').focus();
		}
	}
})


$('#s-postcode').autocomplete({
	source:BASE_URL + 'auto_complete/postcode',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		let adr = $(this).val().trim().split('>>');
		if(adr.length == 4){
			$('#s-sub-district').val(adr[0]);
			$('#s-district').val(adr[1]);
			$('#s-province').val(adr[2]);
			$('#s-postcode').val(adr[3]);
			$('#s-postcode').focus();
			$('#s-phone').focus();
		}
	}
})


function clearAddressField()
{
	$("#id_address").val('');
	$("#Fname").val('');
	$("#address1").val('');
	$('#s-sub-district').val('');
	$('#s-district').val('');
	$("#s-province").val('');
	$("#s-postcode").val('');
	$("#s-phone").val('');
	$("#s-email").val('');
	$("#s-alias").val('');
}


if($('#btn').length) {
	var clipboard = new Clipboard('#btn');
}


function Summary(){
	var amount 		= parseFloat( removeCommas($("#total-td").text() ) );
	var discount 	= parseFloat( removeCommas( $("#discount-td").text() ) );
	var netAmount = amount - discount;
	$("#netAmount-td").text( addCommas( parseFloat(netAmount).toFixed(2) ) );

}


function print_order(id)
{
	var wid = $(document).width();
	var left = (wid - 900) /2;
	window.open("controller/orderController.php?print_order&order_code="+id, "_blank", "width=900, height=1000, left="+left+", location=no, scrollbars=yes");
}


function getSummary()
{
	var order_code = $("#order_code").val();
	$.ajax({
		url:BASE_URL + 'orders/orders/get_summary',
		type:"POST",
		cache:"false",
		data:{
			"order_code" : order_code
		},
		success: function(rs){
			$("#summaryText").html(rs);
		}
	});

	$("#orderSummaryTab").modal("show");
}



$("#Fname").keyup(function(e){ if( e.keyCode == 13 ){ $("#address1").focus(); 	} });
$("#address1").keyup(function(e){ if( e.keyCode == 13 ){ $("#sub_district").focus(); 	} });
$("#adr-phone").keyup(function(e){ if( e.keyCode == 13 ){ $("#email").focus(); 	} });
$("#email").keyup(function(e){ if( e.keyCode == 13 ){ $("#alias").focus(); 	} });
$("#alias").keyup(function(e){ if( e.keyCode == 13 ){ saveAddress(); } });
