function goCancle(code){
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
      cancle(code);
	});
}

function cancle(code)
{
	var reason = $.trim($('#cancle-reason').val());

	if(reason.length < 10)
	{
		$('#cancle-modal').modal('show');
		return false;
	}

  load_in();

  $.ajax({
    url:HOME + 'cancle/'+code,
    type:"POST",
    cache:"false",
    data:{
      "reason" : reason
    },
    success: function(rs) {
      load_out();
      var rs = $.trim(rs);
      if( rs == 'success' ) {
        setTimeout(() => {
          swal({
            title:'Success',
            text: 'ยกเลิกเอกสารเรียบร้อยแล้ว',
            type: 'success',
            timer: 1000
          });

          setTimeout(function(){
            goBack();
          }, 1200);
        }, 200);

      }
      else {
        setTimeout(() => {
          swal("ข้อผิดพลาด", rs, "error");
        }, 200);
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

	return cancle(code);
}



$('#cancle-modal').on('shown.bs.modal', function() {
	$('#cancle-reason').focus();
});


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



$(document).ready(function() {
	//---	reload ทุก 5 นาที
	setTimeout(function(){ goBack(); }, 300000);
});
