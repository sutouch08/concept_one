var HOME = BASE_URL + 'inventory/invoice/';

function goBack(){
  window.location.href = HOME;
}


function viewDetail(code){
  window.location.href = HOME + 'view_detail/'+ code;
}


function createTaxInvoice(code) {
  swal({
    title:'Create Tax Invoice',
    text:'สร้างใบกำกับภาษีจากเอกสารนี้หรือไม่ ?',
    type:'info',
    showCancelButton:true,
    confirmButtonText:'สร้างใบกำกับภาษี',
    cancelButtonText:'ยกเลิก',
    confirmButtonColor:'#428bca',
    closeOnConfirm:true
  }, function() {
    load_in();

    $.ajax({
      url:HOME + 'create_invoice',
      type:'POST',
      cache:false,
      data:{
        'baseType' : 'WO',
        'code' : code,
        'taxStatus' : 'Y'
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {

          let ds = JSON.parse(rs);

          if(ds.status == 'success') {

          }
          else {
            setTimeout(() => {
              swal({
                title : 'Error!',
                text : ds.message,
                type : error
              })
            }, 200);
          }
        }
        else {
          setTimeout(() => {
            swal({
              title : 'Error!',
              text : rs,
              type : error
            })
          }, 200);
        }
      }
    })
  })


  if(isClicked == 0) {
    isClicked = 1;

    var data = [];

  	$('.chk').each(function() {
  		if($(this).is(':checked')) {
  			var no = $(this).data('no');
  			var order_code = $('#orderCode-'+no).val();
  			data.push(order_code);
  		}
  	});

  	if(data.length) {
  		load_in();
  		$.ajax({
  			url:BASE_URL + 'orders/order_invoice/create_each_order_invoice',
  			type:'POST',
  			cache:false,
  			data:{
  				'data' : data
  			},
  			success:function(rs) {
  				load_out();
  				var rs = $.trim(rs);
  				if(isJson(rs)) {
  					var ds = $.parseJSON(rs);
  					print_select_invoice(ds.gen_id, option);
  					window.location.reload();
  				}
  				else {
  					swal({
  						title:'Error!',
  						text:rs,
  						type:'error'
  					});

            isClicked = 0;
  				}
  			},
  			error:function(xhr, status, error) {
  				load_out();
  				swal({
  					title:'Error!',
  					text:'Error-'+xhr.status+' : '+xhr.statusText,
  					type:'error'
  				});

          isClicked = 0;
  			}
  		})
  	}
    else {
      isClicked = 0;
    }
  }

}

function doExport(){
  var code = $('#order_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'inventory/delivery_order/manual_export/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          text:'Export success',
          type:'success',
          timer:1000
        });
      }else{
        swal({
          title:'Error',
          text:rs,
          type:'error'
        });
      }
    }
  })
}


function do_export(code){
  $.ajax({
    url:BASE_URL + 'inventory/delivery_order/manual_export/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          text:'Export success',
          type:'success',
          timer:1000
        });
      }else{
        swal({
          title:'Error',
          text:rs,
          type:'error'
        });
      }
    }
  })
}


function do_export_update(code){
  $.ajax({
    url:BASE_URL + 'inventory/delivery_order/manual_export_update/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          text:'Export success',
          type:'success',
          timer:1000
        });
      }else{
        swal({
          title:'Error',
          text:rs,
          type:'error'
        });
      }
    }
  })
}
