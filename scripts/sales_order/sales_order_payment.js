function viewImage(imageUrl)
{
	var image = '<img src="'+imageUrl+'" width="100%" />';
	$("#imageBody").html(image);
	$("#imageModal").modal('show');
}

function viewPaymentDetail()
{
	var order_code = $('#order_code').val();
	load_in();
	$.ajax({
		url: BASE_URL + 'orders/orders/view_payment_detail',
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






function inputDeliveryNo()
{
	$("#deliveryModal").modal('show');
}






function saveDeliveryNo()
{
	var deliveryNo 	= $("#emsNo").val();
	var order_code 	= $("#order_code").val();
	if( deliveryNo != '')
	{
		$("#deliveryModal").modal('hide');
		$.ajax({
			url: BASE_URL + 'orders/orders/update_shipping_code/',
			type:"POST",
			cache:"false",
			data:{
				"shipping_code" : deliveryNo,
				"order_code" : order_code },
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






function submitPayment()
{
	var code	= $("#code").val();
	var id_account	= $("#id_account").val();
	var acc_no 	= $('#acc_no').val();
	var image	= $("#slip-image")[0].files[0];
	var orderAmount = parseDefault(parseFloat($('#orderAmount').val()), 0);
	var payAmount	= parseDefault(parseFloat($("#payAmount").val()), 0);
	var payDate	= $("#payDate").val();
	var payHour	= $("#payHour").val();
	var payMin	= $("#payMin").val();

	if( code == '' ) {
		swal('ข้อผิดพลาด', 'ไม่พบไอดีออเดอร์กรุณาออกจากหน้านี้แล้วเข้าใหม่อีกครั้ง', 'error');
		return false;
	}

	if( id_account == '' ){
		swal('ข้อผิดพลาด', 'ไม่พบข้อมูลบัญชีธนาคาร กรุณาออกจากหน้านี้แล้วลองแจ้งชำระอีกครั้ง', 'error');
		return false;
	}

	if(acc_no == '') {
		swal('ข้อผิดพลาด', 'ไม่พลเลขที่บัญชี กรุณาออกจากหน้านี้แล้วลองใหม่อีกครั้ง', 'error');
		return false;
	}

	if( image == '' ){
		swal('ข้อผิดพลาด', 'ไม่สามารถอ่านข้อมูลรูปภาพที่แนบได้ กรุณาแนบไฟล์ใหม่อีกครั้ง', 'error');
		return false;
	}

	if( payAmount == 0) {
		swal("ข้อผิดพลาด", "ยอดชำระไม่ถูกต้อง", 'error');
		return false;
	}

	if( !isDate(payDate) ){
		swal('วันที่ไม่ถูกต้อง');
		return false;
	}

	$("#paymentModal").modal('hide');

	var fd = new FormData();
	fd.append('image', $('#slip-image')[0].files[0]);
	fd.append('code', code);
	fd.append('id_account', id_account);
	fd.append('acc_no', acc_no);
	fd.append('payAmount', payAmount);
	fd.append('orderAmount', orderAmount);
	fd.append('payDate', payDate);
	fd.append('payHour', payHour);
	fd.append('payMin', payMin);
	fd.append('type', 'SO');
	load_in();
	$.ajax({
		url: BASE_URL + 'orders/sales_order/confirm_payment',
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




function readImage(input)
{
   if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $('#slipImg').html('<img id="slipImg" src="'+e.target.result+'" width="200px" alt="รูปสลิปของคุณ" />');
        }
        reader.readAsDataURL(input.files[0]);
    }
}






$("#slip-image").change(function(){
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

		readImage(this);

		$("#btn-slip-file").css("display", "none");
		$("#box-image").animate({opacity:1}, 1000);
	}
});





function clearPaymentForm()
{
	$("#id_account").val('');
	$("#payAmount").val('');
	$("#payDate").val('');
	$("#payHour").val('00');
	$("#payMin").val('00');
	removeSlipFile();
}






function removeSlipFile()
{
	$("#slipImg").html('');
	$("#box-image").css("opacity","0");
	$("#btn-slip-file").css('display', '');
	$("#slip-image").val('');
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





function selectSlipFile()
{
	$("#slip-image").click();
}





function payOnThis(id, acc_no)
{
	$("#selectBankModal").modal('hide');

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
	code = $('#code').val();

	$.ajax({
		url: BASE_URL + 'orders/sales_order/get_pay_amount',
		type:"GET",
		cache:"false",
		data: {
			"code" : code
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
