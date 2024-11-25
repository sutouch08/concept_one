// JavaScript Document
var HOME = BASE_URL + 'inventory/receive_po/';

function goDelete(code){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิก '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: true
		}, function(){
			$('#cancle-code').val(code);
			$('#cancle-reason').val('').removeClass('has-error');

			cancle_received(code);
	});
}



function cancle_received(code)
{
	var reason = $.trim($('#cancle-reason').val());

	if(reason.length < 10)
	{
		$('#cancle-modal').modal('show');
		return false;
	}

	load_in();

	$.ajax({
		url: HOME + 'cancle_received',
		type:"POST",
		cache:"false",
		data:{
			"receive_code" : code,
			"reason" : reason
		},
		success: function(rs){
			load_out();

			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({
					title: 'Cancled',
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
}


function doCancle() {
	let code = $('#cancle-code').val();
	let reason = $.trim($('#cancle-reason').val());

	if( reason.length < 10) {
		$('#cancle-reason').addClass('has-error').focus();
		return false;
	}

	$('#cancle-modal').modal('hide');

	return cancle_received(code);
}



$('#cancle-modal').on('shown.bs.modal', function() {
	$('#cancle-reason').focus();
});



function addNew(){
  window.location.href = HOME + 'add_new';
}


function goEdit(code){
	window.location.href = HOME + 'edit/'+ code;
}


function viewDetail(code){
	window.location.href = HOME + 'view_detail/'+ code;
}


function goBack(){
	window.location.href = HOME;
}

function getSearch(){
	$("#searchForm").submit();
}


$(".search").keyup(function(e){
	if( e.keyCode == 13 ){
		getSearch();
	}
});



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



// JavaScript Document
function printReceived(){
	var code = $("#receive_code").val();
	var center = ($(document).width() - 800) /2;
  var target = HOME + 'print_detail/'+code;
  window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
}



function doExport(){
	var code = $('#receive_code').val();
	load_in();
	$.ajax({
		url: HOME + 'do_export/'+code,
		type:'POST',
		cache:false,
		success:function(rs){
			load_out();
			if(rs == 'success'){
				swal({
					title:'Success',
					text:'Send data successfully',
					type:'success',
					timer:1000
				});
			}else{
				swal({
					title:'Errow!',
					text: rs,
					type:'error'
				});
			}
		}
	})
}


function sendToSap(code){
	load_in();
	$.ajax({
		url: HOME + 'do_export/'+code,
		type:'POST',
		cache:false,
		success:function(rs){
			load_out();
			if(rs == 'success'){
				swal({
					title:'Success',
					text:'Send data successfully',
					type:'success',
					timer:1000
				});
			}else{
				swal({
					title:'Errow!',
					text: rs,
					type:'error'
				});
			}
		}
	})
}


function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){
    goBack();
  });
}
