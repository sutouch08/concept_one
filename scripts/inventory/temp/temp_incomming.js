var HOME = BASE_URL + 'inventory/temp_incomming/';

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


function viewDetail(id)
{
  //--- properties for print
  var width = 500;
  var height = 700;
  var center 	= ($(document).width() - width)/2;
  var prop 	= "width="+width+", height="+height+", left="+center+", scrollbars=yes";
	var target 	= HOME + 'get_detail/'+id+'?nomenu';
	window.open(target, "_blank", prop );
}

function viewDpm(code) {
	if(code != "" && code != null && code.length > 9) {
		//--- properties for print
		var center    = ($(document).width() - 800)/2;
		var prop 			= "width=800, height=900, left="+center+", scrollbars=yes";
		var target = BASE_URL + 'orders/order_down_payment/view_detail/'+code+'?nomenu';
		window.open(target, "_blank", prop);
	}
}


function deleteTemp(docEntry, code, row_no)
{
	swal({
		title:'คุณแน่ใจ ?',
		text:'ต้องการลบ '+code+' หรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		comfirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
	}, function() {
		$.ajax({
			url:HOME + 'delete_temp/'+docEntry,
			type:'POST',
			cache:false,
			success:function(rs) {
				var rs = $.trim(rs);
				if(rs === 'success') {
					$('#row-'+row_no).remove();
					reIndex();
					swal({
						title:'Success',
						text:code + ' has been deleted',
						type:'success',
						timer:1000
					})
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			}
		})
	})
}
