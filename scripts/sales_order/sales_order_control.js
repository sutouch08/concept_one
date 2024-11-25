window.addEventListener('load', () => {
	shortNumInit();
	setColorbox();
	item_init();
})

$("#model-code").autocomplete({
	source: BASE_URL + 'auto_complete/get_style_code',
	autoFocus: true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    $(this).val(arr[0]);
  }
});


$('#model-code').keyup(function(event) {
	if(event.keyCode == 13){
		var code = $(this).val();
		if(code.length > 0){
			setTimeout(function(){
				getItemGrid();
			}, 300);
		}
	}
});


function item_init() {
	let vatType = $('#vat-type').val();

	if(vatType == "") {
		$('#item-code').val('กรุณาเลือกชนิด VAT').attr('disabled', 'disabled');
	}
	else {
		$('#item-code').removeAttr('disabled', 'disabled');
		$('#item-code').val('').focus();

		$('#item-code').autocomplete({
			source:BASE_URL + 'auto_complete/get_product_code_and_name',
			autoFocus:true,
		  close:function() {
		    var rs = $(this).val();
		    var arr = rs.split(' | ');
		    $(this).val(arr[0]);
		  }
		});
	}
}



$('#item-code').keyup(function(e){
	if(e.keyCode == 13){
		var code = $(this).val();
		if(code.length > 0){
			setTimeout(function(){
				getItem();
			}, 200);
		}
	}
});

$('#input-price').keyup(function(e) {
	if(e.keyCode === 13) {
		$('#input-qty').focus();
	}
});

$('#input-qty').keyup(function(e) {
	if(e.keyCode === 13) {
		let qty = parseDefault(parseFloat($(this).val()), 0);

		if(qty > 0) {
			addItem();
		}
		else {
			$(this).addClass('has-error');
		}
	}
})


function getItem() {
	let code = $('#item-code').val();

	if(code.length > 0) {
		$.ajax({
			url:HOME + 'get_item',
			type:'POST',
			cache:false,
			data:{
				'item_code' : code
			},
			success:function(rs) {
				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status === 'success') {
						$('#item-data').val(JSON.stringify(ds.item));
						let price = roundNumber(ds.item.price, 2);
            let stock = roundNumber(ds.item.stock, 2);
						$('#input-price').val(price);
						$('#input-stock').val(stock);
            $('#input-qty').focus();
					}
					else {
						swal({
							title:'Error!',
							text:ds.message,
							type:'error'
						}, function() {
							setTimeout(() => {
								$('#item-code').focus();
							}, 200);
						});

						$('#item-data').val('');
					}
				}
				else
				{
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					}, function() {
						setTimeout(() => {
							$('#item-code').focus();
						}, 200);
					});

					$('#item-data').val('');
				}
			}
		})
	}
}


function getItemGrid(){
	let styleCode = $('#model-code').val();
	let whsCode = $('#warehouse').val();

  if(styleCode.length) {
    load_in();

    $.ajax({
      url: HOME + 'get_item_grid',
      type:"POST",
      cache:"false",
      data:{
        "style_code" : styleCode,
				"warehouse_code" : whsCode
      },
      success: function(rs){
        load_out();

        if(isJson(rs)) {
          let ds = $.parseJSON(rs);
          $('#modal').css('width', ds.tableWidth + 'px');
          $('#modalTitle').html(ds.styleCode);
          $('#modalBody').html(ds.table);
          $('#itemGrid').modal('show');

					shortKeyInit();
        }
        else {
          swal(rs);
        }
      }
    });
  }
}

async function addItem() {
	const json = $('#item-data').val();
	const item = await json.length ? JSON.parse(json) : null;
	const code = $('#item-code').val();
	const price = parseDefault(parseFloat($('#input-price').val()), 0);
	const qty = parseDefault(parseFloat($('#input-qty').val()), 0);
  const vatType = $('#vat-type').val();
	let no = parseDefault(parseInt($('#no').val()), 0);

	if(qty > 0) {
		$('#input-qty').removeClass('has-error');

		if(item !== null && item !== undefined) {
			no++;
			let amount = qty * price;

			let items = {
				'no' : no,
				'product_code' : item.code,
				'product_name' : item.name,
				'style_code' : item.style_code,
				'unit_code' : item.unit_code,
				'cost' : item.cost,
				'vatCode' : item.sale_vat_code,
				'vatRate' : parseDefault(parseFloat(item.sale_vat_rate), 0),
				'price' : price,
				'priceLabel' : addCommas(price.toFixed(2)),
				'qty' : qty,
				'qtyLabel' : addCommas(qty.toFixed(2)),
				'amount' : amount,
				'amountLabel' : addCommas(amount.toFixed(2)),
				'count_stock' : item.count_stock
			};

			let source = $('#row-template').html();
			let output = $('#detail-list');

			render_append(source, items, output);

			//--- update last no for next gennerate
			$('#no').val(no);
			//--- Calculate Summary
			recalTotal();

			//---- update running no
			reIndex();

			//---- initial keyboard key to focus next and prev input by enter and arrow key
			shortNumInit();

			$('#item-code').val('');
			$('#item-data').val('');
			$('#input-price').val('');
      $('#input-stock').val('');
			$('#input-qty').val('');

			setTimeout(() => {
				$('#item-code').focus();
			}, 200);
		}
		else {
			swal({
				title:'Error!',
				text:'ไม่พบข้อมูลสินค้า',
				type:'error'
			});
		}
	}
	else {
		$('#input-qty').addClass('has-error');
		return false;
	}
}


//---- เพิ่มรายการจาก item grid
function addItems() {
	let no = parseDefault(parseInt($('#no').val()), 0);
	let items = [];

	$('#itemGrid').modal('hide');

	load_in();

	$('.item-grid').each(function() {

		let el = $(this);

		if(el.val() != "") {
			let qty = parseDefault(parseFloat(el.val()), 0);

			if(qty > 0) {
				no++;
				let itemCode = el.data('code'); //--- product code;
				let itemName = el.data('name');
				let style = el.data('style');
				let unitCode = el.data('uom');
				let price = parseDefault(parseFloat(el.data('price')), 0.00);
				let cost = parseDefault(parseFloat(el.data('cost')), 0.00);
				let amount = qty * price;
				let vatCode = el.data('vatcode');
				let vatRate = parseDefault(parseFloat(el.data('vatrate')), 0);

				let item = {
					'no' : no,
					'product_code' : itemCode,
					'product_name' : itemName,
					'style_code' : style,
					'unit_code' : unitCode,
					'cost' : cost,
					'vatCode' : vatCode,
					'vatRate' : vatRate,
					'price' : price,
					'priceLabel' : addCommas(price.toFixed(2)),
					'qty' : qty,
					'qtyLabel' : addCommas(qty.toFixed(2)),
					'amount' : amount,
					'amountLabel' : addCommas(amount.toFixed(2)),
					'count_stock' : el.data('count')
				}

				items.push(item);
			}
		}
	})

	if(items.length > 0) {

		let source = $('#details-template').html();
		let output = $('#detail-list');
		render_append(source, items, output);

		//--- update last no for next gennerate
		$('#no').val(no);
		//--- Calculate Summary
		recalTotal();

		//---- update running no
		reIndex();

		//---- initial keyboard key to focus next and prev input by enter and arrow key
		shortNumInit();

		swal({
			title:'Success',
			type:'success',
			timer:1000
		});
	}

	load_out();
}


function recalQty(no) {
	let inputQty = $('#qty-label-'+no);

	let qty = parseDefault(parseFloat(removeCommas(inputQty.val())), 0)

	//--- จำนวนก่อนแก้ไขจำนวน
	let prevQty = inputQty.data('qty');

	//--- OpenQty
	let openQty = parseDefault(parseFloat(inputQty.data('openqty')), 0);

	//--- รายการนี้ได้ link กับเอกสารอื่นแล้วหรือยัง
	let isLinked = inputQty.data('linked'); //-- Y / N

	console.log(isLinked);

	if(isLinked == 'Y' && qty < openQty) {
		swal({
			title:'Oops',
			text:`รายการนี้ถูกดึงไปใช้กับเอกสารอื่นแล้ว ไม่สามารถแก้ไขจำนวนให้น้อยกว่า ${openQty} ได้`,
			type:'warning'
		}, function() {
			inputQty.val(prevQty);
		});

		return false;
	}

	let newOpenQty = (qty - prevQty) + openQty;
	$('#open-qty-'+no).val(addCommas(newOpenQty.toFixed(2)));

	recalAmount(no);
}

function recalAmount(no) {
  $('#price-label-'+no).removeClass('has-error')
  $('#qty-label-'+no).removeClass('has-error')
  $('#disc-label-'+no).removeClass('has-error')

	let qty = parseDefault(parseFloat(removeCommas($('#qty-label-'+no).val())), 0)
	let price = parseDefault(parseFloat(removeCommas($('#price-label-'+no).val())), 0)
  let discAmount = parseDiscountAmount($('#disc-label-'+no).val(), price)
  let amount = qty * (price - discAmount)



  if(price < 0) {
    $('#price-label-'+no).addClass('has-error')
    amount = 0
  }

  if(qty < 0) {
    $('#qty-label-'+no).addClass('has-error')
    amount = 0
  }

  if(discAmount > price) {
    $('#disc-label-'+no).addClass('has-error')
    amount = 0
  }

	$('#qty-label-'+no).val(addCommas(qty.toFixed(2)))
	$('#price-label-'+no).val(addCommas(price.toFixed(2)))
	$('#total-label-'+no).val(addCommas(amount.toFixed(2)))
  $('#price-'+no).val(price)
  $('#disc-amount-'+no).val(discAmount)
  $('#line-total-'+no).val(amount)

	recalTotal();
}


function recalTotal() {
	let totalBfDisc = 0.00; //--- มูลค่ารวมสินค้าหลังส่วนลดรายการ ก่อนส่วนลดท้ายบิล
	let billDiscAmount = roundNumber(parseDefault(parseFloat($('#disc-amount').val()), 0.00), 2); //--- มูลค่าส่วนลดท้ายบิล
	let billDiscPrcnt = roundNumber(parseDefault(parseFloat($('#discPrcnt').val()), 0), 2);
	let totalTaxAmount = 0.00; //-- มูลค่าภาษีรวมหลังส่วนลดท้ายบิล
	let rounding = 0;
	let whtPrcnt = roundNumber(parseDefault(parseFloat($('#whtPrcnt').val()), 0.00), 2); //--- หัก ณ ที่จ่าย
	let vatType = $('#vat-type').val() == 'E' ? 'E' : 'I';

	$('.row-qty').each(function(){
		let no = $(this).data('no');
		let qty = roundNumber(parseDefault(parseFloat(removeCommas($('#qty-label-'+no).val())), 0.00), 2);
		let price = roundNumber(parseDefault(parseFloat(removeCommas($('#price-label-'+no).val())), 0.00), 2); //--- ราคาขายก่อนส่วนลดรายการ
		let amount = roundNumber(parseDefault(parseFloat(removeCommas($('#total-label-'+no).val())), 0.00), 2); //--- มูลค่ารวมหลังส่วนลดรายการของแต่ละ item (qty * (price - discount))

		if(qty > 0 && price > 0)
		{
			totalBfDisc += amount; //-- ่รวมยอดสินค้า
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
	$('.row-qty').each(function() {
		let no = $(this).data('no');
		let qty = roundNumber(parseDefault(parseFloat(removeCommas($('#qty-label-'+no).val())), 0.00), 2);
		let price = roundNumber(parseDefault(parseFloat(removeCommas($('#price-label-'+no).val())), 0.00), 2); //--- ราคาขายก่อนส่วนลดรายการ
		let amount = roundNumber(parseDefault(parseFloat(removeCommas($('#total-label-'+no).val())), 0.00), 2); //--- มูลค่ารวมหลังส่วนลดรายการของแต่ละ item (qty * (price - discount))
		let rate = parseDefault(parseFloat($('#qty-label-'+no).data('vatrate')), 0.00); //--- ภาษีของแต่ละ Item

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
  docTotal = vatType == 'E' ? roundNumber(amountAfterDisc + totalTaxAmount, 2) : roundNumber(amountAfterDisc, 2);

	whtAmount = vatType == 'E' ? roundNumber(amountAfterDisc * (whtPrcnt * 0.01)) : roundNumber((amountAfterDisc - totalTaxAmount) * (whtPrcnt * 0.01), 2);

	$('#total-amount').val(totalBfDisc);
	$('#total-amount-label').val(addCommas(totalBfDisc.toFixed(2)));
	$('#disc-amount').val(billDiscAmount);
	$('#disc-amount-label').val(addCommas(billDiscAmount.toFixed(2)));
	$('#whtPrcnt').val(whtPrcnt.toFixed(2));
	$('#wht-amount').val(whtAmount);
	$('#wht-amount-label').val(addCommas(whtAmount.toFixed(2)));
	$('#vat-total').val(totalTaxAmount);
	$('#vat-total-label').val(addCommas(totalTaxAmount.toFixed(2)));
	$('#doc-total').val(docTotal);
	$('#doc-total-label').val(addCommas(docTotal.toFixed(2)));
}


$('#discPrcnt').change(function() {
	var total = parseDefault(parseFloat($('#total-amount').val()), 0);
	var disc = $(this).val();

	if(disc < 0) {
		$(this).val(0);
	}
	else if(disc > 100) {
		$(this).addClass('has-error');
	}
	else {
		$(this).removeClass('has-error');
		let discAmount = (total * (disc * 0.01));
		$('#disc-amount').val(discAmount);
		$('#disc-amount-label').val(addCommas(discAmount.toFixed(2)));

		recalTotal();
	}
});

$('#discPrcnt').focus(function() {
  $(this).select()
})

$('#disc-amount-label').focus(function() {
	$(this).select();
})

$('#whtPrcnt').focus(function() {
  $(this).select()
})


function reCalDiscAmount() {
	$('#disc-amount-label').removeClass('has-error')
	let amount = parseDefault(parseFloat(removeCommas($('#total-amount-label').val())), 0.00)
  let discAmount = parseDefault(parseFloat(removeCommas($('#disc-amount-label').val())), 0.00)

  if(discAmount > amount) {
    $('#disc-amount-label').addClass('has-error')

    return false
  }

	$('#discPrcnt').val(0.00);
  $('#disc-amount').val(discAmount.toFixed(2))
  $('#disc-amount-label').val(addCommas(discAmount.toFixed(2)))

  recalTotal()
}


function shortKeyInit() {
	//--- focus to next input by arrow key
	$('.item-grid').keydown(function(e) {
		if(e.keyCode == 13 || (e.keyCode > 36 && e.keyCode < 41)) {
			e.preventDefault();
			let row = parseDefault(parseInt($(this).data('row')), 0);
			let col = parseDefault(parseInt($(this).data('col')), 0);

			//-- enter and down arrow
			if(e.keyCode == 13 || e.keyCode == 40) {
				focusNextRow(row, col)
				return
			}

			//--- up arrow
			if(e.keyCode == 38) {
				focusPrevRow(row, col)
				return
			}

			//--- left arrow
			if(e.keyCode == 37) {
				focusPrevCol(row, col)
				return
			}

			//--- right arrow
			if(e.keyCode == 39) {
				focusNextCol(row, col)
				return;
			}
		}
	});

}


function focusNextRow(row, col) {
	let nextRow = row + 1;
	let nextCol = col + 1;
	let lastRow = $('.r').length - 1;
	let el = nextRow <= lastRow ? nextRow.toString() + col.toString() : "0" + nextCol.toString();
	$('#qty-'+el).focus();
}


function focusPrevRow(row, col) {
	let prevRow = row - 1;
	let prevCol = col > 0 ? col - 1 : 0;
	let lastRow = $('.r').length - 1;
	let el = prevRow >= 0 ? prevRow.toString() + col.toString() : lastRow.toString() + prevCol.toString();
	$('#qty-'+el).focus();
}


function focusNextCol(row, col) {
	let nextCol = col + 1;
	let nextRow = row + 1;
	let lastCol = $('.c').length - 1;
	let el = nextCol <= lastCol ? row.toString() + nextCol.toString() : nextRow.toString() + "0";
	$('#qty-' + el).focus();
}


function focusPrevCol(row, col) {
	let prevCol = col - 1;
	let prevRow = row > 0 ? row  - 1 : 0;
	let lastCol = $('.c').length - 1;
	let el = prevCol >= 0 ? row.toString() + prevCol.toString() : prevRow.toString() + lastCol.toString();
	$('#qty-' + el).focus();
}


function shortNumInit() {
	$('.row-price').keyup(function(e) {
    if(e.keyCode == 13) {
      let no = parseDefault(parseInt($(this).data('no')), 0);
			$('#qty-label-'+no).focus().select()
    }

		if(e.keyCode == 40) {
			let no = parseDefault(parseInt($(this).data('no')), 0);
			focusNextPrice(no);
		}

		if(e.keyCode == 38) {
			let no = parseDefault(parseInt($(this).data('no')), 0);
			focusPrevPrice(no);
		}
	})

	$('.row-qty').keyup(function(e) {
    if(e.keyCode == 13) {
      let no = parseDefault(parseInt($(this).data('no')), 0);
      $('#disc-label-'+no).focus().select()
    }

		if(e.keyCode == 40) {
			let no = parseDefault(parseInt($(this).data('no')), 0);
			focusNextQty(no);
		}

		if(e.keyCode == 38) {
			let no = parseDefault(parseInt($(this).data('no')), 0);
			focusPrevQty(no);
		}
	})

  $('.row-disc').keyup(function(e) {
    if(e.keyCode == 13) {
      let no = parseDefault(parseInt($(this).data('no')), 0);
      focusNextPrice(no)
    }

    if(e.keyCode == 40) {
      let no = parseDefault(parseInt($(this).data('no')), 0);
			focusNextDisc(no);
    }

    if(e.keyCode == 38) {
			let no = parseDefault(parseInt($(this).data('no')), 0);
			focusPrevDisc(no);
		}
  })

	$('.row-disc').keydown(function(event){
		var e = event || window.event,
		key = e.keyCode || e.which,
		ruleSetArr_1 = [8,9,13,46], // backspace,tab, enter, delete
		ruleSetArr_2 = [48,49,50,51,52,53,54,55,56,57],	// top keyboard num keys
		ruleSetArr_3 = [96,97,98,99,100,101,102,103,104,105], // side keyboard num keys
		ruleSetArr_4 = [110,189,190], //add this to ruleSetArr to allow float values
		ruleSetArr_5 = [53, 107, 187], // add plus and %
		ruleSetArr = ruleSetArr_1.concat(ruleSetArr_2,ruleSetArr_3,ruleSetArr_4, ruleSetArr_5);	// merge arrays of keys

		if(ruleSetArr.indexOf() !== "undefined"){	// check if browser supports indexOf() : IE8 and earlier
			var retRes = ruleSetArr.indexOf(key);
		} else {
			var retRes = $.inArray(key,ruleSetArr);
		};

		if(e.keyCode == 32) {
			e.preventDefault();
			let txt = $(this).val();
			if(txt.length > 0) {
				let last = txt.slice(-1);

				if(last != '%' && last != '+' && last != '.') {
					let value = txt + '%';
					$(this).val(value);
				}
			}
		}

		if(retRes == -1){	// if returned key not found in array, return false
			return false;
		} else if(key == 67 || key == 86){	// account for paste events
			event.stopPropagation();
		};

	});


  $('.row-qty').focus(function() {
    $(this).select();
  })

  $('.row-price').focus(function() {
    $(this).select();
  })

  $('.row-disc').focus(function() {
    $(this).select();
  })
}


function focusNextPrice(no) {
	$('.row-price').each(function() {
		let ro = parseDefault(parseInt($(this).data('no')), 0);

		if(ro > no) {
			$(this).focus().select();
			return false;
		}
		else {
			recalAmount(no);
		}
	})
}

function focusPrevPrice(no) {
	$($('.row-price').get().reverse()).each(function() {
		let ro = parseDefault(parseInt($(this).data('no')), 0);
		if(ro < no) {
			$(this).focus().select();
			return false;
		}
	})
}

function focusNextQty(no) {
	$('.row-qty').each(function() {
		let ro = parseDefault(parseInt($(this).data('no')), 0);

		if(ro > no) {
			$(this).focus().select();
			return false;
		}
	})
}

function focusPrevQty(no) {
	$($('.row-qty').get().reverse()).each(function() {
		let ro = parseDefault(parseInt($(this).data('no')), 0);
		if(ro < no) {
			$(this).focus().select();
			return false;
		}
	})
}

function focusNextDisc(no) {
	$('.row-disc').each(function() {
		let ro = parseDefault(parseInt($(this).data('no')), 0);

		if(ro > no) {
			$(this).focus().select();
			return false;
		}
	})
}

function focusPrevDisc(no) {
	$($('.row-disc').get().reverse()).each(function() {
		let ro = parseDefault(parseInt($(this).data('no')), 0);
		if(ro < no) {
			$(this).focus().select();
			return false;
		}
	})
}


function toggleCheckAll(el) {
	if(el.is(':checked')) {
		$('.chk').prop('checked', true);
	}
	else {
		$('.chk').prop('checked', false);
	}
}


function removeChecked() {
	if($('.chk:checked').length) {
		swal({
			title:'คุณแน่ใจ ?',
			text:'ต้องการลบรายการที่เลือกหรือไม่ ?',
			type:'warning',
			showCancelButton:true,
			confirmButtonColor:'#d15b47',
			confirmButtonText:'Yes',
			cancelButtonText:'No',
			closeOnConfirm:true
		}, function() {
			$('.chk:checked').each(function() {
				let no = $(this).val();
				$('#row-'+no).remove();
			});

			recalTotal();
			reIndex();
		})
	}
}

function viewStock(pdCode) {
	let width = window.innerWidth * 0.9;
	let height = window.innerHeight * 0.9;
	let center = (window.innerWidth - width) / 2;
	let top = 100;
	let prop = "width="+width+", height="+height+", left="+center+", top="+top+", scrollbars=yes";
	let target = BASE_URL + 'inventory/stock/available_stock/'+pdCode+'?nomenu';

	window.open(target, '_blank', prop);
}

//---- images
function addImage() {
	$('#imageModal').modal('show');
}

$("#image").change(function(){
	if($(this).val() != '')
	{
		var file 		= this.files[0];
		var name		= file.name;
		var type 		= file.type;
		var size		= file.size;

		if(file.type != 'image/png' && file.type != 'image/jpg' && file.type != 'image/gif' && file.type != 'image/jpeg' )
		{
			swal("รูปแบบไฟล์ไม่ถูกต้อง", "กรุณาเลือกไฟล์นามสกุล jpg, jpeg, png หรือ gif เท่านั้น", "error");
			$(this).val('');
			return false;
		}

		if( size > 2000000 )
		{
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 2 MB", "error");
			$(this).val('');
			return false;
		}

		readURL(this);

		$("#btn-select-file").css("display", "none");
		$("#block-image").animate({opacity:1}, 1000);
	}
});


function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
			$('#previewImg').html('<img id="previewImg" src="'+e.target.result+'" width="200px" alt="รูปสินค้า" />');

      $('#img-blob').val(e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

function getImage() {
	var blob = $('#img-blob').val();
	$("#imageModal").modal('hide');

	if(blob.length) {
		$('#so-img-preview').html('<img class="editable image-responsive" id="so-image" src="'+blob+'" style="max-width:100%; max-height:100%; border-radius:10px;" alt="Item image" />');
		$('#btn-add-img').addClass('hide');
		$('#btn-del-img').removeClass('hide');
		$('#btn-save-img').removeClass('hide');
	}
}


function saveImage() {
	let code = $('#code').val();
	let blobImage = $('#img-blob').val();

	if( blobImage == '') {
		swal('ข้อผิดพลาด', 'ไม่สามารถอ่านข้อมูลรูปภาพที่แนบได้ กรุณาแนบไฟล์ใหม่อีกครั้ง', 'error');
		return false;
	}

	$.ajax({
		url:HOME + 'save_image',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'imageData' : blobImage
		},
		success:function(rs) {
			if(rs == 'success') {
				swal({
					title:'Success!',
					type:'success',
					timer:1000
				});

				$('#btn-save-img').addClass('hide');
				$('#btn-add-img').removeClass('hide');
				$('#btn-del-img').removeClass('hide');
			}
			else {
				swal({
					title:'Error!',
					type:'error',
					text:rs,
					html:true
				});
			}
		}
	})
}


function doUpload()
{
	var code = $('#code').val();
	var image	= $("#image")[0].files[0];

	if( image == '' ){
		swal('ข้อผิดพลาด', 'ไม่สามารถอ่านข้อมูลรูปภาพที่แนบได้ กรุณาแนบไฟล์ใหม่อีกครั้ง', 'error');
		return false;
	}


	$("#imageModal").modal('hide');

	var fd = new FormData();
	fd.append('image', $('input[type=file]')[0].files[0]);
	fd.append('code', code);

	load_in();

	$.ajax({
		url: HOME + 'change_image',
		type:"POST",
		cache: "false",
		data: fd,
		processData:false,
		contentType: false,
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success')
			{
				swal({
					title : 'Success',
					type: 'success',
					timer: 1000
				});

				setTimeout(function(){
					window.location.reload();
				}, 1200);

			}
			else
			{
				swal("ข้อผิดพลาด", rs, "error");
			}
		},
		error:function(xhr, status, error) {
			load_out();
			swal({
				title:'Error!',
				text:"Error-"+xhr.status+": "+xhr.statusText,
				type:'error'
			})
		}
	});
}

function removeFile()
{
	let img = $('#prev-image').val();
	$("#previewImg").html('');
	$('#img-blob').val('');
	$("#block-image").css("opacity","0");
	$("#btn-select-file").css('display', '');
	$("#image").val('');
}

function removeImage() {
	let img = $('#prev-image').val();
	$('#so-img-preview').html('<img class="editable img-responsive" id="so-image" src="'+img+'" style="width-100%; height:100%; max-width:160px; max-height:160px; border-radius:10px;" alt="Item image" />');
	$("#previewImg").html('');
	$('#img-blob').val('');
	$("#block-image").css("opacity","0");
	$("#btn-select-file").css('display', '');
	$("#image").val('');
	$('#btn-add-img').removeClass('hide');
	$('#btn-del-img').addClass('hide');
}

function deleteImage()
{
	var code = $('#code').val();
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบรูปภาพ หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#FA5858",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function() {
			setTimeout(() => {
				$.ajax({
					url: HOME + 'delete_image',
					type:"POST",
					cache:"false",
					data:{
						"code" : code
					},
					success: function(rs) {
						if( rs == 'success' )
						{
							swal({
								title:'Success',
								type:'success',
								timer:1000
							})

							let path = $('#no-img-path').val();
							$('#so-image').attr('src', path);
							$('#btn-save-img').addClass('hide');
							$('#btn-add-img').removeClass('hide');
							$('#btn-del-img').addClass('hide');
						}
						else
						{
							swal({
								title:'Error!',
								text:rs,
								type:'error'
							})
						}
					},
					error: function(rs) {
						swal({
							title:'Error!',
							text:"Error-" + rs.status + ": "+rs.statusText,
							type:"error"
						})
					}
				});
			}, 200)
	});
}


$('#dep-amount').change(function() {
	$(this).removeClass('has-error');

	let amount = parseDefault(parseFloat(removeCommas($(this).val())), 0);
	let docTotal = parseDefault(parseFloat($('#doc-total').val()), 0);

	$(this).val(addCommas(amount.toFixed(2)));

	if(amount > docTotal) {
		$(this).addClass('has-error');
	}
})


function openWq() {
	let wq = $('#wq').val();

	if(wq != "" && wq != null && wq.length > 9) {
		//--- properties for print
		var center    = ($(document).width() - 800)/2;
		var prop 			= "width=800, height=900, left="+center+", scrollbars=yes";
		var target = BASE_URL + 'inventory/transform/edit_order/'+wq+'?nomenu';
		window.open(target, "_blank", prop);
	}
}


function openWo() {
	let code = $('#wo').val();
	if(code != "" && code != null && code.length > 9) {
		//--- properties for print
		var center    = ($(document).width() - 800)/2;
		var prop 			= "width=800, height=900, left="+center+", scrollbars=yes";
		var target = BASE_URL + 'orders/orders/edit_order/'+code;
		window.open(target, "_blank", prop);
	}
}

function openBill() {
	let code = $('#bi').val();
	if(code != "" && code != null && code.length > 9) {
		//--- properties for print
		var center    = ($(document).width() - 800)/2;
		var prop 			= "width=800, height=900, left="+center+", scrollbars=yes";
		var target = BASE_URL + 'orders/order_pos_bill/view_detail/'+code+'?nomenu';
		window.open(target, "_blank", prop);
	}
}
