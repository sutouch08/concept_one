var HOME = BASE_URL + 'orders/order_invoice/';

window.addEventListener('load', () => {
	resizeDisplay();
})

window.addEventListener('resize', () => {
	resizeDisplay();
});

function resizeDisplay() {
	let height = $(window).height();
	let navHeight = 45;
	let searchHeight = $('#search-row').height() + navHeight + 25;
	let pageContentHeight = height - (53 + 70); //(navHeight + searchHeight + 50);
	let billTableHeight = pageContentHeight - (searchHeight + 65); //(195); //-- 155
	let minHeight = 500;

	billTableHeight = billTableHeight < minHeight ? minHeight : billTableHeight;

	$('.page-content').css('height', pageContentHeight + 'px');
	$('#bill-div').css('height', billTableHeight + 'px');
}

function goBack() {
  window.location.href = HOME;
}

function addNew() {
  window.location.href = HOME + 'add_new';
}

function getSearch() {
  $('#searchForm').submit();
}


function clearFilter() {
  $.get(HOME + 'clear_filter', function() {
    goBack();
  })
}

function viewDetail(id) {
  window.location.href = HOME + 'view_detail/'+id;
}

$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
})


function getCancel(id, code) {
  swal({
    title:'คุณแน่ใจ ?',
    text:'ต้องการยกเลิกเอกสาร ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    confirmButtonText:'Yes',
    cancelButtonText:'No',
    confirmButtonColor:'#d15b47',
    closeOnConfirm:true
  }, function() {
    $('#cancle-id').val(id);
    $('#cancle-code').val(code);

    $('#cancle-modal').on('shown.bs.modal', () => {
      $('#cancle-reason').focus();
    });

    $('#cancle-modal').modal('show');
  });
}


function doCancle() {
  let id = $('#cancle-id').val();
  let reason = $.trim($('#cancle-reason').val());

  if(reason.length < 10 ) {
    $('#cancle-error').text('กรุณาระบุเหตุผลอย่างน้อย 10 ตัวอักษร');
    $('#cancle-reason').addClass('has-error');
    $('#cancle-error').removeClass('hide');
    return false;
  }
  else {
    $('#cancle-reason').removeClass('has-error');
    $('#cancle-error').addClass('hide');
  }

  $('#cancle-modal').modal('hide');

  load_in();

  setTimeout(() => {
    $.ajax({
      url:HOME + 'cancel_invoice',
      type:'POST',
      cache:false,
      data:{
        'id' : id,
        'reason' : reason
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              window.location.reload();
            }, 1200);
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
    })
  }, 200);
}


function sendToSap(code) {
  load_in();

  $.ajax({
    url: HOME + 'send_to_sap/' + code,
    type:'POST',
    cache:false,
    success:function(rs) {
      load_out();

      if(rs == 'success') {
        swal({
          title:'Success',
          text:'Export success',
          type:'success',
          timer:1000
        });
      }
      else {
        swal({
          title:'Error',
          text:rs,
          type:'error'
        });
      }
    }
  })
}
