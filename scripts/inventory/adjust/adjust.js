var HOME = BASE_URL + 'inventory/adjust/';
//--- กลับหน้าหลัก
function goBack(){
  window.location.href = HOME;
}


function goAdd(){
  window.location.href = HOME + 'add_new';
}


function goEdit(code){
  window.location.href = HOME + 'edit/'+code;
}

//--- ไปหน้ารายละเอียดออเดอร์
function goDetail(code){
  window.location.href = HOME + 'view_detail/'+ code;
}


function approve(){
	var code = $('#code').val();
  load_in();
	$.ajax({
		url:HOME + 'do_approve',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs){
      load_out();
			var rs = $.trim(rs);
			if(rs === 'success'){
				swal({
					title:'Approved',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					window.location.reload();
				}, 1200);
			}
			else{
				swal("Error!!", rs, 'error');
			}
		}
	})
}


function unapprove(){
	var code = $('#code').val();
	$.ajax({
		url:HOME + 'un_approve',
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

				setTimeout(function(){
					window.location.reload();
				}, 1200);
			}
			else{
				swal("Error!!", rs, 'error');
			}
		}
	})
}
