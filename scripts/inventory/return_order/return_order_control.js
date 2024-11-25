function getInvoice() {
  let invoice_code = $('#invoice').val();

  if(invoice_code.length > 1) {

    if($('.input-qty').length) {
      swal({
        title:'คำเตือน',
        text:'รายการทั้งหมดจะแทนที่ด้วยรายการใหม่ <br/>ต้องการดำเนินการหรือไม่ ?',
        type:'warning',
        html:true,
        showCancelButton:true,
        confirmButtonText:'Yes',
        cancelButtonText:'No',
        closeOnConfirm:true
      }, function() {
        setTimeout(() => {
          loadInvoice();
        }, 100)
      })
    }
    else {
      loadInvoice();
    }
  }
}


function loadInvoice() {
  let invoice_code = $('#invoice').val();
  let code = $('#code').val();

  if(invoice_code.length > 1) {

    load_in();

    $.ajax({
      url:HOME + 'get_invoice',
      type:'POST',
      cache:false,
      data: {
        "code" : code,
        "invoice_code" : invoice_code
      },
      success:function(rs) {
        load_out();
        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.length > 0) {
            let source = $('#invoice-template').html();
            let output = $('#detail-table');
            render(source, ds, output);
            reIndex();
            recalTotal();
            $('#btn-confirm-inv').addClass('hide');
            $('#btn-clear-inv').removeClass('hide');
            $('#invoice').attr('disabled', 'disabled');
            $('.item-control').attr('disabled', 'disabled');
          }
          else {
            swal("ไม่พบรายการขาย");
          }
        }
      }
    })
  }
}


function clearInvoice() {
  swal({
    title:'Clear data',
    text:'รายการทั้งหมดจะถูกลบ ต้องการดำเนินการต่อหรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    confirmButtonText:'Yes',
    cancelButtonText:'No',
    closeOnConfirm:true
  }, function() {
    setTimeout(() => {
      $('#detail-table').html('');
      recalTotal();

      $('#btn-clear-inv').addClass('hide');
      $('#btn-confirm-inv').removeClass('hide');
      $('#invoice').removeAttr('disabled');
      $('#invoice').val('').focus();
      $('.item-control').removeAttr('disabled');

      swal({
        title:'Success',
        type:'success',
        timer:1000
      });
    }, 200)
  })
}


$("#model-code").autocomplete({
	source: BASE_URL + 'auto_complete/get_style_code_and_name',
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
	source:BASE_URL + 'auto_complete/get_product_code_and_name',
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


function getItemGrid() {
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
	let no = generateUID();

	if(qty > 0) {

		$('#input-qty').removeClass('has-error');

		if(item !== null && item !== undefined) {
			let itemCode = item.code;
			let itemName = item.name;
			let amount = qty * price;
			let vatCode = item.purchase_vat_code;
			let vatRate = parseDefault(parseFloat(item.purchase_vat_rate), 0);
			let vatAmount = amount * (vatRate * 0.01);
			let limit = -1;

			let items = {
				'no' : no,
				'product_code' : itemCode,
				'product_name' : itemName,
        'invoice_code' : null,
        'order_code' : null,
        'discount' : 0,
				'vat_rate' : vatRate,
				'price' : price,
				'qty' : qty,
				'limit' : limit,
				'amount' : amount,
				'amountLabel' : addCommas(amount.toFixed(2))
			};

			let source = $('#row-template').html();
			let output = $('#detail-table');

			render_append(source, items, output);

			//--- Calculate Summary
			recalTotal();

			//---- update running no
			reIndex();

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
	let items = [];
	$('#itemGrid').modal('hide');
	load_in();
	$('.item-grid').each(function() {

		let el = $(this);

		if(el.val() != "") {

			let qty = parseDefault(parseFloat(el.val()), 0);

			if(qty > 0) {
				let no = generateUID();
				let itemCode = el.data('code'); //--- product code;
				let itemName = el.data('name');
				let price = parseDefault(parseFloat(el.data('price')), 0.00);
				let amount = qty * price;
				let vatRate = parseDefault(parseFloat(el.data('vatrate')), 0);
  			let limit = -1;

  			let item = {
  				'no' : no,
  				'product_code' : itemCode,
  				'product_name' : itemName,
          'invoice_code' : null,
          'order_code' : null,
          'discount' : 0,
  				'vat_rate' : vatRate,
  				'price' : price,
  				'qty' : qty,
  				'limit' : limit,
  				'amount' : amount,
  				'amountLabel' : addCommas(amount.toFixed(2))
  			};

				items.push(item);
			}
		}
	})

	if(items.length > 0) {

		let source = $('#rows-template').html();
		let output = $('#detail-table');

		render_append(source, items, output);

		//--- Calculate Summary
		recalTotal();

		//---- update running no
		reIndex();

    $('#model-code').val('').focus();
	}

	load_out();
}
