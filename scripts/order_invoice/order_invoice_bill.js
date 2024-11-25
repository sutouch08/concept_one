function toggleRefType() {
	let refType = $('#ref-type').val();

	if(refType == 'WO') {
		woInit();
	}

	if(refType == 'WU') {
		wuInit();
	}

	if(refType == 'POS') {
		posInit();
	}

	if(refType == 'SO') {
		soInit();
	}

	if(refType == 'DP') {
		dpInit();
	}

	$('#bill-code').removeAttr('disabled');
	$('#bill-code').focus();
}

function toggleVatType() {
  let type = $('#vat-type').val();
  let taxStatus = type == 'N' ? 'N' : 'Y';
  $('#tax-status').val(taxStatus);

  if(type == 'N') {
    $('#bill-vat').addClass('hide');
    $('#bill-wht').addClass('hide');
  }
  else {
    $('#bill-vat').removeClass('hide');
    $('#bill-wht').removeClass('hide');
  }

  recalTotal();
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


$('#customer-code').autocomplete({
  source:BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  open:function(event) {
    var $ul = $(this).autocomplete('widget')
    $ul.css('width', 'auto')
  },
  select:function(event, ui) {
    let code = ui.item.code
    let name = ui.item.name
    let tax_id = ui.item.tax_id

    if(code.length) {
      $('#customer-code').val(code);
      $('#customer-name').val(name);
      $('#tax-id').val(tax_id);

      get_bill_to_address(code);
    }
    else {
      $('#customer-code').val('');
      $('#customer-name').val('');
      $('#tax-id').val('');
    }
  },
  close:function() {
    let label = $(this).val();
    let arr = label.split(' | ');

    $(this).val(arr[0]);
  }
})


function get_bill_to_address(code) {
  if(code.length) {
    $.ajax({
      url:BASE_URL + 'masters/address/get_customer_bill_to_address',
      type:'GET',
      cach:false,
      data:{
        'code' : code
      },
      success:function(rs) {
        if(isJson(rs)) {
          let ds = JSON.parse(rs);
					if(ds.status == 'success') {
						if(ds.address != null && ds.address != undefined) {
							if(ds.address.length == 1) {
								let adr = ds.address[0];
								$('#phone').val(adr.phone);
								$('#branch-code').val(adr.branch_code);
								$('#branch-name').val(adr.branch_name);
								$('#address').val(adr.address);
								$('#sub-district').val(adr.sub_district);
								$('#district').val(adr.district);
								$('#province').val(adr.province);
								$('#postcode').val(adr.postcode);
							}
							else {
								let source = $('#bill-to-template').html();
								let output = $('#bill-to-table');

								render(source, ds.address, output);

								$('#billToModal').modal('show');
							}
						}
					}
        }
      }
    })
  }
}



function getBillDetails(code) {
	load_in();

	$.ajax({
		url:HOME + 'get_bill_details',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					let hd = ds.header;

					$('#billCode').val(hd.code);
					$('#is-term').val(hd.is_term);
					$('#vat-type').val(hd.vat_type);
					$('#tax-status').val(hd.TaxStatus);
					$('#customer-code').val(hd.CardCode);
					$('#customer-name').val(hd.NumAtCard);
					$('#tax-id').val(hd.LicTradNum);
					$('#branch-code').val(hd.branch_code);
					$('#branch-name').val(hd.branch_name);
					$('#customer-ref').val(hd.NumAtCard);
					$('#address').val(hd.address);
					$('#sub-district').val(hd.sub_district);
					$('#district').val(hd.district);
					$('#province').val(hd.province);
					$('#postcode').val(hd.postcode);
					$('#phone').val(hd.phone);
					$('#sale-id').val(hd.SlpCode);
					$('#owner').val(hd.user);
					$('#remark').val(hd.Comments);
					$('#total-qty').val(hd.totalQty);
					$('#total-amount').val(hd.TotalBfDisc)
					$('#total-after-disc').val(hd.TotalAfDisc);
					$('#bill-disc-percent').val(hd.DiscPrcnt);
					$('#bill-disc-amount').val(hd.DiscSum);
					$('#whtPrcnt').val(hd.whtPrcnt);
					$('#wht-amount').val(parseDefault(parseFloat(removeCommas(hd.whtAmount)), 0));
					$('#wht-amount-label').val(hd.whtAmount);
					$('#vat-total').val(hd.VatSum);
					$('#doc-total').val(hd.DocTotal);
					$('#down-amount').val(hd.down_payment_amount);
					$('#doc-balance').val(hd.PayAmount);

					if(hd.branch_code != "") {
						$('#is-company').prop('checked', true);
					}
					else {
						$('#is-company').prop('checked', false);
					}

					let data = ds.details;
					let source = $('#pos-template').html();
					let output = $('#detail-table');

					render(source, data, output);

					reIndex();

					let dps = ds.down_payment_list;
					let dsource = $('#pos-down-payment-template').html();
					let doutput = $('#down-payment-table');

					render(dsource, dps, doutput);

					reIndex('dp-no');

					toggleVatType();

					$('#ref-type').attr('disabled', 'disabled');
					$('#bill-code').attr('disabled', 'disabled');
					$('#btn-submit-bill').addClass('hide');
					$('#btn-change-bill').removeClass('hide');
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error'
					}, function() {
						setTimeout(() => {
							$('#wo-code').focus();
						}, 200);
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


function getWoDetails(code) {
	load_in();

	$.ajax({
		url:HOME + 'get_wo_details',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					let hd = ds.header;
					let docTotal = removeCommas(hd.DocTotal);
					let balance = docTotal - ds.downPaymentUse;

					$('#billCode').val(hd.code);
					$('#is-term').val(hd.is_term);
					$('#vat-type').val(hd.vat_type);
					$('#tax-status').val(hd.TaxStatus);
					$('#customer-code').val(hd.CardCode);
					$('#customer-name').val(hd.NumAtCard);
					$('#tax-id').val(hd.LicTradNum);
					$('#branch-code').val(hd.branch_code);
					$('#branch-name').val(hd.branch_name);
					$('#customer-ref').val(hd.NumAtCard);
					$('#address').val(hd.address);
					$('#sub-district').val(hd.sub_district);
					$('#district').val(hd.district);
					$('#province').val(hd.province);
					$('#postcode').val(hd.postcode);
					$('#phone').val(hd.phone);
					$('#sale-id').val(hd.SlpCode);
					$('#owner').val(hd.user);
					$('#remark').val(hd.Comments);
					$('#total-qty').val(hd.totalQty);
					$('#total-amount').val(hd.TotalBfDisc)
					$('#total-after-disc').val(hd.TotalAfDisc);
					$('#bill-disc-percent').val(hd.DiscPrcnt);
					$('#bill-disc-amount').val(hd.DiscSum);
					$('#whtPrcnt').val(hd.whtPrcnt);
					$('#wht-amount').val(parseDefault(parseFloat(removeCommas(hd.whtAmount)), 0));
					$('#wht-amount-label').val(hd.whtAmount);
					$('#vat-total').val(hd.VatSum);
					$('#doc-total').val(hd.DocTotal);
					$('#down-amount').val(addCommas(ds.downPaymentUse));
					$('#doc-balance').val(addCommas(balance));

					if(hd.branch_code != "") {
						$('#is-company').prop('checked', true);
					}
					else {
						$('#is-company').prop('checked', false);
					}

					let data = ds.details;
					let source = $('#wo-template').html();
					let output = $('#detail-table');

					render(source, data, output);

					reIndex();

					let dps = ds.down_payment_list;
					let dsource = $('#down-payment-template').html();
					let doutput = $('#down-payment-table');

					render(dsource, dps, doutput);

					reIndex('dp-no');

					toggleVatType();

					$('#ref-type').attr('disabled', 'disabled');
					$('#bill-code').attr('disabled', 'disabled');
					$('#btn-submit-bill').addClass('hide');
					$('#btn-change-bill').removeClass('hide');
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error'
					}, function() {
						setTimeout(() => {
							$('#wo-code').focus();
						}, 200);
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


function getWuDetails(code) {
	load_in();

	$.ajax({
		url:HOME + 'get_wu_details',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					let hd = ds.header;
					let docTotal = removeCommas(hd.DocTotal);
					let balance = docTotal - ds.downPaymentUse;

					$('#billCode').val(hd.code);
					$('#is-term').val(hd.is_term);
					$('#vat-type').val(hd.vat_type);
					$('#tax-status').val(hd.TaxStatus);
					$('#customer-code').val(hd.CardCode);
					$('#customer-name').val(hd.NumAtCard);
					$('#tax-id').val(hd.LicTradNum);
					$('#branch-code').val(hd.branch_code);
					$('#branch-name').val(hd.branch_name);
					$('#customer-ref').val(hd.NumAtCard);
					$('#address').val(hd.address);
					$('#sub-district').val(hd.sub_district);
					$('#district').val(hd.district);
					$('#province').val(hd.province);
					$('#postcode').val(hd.postcode);
					$('#phone').val(hd.phone);
					$('#sale-id').val(hd.SlpCode);
					$('#owner').val(hd.user);
					$('#remark').val(hd.Comments);
					$('#total-qty').val(hd.totalQty);
					$('#total-amount').val(hd.TotalBfDisc)
					$('#total-after-disc').val(hd.TotalAfDisc);
					$('#bill-disc-percent').val(hd.DiscPrcnt);
					$('#bill-disc-amount').val(hd.DiscSum);
					$('#whtPrcnt').val(hd.whtPrcnt);
					$('#wht-amount').val(parseDefault(parseFloat(removeCommas(hd.whtAmount)), 0));
					$('#wht-amount-label').val(hd.whtAmount);
					$('#vat-total').val(hd.VatSum);
					$('#doc-total').val(hd.DocTotal);
					$('#down-amount').val(addCommas(ds.downPaymentUse));
					$('#doc-balance').val(addCommas(balance));

					if(hd.branch_code != "") {
						$('#is-company').prop('checked', true);
					}
					else {
						$('#is-company').prop('checked', false);
					}

					let data = ds.details;
					let source = $('#wo-template').html();
					let output = $('#detail-table');

					render(source, data, output);

					reIndex();

					let dps = ds.down_payment_list;
					let dsource = $('#down-payment-template').html();
					let doutput = $('#down-payment-table');

					render(dsource, dps, doutput);

					reIndex('dp-no');

					toggleVatType();

					$('#ref-type').attr('disabled', 'disabled');
					$('#bill-code').attr('disabled', 'disabled');
					$('#btn-submit-bill').addClass('hide');
					$('#btn-change-bill').removeClass('hide');
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error'
					}, function() {
						setTimeout(() => {
							$('#wo-code').focus();
						}, 200);
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

function getDpDetails(code) {
	load_in();

	$.ajax({
		url:HOME + 'get_down_payment_details',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					let hd = ds.header;
					let docTotal = removeCommas(hd.DocTotal);
					let balance = docTotal - ds.downPaymentUse;

					$('#billCode').val(hd.code);
					$('#is-term').val(hd.is_term);
					$('#vat-type').val(hd.vat_type);
					$('#tax-status').val(hd.TaxStatus);
					$('#customer-code').val(hd.CardCode);
					$('#customer-name').val(hd.NumAtCard);
					$('#tax-id').val(hd.LicTradNum);
					$('#branch-code').val(hd.branch_code);
					$('#branch-name').val(hd.branch_name);
					$('#customer-ref').val(hd.NumAtCard);
					$('#address').val(hd.address);
					$('#sub-district').val(hd.sub_district);
					$('#district').val(hd.district);
					$('#province').val(hd.province);
					$('#postcode').val(hd.postcode);
					$('#phone').val(hd.phone);
					$('#sale-id').val(hd.SlpCode);
					$('#owner').val(hd.user);
					$('#remark').val(hd.Comments);
					$('#total-qty').val(hd.totalQty);
					$('#total-amount').val(hd.TotalBfDisc)
					$('#total-after-disc').val(hd.TotalAfDisc);
					$('#bill-disc-percent').val(hd.DiscPrcnt);
					$('#bill-disc-amount').val(hd.DiscSum);
					$('#whtPrcnt').val(hd.whtPrcnt);
					$('#wht-amount').val(parseDefault(parseFloat(removeCommas(hd.whtAmount)), 0));
					$('#wht-amount-label').val(hd.whtAmount);
					$('#vat-total').val(hd.VatSum);
					$('#doc-total').val(hd.DocTotal);
					$('#down-amount').val(addCommas(ds.downPaymentUse));
					$('#doc-balance').val(addCommas(balance));

					if(hd.branch_code != "") {
						$('#is-company').prop('checked', true);
					}
					else {
						$('#is-company').prop('checked', false);
					}

					let data = ds.details;
					let source = $('#wo-template').html();
					let output = $('#detail-table');

					render(source, data, output);

					reIndex();

					let dps = ds.down_payment_list;
					let dsource = $('#down-payment-template').html();
					let doutput = $('#down-payment-table');

					render(dsource, dps, doutput);

					reIndex('dp-no');

					toggleVatType();

					$('#ref-type').attr('disabled', 'disabled');
					$('#bill-code').attr('disabled', 'disabled');
					$('#btn-submit-bill').addClass('hide');
					$('#btn-change-bill').removeClass('hide');
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error',
						html:true
					}, function() {
						setTimeout(() => {
							$('#wo-code').focus();
						}, 200);
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
		}
	})
}

function woInit() {
	$('#bill-code').autocomplete({
		source:HOME + 'get_wo_code',
		autoFocus:true
	});
}

function wuInit() {
	$('#bill-code').autocomplete({
		source:HOME + 'get_wu_code',
		autoFocus:true
	});
}


function posInit() {
	$('#bill-code').autocomplete({
		source:HOME + 'get_bill_code',
		autoFocus:true
	});
}


function dpInit() {
	$('#bill-code').autocomplete({
		source:HOME + 'get_dp_code',
		autoFocus:true
	});
}


function getOrderDetails() {
	let code = $.trim($('#bill-code').val());
	let refType = $('#ref-type').val();

	if(refType == 'WO' && code.length > 5) {
		getWoDetails(code);
	}

	if(refType == 'WU' && code.length > 5) {
		getWuDetails(code);
	}

	if(refType == 'POS' && code.length > 5) {
		getBillDetails(code);
	}

	if(refType == 'DP' && code.length > 5) {
		getDpDetails(code);
	}
}





function changeBill() {
	swal({
		title:'Change Bill',
		text:'รายการปัจจุบันจะถูกลบ ต้องการดำเนินการต่อหรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonText:"Yes",
		cancelButtonText:"No",
		confirmButtonColor:"#428bca",
		closeOnConfirm:true
	}, function() {
		$('#customer-code').val('');
		$('#customer-name').val('');
		$('#tax-id').val('');
		$('#billCode').val('');
		$('#bill-code').val('');
		$('#detail-table').html('');
		$('#total-qty').val('');
		$('#total-amount').val('');
		$('#bill-disc-percent').val('');
		$('#bill-disc-amount').val('');
		$('#vat-total').val('');
		$('#doc-total').val('');
		$('#sale-id').val($('#default_sale_id').val());
		$('#owner').val('');
		$('#remark').val('');

		$('#ref-type').removeAttr('disabled');
		$('#bill-code').removeAttr('disabled');
		$('#btn-change-bill').addClass('hide');
		$('#btn-submit-bill').removeClass('hide');

		setTimeout(() => {
			$('#bill-code').focus();
		}, 100);
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


var click = 0;

function addInvoice() {
	if(click > 0) {
		return false;
	}

	$('#btn-save').attr('disabled', 'disabled');

	click = 1;

	$('.h').removeClass('has-error');

	let h = {
		'vat_type' : $('#vat-type').val(),
		'is_term' : $('#is-term').val(),
		'taxStatus' : $('#tax-status').val(),
		'date_add' : $('#date').val(),
		'customer_code' : $('#customer-code').val(),
		'customer_name' : $('#customer-name').val(),
		'customer_ref' : $('#customer-ref').val(),
		'is_company' : $('#is-company').is(':checked') ? 1 : 0,
		'phone' : $('#phone').val(),
		'branch_code' : $('#branch-code').val(),
		'branch_name' : $('#branch-name').val(),
		'tax_id' : $('#tax-id').val(),
		'address' : $('#address').val(),
		'sub_district' : $('#sub-district').val(),
		'district' : $('#district').val(),
		'province' : $('#province').val(),
		'postcode' : $('#postcode').val(),
		'sale_id' : $('#sale-id').val(),
		'remark' : $('#remark').val(),
		'refType' : $('#ref-type').val(),
		'bill_code' : $('#bill-code').val(),
		'billCode' : $('#billCode').val(),  //-- hidden bill code for check difference bill code
		'amountBfDisc' : parseDefault(parseFloat(removeCommas($('#total-amount').val())), 0),
		'billDiscPrcnt' : parseDefault(parseFloat($('#bill-disc-percent').val()), 0),
		'billDiscAmount' : parseDefault(parseFloat(removeCommas($('#bill-disc-amount').val())), 0),
		'whtPrcnt' : parseDefault(parseFloat($('#whtPrcnt').val()), 0),
		'whtAmount' : parseDefault(parseFloat($('#wht-amount').val()), 0),
		'vatSum' : parseDefault(parseFloat(removeCommas($('#vat-total').val())), 0),
		'docTotal' : parseDefault(parseFloat(removeCommas($('#doc-total').val())), 0)
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

	if(h.is_term == "") {
		swal("กรุณาเลือกเล่มเอกสาร");
		$('#is-term').addClass('has-error');
		$('#btn-save').removeAttr('disabled');
		click = 0;
		return false;
	}

	if(h.vat_type == "") {
		swal("กรุณาเลือกชนิด VAT");
		$('#vat-type').addClass('has-error');
		$('#btn-save').removeAttr('disabled');
		click = 0;
		return false;
	}

	if( ! isDate(h.date_add)) {
		swal("วันที่ไม่ถูกต้อง");
		$('#date').addClass('has-error');
		$('#btn-save').removeAttr('disabled');
		click = 0;
		return false;
	}

	if( h.customer_code.length == 0) {
		swal("กรุณาระบุรหัสลูกค้า");
		$('#customer-code').addClass('has-error');
		$('#btn-save').removeAttr('disabled');
		click = 0;
		return false;
	}

	if( h.customer_name.length == 0) {
		swal("กรุณาระบุชื่อลูกค้า");
		$('#customer-name').addClass('has-error');
		$('#btn-save').removeAttr('disabled');
		click = 0;
		return false;
	}

	if(h.taxStatus == 'Y') {
		let err = 0;
		let msg = "กรุณาระบุที่อยู่";

		if(h.tax_id.length < 10) {
			$('#tax-id').addClass('has-error');
			msg = "กรุณาระบุเลขที่ผู้เสียภาษี";
			err++;
		}

		if(h.address == "") {
			$('#address').addClass('has-error');
			err++;
		}

		if(h.sub_district == "") {
			$('#sub-district').addClass('has-error');
			err++;
		}

		if(h.district == "") {
			$('#district').addClass('has-error');
			err++;
		}

		if(h.province == "") {
			$('#province').addClass('has-error');
			err++;
		}

		if(err > 0) {
			swal(msg);
			$('#btn-save').removeAttr('disabled');
			click = 0;
			return false;
		}
	}

	if( h.bill_code == "" || h.bill_code != h.billCode) {
		swal("เลขที่เอกสารไม่ถูกต้อง");
		$('#btn-save').removeAttr('disabled');
		click = 0;
		return false;
	}

	if(h.docTotal < totalDownAmount) {
		swal("ยอดตัดเงินมัดจำไม่ถูกต้อง กรุณาแก้ไข");
		$('#down-tab').trigger('click');
		$('#btn-save').removeAttr('disabled');
		click = 0;
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'add_invoice',
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
					type:'error'
				})
			}
		}
	})

}

function addTaxInvoice() {
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
	let invoice_code = $('#selected-bill-invoice').val();

	if(invoice_code.length) {
		swal({
			title:'Error!',
			text:"บิลนี้ถูกเปิดใบกำกับภาษีไปแล้ว <br/>ใบกำกับภาษีเลขที่ :  "+invoice_code,
			type:'warning',
			html:true
		}, function() {
			setTimeout(() => {
				$('#bill-code').focus();
			}, 200);
		});

		return false;
	}

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

	setTimeout(() => {
		load_in();

		$.ajax({
			url:HOME + 'add_invoice',
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
					$('#address').val(parseAddress(h.address, h.subDistrict, h.district, h.province, h.postcode));
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
}

function parseAddress(address, subDistrict, district, province, postcode) {
	let addr = address;

	if(subDistrict != "" || subDistrict != null || subDistrict != undefined) {
		addr = addr + " ต. " + subDistrict;
	}

	if(district != "" || disctrict != null || district != undefined) {
		addr = addr + " อ. " + district;
	}

	if(province != "" || province != null || province != undefined) {
		addr = addr + " จ. " + province;
	}

	if(postcode != "" || postcode != null || postcode != undefined) {
		addr = addr + " " +postcode;
	}

	return addr;
}


$('#tax-search').keydown(function(e) {
	if(e.keyCode == 13) {
		getCustomerByTaxId();
	}

	if(e.keyCode == 114) {
		e.preventDefault();
		newCustomer();
	}

	if(e.keyCode == 121) {
		e.preventDefault();
		addCustomer();
	}
});


$('#form-name').keyup(function(e) {
	if(e.keyCode == 13) {
		$('#form-tax-id').focus();
	}
});

$('#form-tax-id').keyup(function(e) {
	if(e.keyCode == 13) {
		$('#form-branch-code').focus();
	}
});

$('#form-branch-code').keyup(function(e) {
	if(e.keyCode == 13) {
		$('#form-branch-name').focus();
	}
});

$('#form-branch-name').keyup(function(e) {
	if(e.keyCode == 13) {
		$('#address').focus();
	}
});

$('#form-address').keyup(function(e) {
	if(e.keyCode == 13) {
		$('#phone').focus();
	}
});

$('#form-phone').keyup(function(e) {
	if(e.keyCode == 13) {
		$('#form-is-company').focus();
	}
});


function recalTotal() {
  let totalQty = 0;
  let totalBfDisc = 0.00; //--- มูลค่ารวมสินค้าหลังส่วนลดรายการ ก่อนส่วนลดท้ายบิล
	let billDiscAmount = roundNumber(parseDefault(parseFloat(removeCommas($('#bill-disc-amount').val())), 0.00), 2); //--- มูลค่าส่วนลดท้ายบิล
  let billDiscPrcnt = roundNumber(parseDefault(parseFloat($('#bill-disc-percent').val()), 0), 2);
	let totalTaxAmount = 0.00; //-- มูลค่าภาษีรวมหลังส่วนลดท้ายบิล
	let downPayment = parseDefault(parseFloat($('#down-amount').val()), 0.00);
	let whtPrcnt = roundNumber(parseDefault(parseFloat($('#whtPrcnt').val()), 0.00), 2); //--- หัก ณ ที่จ่าย
	let vatType = $('#vat-type').val() == 'E' ? 'E' : 'I';

	$('.line-qty').each(function() {
		let no = $(this).data('id');
		let qty = roundNumber(parseDefault(parseFloat($('#qty-'+no).val()), 0.00), 2);
		let price = roundNumber(parseDefault(parseFloat($('#price-'+no).val()), 0.00), 2);
		let amount = roundNumber(parseDefault(parseFloat(removeCommas($('#total-'+no).val())), 0.00), 2);

		if(qty > 0 && price > 0)
		{
			totalBfDisc += amount; //-- ่รวมยอดสินค้า
      totalQty += qty;
		}
	});

  if(billDiscPrcnt > 0) {
    billDiscAmount = totalBfDisc * (billDiscPrcnt * 0.01);
  }

  amountAfterDisc = parseDefault(parseFloat(totalBfDisc - billDiscAmount), 0.00); //--- มูลค่าสินค้า หลังหักส่วนลดท้ายบิล

	//---- เฉลี่ยส่วนลดท้ายบิล
	//--- เฉลี่ยส่วนลดออกให้ทุกรายการ โดยเอาส่วนลดท้ายบิล(จำนวนเงิน)/มูลค่าสินค้าก่อนส่วนลด
	//--- ได้มูลค่าส่วนลดท้ายบิลที่เฉลี่ยนแล้ว ต่อ บาท เช่น หารกันมาแล้ว ได้ 0.16 หมายถึงทุกๆ 1 บาท จะลดราคา 0.16 บาท
	everageBillDisc = parseDefault(parseFloat((totalBfDisc > 0 ? billDiscAmount/totalBfDisc : 0)), 0);

  //--- คำนวนภาษี
	//--- นำผลลัพธ์ข้างบนมาคูณ กับ มูลค่าที่ต้องคิดภาษี (ตัวที่ไม่มีภาษีไม่เอามาคำนวณ)
	//--- จะได้มูลค่าส่วนลดที่ต้องไปลบออกจากมูลค่าสินค้าที่ต้องคิดภาษี
	$('.line-qty').each(function() {
		let no = $(this).data('id');
		let qty = roundNumber(parseDefault(parseFloat($('#qty-'+no).val()), 0.00), 2);
		let price = roundNumber(parseDefault(parseFloat($('#price-'+no).val()), 0.00), 2); //--- ราคาขายก่อนส่วนลดรายการ
		let amount = roundNumber(parseDefault(parseFloat(removeCommas($('#total-'+no).val())), 0.00), 2); //--- มูลค่ารวมหลังส่วนลดรายการของแต่ละ item (qty * (price - discount))
		let rate = parseDefault(parseFloat($('#qty-'+no).data('vatrate')), 0.00); //--- ภาษีของแต่ละ Item

		if(qty > 0 && price > 0)
		{
			if(rate > 0) {
				discAmount = amount * everageBillDisc; //---- มูลค่าส่วนลดท้ายบิลเฉลี่ย เฉพาะที่มีภาษี
				amountAfDisc = amount - discAmount; //---- มูลค่าหลังส่วนลดท้ายบิลเฉลี่ย เฉพาะที่มีภาษี
				totalTaxAmount += amountAfDisc > 0 ? (vatType == 'E' ? amountAfDisc * (rate * 0.01) : (amountAfDisc * rate) / (100 + rate)) : 0; //--- รวมยอดภาษี
			}
		}
	});


	totalTaxAmount = roundNumber(totalTaxAmount, 2);
	whtAmount = vatType == 'E' ? roundNumber(amountAfterDisc * (whtPrcnt * 0.01)) : roundNumber((amountAfterDisc - totalTaxAmount) * (whtPrcnt * 0.01), 2);
	amountAfterDiscAndTax = vatType == 'E' ? roundNumber(amountAfterDisc + totalTaxAmount, 2) : roundNumber(amountAfterDisc, 2);

  docTotal = vatType == 'E' ? roundNumber((amountAfterDisc + totalTaxAmount), 2) : roundNumber(amountAfterDisc, 2);
	docTotal = docTotal - downPayment - whtAmount;

	//--- update bill discount
	$('#total-qty').val(addCommas(totalQty));
  $('#total-amount').val(addCommas(totalBfDisc.toFixed(2)));
  $('#bill-disc-amount').val(addCommas(billDiscAmount.toFixed(2)));
  $('#whtPrcnt').val(whtPrcnt.toFixed(2));
	$('#wht-amount').val(whtAmount);
	$('#wht-amount-label').val(addCommas(whtAmount.toFixed(2)));
	$('#vat-total').val(totalTaxAmount);
	$('#vat-total-label').val(addCommas(totalTaxAmount.toFixed(2)));
  $('#doc-total').val(addCommas(docTotal.toFixed(2)));

	recalDownPayment();
}


function recalDownPayment() {
	$('.down-amount').removeClass('has-error');
	let total = 0;
	let err = 0;
	let msg = "ยอดตัดมัดจำไม่ถูกต้อง กรุณาแก้ไข";

	$('.down-amount').each(function() {
		let amount = parseDefault(parseFloat($(this).val()), 0);
		let available = parseDefault(parseFloat($(this).data('available')), 0);

		if(available < amount) {
			$(this).addClass('has-error');
			err++;
		}
		else {
			total += amount;
		}
	});

	if(err > 0) {
		swal({
			title:'ข้อผิดพลาด',
			text:msg,
			type:'error'
		});

		return false;
	}

	let docTotal = parseDefault(parseFloat(removeCommas($('#doc-total').val())), 0);

	let balance = docTotal - total;
	balance = balance < 0 ? 0 : balance;

	$('#down-amount').val(addCommas(total.toFixed(2)));
	$('#doc-balance').val(addCommas(balance.toFixed(2)));
}
