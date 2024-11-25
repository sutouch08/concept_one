var HOME = BASE_URL + 'inventory/temp_transfer/';

function goBack(){
  window.location.href = HOME;
}



function getSearch(){
  $("#searchForm").submit();
}



function clearFilter(){
  $.get(HOME + 'clear_filter', function(){ goBack(); });
}


$(".search").keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


$("#fromDate").datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $("#toDate").datepicker("option", "minDate", sd);
  }
});


$("#toDate").datepicker({
  dateFormat: 'dd-mm-yy',
  onClose:function(sd){
    $("#fromDate").datepicker("option", "maxDate", sd);
  }
});


function get_detail(code)
{
  //--- properties for print
  var prop 			= "width=1100, height=900. left="+center+", scrollbars=yes";
  var center 	= ($(document).width() - 1100)/2;
	var target 	= HOME + 'get_detail/'+code+'?nomenu';
	window.open(target, "_blank", prop );
}

function get_delete(docEntry, code) {
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: HOME + 'delete',
				type:"POST",
				cache:"false",
				data:{
					"docEntry" : docEntry
				},
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
							title: 'Deleted',
							type: 'success',
							timer: 1000
						});

						setTimeout(function(){
							window.location.reload();
						}, 1200);

					}else{
						swal("Error !", rs, "error");
					}
				}
			});
	});
}
