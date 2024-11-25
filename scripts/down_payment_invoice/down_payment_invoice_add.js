$('#bill-code').autocomplete({
  source:BASE_URL + 'auto_complete/get_uninvoice_down_payment',
  autoFocus:true,
  option:function(e) {
    let ul = $(this).autocomplete('widget');
    ul.css('width', 'auto');
  },
  select:function(e, ui) {
    $('#billCode').val(ui.item.code);
  },
  close:function() {
    let arr = $(this).val().split(' | ');
    let code = arr.length > 1 ? arr[0] : '';
    $(this).val(code);
  }
});


function getDpm()
{
  $('#bill-code').clearError();

  let code = $('#billCode').val(); //--- hidden field
  let billCode = $('#bill-code').val(); //--- input field

  if(code.length < 9 && billCode.length < 9)
  {
    $('#bill-code').hasError();
    return false;
  }

  if(code != billCode)
  {
    $('#bill-code').hasError();
    swal({
      title:'Oops!',
      text:'เลขที่เอกสารไม่ถูกต้อง',
      type:'error'
    });

    return false;
  }

  load_in();

  $.ajax({
    url:BASE_URL + 'orders/order_down_payment/get/'+code,
    type:'GET',
    cache:false,
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let data = JSON.parse(rs);

        if(data.status == 'success') {
          let h = data.doc;
          let row = data.row;

          $('#customer-code').val(h.customer_code);
          $('#customer-name').val(h.customer_name);
          $('#customer-ref').val(h.customer_ref);
          $('#is-company').prop('checked', is_true(h.isCompany));
          $('#phone').val(h.customer_phone);
          $('#tax-id').val(h.tax_id);
          $('#branch-code').val(h.branch_code);
          $('#branch-name').val(h.branch_name);
          $('#address').val(h.address);
          $('#sub-district').val(h.sub_district);
          $('#district').val(h.district);
          $('#province').val(h.province);
          $('#postcode').val(h.postcode);
          $('#sale-id').val(h.sale_id);
          $('#owner').val(h.user);
          $('#remark').val("Base On "+h.code);

          $('#total-amount').val(addCommas(h.PriceBefDi.toFixed(2)));
          $('#vat-total').val(addCommas(h.VatSum.toFixed(2)));
          $('#doc-total').val(addCommas(h.DocTotal.toFixed(2)));

          let source = $('#template').html();
          let output = $('#result');

          render(source, row, output);
        }
        else
        {
          swal({
            title:'Error!',
            text:data.message,
            type:'error',
            html:true
          })
        }
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error',
          html:true
        })
      }
    },
    error:function(e) {
      load_out();

      swal({
        title:'Error!',
        text:e.responseText,
        type:'error',
        html:true
      })
    }
  })
}


function createInvoice() {
	$('.h').clearError();
	let code = $('#billCode').val().trim();
  let dpCode = $('#bill-code').val().trim();
	let tax_id = $('#tax-id').val().trim();
	let addr = $('#address').val().trim();
	let err = 0;

  if(code.length < 9 || dpCode.length < 0 || code != dpCode) {
    $('#bill-code').hasError();
    return false;
  }

	if(tax_id.length == 0 || addr.length == 0) {
		showCustomerModal();
	}
	else {
		let h = {
      'date' : $('#date').val(),
			'dpCode' : dpCode,
      'customer_code' : $('#customer-code').val().trim(),
			'customer_name' : $('#customer-name').val().trim(),
      'customer_ref' : $('#customer-ref').val().trim(),
			'tax_id' : $('#tax-id').val().trim(),
			'branch_code' : $('#branch-code').val().trim(),
			'branch_name' : $('#branch-name').val().trim(),
			'address' : $('#address').val().trim(),
			'sub_district' : $('#sub-district').val().trim(),
			'district' : $('#district').val().trim(),
			'province' : $('#province').val().trim(),
			'postcode' : $('#postcode').val().trim(),
			'phone' : $('#phone').val().trim(),
			'is_company' : $('#is-company').is(':checked') ? 1 : 0,
      'sale_id' : $('#sale-id').val(),
      'user' : $('#owner').val(),
      'remark' : $('#remark').val().trim(),
      'PriceBefDi' : parseDefault(parseFloat(removeCommas($('#total-amount').val())), 0),
      'VatSum' : parseDefault(parseFloat(removeCommas($('#vat-total').val())), 0),
      'DocTotal' : parseDefault(parseFloat(removeCommas($('#doc-total').val())), 0)
		};

    if( ! isDate(h.date)) {
      $('#date').hasError();
      err++;
    }

    if(h.customer_code.length == 0) {
      $('#customer-code').hasError();
      err++;
    }

		if(h.customer_name.length ==  0) {
			$('#customer-name').hasError();
			err++;
		}

		if(h.tax_id.length < 10) {
			$('#tax-id').hasError();
			err++;
		}

		if(h.address.length == 0) {
			$('#address').hasError();
			err++;
		}

		if(h.sub_district.length == 0) {
			$('#sub-district').hasError();
			err++;
		}

		if(h.district.length == 0) {
			$('#district').hasError();
			err++;
		}

		if(h.province.length == 0) {
			$('#province').hasError();
			err++;
		}

		if(err > 0) {
			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'add',
			type:'POST',
			cache:false,
			data:{
				"data" : JSON.stringify(h)
			},
			success:function(rs) {
				load_out();

				if( isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status == 'success') {
						if(ds.ex == 1) {
							printDownPaymentInvoice(ds.code);
							setTimeout(() => {
								viewDetail(ds.code);
							}, 200);
						}
						else {
							setTimeout(() => {
								swal({
									title:'ข้อผิดพลาด',
									text:'สร้างเอกสารสำเร็จแต่ส่งเข้าระบบ SAP ไม่สำเร็จ กรุณากดส่งใหม่ภายหลัง<br/>ต้องการพิมพ์ใบกำกับภาษีหรือไม่ ?',
									type:'info',
									html:true,
									showCancelButton:true,
									cancelButtonText:'No',
									confirmButtonText:'Yes',
									closeOnConfirm:true
								}, function() {
									setTimeout(() => {
										printDownPaymentInvoice(ds.code);

										setTimeout(() => {
											viewDetail(ds.code);
										}, 200);
									}, 500);
								});
							}, 100);
						}
					}
					else {
						setTimeout(() => {
							swal({
								title:'Error!',
								text:ds.message,
								type:'error',
								html:true
							})
						}, 100);
					}
				}
				else {
					setTimeout(() => {
						swal({
							title:'Error!',
							text:rs,
							type:'error',
							html:true
						})
					}, 100);
				}
			},
			error:function(rs) {
				load_out();

				swal({
					title:'Error!',
					text:rs.responeText,
					type:'error',
					html:true
				});
			}
		})
	}
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

function toggleBranch() {
	let bCode = $('#branch-code').val();
	if($('#is-company').is(':checked')) {
		if(bCode.length == 0) {
			$('#branch-code').val('00000');
			$('#branch-name').val('สำนักงานใหญ่');
		}
	}
	else {
		$('#branch-code').val('');
		$('#branch-name').val('');
	}
}

function showCustomerModal() {
	$('#customerModal').modal('show');
	$('#customerModal').on('shown.bs.modal', () => {
		$('#tax-search').focus();
	})
}

$('#tax-search').autocomplete({
	source:BASE_URL + 'auto_complete/get_invoice_customer_by_tax',
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

$('#phone-search').autocomplete({
	source:BASE_URL + 'auto_complete/get_invoice_customer_by_phone',
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
  $('#is-company').prop('checked', is_true(h.is_company));
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
          $('#is-company').prop('checked', is_true(h.is_company));
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


function clearForm() {
	$('.cust-form').val('');
	$('#phone-search').val('');
  $('#tax-search').val('').focus();
}
