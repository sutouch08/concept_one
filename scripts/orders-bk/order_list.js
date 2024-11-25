$(document).ready(function() {
	//---	reload ทุก 5 นาที
	setTimeout(function(){ goBack(); }, 300000);
});


function sendToWms(code) {
	load_in();
	$.ajax({
		url:BASE_URL + 'orders/orders/send_to_wms',
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
					timer:1000
				});
			}
			else {
				swal({
					title:"Error",
					text:rs,
					type:"error",
					html:true
				});
			}
		},
		error:function(xhr, status, error) {
			load_out();
			swal({
				title:'Error!!',
				type:'error',
				text:xhr.responseText,
				html:true
			})
		}
	})
}
