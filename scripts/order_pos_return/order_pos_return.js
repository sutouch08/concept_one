var HOME = BASE_URL + 'orders/order_pos_return/';

window.addEventListener('load', () => {
	let height = $(window).height();
	let pageContentHeight = height - 45;

	let billTableHeight = pageContentHeight - (198); //-- 155
	let billViewHeight = pageContentHeight - (134);

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


function getSearch() {
	$('#searchForm').submit();
}

function goBack() {
	window.location.href = HOME;
}

function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs) {
		goBack();
	});
}

function getReturnView(code, id) {
	$('.bill-row').removeClass('active-row');

	$('#row-'+id).addClass('active-row');
	load_in();

	$.ajax({
		url:HOME + 'get_return_view',
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
	let target = BASE_URL + 'orders/order_pos/print_return/'+code;
	window.open(target, '_blank', prop);
}

function createCN() {
	let shop_id = $('#shop_id').val();

	if(shop_id == "") {
		swal("กรุณาระบุจุดการขาย");
		return false;
	}

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
				url:HOME + 'create_cn',
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
	let payment_role = $("input[type='radio'][name='payment_role']:checked").val();

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
			url:HOME + 'cancel_bill',
			type:'POST',
			cache:false,
			data:{
				'code' : code,
				'id' : id,
				'reason' : reason,
				'return_payment_role' : payment_role
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


function showInvoiceCustomer() {
  $('#invoiceCustomerModal').modal('show');
}

$('#invoiceCustomerModal').on('shown.bs.modal', function() {
  $('#tax-search').focus();
});

function getCustomerByTaxId() {
  let taxid = $('#tax-search').val();

  if(taxid.length < 13) {
    $('#tax-search').addClass('has-error');
    return false;
  }
	else {
		$('#tax-search').removeClass('has-error');
	}

  $.ajax({
    url:BASE_URL + 'orders/order_pos_invoice/get_invoice_customer_by_tax',
    type:'GET',
    cache:false,
    data:{
      'tax_id' : taxid
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
					$('.cust-form').attr('disabled', 'disabled');
					$('#cust-id').val(ds.data.id);
					$('#name').val(ds.data.name);
					$('#tax-id').val(ds.data.tax_id);
					$('#branch-code').val(ds.data.branch_code);
					$('#branch-name').val(ds.data.branch_name);
					$('#address').val(ds.data.address);
					$('#phone').val(ds.data.phone);

					if(ds.data.is_company == 1) {
						$('#is-company').prop('checked', true);
					}
					else {
						$('#is-company').prop('checked', false);
					}
        }
        else {
					result = '<h4 class="title-xs red text-center">'+ds.message+'</h4>';
          $('#cust-result-table').html(result);
					$('.cust-form').val('').attr('disabled', 'disabled');
					$('#tax-search').focus();
        }
      }
      else {
				result = '<h4 class="title-xs red text-center">'+rs+'</h4>';
				$('#cust-result-table').html(result);
				$('.cust-form').val('').attr('disabled', 'disabled');
				$('#tax-search').focus();
      }
    }
  })
}

function addInvoice() {
	let custId = $('#cust-id').val();
	let name = $.trim($('#name').val());
	let taxId = $('#tax-id').val();
	let bCode = $.trim($('#branch-code').val());
	let bName = $.trim($('#branch-name').val());
	let address = $.trim($('#address').val());
	let phone = $('#phone').val();
	let isCompany = $('#is-company').is(':checked') ? 1 : 0;
	let bill_code = $('#selected-bill-code').val();
	let bill_id = $('#selected-bill-id').val();

	if(custId == "") {
		$('.cust-form').removeClass('has-error');

		if(name.length == 0) {
			$('#name').addClass('has-error');
			return false;
		}

		if(taxId.length < 13) {
			$('#tax-id').addClass('has-error');
			return false;
		}

		if(isCompany && (bCode.length == 0 || bName.length == 0)) {
			if(bCode.length == 0) {
				$('#branch-code').addClass('has-error');
			}

			if(bName.length == 0) {
				$('#branch-name').addClass('has-error');
			}

			return false;
		}

		if(address.length < 10) {
			$('#address').addClass('has-error');
			return false;
		}
	}


	let data = {
		'bill_code' : bill_code,
		'bill_id' : bill_id,
		'customer_id' : custId,
		'name' : name,
		'tax_id' : taxId,
		'branch_code' : bCode,
		'branch_name' : bName,
		'address' : address,
		'phone' : phone,
		'is_company' : isCompany
	}

	closeModal('invoiceCustomerModal');

	setTimeout(() => {
		load_in();

		$.ajax({
			url:BASE_URL + 'orders/order_pos_invoice/add_invoice',
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
							printInvoice(ds.invoice_code);
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
						type:'error'
					})
				}
			}
		})
	}, 200);
}


function printInvoice(code) {
	let width = 800;
	let height = 800;
	let center = (window.innerWidth - width)/2;
	let prop = "width="+width+", height="+height+", left="+center+", scrollbars=yes";
	let target = BASE_URL + 'orders/order_pos_invoice/print_invoice/'+code;
	window.open(target, '_blank', prop);
}


function printBill(code) {
	let width = 400;
	let height = 600;
	let center = (window.innerWidth - width)/2;
	let middle = (window.innerHeight - height)/2;
	let prop = "width="+width+", height="+height+", left="+center+", top="+middle+", scrollbars=yes";
	let target = BASE_URL + 'orders/order_pos/print_slip/'+code;
	window.open(target, '_blank', prop);
}


function addNewCustomer() {
	let taxId = $('#tax-search').val();

	$('.cust-form').removeAttr('disabled');
	$('#tax-id').val(taxId);
	$('#cust-id').val('');
	$('#name').focus();
}


function toggleCheckAll(el) {
	if(el.is(':checked')) {
		$('.chk').prop('checked', true);
	}
	else {
		$('.chk').prop('checked', false);
	}
}


function createDelivery() {
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
		title:'สร้างเอกสารตัดยอดขาย',
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
				url:BASE_URL + 'orders/order_pos/create_delivery',
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
							let target = BASE_URL + 'account/consign_order/edit/'+ds.code+'?nomenu';
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


$('#tax-search').keydown(function(e) {
	if(e.keyCode == 13) {
		getCustomerByTaxId();
	}

	if(e.keyCode == 114) {
		e.preventDefault();
		addNewCustomer();
	}

	if(e.keyCode == 121) {
		e.preventDefault();
		addInvoice();
	}
});


$('#name').keyup(function(e) {
	if(e.keyCode == 13) {
		$('#tax-id').focus();
	}
});

$('#tax-id').keyup(function(e) {
	if(e.keyCode == 13) {
		$('#branch-code').focus();
	}
});

$('#branch-code').keyup(function(e) {
	if(e.keyCode == 13) {
		$('#branch-name').focus();
	}
});

$('#branch-name').keyup(function(e) {
	if(e.keyCode == 13) {
		$('#address').focus();
	}
});

$('#address').keyup(function(e) {
	if(e.keyCode == 13) {
		$('#phone').focus();
	}
});

$('#phone').keyup(function(e) {
	if(e.keyCode == 13) {
		$('#is-company').focus();
	}
});
