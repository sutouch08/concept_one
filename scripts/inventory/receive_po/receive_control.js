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



$('#item-code').autocomplete({
	source:BASE_URL + 'auto_complete/get_product_code',
	minLength: 2,
	autoFocus:true,
  close:function() {
    var rs = $(this).val();
    var arr = rs.split(' | ');
    $(this).val(arr[0]);
  }
});

$('#item-code').keyup(function(e){
	if(e.keyCode == 13){
		var code = $(this).val();
		if(code.length > 4){
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

	if(code.length > 4) {
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
						$('#input-price').val(price);
						$('#input-price').select();
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
	// let styleCode = 'WA-PLA024';
  if(styleCode.length) {
    load_in();

    $.ajax({
      url: HOME + 'get_item_grid',
      type:"POST",
      cache:"false",
      data:{
        "style_code" : styleCode
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
	let no = parseDefault(parseInt($('#no').val()), 0);

	if(qty > 0) {
		$('#input-qty').removeClass('has-error');

		if(item !== null && item !== undefined) {
			no++;
			let itemCode = item.code;
			let itemName = item.name;
			let unitCode = item.unit_code;
			let baseEntry = "";
			let baseLine = "";
			let amount = qty * price;
			let vatCode = item.purchase_vat_code;
			let vatRate = parseDefault(parseFloat(item.purchase_vat_rate), 0);
			let vatAmount = amount * (vatRate * 0.01);
			let limit = -1;

			let items = [{
				'no' : no,
				'product_code' : itemCode,
				'product_name' : itemName,
				'unitCode' : unitCode,
				'baseEntry' : baseEntry,
				'baseLine' : baseLine,
				'vatCode' : vatCode,
				'vatRate' : vatRate,
				'priceBefDi' : price,
				'priceBefDiLabel' : addCommas(price.toFixed(2)),
				'discPrcnt' : 0,
				'price' : price,
				'priceLabel' : addCommas(price.toFixed(2)),
				'qty' : qty,
				'backlogs' : 0,
				'qtyLabel' : addCommas(qty.toFixed(2)),
				'limit' : limit,
				'amount' : amount,
				'amountLabel' : addCommas(amount.toFixed(2))
			}];

			let source = $('#receive-template').html();
			let output = $('#receive-list');

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
	let no = parseDefault(parseInt($('#row').val()), 0);
	let items = [];

	$('#itemGrid').modal('hide');

	load_in();

	$('.item-grid').each(function() {

		let el = $(this);

		if(el.val() != "") {
			let qty = parseDefault(parseFloat(el.val()), 0);
			let limit = parseDefault(parseFloat(el.data('limit')), 0);

			if(qty > 0) {
				no++;
				let itemCode = el.data('code'); //--- product code;
				let itemName = el.data('name');
				let baseEntry = poCode.length ? el.data('docentry') : "";
				let baseLine = poCode.length ? el.data('linenum') : "";
				let price = parseDefault(parseFloat(el.data('cost')), 0.00);
				let backlogs = el.data('backlogs');
				let amount = qty * price;
				let vatCode = el.data('vatcode');
				let vatRate = parseDefault(parseFloat(el.data('vatrate')), 0);
				let vatAmount = amount * (vatRate * 0.01);

				let item = {
					'no' : no,
					'product_code' : itemCode,
					'product_name' : itemName,
					'baseEntry' : baseEntry,
					'baseLine' : baseLine,
					'vatCode' : vatCode,
					'vatRate' : vatRate,
					'price' : price,
					'priceLabel' : addCommas(price.toFixed(2)),
					'qty' : qty,
					'backlogs' : backlogs,
					'qtyLabel' : addCommas(qty.toFixed(2)),
					'limit' : limit,
					'amount' : amount,
					'amountLabel' : addCommas(amount.toFixed(2)),
					'vatAmount' : vatAmount
				}

				items.push(item);
			}
		}
	})

	if(items.length > 0) {

		let source = $('#receive-template').html();
		let output = $('#receive-list');
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


//--- เพิ่มรายการจาก PO grid
function addPoItems() {
	let items = [];

	$('#poGrid').modal('hide');

	load_in();

	$('.po-qty').each(function() {
		let el = $(this);

		if(el.val() != "") {
			let qty = parseDefault(parseFloat(removeCommas(el.val())), 0);

			if(qty > 0) {
				let no = el.data('uid');

				if($('#row-qty-'+no).length) {
					let cqty = parseDefault(parseFloat($('#row-qty-'+no).val()), 0);
					let nqty = cqty + qty;
					$('#row-qty-'+no).val(nqty);

					recalAmount(no);
				}
				else {

					let itemCode = el.data('code'); //--- product code;
					let itemName = el.data('name');
					let baseEntry = el.data('baseentry');
					let baseLine = el.data('baseline');
					let priceBefDi = parseDefault(parseFloat(el.data('pricebefdi')), 0.00);
					let discPrcnt = parseDefault(parseFloat(el.data('discprcnt')), 0.00);
					let price = parseDefault(parseFloat(el.data('price')), 0.00); //--- price Af discount
					let limit = parseDefault(parseFloat(el.data('limit')), 0.00);
					let backlogs = parseDefault(parseFloat(el.data('backlogs')), 0);
					let amount = roundNumber(qty * price, 2);
					let vatCode = el.data('vatcode');
					let vatRate = parseDefault(parseFloat(el.data('vatrate')), 7);
					let vatAmount = roundNumber(amount * (vatRate * 0.01), 2);

					let item = {
						'no' : no,
						'product_code' : itemCode,
						'product_name' : itemName,
						'baseEntry' : baseEntry,
						'baseLine' : baseLine,
						'vatCode' : vatCode,
						'vatRate' : vatRate,
						'priceBefDi' : priceBefDi,
						'priceBefDiLabel' : addCommas(priceBefDi.toFixed(3)),
						'discPrcnt' : discPrcnt,
						'price' : price,
						'priceLabel' : addCommas(price.toFixed(3)),
						'qty' : qty,
						'qtyLabel' : addCommas(qty.toFixed(2)),
						'backlogs' : backlogs,
						'limit' : limit,
						'amount' : amount,
						'amountLabel' : addCommas(amount.toFixed(2)),
						'vatAmount' : vatAmount,
						'unitMsr' : el.data('unitmsr'),
						'NumPerMsr' : el.data('numpermsr'),
						'unitMsr2' : el.data('unitmsr2'),
						'NumPerMsr2' : el.data('numpermsr2'),
						'UomEntry' : el.data('uomentry'),
						'UomEntry2' : el.data('uomentry2'),
						'UomCode' : el.data('uomcode'),
						'UomCode2' : el.data('uomcode2')
					}

					items.push(item);
				}
			}
		}
	})

	if(items.length > 0) {

		let source = $('#receive-template').html();
		let output = $('#receive-list');

		render_append(source, items, output);

		$('.item-control').attr('disabled', 'disabled');
		$('#btn-confirm-po').addClass('hide');
		$('#btn-get-po').removeClass('hide');
		$('#poCode').attr('disabled', 'disabled');

		//--- update last no for next gennerate
		$('#no').val(0);

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


//---- recal price before disc
function recalPrice(id) {
	//---- ราคาหลังส่่วนลด
	let price = parseDefault(parseFloat(removeCommas($('#row-price-'+id).val())), 0);
	//--- ส่วนลด %
	let discPrcnt = parseDefault(parseFloat($('#row-disc-'+id).val()), 0);

	if(discPrcnt > 0 && price > 0) {
		let disc = 1 - (discPrcnt * 0.01);
		let bprice = price/disc;
		$('#row-bprice-'+id).val(addCommas(bprice.toFixed(2)));
	}
	else {
		$('#row-bprice-'+id).val(addCommas(price.toFixed(2)));
	}

	recalTotal();
}


function recalDiscount(id) {
	let input = $('#row-disc-'+id);
	let discPrcnt = parseDefault(parseFloat(input.val()), 0);

	if(discPrcnt > 100 || discPrcnt < 0) {
		input.addClass('has-error');

		return false;
	}
	else {
		input.removeClass('has-error');
	}

	recalAmount(id);
}


function recalAmount(id) {
	//--- ราคาก่อนส่วนลด
	let bprice = parseDefault(parseFloat(removeCommas($('#row-bprice-'+id).val())), 0);
	//-- ส่วนลด %
	let discPrcnt = parseDefault(parseFloat($('#row-disc-'+id).val()), 0);
	//--- จำนวน
	let qty = parseDefault(parseFloat(removeCommas($('#row-qty-'+id).val())), 0);

	let vatRate = parseDefault(parseFloat($('#row-qty-'+id).data('vatrate')), 0);
	//--- ราคาหลังส่วนลด
	let price = 0;

	if(discPrcnt > 100 || discPrcnt < 0) {
		$('#row-disc-'+id).addClass('has-error');
		return false;
	}
	else {
		$('#row-disc-'+id).removeClass('has-error');
	}

	if(bprice > 0 && discPrcnt > 0) {
		disc = 1 - (discPrcnt * 0.01);
		price = bprice * disc;
	}
	else {
		price = bprice;
	}

	let amount = qty * price;
	let vatAmount = roundNumber(amount * (vatRate * 0.01), 2);

	$('#row-qty-'+id).val(addCommas(qty.toFixed(2)));
	$('#row-price-'+id).val(addCommas(price.toFixed(3)));
	$('#row-total-'+id).val(addCommas(amount.toFixed(2)));
	$('#row-vat-amount-'+id).val(vatAmount);

	recalTotal();
}


function recalTotal() {
	let totalAmount = 0;
	let totalQty = 0;
	let totalVat = 0;
	let billDisc = parseDefault(parseFloat($('#discPrcnt').val()), 0) * 0.01;

	$('.row-qty').each(function() {
		let id = $(this).data('id');
		let qty = parseDefault(parseFloat(removeCommas($('#row-qty-'+id).val())), 0);
		let price = parseDefault(parseFloat(removeCommas($('#row-price-'+id).val())), 0);
		let vatAmount = parseDefault(parseFloat($('#row-vat-amount-'+id).val()), 0);
		let amount = qty * price;

		totalQty += qty;
		totalAmount += amount;
		totalVat += vatAmount;
	});

	let discAmount = totalAmount * billDisc;
	let totalAfDisc = totalAmount - discAmount;
	let vatSum = totalVat - (totalVat * billDisc);
	let docTotal = totalAfDisc + vatSum;

	$('#total-qty').val(addCommas(totalQty.toFixed(2)));
	$('#total-amount').val(addCommas(totalAmount.toFixed(2)));
	$('#disc-amount').val(addCommas(discAmount.toFixed(2)));
	$('#vat-sum').val(addCommas(vatSum.toFixed(2)));
	$('#doc-total').val(addCommas(docTotal.toFixed(2)));
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
		if(e.keyCode == 13 || e.keyCode == 40) {
			let no = parseDefault(parseInt($(this).data('id')), 0);
			focusNextPrice(no);
		}

		if(e.keyCode == 38) {
			let no = parseDefault(parseInt($(this).data('id')), 0);
			focusPrevPrice(no);
		}
	})

	$('.row-qty').keyup(function(e) {
		if(e.keyCode == 13 || e.keyCode == 40) {
			let no = parseDefault(parseInt($(this).data('id')), 0);
			focusNextQty(no);
		}

		if(e.keyCode == 38) {
			let no = parseDefault(parseInt($(this).data('id')), 0);
			focusPrevQty(no);
		}
	})
}


function focusNextPrice(no) {
	$('.row-price').each(function() {
		let ro = parseDefault(parseInt($(this).data('id')), 0);

		if(ro > no) {
			$(this).focus().select();
			return false;
		}
	})
}

function focusPrevPrice(no) {
	$($('.row-price').get().reverse()).each(function() {
		let ro = parseDefault(parseInt($(this).data('id')), 0);
		if(ro < no) {
			$(this).focus().select();
			return false;
		}
	})
}

function focusNextQty(no) {
	$('.row-qty').each(function() {
		let ro = parseDefault(parseInt($(this).data('id')), 0);

		if(ro > no) {
			$(this).focus().select();
			return false;
		}
	})
}

function focusPrevQty(no) {
	$($('.row-qty').get().reverse()).each(function() {
		let ro = parseDefault(parseInt($(this).data('id')), 0);
		if(ro < no) {
			$(this).focus().select();
			return false;
		}
	})
}


function poKeyInit() {
	$('.po-qty').keyup(function(e) {
		if(e.keyCode == 13 || e.keyCode == 40) {
			let no = parseDefault(parseInt($(this).data('no')), 1);
			nextRow(no);
		}

		if(e.keyCode == 38) {
			let no = parseDefault(parseInt($(this).data('no')), 1);
			prevRow(no);
		}
	})
}

function nextRow(no) {
	no = no + 1;
	let uid = $('#uid-'+no).val();

	$('#po-qty-'+uid).focus().select();
}

function prevRow(no) {
	no = no - 1;

	if(no > 0) {
		let uid = $('#uid-'+no).val();
		$('#po-qty-'+uid).focus().select();
	}
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


function confirmPO() {
	let poCode = $.trim($('#poCode').val());

	if(poCode.length) {
		if($('.row-qty').length) {
			swal({
				title:'คุณแน่ใจ ?',
				text:'รายการปัจจุบันจะถูกแทนที่ด้วยรายการจากใบสั่งซื้อเลขที่ ' + poCode,
				type:'warning',
				showCancelButton:true,
				confirmButtonText:'Yes',
				cancelButtonText:'No',
				closeOnConfirm:true
			}, function() {
				setTimeout(() => {
					getPoDetail(poCode);
				}, 100);
			})
		}
		else
		{
			setTimeout(() => {
				getPoDetail(poCode);
			}, 100);
		}
	}
}

function getPoDetail(poCode) {

	if(poCode != "" || poCode == null || poCode == undefined) {
		poCode = $('#poCode').val();
	}

	if(poCode.length == 0) {
		swal({
			title:'Oop!',
			text:'กรุณาระบุเลขที่ใบสั่งซื้อ',
			type:'warning'
		});

		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'get_po_detail',
		type:'GET',
		cache:false,
		data:{
			'po_code' : poCode
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status === 'success') {
					$('#po-code').val(ds.DocNum);
					$('#DocCur').val(ds.DocCur);
					$('#DocRate').val(ds.DocRate);
					$('#vendor_code').val(ds.CardCode);
					$('#vendorName').val(ds.CardName);

					let source = $('#po-template').html();
					let data = ds.details;
					let output = $('#po-body');

					render(source, data, output);

					$('#poGrid').modal('show');

					poKeyInit();

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
		}
	})
}

function getPoItems() {
	let po = $('#poCode').val();

	if(po.length == 0) {
		swal({
			title:'Oops !',
			text:'กรุณาระบุเลขที่ใบสั่งซื้อ',
			type:'warning'
		});

		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'get_po_detail',
		type:'GET',
		cache:false,
		data:{
			'po_code' : poCode
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status === 'success') {
					$('#po-code').val(ds.DocNum);
					$('#DocCur').val(ds.DocCur);
					$('#DocRate').val(ds.DocRate);
					$('#vendor_code').val(ds.CardCode);
					$('#vendorName').val(ds.CardName);

					let source = $('#po-template').html();
					let data = ds.details;
					let output = $('#po-body');

					render(source, data, output);

					$('#poGrid').modal('show');

					poKeyInit();

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
		}
	})
}


$('#poGrid').on('shown.bs.modal', function() {
	let id = $('#uid-1').val();

	$('#po-qty-'+id).focus();
})


function receiveAll() {
	$('.po-qty').each(function() {
		let qty = parseDefault(parseFloat($(this).data('qty')), 0);
		if(qty > 0) {
			$(this).val(addCommas(qty));
		}
	});
}

function clearAll() {
	$('.po-qty').each(function() {
		$(this).val('');
	});
}


function clearPo() {
	let poCode = $('#poCode').val();

	if(poCode.length == 0) {
		return false;
	}

	swal({
		title:'คุณแน่ใจ ?',
		text:'ต้องการเปลียนใบสั่งซื้อหรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonText:'Yes',
		cancelButtonText:'No',
		closeOnConfirm:true
	}, function() {
		load_in();
		setTimeout(() => {
			load_out();
			$('#receive-list').html('');
			$('#no').val(0);
			$('#poCode').val('');
			$('#poCode').removeAttr('disabled');
			$('#btn-get-po').addClass('hide');
			$('#btn-confirm-po').removeClass('hide');
			$('.item-control').removeAttr('disabled');
			$('#total-qty').text('0.00');
			$('#total-amount').text('0.00');

			setTimeout(() => {
				$('#poCode').focus();
			}, 200);
		}, 200);
	});
}
