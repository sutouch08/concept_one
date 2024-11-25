function goBack(){
  window.location.href = BASE_URL + 'inventory/pack';
}

function getSearch(){
  $('#searchForm').submit();
}


$('.search').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
})


function clearFilter(){
  $.get(BASE_URL + 'inventory/pack/clear_filter', function(){
    goBack();
  })
}


$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#toDate").datepicker("option", "minDate", ds);
	}
});



$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#fromDate").datepicker("option", "maxDate", ds);
	}
});


function deletePack(id, order, product) {
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ " + order + "/"+product+" หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: BASE_URL + 'inventory/pack/delete_row',
				type:"POST",
        cache:"false",
				data:{
					'id' : id
				},
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
							title: 'Deleted',
							type: 'success',
							timer: 1000 });

						$('#row-'+id).remove();
						reIndex();
					}else{
						swal("Error !", rs , "error");
					}
				}
			});
	});
}
