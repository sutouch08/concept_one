var HOME = BASE_URL + 'masters/vat/';

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
	$.get(HOME + 'clear_filter', function(){
		goBack();
	});
}

function setAsDefault(code) {
	$.ajax({
		url:HOME + 'set_default',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			var rs = $.trim(rs);
			if(rs === 'success') {
				window.location.reload();
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


function toggleActive(option) {
	$('#active').val(option);

	if(option == 1) {
		$('#btn-on').addClass('btn-primary');
		$('#btn-off').removeClass('btn-danger');
		return;
	}

	if(option == 0) {
		$('#btn-on').removeClass('btn-primary');
		$('#btn-off').addClass('btn-danger');
		return;
	}
}


function getDelete(code, name){
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
		load_in();
		$.ajax({
			url:HOME + 'delete',
			type:'POST',
			cache:false,
			data:{
				'code' : code
			},
			success:function(rs) {
				load_out();
				var rs = $.trim(rs);
				if(rs === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:'1000'
					});

					setTimeout(function() {
						window.location.reload();
					}, 1200);
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
  });
}



function save() {
	var code = $('#code').val();
	var name = $('#name').val();
	var rate = parseDefault(parseFloat($('#rate').val()),0);
	var active = $('#active').val();

	if(code.length == 0) {
		$('#code').addClass('has-error');
		return false;
	}
	else {
		$('#code').removeClass('has-error');
	}

	if(name.length == 0) {
		$('#name').addClass('has-error');
		return false;
	}
	else {
		$('#name').removeClass('has-error');
	}

	if(rate < 0 || rate > 100) {
		$('#rate').addClass('has-error');
		set_error($('#rate'), $('#rate-error'), "อัตราภาษีต้องอยู่ในช่วง 0 - 100");
		return false;
	}
	else {
		clear_error($('#rate'), $('#rate-error'));
	}

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'rate' : rate,
			'active' : active
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
					title: "Error!",
					text:rs,
					type:'error'
				});
			}
		}
	})
}



function update() {
	var code = $('#code').val();
	var name = $('#name').val();
	var rate = parseDefault(parseFloat($('#rate').val()),0);
	var active = $('#active').val();
	var old_name = $('#old_name').val();

	if(code.length == 0) {
		$('#code').addClass('has-error');
		return false;
	}
	else {
		$('#code').removeClass('has-error');
	}

	if(name.length == 0) {
		$('#name').addClass('has-error');
		return false;
	}
	else {
		$('#name').removeClass('has-error');
	}

	if(rate < 0 || rate > 100) {
		$('#rate').addClass('has-error');
		set_error($('#rate'), $('#rate-error'), "อัตราภาษีต้องอยู่ในช่วง 0 - 100");
		return false;
	}
	else {
		clear_error($('#rate'), $('#rate-error'));
	}

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'name' : name,
			'rate' : rate,
			'active' : active,
			'old_name' : old_name
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
					title: "Error!",
					text:rs,
					type:'error'
				});
			}
		}
	})
}

$('.search-box').keyup(function(e){
	if(e.keyCode === 13){
		getSearch();
	}
})
function getSearch() {
	$('#searchForm').submit();
}
