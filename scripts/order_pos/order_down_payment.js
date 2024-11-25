window.addEventListener('load', () => {
	resizeDisplay();
})

window.addEventListener('resize', () => {
	resizeDisplay();
});

function resizeDisplay() {
	let height = $(window).height();
	let footerHeight = $('#page-footer').height();
	let filterHeight = $('#search-row').height();
	let hr = 20;
	let pagination = $('#pagination').height();
	let pageContentHeight = height - 53;
	let billTableHeight = pageContentHeight - (footerHeight + filterHeight + hr + pagination + 30); //-- 155
	let billViewHeight = pageContentHeight - (pagination + hr + 15);

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

$('#receive-date').datepicker({
	dateFormat:'dd-mm-yy',
	beforeShow:function() {
		setTimeout(() => {
			$('.ui-datepicker').css('z-index', 10000);
		}, 100)
	}
});


function down_payment_list() {
	let shop_id = $('#shop_id').val();
	let deviceId = getDeviceId();
	window.location.href = HOME + 'down_payment_list/'+shop_id+'/'+deviceId;
}

function getSearch() {
	$('#searchForm').submit();
}

function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){ down_payment_list(); });
}

function getDownPaymentView(code) {
	load_in();

	$.ajax({
		url:BASE_URL + 'orders/order_pos/get_down_payment_view',
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
					let source = $('#bill-view-template').html();
					let output = $('#bill-view');

					render(source, data, output);
				}
			}
		}
	})
}


function printDownPayment(code) {
	let width = 400;
	let height = 600;
	let center = (window.innerWidth - width)/2;
	let prop = "width="+width+", height="+height+", left="+center+", top=100, scrollbars=yes";
	let target = HOME + 'print_down_payment/'+code;
	window.open(target, '_blank', prop);
}


function cancelDownPayment(code, id) {
	swal({
		title:'ยกเลิกบิล',
		text:'ต้องการยกเลิก '+code+' หรือไม่ ?',
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
	$('#cancel-reason').clearError();

	let id = $('#cancel-id').val();
	let code = $('#cancel-code').val();
	let reason = $('#cancel-reason').val().trim();
	let shop_id = $('#shop_id').val();
	let pos_id = $('#pos_id').val();

	if(reason.length == 0) {
		$('#cancel-reason').hasError().focus();
		return false;
	}

	$('#cancelModal').modal('hide');

	load_in();

	setTimeout(() => {
		$.ajax({
			url:BASE_URL + 'orders/order_down_payment/cancel_payment',
			type:'POST',
			cache:false,
			data:{
				'shop_id' : shop_id,
				'pos_id' : pos_id,
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
						$('#btn-print').remove();
						$('#btn-cancel').remove();
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


function getSoBill() {
	let date = $('#to-day').val();
	$('#receive-date').val(date);
	$('#title').text("รับมัดจำใบสั่งขาย");
	$('#doc-type').val('SO');
	$('#bill-result-table').html('');
	$('#bill-view-table').addClass('hide');
	$('.so').val('');
	$('#changeAmount').val('0.00');
	$('#bill-search').val('');
	searchInit('SO');

  $('#downModal').modal('show');
}


function getWoBill() {
	let date = $('#to-day').val();
	$('#receive-date').val(date);
	$('#title').text("รับมัดจำใบออเดอร์");
	$('#doc-type').val('WO');
	$('#bill-result-table').html('');
	$('#bill-view-table').addClass('hide');
	$('.so').val('');
	$('#changeAmount').val('0.00');
	$('#bill-search').val('');
	searchInit('WO');

  $('#downModal').modal('show');
}


$('#downModal').on('shown.bs.modal', function() {
  $('#bill-search').focus();
});


function searchInit(docType) {
	if(docType == 'WO') {
		$('#bill-search').autocomplete({
			source:HOME + 'search_wo_code',
			autoFocus:true,
			close:function() {
				let rs = $(this).val();
				let arr = rs.split(' | ');

				if(arr.length == 2) {
					$(this).val(arr[0]);
				}
				else {
					$(this).val('');
				}
			}
		});
	}
	else {
		$('#bill-search').autocomplete({
			source:HOME + 'search_so_code',
			autoFocus:true,
			close:function() {
				let rs = $(this).val();
				let arr = rs.split(' | ');

				if(arr.length == 2) {
					$(this).val(arr[0]);
				}
				else {
					$(this).val('');
				}
			}
		});
	}
}


function getSoData() {
	let code = $('#bill-search').val();
	let shop_id = $('#shop_id').val();
	let pos_id = $('#pos_id').val();

  if(code.length < 7) {
    $('#bill-search').addClass('has-error');
    return false;
  }
	else {
		$('#bill-search').removeClass('has-error');
	}

	load_in();

  $.ajax({
    url:HOME + 'get_so_view',
    type:'GET',
    cache:false,
    data:{
      'code' : code,
			'shop_id' : shop_id,
			'pos_id' : pos_id
    },
    success:function(rs) {
			load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
					$('#bill-result-table').html('');
					$('.so').val('');
					$('#changeAmount').val('0.00');
					let data = ds.data;
					$('#so-code').val(data.code);
					$('#customer-name').val(data.customer_ref);
					$('#amount').val(data.TotalBalance);
					$('#amount-label').val(addCommas(data.TotalBalance));
					$('#depositAmount').val(addCommas(data.DepAmount));
					$('#balanceAmount').val(addCommas(data.DepAmount));

					$('#bill-view-table').removeClass('hide');
					$('#cashReceive').focus();
        }
        else {
					result = '<h4 class="title-xs red text-center">'+ds.message+'</h4>';
          $('#bill-result-table').html(result);
					$('#bill-view-table').addClass('hide');
					$('.so').val('');
					$('#changeAmount').val('0.00');
					$('#bill-search').focus();
        }
      }
      else {
				result = '<h4 class="title-xs red text-center">'+rs+'</h4>';
				$('#bill-result-table').html(result);
				$('#bill-view-table').addClass('hide');
				$('.so').val('');
				$('#changeAmount').val('0.00');
				$('#bill-search').focus();
      }
    }
  })
}


function getWoData() {
	let code = $('#bill-search').val();
	let shop_id = $('#shop_id').val();
	let pos_id = $('#pos_id').val();

  if(code.length < 7) {
    $('#bill-search').addClass('has-error');
    return false;
  }
	else {
		$('#bill-search').removeClass('has-error');
	}

	load_in();

  $.ajax({
    url:HOME + 'get_wo_view',
    type:'GET',
    cache:false,
    data:{
      'code' : code,
			'shop_id' : shop_id,
			'pos_id' : pos_id
    },
    success:function(rs) {
			load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
					$('#bill-result-table').html('');
					$('.so').val('');
					$('#changeAmount').val('0.00');
					let data = ds.data;
					$('#so-code').val(data.code);
					$('#customer-name').val(data.customer_ref);
					$('#amount').val(data.TotalBalance);
					$('#amount-label').val(addCommas(data.TotalBalance));
					$('#depositAmount').val(addCommas(data.DepAmount));
					$('#balanceAmount').val(addCommas(data.DepAmount));

					$('#bill-view-table').removeClass('hide');
					$('#cashReceive').focus();
        }
        else {
					result = '<h4 class="title-xs red text-center">'+ds.message+'</h4>';
          $('#bill-result-table').html(result);
					$('#bill-view-table').addClass('hide');
					$('.so').val('');
					$('#changeAmount').val('0.00');
					$('#bill-search').focus();
        }
      }
      else {
				result = '<h4 class="title-xs red text-center">'+rs+'</h4>';
				$('#bill-result-table').html(result);
				$('#bill-view-table').addClass('hide');
				$('.so').val('');
				$('#changeAmount').val('0.00');
				$('#bill-search').focus();
      }
    }
  })
}


function getBillView() {
  let docType = $('#doc-type').val();

	if(docType == 'WO') {
		getWoData();
	}
	else {
		getSoData();
	}
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



$('#depositAmount').keydown(function(e) {
	if(e.keyCode == 40) {
		e.preventDefault();
		let role = $('#payment-role').val();
		let amount = parseDefault(parseFloat($(this).val()), 0.00);

		if(role == 2 || role == 3) {
			$('#receiveAmount').val(amount);
		}

		$('#receiveAmount').focus();
	}
})

$('#depositAmount').keyup(function(e) {
	if(e.keyCode == 13) {
		let role = $('#payment-role').val();
		let amount = parseDefault(parseFloat($(this).val()), 0.00);

		if(role == 2 || role == 3) {
			$('#receiveAmount').val(amount);
		}

		$('#receiveAmount').focus();
	}

	calChange();
})

$('.so').keyup(function(e) {
	$(this).removeClass('has-error');

	if(e.keyCode == 13) {
		submitPayment();
	}
	else {
		if($(this).val() < 0) {
			$(this).addClass('has-error');
		}

		calChange();
	}
});

$('#cashReceive').keydown(function(e) {
	if(e.keyCode === 38) {
		e.preventDefault();
	}

	if(e.keyCode === 40) {
		e.preventDefault();
		$('#transferAmount').focus();
	}

	if(e.keyCode === 32) {
		e.preventDefault();
		$(this).val('');
	}
})

$('#transferAmount').keydown(function(e) {
	if(e.keyCode === 38) {
		e.preventDefault();
		$('#cashReceive').focus();
	}

	if(e.keyCode === 40) {
		e.preventDefault();
		$('#cardAmount').focus();
	}

	if(e.keyCode === 32) {
		e.preventDefault();
		$(this).val('');
	}
})

$('#cardAmount').keydown(function(e) {
	if(e.keyCode === 38) {
		e.preventDefault();
		$('#transferAmount').focus();
	}

	if(e.keyCode === 40) {
		e.preventDefault();
	}

	if(e.keyCode === 32) {
		e.preventDefault();
		$(this).val('');
	}
})


function calChange() {
	let depAmount = parseDefault(parseFloat(removeCommas($('#depositAmount').val())), 0.00);
	let cashReceive = parseDefault(parseFloat($('#cashReceive').val()), 0.00);
	let transferAmount = parseDefault(parseFloat($('#transferAmount').val()), 0);
	let cardAmount = parseDefault(parseFloat($('#cardAmount').val()), 0);
	let chequeAmount = parseDefault(parseFloat($('#chequeAmount').val()), 0);
	let receive = cashReceive + transferAmount + cardAmount + chequeAmount;
	let balance = depAmount;
	let change = 0;

	balance = balance - receive;

	if( receive > depAmount)
	{
		change = receive - depAmount;
		change = change <= cashReceive ? change : cashReceive;
	}

	change = change > 0 ? change : 0;
	balance = balance > 0 ? balance : 0;

	$('#changeAmount').val(addCommas(change.toFixed(2)));
	$('#balanceAmount').val(addCommas(balance.toFixed(2)));
}


function setPayment(payment_code, role) {
	$('#payment-code').val(payment_code);
	$('#payment-role').val(role);
	$('#change').removeClass('hide');
	let amount = parseDefault(parseFloat(removeCommas($('#depositAmount').val())), 0.00);

	if(role == 1) {
		$('.p').addClass('hide');
		$('#transferAmount').val('');
		$('#cardAmount').val('');
		$('#p-cash').removeClass('hide');
		$('#cashReceive').val('').focus();
	}

	if(role == 2) {
		$('.p').addClass('hide');
		$('#transferAmount').val('');
		$('#cardAmount').val('');
		$('#cashReceive').val('');
		$('#p-transfer').removeClass('hide');
		$('#p-account').removeClass('hide');
		$('#transferAmount').val(amount).focus();
		$('#change').addClass('hide');
	}

	if(role == 3) {
		$('.p').addClass('hide');
		$('#transferAmount').val('');
		$('#cardAmount').val('');
		$('#cashReceive').val('');
		$('#chequeAmount').val('');
		$('#p-card').removeClass('hide');
		$('#cardAmount').val(amount).focus();
		$('#change').addClass('hide');
	}

	if(role == 7) {
		$('.p').addClass('hide');
		$('#transferAmount').val('');
		$('#cardAmount').val('');
		$('#cashReceive').val('');
		$('#p-cheque').removeClass('hide');
		$('#chequeAmount').val(amount).focus();
		$('#change').addClass('hide');
	}

	if(role == 6) {
		$('.p').removeClass('hide');
		$('#transferAmount').val('');
		$('#cardAmount').val('');
		$('#chequeAmount').val('');
		$('#cashReceive').val('');
		$('#cashReceive').focus();
	}

	$('.btn-role').removeClass('btn-success');
	$('#btn-role-'+role).addClass('btn-success');

	calChange();
}

function submitPayment() {
	$('#payment-error').val('');
	$('#depositAmount').removeClass('has-error');
	$('#receiveAmount').removeClass('has-error');

	let paymentDate = $('#receive-date').val();
	let doc_type = $('#doc-type').val();
	let pos_id = $('#pos_id').val();
	let shop_id = $('#shop_id').val();
	let acc_id = $('#acc-id').val();
	let soCode = $('#so-code').val();
	let role = $('#payment-role').val();
	let paymentCode = $('#payment-code').val();
	let amount = parseDefault(parseFloat(removeCommas($('#amount').val())), 0);
	let payAmount = parseDefault(parseFloat(removeCommas($('#depositAmount').val())), 0.00);
	let cashReceive = parseDefault(parseFloat($('#cashReceive').val()), 0.00);
	let transferAmount = parseDefault(parseFloat($('#transferAmount').val()), 0);
	let cardAmount = parseDefault(parseFloat($('#cardAmount').val()), 0);
	let chequeAmount = parseDefault(parseFloat($('#chequeAmount').val()), 0);
	let receive = cashReceive + transferAmount + cardAmount + chequeAmount;
	let nonCash =  transferAmount + cardAmount + chequeAmount;
	let cashAmount = role == 1 ? payAmount : 0;
	let paidAmount = payAmount;

	if( ! isDate(paymentDate)) {
		$('#payment-error').val('วันที่ไม่ถูกต้อง');
		return false;
	}

	if(role == 6) {
		if(transferAmount > 0 && acc_id == "") {
			$('#payment-error').val('กรุณาเลือกบัญชีธนาคาร');
			return false;
		}

		if(receive > payAmount) {
			if(nonCash > payAmount)
			{
				if(cashReceive > 0) {
					$('#payment-error').val('ยอดเงินเกินมูลค่ามัดจำ');
					return false;
				}
				else {
					paidAmount = nonCash;
					cashAmount = 0;
				}
			}
			else {
				cash = payAmount - nonCash;
				cashAmount = cash > 0 ? cash : 0;
				paidAmount = nonCash + cashAmount;
			}
		}
		else {
			cashAmount = cashReceive;
			paidAmount = payAmount;
		}
	}
	else if(role == 2) {
		paidAmount = transferAmount;

		if(acc_id == "") {
			$('#payment-error').val('กรุณาเลือกบัญชีธนาคาร');
			return false;
		}
	}
	else if(role == 3) {
		paidAmount = cardAmount;
	}
	else {
		paidAmount = payAmount;
	}


	let change = receive - paidAmount;

	if(soCode.length) {

		if(receive > payAmount && (role == 2 || role == 3 || role == 7)) {
			change = 0;
		}

		if(receive < payAmount) {
			$('#payment-error').val('รับเงินไม่ครบ');
			return false;
		}

		let ds = {
			'pos_id' : pos_id,
			'shop_id' : shop_id,
			'acc_id' : acc_id,
			'so_code' : soCode,
			'doc_type' : doc_type,
			'payment_role' : role,
			'payment_code' : paymentCode,
			'amount' : paidAmount,
			'cashReceive' : cashReceive,
			'cashAmount' : cashAmount,
			'transferAmount' : transferAmount,
			'cardAmount' : cardAmount,
			'chequeAmount' : chequeAmount,
			'receive' : receive,
			'change' : change,
			'paymentDate' : paymentDate
		}

		$('#downModal').modal('hide');

		load_in();

		$.ajax({
			url:HOME + 'add_down_payment',
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

						let source = $('#down-payment-template').html();
						let output = $('#down-payment-list');
						render_prepend(source, ds.data, output);

						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(() => {
							printDownPayment(ds.data.code);

						}, 1200);
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
}
