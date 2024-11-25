$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


//---- เปลี่ยนสถานะออเดอร์  เป็นบันทึกแล้ว
function saveOrder(){
  var order_code = $('#order_code').val();
  if(validateTransformProducts()){
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
  }else{
		swal('warning !', 'กรุณากำหนดสินค้าแปรสภาพให้ครบถ้วน', 'warning');
	}

}


$("#customer-code").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
      $('#customer-code').val(code);
			$("#customer").val(name);
		}else{
      $('#customer-code').val('');
			$("#customerCode").val('');
			$('#customer').val('');
		}
	}
});



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
      $('#customer-code').val(code);
			$("#customer").val(name);
		}else{
      $('#customer-code').val('');
			$("#customerCode").val('');
			$(this).val('');
		}
	}
});


$('#reference').autocomplete({
  source: HOME + 'get_so_and_job_title',
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
})


$('#so-code').autocomplete({
  source: HOME + 'get_so_and_job_title',
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


$('#customer').focusout(function(){
  var code = $(this).val();
  if(code.length == 0)
  {
    $('#customerCode').val('');
  }
});

$('#customerCode').focusout(function(){
  var code = $(this).val();
  if(code.length == 0)
  {
    $('#customer').val('');
  }
});


$("#zone").autocomplete({
	source: BASE_URL + 'auto_complete/get_transform_zone',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#zoneCode").val(code);
			$(this).val(name);
		}else{
			$("#zoneCode").val('');
			$(this).val('');
		}
	}
});


$("#empName").autocomplete({
	source: BASE_URL + 'auto_complete/get_user',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#empName").val(name);
		}else{
			$("#empName").val('');
		}
	}
});



function add() {

  let h = {
    "customer_code" : $('#customerCode').val(),
    "customer_name" : $('#customer').val(),
    "date_add" : $('#date').val(),
    "empName" : $('#empName').val(),
    "role" : $('#role').val(),
    "reference" : $('#reference').val(),
    "zone_code" : $('#zoneCode').val(),
    "zone_name" : $('#zone').val(),
    "warehouse_code" : $('#warehouse').val(),
    "remark" : $('#remark').val()
  }

  $('.h').removeClass('has-error');

  if( h.customer_code == "" || h.customer_name == "" ) {
    swal('กรุณาระบุลูกค้า');
    $('#customer-code').addClass('has-error');
    $('#customer').addClass('has-error');
    return false;
  }

  if(!isDate(h.date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    $('#date').addClass('has-error');
    return false;
  }

  if(h.empName == "")
  {
    swal('ชื่อผู้เบิกไม่ถูกต้อง');
    $('#empName').addClass('has-error');
    return false;
  }

  if(h.zone_code.length == 0 || h.zone_name.length == 0 )
  {
    swal('โซนแปรสภาพไม่ถูกต้อง');
    $('#zone').addClass('has-error');
    return false;
  }

  if(h.warehouse_code.length == 0){
    swal('กรุณาเลือกคลัง');
    $('#warehouse').addClass('has-error');
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
          window.location.href = HOME + 'edit_detail/'+ ds.code;
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
    error:function(xhr) {
      load_out();

      swal({
        title:'Error!',
        type:'error',
        text:xhr.responseText,
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
      url: BASE_URL + 'inventory/transform/load_so',
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



// JavaScript Document
function updateDetailTable(){
	var order_code = $("#order_code").val();
	$.ajax({
		url: HOME + 'get_detail_table/'+order_code,
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


function removeChecked() {

  if($('.chk:checked').length > 0) {

    let code = $('#order_code').val();
    let items = [];

    $('.chk:checked').each(function() {
      let id = $(this).val();
      items.push(id);
    });

    if(items.length > 0) {
      swal({
    		title: "คุณแน่ใจ ?",
    		text: "ต้องการลบ " + items.length + " รายการ หรือไม่ ?",
    		type: "warning",
    		showCancelButton: true,
    		confirmButtonColor: "#DD6B55",
    		confirmButtonText: 'ใช่, ฉันต้องการลบ',
    		cancelButtonText: 'ยกเลิก',
    		closeOnConfirm: false
    		}, function(){
    			$.ajax({
    				url: BASE_URL + 'orders/orders/remove_details/',
    				type:"POST",
            cache:"false",
            data:{
              'code' : code,
              'ids' : JSON.stringify(items)
            },
    				success: function(rs) {
    					var rs = $.trim(rs);
    					if( rs == 'success' ){
    						swal({
                  title: 'Deleted',
                  type: 'success',
                  timer: 1000
                });

                setTimeout(() => {
                  window.location.reload();
                }, 1200);
    					}
              else
              {
    						swal("Error !", rs , "error");
    					}
    				}
    			});
    	});
    }
  }
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
	autoFocus:true,
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');

    if(arr.length > 1) {
      $(this).val(arr[0]);

      setTimeout(() => {
        getItemGrid();
      }, 200)
    }
    else {
      $(this).val('');
    }
  }
});



$('#item-code').keyup(function(e){
	if(e.keyCode == 13){
		var code = $(this).val();
		if(code.length > 4){
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
	var limit = parseDefault(parseInt($('#stock-qty').val()), 0);
	var itemCode = $('#item-code').val();
  var data = [{'code':itemCode, 'qty' : qty}];

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
  var customer_code = $('#customerCode').val();
  var customer_name = $('#customer').val();
  var date_add = $('#date').val();
  var empName = $('#empName').val();
  var role = $('#role').val();
  var zoneCode = $('#zoneCode').val();
  var zoneName = $('#zone').val();
  var warehouse_code = $('#warehouse').val();
  var remark = $('#remark').val().trim();
  var reqRemark = $('#require_remark').val() == 1 ? true : false;

  if(customer_code.length == 0 || customer_name.length == 0){
    swal('ชื่อผู้รับไม่ถูกต้อง');
    return false;
  }

  if(!isDate(date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    console.log('date error');
    return false;
  }

  if(empName.length == 0)
  {
    swal('ชื่อผู้เบิกไม่ถูกต้อง');
    return false;
  }

  if(zoneCode.length == 0 || zoneName.length == 0 )
  {
    swal('โซนแปรสภาพไม่ถูกต้อง');
    return false;
  }

  if(warehouse_code.length == 0){
    swal('กรุณาเลือกคลัง');
    return false;
  }

	//---- ตรวจสอบวันที่
	if( ! isDate(date_add) ){
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}

  if(reqRemark && remark.length < 10) {
    swal({
      title:'Required',
      text:'กรุณาระบุหมายเหตุอย่างน้อย 10 ตัวอักษร',
      type:'warning'
    });

    return false;
  }

  updateOrder();
}





function updateOrder(){
	var order_code = $("#order_code").val();
	var date_add = $("#date").val();
	var customer_code = $("#customerCode").val();
  var customer_name = $("#customer").val();
	var user_ref = $('#empName').val();
	var remark = $("#remark").val();
  var zoneCode = $('#zoneCode').val();
  var zoneName = $('#zone').val();
  var warehouse_code = $('#warehouse').val();
  var reference = $('#wq-ref').val();

	load_in();

	$.ajax({
		url:HOME + 'update_order',
		type:"POST",
		cache:"false",
		data:{
      "order_code" : order_code,
  		"date_add"	: date_add,
  		"customer_code" : customer_code,
      "reference" : reference,
      "user_ref" : user_ref,
      "zone_code" : zoneCode,
      "warehouse" : warehouse_code,
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
										})
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



function validateOrder(){
  var prefix = $('#prefix').val();
  var runNo = parseInt($('#runNo').val());
  let code = $('#code').val();
  if(code.length == 0){
    addOrder();
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
            addOrder();
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


function checkAll(el) {
  if(el.is(':checked')) {
    $('.chk').prop('checked', true);
  }
  else {
    $('.chk').prop('checked', false);
  }
}
