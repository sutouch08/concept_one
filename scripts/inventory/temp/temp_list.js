var HOME = BASE_URL + 'inventory/temp_delivery_order/';

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


function get_detail(id)
{
  //--- properties for print
  var prop 			= "width=1100, height=900. left="+center+", scrollbars=yes";
  var center 	= ($(document).width() - 1100)/2;
	var target 	= HOME + 'get_detail/'+id+'?nomenu';
	window.open(target, "_blank", prop );
}



function getInvoice(code)
{
  //--- properties for print
  var prop 			= "width=1100, height=900. left="+center+", scrollbars=yes";
  var center 	= ($(document).width() - 1100)/2;
	var target 	= BASE_URL + 'inventory/invoice/view_detail/'+code+'?nomenu';
	window.open(target, "_blank", prop );
}

function getPosInvoice(code)
{
  //--- properties for print
  var prop 			= "width=1100, height=900. left="+center+", scrollbars=yes";
  var center 	= ($(document).width() - 1100)/2;
	var target 	= BASE_URL + 'inventory/invoice/view_detail/'+code+'?nomenu';
	window.open(target, "_blank", prop );
}


function getConsign(code)
{
  //--- properties for print
  var prop 			= "width=1100, height=900. left="+center+", scrollbars=yes";
  var center 	= ($(document).width() - 1100)/2;
	var target 	= BASE_URL + 'account/consign_order/view_detail/'+code+'?nomenu';
	window.open(target, "_blank", prop );
}


function export_diff()
{
  var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();
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
