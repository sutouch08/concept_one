var HOME = BASE_URL + 'masters/supplier/';

function getSearch(){
  $('#searchForm').submit();
}


$('.search-box').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


function clearFilter(){
  $.get(HOME + 'clear_filter', function(){
    goBack();
  });
}

function goBack(){
  window.location.href = HOME;
}

function addNew(){
  window.location.href = HOME + 'add_new';
}


function getEdit(id){
  window.location.href = HOME + 'edit/'+id;
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
				var rs = $.trim(rs);
				if(rs === 'success') {
					swal({
						title:'Deleted',
						type:'success',
						timer:1000
					});

					$('#row-'+id).remove();
					reIndex();
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
		});
  });
}


function save() {
	var code = $('#code').val();
	var name = $('#name').val();
	var adr1 = $('#address1').val();
	var adr2 = $('#address2').val();
	var phone = $('#phone').val();
	var active = $('#active').is(':checked') ? 1 : 0;

	if(code.length === 0) {
		swal("กรุณากำหนดรหัส");
		return false;
	}

	if(name.length === 0) {
		swal("กรุณากำหนดชื่อ");
		return false;
	}

	if(adr1.length === 0) {
		swal("กรุณาระบุที่อยู่");
		return false;
	}

	load_in();
	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'address1' : adr1,
			'address2' : adr2,
			'phone' : phone,
			'active' : active
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(function() {
					addNew();
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
		error:function(xhr, status, error) {
			load_out();
			swal({
				title:'Error!',
				text:xhr.responseText,
				type:'error',
				html:true
			});
		}
	});
}


function update()
{
	var id = $('#id_supplier').val();
	var name = $('#name').val();
	var adr1 = $('#address1').val();
	var adr2 = $('#address2').val();
	var phone = $('#phone').val();
	var active = $('#active').is(':checked') ? 1 : 0;

	if(name.length == 0) {
		swal("กรุณากำหนดชื่อ");
		return false;
	}

	if(adr1.length === 0) {
		swal("กรุณาระบุที่อยู่");
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'id' : id,
			'name' : name,
			'address1' : adr1,
			'address2' : adr2,
			'phone' : phone,
			'status' : active
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Updated',
					type:'success',
					timer:1000
				});
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
		error:function(xhr, status, error) {
			load_out();
			swal({
				title:'Error!',
				text:xhr.responseText,
				type:'error',
				html:true
			});
		}
	});

}
