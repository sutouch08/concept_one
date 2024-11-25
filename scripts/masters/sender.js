var HOME = BASE_URL + 'masters/sender/';

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



function save() {
	var code = $('#code').val();
	var name = $('#name').val();
	var adr1 = $('#address1').val();
	var adr2 = $('#address2').val();
	var phone = $('#phone').val();
	var open = $('#open').val();
	var close = $('#close').val();
	var type = $('#type').val();
	var inlist = $('#in_list').is(':checked') ? 1 : 0;
	var force_tracking = $('#force_tracking').is(':checked') ? 1 : 0;
	var auto_gen = $('#auto_gen').is(':checked') ? 1 : 0;
	var prefix = $('#tracking_prefix').val();

	if(code.length === 0) {
		swal("กรุณากำหนดรหัส");
		return false;
	}

	if(name.length === 0) {
		swal("กรุณากำหนดชื่อ");
		return false;
	}

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
			'open' : open,
			'close' : close,
			'type' : type,
			'show_in_list' : inlist,
			'force_tracking' : force_tracking,
			'auto_gen' : auto_gen,
			'prefix' : prefix
		},
		success:function(rs) {
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
				})
			}
		},
		error:function(xhr, status, error) {
			swal({
				title: 'Error',
				text:xhr.responseText,
				type:'error',
				html:true
			});
		}
	})
}



function update() {
	var id = $('#id').val();
	var code = $('#code').val();
	var name = $('#name').val();
	var adr1 = $('#address1').val();
	var adr2 = $('#address2').val();
	var phone = $('#phone').val();
	var open = $('#open').val();
	var close = $('#close').val();
	var type = $('#type').val();
	var inlist = $('#in_list').is(':checked') ? 1 : 0;
	var force_tracking = $('#force_tracking').is(':checked') ? 1 : 0;
	var auto_gen = $('#auto_gen').is(':checked') ? 1 : 0;
	var prefix = $('#tracking_prefix').val();

	if(code.length === 0) {
		swal("กรุณากำหนดรหัส");
		return false;
	}

	if(name.length === 0) {
		swal("กรุณากำหนดชื่อ");
		return false;
	}

	$.ajax({
		url:HOME + 'update/'+id,
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'address1' : adr1,
			'address2' : adr2,
			'phone' : phone,
			'open' : open,
			'close' : close,
			'type' : type,
			'show_in_list' : inlist,
			'force_tracking' : force_tracking,
			'auto_gen' : auto_gen,
			'prefix' : prefix
		},
		success:function(rs) {
			var rs = $.trim(rs);
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
					type:'error',
					html:true
				})
			}
		},
		error:function(xhr, status, error) {
			swal({
				title: 'Error',
				text:xhr.responseText,
				type:'error',
				html:true
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
    window.location.href = HOME +'delete/' + id;
  })
}

function toggleAutoGen() {
	var el = $('#force_tracking');

	if(el.is(':checked')) {
		$('#gen_potion').removeClass('hide');
	}
	else {
		$('#gen_potion').addClass('hide');
		$('#prefix').addClass('hide');
	}

	$('#auto_gen').prop('checked', false);
}


function togglePrefix() {
	var el = $('#auto_gen');
	if(el.is(':checked')) {
		$('#prefix').removeClass('hide');
	}
	else {
		$('#prefix').addClass('hide');
	}

}
