$('#date_add').datepicker({
  dateFormat:'dd-mm-yy'
});


function send_to_sap(){
  var code = $('#code').val();
  $.ajax({
    url:HOME + 'manual_export',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(rs === 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}


function saveAdjust(){
  var code = $('#code').val();
  var reference = $('#transform_code').val();
  var items = [];
  var count = $('.input-qty').length;
	var err = 0;
  if(count)
  {
    $('.input-qty').each(function(){
			let no = $(this).data('no');
      let pdCode = $(this).data('product');
      let qty = parseDefault(parseInt($(this).val()), 0);
			let limit = parseDefault(parseInt($('#limit-'+no).val()), 0);

			if(qty > limit || qty == 0) {
				$(this).addClass('has-error');
				err++;
			}
			else {
				$(this).removeClass('has-error');
			}

      items.push({"product_code" : pdCode, "qty" : qty});
      count--;

      if(count === 0)
      {
				if(err == 0) {
					load_in();
	        $.ajax({
	          url:HOME + 'save',
	          type:'POST',
	          cache:false,
	          data:{
	            "code" : code,
	            "transform_code" : reference,
	            "items" : JSON.stringify(items)
	          },
	          success:function(rs){
	            load_out();
	            var rs = $.trim(rs);
	            if(rs === 'success'){
	              swal({
	                title:"Success",
	                type:"success",
	                timer:1000
	              });

	              setTimeout(function(){
	                goDetail(code);
	              }, 1500);
	            }
	          }
	        });
				}
				else {
					return false;
				}
      }

    });
  }
}



function getEdit(){
  $('#date_add').removeAttr('disabled');
  $('#zone').removeAttr('disabled');
  $('#remark').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}



function updateHeader(){
  let code = $('#code').val();
  let date_add = $('#date_add').val();
  let zone_code = $('#zone_code').val();
  let remark = $('#remark').val();

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'date_add' : date_add,
      'zone_code' : zone_code,
      'remark' : remark
    },
    success:function(rs){
      if(rs == 'success'){
        swal({
          title:'Updated',
          text:'ปรับปรุงข้อมูลเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        $('#date_add').attr('disabled', 'disabled');
        $('#zone').attr('disabled', 'disabled');
        $('#remark').attr('disabled', 'disabled');
        $('#btn-edit').removeClass('hide');
        $('#btn-update').addClass('hide');
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}



function add(){
  var date_add = $('#date_add').val();
  var zone_code = $('#zone_code').val();
  var remark = $('#remark').val();

  if(!isDate(date_add)){
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if(zone_code.length === 0){
    swal("กรุณาระบุโซน");
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'date_add' : date_add,
      'zone_code' : zone_code,
      'remark' : remark
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      var arr = rs.split('|');
      if(arr.length === 2){
        goEdit(arr[1]);
      }else{
        swal({
          title:'Error!',
          text: rs,
          type:'error'
        });
      }
    }
  });
}



//--- ลบรายการ 1 บรรทัด
function removeRow(row, pdCode){
  $('#'+row).remove();
  reIndex();
  recal();
}



function recal()
{
  var in_zone = 0;
  var total = 0;
  $('.in-zone').each(function(){
    in_zone += parseInt(removeCommas($(this).text()));
  });

  $('.input-qty').each(function(){
    total += parseInt($(this).val());
  });

  $('#total-in-zone').text(addCommas(in_zone));
  $('#total-qty').text(addCommas(total));
}





$('#zone').autocomplete({
  source: BASE_URL + 'auto_complete/get_transform_zone',
  autoFocus:true,
  close:function(){
    let rs = $(this).val();
    let arr = rs.split(' | ');
    if(arr.length == 2){
      let code = arr[0];
      let name = arr[1];
      $('#zone').val(code);
      $('#zoneName').val(name);
      $('#zone_code').val(code);
    }else{
      $('#zone').val('');
      $('#zone_code').val('');
      $('#zoneName').val();
    }
  }
})


$('#reference').autocomplete({
  source: HOME + 'get_closed_transform_order',
  autoFocus:true,
  close:function(){
    $('#transform_code').val($(this).val());
  }
});



function load_reference()
{
  let reference = $('#reference').val();
  let code = $('#code').val();
  if(reference.length > 0){
    load_in();
    $.ajax({
      url:HOME + 'get_billed_details',
      type:'GET',
      cache:false,
      data:{
        'code' : code,
        'reference' : reference
      },
      success:function(rs){
        load_out();
        rs = $.trim(rs);
        if(isJson(rs)){
          var data = $.parseJSON(rs);
          var source = $('#detail-template').html();
          var output = $('#detail-table');

          render(source, data, output);
        }
      }
    })
  }
}



function validateOrder(){
  var prefix = $('#prefix').val();
  var runNo = parseInt($('#runNo').val());
  let code = $('#code').val();

  if(code.length == 0){
    add();
  }
  else
  {
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
          url: HOME + 'is_exists/'+code,
          type:'GET',
          cache:false,
          success:function(rs){
            if(rs == 'not_exists'){
              add();
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
}



$('#code').keyup(function(e){
	if(e.keyCode == 13){
		validateOrder();
	}
});
