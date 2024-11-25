window.addEventListener('load', () => {
  percent_init();
})

$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


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
function saveOrder(){
  var order_code = $('#order_code').val();
	var id_sender = $('#id_sender').val();
	var tracking = $('#tracking').val();
	$.ajax({
		url: BASE_URL + 'orders/orders/save/'+ order_code,
		type:"POST",
    cache:false,
		data:{
			'id_sender' : id_sender,
			'tracking' : tracking
		},
		success:function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({
          title: 'Saved',
          type: 'success',
          timer: 1000
        });
				setTimeout(function(){ editOrder(order_code) }, 1200);
			}else{
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
      $('#remark').focus();
    }
    else {
      $(this).val('');
    }
  }
});


function getAll() {
	$('.so-qty').each(function() {
		let qty = parseDefault(parseFloat($(this).data('qty')), 0);
		if(qty > 0) {
			$(this).val(addCommas(qty));
		}
	});
}

function clearAll() {
	$('.so-qty').each(function() {
		$(this).val('');
	});
}

function loadSO() {
  let code = $('#order_code').val();
  let soCode = $('#so-code').val();

  if(soCode.length) {
    load_in();

    $.ajax({
      url: BASE_URL + 'orders/orders/get_so_details',
      type:'POST',
      cache:false,
      data:{
        'code' : code,
        'so_code' : soCode
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {

          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            $('#so-title').text(soCode);
            let data = ds.data;
            let source = $('#so-template').html();
            let output = $('#so-body');

            render(source, data, output);

            $('#soModal').modal('show');
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
  let code = $('#order_code').val();
  let soCode = $('#so-code').val();

  let items = [];

  $('.so-qty').each(function() {
    let el = $(this);

    if(el.val() != "") {
      let qty = parseDefault(parseFloat(el.val()), 0);

      if(qty > 0) {

        let item = {
          "id_order" :  el.data('orderid'),
          "order_code" : el.data('ordercode'),
          "style_code"	: el.data('model'),
          "product_code"	: el.data('code'),
          "product_name"	: el.data('name'),
          "cost"  : el.data('cost'),
          "price"	: el.data('price'),
          "qty"		: qty,
          "vat_code" : el.data('vatcode'),
          "vat_rate" : el.data('vatrate'),
          "baseCode" : el.data('basecode'),
          "line_id" : el.data('lineid')
        };

        items.push(item);
      }
    }
  });

  if(items.length > 0) {

    $('#soModal').modal('hide');

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

            updateDetailTable();

            $('#so-code').val('');
            $('#reference').val('');
            $('#so-code').removeAttr('disabled');
            $('#btn-clear-so').addClass('hide');
            $('#btn-add-so').removeClass('hide');
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

$("#customer").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
			$('#customer_code').val(code);
			$("#customer").val(name);
		}else{
			$("#customerCode").val('');
			$('#customer_code').val('');
			$(this).val('');
		}
	}
});


$("#customer_code").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
			$('#customer_code').val(code);
			$("#customer").val(name);
		}else{
			$("#customerCode").val('');
			$('#customer_code').val('');
			$(this).val('');
		}
	}
});


var customer;
var channels;
var payment;
var date;


function getEdit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');

  customer = $("#customerCode").val();
	channels = $("#channels").val();
	payment  = $("#payment").val();
	date = $("#date").val();
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
	var limit = allow_over_stock == '1' ? 100000 : parseDefault(parseInt($('#stock-qty').val()), 0);
	var itemCode = $('#item-code').val();
  var data = [{'code':itemCode, 'qty' : qty}];

	if(qty > 0 && qty <= limit){
		load_in();
		$.ajax({
			url:BASE_URL + 'orders/orders/add_detail/'+orderCode,
			type:"POST",
			cache:"false",
			data:{
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

					setTimeout(function(){
						$('#item-code').val('');
						$('#stock-qty').val('');
						$('#input-qty').val('');
						$('#item-code').focus();
					},1200);


				}else{
					swal("Error", rs, "error");
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
				url: BASE_URL + 'orders/orders/remove_detail/'+ id,
				type:"POST",
        cache:"false",
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title: 'Deleted', type: 'success', timer: 1000 });
						updateDetailTable();
					}else{
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



$('#item-code').autocomplete({
	source:BASE_URL + 'auto_complete/get_product_code_and_name',
	minLength: 1,
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
		if(code.length > 1){
			setTimeout(function(){
				getItemGrid();
			}, 200);
		}
	}
});


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
  var customer_name = $('#customer').val();
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
    $('#customer_code').addClass('has-error');
    $('#customer').addClass('has-error');
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
  var customer_name = $("#customer").val();
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
  console.log('update');
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

  setTimeout(() => {
    $.ajax({
      url:BASE_URL + 'orders/orders/update_item',
      type:'POST',
      cache:false,
      data:{
        'id' : id,
        'price' : price,
        'qty' : qty,
        'discount_label' : disc,
        'product_code' : pdCode,
        'product_name' : name
      },
      success:function(rs) {
        if(rs == 'success') {
          //--- update current
          $('#currentPrice-'+id).val(price);
          $('#currentDisc-'+id).val(disc);

          recalItem(id);
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
	var totalQty = 0;
  var totalPrice = 0;
	var total = 0.00; //--- total amount after row discount
	var totalDisc = 0;

	$('.line-qty').each(function(){
		let no = $(this).data('id');
		let qty = parseDefault(parseFloat($('#qty-'+no).val()), 0.00);
		let price = roundNumber(parseDefault(parseFloat($('#price-'+no).val()), 0.00), 2);
		let amount = roundNumber(parseDefault(parseFloat(removeCommas($('#total-'+no).val())), 0.00), 2);
    let disc = roundNumber(parseDefault(parseFloat($('#discAmount-'+no).val()), 0.00), 2);
    let priceAmount = roundNumber((qty * price), 2);

		if(qty > 0 && price > 0)
		{
			total += amount;
      totalPrice += priceAmount;
      totalDisc += disc;
      totalQty += qty;
		}
	});

	//--- update bill discount
	$('#total-qty').text(addCommas(totalQty));
  $('#total-td').text(addCommas(totalPrice.toFixed(2)));
  $('#discount-td').text(addCommas(totalDisc.toFixed(2)));
  $('#netAmount-td').text(addCommas(total.toFixed(2)));
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
