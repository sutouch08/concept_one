function toggleCheckAll(el) {
	if (el.is(":checked")) {
		$('.chk').prop("checked", true);
	} else {
		$('.chk').prop("checked", false);
	}
}


function toggleInterface() {
	var wms = $('#is_wms').val();
	if(wms == "1") {
		$('#is_api').val(1);
	}
	else {
		$('#is_api').val(0);
	}
}


function deleteChecked(){
	var count = $('.chk:checked').length;
	if(count > 0){
		load_in();
		$('.chk:checked').each(function(){
			var id = $(this).data('id');
			var no = $(this).val();
			count--;
			removeRow(no, id, count);
		})
	}
}


function unsave(){
	var code = $('#return_code').val();
	$.ajax({
		url:HOME + 'unsave/'+code,
		type:'POST',
		cache:false,
		success:function(rs){
			if(rs === 'success'){
				swal({
					title:'Success',
					text:'ยกเลิกการบันทึกเรียบร้อยแล้ว',
					type:'success',
					time:1000
				});

				setTimeout(function(){
					goEdit(code);
				}, 1500);
			}
		}
	})
}


function save()
{
	var error = 0;
	var count = 0;
	var items = [];
	$('.input-qty').each(function(){
		let no = $(this).data("no");
		let qty = parseDefault(parseFloat($(this).val()), 0);

		if(qty > 0) {
			let item_code = $('#item_'+no).val();
			let item_name = $('#item_name_'+no).val();
			let price = parseDefault(parseFloat($('#price_'+no).val()), 0);
			let discount = parseDefault(parseFloat($('#discount_'+no).val()), 0);

			var ds = {
				"item_code" : item_code,
				"item_name" : item_name,
				"price" : price.toFixed(2),
				"discount" : discount.toFixed(2),
				"qty" : qty
			}

			items.push(ds);

			count++;
		}
	})

	if(count == 0) {
		swal("ไม่พบรายการคืนสินค้า", "", "error");
		return false;
	}

	var code = $('#return_code').val();
	var data = {"code" : code, "items" : items}
	load_in();
	$.ajax({
		url:HOME + 'add_details',
		type:'POST',
		cache:false,
		data:JSON.stringify(data),
		contentType: 'application/json',
		complete:function(rs) {
			load_out();
			if(rs.responseText === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(function() {
					viewDetail(code);
				}, 1200);

			}
			else {
				if(rs.status == 200) {
					swal({
						title:"Error!",
						text:rs.responseText,
						type:'error'
					});
				}
				else {
					swal({
						title:"Error!",
						text:"Error-" + rs.status + " : Internal server error",
						type:'error',
						html:true
					});
				}
			}
		}
	})
}



function approve(){
	var code = $('#return_code').val();
	load_in();
	$.get(HOME+'approve/'+code, function(rs){
		if(rs === 'success'){
			load_out();
			swal({
				title:'Success',
				type:'success',
				timer: 1000
			});

			setTimeout(function(){
				window.location.reload();
			}, 1000);

		}else{
			swal({
				title:'Error',
				text:rs,
				type:'error'
			})
		}
	});
}



function doExport(){
	var code = $('#return_code').val();
	load_in();
	$.ajax({
		url:HOME + 'export_return/'+code,
		type:'GET',
		cache:false,
		success:function(rs) {
			load_out();
			if(rs == 'success') {
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
			else {
				swal({
					title:"Error!",
					text:rs,
					type:'error'
				})
			}
		}
	})
}


function sendToWms() {
	var code = $('#return_code').val();

	load_in();
	$.ajax({
		url:HOME + 'send_to_wms',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
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
	var is_wms = $('#is_wms').val();
	var is_api = $('#is_api').val();
	var warehouse_code = $('#warehouse_code').val();
	var zone_code = $('#zone_code').val();
	var from_warehouse_code = $('#from_warehouse_code').val();
	var from_zone_code = $('#from_zone_code').val();
  var remark = $('#remark').val();
	var gp = $('#gp').val();

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

	if(zone_code.length == 0){
		swal('กรุณาระบุโซนรับสินค้า');
		return false;
	}

	if(from_zone_code.length == 0){
		swal('กรุณาระบุโซนฝากขาย');
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
			'is_wms' : is_wms,
			'is_api' : is_api,
			'warehouse_code' : warehouse_code,
			'zone_code' : zone_code,
			'from_zone' : from_zone_code,
			'remark' : remark,
			'gp' : gp
		},
		success:function(rs){
			load_out();
			if(rs == 'success'){
				$('.edit').attr('disabled', 'disabled');
				$('#btn-update').addClass('hide');
				$('#btn-edit').removeClass('hide');

				swal({
					title:'Success',
					type: 'success',
					timer:1000
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


function invoice_init()
{
	var customer_code = $('#customer_code').val();
	$('#invoice').autocomplete({
		source:HOME + 'search_invoice_code',
		autoFocus:true,
		close:function(){
			var rs = $.trim($(this).val());
			var arr = rs.split(' | ');
			if(arr.length == 2)
			{
				$(this).val(arr[0]);
			}
		}
	})
}




function addNew()
{
  var date_add = $('#dateAdd').val();
	var invoice = $('#invoice').val();
	var is_wms = $('#is_wms').val();
	var is_api = $('#is_api').val();
	var gp = $('#gp').val();
	var customer_code = $('#customer_code').val();
	var from_zone = $('#from_zone_code').val();
	var zone_code = $('#zone_code').val();
	var remark = $('#remark').val();

  if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

	if(invoice.length == 0){
		swal('กรุณาอ้างอิงเลขที่บิล');
		return false;
	}

	if(customer_code.length == 0){
		swal('กรุณาระบุลูกค้า');
		return false;
	}

	if(from_zone.length == 0){
		swal('กรุณาระบุโซนฝากขาย');
		return false;
	}

	if(zone_code.length == 0){
		swal('กรุณาระบุโซนรับสินค้า');
		return false;
	}

	load_in();
	$.ajax({
		url: HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'date_add' : date_add,
			'invoice' : invoice,
			'is_wms' : is_wms,
			'is_api' : is_api,
			'gp' : gp,
			'customer_code' : customer_code,
			'from_zone' : from_zone,
			'zone_code' : zone_code,
			'remark' : remark
		},
		success:function(rs){
			load_out();
			var rs = $.parseJSON(rs);
			if(rs.status === 'success'){
				goEdit(rs.code);
			}else{
				swal({
					title:'Error',
					text:rs.message,
					type:'error'
				})
			}
		}
	});
}


$('#customer').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#customer_code').val(arr[0]);
			$('#customerCode').val(arr[0]);
			$('#customer').val(arr[1]);
		}else{
			$('#customer_code').val('');
			$('#customerCode').val('');
			$('#customer').val('');
		}

		fromZoneInit();
		invoice_box_init();
		invoiceInit();
	}
});


$('#customerCode').autocomplete({
	source:BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$('#customer_code').val(arr[0]);
			$('#customerCode').val(arr[0]);
			$('#customer').val(arr[1]);
		}else{
			$('#customer_code').val('');
			$('#customerCode').val('');
			$('#customer').val('');
		}

		fromZoneInit();
		invoice_box_init();
		invoiceInit();
	}
});


function fromZoneInit(){
	var customer = $('#customer_code').val();
	$('#fromZone').autocomplete({
		source : BASE_URL + 'auto_complete/get_consignment_zone/'+customer,
		autoFocus:true,
		close:function(){
			var arr = $(this).val().split(' | ');
			if(arr.length == 2){
				$(this).val(arr[1]);
				$('#from_zone_code').val(arr[0]);
			}else{
				$(this).val('');
				$('#from_zone_code').val('');
			}
		}
	});
}




$('#zone').autocomplete({
	source : BASE_URL + 'auto_complete/get_common_zone_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$(this).val(arr[1]);
			$('#zone_code').val(arr[0]);
		}else{
			$(this).val('');
			$('#zone_code').val('');
		}
	}
})



function recalRow(no) {
	var price = parseFloat($('#price_' + no).val());
	var qty = parseFloat($('#qty_'+no).val());
	var discount = parseFloat($('#discount_' + no).val()) * 0.01;
	price = isNaN(price) ? 0 : price;
	qty = isNaN(qty) ? 0 : qty;
	discount = qty * (price * discount);
	var amount = (qty * price) - discount;
	amount = amount.toFixed(2);
	$('#amount_' + no).text(addCommas(amount));
	recalTotal();
}


function recalTotal(){
	var totalAmount = 0;
	var totalQty = 0;
	$('.amount-label').each(function(){
		let amount = removeCommas($(this).text());
		amount = parseFloat(amount);
		totalAmount += amount;
	});

	$('.input-qty').each(function(){
		let qty = $(this).val();
		qty = parseFloat(qty);
		totalQty += qty;
	});

	$('#total-qty').text(addCommas(totalQty));
	$('#total-amount').text(addCommas(totalAmount.toFixed(2)));
}



function removeRow(no, id, count){
	if(id != '' && id != '0' && id != 0){
		$('#row_' + no).remove();
		if(count == 1) {
			reIndex();
			recalTotal();
			load_out();
		}
	}
	else
	{
		$('#row_'+no).remove();
		reIndex();
		recalTotal();
	}
}


function load_stock_in_zone(){
	swal({
    title: "นำเข้าสินค้าในโซน",
		text: "รายการที่มีอยู่ในเอกสารจะถูกลบแล้วแทนที่ด้วยสินค้าในโซน <br/> ต้องการดำเนินการหรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonText: 'ดำเนินการ',
		cancelButtonText: 'ยกเลิก',
		html:true,
		closeOnConfirm: false
  },function(){
    var code = $('#return_code').val();
    load_in();
    $.ajax({
      url: HOME + 'load_stock_in_zone',
      type:'POST',
      cache:'false',
      data:{
        'code' : code
      },
      success:function(rs){
        load_out();
        var rs = $.trim(rs);
        if(rs == 'success'){
          swal({
            title: 'Success',
            type:'success',
            timer:1000
          });

          setTimeout(function(){
            window.location.reload();
          },1500);
        }else{
          swal('Error!', rs, 'error');
        }
      }
    });

  });//--- swal
}



function invoice_box_init() {
	var customer_code = $('#customer_code').val();
	$('#invoice-box').autocomplete({
		source: BASE_URL + 'auto_complete/get_sap_invoice_code/'+customer_code,
		autoFocus:true,
		close:function() {
			var arr = $(this).val().split(' | ');
			if(arr.length === 2) {
				$(this).val(arr[0]);
			}
			else {
				$(this).val('');
			}
		}
	})
}


function get_invoice_gp(invoice) {
	$.ajax({
		url:HOME + 'get_invoice_gp',
		type:'GET',
		cache:false,
		data:{
			'invoice' : invoice
		},
		success:function(rs) {
			var arr = rs.split(' | ');
			if(arr.length == 2) {
				$('#gp').val(arr[1]);
			}
			else {
				$('#gp').val("");
			}
		}
	})
}



function invoiceInit() {
	var customer_code = $('#customer_code').val();
	$('#invoice').autocomplete({
		source: BASE_URL + 'auto_complete/get_sap_invoice_code/'+customer_code,
		autoFocus:true,
		close:function() {
			var arr = $(this).val().split(' | ');
			if(arr.length === 2) {
				$(this).val(arr[0]);
				get_invoice_gp(arr[0]);
			}
			else {
				$(this).val('');
			}
		}
	})
}


$(document).ready(function(){
	fromZoneInit();
	invoice_box_init();
	invoiceInit();
})
