var HOME = BASE_URL + 'report/sales/sales_by_employee/';

function toggleAllSlp(option){
  $('#allSlp').val(option);
  if(option == 1){
    $('#btn-slp-all').addClass('btn-primary');
    $('#btn-slp-selected').removeClass('btn-primary');
    return
  }

  if(option == 0){
    $('#btn-slp-all').removeClass('btn-primary');
    $('#btn-slp-selected').addClass('btn-primary');
    $('#slp-modal').modal('show');
  }
}


function toggleGroupBy(option){
  $('#groupBy').val(option);

  if(option == 'S'){
    $('#btn-group-sale').addClass('btn-primary');
    $('#btn-group-doc').removeClass('btn-primary');
    return
  }

  if(option == 'D'){
    $('#btn-group-sale').removeClass('btn-primary');
    $('#btn-group-doc').addClass('btn-primary');
  }
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


function getReport() {
  let option = {
    'bookcode' : $('#bookcode').val(),
    'fromDate' : $('#fromDate').val(),
    'toDate' : $('#toDate').val(),
    'dateType' : $('#date-type-s').is(':checked') ? 'S' : 'D',
    'allSlp' : $('#allSlp').val(),
    'slpList' : []
  }

  if(option.allSlp == 0) {

    $('.slp-chk').each(function() {
      if($(this).is(':checked')) {
        option.slpList.push($(this).val());
      }
    })
  }

  if(option.allSlp == 0 && option.slpList.length == 0) {
    swal("กรุณาเลือกพนักงานขาย");
    $('#slp-modal').modal('show');    
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
      'filter' : JSON.stringify(option)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        let source = $('#sale-template').html();
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

// function getReport() {
//   let fromDate = $('#fromDate').val();
//   let toDate = $('#toDate').val();
//   let allSlp = $('#allSlp').val();
//   let groupBy = $('#groupBy').val();
//   let slpCount = $('.slp-chk:checked').length;
//   let slpList = [];
//
//   if( ! isDate(fromDate) || ! isDate(toDate)) {
//     swal("กรุณาระบุวันที่ให้ถูกต้อง");
//     return false;
//   }
//
//   if(allSlp == 0 && slpCount == 0) {
//     swal("กรุณาระบุพนักงานขาย");
//     $('#slp-modal').modal('show');
//     return false;
//   }
//
//   if(allSlp == 0 && slpCount > 0) {
//     $('.slp-chk:checked').each(function() {
//       slpList.push($(this).val());
//       console.log($(this).val());
//     });
//   }
//
//
//   let data = {
//     'from_date' : fromDate,
//     'to_date' : toDate,
//     'allSlp' : allSlp,
//     'slpList' : slpList,
//     'groupBy' : groupBy
//   };
//
//   load_in();
//
//   $.ajax({
//     url:HOME + 'get_report',
//     type:'POST',
//     cache:false,
//     data:{
//       'filter' : JSON.stringify(data)
//     },
//     success:function(rs) {
//       load_out();
//
//       if( isJson(rs)) {
//         let ds = JSON.parse(rs);
//         let source = $('#sale-template').html();
//         let output = $('#result');
//
//         render(source, ds, output);
//       }
//       else {
//         swal({
//           title:'Error!',
//           text:rs,
//           type:'error'
//         });
//       }
//     }
//   })
//
// }


function doExport(){
  let fromDate = $('#fromDate').val();
  let toDate = $('#toDate').val();
  let allSlp = $('#allSlp').val();
  let groupBy = $('#groupBy').val();
  let slpCount = $('.slp-chk:checked').length;

  if( ! isDate(fromDate) || ! isDate(toDate)) {
    swal("กรุณาระบุวันที่ให้ถูกต้อง");
    return false;
  }

  if(allSlp == 0 && slpCount == 0) {
    swal("กรุณาระบุพนักงานขาย");
    $('#slp-modal').modal('show');
    return false;
  }

  var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();

}
