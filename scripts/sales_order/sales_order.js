var HOME = BASE_URL + 'orders/sales_order/';

function goBack() {
  window.location.href = HOME
}


function leave(){
	swal({
		title: 'ออกจากหน้านี้',
    text:'การเปลี่ยนแปลงจะไม่ถูกบันทึก ต้องการออกจากหน้านี้หรือไม่ ?',
		type: 'warning',
    html:true,
		showCancelButton: true,
		cancelButtonText: 'No',
		confirmButtonText: 'Yes',
		closeOnConfirm: false
	}, function(){
		goBack();
	});
}


function addNew() {
  window.location.href = HOME + 'add_new'
}


function edit(code) {
  window.location.href = HOME + 'edit/'+code;
}


function viewDetail(code) {
  window.location.href = HOME + 'view_detail/'+code;
}

function printOrder(code) {
	var center = ($(document).width() - 800) /2;
  var target = HOME + 'print_sales_order/'+code;
  window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
}

function getSearch() {
  $('#searchForm').submit()
}

function clearFilter() {
  $.get(HOME + 'clear_filter', function() { goBack() })
}


$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#toDate').datepicker('option', 'minDate', sd)
  }
})

$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#fromDate').datepicker('option', 'maxDate', sd)
  }
})

$('#dueFromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#dueToDate').datepicker('option', 'minDate', sd)
  }
})

$('#dueToDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#dueFromDate').datepicker('option', 'maxDate', sd)
  }
})

$('#date_add').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#due_date').datepicker('option', 'minDate', sd)
  }
});

$('#due_date').datepicker({
  dateFormat:'dd-mm-yy',
  minDate: new Date()
});


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

			cancle_order(code);
	});
}



function cancle_order(code)
{
	var reason = $.trim($('#cancle-reason').val());

	if(reason.length < 10)
	{
		$('#cancle-modal').modal('show');
		return false;
	}

	load_in();

	$.ajax({
		url: HOME + 'cancle_order',
		type:"POST",
		cache:"false",
		data:{
			"code" : code,
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

	return cancle_order(code);
}



$('#cancle-modal').on('shown.bs.modal', function() {
	$('#cancle-reason').focus();
});


function setColorbox()
{
	var colorbox_params = {
				rel: 'colorbox',
				reposition: true,
				scalePhotos: true,
				scrolling: false,
				previous: '<i class="fa fa-arrow-left"></i>',
				next: '<i class="fa fa-arrow-right"></i>',
				close: 'X',
				current: '{current} of {total}',
				maxWidth: '800px',
				maxHeight: '800px',
				opacity:0.5,
				speed: 500,
				onComplete: function(){
					$.colorbox.resize();
				}
		}

	$('[data-rel="colorbox"]').colorbox(colorbox_params);
}



function toggleState(state){
  var current = $('#state_'+state).val();
  if(current == 'Y'){
    $('#state_'+state).val('N');
    $('#btn-state-'+state).removeClass('btn-info');
  }else{
    $('#state_'+state).val('Y');
    $('#btn-state-'+state).addClass('btn-info');
  }

  getSearch();
}


function toggleOnlyMe(){
  var current = $('#onlyMe').val();
  if(current == ''){
    $('#onlyMe').val(1);
    $('#btn-only-me').addClass('btn-info');
  }else{
    $('#onlyMe').val('');
    $('#btn-only-me').removeClass('btn-info');
  }

  getSearch();
}
