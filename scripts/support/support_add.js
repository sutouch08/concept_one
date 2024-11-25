$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


//---- เปลี่ยนสถานะออเดอร์  เป็นบันทึกแล้ว
function saveOrder(){
  var order_code = $('#order_code').val();
	$.ajax({
		url: HOME + 'save/'+ order_code,
		type:"POST",
    cache:false,
		success:function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({
          title: 'Saved',
          type: 'success',
          timer: 1000
        });

				setTimeout(function(){
          editOrder(order_code)
        }, 1200);

			}else{
				swal("Error ! ", rs , "error");
			}
		}
	});
}



$("#customerCode").autocomplete({
	source: BASE_URL + 'auto_complete/get_support',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
			$("#customer").val(name);
      getBudget(code);
		}
    else {
			$("#customerCode").val('');
			$("#customer").val('');
      $('#budgetLabel').val('');
      $('#budgetAmount').val(0);
		}
	}
});


$("#customer").autocomplete({
	source: BASE_URL + 'auto_complete/get_support',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
			$("#customer").val(name);
      getBudget(code);
		}else{
			$("#customerCode").val('');
			$("#customer").val('');
      $('#budgetAmount').val(0);
      $('#budgetLabel').val('');
		}
	}
});


function getBudget(code){
  $.ajax({
    url: HOME + 'get_support_budget/'+code,
    type:'GET',
    cache:false,
    success:function(rs){
      $('#budgetAmount').val(rs);
      $('#budgetLabel').val(addCommas(rs));
    }
  });
}



$('#customer').focusout(function(){
  var code = $(this).val();
  if(code.length == 0)
  {
    $('#customerCode').val('');
    $('#budgetLabel').val('');
    $('#budgetAmount').val(0);
  }
});

$('#customerCode').focusout(function(){
  var code = $(this).val();
  if(code.length == 0)
  {
    $('#customer').val('');
    $('#budgetLabel').val('');
    $('#budgetAmount').val(0);
  }
});


function addOrder() {
  $('.h').removeClass('has-error');

  let h = {
    'customer_code' : $('#customerCode').val(),
    'customer_name' : $('#customer').val(),
    'date_add' : $('#date').val(),
    'empName' : $('#empName').val(),
    'warehouse' : $('#warehouse').val(),
    'remark' : $.trim($('#remark').val())
  }

  if(h.customer_code.length == 0) {
    swal("รหัสผู้เบิกไม่ถูกต้อง");
    $('#customerCode').addClass('has-error');
    return false;
  }

  if(h.customer_name.length == 0){
    swal("ชื่อผู้เบิกไม่ถูกต้อง");
    $('#customer').addClass('has-error');
    return false;
  }

  if( ! isDate(h.date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    $('#date').addClass('has-error');
    return false;
  }

  if(empName.length == 0)
  {
    swal('ชื่อผู้เบิกไม่ถูกต้อง');
    $('#empName').addClass('has-error');
    return false;
  }

  if(warehouse.length == 0){
    swal('กรุณาเลือกคลัง');
    $('#warehouse').addClass('has-error');
    return false;
  }

  load_in();

  $.ajax({
    url:HOME  + 'add',
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
          window.location.href = HOME + 'edit_detail/'+ds.code;
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
        })
      }
    },
    error:function(rs) {
      load_out();

      swal({
        title:'Error!',
        text:rs.responseText,
        type:'error',
        html:true
      })
    }
  })
}


var customer;
var channels;
var payment;
var date;

function getEdit(){
  let approved = $('#is_approved').val();
  if(approved == 1){
    $('#remark').removeAttr('disabled');
  } else {
    $('.edit').removeAttr('disabled');
  }

  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
  customer = $("#customerCode").val();
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
	var date_add = $("#date").val();
	var customer_code = $("#customerCode").val();
  var customer_name = $('#customer').val();
	var user_ref = $("#user_ref").val();
  var warehouse = $('#warehouse').val();

	//---- ตรวจสอบวันที่
	if( ! isDate(date_add) ){
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

	//--- ตรวจสอบลูกค้า
	if( customer_code.length == 0 || customer_name == "" ){
		swal("ชื่อลูกค้าไม่ถูกต้อง");
		return false;
	}

  if(user_ref == ""){
    swal('กรุณาระบุผู้เบิก[ผู้สั่งงาน]');
    return false;
  }

  if(warehouse.length == 0)
  {
    swal('กรุณาเลือกคลัง');
    return false;
  }

  updateOrder();
}





function updateOrder(){
	var order_code = $("#order_code").val();
	var date_add = $("#date").val();
	var customer_code = $("#customerCode").val();
  var customer_name = $("#customer").val();
	var user_ref = $('#user_ref').val();
  var warehouse = $('#warehouse').val();
	var remark = $("#remark").val();

	load_in();

	$.ajax({
		url:HOME + 'update_order',
		type:"POST",
		cache:"false",
		data:{
      "order_code" : order_code,
  		"date_add"	: date_add,
  		"customer_code" : customer_code,
      "user_ref" : user_ref,
      "warehouse" : warehouse,
  		"remark" : remark,
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


// JavaScript Document
function changeState(){
    var order_code = $("#order_code").val();
    var state = $("#stateList").val();
		var id_address = $('#address_id').val();
		var id_sender = $('#id_sender').val();
		var trackingNo = $('#trackingNo').val();
		var tracking = $('#tracking').val();
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
                    swal({
											title:'Error!',
											text:rs,
											type:'error',
											html:true
										});
                }
            },
						error:function(xhr, status, error) {
							load_out();
							swal({
								title:'Error!',
								text:xhr.responseText,
								type:'error',
								html:true
							})
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
