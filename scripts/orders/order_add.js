window.addEventListener('load', () => {
  percent_init();
  item_init();
})

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

        getItem();
		  }
		});
	}
}

var click = 0;

$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


function add() {
  if(click > 0) {
    return false;
  }

  $('.h').removeClass('has-error');

  click = 1;

  let h = {
    'TaxStatus' : $('#tax-status').val(),
    'vat_type' : $('#vat-type').val(),
    'is_term' : $('#is-term').val(),
    'date_add' : $('#date').val(),
    'customer_code' : $.trim($('#customer-code').val()),
    'customer_name' : $.trim($('#customer-name').val()),
    'customer_ref' : $.trim($('#customer_ref').val()),
    'phone' : $('#phone').val(),
    'tax_id' : $('#tax-id').val(),
    'isCompany' : $('is-company').is(':checked') ? 1 : 0,
    'branch_code' : $('#branch-code').val(),
    'branch_name' : $('#branch-name').val(),
    'address' : $('#address').val(),
    'sub_district' : $('#sub-district').val(),
    'district' : $('#district').val(),
    'province' : $('#province').val(),
    'postcode' : $('#postcode').val(),
    'reference' : $('#reference').val(),
    'channels_code' : $('#channels-code').val(),
    'sale_code' : $('#sale-id').val(),
    'warehouse_code' : $('#warehouse').val(),
    'remark' : $('#remark').val()
  };

  if(h.is_term == "") {
    swal("กรุณาเลือกเล่มเอกสาร");
    $('#is-term').addClass('has-error');
    click = 0;
    return false;
  }


  if(h.vat_type == "") {
    swal("กรุณาเลือกชนิด VAT");
    $('#vat-type').addClass('has-error');
    click = 0;
    return false;
  }


  if( ! isDate(h.date_add)) {
    swal("วันที่ไม่ถูกต้อง");
    $('#date').addClass('has-error');
    click = 0;
    return false;
  }

  if(h.customer_code == "") {
    swal("กรุณาระบุรหัสลูกค้า");
    $('#customer-code').addClass('has-error');
    click = 0;
    return false;
  }

  if(h.customer_name == "") {
    swal("กรุณาระบุชื่อลูกค้า");
    $('#customer-name').addClass('has-error');
    click = 0;
    return false;
  }

  if(h.channels_code == "") {
    swal("กรุณาเลือกช่องทางขาย");
    $('#channels-code').addClass('has-error');
    click = 0;
    return false;
  }

  if(h.warehouse_code == "") {
    swal("กรุณาเลือกคลังสินค้า");
    $('#warehouse').addClass('has-error');
    click = 0;
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
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
          editOrder(ds.code);
        }
        else {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error',
            html:true
          })

          click = 0;
        }
      }
      else {
        swal({
          title:'Error',
          text:rs,
          type:'error',
          html:true
        });

        click = 0;
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

      click = 0;
    }
  })
}



$('#qt_no').autocomplete({
	source:BASE_URL + 'auto_complete/get_active_quotation',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function() {
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if(arr.length === 2) {
			$(this).val(arr[0]);
		}
		else {
			$(this).val('');
		}
	}
})


function get_quotation()
{
	var qt_no = $('#qt_no').val();
	var code = $('#order_code').val();

	swal({
		title: "คุณแน่ใจ ?",
		text: "การทั้งเก่าหมดจะถูกลบและโหลดใหม่  ยืนยันการดึงรายการหรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			load_in();
			$.ajax({
				url: BASE_URL + 'orders/orders/load_quotation',
				type:"GET",
				cache:"false",
				data:{
					'order_code' : code,
					'qt_no' : qt_no
				},
				success: function(rs){
					load_out();
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
							title:'Success',
							text:'ดึงรายการใหม่เรียบร้อยแล้ว',
							type:'success',
							timer:1000
						});

						window.location.reload();

					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});

}




//---- เปลี่ยนสถานะออเดอร์  เป็นบันทึกแล้ว
function saveOrder() {
  $('.h').removeClass('has-error');

  let order_code = $('#order_code').val();

  let channels = $('#current-channels_code').val(); //--- ช่องทางขายปัจจุบัน
  let customer = $('#current-customer').val();
  let date = $('#current-date').val();
  let bDiscText = parseDefault(parseFloat($('#bill-disc-percent').val()), 0.00);
  let bDiscAmount = parseDefault(parseFloat(removeCommas($('#bill-disc-amount').val())), 0.00);

  let ds = {
    "is_term" : $('#is-term').val(),
    "vat_type" : $('#vat-type').val(),
    "TaxStatus" : $('#tax-status').val(),
    "id_sender" : $('#id_sender').val(),
    "tracking" : $('#tracking').val(),
    "date_add" : $("#date").val(),
    "customer_code" : $("#customerCode").val(),
    "customer_name" : $('#customer-name').val(),
    "customer_ref" : $('#customer_ref').val(),
    "tax_id" : $('#tax-id').val(),
    "branch_code" : $('#branch-code').val(),
    "branch_name" : $('#branch-name').val(),
    "address" : $('#address').val(),
    "sub_district" : $('#sub-district').val(),
    "district" : $('#district').val(),
    "province" : $('#province').val(),
    "postcode" : $('#postcode').val(),
    "phone" : $('#phone').val(),
    "channels_code" : $("#channels").val(),
    "reference" : $('#reference').val(),
    "warehouse_code" : $('#warehouse').val(),
    "sale_id" : $('#sale-id').val(),
    "remark" : $.trim($("#remark").val()),
    "amountBfDisc" : parseDefault(parseFloat(removeCommas($('#total-amount').val())), 0),
    "bDiscText" : bDiscText,
    "bDiscAmount" : bDiscAmount,
    "WhtPrcnt" : parseDefault(parseFloat($('#whtPrcnt').val()), 0),
    "WhtAmount" : roundNumber(parseDefault(parseFloat($('#wht-amount').val()), 0), 2),
    "VatSum" : roundNumber(parseDefault(parseFloat($('#vat-total').val()), 0), 2),
    "DocTotal" : parseDefault(parseFloat(removeCommas($('#doc-total').val())),0)
  }

  if(ds.is_term == "") {
    swal("กรุณาเลือกเล่มเอกสาร");
    $('#is-term').addClass('has-error');
    return false;
  }

  if(ds.vat_type == "") {
    swal("กรุณาเลือกชนิด VAT");
    $('#vat-type').addClass('has-error');
    return false;
  }

  // if(ds.vat_type == 'I' || ds.vat_type == 'E') {
  //   if(ds.tax_id == "") {
  //     swal("กรุณาระบุเลขที่ผู้เสียภาษี");
  //     $('#tax-id').addClass('has-error');
  //     return false;
  //   }
  // }

  //---- ตรวจสอบวันที่
	if( ! isDate(ds.date_add) ) {
		swal("วันที่ไม่ถูกต้อง");
    $('#date').addClass('has-error');
		return false;
	}

	//--- ตรวจสอบลูกค้า
	if( ds.customer_code.length == 0 || ds.customer_name.length == 0 ) {
		swal("ชื่อลูกค้าไม่ถูกต้อง");
    $('#customer-code').addClass('has-error');
    $('#customer-name').addClass('has-error');
		return false;
	}

  // if( ds.customer_ref.length == 0) {
  //   swal("กรุณาอ้างอิงชื่อลูกค้า");
  //   $('#customer_ref').addClass('has-error');
  //   return false;
  // }

  // if( ds.phone.length == 0) {
  //   swal("กรุณาระบุเบอร์โทรศัทพ์ลูกค้า");
  //   $('#phone').addClass('has-error');
  //   return false;
  // }

  if(ds.channels_code == ""){
    swal('กรุณาเลือกช่องทางขาย');
    $('#channels').addClass('has-error');
    return false;
  }

  if(ds.warehouse_code == "") {
    swal('กรุณาเลือกคลัง');
    $('#warehouse').addClass('has-error');
    return false;
  }

	load_in();

	$.ajax({
		url: BASE_URL + 'orders/orders/save/'+ order_code,
		type:"POST",
    cache:false,
		data:{
      "data" : JSON.stringify(ds)
		},
		success:function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({
          title: 'Saved',
          type: 'success',
          timer: 1000
        });
				setTimeout(() => {
          editOrder(order_code)
        }, 1200);
			}
      else {
				swal("Error ! ", rs , "error");
			}
		}
	});
}


$('#so-code').autocomplete({
  source: BASE_URL + 'orders/orders/get_so_and_job_title',
  autoFocus: true,
  close:function() {
    let result = $(this).val();
    let arr = result.split(' | ');

    if(arr.length > 1) {
      $(this).val(arr[0]);
    }
    else {
      $(this).val('');
    }
  }
});


function takeAll() {
  $('.so-qty').each(function() {
    available = $(this).data('available');
    $(this).val(available);
  });
}

function clearAll() {
	$('.so-qty').each(function() {
		$(this).val('');
	});
}


function loadSo() {
  let soCode = $('#so-code').val();
  let count = $('.line-qty').length;

  if(soCode.length) {

    if(count > 0) {
      swal({
        title:'โหลดใบสั่งขาย',
        text:'รายการที่คีย์ไว้แล้วจะถูกลบ <br/>ต้องการดำเนินการต่อหรือไม่ ?',
        type:'info',
        html:true,
        showCancelButton:true,
        confirmButtonText:'ดำเนินการ',
        cancelButtonText:'ยกเลิก',
        confirmButtonColor:'#428bca',
        closeOnConfirm:true
      }, function() {
        load_so();
      });
    }
    else {
      load_so();
    }
  }
}


function load_so() {
  let code = $('#order_code').val();
  let soCode = $('#so-code').val();

  if(soCode.length) {
    load_in();

    $.ajax({
      url: BASE_URL + 'orders/sales_order/get_details',
      type:'GET',
      cache:false,
      data:{
        'code' : soCode
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {

          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            $('#so-title').text(soCode);
            let data = ds.details;
            let source = $('#so-template').html();
            let output = $('#so-table');

            render(source, data, output);

            $('#soGrid').modal('show');
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


function addSoItem() {
  if(click > 0) {
    return false;
  }

  click = 1;

  let btn = $('#btn-add-so');

  $('.so-qty').removeClass('has-error');

  let err = 0;

  let code = $('#order_code').val();
  let soCode = $('#so-code').val();

  let items = [];

  $('.so-qty').each(function() {
    let el = $(this);

    if(el.val() != "") {
      let qty = parseDefault(parseFloat(el.val()), 0);
      let available = parseDefault(parseFloat(el.data('available')), 0);

      if(qty > available) {
        el.addClass('has-error');
        err++;
      }
      else {
        if(qty > 0) {

          let item = {
            "style_code"	: el.data('style'),
            "product_code"	: el.data('code'),
            "product_name"	: el.data('name'),
            "cost"  : el.data('cost'),
            "price"	: el.data('price'),
            "sell_price" : el.data('sellprice'),
            "qty"		: qty,
            "discount_label" : el.data('discprcnt'),
            "discount_amount" : el.data('discamount'),
            "avgBillDiscAmount" : el.data('avgbilldisc'),
            "vat_code" : el.data('vatcode'),
            "vat_rate" : el.data('vatrate'),
            "vat_type" : el.data('vattype'),
            "baseCode" : el.data('basecode'),
            "baseLine" : el.data('baseline'),
            "baseId" : el.data('baseentry'),
            "line_id" : el.data('baseline'),
            "is_count" : el.data('iscount')
          };

          items.push(item);
        }
      }
    }
  });

  if(err > 0 || items.length == 0) {
    click = 0;
    btn.removeAttr('disabled');
    return false;
  }

  if(items.length > 0) {

    $('#soGrid').modal('hide');

    load_in();

    $.ajax({
      url:BASE_URL + 'orders/orders/load_so',
      type:'POST',
      cache:false,
      data:{
        'code' : code,
        'so_code' : soCode,
        'details' : JSON.stringify(items)
      },
      success:function(rs) {
        load_out();

        if(rs == 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          setTimeout(() => {
            window.location.reload();
          }, 1200);
        }
      }
    })
  }
}


function clearSO() {
  let soCode = $('#so-code').val();
  let code = $('#order_code').val();

  swal({
    title:'คุณแน่ใจ ?',
    text:'รายการนำเข้าจาก ' + soCode + ' จะถูกลบ <br/> ต้องการดำเนินการต่อหรือไม่ ?',
    type:'warning',
    html:true,
    showCancelButton:true,
    confirmButtonText:'Yes',
    cancelButtonText:'No',
    closeOnConfirm:true
  }, function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:BASE_URL + 'orders/orders/clear_so',
        type:'POST',
        cache:false,
        data:{
          'code' : code,
          'so_code' : soCode
        },
        success:function(rs) {
          load_out();

          if(rs == 'success') {
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
              text:rs,
              type:'error',
              html:true
            });
          }
        }
      })
    },100);
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
      $('#customerCode').val(code);
      $('#customer-code').val(code);
      $('#customer-name').val(name);
      $('#tax-id').val(tax_id);

      get_bill_to_address(code);
    }
    else {
      $("#customerCode").val('');
			$('#customer-code').val('');
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
      url:HOME + 'get_customer_bill_to_address',
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
                $('#customer_ref').val(adr.name);
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
            else {
              $('#customer_ref').val('');
              $('#phone').val('');
              $('#branch-code').val('');
              $('#branch-name').val('');
              $('#address').val('');
              $('#sub-district').val('');
              $('#district').val('');
              $('#province').val('');
              $('#postcode').val('');
            }
          }
          else {
            $('#customer_ref').val('');
            $('#phone').val('');
            $('#branch-code').val('');
            $('#branch-name').val('');
            $('#address').val('');
            $('#sub-district').val('');
            $('#district').val('');
            $('#province').val('');
            $('#postcode').val('');
          }
        }
      }
    })
  }
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

  item_init();
  recalTotal();
  updateVatType();
}


function updateTaxStatus() {
  let type = $('#vat-type').val();
  let taxStatus = type == 'N' ? 'N' : 'Y';
  $('#tax-status').val(taxStatus);
}


//---- เพิ่มรายการสินค้าเช้าออเดอร์
function addToOrder(){
  var order_code = $('#order_code').val();
	//var count = countInput();
  var data = [];
  $(".order-grid").each(function(index, element){
    if($(this).val() != ''){
      var code = $(this).attr('id');
      var arr = code.split('qty_');
      data.push({'code' : arr[1], 'qty' : $(this).val()});
    }
  });

	if(data.length > 0 ){
		$("#orderGrid").modal('hide');
		$.ajax({
			url: BASE_URL + 'orders/orders/add_detail/'+order_code,
			type:"POST",
      cache:"false",
      data: {
        'data' : data
      },
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
            title: 'success',
            type: 'success',
            timer: 1000
          });
					$("#btn-save-order").removeClass('hide');
					updateDetailTable(); //--- update list of order detail
				}else{
					swal("Error", rs, "error");
				}
			}
		});
	}
}



//---- เพิ่มรายการสินค้าเช้าออเดอร์
function addItemToOrder(){
	var orderCode = $('#order_code').val();
	var qty = parseDefault(parseInt($('#input-qty').val()), 0);
  var allow_over_stock = $('#allow-over-stock').val();
	var limit = allow_over_stock == '1' ? 100000 : parseDefault(parseInt($('#available-qty').val()), 0);
	var itemCode = $('#item-code').val();
  let billDiscPrcnt = parseDefault(parseFloat($('#bill-disc-percent').val()), 0);
  let billDiscAmount = parseDefault(parseFloat(removeCommas($('#bill-disc-amount').val())), 0);
  let whtPrcnt = parseDefault(parseFloat($('#whtPrcnt').val()), 0);
  let vat_type = $('#vat-type').val();

  let data = {
    "order_code" : orderCode,
    "product_code" : itemCode,
    "qty" : qty,
    "billDiscPrcnt" : billDiscPrcnt,
    "billDiscAmount" : billDiscAmount,
    "whtPrcnt" : whtPrcnt,
    "vat_type" : vat_type
  }

	if(qty > 0 && qty <= limit){
		load_in();
		$.ajax({
			url:BASE_URL + 'orders/orders/add_order_row',
			type:"POST",
			cache:"false",
			data:{
				'data' : JSON.stringify(data)
			},
			success: function(rs) {
				load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);
          if(ds.status == 'success') {
            let row = ds.row;
            let source = $('#row-template').html();
            let output = $('#detail-table');
            $('#no-rows').remove();
            render_append(source, ds.row, output);

            reIndex();
            recalTotal();

            $("#btn-save-order").removeClass('hide');
            $('#btn-change-state').addClass('hide');

            setTimeout(function(){
  						$('#item-code').val('');
              $('#item-price').val('');
  						$('#stock-qty').val('');
              $('#reserv-qty').val('');
              $('#available-qty').val('');
  						$('#input-qty').val('');
  						$('#item-code').focus();
  					},1200);
          }
          else {
            swal({
              title:'Error!',
              text:ds.message,
              type:'error'
            })
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
		});
	}
}


// JavaScript Document
function updateDetailTable(){
	var order_code = $("#order_code").val();
	$.ajax({
		url: BASE_URL + 'orders/orders/get_detail_table/'+order_code,
		type:"GET",
    cache:"false",
		success: function(rs){
			if( isJson(rs) ){
				var source = $("#detail-table-template").html();
				var data = $.parseJSON(rs);
				var output = $("#detail-table");
				render(source, data, output);
        percent_init();
			}
			else
			{
				var source = $("#nodata-template").html();
				var data = [];
				var output = $("#detail-table");
				render(source, data, output);
			}
		}
	});
}



function removeDetail(id, name){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '" + name + "' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: BASE_URL + 'orders/orders/remove_item_row/'+ id,
				type:"POST",
        cache:"false",
				success: function(rs) {
					if( rs == 'success' ) {
						swal({ title: 'Deleted', type: 'success', timer: 1000 });
						$('#row-'+id).remove();
            $('#text-row-'+id).remove();
            reIndex();
            recalTotal();
					}
          else {
						swal("Error !", rs , "error");
					}
				}
			});
	});
}




$("#pd-box").autocomplete({
	source: BASE_URL + 'auto_complete/get_style_code',
	autoFocus: true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    $(this).val(arr[0]);
  }
});




$('#pd-box').keyup(function(event) {
	if(event.keyCode == 13){
		var code = $(this).val();
		if(code.length > 0){
			setTimeout(function(){
				getProductGrid();
			}, 300);

		}
	}

});


$('#item-code').keyup(function(e){
	if(e.keyCode == 13){
		var code = $(this).val();
		if(code.length > 1){
			setTimeout(function(){
				getItem();
			}, 200);
		}
	}
});


function getItem() {
	let code = $('#item-code').val();
  let whCode = $('#warehouse').val();

	if(code.length > 0) {
		$.ajax({
			url:HOME + 'get_item',
			type:'POST',
			cache:false,
			data:{
				'item_code' : code,
        'warehouse_code' : whCode
			},
			success:function(rs) {
				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status === 'success') {
						$('#item-data').val(JSON.stringify(ds.item));
						let price = roundNumber(ds.item.price, 2);
            let stock = roundNumber(ds.item.stock, 2);
            let reserv = roundNumber(ds.item.reserv_stock, 2);
            let available = roundNumber(ds.item.available, 2);

						$('#item-price').val(price);
						$('#stock-qty').val(stock);
            $('#reserv-qty').val(reserv)
            $('#available-qty').val(available);
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

$('#input-qty').keyup(function(e){
	if(e.keyCode == 13){
		addItemToOrder();
	}
});




//--- ตรวจสอบจำนวนที่คีย์สั่งใน order grid
function countInput(){
	var qty = 0;
	$(".order-grid").each(function(index, element) {
        if( $(this).val() != '' ){
			qty++;
		}
    });
	return qty;
}


function validUpdate(){
  $('.edit').removeClass('has-error');
	var date_add = $("#date").val();
	var customer_code = $("#customerCode").val();
  var customer_ref = $('#customer_ref').val();
  var phone = $('#phone').val();
  var customer_name = $('#customer-name').val();
	var channels_code = $("#channels").val();
	var payment_code = $("#payment").val();
  var recal = 0;


	//---- ตรวจสอบวันที่
	if( ! isDate(date_add) ){
		swal("วันที่ไม่ถูกต้อง");
    $('#date').addClass('has-error');
		return false;
	}

	//--- ตรวจสอบลูกค้า
	if( customer_code.length == 0 || customer_name == "" ){
		swal("ชื่อลูกค้าไม่ถูกต้อง");
    $('#customer-code').addClass('has-error');
    $('#customer-name').addClass('has-error');
		return false;
	}

  if( customer_ref.length == 0) {
    swal("กรุณาอ้างอิงชื่อลูกค้า");
    $('#customer_ref').addClass('has-error');
    return false;
  }

  if( phone.length == 0) {
    swal("กรุณาระบุเบอร์โทรศัทพ์ลูกค้า");
    $('#phone').addClass('has-error');
    return false;
  }

  if(channels_code == ""){
    swal('กรุณาเลือกช่องทางขาย');
    $('#channels').addClass('has-error');
    return false;
  }


  if(payment_code == ""){
    swal('กรุณาเลือกช่องทางการชำระเงิน');
    $('#payment').addClass('has-error');
    return false;
  }

	//--- ตรวจสอบความเปลี่ยนแปลงที่สำคัญ
	if( (date_add != date) || ( customer_code != customer ) || ( channels_code != channels ) || ( payment_code != payment ) )
  {
		recal = 1; //--- ระบุว่าต้องคำนวณส่วนลดใหม่
	}

  updateOrder(recal);
}





function updateOrder(recal){
	var order_code = $("#order_code").val();
	var date_add = $("#date").val();
	var customer_code = $("#customerCode").val();
  var customer_name = $('#customer-name').val();
  var customer_ref = $('#customer_ref').val();
  var phone = $('#phone').val();
	var channels_code = $("#channels").val();
	var payment_code = $("#payment").val();
	var reference = $('#reference').val();
  var warehouse_code = $('#warehouse').val();
  var sale_id = $('#sale-id').val();
	var transformed = $('#transformed').val();
	var remark = $("#remark").val();

	load_in();

	$.ajax({
		url:BASE_URL + 'orders/orders/update_order',
		type:"POST",
		cache:"false",
		data:{
      "order_code" : order_code,
  		"date_add"	: date_add,
  		"customer_code" : customer_code,
      "customer_ref" : customer_ref,
      "phone" : phone,
  		"channels_code" : channels_code,
  		"payment_code" : payment_code,
      "sale_id" : sale_id,
  		"reference" : reference,
      "warehouse_code" : warehouse_code,
  		"remark" : remark,
			"transformed" : transformed,
      "recal" : recal
    },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({
          title: 'Done !',
          type: 'success',
          timer: 1000
        });

				setTimeout(function(){
          window.location.reload();
        }, 1200);

			}else{
				swal({
          title: "Error!",
          text: rs,
          type: 'error'
        });
			}
		}
	});
}



function recalDiscount(){
	updateOrder(1);
}


function updateItem(id) {
  let code = $('#order_code').val();
	let qty = parseDefault(parseFloat($('#qty-'+id).val()), 1.00);
	let price = parseDefault(parseFloat($('#price-'+id).val()), 0.00);
	let disc = $('#disc-'+id).val();
  let currentQty = parseDefault(parseFloat($('#currentQty-'+id).val()), 0);
	let currentPrice = parseDefault(parseFloat($('#currentPrice-'+id).val()), 0);
	let currentDisc = $('#currentDisc-'+id).val();
  let name = $('#pd-name-'+id).val();
  let pdCode = $('#qty-'+id).data('code');

	disc = disc == '' ? 0 : disc;
	currentDisc = currentDisc == '' ? 0 : currentDisc;

  if(price < 0) {
    price = price * (-1);
    $('#price-'+id).val(price.toFixed(2));
    recalItem(id);
  }

  let amountBfDisc = roundNumber(parseDefault(parseFloat(removeCommas($('#total-amount').val())), 0), 2);
  let billDiscPrcnt = roundNumber(parseDefault(parseFloat($('#bill-disc-percent').val()), 0), 2);
  let billDiscAmount = roundNumber(parseDefault(parseFloat(removeCommas($('#bill-disc-amount').val())), 0.00), 2);
  let whtPrcnt = roundNumber(parseDefault(parseFloat($('#whtPrcnt').val()), 0), 2);
  let whtAmount = roundNumber(parseDefault(parseFloat($('#wht-amount').val()), 0), 2);
  let vatSum = roundNumber(parseDefault(parseFloat($('#vat-total').val()), 0), 2);
  let docTotal = roundNumber(parseDefault(parseFloat(removeCommas($('#doc-total').val())), 0), 2);
  let vatType = $('#vat-type').val();

  setTimeout(() => {
    $.ajax({
      url:BASE_URL + 'orders/orders/update_item',
      type:'POST',
      cache:false,
      data:{
        'order_code' : code,
        'id' : id,
        'price' : price,
        'qty' : qty,
        'discount_label' : disc,
        'product_code' : pdCode,
        'product_name' : name,
        'amountbfDisc' : amountBfDisc,
        'billDiscPrcnt' : billDiscPrcnt,
        'billDiscAmount' : billDiscAmount,
        'whtPrcnt' : whtPrcnt,
        'whtAmount' : whtAmount,
        'vatSum' : vatSum,
        'docTotal' : docTotal,
        'vatType' : vatType
      },
      success:function(rs) {
        if(rs == 'success') {
          //--- update current
          $('#currentPrice-'+id).val(price);
          $('#currentDisc-'+id).val(disc);

          recalItem(id);
          $('#btn-change-state').addClass('hide');
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'eror'
          });

          //--- roll back data
          var c_price = addCommas($('#currentPrice-'+id).val());
          var c_disc = $('#currentDisc-'+id).val();

          $('#price-'+id).val(c_price);
          $('#disc-'+id).val(c_disc);

          recalItem(id);
        }
      }
    })
  }, 100);
}

function recalItem(id) {
	var price = parseDefault(parseFloat($('#price-'+id).val()), 0);
	var qty = parseDefault(parseFloat($('#qty-'+id).val()), 1.00);
	var disc = parseDiscountAmount($('#disc-'+id).val(), price);
	var sell_price = price - disc;
	var total = qty * sell_price;
	var discount_amount = qty * disc;

	$('#total-'+id).val(addCommas(total.toFixed(2)));
	$('#sellPrice-'+id).val(sell_price);
	$('#discAmount-'+id).val(discount_amount);

	recalTotal();
}


function recalTotal() {
  let totalQty = 0;
  let totalBfDisc = 0.00; //--- มูลค่ารวมสินค้าหลังส่วนลดรายการ ก่อนส่วนลดท้ายบิล
	let billDiscAmount = roundNumber(parseDefault(parseFloat(removeCommas($('#bill-disc-amount').val())), 0.00), 2); //--- มูลค่าส่วนลดท้ายบิล
  let billDiscPrcnt = roundNumber(parseDefault(parseFloat($('#bill-disc-percent').val()), 0), 2);
	let totalTaxAmount = 0.00; //-- มูลค่าภาษีรวมหลังส่วนลดท้ายบิล
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
  docTotal = vatType == 'E' ? roundNumber(amountAfterDisc + totalTaxAmount, 2) : roundNumber(amountAfterDisc, 2);

	whtAmount = vatType == 'E' ? roundNumber(amountAfterDisc * (whtPrcnt * 0.01)) : roundNumber((amountAfterDisc - totalTaxAmount) * (whtPrcnt * 0.01), 2);

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
}




$('#bill-disc-percent').focus(function() {
	$(this).select();
});

$('#bill-disc-amount').focus(function() {
	$(this).select();
})

$('#whtPrcnt').focus(function() {
	$(this).select();
})

$('#whtPrcnt').change(function() {
  recalTotal();
  updateWithHoldingTax();
});


$('#bill-disc-amount').change(function() {
  $(this).removeClass('has-error');
  let total = parseDefault(parseFloat(removeCommas($('#total-amount').val())), 0);
	let discAmount = parseDefault(parseFloat(removeCommas($(this).val())), 0.00);

  if(discAmount > total) {
    $(this).addClass('has-error');
  }
  else {
    if(discAmount < 0) {
      $(this).val(0);
    }

    $('#bill-disc-percent').val(0.00);
    $(this).val(addCommas(discAmount.toFixed(2)));

    updateBillDiscAmount();
  }

  recalTotal();
});


$('#bill-disc-percent').change(function() {
  $(this).removeClass('has-error');
	let total = parseDefault(parseFloat(removeCommas($('#total-amount').val())), 0);
	let disc = $(this).val();

	if(disc < 0) {
		$(this).val(0);
	}
	else if(disc > 100) {
		$(this).addClass('has-error');
	}
	else {
		let discAmount = (total * (disc * 0.01));
		$('#bill-disc-amount').val(addCommas(discAmount.toFixed(2)));
    updateBillDiscAmount();
		recalTotal();
	}
});


function updateVatType() {
  let code = $('#order_code').val();
  let vat_type = $('#vat-type').val();

  if(vat_type == "") {
    swal("กรุณาเลือกชนิด VAT");
    return false;
  }

  load_in();

  $.ajax({
    url:BASE_URL + 'orders/orders/update_vat_type',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'vat_type' : vat_type
    },
    success:function(rs) {
      load_out();
      if(rs != 'success') {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    }
  })
}

function updateBillDiscAmount() {
  let code = $('#order_code').val();
  let billDiscAmount = roundNumber(parseDefault(parseFloat(removeCommas($('#bill-disc-amount').val())), 0.00), 2);
  let billDiscPrcnt = roundNumber(parseDefault(parseFloat($('#bill-disc-percent').val()), 0.00), 2);

  $.ajax({
    url:BASE_URL + 'orders/orders/update_bill_discount',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'DiscPrcnt' : billDiscPrcnt,
      'DiscAmount' : billDiscAmount
    },
    success:function(rs) {
      if(rs != 'success') {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    }
  })
}


function updateWithHoldingTax() {
  let code = $('#order_code').val();
  let whtPrcnt = roundNumber(parseDefault(parseFloat($('#whtPrcnt').val()), 0.00), 2);
  let whtAmount = roundNumber(parseDefault(parseFloat($('#wht-amount').val()), 0.00), 2);

  $.ajax({
    url:BASE_URL + 'orders/orders/update_with_holding_tax',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'whtPrcnt' : whtPrcnt,
      'whtAmount' : whtAmount
    },
    success:function(rs) {
      if(rs != 'success') {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    }
  })
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

// JavaScript Document
function changeState(){
    var order_code = $("#order_code").val();
    var state = $("#stateList").val();
		var trackingNo = $('#trackingNo').val();
		var tracking = $('#tracking').val();
		var id_address = $('#address_id').val();
		var id_sender = $('#id_sender').val();
		var cancle_reason = $.trim($('#cancle-reason').val());

		if(state == 9 && cancle_reason.length < 10) {
      $('#cancle-reason').removeClass('has-error');
			$('#cancle-modal').modal('show');
			return false;
		}


    if( state != 0){
      load_in();
        $.ajax({
            url:BASE_URL + 'orders/orders/order_state_change',
            type:"POST",
            cache:"false",
            data:{
              "order_code" : order_code,
              "state" : state,
							"id_address" : id_address,
							"id_sender" : id_sender,
							"tracking" : tracking,
							"cancle_reason" : cancle_reason
            },
            success:function(rs){
              load_out();
                var rs = $.trim(rs);
                if(rs == 'success'){
                    swal({
                      title:'success',
                      text:'status updated',
                      type:'success',
                      timer: 1000
                    });

                    setTimeout(function(){
                      window.location.reload();
                    }, 1500);

                }else{
                    swal("Error !", rs, "error");
                }
            }
        });
    }
}



function doCancle() {
	let reason = $.trim($('#cancle-reason').val());

	if( reason.length < 10) {
		$('#cancle-reason').addClass('has-error').focus();
		return false;
	}

	$('#cancle-modal').modal('hide');

	return changeState();
}



$('#cancle-modal').on('shown.bs.modal', function() {
	$('#cancle-reason').focus();
});


function showReason() {
	$('#cancle-reason-modal').modal('show');
}


function setNotExpire(option){
  var order_code = $('#order_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'orders/orders/set_never_expire',
    type:'POST',
    cache:'false',
    data:{
      'order_code' : order_code,
      'option' : option
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

        setTimeout(function(){
          window.location.reload();
        },1500);
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}

function unExpired(){
  var order_code = $('#order_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'orders/orders/un_expired',
    type:'GET',
    cache:'false',
    data:{
      'order_code' : order_code
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

        setTimeout(function(){
          window.location.reload();
        },1500);
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}


function validateOrder(){
  var prefix = $('#prefix').val();
  var runNo = parseInt($('#runNo').val());
  let code = $.trim($('#code').val());

  if(code.length == 0){
    $('#btn-submit').click();
    return false;
  }

  let arr = code.split('-');

  if(arr.length == 2){
    if(arr[0] !== prefix){
      swal('Prefix ต้องเป็น '+prefix);
      return false;
    }else if(arr[1].length != (4 + runNo)){
      swal('Run Number ไม่ถูกต้อง');
      return false;
    }else{
      $.ajax({
        url: BASE_URL + 'orders/orders/is_exists_order/'+code,
        type:'GET',
        cache:false,
        success:function(rs){
          if(rs == 'not_exists'){
            $('#btn-submit').click();
          }else{
            swal({
              title:'Error!!',
              text: rs,
              type: 'error'
            });
          }
        }
      })
    }

  }else{
    swal('เลขที่เอกสารไม่ถูกต้อง');
    return false;
  }
}

function insertTextRow(id) {
	setTimeout(() => {
		var data = {"id" : id};
		var source = $('#text-template').html();
		var output = $('#row-'+id);

		render_after(source, data, output);
		reIndex();
		$('#text-'+id).focus();
		$('#add-text-'+id).addClass('hide');
	}, 100)
}

function removeTextRow(id) {
	$('#text-row-'+id).remove();
	$('#add-text-'+id).removeClass('hide');
  updateLineText(id);
	reIndex();
	recalTotal();
}

function updateLineText(id) {
  let exists = $('#text-'+id).length;
  let text = exists == 1 ? $('#text-'+id).val() : "";
  let data = {
    "id" : id,
    "line_text" : text
  }

  $.ajax({
    url:BASE_URL + 'orders/orders/update_line_text',
    type:'POST',
    cache:false,
    data:{
      "data" : JSON.stringify(data)
    },
    success:function(rs) {
      if(rs == 'success') {
        console.log('updated');
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    }
  });
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
