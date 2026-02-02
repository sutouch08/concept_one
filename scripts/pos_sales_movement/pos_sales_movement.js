var HOME = BASE_URL + 'orders/pos_sales_movement/';

window.addEventListener('load', () => {
	resizeDisplay();
})

window.addEventListener('resize', () => {
	resizeDisplay();
});

function resizeDisplay() {
	let height = $(window).height();
  let title = $('#page-title').height();
	let footerHeight = $('.footer-content').outerHeight();
	let filterHeight = $('#search-row').height();
	let hr = 20;
	let pagination = $('#pagination').height();
	let pageContentHeight = height - (45 + footerHeight);
	let tableHeight = pageContentHeight - (title + footerHeight + filterHeight + hr + pagination); //-- 155

  $('.page-content').css('padding-bottom', '0px');
	$('.page-content').css('height', pageContentHeight + 'px');
	$('#item-div').css('height', tableHeight + 'px');
}

function goBack() {
  window.location.href = HOME;
}

function getSearch() {
  $('#searchForm').submit();
}


function clearFilter() {
  $.get(HOME + 'clear_filter', function() {
    goBack();
  });
}


$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#fromDate').datepicker('option', 'minDate', sd)
  }
});

$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#tromDate').datepicker('option', 'maxDate', sd)
  }
});


function exportFilter() {
	let token = generateUID();
	let h = {
		'code' : $('#code').val().trim(),
		'round_code' : $('#round_code').val().trim(),
		'shop_id' : $('#shop_id').val(),
		'pos_id' : $('#pos_id').val(),
		'type' : $('#type').val(),
		'role' : $('#role').val(),
		'bank' : $('#bank').val(),
		'from_date' : $('#fromDate').val(),
		'to_date' : $('#toDate').val()
	}

	if(h.from_date == "" || h.to_date == "") {
		swal("กรุณาระบุวันที่");
		return false;
	}

	$('#data').val(JSON.stringify(h));
	$('#token').val(token);

	get_download(token);
	$('#exportForm').submit();
}
