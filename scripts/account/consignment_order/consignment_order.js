var HOME = BASE_URL + 'account/consignment_order/';

function goBack(){
  window.location.href = HOME;
}


function goAdd(){
  window.location.href = HOME + 'add_new';
}


function viewDetail(code){
  window.location.href = HOME + 'view_detail/'+code;
}


function goEdit(code)
{
  window.location.href = HOME + 'edit/'+code;
}


//--- delete all data and cancle document
function getDelete(code){
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
    url: HOME + 'cancle/'+code,
    type:"POST",
    cache:"false",
    data:{
      "reason" : reason
    },
    success: function(rs) {
      var rs = $.trim(rs);
      if( rs == 'success' ) {
				setTimeout(function() {
					swal({
						title: 'Cancled',
						type: 'success',
						timer: 1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);
				}, 200);
			}
			else {
				setTimeout(function() {
					swal({
						title:"Error!",
						text:rs,
						type:'error'
					});
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



function doExport(){
  var code = $('#consign_code').val();
  load_in();
  $.ajax({
    url: HOME + 'export_consign/'+code,
    type:'POST',
    cache:'false',
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);
      }else{
        swal({
          title:'Error!',
          text:rs,
          type: 'error'
        })
      }
    }
  });
}


function sendToSAP(code){
  load_in();
  $.ajax({
    url: HOME + 'export_consign/'+code,
    type:'POST',
    cache:'false',
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'success',
          type:'success',
          timer:1000
        });
      }
    }
  });
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
function printConsignOrder(){
  var code = $('#consign_code').val();
	var center = ($(document).width() - 800) /2;
  var target = HOME + 'print_consign/'+ code;
  window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
}



function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){
    goBack();
  });
}
