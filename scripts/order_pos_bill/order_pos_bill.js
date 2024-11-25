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
	let searchHeight = $('#search-row').height() + navHeight + 25;

	let pageContentHeight = height - (53 + 70); //(navHeight + searchHeight + 50);

	let billTableHeight = pageContentHeight - (searchHeight + 65); //(195); //-- 155


	$('.page-content').css('height', pageContentHeight + 'px');
	$('#bill-div').css('height', billTableHeight + 'px');
}

function hilightRow(id) {
	$('.pos-rows').removeClass('active-row');
	$('#row-'+id).addClass('active-row');
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

function goBack() {
	window.location.href = HOME;
}

function getSearch() {
	$('#searchForm').submit();
}

function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){ goBack(); });
}

function viewBillDetail(id) {
	window.location.href = HOME + 'view_bill_detail/'+id;
}


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

function showCustomerModal() {
	$('#customerModal').modal('show');
	$('#customerModal').on('shown.bs.modal', () => {
		$('#tax-search').focus();
	})
}

$('#tax-search').autocomplete({
	source:BASE_URL + 'auto_complete/get_invoice_customer',
	autoFocus:true,
	open:function(event) {
		let ul = $(this).autocomplete('widget');
		ul.css('width', 'auto');
	},
	select:function(event, ui) {
		$('#cust-id').val(ui.item.id);
		$('#form-name').val(ui.item.name);
		$('#form-tax-id').val(ui.item.tax_id);
		$('#form-branch-code').val(ui.item.branch_code);
		$('#form-branch-name').val(ui.item.branch_name);
		$('#form-address').val(ui.item.address);
		$('#form-subDistrict').val(ui.item.sub_district);
		$('#form-district').val(ui.item.district);
		$('#form-province').val(ui.item.province);
		$('#form-postcode').val(ui.item.postcode);
		$('#form-phone').val(ui.item.phone);

		if(ui.item.is_company == '1') {
			$('#form-is-company').prop('checked', true);
		}
		else {
			$('#form-is-company').prop('checked', false);
		}
	},
	close:function() {
		let arr = $(this).val().split(' | ');
		$(this).val(arr[0]);
	}
})

function addCustomer() {
	$('.cust-form').removeClass('has-error');

	let h = {
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
		url:BASE_URL + 'orders/order_invoice/add_invoice_customer',
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
}


function clearForm() {
	$('.cust-form').val('');
	$('#tax-search').val('').focus();
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
		let amount = $('#pay-amount').val();
		let downAmount = $('#down-amount').val();
		$('#payment-role-'+role).prop('checked', true);
		$('#cancel-bill-amount').text(amount);
		$('#cancel-down-amount').text(downAmount);
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

	if(reason.length == 0) {
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

function printBill(code) {
	let width = 400;
	let height = 600;
	let center = (window.innerWidth - width)/2;
	let middle = (window.innerHeight - height)/2;
	let prop = "width="+width+", height="+height+", left="+center+", top="+middle+", scrollbars=yes";
	let target = BASE_URL + 'orders/order_pos/print_slip/'+code;
	window.open(target, '_blank', prop);
}

function viewSo(code) {
	let width = 1000;
	let height = 600;
	let center = (window.innerWidth - width)/2;
	let prop = "width="+width+", height="+height+", left="+center+", scrollbars=yes";
	let target = BASE_URL + 'orders/sales_order/view_detail/'+code+'?nomenu';
	window.open(target, '_blank', prop);
}

function viewInvoice(code) {
	let width = 1000;
	let height = 600;
	let center = (window.innerWidth - width)/2;
	let prop = "width="+width+", height="+height+", left="+center+", scrollbars=yes";
	let target = BASE_URL + 'orders/order_invoice/view_detail/'+code+'?nomenu';
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
})
