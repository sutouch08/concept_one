var HOME = BASE_URL + 'report/audit/down_payment_details/';

window.addEventListener('load', () => {
	resizeDisplay();
})

window.addEventListener('resize', () => {
	resizeDisplay();
});

function resizeDisplay() {
	let height = $(window).height();
	let navHeight = 45;
  let headerRow = $('#header-row').height();
	let searchHeight = $('#search-row').height() + navHeight + headerRow;
	let pageContentHeight = height - (navHeight + 75);
	let billTableHeight = pageContentHeight - (searchHeight + 0);
	let minHeight = 300;

	billTableHeight = billTableHeight < minHeight ? minHeight : billTableHeight;

	$('.page-content').css('height', pageContentHeight + 'px');
	$('#result').css('height', billTableHeight + 'px');
}

//--- Date picker
$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#fromDate').datepicker('option','maxDate', sd);
  }
});

function clear_form() {
	$('.e').removeClass('has-error');
	$('.e').val('');
	$('#status').val('all');
}


function toggleHilight(el) {
	if(el.hasClass('hilight')) {
		el.removeClass('hilight');
	}
	else {
		el.addClass('hilight');
	}
}


function getReport() {
  $('.e').removeClass('has-error');

  let filter = {
    'fromDate' : $('#fromDate').val(),
    'toDate' : $('#toDate').val(),
    'code' : $.trim($('#code').val()),
		'customer_code' : $.trim($('#customer-code').val()),
		'customer_name' : $.trim($('#customer-name').val()),
		'phone' : $.trim($('#phone').val()),
		'reference' : $.trim($('#reference').val()),
    'status' : $('#status').val()
  }

  if( ! isDate(filter.fromDate) || ! isDate(filter.toDate)) {
    swal({
      title:'วันที่ไม่ถูกต้อง',
      text:'กรุณาระบุวันทึ่ให้ถูกต้อง',
      type:'error'
    });

    $('#fromDate').addClass('has-error');
    $('toDate').addClass('has-error');

    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'GET',
    cache:false,
    data: {
      "filter" : JSON.stringify(filter)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let source = $('#template').html();
          let output = $('#result');

          render(source, ds, output);
        }
        else {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error'
          });
        }
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}



function doExport(){
  $('.e').removeClass('has-error');

	let filter = {
    'fromDate' : $('#fromDate').val(),
    'toDate' : $('#toDate').val(),
    'code' : $('#code').val(),
		'customer_code' : $('#customer-code').val(),
		'customer_name' : $('#customer-name').val(),
		'phone' : $('#phone').val(),
		'reference' : $('#reference').val(),
    'status' : $('#status').val()
  }

  if( ! isDate(filter.fromDate) || ! isDate(filter.toDate)) {
    swal({
      title:'วันที่ไม่ถูกต้อง',
      text:'กรุณาระบุวันทึ่ให้ถูกต้อง',
      type:'error'
    });

    $('#fromDate').addClass('has-error');
    $('toDate').addClass('has-error');

    return false;
  }


  var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();
}
