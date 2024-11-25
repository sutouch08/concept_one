var HOME = BASE_URL + 'report/sales/sales_by_document/';


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
	let pageContentHeight = height - (53 + 70); //(navHeight + searchHeight + 50);
	let billTableHeight = pageContentHeight - (searchHeight + 65); //(195); //-- 155
	let minHeight = 500;

	billTableHeight = billTableHeight < minHeight ? minHeight : billTableHeight;

	$('.page-content').css('height', pageContentHeight + 'px');
	$('#report-div').css('height', billTableHeight + 'px');
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


function toggleAllSale(option) {
  $('#all-sale').val(option);

  if(option == 1) {
    $('#btn-sale-all').addClass('btn-primary');
    $('#btn-sale-select').removeClass('btn-primary');
    $('#saleModal').modal('hide');
  }

  if(option == 0) {
    $('#btn-sale-select').addClass('btn-primary');
    $('#btn-sale-all').removeClass('btn-primary');
    $('#saleModal').modal('show');
  }
}


function getReport() {
  let option = {
    'bookcode' : $('#bookcode').val(),
    'fromDate' : $('#fromDate').val(),
    'toDate' : $('#toDate').val(),
    'dateType' : $('#date-type-s').is(':checked') ? 'S' : 'D',
    'allSale' : $('#all-sale').val(),
    'saleList' : []
  }

  if(option.allSale == 0) {

    $('.sale-chk').each(function() {
      if($(this).is(':checked')) {
        option.saleList.push($(this).val());
      }
    })
  }

  if(option.allSale == 0 && option.saleList.length == 0) {
    swal("กรุณาเลือกพนักงานขาย");
    $('#saleModal').modal('show');
    return false;
  }

  if( ! isDate(option.fromDate) ||  ! isDate(option.toDate)) {
    swal("กรุณาระบุวันที่");
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'POST',
    cache:false,
    data:{
      'option' : JSON.stringify(option)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        let source = $('#report-template').html();
        let output = $('#result');

        render(source, ds, output);
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        })
      }
    },
    error:function(rs) {
      load_out();
      swal({
        title:'Error!',
        text:rs.responseText,
        type:'error',
        html:true
      })
    }
  })
}

function doExport(){
  let option = {
    'bookcode' : $('#bookcode').val(),
    'fromDate' : $('#fromDate').val(),
    'toDate' : $('#toDate').val(),
    'dateType' : $('#date-type-s').is(':checked') ? 'S' : 'D',
    'allSale' : $('#all-sale').val(),
    'saleList' : []
  }

  if(option.allSale == 0) {

    $('.sale-chk').each(function() {
      if($(this).is(':checked')) {
        option.saleList.push($(this).val());
      }
    })
  }

  if(option.allSale == 0 && option.saleList.length == 0) {
    swal("กรุณาเลือกพนักงานขาย");
    $('#saleModal').modal('show');
    return false;
  }

  if( ! isDate(option.fromDate) ||  ! isDate(option.toDate)) {
    swal("กรุณาระบุวันที่");
    return false;
  }

  var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();

}
