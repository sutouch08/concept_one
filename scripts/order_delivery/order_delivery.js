var HOME = BASE_URL + 'orders/order_delivery/';

window.addEventListener('load', () => {
	resizeDisplay();
})

window.addEventListener('resize', () => {
	resizeDisplay();
});

function resizeDisplay() {
	let height = $(window).height();
	let navHeight = 45;
	let searchHeight = $('#search-row').height() + navHeight;
	let pagination = $('#pagination').height();
	let pageContentHeight = height - pagination - 75;
	let billTableHeight = pageContentHeight - (searchHeight + 45 + pagination);
	billTableHeight = billTableHeight < 500 ? 500 : billTableHeight;

	$('.page-content').css('height', pageContentHeight + 'px');
  $('.page-content').css('padding-bottom', '0px');
	$('#bill-div').css('height', billTableHeight + 'px');
}

function goBack() {
  window.location.href = HOME;
}

function getSearch() {
  $('#searchForm').submit();
}


function clearFilter() {
  $.get(HOME + 'clear_filter', function() {
    goBack();
  })
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

function viewDetail(code) {
  window.location.href = HOME + 'view_detail/'+code;
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

function showInvoiceCustomer() {
  $('#invoiceCustomerModal').modal('show');
}

$('#invoiceCustomerModal').on('shown.bs.modal', function() {
  $('#tax-search').focus();
});

function createTaxInvoice() {
  $('.h').removeClass('has-error');

  let h = {
    'bill_code' : $('#code').val(),
    'billCode' : $('#code').val(),
    'refType' : $('#ref-type').val(),
		'vat_type' : $('#vat-type').val(),
		'is_term' : $('#is-term').val(),
		'taxStatus' : 'Y',
		'date_add' : $('#date').val(),
		'customer_code' : $('#customer-code').val(),
		'customer_name' : $('#customer-name').val(),
		'customer_ref' : $('#customer-name').val(),
		'phone' : $('#phone').val(),
		'branch_code' : $('#branch-code').val(),
		'branch_name' : $('#branch-name').val(),
		'tax_id' : $('#tax-id').val(),
		'address' : $('#address').val(),
		'sub_district' : $('#sub-district').val(),
		'district' : $('#district').val(),
		'province' : $('#province').val(),
		'postcode' : $('#postcode').val(),
    'is_company' : $('#is-company').is(':checked') ? 1 : 0,
		'sale_id' : $('#sale_id').val(),
    'remark' : "",
		'amountBfDisc' : parseDefault(parseFloat(removeCommas($('#total-amount').val())), 0),
		'billDiscPrcnt' : parseDefault(parseFloat($('#bill-disc-percent').val()), 0),
		'billDiscAmount' : parseDefault(parseFloat(removeCommas($('#bill-disc-amount').val())), 0),
		'whtPrcnt' : parseDefault(parseFloat($('#whtPrcnt').val()), 0),
		'whtAmount' : parseDefault(parseFloat($('#wht-amount').val()), 0),
		'vatSum' : parseDefault(parseFloat(removeCommas($('#vat-total').val())), 0),
		'docTotal' : parseDefault(parseFloat(removeCommas($('#doc-total').val())), 0),
    'totalDownAmount' : parseDefault(parseFloat($('#down-payment-amount').val()), 0)
	}

	let down = [];
	let totalDownAmount = 0;

	$('.down-amount').each(function() {
		let amount = roundNumber(parseDefault(parseFloat($(this).val()), 0), 2);
		let id = $(this).data('id');

		if(amount > 0 && id != "") {
			let dp = {
				'id' : id,
				'amount' : amount
			}

			down.push(dp);
			totalDownAmount += amount;
		}
	});

	totalDownAmount = roundNumber(totalDownAmount, 2);

	h.downPaymentUse = down;
	h.totalDownAmount = totalDownAmount;

  if(h.customer_code.length == 0 || h.customer_name.length == 0) {
    $('#customer_code').addClass('has-error');
    $('#customer_name').addClass('has-error');
    swal("กรุณาระบุลูกค้า");
    return false;
  }

  if(h.tax_id.length == 0 || h.address.length == 0 || h.sub_district.length == 0 || h.district.length == 0 || h.province.length == 0) {
    showCustomerModal();
    return false;
  }

  let count = 0;

  $('.bill-item').each(function() {
    let qty = parseDefault(parseFloat($(this).data('qty')));
    count++;
  });

  if(count == 0) {
    swal("ไม่พบรายการขาย กรุณาตรวจสอบ");
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
						text:'สร้าง invoice เลขที่ '+ds.invoice_code+' สำเร็จ <br/> ต้องการพิมพ์ Invoice หรือไม่ ? ',
						type:'success',
						showCancelButton:true,
						closeOnConfirm:true,
						html:true
					}, function(isConfirm) {
						if(isConfirm) {
							printInvoice(ds.invoice_code);

							setTimeout(() => {
								window.location.reload();
							},100);
						}
						else {
							window.location.reload();
						}
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
          type:'error',
          html:true
        })
      }
    },
    error:function(xhr) {
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


function createInvoice() {
  $('.h').removeClass('has-error');

  let h = {
    'bill_code' : $('#code').val(),
    'billCode' : $('#code').val(),
    'refType' : $('#ref-type').val(),
		'vat_type' : $('#vat-type').val(),
		'is_term' : $('#is-term').val(),
		'taxStatus' : 'N',
		'date_add' : $('#date').val(),
		'customer_code' : $('#customer-code').val(),
		'customer_name' : $('#customer-name').val(),
		'customer_ref' : $('#customer-name').val(),
		'phone' : $('#phone').val(),
		'branch_code' : $('#branch-code').val(),
		'branch_name' : $('#branch-name').val(),
		'tax_id' : $('#tax-id').val(),
		'address' : $('#address').val(),
		'sub_district' : $('#sub-district').val(),
		'district' : $('#district').val(),
		'province' : $('#province').val(),
		'postcode' : $('#postcode').val(),
    'is_company' : $('#is-company').is(':checked') ? 1 : 0,
		'sale_id' : $('#sale_id').val(),
    'remark' : "",
		'amountBfDisc' : parseDefault(parseFloat(removeCommas($('#total-amount').val())), 0),
		'billDiscPrcnt' : parseDefault(parseFloat($('#bill-disc-percent').val()), 0),
		'billDiscAmount' : parseDefault(parseFloat(removeCommas($('#bill-disc-amount').val())), 0),
		'whtPrcnt' : parseDefault(parseFloat($('#whtPrcnt').val()), 0),
		'whtAmount' : parseDefault(parseFloat($('#wht-amount').val()), 0),
		'vatSum' : parseDefault(parseFloat(removeCommas($('#vat-total').val())), 0),
		'docTotal' : parseDefault(parseFloat(removeCommas($('#doc-total').val())), 0),
    'totalDownAmount' : parseDefault(parseFloat($('#down-payment-amount').val()), 0)
	}

	let down = [];
	let totalDownAmount = 0;

	$('.down-amount').each(function() {
		let amount = roundNumber(parseDefault(parseFloat($(this).val()), 0), 2);
		let id = $(this).data('id');

		if(amount > 0 && id != "") {
			let dp = {
				'id' : id,
				'amount' : amount
			}

			down.push(dp);
			totalDownAmount += amount;
		}
	});

	totalDownAmount = roundNumber(totalDownAmount, 2);

	h.downPaymentUse = down;
	h.totalDownAmount = totalDownAmount;

  if(h.customer_code.length == 0 || h.customer_name.length == 0) {
    $('#customer_code').addClass('has-error');
    $('#customer_name').addClass('has-error');
    swal("กรุณาระบุลูกค้า");
    return false;
  }

  let count = 0;

  $('.bill-item').each(function() {
    let qty = parseDefault(parseFloat($(this).data('qty')));
    count++;
  });

  if(count == 0) {
    swal("ไม่พบรายการขาย กรุณาตรวจสอบ");
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
						text:'สร้าง invoice เลขที่ '+ds.invoice_code+' สำเร็จ <br/> ต้องการพิมพ์ Invoice หรือไม่ ? ',
						type:'success',
						showCancelButton:true,
						closeOnConfirm:true,
						html:true
					}, function(isConfirm) {
						if(isConfirm) {
							printInvoice(ds.invoice_code);

							setTimeout(() => {
								window.location.reload();
							},100);
						}
						else {
							window.location.reload();
						}
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
          type:'error',
          html:true
        })
      }
    },
    error:function(xhr) {
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


function createInvoiceByCheckedBill() {
  let bills = [];
  $('.chk:checked').each(function() {
    bills.push($(this).val());
  });

  if(bills.length == 0) {
    swal("กรุณาเลือกบิลอย่างน้อย 1 ใบ");
    return false;
  }

  swal({
    title:'สร้าง Invoice',
    text:'ต้องการสร้าง Invoice ตามบิลที่เลือกหรือไม่ ?',
    type:'info',
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  }, function() {
    setTimeout(() => {
      load_in();

      $.ajax({
        url:BASE_URL + 'orders/order_invoice/add_each_invoice_by_delivery',
        type:'POST',
        cache:false,
        data:{
          'bills' : JSON.stringify(bills)
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
                type:'error',
                html:true
              }, function() {
                window.location.reload();
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
    }, 100);
  })
}
