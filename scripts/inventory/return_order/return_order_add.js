window.addEventListener('load', () => {
	zoneInit();
	invoiceInit();
});

function toggleCheckAll(el) {
	if (el.is(":checked")) {
		$('.chk').prop("checked", true);
	} else {
		$('.chk').prop("checked", false);
	}
}


function removeChecked() {
	$('.chk:checked').each(function() {
		let no = $(this).val();

		$('#row-'+no).remove();
	});

	reIndex();
	recalTotal();
}

function deleteChecked(){
	load_in();

	setTimeout(function(){
		$('.chk:checked').each(function(){
			var no = $(this).val();
			removeRow(no);
		})

		reIndex();
		recalTotal();
		load_out();
	}, 500)

}



function unsave(){
	var code = $('#return_code').val();

	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิกการบันทึก '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: true
		}, function() {
			load_in();

			$.ajax({
				url:HOME + 'unsave/'+code,
				type:'POST',
				cache:false,
				success:function(rs) {
					load_out();
					if(rs === 'success') {
						setTimeout(function() {
							swal({
								title:'Success',
								text:'ยกเลิกการบันทึกเรียบร้อยแล้ว',
								type:'success',
								time:1000
							});

							setTimeout(function(){
								goEdit(code);
							}, 1500);
						}, 200);
					}
					else {
						setTimeout(function() {
							swal({
								title:'Error!',
								text:rs,
								type:'error'
							})
						}, 200);
					}
				}
			});
	});
}


function save(isDraft = 0)
{
	let error = 0;
	let allow_no_inv = $('#allow-return-no-inv').val() == 1 ? true : false;

	let h = {
		"date_add" : $('#dateAdd').val(),
		"customer_code" : $('#customer_code').val(),
		"customer_name" : $('#customer_name').val(),
		"reference" : $('#reference').val(),
		"remark" : $('#remark').val(),
		"invoice_code" : $('#invoice').val(),
		"warehouse_code" : $('#warehouse').val(),
		"zone_code" : $('#zone_code').val(),
		"is_draft" : isDraft == 1 ? 1 : 0
	}

	let items = [];

	//--- clear error
	$('.h').removeClass('has-error');
	$('.r').removeClass('has-error');

	if( ! isDate(h.date_add)) {
		$('#dateAdd').addClass('has-error');
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	if(h.customer_code == "" || h.customer_name == "") {
		$('#customer_code').addClass('has-error');
		$('#customer_name').addClass('has-error');
		swal("กรุณาระบุลูกค้า");
		return false;
	}

	if(h.warehouse_code == "") {
		$('#warehouse').addClass('has-error');
		swal("กรุณาระบุคลังสินค้า");
		return false;
	}

	if(h.zone_code == "") {
		$('#zone_code').addClass('has-error');
		swal("กรุณาระบุโซนรับสินค้า");
		return false;
	}

	if( allow_no_inv == false && h.invoice_code == '') {
		$('#invoice').addClass('has-error');
		swal("กรุณาระบุใบกำกับภาษี");
		return false;
	}

	$('.input-qty').each(function() {
		let no = $(this).data('no');
		let el = $('#qty-'+no);
		let qty = parseDefault(parseFloat(el.val()), 0);

		if(qty > 0) {
			let limit = parseDefault(parseFloat(el.data('limit')), 0);
			let order_code = el.data('ordercode');
			let product_code = el.data('item');
			let product_name = el.data('itemname');
			let invoice_code = el.data('invoice');
			let price = parseDefault(parseFloat($('#price-'+no).val()), 0);
			let disc_percent = parseDefault(parseFloat($('#discount-'+no).val()), 0);
			let disc_amount = (disc_percent * 0.01) * price; //--- discount amount per item
			let amount = qty * (price - disc_amount);
			let rate = parseDefault(parseFloat(el.data('vatrate')), 7);

			if(limit >= 0 && qty > limit) {
				el.addClass('has-error');
				error++;
			}

			if(price < 0) {
				$('#price-'+no).addClass('has-error');
				error++;
			}

			let row = {
				"sold_qty" : limit,
				"qty" : qty,
				"receive_qty" : qty,
				"price" : price,
				"discount_percent" : disc_percent,
				"discount_amount" : disc_amount,
				"amount" : amount,
				"vat_rate" : rate,
				"product_code" : product_code,
				"product_name" : product_name,
				"invoice_code" : invoice_code,
				"order_code" : order_code
			}

			items.push(row);
		}
	})

	if(error > 0) {
		swal({
			title:'ข้อผิดพลาด !',
			text:'กรุณาแก้ไขรายการที่ผิดพลาด',
			type:'error'
		});

		return false;
	}

	if(items.length == 0) {
		swal("ไม่พบรายการรับคืน");
		return false;
	}

	let data = {
		"header" : h,
		"items" : items
	};

	load_in();

	$.ajax({
		url:HOME + 'save',
		type:'POST',
		cache:false,
		data:{
			'data' : JSON.stringify(data)
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(() => {
						viewDetail(ds.code);
					}, 1200)
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error'
					});
				}
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


function saveUpdate(isDraft = 0)
{
	let error = 0;
	let allow_no_inv = $('#allow-return-no-inv').val() == 1 ? true : false;
	let code = $('#code').val();

	let h = {
		"date_add" : $('#dateAdd').val(),
		"customer_code" : $('#customer_code').val(),
		"customer_name" : $('#customer_name').val(),
		"reference" : $('#reference').val(),
		"remark" : $('#remark').val(),
		"invoice_code" : $('#invoice').val(),
		"warehouse_code" : $('#warehouse').val(),
		"zone_code" : $('#zone_code').val(),
		"is_draft" : isDraft == 1 ? 1 : 0
	}

	let items = [];

	//--- clear error
	$('.h').removeClass('has-error');
	$('.r').removeClass('has-error');

	if( ! isDate(h.date_add)) {
		$('#dateAdd').addClass('has-error');
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	if(h.customer_code == "" || h.customer_name == "") {
		$('#customer_code').addClass('has-error');
		$('#customer_name').addClass('has-error');
		swal("กรุณาระบุลูกค้า");
		return false;
	}

	if(h.warehouse_code == "") {
		$('#warehouse').addClass('has-error');
		swal("กรุณาระบุคลังสินค้า");
		return false;
	}

	if(h.zone_code == "") {
		$('#zone_code').addClass('has-error');
		swal("กรุณาระบุโซนรับสินค้า");
		return false;
	}

	if( allow_no_inv == false && h.invoice_code == '') {
		$('#invoice').addClass('has-error');
		swal("กรุณาระบุใบกำกับภาษี");
		return false;
	}

	$('.input-qty').each(function() {
		let no = $(this).data('no');
		let el = $('#qty-'+no);
		let qty = parseDefault(parseFloat(el.val()), 0);

		if(qty > 0) {
			let limit = parseDefault(parseFloat(el.data('limit')), 0);
			let order_code = el.data('ordercode');
			let product_code = el.data('item');
			let product_name = el.data('itemname');
			let invoice_code = el.data('invoice');
			let price = parseDefault(parseFloat($('#price-'+no).val()), 0);
			let disc_percent = parseDefault(parseFloat($('#discount-'+no).val()), 0);
			let disc_amount = (disc_percent * 0.01) * price; //--- discount amount per item
			let amount = qty * (price - disc_amount);
			let rate = parseDefault(parseFloat(el.data('vatrate')), 7);

			if(limit >= 0 && qty > limit) {
				el.addClass('has-error');
				error++;
			}

			if(price < 0) {
				$('#price-'+no).addClass('has-error');
				error++;
			}

			let row = {
				"sold_qty" : limit,
				"qty" : qty,
				"receive_qty" : qty,
				"price" : price,
				"discount_percent" : disc_percent,
				"discount_amount" : disc_amount,
				"amount" : amount,
				"vat_rate" : rate,
				"product_code" : product_code,
				"product_name" : product_name,
				"invoice_code" : invoice_code,
				"order_code" : order_code
			}

			items.push(row);
		}
	})

	if(error > 0) {
		swal({
			title:'ข้อผิดพลาด !',
			text:'กรุณาแก้ไขรายการที่ผิดพลาด',
			type:'error'
		});

		return false;
	}

	if(items.length == 0) {
		swal("ไม่พบรายการรับคืน");
		return false;
	}

	let data = {
		"header" : h,
		"items" : items
	};

	load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'data' : JSON.stringify(data)
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(() => {
						viewDetail(ds.code);
					}, 1200)
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error'
					});
				}
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


function approve() {
	var code = $('#return_code').val();

	swal({
		title:'Approval',
		text:'ต้องการอนุมัติ '+code+' หรือไม่ ?',
		showCancelButton:true,
		confirmButtonColor:'#8bc34a',
		confirmButtonText:'อนุมัติ',
		cancelButtonText:'ยกเลิก',
		closeOnConfirm:true
	}, () => {
		let count = $('#approve-count').val();

		if(count != 0) {
			return false;
		}
		else {
			$('#approve-count').val(1);
		}
				
		load_in();

		$.ajax({
			url:HOME + 'approve/'+code,
			type:'GET',
			cache:false,
			success:function(rs) {
				load_out();

				if(rs === 'success') {
					setTimeout(() => {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(() => {
							window.location.reload();
						}, 1200);
					}, 200);
				}
				else {
					setTimeout(() => {
						swal({
							title:'Error!',
							text:rs,
							type:'errr'
						}, () => {
							window.location.reload();
						});
					}, 200);
				}
			}
		});
	});
}



function unapprove() {
	var code = $('#return_code').val();
	swal({
		title:'Warning',
		text:'ต้องการยกเลิกการอนุมัติ '+code+' หรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonColor:'#DD6B55',
		confirmButtonText:'Yes',
		cancelButtonText:'No',
		closeOnConfirm:true
	}, () => {
		load_in();

		$.ajax({
			url: HOME + 'unapprove/'+code,
			type:'GET',
			cache:false,
			success : function(rs) {
				load_out();
				if(rs === 'success') {
					setTimeout(() => {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(() => {
							window.location.reload();
						}, 1200);
					}, 200);
				}
				else {
					setTimeout(() => {
						swal({
							title:'Error',
							text:rs,
							type:'error'
						}, () => {
							window.location.reload();
						});
					}, 200);
				}
			}
		});
	});
}



function doExport(){
	var code = $('#return_code').val();
	$.get(HOME + 'export_return/'+code, function(rs){
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
		}else{
			swal({
				title:'Error!',
				text:rs,
				type:'error'
			});
		}
	});
}


function editHeader(){
	$('.edit').removeAttr('disabled');
	$('#btn-edit').addClass('hide');
	$('#btn-update').removeClass('hide');
}


function updateHeader(){
	var code = $('#return_code').val();
	var date_add = $('#dateAdd').val();
	var invoice = $('#invoice').val();
	var customer_code = $('#customer_code').val();
	var warehouse_code = $('#warehouse').val();
	var zone_code = $('#zone_code').val();
	var reqRemark = $('#required_remark').val();
  var remark = $.trim($('#remark').val());

	if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

	if(invoice.length == 0){
		swal('กรุณาอ้างอิงเลขที่บิล');
		return false;
	}

	if(customer_code.length == 0){
		swal('กรุณาอ้างอิงลูกค้า');
		return false;
	}

	if(warehouse_code.length == 0){
		swal('กรุณาระบุคลังสินค้า');
		return false;
	}

	if(zone_code.length == 0){
		swal('กรุณาระบุโซนรับสินค้า');
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

  load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'return_code' : code,
			'date_add' : date_add,
			'invoice' : invoice,
			'customer_code' : customer_code,
			'warehouse_code' : warehouse_code,
			'zone_code' : zone_code,
			'remark' : remark
		},
		success:function(rs){
			load_out();

			if(rs == 'success') {
				$('.edit').attr('disabled', 'disabled');
				$('#btn-update').addClass('hide');
				$('#btn-edit').removeClass('hide');

				swal({
					title:'Success',
					text:'ต้องการโหลดข้อมูลรายการสินค้าใหม่หรือไม่ ?',
					type: 'success',
					showCancelButton: true,
					cancelButtonText: 'No',
					confirmButtonText: 'Yes',
					closeOnConfirm: true
				}, function() {
					load_in();
					window.location.reload();
				});
			}
			else
			{
				swal({
					title:'Error!!',
					text:rs,
					type:'error'
				});
			}
		}
	})
}



$('#dateAdd').datepicker({
	dateFormat:'dd-mm-yy'
});



function addNew()
{
	let is_allow = $('#allow-return-no-inv').val();
  let date_add = $('#dateAdd').val();
	let invoice = $('#invoice').val();
	let customer_code = $('#customer_code').val();
	let warehouse_code = $('#warehouse').val();
	let zone_code = $('#zone_code').val();
	let remark = $('#remark').val();


  if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

	if(is_allow == 0) {
		if(invoice.length == 0){
			swal('กรุณาอ้างอิงเลขที่บิล');
			return false;
		}
	}

	if(customer_code.length == 0){
		swal('กรุณาอ้างอิงลูกค้า');
		return false;
	}

	if(warehouse_code.length == 0) {
		swal('กรุณาระบุคลัง');
		return false;
	}

	if(zone_code.length == 0){
		swal('กรุณาระบุโซนรับสินค้า');
		return false;
	}

	let h = {
		'date_add' : date_add,
		'invoice' : invoice,
		'customer_code' : customer_code,
		'warehouse_code' : warehouse_code,
		'zone_code' : zone_code,
		'remark' : remark
	};

  $.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data: {
			'data' : JSON.stringify(h)
		},
		success:function(rs) {
			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					goEdit(ds.code);
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error',
						html:true
					});
				}
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


function zoneInit(clearZone = 0) {
	var whsCode = $('#warehouse').val();

	if(clearZone == 1) {
		$('#zone_code').val('');
		$('#zone').val('');
	}

	if(whsCode.length) {
		$('#zone_code').autocomplete({
			source : BASE_URL + 'auto_complete/get_zone_code_and_name/'+whsCode,
			autoFocus:true,
			close:function(){
				var arr = $(this).val().split(' | ');
				if(arr.length == 2){
					$('#zone').val(arr[1]);
					$('#zone_code').val(arr[0]);
				}else{
					$('#zone').val('');
					$('#zone_code').val('');
				}
			}
		});


		$('#zone').autocomplete({
			source : BASE_URL + 'auto_complete/get_zone_code_and_name/'+whsCode,
			autoFocus:true,
			close:function(){
				var arr = $(this).val().split(' | ');
				if(arr.length == 2){
					$('#zone').val(arr[1]);
					$('#zone_code').val(arr[0]);
				}else{
					$('#zone').val('');
					$('#zone_code').val('');
				}
			}
		});
	}
}

function invoiceInit(clear = 0) {
	if(clear == 1) {
		$('#invoice').val('');
	}

	var customerCode = $('#customer_code').val();

	$('#invoice').autocomplete({
		source:BASE_URL + 'auto_complete/get_invoice_code/'+customerCode,
		autoFocus:true,
		open:function(event) {
			var $ul = $(this).autocomplete('widget');
			$ul.css('width', 'auto');
		},
		select:function(event, ui) {
			let code = ui.item.inv_code;
			let cardCode = ui.item.customer_code;
			let cardName = ui.item.customer_name;

			if(code != "" && code != undefined) {
				console.log(code);
				$('#customer_code').val(cardCode);
				$('#customer_name').val(cardName);

				setTimeout(() => {
					$(this).val(code);
				}, 100);
			}
			else {
				setTimeout(() => {
					$(this).val('');
				}, 100);
			}
		}
	})
}


$('#customer_code').change(function() {
	setTimeout(() => {
		if($(this).val() == '') {
			$('#customer_name').val('');
		}
	}, 100)

	invoiceInit(1);
})

$('#customer_code').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#customer_code').val(arr[0]);
			$('#customer_name').val(arr[1]);
			invoiceInit(1);
		}
		else {
			$('#customer_code').val('');
			$('#customer_name').val('');
			invoiceInit(1);
		}
	}
});

$('#customer_name').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#customer_code').val(arr[0]);
			$('#customer_name').val(arr[1]);
			invoiceInit(1);
		}
		else {
			$('#customer_code').val('');
			$('#customer_name').val('');
			invoiceInit(1);
		}
	}
});

function recalRow(no) {
	let el = $('#qty-'+no);
	let pr = $('#price-'+no);
	let dc = $('#discount-'+no);

	el.removeClass('has-error');
	pr.removeClass('has-error');

	let price = parseDefault(parseFloat(pr.val()), 0);
	let qty = parseDefault(parseFloat(el.val()), 0);
	let limit = parseDefault(parseFloat(el.data('limit')), 0);
	let discount = parseFloat(dc.val()) * 0.01;
	let discAmount = discount = qty * (price * discount);
	let amount = (qty * price) - discount;
	amount = amount.toFixed(2);

	$('#amount-' + no).text(addCommas(amount));

	if(limit >= 0 && qty > limit) {
		el.addClass('has-error');
	}

	if(price < 0) {
		pr.addClass('has-error');
	}

	recalTotal();
}



function recalTotal() {
	var totalAmount = 0;
	var totalQty = 0;

	$('.amount-label').each(function() {
		let amount = removeCommas($(this).text());
		amount = parseDefault(parseFloat(amount), 0);
		totalAmount += amount;
	});

	$('.input-qty').each(function(){
		qty = parseDefault(parseFloat($(this).val()), 0);
		totalQty += qty;
	});

	totalQty = totalQty.toFixed(2);
	totalAmount = totalAmount.toFixed(2);

	$('#total-qty').text(addCommas(totalQty));
	$('#total-amount').text(addCommas(totalAmount));
}



function removeRow(no, id){
	if(id != '' && id != '0' && id != 0){
		$.ajax({
			url:HOME + 'delete_detail/'+id,
			type:'GET',
			cache:false,
			success:function(rs){
				if(rs == 'success'){
					$('#row_' + no).remove();
					//reIndex();
					//recalTotal();
				}
				else
				{
					swal(rs);
					return false;
				}
			}
		});
	}
	else
	{
		$('#row_'+no).remove();
		// reIndex();
		// recalTotal();
	}
}


function accept() {
	$('#accept-modal').on('shown.bs.modal', () => $('#accept-note').focus());
	$('#accept-modal').modal('show');
}

function acceptConfirm() {
	let code = $('#return_code').val();
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

			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(() => {
					window.location.reload();
				}, 1200);
			}
			else {
				swal({
					title:'Error!',
					text: rs,
					type:'error'
				});
			}
		}
	});

}


$(document).ready(function(){
	load_out();
});
