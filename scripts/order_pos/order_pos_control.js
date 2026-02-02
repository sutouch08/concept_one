var autoFocus = 0;

window.addEventListener('load', () => {
	resizeDisplay();
	percent_init();
	focus_init();

	let round_id = $('#round-id').val();

	if(! round_id) {
		disabledControl();
	}
})

window.addEventListener('resize', () => {
	resizeDisplay();
});

function disabledControl() {
	$('.focus').attr('disabled', 'disabled');
	$('.pos-payment-btn').attr('disabled', 'disabled');
	$('.r-btn').attr('disabled', 'disabled');
}

function resizeDisplay() {
	let height = $(window).height();
	let pageContentHeight = height - 45;
	let footerHeight = $('.pg-footer-content').height();
	//padding-top = 8, padding-bottom = 24, header = 87, hr = 15, table margin = 10, footer 170, margin-bottom = 15
	let itemTableHeight = pageContentHeight - (8 + 87 + 24 + 15 + 10 + footerHeight + 15 + 55);
	let rightBlock = itemTableHeight;

	$('.page-content').css('height', pageContentHeight + 'px');
	$('#item-div').css('height', itemTableHeight + 'px');
	$('#right-block').css('height', rightBlock+'px');
}

function openRoundInit() {
	$('#open-amount').removeAttr('disabled').val('');

	$('#open-amount').keyup(function(e) {
		if(e.keyCode == 13) {
			openRound();
		}
	});

	$('#openRoundModal').on('shown.bs.modal', () => {
		$('#open-amount').focus();
	});

	$('#openRoundModal').modal('show');
}

function focus_init() {
	$('.focus').focusout(function() {
		autoFocus = 1
		setTimeout(() => {
			if(autoFocus == 1) {
				barcodeFocus();
			}
		}, 1000)
	})

	$('.focus').focusin(function() {
		autoFocus = 0;
	});
}

function barcodeFocus() {
	$('#item-barcode').focus();
}


$('#receive-date').datepicker({
	dateFormat:'dd-mm-yy',
	beforeShow:function() {
		setTimeout(() => {
			$('.ui-datepicker').css('z-index', 10000);
		}, 100)
	}
});

function getItemDetail() {
	var zone_code = $('#zone_code').val();
	var code = $('#pd-box').val();

	if(code.length) {

		$('#item-code-label').text(code);

		$.ajax({
			url: HOME + 'get_product_data',
			type:'GET',
			cache:false,
			data:{
				'product_code' : code,
				'zone_code' : zone_code
			},
			success:function(rs) {
				var rs = $.trim(rs);
				if(isJson(rs)) {
					var ds = $.parseJSON(rs);
					var source = $('#item-template').html();
					var output = $('#item-data');

					render(source, ds, output);

					$('#productModal').modal('show');

					$('#productModal').on('hidden.bs.modal', function() {
						$('#pd-box').focus();
					});
				}
				else {
					swal("Product not found");
				}
			}
		})
	}
}


function dropTemp() {
	if($('.sell-item').length) {
		let temp_id = $('#order-temp-id').val();

		$.ajax({
			url: HOME + 'clear_temp_details',
			type:'POST',
			cache:false,
			data:{
				'temp_id' : temp_id
			},
			success: function(rs) {
				if(rs === 'success') {
					$('#item-table').html('');
					$('#total-item').val(0);
					$('#net-amount').val(0);
					$('#total-amount').val(0);
					$('#total-discount').val(0);
					$('#total-tax').val(0);
					$('#item-barcode').focus();
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error',
						html:true
					}, function() {
						$('#item-barcode').focus();
					});
				}
			}
		});
	}
	else {
		$('#item-barcode').focus();
	}
}


$('#item-barcode').autocomplete({
	source:BASE_URL + 'auto_complete/get_item_barcode',
	autoFocus:true,
	close:function() {
		let arr = $(this).val().split(' | ');
		$(this).val(arr[0]);
	}
})

$('#item-barcode').keyup(function(e){
	if(e.keyCode === 13) {
		let is_free = 0;
		let barcode = $.trim($(this).val());
    let qty = parseDefault(parseFloat($('#item-qty').val()), 1.00);

		if(barcode.length) {
			$(this).val('');
      $('#item-qty').val(1.00);
			addToTemp(barcode, qty, is_free);
			$('#barcode-item').focus();
		}

		return;
	}
});

$('#free-item-barcode').keyup(function(e){
	if(e.keyCode === 13) {
		let is_free = 1;
		let barcode = $.trim($(this).val());
    let qty = parseDefault(parseFloat($('#item-qty').val()), 1.00);

		if(barcode.length) {
			$(this).val('');
      $('#item-qty').val(1.00);
			addToTemp(barcode, qty, is_free);
			$('#free-item-barcode').focus();
		}

		return;
	}
});


$('#so-code').autocomplete({
	source:BASE_URL + 'auto_complete/get_so_code',
	autoFocus:true,
	position:{
		my:"left bottom",
		at:"left top",
		collision:"flip"
	}
});


$('#so-code').keyup(function(e) {
	if(e.keyCode == 13) {
		setTimeout(() => {
			addSaleOrder();
		},100);
	}
})


$('#pc-code').keyup(function(e) {
	if(e.keyCode === 13) {
		setPc();
	}
});


function setPc() {
	let pcCode = $('#pc-code').val();
	let prevCode = $('#pcCode').val();
	let temp_id = $('#order-temp-id').val();

	if(pcCode.length) {
		$.ajax({
			url:HOME + 'set_pc',
			type:'POST',
			cache:false,
			data:{
				'temp_id' : temp_id,
				'pc_code' : pcCode
			},
			success:function(rs) {
				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status == 'success') {
						$('#pc-name').val(ds.pc.name);
						$('#pc-id').val(ds.pc.id);
						$('#pcCode').val(ds.pc.code);
						$('#item-barcode').focus();
					}
					else {
						beep();
						swal({
							title:'Error!',
							text:ds.message,
							type:'error',
						}, function() {
							setTimeout(() => {
								$('#pc-code').focus();
							}, 200);
						});
					}
				}
				else {
					beep();
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					}, function() {
						setTimeout(() => {
							$('#pc-code').focus();
						}, 200);
					});
				}
			}
		})
	}
}


function addToTemp(barcode, qty, is_free)
{
	let temp_id = $('#order-temp-id').val(); //--- order_pos_temp
  let pos_id = $('#pos_id').val();
	let payment_code = $('#payment-code').val();
	let channels_code = $('#channels-code').val();
	let customer_code = $('#customer-code').val();

	if(barcode.length > 0) {

		$.ajax({
			url: HOME + 'add_to_temp',
			type:'POST',
			cache:false,
			data:{
        'pos_id' : pos_id,
				'order_temp_id' : temp_id,
				'customer_code' : customer_code,
				'channels_code' : channels_code,
				'payment_code' : payment_code,
				'barcode' : barcode,
        'qty' : qty,
				'is_free' : is_free
			},
			success:function(rs) {
				if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            addToOrder(ds.data);
          }
          else {
						beep();
            swal({
              title:'Error!',
              text:ds.message,
              type:'error'
            });
          }
				}
				else {
					swal({
						title:'Error',
						text:rs,
						type:'error'
					});
				}
			},
			error:function(xhr, status, error) {
				swal({
					title:'Error!',
					text:'Error-'+xhr.status+': '+xhr.statusText,
					type:'error'
				});
			}
		})
	}
}


function confirmAddSaleOrder() {
	let soCode = $.trim($('#so-code').val());
	let count = $('.sell-item').length;

	if(count > 0) {
		swal({
			title:'Info',
			text:'รายการที่ค้างอยู่จะถูกลบ ต้องการดำเนินการต่อหรือไม่ ?',
			type:'info',
			showCancelButton:true,
			cancelButtonText:'No',
			confirmButtonText:'Yes',
			closeOnConfirm:true
		}, function() {
			setTimeout(() => {
				addSaleOrder();
			}, 100);
		})
	}
	else {
		addSaleOrder();
	}
}

function addSaleOrder() {
	let soCode = $.trim($('#so-code').val())
	let temp_id = $('#order-temp-id').val(); //--- order_pos_temp
  let pos_id = $('#pos_id').val();
	let payment_code = $('#payment-code').val();
	let channels_code = $('#channels-code').val();
	let customer_code = $('#customer-code').val();

	if(soCode.length) {
		load_in();

		$.ajax({
			url:HOME + 'add_so_to_temp',
			type:'POST',
			cache:false,
			data:{
        'pos_id' : pos_id,
				'order_temp_id' : temp_id,
				'customer_code' : customer_code,
				'channels_code' : channels_code,
				'payment_code' : payment_code,
				'so_code' : soCode
			},
			success:function(rs) {
				load_out();

				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status == 'success') {
						window.location.reload();
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
}


function clearSaleOrder() {
	let soCode = $.trim($('#so-code').val())
	let temp_id = $('#order-temp-id').val(); //--- order_pos_temp

	swal({
		title:'คุณแน่ใจ ?',
		text:'รายการที่นำเข้าจากใบสั่งงานเลขที่ '+soCode+' จะถูกลบ <br>ต้องการดำเนินการหรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonText:'Yes',
		cancelButtonText:'No',
		closeOnConfirm:true,
		html:true
	}, function() {
		load_in();
		setTimeout(() => {
			$.ajax({
				url:HOME + 'remove_so_temp',
				type:'POST',
				cache:false,
				data: {
					'temp_id' : temp_id,
					'so_code' : soCode
				},
				success:function(rs) {
					load_out();

					if(isJson(rs)) {
						let ds = JSON.parse(rs);

						if(ds.status === 'success') {
							window.location.reload();
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
					})
				}
			})
		}, 100);
	})
}

function addToOrder(ds) {

	$('#item-name-bar').val(ds.product_name);
	$('#item-price-bar').val(ds.price_label);

	if(ds.status == 'update') {
		let source = $('#update-template').html();
		let output = $('#row-'+ds.id);
		render(source, ds, output);
	}
	else {
		var source = $('#row-template').html();
		var output = $('#item-table');
		render_append(source, ds, output);
	}

  percent_init();
	focus_init();
  recalItem(ds.id);
  reIndex();
	hilightRow(ds.id);
	scrollToBottom();
}


function hilightRow(id) {
	$('.pos-rows').removeClass('active-row');
	$('#row-'+id).addClass('active-row');
}

function scrollToBottom() {
	let el = $('#item-div');
	el.scrollTop(el.prop('scrollHeight'));
}

function percent_init() {
	$('.line-disc').keyup(function(e) {
		if(e.keyCode === 32) {
			//-- press space bar
			var value = $.trim($(this).val());
			if(value.length) {
				var last = value.slice(-1);
				if(isNaN(last)) {
					//--- ถ้าตัวสุดท้ายไม่ใช่ตัวเลข เอาออก
					value = value.slice(0, -1);
				}
				value = value +"%";
				$(this).val(value);
			}
			else {
				$(this).val('');
			}

			recalItem($(this).data('id'));
		}
	})
}

function checkAll() {
	if($('#chk-all').is(':checked')) {
		$('.chk-row').prop('checked', true);
	}
	else {
		$('.chk-row').prop('checked', false);
	}
}


function removeItems() {
	let rows = [];
	let count = $('.chk-row:checked').length;

	if(count > 0) {
		$('.chk-row:checked').each(function() {
			rows.push($(this).val());
		});

		if(rows.length > 0) {
			$.ajax({
				url:HOME + 'remove_temp_rows',
				type:'POST',
				cache:false,
				data:{
					'rows' : rows
				},
				success:function(rs) {
					if(rs === 'success') {
						$('.chk-row:checked').each(function() {
							let id = $(this).val();
							$('#row-'+id).remove();
						});

						recalTotal();
						reIndex();
						barcodeFocus();
					}
					else {
						swal({
							title:'Error!',
							text:rs,
							type:'error'
						})
					}
				},
				error:function(xhr, status, error) {
					swal({
						title:'Error!!',
						type:'error',
						text:'Delete failed - '+ xhr.status+' : '+xhr+statusText
					}, function() {
						barcodeFocus();
					});
				}
			})
		}
	}



}


function reCalDiscount() {
	let soCode = $('#soCode').val();

	if(soCode.length == 0) {
		if($('.sell-item').length) {
			let temp_id = $('#order-temp-id').val(); //--- order_pos_temp
			let pos_id = $('#pos_id').val();
			let payment_code = $('#payment-code').val();
			let channels_code = $('#channels-code').val();
			let customer_code = $('#customer-code').val();

			load_in();

			$.ajax({
				url:HOME + 'recal_discount',
				type:'POST',
				cache:false,
				data: {
					'temp_id' : temp_id,
					'pos_id' : pos_id,
					'payment_code' : payment_code,
					'channels_code' : channels_code,
					'customer_code' : customer_code
				},
				success:function(rs) {
					load_out();

					if( isJson(rs)) {
						let ds = JSON.parse(rs);

						if(ds.status == 'success') {
							let source = $('#details-template').html();
							let output = $('#item-table');
							render(source, ds.data, output);
							recalTotal();
							reIndex();
							focus_init();
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
						title:'Error',
						text:xhr.responseText,
						type:'error'
					});
				}
			})
		}
	}
}


//--- toggle ปุ่ม recal/payment
function recalState(option) {
	if(option == 1) {
		$('#recal-btn').addClass('hide');
		$('#pay-btn').removeClass('hide');
	}
	else {
		$('#recal-btn').removeClass('hide');
		$('#pay-btn').addClass('hide');
	}
}


function setPayment(payment_code, role) {
	let temp_id = $('#order-temp-id').val();
	$('#payment-code').val(payment_code);
	$('#payment-role').val(role);
	$('#item-barcode').focus();

	load_in();

	$.ajax({
		url:HOME + 'update_temp_payment',
		type:'POST',
		cache:false,
		data: {
			"temp_id" : temp_id,
			"payment_code" : payment_code,
			"payment_role" : role
		},
		success:function(rs) {
			load_out();
			if(rs == 'success') {
				reCalDiscount();
				togglePaymentBtn(payment_code);
			}
			else {
				load_out();
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		},
		error:function(xhr) {
			load_out();
			swal({
				title:'Error!',
				text:xhr.responseText,
				type:'error'
			});
		}
	});
}


function togglePaymentBtn(payment_code)
{
	$('.payment-btn').removeClass('btn-success');
	$('#btn-'+payment_code).addClass('btn-success');
}


$('.pos-rows').click(function() {
	$('.pos-rows').removeClass('active-row');
	$(this).addClass('active-row');
})


$('.line-price').keydown(function(event){
	var e = event || window.event,
	key = e.keyCode || e.which,
	ruleSetArr_1 = [8,9,13,46], // backspace,tab, enter, delete
	ruleSetArr_2 = [48,49,50,51,52,53,54,55,56,57],	// top keyboard num keys
	ruleSetArr_3 = [96,97,98,99,100,101,102,103,104,105], // side keyboard num keys
	ruleSetArr_4 = [110,189,190], //add this to ruleSetArr to allow float values
	ruleSetArr = ruleSetArr_1.concat(ruleSetArr_2,ruleSetArr_3,ruleSetArr_4);	// merge arrays of keys

	if(ruleSetArr.indexOf() !== "undefined"){	// check if browser supports indexOf() : IE8 and earlier
		var retRes = ruleSetArr.indexOf(key);
	} else {
		var retRes = $.inArray(key,ruleSetArr);
	};
	if(retRes == -1){	// if returned key not found in array, return false
		return false;
	} else if(key == 67 || key == 86){	// account for paste events
		event.stopPropagation();
	};

});


function updateItem(id) {
	let qty = parseDefault(parseFloat(removeCommas($('#qty-'+id).val())), 1.00);
	let price = parseDefault(parseFloat(removeCommas($('#price-'+id).val())), 0.00);
	let disc = $('#disc-'+id).val();
	let currentQty = parseDefault(parseFloat($('#currentQty-'+id).val()), 0);
	let currentPrice = parseDefault(parseFloat($('#currentPrice-'+id).val()), 0);
	let currentDisc = $('#currentDisc-'+id).val();

	disc = disc == '' ? 0 : disc;
	currentDisc = currentDisc == '' ? 0 : currentDisc;

	let disc_price = parseDiscountAmount(disc, price);

	if(price < disc_price) {
		beep();
		swal({
			title:'Error!',
			text:"ส่วนลดเกินราคาขาย",
			type:'error'
		});

		//--- roll back data
		var c_price = addCommas($('#currentPrice-'+id).val());
		var c_disc = $('#currentDisc-'+id).val();
		var c_qty = $('#currentQty-'+id).val();

		$('#price-'+id).val(c_price);
		$('#disc-'+id).val(c_disc);
		$('#qty-'+id).val(c_qty);

		recalItem(id);

		return false;
	}

	$('#price-'+id).val(addCommas(price.toFixed(2)));
	$('#qty-'+id).val(addCommas(qty.toFixed(2)));

	if((price != currentPrice) || (disc != currentDisc) || qty != currentQty) {

		if(price < 0) {
			price = price * (-1);
			$('#price-'+id).val(addCommas(price.toFixed(2)));
			recalItem(id);
		}

		$.ajax({
			url:HOME + 'update_item',
			type:'POST',
			cache:false,
			data:{
				'id' : id,
				'price' : price,
				'qty' : qty,
				'discount_label' : disc,
				'is_edit' : 1
			},
			success:function(rs) {
				if(rs == 'success') {
					//--- update current
					$('#currentPrice-'+id).val(addCommas(price));
					$('#currentDisc-'+id).val(disc);
					$('#currentQty-'+id).val(qty);
					$('#isEdit-'+id).val(1);

					recalItem(id);
				}
				else {
					setTimeout(() => {
						swal({
							title:'Error!',
							text:rs,
							type:'error'
						});

						//--- roll back data
						var c_price = addCommas($('#currentPrice-'+id).val());
						var c_disc = $('#currentDisc-'+id).val();
						var c_qty = $('#currentQty-'+id).val();

						$('#price-'+id).val(c_price);
						$('#disc-'+id).val(c_disc);
						$('#qty-'+id).val(c_qty);

						recalItem(id);
					}, 100);
				}
			}
		})
	}
}


function updateBillDisc() {
	let temp_id = $('#order-temp-id').val();
	let discPercent = $('#discPrcnt').val();
	let discAmount = $('#bill-disc-amount').val();

	$.ajax({
		url:HOME + 'update_temp_bill_disc',
		type:'POST',
		cache:false,
		data:{
			'temp_id' : temp_id,
			'discount_percent' : discPercent,
			'discount_amount' : discAmount
		},
		success:function(rs) {
			console.log(rs);
		}
	})
}



$('#discPrcnt').change(function() {
	$(this).removeClass('has-error');
	var total = parseDefault(parseFloat($('#total-amount').val()), 0);
	var disc = $(this).val();

	if(disc < 0) {
		$(this).val('0.00');
		disc = 0;
	}

	if(disc > 100) {
		$(this0.val('100'))
		disc = 100;
	}

	let discAmount = (total * (disc * 0.01));
	$('#bill-disc-amount').val(discAmount);
	$('#bill-disc-label').val(addCommas(discAmount.toFixed(2)));

	recalTotal();

	updateBillDisc();
});


$('#discPrcnt').focus(function() {
  $(this).select();
});


$('#bill-disc-label').focus(function() {
	$(this).select();
});

$('#whtPrcnt').focus(function() {
	$(this).select();
});


function reCalDiscAmount() {
	$('#bill-disc-label').removeClass('has-error')

	let amount = parseDefault(parseFloat(removeCommas($('#total-amount-label').val())), 0.00)
  let discAmount = parseDefault(parseFloat(removeCommas($('#bill-disc-label').val())), 0.00)

  if(discAmount > amount) {
    $('#bill-disc-label').addClass('has-error')
    return false
  }

	discPrcnt = 0.00

  $('#discPrcnt').val(discPrcnt)
  $('#bill-disc-amount').val(discAmount.toFixed(2))
  $('#bill-disc-label').val(addCommas(discAmount.toFixed(2)))

  recalTotal()

	updateBillDisc()
}


function recalItem(id) {
	var price = parseDefault(parseFloat(removeCommas($('#price-'+id).val())), 0);
	var qty = parseDefault(parseFloat(removeCommas($('#qty-'+id).val())), 1.00);
	var disc = parseDiscountAmount($('#disc-'+id).val(), price);
	var sell_price = price - disc;
	var tax_rate = parseDefault(parseFloat($('#taxRate-'+id).val()), 0.00) * 0.01;
	var total = qty * sell_price;
	var tax_amount = total * tax_rate;
	var discount_amount = qty * disc;


	$('#total-'+id).val(addCommas(total.toFixed(2)));
	$('#taxAmount-'+id).val(tax_amount);
	$('#sellPrice-'+id).val(sell_price);
	$('#discAmount-'+id).val(discount_amount);

	recalTotal();
}


function recalTotal() {
	let totalQty = 0;
	let totalBfDisc = 0.00; //--- มูลค่ารวมสินค้าหลังส่วนลดรายการ ก่อนส่วนลดท้ายบิล
	let billDiscAmount = roundNumber(parseDefault(parseFloat($('#bill-disc-amount').val()), 0.00), 2); //--- มูลค่าส่วนลดท้ายบิล
	let billDiscPrcnt = roundNumber(parseDefault(parseFloat($('#discPrcnt').val()), 0), 2);
	let totalTaxAmount = 0.00; //-- มูลค่าภาษีรวมหลังส่วนลดท้ายบิล
	let whtPrcnt = roundNumber(parseDefault(parseFloat($('#whtPrcnt').val()), 0.00), 2); //--- หัก ณ ที่จ่าย
	let downPayment = parseDefault(parseFloat($('#down-payment').val()), 0.00);
	let vatType = $('#vat-type').val() == 'E' ? 'E' : 'I';

	$('.line-qty').each(function() {
		let no = $(this).data('id');
		let qty = roundNumber(parseDefault(parseFloat(removeCommas($('#qty-'+no).val())), 0.00), 2);
		let price = roundNumber(parseDefault(parseFloat(removeCommas($('#price-'+no).val())), 0.00), 2); //--- ราคาขายก่อนส่วนลดรายการ
		let amount = roundNumber(parseDefault(parseFloat(removeCommas($('#total-'+no).val())), 0.00), 2); //--- มูลค่ารวมหลังส่วนลดรายการของแต่ละ item (qty * (price - discount))
		let avgBillDiscAmount = parseDefault(parseFloat($('#avgBillDiscAmount-'+no).val()), 0);

		if(qty > 0 && price > 0)
		{
			totalBfDisc += amount; //-- รวมยอดสินค้า
			totalQty += qty;

			let sumBillDiscAmount = amount * avgBillDiscAmount;
			billDiscAmount += sumBillDiscAmount;
		}
	});

	billDiscAmount = billDiscPrcnt > 0 ? roundNumber(totalBfDisc * (billDiscPrcnt * 0.01), 2) : roundNumber(billDiscAmount, 2);

	amountAfterDisc = parseDefault(parseFloat(totalBfDisc - billDiscAmount), 0.00); //--- มูลค่าสินค้า หลังหักส่วนลดท้ายบิล

	//---- เฉลี่ยส่วนลดท้ายบิล
	//--- เฉลี่ยส่วนลดออกให้ทุกรายการ โดยเอาส่วนลดท้ายบิล(จำนวนเงิน)/มูลค่าสินค้าก่อนส่วนลด
	//--- ได้มูลค่าส่วนลดท้ายบิลที่เฉลี่ยนแล้ว ต่อ บาท เช่น หารกันมาแล้ว ได้ 0.16 หมายถึงทุกๆ 1 บาท จะลดราคา 0.16 บาท
	everageBillDisc = parseDefault(parseFloat((totalBfDisc > 0 ? billDiscAmount/totalBfDisc : 0)), 0);

	//--- คำนวนภาษี
	//--- นำผลลัพธ์ข้างบนมาคูณ กับ มูลค่าที่ต้องคิดภาษี (ตัวที่ไม่มีภาษีไม่เอามาคำนวณ)
	//--- จะได้มูลค่าส่วนลดที่ต้องไปลบออกจากมูลค่าสินค้าที่ต้องคิดภาษ
	$('.line-qty').each(function(){
		let no = $(this).data('id');
		let qty = roundNumber(parseDefault(parseFloat(removeCommas($('#qty-'+no).val())), 0.00), 2);
		let price = roundNumber(parseDefault(parseFloat(removeCommas($('#price-'+no).val())), 0.00), 2); //--- ราคาขายก่อนส่วนลดรายการ
		let amount = roundNumber(parseDefault(parseFloat(removeCommas($('#total-'+no).val())), 0.00), 2); //--- มูลค่ารวมหลังส่วนลดรายการของแต่ละ item (qty * (price - discount))
		let rate = parseDefault(parseFloat($('#taxRate-'+no).val()), 0.00);

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


	$('#total-item').val(totalQty);
	$('#total-amount').val(totalBfDisc);
	$('#total-amount-label').val(addCommas(totalBfDisc.toFixed(2)));
	$('#bill-disc-amount').val(billDiscAmount);
	$('#bill-disc-label').val(addCommas(billDiscAmount));
	$('#whtPrcnt').val(whtPrcnt.toFixed(2));
	$('#whtAmount').val(addCommas(whtAmount.toFixed(2)));
	$('#vat-total').val(totalTaxAmount);
	$('#vat-total-label').val(addCommas(totalTaxAmount.toFixed(2)));
	$('#amountAfterDiscAndTax').val(amountAfterDiscAndTax);
	$('#amountAfterDiscAndTax-label').val(addCommas(amountAfterDiscAndTax.toFixed(2)));
	$('#doc-total').val(docTotal);
	$('#doc-total-label').val(addCommas(docTotal.toFixed(2)));
	$('#net-amount').val(addCommas(docTotal.toFixed(2)));

	updateBillDisc();
}


function showPayment() {
	let today = $('#to-day').val();
	let role = $('#payment-role').val();
	let soCode = $('#so-code').val();
	let payAmount = parseDefault(parseFloat(removeCommas($('#net-amount').val())), 0);
	let amountAfterDiscAndTax = roundNumber(parseDefault(parseFloat($('#amountAfterDiscAndTax').val()), 0), 2);
	let whtAmount = parseDefault(parseFloat(removeCommas($('#whtAmount').val())), 0);
	let totalDownAmount = 0;
	let downErr = 0;

	$('#receive-date').val(today);

	$('.down-amount').each(function() {
		let amount = roundNumber(parseDefault(parseFloat($(this).val()), 0), 2);
		let id = $(this).data('id');
		let available = roundNumber(parseDefault(parseFloat($(this).data('available')), 0), 2);

		if(amount > 0 && id != "") {
			if(amount <= available) {
				totalDownAmount += amount;
			}
			else {
				downErr++;
			}
		}
	});

	totalDownAmount = roundNumber(totalDownAmount, 2);

	if(amountAfterDiscAndTax < totalDownAmount) {
		downErr++;
	}

	if(downErr > 0) {
		swal({
			title:'มัดจำไม่ถูกต้อง',
			text:'ยอดตัดเงินมัดจำไม่ถูกต้อง กรุณาแก้ไข',
			type:'warning'
		}, function() {
			setTimeout(() => {
				showDownPayment();
			}, 100);
		});

		return false;
	}

	$('.p').val('');
	$('.c').addClass('hide');

	if(role == 1) {
		$('#p-cash').removeClass('hide');
		$('#changeAmount').val('');
		$('#paymentModal').on('shown.bs.modal', function() {
			$('#cashReceive').focus();
		})
	}

	if(role == 2) {
		$('#p-transfer').removeClass('hide');
		$('#p-account').removeClass('hide');
		$('#transferAmount').val(payAmount);
		$('#changeAmount').val('');
		$('#paymentModal').on('shown.bs.modal', function() {
			$('#transferAmount').focus();
		})
	}

	if(role == 3) {
		$('#p-card').removeClass('hide');
		$('#cardAmount').val(payAmount);
		$('#changeAmount').val('');
		$('#paymentModal').on('shown.bs.modal', function() {
			$('#cardAmount').focus();
		})
	}

	if(role == 7) {
		$('#p-cheque').removeClass('hide');
		$('#chequeAmount').val(payAmount);
		$('#changeAmount').val('');
		$('#paymentModal').on('shown.bs.modal', function() {
			$('#chequeAmount').focus();
		})
	}

	if(role == 6) {
		$('.c').removeClass('hide');
		$('#paymentModal').on('shown.bs.modal', function() {
			$('#cashReceive').focus();
		})
	}

	calChange();

	$('#paymentModal').modal('show');
}


$('.p').keyup(function(e) {
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


$('.p').focus(function() {
	let tmp = $(this).val();
	$(this).val(tmp);
})


function focusTransfer() {
	$('#transferAmount').focus();
}


function calChange() {
	let amount = parseDefault(parseFloat($('#doc-total').val()), 0.00);
	let cashReceive = parseDefault(parseFloat(removeCommas($('#cashReceive').val())), 0.00);
	let transferAmount = parseDefault(parseFloat(removeCommas($('#transferAmount').val())), 0.00);
	let cardAmount = parseDefault(parseFloat(removeCommas($('#cardAmount').val())), 0.00);
	let chequeAmount = parseDefault(parseFloat(removeCommas($('#chequeAmount').val())), 0.00);
	let receive = cashReceive + transferAmount + cardAmount + chequeAmount;
	let balance = amount;
	let change = 0;

	balance = balance - receive;

	if( receive > amount)
	{
		change = receive - amount;
		change = change <= cashReceive ? change : cashReceive;
	}

	change = change > 0 ? change : 0;
	balance = balance > 0 ? balance : 0;

	$('#changeAmount').val(addCommas(change.toFixed(2)));
	$('#balanceAmount').val(addCommas(balance.toFixed(2)));
}


function submitPayment() {
	$('#payment-error').val('');
	let paymentDate = $('#receive-date').val();
	let pos_id = $('#pos_id').val();
	let temp_id = $('#order-temp-id').val();
	let role = $('#payment-role').val();
	let acc_id = $('#acc-id').val();
	let amountBfDisc = parseDefault(parseFloat($('#total-amount').val()), 0.00);
	let discPrcnt = parseDefault(parseFloat($('#discPrcnt').val()), 0.00);
	let discAmount = parseDefault(parseFloat($('#bill-disc-amount').val()), 0.00);
	let vatSum = parseDefault(parseFloat($('#vat-total').val()), 0.00);
	let downPayment = parseDefault(parseFloat($('#down-payment').val()), 0.00);
	let whtPrcnt = parseDefault(parseFloat($('#whtPrcnt').val()), 0.00);
	let whtAmount = parseDefault(parseFloat(removeCommas($('#whtAmount').val())), 0.00); //--- หัก ณ ที่จ่าย
	let amount = parseDefault(parseFloat($('#amountAfterDiscAndTax').val()), 0.00); //--- ยอดขายสำหรับบันทึกขาย
	let payAmount = roundNumber(parseDefault(parseFloat($('#doc-total').val()), 0.00), 2); //--- ยอดเงินที่ลูกค้าต้องชำระ (ยอดขาย - มัดจำ - หัก ณ)

	let cashReceive = parseDefault(parseFloat(removeCommas($('#cashReceive').val())), 0.00);
	let transferAmount = parseDefault(parseFloat(removeCommas($('#transferAmount').val())), 0.00);
	let cardAmount = parseDefault(parseFloat(removeCommas($('#cardAmount').val())), 0.00);
	let chequeAmount = parseDefault(parseFloat(removeCommas($('#chequeAmount').val())), 0.00);
	let receive = cashReceive + transferAmount + cardAmount + chequeAmount; //--- ยอดรับทั้งหมด
	let nonCash =  transferAmount + cardAmount + chequeAmount;
	let cashAmount = role == 1 ? payAmount : 0;

	let down = [];

	$('.down-amount').each(function() {
		let downAmount = roundNumber(parseDefault(parseFloat($(this).val()), 0), 2);
		let id = $(this).data('id');

		if(downAmount > 0 && id != "") {
			let dp = {
				'id' : id,
				'amount' : downAmount
			}
			down.push(dp);
		}
	});


	if(role == 6) {
		if(transferAmount > 0 && acc_id == "") {
			$('#payment-error').val('กรุณาเลือกบัญชีธนาคาร');
			return false;
		}

		if(receive > payAmount) {
			if(nonCash > payAmount)
			{
				if(cashReceive > 0) {
					$('#payment-error').val('ยอดเงินสดเกินบิล');
					return false;
				}
				else {
					payAmount = nonCash;
					cashAmount = 0;
				}
			}
			else {
				cash = payAmount - nonCash;
				cashAmount = cash > 0 ? cash : 0;
			}
		}
		else {
			cashAmount = cashReceive;
		}
	}
	else if(role == 2) {
		if(acc_id == "") {
			$('#payment-error').val('กรุณาเลือกบัญชีธนาคาร');
			return false;
		}
	}

	let change = receive - payAmount;
	let vat_type = $('#vat-type').val()

	if(payAmount > receive) {
		$('#payment-error').val('ยอดเงินไม่ครบ');
		return false;
	}

	if($('.sell-item').length == 0) {
		$('#payment-error').val('ไม่พบรายการสินค้า');
		return  false;
	}

	if( ! isDate(paymentDate)) {
		$('#payment-error').val('วันที่ไม่ถูกต้อง');
		return false;
	}

	$('#paymentModal').modal('hide');

	load_in();

	$.ajax({
		url:HOME + 'save_order',
		type:'POST',
		data: {
			'pos_id' : pos_id,
			'temp_id' : temp_id,
			'acc_id' : acc_id,
			'amountBfDisc' : amountBfDisc,
			'discPrcnt' : discPrcnt,
			'discAmount' : discAmount,
			'whtPrcnt' : whtPrcnt,
			'whtAmount' : whtAmount,
			'vatSum' : vatSum,
			'amountAfterDiscAndTax' : amount,
			'downPaymentAmount' : downPayment,
			'downPaymentUse' : JSON.stringify(down),
			'amount' : amount,
			'paymentRole' : role,
			'payAmount' : payAmount,
			'cashReceive' : cashReceive,
			'cashAmount' : cashAmount,
			'transferAmount' : transferAmount,
			'cardAmount' : cardAmount,
			'chequeAmount' : chequeAmount,
			'receive' : receive,
			'change' : change.toFixed(2),
			'paymentDate' : paymentDate
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					waitForPrint();
					printBill(ds.order_code);
				}
				else {
					setTimeout(() => {
						swal({
							title:'Error!',
							text:ds.message,
							type:'error'
						});
					},200);
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
		error:function(xhr, status, error) {
			load_out();
			swal({
				title:'Error!',
				text: xhr.responseText,
				type:'error',
				html:true
			})
		}
	})
}

var waiter; //-- for setInterval

function waitForPrint() {
	localStorage.setItem('printState', 0);

	waiter = setInterval(function() {
		let printState = localStorage.getItem('printState');

		if(printState == 1) {
			setTimeout(() => {
				newBill();
			}, 500);
		}
	}, 1000);
}


$('#cashInModal').on('shown.bs.modal', function() {
	$('#cash-in-amount').focus();
});


function cashIn() {
	$('#cash-in-amount').val('');
	$('#cashInModal').modal('show');
}


$('#cash-in-amount').keyup(function(e) {
	if(e.keyCode == 13) {
		doCashIn();
	}
})


function doCashIn() {
	let pos_id = $('#pos_id').val();
	let amount = parseDefault(parseFloat($('#cash-in-amount').val()), 0);

	if(amount <= 0) {
		$('#cash-in-error').text("จำนวนเงินไม่ถูกต้อง");
		$('#cash-in-amount').addClass('has-error');
		return false;
	}
	else {
		$('#cash-in-error').text('');
		$('#cash-in-amount').removeClass('has-error');
	}

	$('#cashInModal').modal('hide');

	load_in();

	$.ajax({
		url: HOME + 'save_cash_in',
		type:'POST',
		cache:false,
		data: {
			'pos_id' : pos_id,
			'amount' : amount
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {

					printCashIn(ds.movement_id);
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
				type:error,
				html:true
			});
		}
	});
}


function printCashIn(movement_id) {
	let width = 400;
	let height = 500;
	let center = (window.innerWidth - width)/2;
	let middle = (window.innerHeight - height)/2;
	let prop = "width="+width+", height="+height+", left="+center+", top="+middle+", scrollbars=yes";
	let target = HOME + 'print_cash_in/' + movement_id;
	window.open(target, '_blank', prop);
}


$('#cashOutModal').on('shown.bs.modal', function() {
	$('#cash-out-amount').focus();
});


function cashOut() {
	$('#cash-out-amount').val('');
	$('#cashOutModal').modal('show');
}


$('#cash-out-amount').keyup(function(e) {
	if(e.keyCode == 13) {
		doCashOut();
	}
})


function doCashOut() {
	let pos_id = $('#pos_id').val();
	let amount = parseDefault(parseFloat($('#cash-out-amount').val()), 0);

	if(amount <= 0) {
		$('#cash-out-error').text("จำนวนเงินไม่ถูกต้อง");
		$('#cash-out-amount').addClass('has-error');
		return false;
	}
	else {
		$('#cash-out-error').text('');
		$('#cash-out-amount').removeClass('has-error');
	}

	$('#cashOutModal').modal('hide');

	load_in();

	$.ajax({
		url: HOME + 'save_cash_out',
		type:'POST',
		cache:false,
		data: {
			'pos_id' : pos_id,
			'amount' : amount
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {

					printCashOut(ds.movement_id);
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
				type:error,
				html:true
			});
		}
	});
}


function printCashOut(movement_id) {
	let width = 400;
	let height = 500;
	let center = (window.innerWidth - width)/2;
	let middle = (window.innerHeight - height)/2;
	let prop = "width="+width+", height="+height+", left="+center+", top="+middle+", scrollbars=yes";
	let target = HOME + 'print_cash_out/' + movement_id;
	window.open(target, '_blank', prop);
}

function openDrawer() {
	let pos_id = $('#pos_id').val();
	let width = 400;
	let height = 500;
	let center = (window.innerWidth - width)/2;
	let middle = (window.innerHeight - height)/2;
	let prop = "width="+width+", height="+height+", left="+center+", top="+middle+", scrollbars=yes";
	let target = HOME + 'open_drawer/' + pos_id;
	window.open(target, '_blank', prop);
}


function findItem() {
	let width = 1300;
	let height = 800;
	let center = (window.innerWidth - width)/2;
	let middle = 100;
	let prop = "width="+width+", height="+height+", left="+center+", top="+middle+", scrollbars=yes";
	let target = BASE_URL + 'find_stock?nomenu';
	window.open(target, '_blank', prop);
}


function openRound() {
	let pos_id = $('#pos_id').val();

	$('#open-round-error').val('');

	let amount = parseDefault(parseFloat($('#open-amount').val()), 0.00);

	if(amount < 0) {
		$('#open-round-error').val('ยอดเงินในลิ้นชักไม่ถูกต้อง');
		return false;
	}

	$('#openRoundModal').modal('hide');

	if(amount == 0) {
		setTimeout(() => {
			swal({
				title:'ยืนยันยอดเงิน',
				text:'ยอดเงินที่คุณระบุ = 0.00 ยืนยันยอดเงินนี้หรือไม่ ?',
				type:'warning',
				showCancelButton:true,
				confirmButtonText:'Yes',
				cancelButtonText:'No',
				closeOnConfirm:true
			}, function() {
				doOpenRound(pos_id, amount);
			})
		})
	}
	else {
		doOpenRound(pos_id, amount);
	}
}


function doOpenRound(pos_id, amount) {
	load_in();

	$.ajax({
		url:HOME + 'open_pos_round',
		type:'POST',
		cache:false,
		data:{
			'pos_id' : pos_id,
			'amount' : amount
		},
		success:function(rs) {
			load_out();

			if(rs === 'success') {
				window.location.reload();
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


function closeRound() {
	$('#close-amount').val('');
	$('#close-round-error').val('');

	$('#closeRoundModal').on('shown.bs.modal', () => {
		$('#close-amount').focus();
	});

	$('#close-amount').keyup(function(e) {
		if(e.keyCode == 13) {
			preCloseRound();
		}
	});

	$('#closeRoundModal').modal('show');
}


function preCloseRound() {
	let pos_id = $('#pos_id').val();
	let amount = parseDefault(parseFloat($('#close-amount').val()), 0.00);
	$('#close-round-error').val('');

	if(amount < 0) {
		$('#close-round-error').val('ยอดเงินไม่ถูกต้อง');
		return false;
	}

	$('#closeRoundModal').modal('hide');

	if(amount == 0) {
		setTimeout(() => {
			swal({
				title:'ยืนยันยอดเงิน',
				text:'ยอดเงินที่คุณระบุ = 0.00 ยืนยันยอดเงินนี้หรือไม่ ?',
				type:'warning',
				showCancelButton:true,
				confirmButtonText:'Yes',
				cancelButtonText:'No',
				closeOnConfirm:true
			}, function() {
				doCloseRound(pos_id, amount);
			})
		})
	}
	else {
		doCloseRound(pos_id, amount);
	}
}


function doCloseRound(pos_id, amount) {
	load_in();

	$.ajax({
		url:HOME + 'close_pos_round',
		type:'POST',
		cache:false,
		data:{
			'pos_id' : pos_id,
			'amount' : amount
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					$('#round-title').text(ds.code);
					$('#open-time').text(ds.open_date);
					$('#close-time').text(ds.close_date);
					$('#total-round').text(ds.total_round);
					$('#round-id').val(ds.round_id);

					$('#roundModal').modal('show');
				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
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
				});
			}
		}
	})
}

function closeAndGo() {
	$('#roundModal').modal('hide');

	setTimeout(() => {
		window.location.reload();
	}, 500);
}


function showDownPayment() {
	$('.down-amount').focus(function() {
		$(this).select();
	});

	$('#downPaymentModal').modal('show');
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

	let amountAfterDiscAndTax = parseDefault(parseFloat($('#amountAfterDiscAndTax').val()), 0);
	let whtAmount = parseDefault(parseFloat(removeCommas($('#whtAmount').val())), 0);
	let balance = amountAfterDiscAndTax - total - whtAmount;

	$('#down-payment').val(roundNumber(total, 2))
	$('#down-payment-label').val(addCommas(total.toFixed(2)));
	$('#doc-total').val(roundNumber(balance, 2));
	$('#doc-total-label').val(addCommas(balance.toFixed(2)));
	$('#net-amount').val(addCommas(balance.toFixed(2)));
}

function printPosRound() {
	let id = $('#round-id').val();
  let width = 400;
	let height = 600;
	let center = (window.innerWidth - width)/2;
	let middle = (window.innerHeight - height)/2;
	let prop = "width="+width+", height="+height+", left="+center+", top="+middle+", scrollbars=yes";
	let target = BASE_URL + 'orders/order_pos_round/print_pos_round/'+id;
	window.open(target, '_blank', prop);
}
