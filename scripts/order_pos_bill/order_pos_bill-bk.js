var HOME = BASE_URL + 'orders/order_pos_bill/';

window.addEventListener('load', () => {
	resizeDisplay();
})

window.addEventListener('resize', () => {
	resizeDisplay();
});

function resizeDisplay() {
	let height = $(window).height();
	let navHeight = 45;
	let searchHeight = $('#search-row').height() + navHeight + 65;
	let pageContentHeight = height - searchHeight//(navHeight + searchHeight);

	let billTableHeight = pageContentHeight - (189); //-- 155
	let billViewHeight = pageContentHeight - (125);

	$('.page-content').css('height', pageContentHeight + 'px');
	$('#bill-div').css('height', billTableHeight + 'px');
	$('#bill-view').css('height', billViewHeight + 'px');
}

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

function viewDetail(id) {
	window.location.href = HOME + 'view_detail/'+id;
}


function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs) {
		goBack();
	});
}


function cancelBill(code, id) {
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
		let role = $('#payment-role').val();
		let amount = $('#bill-amount').val();
		$('#payment-role-'+role).prop('checked', true);
		$('#cancel-bill-amount').text(amount);
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


function createInvoice() {
	let code = $('#code').val();
	let customer_code = $('#customer-code').val();
	let customer_name = $('#customer-name').val();
	let tax_id = $('#tax-id').val();
	let bCode = $('#branch-code').val();
	let bName = $('#branch-name').val();
	let addr = $('#address').val();
	let sub_district = $('#sub-district').val();
	let district = $('#district').val();
	let province = $('#province').val();
	let postcode = $('#postcode').val();
	let phone = $('#phone').val();


}

$('#tax-search').autocomplete({
	source:BASE_URL + 'auto_complete/get_invoice_customer_by_tax',
	autoFocus:true,
	open:function(event) {
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	select:function(event, ui) {
		let id = ui.item.id;
		let isCompany = ui.item.is_company;
		$('#cust-id').val(id);
		$('#form-name').val(ui.item.name);
		$('#form-tax-id').val(ui.item.tax_id);
		$('#form-phone').val(ui.item.phone);
		$('#form-branch-code').val(ui.item.branch_code);
		$('#form-branch-name').val(ui.item.branch_name);
		$('#form-address').val(ui.item.address);
		$('#form-subDistrict').val(ui.item.sub_district);
		$('#form-district').val(ui.item.district);
		$('#form-province').val(ui.item.province);
		$('#form-postcode').val(ui.item.postcode);

		if(isCompany) {
			$('#form-is-company').prop('checked', true);
		}
		else {
			$('#form-is-company').prop('checked', false);
		}
	},
	close:function() {
		let name = $(this).val();
		let arr = name.split(' | ');
		$('#tax-search').val(arr[0]);
	}
})



function showInvoiceCustomer() {
  $('#invoiceCustomerModal').modal('show');
}

$('#invoiceCustomerModal').on('shown.bs.modal', function() {
  $('#tax-search').focus();
});



function toggleFormBranch() {
	if($('#form-is-company').is(':checked')) {
		$('#form-branch-code').val('00000');
		$('#form-branch-name').val('สำนักงานใหญ่');
	}
	else {
		$('#form-branch-code').val('');
		$('#form-branch-name').val('');
	}
}

function toggleBranch() {
	if($('#is-company').is(':checked')) {
		$('#branch-code').val('00000');
		$('#branch-name').val('สำนักงานใหญ่');
	}
	else {
		$('#branch-code').val('');
		$('#branch-name').val('');
	}
}

function showCustomerModal(taxStatus) {
	taxOption = taxStatus == 'Y' ? 'Y' : '';

	$('#customerModal').modal('show');
	$('#customerModal').on('shown.bs.modal', () => {
		$('#tax-search').focus();
	})
}

function newCustomer() {
	let taxId = $('#tax-search').val();
	$('.cust-form').removeAttr('disabled');
	$('#form-tax-id').val(taxId);
	$('#cust-id').val('');
	$('#form-name').focus();
}


function addCustomer() {
	$('.cust-form').removeClass('has-error');

	let h = {
		'customer_id' : $('#cust-id').val(),
		'customer_name' : $.trim($('#form-name').val()),
		'tax_id' : $('#form-tax-id').val(),
		'branch_code' : $.trim($('#form-branch-code').val()),
		'branch_name' : $.trim($('#form-branch-name').val()),
		'address' : $.trim($('#form-address').val()),
		'subDistrict' : $.trim($('#form-subDistrict').val()),
		'district' : $.trim($('#form-district').val()),
		'province' : $.trim($('#form-province').val()),
		'postcode' : $.trim($('#form-postcode').val()),
		'phone' : $.trim($('#form-phone').val()),
		'is_company' : $('#form-is-company').is(':checked') ? 1 : 0
	};

	if(h.customer_name.length == 0) {
		$('#form-name').addClass('has-error');
		return false;
	}

	if(h.tax_id.length < 13) {
		$('#form-tax-id').addClass('has-error');
		return false;
	}

	if(h.is_company && (h.branch_code.length == 0 || h.branch_name.length == 0)) {
		if(h.branch_code.length == 0) {
			$('#form-branch-code').addClass('has-error');
		}

		if(h.branch_name.length == 0) {
			$('#form-branch-name').addClass('has-error');
		}

		return false;
	}

	if(h.address.length == 0) {
		$('#form-address').addClass('has-error');
		return false;
	}

	$('#customerModal').modal('hide');

	load_in();

	$.ajax({
		url:HOME + 'add_invoice_customer',
		type:'POST',
		cache:false,
		data:{
			"data" : JSON.stringify(h)
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					$('#customer-name').val(h.customer_name);
					$('#tax-id').val(h.tax_id);
					$('#branch-code').val(h.branch_code);
					$('#branch-name').val(h.branch_name);
					$('#address').val(h.address);
					$('#sub-district').val(h.subDistrict);
					$('#district').val(h.district);
					$('#province').val(h.province);
					$('#postcode').val(h.postcode);
					$('#phone').val(h.phone);

					if(h.is_company) {
						$('#is-company').prop('checked', true);
					}
					else {
						$('#is-company').prop('checked', false);
					}

					if(taxOption == 'Y') {
						createTaxInvoice();
					}
					else {
						createInvoice();
					}
				}
				else {
					message = '<h4 class="title-xs red text-center">'+ds.message+'</h4>';
					$('#cust-result-table').html(message);
					$('.cust-form').val('').attr('disabled', 'disabled');
					$('#tax-search').focus();
				}
			}
			else {
				message = '<h4 class="title-xs red text-center">'+rs+'</h4>';
				$('#cust-result-table').html(message);
				$('#tax-search').focus();
			}
		}
	})
}


function addToBill() {
	$('.cust-form').removeClass('has-error');

	let h = {
		'bill_code' : $('#bill-code').val(),
		'bill_id' : $('#bill-id').val(),
		'customer_id' : $('#cust-id').val(),
		'customer_name' : $.trim($('#form-name').val()),
		'tax_id' : $('#form-tax-id').val(),
		'branch_code' : $.trim($('#form-branch-code').val()),
		'branch_name' : $.trim($('#form-branch-name').val()),
		'address' : $.trim($('#form-address').val()),
		'sub_district' : $.trim($('#form-subDistrict').val()),
		'district' : $.trim($('#form-district').val()),
		'province' : $.trim($('#form-province').val()),
		'postcode' : $.trim($('#form-postcode').val()),
		'phone' : $.trim($('#form-phone').val()),
		'is_company' : $('#form-is-company').is(':checked') ? 1 : 0
	};

	if(h.customer_name.length == 0) {
		$('#form-name').addClass('has-error');
		return false;
	}

	if(h.tax_id.length < 10) {
		$('#form-tax-id').addClass('has-error');
		return false;
	}

	if(h.is_company && (h.branch_code.length == 0 || h.branch_name.length == 0)) {
		if(h.branch_code.length == 0) {
			$('#form-branch-code').addClass('has-error');
		}

		if(h.branch_name.length == 0) {
			$('#form-branch-name').addClass('has-error');
		}

		return false;
	}

	if(h.address.length == 0) {
		$('#form-address').addClass('has-error');
		return false;
	}

	$('#customerModal').modal('hide');

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'data' : JSON.stringify(h)
		},
		success:function(rs) {
			if(rs === 'success') {
				$('#customer-name').val(h.customer_name);
				$('#tax-id').val(h.tax_id);
				$('#branch-code').val(h.branch_code);
				$('#branch-name').val(h.branch_name);
				$('#address').val(h.address);
				$('#sub-district').val(h.sub_district);
				$('#district').val(h.district);
				$('#province').val(h.province);
				$('#postcode').val(h.postcode);
				$('#phone').val(h.phone);

				if(h.is_company) {
					$('#is-company').prop('checked', true);
				}
				else {
					$('#is-company').prop('checked', false);
				}

				if(taxOption == 'Y') {
					createTaxInvoice();
				}
				else {
					createInvoice();
				}
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
		}
	})
}


var taxOption = '';

function clearForm() {
	$('#tax-search').val('');
	$('#form-name').val('');
	$('#form-phone').val('');
	$('#form-is-company').prop('checked', false);
	$('#form-tax-id').val('');
	$('#form-branch-code').val('');
	$('#form-branch-name').val('');
	$('#form-address').val('');
	$('#form-subDistrict').val('');
	$('#form-district').val('');
	$('#form-province').val('');
	$('#form-postcode').val('');
	$('#tax-search').focus();
}

function createTaxInvoice() {
	$('.h').removeClass('has-error');

	let name = $.trim($('#customer-name').val());
	let tax_id = $.trim($('#tax-id').val());
	let address = $.trim($('#address').val());

	let h = {
		'taxStatus' : 'Y',
		'refType' : 'POS',
		'date_add' : $('#date').val(),
		'bill_id' : $('#bill-id').val(),
		'bill_code' : $('#bill-code').val(),
		'billCode' : $('#bill-code').val(),
		'customer_code' : $('#customer-code').val(),
		'customer_name' : $.trim($('#customer-name').val()),
		'tax_id' : $.trim($('#tax-id').val()),
		'branch_code' : $.trim($('#branch-code').val()),
		'branch_name' : $.trim($('#branch-name').val()),
		'address' : $.trim($('#address').val()),
		'sub_district' : $.trim($('#sub-district').val()),
		'district' : $.trim($('#district').val()),
		'province' : $.trim($('#province').val()),
		'postcode' : $.trim($('#postcode').val()),
		'phone' : $.trim($('#phone').val()),
		'is_company' : $('#is-company').is(':checked') ? 1 : 0,
		'sale_id' : $('#sale-id').val()
	};

	if(name.length < 5 || tax_id.length < 10 || address.length < 3) {
		showCustomerModal('Y');
		return false;
	}


	if(h.customer_name == "") {
		$('#name').addClass('has-error');
		return false;
	}

	if(h.tax_id < 10) {
		$('#tax-id').addClass('has-error');
		return false;
	}

	if(h.is_company && (h.branch_code == "" || h.branch_name == "")) {
		if(h.branch_code == "") {
			$('#branch-code').addClass('has-error');
		}

		if(h.branch_name == "") {
			$('#branch-name').addClass('has-error');
		}

		return false;
	}

	if(address.length < 3) {
		$('#address').addClass('has-error');
		return false;
	}


	load_in();

	$.ajax({
		url:BASE_URL + 'orders/order_invoice/add_invoice',
		type:'POST',
		cache:false,
		data:{
			'data' : JSON.stringify(h)
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
						window.location.reload();
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
