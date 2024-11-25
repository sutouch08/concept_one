function holdBill() {

	if( $('.sell-item').length > 0) {
		swal({
			title:'พักบิล',
			text:'ต้องการพักบิลใช่หรือไม่ ?',
			type:'info',
			showCancelButton:true,
			confirmButtonColor:'#03A9F4',
			confirmButtonText:'ใช่',
			cancelButtonText:'ไม่ใช่',
			closeOnconfirm:true
		},
		function() {
			let temp_id = $('#order-temp-id').val();
			let pos_id = $('#pos_id').val();

			load_in();
			$.ajax({
				url:HOME + 'hold_bill',
				type:'POST',
				cache:false,
				data:{
					'temp_id' : temp_id,
					'pos_id' : pos_id
				},
				success:function(rs) {
					load_out();

					if(rs === 'success') {
						setTimeout(() => {
							swal({
								title:'Success',
								type:'success',
								timer:800
							});
						},200);

						setTimeout(() => {
							window.location.reload();
						}, 1200);
					}
					else {
						swal({
							title:'Error!',
							text:rs,
							type:'error'
						});
					}
				},
				error:function(xhr) {
					load_out();
					setTimeout(() => {
						swal({
							title:'Error!',
							text:xhr.responseText,
							type:'error',
							html:true
						});
					}, 200)
				}
			}) // end ajax
		})//--- end swal
	}
} //--- end function


function showHoldBill() {
	let pos_id = $('#pos_id').val();

	$.ajax({
		url:HOME + 'get_hold_bills/'+pos_id,
		type:'GET',
		cache:false,
		success:function(rs) {
			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.count > 0)
				{
					let source = $('#list-template').html();
					let output = $('#hold-list');
					render(source, ds.data, output);
					$('#holdListModal').modal('show');
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


function unHoldBill(temp_id, pos_id) {
	window.location.href = HOME + 'edit_temp/'+pos_id+'/'+temp_id;
}
