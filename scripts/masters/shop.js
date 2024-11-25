var HOME = BASE_URL + 'masters/shop/';

function addNew() {
  window.location.href = HOME + 'add_new';
}



function goBack() {
  window.location.href = HOME;
}


function getEdit(id) {
  window.location.href = HOME + 'edit/'+id;
}

function viewDetail(id) {
  window.location.href = HOME + 'view_detail/'+id;
}


function clearFilter(){
  let url = HOME + 'clear_filter';
  $.get(url, function(rs){
    goBack();
  });
}


function getSearch() {
	$('#searchForm').submit();
}


$('.search-box').keyup(function(e){
	if(e.keyCode === 13){
		getSearch();
	}
});


function save() {
	let code = $('#code').val();
	let name = $('#name').val();
	let zone = $('#zone').val();
	let zoneCode = $('#zone_code').val();
	let customer = $('#customer').val();
	let customerCode = $('#customer_code').val();
  let channels = $('#channels').val();
  let cash = $('#cash').val();
  let transfer = $('#transfer').val();
  let card = $('#card').val();
  let bill_header_1 = $('#bill-header-1').val();
  let header_size_1 = $('#header-size-1').val();
	let bill_header_2 = $('#bill-header-2').val();
  let header_size_2 = $('#header-size-2').val();
	let bill_header_3 = $('#bill-header-3').val();
  let header_size_3 = $('#header-size-3').val();
	let bill_footer = $('#bill-footer').val();
  let footer_size = $('#footer-size').val();
  let font_size = $('#text-size').val();
  let header_align_1 = $('#header-align-1').val();
  let header_align_2 = $('#header-align-2').val();
  let header_align_3 = $('#header-align-3').val();
	let use_vat = $('#use_vat').val();
	let tax_id = $('#tax_id').val();
	let active = $('#active').val();
  let prefix = $('#prefix').val();
  let running = $('#running').val();
  let barcode = $('#barcode').val();

	if(code.length === 0) {
		$('#code').addClass('has-error');
		return false;
	}
	else {
		$('#code').removeClass('has-error');
	}

	if(name.length === 0) {
		$('#name').addClass('has-error');
		return false;
	}
	else {
		$('#name').removeClass('has-error');
	}


	if(zone.length === 0 || zoneCode.length === 0) {
		$('#zone').addClass('has-error')
		return false;
	}
	else {
		$('#zone').removeClass('has-error');
	}

	if(customer.length === 0 || customerCode.length === 0) {
		$('#customer').addClass('has-error')
		return false;
	}
	else {
		$('#customer').removeClass('has-error');
	}

  if(prefix.length === 0) {
		$('#prefix').addClass('has-error')
		return false;
	}
	else {
		$('#prefix').removeClass('has-error');
	}

  if(channels == '') {
    $('#channels').addClass('has-error')
		return false;
  }
  else {
    $('#channels').removeClass('has-error');
  }

  if(cash == '') {
    $('#cash').addClass('has-error')
		return false;
  }
  else {
    $('#cash').removeClass('has-error');
  }

	if(use_vat == 1 && tax_id.length == 0) {
		$('#tax_id').addClass('has-error');
		return false;
	}
	else {
		$('#tax_id').removeClass('has-error');
	}

  let data = {
    'code' : code,
    'name' : name,
    'zone_code' : zoneCode,
    'customer_code' : customerCode,
    'prefix' : prefix,
    'running' : running,
    'channels' : channels,
    'cash_payment' : cash,
    'transfer_payment' : transfer,
    'card_payment' : card,
    'bill_header_1' : bill_header_1,
    'bill_header_2' : bill_header_2,
    'bill_header_3' : bill_header_3,
    'header_size_1' : header_size_1,
    'header_size_2' : header_size_2,
    'header_size_3' : header_size_3,
    'header_align_1' : header_align_1,
    'header_align_2' : header_align_2,
    'header_align_3' : header_align_3,
    'bill_footer' : bill_footer,
    'footer_size' : footer_size,
    'font_size' : font_size,
    'use_vat' : use_vat,
    'tax_id' : tax_id,
    'active' : active,
    'barcode' : barcode
  };

	load_in();

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			"data" : JSON.stringify(data)
		},
		success:function(rs) {
			load_out();

			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					addNew();
				}, 1200);
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		},
		error:function(xhr, status, error){
			load_out();
			let errorMessage = xhr.status + ': '+xhr.statusText;
			swal({
				title:'Error!',
				text:'Error-'+errorMessage,
				type:'error'
			});
		}
	})

}


function update() {
  let id = $('#shop_id').val();
	let code = $('#code').val();
	let name = $('#name').val();
	let zone = $('#zone').val();
	let zoneCode = $('#zone_code').val();
	let customer = $('#customer').val();
	let customerCode = $('#customer_code').val();
  let channels = $('#channels').val();
  let cash = $('#cash').val();
  let transfer = $('#transfer').val();
  let card = $('#card').val();
	let bill_header_1 = $('#bill-header-1').val();
  let header_size_1 = $('#header-size-1').val();
	let bill_header_2 = $('#bill-header-2').val();
  let header_size_2 = $('#header-size-2').val();
	let bill_header_3 = $('#bill-header-3').val();
  let header_size_3 = $('#header-size-3').val();
  let header_align_1 = $('#header-align-1').val();
  let header_align_2 = $('#header-align-2').val();
  let header_align_3 = $('#header-align-3').val();
	let bill_footer = $('#bill-footer').val();
  let footer_size = $('#footer-size').val();
  let font_size = $('#text-size').val();
	let use_vat = $('#use_vat').val();
	let tax_id = $('#tax_id').val();
	let active = $('#active').val();
  let prefix = $('#prefix').val();
  let running = $('#running').val();
  let barcode = $('#barcode').val();


	if(name.length === 0) {
		$('#name').addClass('has-error');
		return false;
	}
	else {
		$('#name').removeClass('has-error');
	}


	if(zone.length === 0 || zoneCode.length === 0) {
		$('#zone').addClass('has-error')
		return false;
	}
	else {
		$('#zone').removeClass('has-error');
	}

	if(customer.length === 0 || customerCode.length === 0) {
		$('#customer').addClass('has-error')
		return false;
	}
	else {
		$('#customer').removeClass('has-error');
	}

  if(prefix.length === 0) {
		$('#prefix').addClass('has-error')
		return false;
	}
	else {
		$('#prefix').removeClass('has-error');
	}

  if(channels == '') {
    $('#channels').addClass('has-error')
		return false;
  }
  else {
    $('#channels').removeClass('has-error');
  }

  if(cash == '') {
    $('#cash').addClass('has-error')
		return false;
  }
  else {
    $('#cash').removeClass('has-error');
  }


	if(use_vat == 1 && tax_id.length == 0) {
		$('#tax_id').addClass('has-error');
		return false;
	}
	else {
		$('#tax_id').removeClass('has-error');
	}

  let data = {
    'id' : id,
    'code' : code,
    'name' : name,
    'zone_code' : zoneCode,
    'customer_code' : customerCode,
    'prefix' : prefix,
    'running' : running,
    'channels' : channels,
    'cash_payment' : cash,
    'transfer_payment' : transfer,
    'card_payment' : card,
    'bill_header_1' : bill_header_1,
    'bill_header_2' : bill_header_2,
    'bill_header_3' : bill_header_3,
    'header_size_1' : header_size_1,
    'header_size_2' : header_size_2,
    'header_size_3' : header_size_3,
    'header_align_1' : header_align_1,
    'header_align_2' : header_align_2,
    'header_align_3' : header_align_3,
    'bill_footer' : bill_footer,
    'footer_size' : footer_size,
    'font_size' : font_size,
    'use_vat' : use_vat,
    'tax_id' : tax_id,
    'active' : active,
    'barcode' : barcode
  };

	load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			"data" : JSON.stringify(data)
		},
		success:function(rs) {
			load_out();

			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		},
		error:function(xhr, status, error){
			load_out();
			let errorMessage = xhr.status + ': '+xhr.statusText;
			swal({
				title:'Error!',
				text:'Error-'+errorMessage,
				type:'error'
			});
		}
	})

}


function getDelete(id, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
			url:HOME + 'delete',
			type:'POST',
			cache:false,
			data:{
				'id' : id
			},
			success:function(rs) {

				if(rs === 'success') {
					swal({
						title:'Deleted',
						text:'ลบรายการเรียบร้อยแล้ว',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);
				}
        else {
					swal({
						title:'Error!',
						text: rs,
						type:'error'
					})
				}
			}
		})
  })
}


$('#code').keyup(function(e){
	if(e.keyCode === 13){
		$('#name').focus();
	}
})

$('#name').keyup(function(e){
	if(e.keyCode === 13){
		$('#zone').focus();
	}
})



$('#zone').autocomplete({
	source:HOME + 'get_zone_code_and_name',
	autoFocus:true,
	close:function() {
		let rs = $(this).val();
		let arr = rs.split(' | ');
		if(arr.length === 2) {
			$('#zone_code').val(arr[0]);
			$('#zone').val(arr[1]);
			$('#customer').focus();
		}
		else {
			$('#zone_code').val('');
			$('#zone').val('');
		}
	}
});


$('#customer').autocomplete({
	source:HOME + 'get_customer_code_and_name',
	autoFocus:true,
	close:function() {
		let rs = $(this).val()
		let arr = rs.split(' | ')
		if(arr.length == 2) {
			$('#customer_code').val(arr[0])
			$('#customer').val(arr[1])
		}
		else {
			$('#customer_code').val('')
			$('#customer').val('')
		}
	}
})


function toggleActive(option) {
	$('#active').val(option)

	if(option == 1) {
		$('#btn-active-yes').addClass('btn-success')
		$('#btn-active-no').removeClass('btn-danger')
		return
	}

	if(option == 0) {
		$('#btn-active-yes').removeClass('btn-success')
		$('#btn-active-no').addClass('btn-danger')
		return
	}
}


function toggleVat(option) {
	$('#use_vat').val(option);

	if(option == 1) {
		$('#btn-vat-yes').addClass('btn-success')
		$('#btn-vat-no').removeClass('btn-success')
		return
	}

	if(option == 0) {
		$('#btn-vat-yes').removeClass('btn-success')
		$('#btn-vat-no').addClass('btn-success')
		return
	}
}


$('#user-box').autocomplete({
	source:HOME + 'get_user_and_name',
	autoFocus:true,
	close:function() {
		let user = $(this).val();
		let arr = user.split(' | ');
		if(arr.length == 2) {
			$(this).val(arr[0]);
		}
		else {
			$(this).val('');
		}
	}
})


function add_user() {
	let shop_id = $('#shop_id').val();
	let uname = $.trim($('#user-box').val());
	if(uname.length > 0)
	{
		load_in();
		$.ajax({
			url:HOME + 'add_user',
			type:'POST',
			cache:false,
			data:{
				'shop_id' : shop_id,
				'uname' : uname
			},
			success:function(rs) {
				load_out();

				if(isJson(rs)) {
					let data = $.parseJSON(rs);
					let source = $('#user-template').html();
					let output = $('#user-table');
					render_append(source, data, output);
					reIndex();
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						$('#user-box').val('');
						$('#user-box').focus();
					},1100);


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
				load_out();
				let errorMessage = xhr.status +': '+ xhr.statusText;
				swal({
					title:'Error!',
					text:'Error-' + errorMessage,
					type:'error'
				});
			}
		})
	}
}


function removeUser(id, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
			url:HOME + 'remove_user',
			type:'POST',
			cache:false,
			data:{
				'id' : id
			},
			success:function(rs) {

				if(rs === 'success') {
					swal({
						title:'Deleted',
						text:'ลบรายการเรียบร้อยแล้ว',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						$('#row-'+id).remove();
						reIndex();
					}, 1200);

				} else {
					swal({
						title:'Error!',
						text: rs,
						type:'error'
					})
				}
			},
			error:function(xhr, status, error) {
				let errorMessage = xhr.status + ": "+xhr.statusText;
				swal({
					title:'Error!',
					text:'Error-'+ errorMessage,
					type:'error'
				})
			}
		})
  })
}


function addPayment() {
	let shop_id = $('#shop_id').val();
	let payment_code = $('#payments-list').val();
  console.log(payment_code);

	if(payment_code.length)
	{
		load_in();

		$.ajax({
			url: HOME + 'add_payment_method',
			type:'POST',
			cache:false,
			data:{
				'shop_id' : shop_id,
				'payment_code' : payment_code
			},
			success:function(rs) {
				load_out();

				if(isJson(rs)) {
					let ds = $.parseJSON(rs);

          if(ds.status == 'success') {
            let source = $('#payment-template').html();
            let output = $('#payment-table');
            render_append(source, ds.data, output);

            reIndex('no');

            $('#payment-list').val('');
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });
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
			},
			error:function(xhr, status, error) {
				load_out();
				let errorMessage = xhr.status +': '+ xhr.statusText;
				swal({
					title:'Error!',
					text:'Error-' + errorMessage,
					type:'error'
				});
			}
		})
	}
}


function removePayment(id, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
			url:HOME + 'remove_payment_method',
			type:'POST',
			cache:false,
			data:{
				'id' : id
			},
			success:function(rs) {

				if(rs === 'success') {
					swal({
						title:'Deleted',
						text:'ลบรายการเรียบร้อยแล้ว',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						$('#payment-row-'+id).remove();
						reIndex('p-no');
					}, 1200);

				} else {
					swal({
						title:'Error!',
						text: rs,
						type:'error'
					})
				}
			},
			error:function(xhr, status, error) {
				let errorMessage = xhr.status + ": "+xhr.statusText;
				swal({
					title:'Error!',
					text:'Error-'+ errorMessage,
					type:'error'
				})
			}
		})
  })
}
