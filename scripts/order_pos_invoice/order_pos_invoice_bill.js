window.addEventListener('load', () => {
	let height = $(window).height();
	let pageContentHeight = height - 123;
	let billViewHeight = pageContentHeight - (125);

	$('.page-content').css('height', pageContentHeight + 'px');
	$('#bill-div').css('height', billViewHeight + 'px');
	$('#bill-view').css('height', billViewHeight + 'px');
})



function getBillView(code) {
	load_in();

	$.ajax({
		url:BASE_URL + 'orders/order_pos/get_bill_view',
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
					let source = $('#bill-template').html();
					let output = $('#bill-view');

					render(source, data, output);
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error'
					}, function() {
						setTimeout(() => {
							$('#bill-code').focus();
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


$('#bill-code').autocomplete({
	source:HOME + 'get_bill_code',
	autoFocus:true
});

$('#bill-code').keyup((e) => {
	if(e.keyCode == 13) {
		setTimeout(() => {
			let code = $.trim($('#bill-code').val());

			if(code.length > 5) {
				getBillView(code);
			}
		}, 200);
	}
});

$('#btn-submit-bill').click(() => {
	let code = $.trim($('#bill-code').val());
	if(code.length > 5) {
		getBillView(code);
	}
})


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


function printInvoice(code) {
	let width = 800;
	let height = 800;
	let center = (window.innerWidth - width)/2;
	let prop = "width="+width+", height="+height+", left="+center+", scrollbars=yes";
	let target = HOME + 'print_invoice/'+code;
	window.open(target, '_blank', prop);
}


function addNewCustomer() {
	let taxId = $('#tax-search').val();

	$('.cust-form').removeAttr('disabled');
	$('#tax-id').val(taxId);
	$('#cust-id').val('');
	$('#name').focus();
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
