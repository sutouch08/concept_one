var HOME = BASE_URL + 'masters/shop_pc/';

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

  let data = {
    'code' : code,
    'name' : name,
    'active' : active
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
  let id = $('#id').val();
	let code = $('#code').val();
	let name = $('#name').val();
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


  let data = {
    'id' : id,
    'code' : code,
    'name' : name,
    'active' : active
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
		$('#active').focus();
	}
})
