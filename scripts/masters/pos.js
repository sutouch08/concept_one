var HOME = BASE_URL + 'masters/pos/';

function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}


function getEdit(code){
  window.location.href = HOME + 'edit/'+code;
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
	let prefix = $('#prefix').val();
  let running = $('#running').val();
  let return_prefix = $('#return-prefix').val();
  let return_running = $('#return-running').val();
	let pos_no = $('#pos_no').val();
	let shop_id = $('#shop').val();
	let active = $('#active').val();

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

	if(prefix.length === 0 ) {
		$('#prefix').addClass('has-error')
		return false;
	}
	else {
		$('#prefix').removeClass('has-error');
	}

  if(return_prefix.length === 0 ) {
		$('#return-prefix').addClass('has-error')
		return false;
	}
	else {
		$('#return-prefix').removeClass('has-error');
	}

	if(shop_id === "") {
		$('#shop').addClass('has-error')
		return false;
	}
	else {
		$('#shop').removeClass('has-error');
	}


	load_in();
	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'prefix' : prefix,
      'running' : running,
      'return_prefix' : return_prefix,
      'return_running' : return_running,
			'pos_no' : pos_no,
			'shop_id' : shop_id,
			'active' : active
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
	let code = $('#code').val();
	let name = $('#name').val();
  let prefix = $('#prefix').val();
  let running = $('#running').val();
  let return_prefix = $('#return-prefix').val();
  let return_running = $('#return-running').val();
	let pos_no = $('#pos_no').val();
	let shop_id = $('#shop').val();
	let active = $('#active').val();

	if(name.length === 0) {
		$('#name').addClass('has-error');
		return false;
	}
	else {
		$('#name').removeClass('has-error');
	}


	if(prefix.length === 0 ) {
		$('#prefix').addClass('has-error')
		return false;
	}
	else {
		$('#prefix').removeClass('has-error');
	}

  if(return_prefix.length === 0 ) {
		$('#return-prefix').addClass('has-error')
		return false;
	}
	else {
		$('#return-prefix').removeClass('has-error');
	}

	if(shop_id === "") {
		$('#shop').addClass('has-error')
		return false;
	}
	else {
		$('#shop').removeClass('has-error');
	}

	load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
      'prefix' : prefix,
      'running' : running,
      'return_prefix' : return_prefix,
      'return_running' : return_running,
			'pos_no' : pos_no,
			'shop_id' : shop_id,
			'active' : active
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


function getDelete(code, name, no){
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
				'code' : code
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
						$('#row-'+no).remove();
						reIndex();
					}, 1200);
				} else {
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
		$('#prefix').focus();
	}
})

$('#prefix').keyup(function(e){
	if(e.keyCode === 13){
		$('#pos_no').focus();
	}
})

$('#pos_no').keyup(function(e){
	if(e.keyCode === 13){
		$('#shop').focus();
	}
})

$('input[type=text]').focus(function(){
	$(this).select();
});


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


function getDeviceByDeviceId() {

  deviceId = getDeviceId();

  $.ajax({
    url:HOME + 'get_pos_by_device_id',
    type:'GET',
    cache:false,
    data:{
      'deviceId' : deviceid
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        if(ds.status == 'success') {
          goToPOS(ds.id);
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
  })
}


function warningRegister(id, name) {
  swal({
    title:'Register POS Mechane',
    text:`ระบบจะทำการตั้งค่าคอมพิวเตอร์เครื่องนี้กับ ${name} ต้องการดำเนินการหรือไม่ ?`,
    type:'info',
    showCancelButton:true,
    confirmButtonColor: '#6fb3e0',
    cancelButtonColor:'#428bca',
    confirmButtonText:'Yes',
    cancelButtonText:'No',
    closeOnConfirm:true
  }, function() {

    load_in();
    deviceId = getDeviceId();

    setTimeout(() => {
      $.ajax({
        url:BASE_URL + 'masters/pos/get_avalible_pos_by_id',
        type:'GET',
        cache:false,
        data:{
          'id' : id,
          'deviceId' : deviceId
        },
        success:function(rs) {
          load_out();

          if(isJson(rs)) {
            let ds = JSON.parse(rs);

            if(ds.status == 'success') {
              let source = $('#pos-data-template').html();
              let output = $('#pos-data-table');
              ds.data.deviceId = deviceId;
              $('#pos-data').val(JSON.stringify(ds.data));

              render(source, ds.data, output);

              $('#pos-data-modal').modal('show');
            }
            else {
              swal({
                title:'Error !',
                text:ds.message,
                type:'error'
              })
            }
          }
          else {
            swal({
              title:'Error !',
              text:rs,
              type:'error'
            })
          }
        }
      })

    }, 500);
  })
}


function doRegister() {
  closeModal('pos-data-modal');

  setTimeout(() => {
    let data = $('#pos-data').val();

    if(data.length == 0) {
      swal({
        title:'Error!',
        text:'ไม่พบข้อมูลเครื่อง POS กรุณาติดต่อผู้ดูแลระบบ',
        type:'error'
      });

      return false;
    }

    pos = JSON.parse(data);

    if(pos.id) {
      $.ajax({
        url:HOME + 'register_pos_id',
        type:'POST',
        cache:false,
        data:{
          'id' : pos.id,
          'deviceId' : pos.deviceId
        },
        success:function(rs) {
          if(rs == 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            localStorage.setItem('IXPOSDATA', data);
          }
          else {
            swal({
              title:'Error!',
              text:'Registration failed',
              type:'error'
            });
          }
        },
        error:function(xhr) {
          swal({
            title:'Error!',
            text:xhr.responseText,
            type:'error',
            html:true
          });
        }
      })
    }
  },200);
}


function unRegister(id, name) {
  swal({
    title:'Unregistered POS Mechane',
    text:`ระบบจะทำการลบตั้งค่าคอมพิวเตอร์เครื่องนี้กับ ${name} ต้องการดำเนินการหรือไม่ ?`,
    type:'warning',
    showCancelButton:true,
    confirmButtonColor: '#6fb3e0',
    cancelButtonColor:'#428bca',
    confirmButtonText:'Yes',
    cancelButtonText:'No',
    closeOnConfirm:true
  }, function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'un_register_pos_id',
        type:'POST',
        cache:false,
        data: {
          'id' : id
        },
        success:function(rs) {
          load_out();

          if(rs === 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            localStorage.removeItem('IXPOSDATA');
            
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
        },
        error:function(xhr) {
          swal({
            title:'Error!',
            text:xhr.responseText,
            type:'error',
            html:true
          });
        }
      })
    }, 200);
  });
}
