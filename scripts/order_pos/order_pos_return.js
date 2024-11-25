window.addEventListener('load', () => {
	let height = $(window).height();
	let pageContentHeight = height - 53;

	let billTableHeight = pageContentHeight - (195); //-- 155
	let billViewHeight = pageContentHeight - (75);

	$('.page-content').css('height', pageContentHeight + 'px');
	$('#bill-div').css('height', billTableHeight + 'px');
	$('#bill-view').css('height', billViewHeight + 'px');
})

$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#toDate').datepicker("option", "minDate", sd);
  }
});

$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#fromDate').datepicker("option", "maxDate", sd);
  }
});

function return_list() {
	let shop_id = $('#shop_id').val();
	let deviceId = getDeviceId();
	window.location.href = HOME + 'return_list/'+shop_id+'/'+deviceId;
}

function getSearch() {
	$('#searchForm').submit();
}

function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){ return_list(); });
}

function getReturnView(code) {
	load_in();

	$.ajax({
		url:BASE_URL + 'orders/order_pos/get_return_view',
		type:'GET',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					let data = ds.data;
					let source = $('#return-view-template').html();
					let output = $('#bill-view');

					render(source, data, output);
				}
			}
		}
	})
}


function printReturn(code) {
	let width = 400;
	let height = 600;
	let center = (window.innerWidth - width)/2;
	let prop = "width="+width+", height="+height+", left="+center+", top=100, scrollbars=yes";
	let target = HOME + 'print_return/'+code;
	window.open(target, '_blank', prop);
}


function cancelReturn(code, id) {
	swal({
		title:'ยกเลิกบิล',
		text:'ต้องการยกเลิกบิล '+code+' หรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		cancelButtonText:'No',
		confirmButtonText:'Yes',
		confirmButtonColor:'#d15b47',
		closeOnConfirm:true
	},
	function() {
		$('#cancel-id').val(id);
		$('#cancel-code').val(code);
		$('#cancel-reason').val('');

		setTimeout(() => {
			cancelReason();
		}, 200);
	});
}


function cancelReason() {
	$('#cancelModal').modal('show');
}

$('#cancelModal').on('shown.bs.modal', function() {
	$('#cancel-reason').focus();
});


function doCancel() {
	let id = $('#cancel-id').val();
	let code = $('#cancel-code').val();
	let reason = $.trim($('#cancel-reason').val());

	if(reason.length < 10) {
		$('#cancel-reason').addClass('has-error').focus();
		return false;
	}
	else {
		$('#cancel-reason').removeClass('has-error');
	}

	$('#cancelModal').modal('hide');

	load_in();

	setTimeout(() => {
		$.ajax({
			url:BASE_URL + 'orders/order_pos_return/cancel_bill',
			type:'POST',
			cache:false,
			data:{
				'code' : code,
				'id' : id,
				'reason' : reason
			},
			success:function(rs) {
				load_out();

				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status == 'success') {
						swal({
							title:'Success',
							text:'ยกเลิกบิลเรียบร้อยแล้ว',
							type:'success',
							timer:1000
						});

						$('#row-'+id).addClass('red');
						$('#status-'+id).text('Canceled');
						$('#chk-'+id).remove();
						$('#btn-print').remove();
						$('#btn-cancel').remove();
						$('#btn-invoice').remove();
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
						type:'error'
					});
				}
			},
			error:function(xhr) {
				load_out();
				swal({
					title:'Error!',
					text:xhr.responseText,
					type:'error',
					html:true
				});
			}
		});
	}, 200);
}


function getReturnBill() {
	let pos_id = $('#pos_id').val();
	let shop_id = $('#shop_id').val();

	$('#bill-result-table').html('');
	$('#bill-view-table').html('');
	$('#bill-search').val('');

	billInit(shop_id, pos_id);

  $('#returnModal').modal('show');
}


$('#returnModal').on('shown.bs.modal', function() {
  $('#bill-search').focus();
});

function billInit(shop_id, pos_id) {
	$('#bill-search').autocomplete({
		source:HOME + 'search_bill_code/'+shop_id+'/'+pos_id,
		autoFocus:true
	});
}


function getBillView() {
  let code = $('#bill-search').val();

  if(code.length < 7) {
    $('#bill-search').addClass('has-error');
    return false;
  }
	else {
		$('#bill-search').removeClass('has-error');
	}

	load_in();

  $.ajax({
    url:HOME + 'get_bill_view',
    type:'GET',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
			load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
					$('#bill-result-table').html('');
					let data = ds.data;
					let source = $('#bill-view-template').html();
					let output = $('#bill-view-table');

					render(source, data, output);

					if(ds.data.status == 'D') {
						$('#btn-add-return').attr('disabled', 'disabled');
					}
					else {
						$('#btn-add-return').removeAttr('disabled');
					}
        }
        else {
					result = '<h4 class="title-xs red text-center">'+ds.message+'</h4>';
          $('#bill-result-table').html(result);
					$('#bill-view-table').html('');
					$('#bill-search').focus();
        }
      }
      else {
				result = '<h4 class="title-xs red text-center">'+rs+'</h4>';
				$('#bill-result-table').html(result);
				$('#bill-view-table').html('');
				$('#bill-search').focus();
      }
    }
  })
}


function newReturn() {
	let bill_id = $('#selected-bill-id').val();
	let bill_code = $('#selected-bill-code').val();
	let status = $('#selected-bill-status').val();

	if(status == 'D') {
		console.log(status);
		return false;
	}

	if(bill_id == "" || bill_id == undefined) {
		console.log(bill_id);
		return false;
	}

	window.location.href = HOME + 'return_bill/'+bill_id;
}


function toggleCheckAll(el) {
	if(el.is(':checked')) {
		$('.chk').prop('checked', true);
	}
	else {
		$('.chk').prop('checked', false);
	}
}


function createCN() {
	let shop_id = $('#shop_id').val();
	let bills = [];

	$('.chk').each(function() {
		if($(this).is(':checked')) {
			bills.push($(this).val());
		}
	});

	if(bills.length == 0) {
		swal("กรุณาเลือกรายการ");
		return false;
	}

	swal({
		title:'สร้างเอกสารลดหนี้',
		text:'รายการที่เลือกจำนวน '+bills.length+' รายการ <br/> ต้องการดำเนินการต่อหรือไม่ ?',
		type:'info',
		html:true,
		showCancelButton:true,
		cancelButtonText:'ยกเลิก',
		confirmButtonText:'ดำเนินการ',
		closeOnConfirm:true
	},
	function() {
		setTimeout(() => {
			load_in();
			let data = {
				'shop_id' : shop_id,
				'bills' : bills
			};

			$.ajax({
				url:BASE_URL + 'orders/order_pos_return/create_cn',
				type:'POST',
				cache:false,
				data: {
					'data' : JSON.stringify(data)
				},
				success:function(rs) {
					load_out();

					if(isJson(rs)) {
						let ds = JSON.parse(rs);

						if(ds.status == 'success') {
							let width = window.innerWidth - 200;
							let height = window.innerHeight - 100;
							let center = (window.innerWidth - width)/2;
							let prop = "width="+width+", height="+height+", left="+center+", top=40, scrollbars=yes";
							let target = BASE_URL + 'inventory/return_order/edit/'+ds.code+'?nomenu';
							window.open(target, '_blank', prop);

							setTimeout(() => {
								window.location.reload();
							}, 1000);
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
				},
				error:function(xhr) {
					load_out();
					swal({
						title:'Error!',
						text:xhr.responseText,
						type:'error',
						html:true
					});
				}
			})
		}, 200);
	})
}


$('#bill-search').keydown(function(e) {
	if(e.keyCode == 13) {
		setTimeout(() => {
			getBillView();
		}, 200);
	}
});

$('#date_add').datepicker({
	dateFormat:'dd-mm-yy'
});

$('#barcode').keyup(function(e) {
	if(e.keyCode == 13) {
		doRecieve();
	}
});


function doRecieve() {
	let barcode = $('#barcode').val();
	$('#barcode').val('');

	let qty = 1;
	if(barcode.length) {
		let bCode = md5(barcode);
		if($("."+bCode).length > 0) {
			$('.'+bCode).each(function() {
				if(qty > 0) {
					let limit = parseDefault(parseFloat($(this).data('limit')), 0);
					let cQty = parseDefault(parseFloat($(this).val()), 0);

					if(limit > cQty) {
						cQty++;
						qty--;

						$(this).val(cQty);

						recalAmount($(this).data('id'));
					}
				}
			});

			if(qty > 0) {
				beep();
				swal({
					title:'Oops!',
					text:"สินค้าเกิน",
					type:'warning'
				}, function() {
					setTimeout(() => {
						$('#barcode').focus();
					}, 200);
				})
			}
			else {
				$('#barcode').focus();
			}
		}
		else {
			beep();
			swal({
				title:'Oops!',
				text:'บาร์โค้ดไม่ถูกต้อง',
				type:'warning'
			}, function() {
				setTimeout(() => {
					$('#barcode').focus();
				}, 200);
			});
		}
	}
}

$('.return-qty').keydown(function(e) {
	if(e.keyCode == 40 || e.keyCode == 13) {
		//--- key down
		e.preventDefault();
		let el = $(this);
		let no = parseDefault(parseInt(el.data('no')), 1);
		no++;
		if($('.no-'+no).length) {
			$('.no-'+no).focus();
		}
	}

	if(e.keyCode == 38) {
		//--- key down
		e.preventDefault();
		let el = $(this);
		let no = parseDefault(parseInt(el.data('no')), 1);
		no--;
		if($('.no-'+no).length) {
			$('.no-'+no).focus();
		}
	}
});

$('.return-qty').keyup(function(e) {
	let el = $(this);
	let qty = parseDefault(parseFloat(el.val()), 0.00);
	let price = parseDefault(parseFloat(el.data('price')), 0.00);
	let limit = parseDefault(parseFloat(el.data('limit')), 0.00);
	recalAmount(el.data('id'));
	if(limit < qty) {
		el.addClass('has-error');

		beep();
		swal({
			title:'Oops!',
			text:"สินค้าเกิน",
			type:'warning'
		}, function() {
			setTimeout(() => {
				el.focus().select();
			}, 200);
		})
	}
	else {
		el.removeClass('has-error');
	}
})

function recalAmount(id) {
	let el = $('#return-qty-'+id);
	let qty = parseDefault(parseFloat(el.val()), 0.00);
	let price = parseDefault(parseFloat(el.data('price')), 0.00);

	let amount = qty * price;

	$('#return-amount-'+id).text(addCommas(amount.toFixed(2)));

	recalTotal();
}


function recalTotal() {
	let totalQty = 0.00;
	let totalAmount = 0.00;

	$('.return-qty').each(function() {
		let id = $(this).data('id');
		let qty = parseDefault(parseFloat($(this).val()), 0);
		let amount = $('#return-amount-'+id).text();
		if(amount.length) {
			amount = parseDefault(parseFloat(removeCommas(amount)), 0.00);
		}
		totalQty += qty;
		totalAmount += amount;
	});

	$('#total-qty').text(addCommas(totalQty.toFixed(2)));
	$('#total-amount').text(addCommas(totalAmount.toFixed(2)));
}

function getApprove() {
	var initialData = {
		'title' : 'อนุมัติรับคืนสินค้า (POS)',
		'menu' : 'SOPOSRT',
		'field' : 'can_approve',
		'callback' : function() {
			addReturn();
		}
	}

	showValidateBox(initialData);
}


function saveReturn() {
	$('#remark').removeClass('has-error');
	$('.return-qty').removeClass('has-error');

	let remark = $.trim($('#remark').val());

	if(remark.length < 10) {
		$('#remark').addClass('has-error');

		swal("กรุณาระบุหมายในการยกเลิก");

		return false;
	}

	let error = 0;

	let items = 0;

	$('.return-qty').each(function() {
		let el = $(this);
		let id = el.data('id');
		let pdCode = el.data('pd');
		let qty = parseDefault(parseFloat(el.val()), 0);
		let price = parseDefault(parseFloat(el.data('price')), 0.00);
		let limit = parseDefault(parseFloat(el.data('limit')), 0.00);

		if(qty > 0) {
			if(qty > limit) {
				error++;
				el.addClass('has-error');
			}
			else {
				items++;
			}
		}
	});

	if(error > 0) {
		swal({
			title: 'Error!',
			text: 'กรุณาแก้ไขรายการที่ไม่ถูกต้อง',
			type:'error'
		});

		return false;
	}

	if(items == 0) {
		swal({
			title:'Not found',
			text:'ไม่พบรายการรับคืน',
			type:'warning'
		});

		return false;
	}

	swal({
		title:'บันทึกรับคืน',
		text:'ต้องการบันทึกเอกสารนี้หรือไม่ ?',
		type:'info',
		showCancelButton:true,
		confirmButtonText:'ยืนยัน',
		cancelButtonText:'ยกเลิก',
		closeOnConfirm:true
	}, function() {
		setTimeout(() => {
			getApprove();
		}, 200);
	});
}


function addReturn() {
	let error = 0;
	let ds = {
		'bill_id' : $('#bill-id').val(),
		'bill_code' : $('#bill-code').val(),
		'payment_role' : $('#payment-role').val(),
		'remark' : $.trim($('#remark').val()),
		'approver' : $('#approverName').val()
	}

	let items = [];

	$('.return-qty').each(function() {
		let el = $(this);
		let id = el.data('id');
		let pdCode = el.data('pd');
		let qty = parseDefault(parseFloat(el.val()), 0);
		let price = parseDefault(parseFloat(el.data('price')), 0.00);
		let limit = parseDefault(parseFloat(el.data('limit')), 0.00);

		if(qty > 0) {
			if(qty > limit) {
				error++;
				el.addClass('has-error');
			}
			else {
				item = {
					"id" : id,
					"product_code" : pdCode,
					"qty" : qty,
					"amount" : qty * price
				}

				items.push(item);
				el.removeClass('has-error');
			}
		}
	});

	if(items.length > 0) {
		ds['items'] = items;
	}

	if(error > 0) {
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + '/add_return',
		type:'POST',
		cache:false,
		data:{
			'data' : JSON.stringify(ds)
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
						window.location.href = HOME + 'return_detail/'+ds.return_id;
					}, 1200);
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
		},
		error:function(xhr) {
			load_out();

			swal({
				title:'Error!',
				text:xhr.responseText,
				type:'error',
				html:true
			});
		}
	})
}
