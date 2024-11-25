var HOME = BASE_URL + 'orders/down_payment_invoice/';

window.addEventListener('load', () => {
	resizeDisplay();
})

window.addEventListener('resize', () => {
	resizeDisplay();
});

function resizeDisplay() {
	let height = $(window).height();
	let nav = 45;
	let padding = 8+24+10;
	let hr = 50;
	let title = $('#title-row').height();
	let filter = $('#search-row').height();
	let pagination = $('#pagination').height();
	let footer = $('.footer-content').height();
	let pageContentHeight = height - (nav + footer + padding);
	let billTableHeight = pageContentHeight - (title + filter + padding + pagination + hr);
	let minHeight = 300;

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

function viewDetail(code) {
  window.location.href = HOME + 'view_detail/'+code;
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

function viewSo(code) {
	let width = 1000;
	let height = 600;
	let center = (window.innerWidth - width)/2;
	let prop = "width="+width+", height="+height+", left="+center+", scrollbars=yes";
	let target = BASE_URL + 'orders/sales_order/view_detail/'+code+'?nomenu';
	window.open(target, '_blank', prop);
}

function viewWo(code) {
	if(code != "" && code != null && code.length > 9) {
		//--- properties for print
		var center    = ($(document).width() - 800)/2;
		var prop 			= "width=800, height=900, left="+center+", scrollbars=yes";
		var target = BASE_URL + 'orders/orders/edit_order/'+code+'?nomenu';
		window.open(target, "_blank", prop);
	}
}


function viewDpm(code) {
	if(code != "" && code != null && code.length > 9) {
		//--- properties for print
		var center    = ($(document).width() - 800)/2;
		var prop 			= "width=800, height=900, left="+center+", scrollbars=yes";
		var target = BASE_URL + 'orders/order_down_payment/view_detail/'+code+'?nomenu';
		window.open(target, "_blank", prop);
	}
}



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
    $('#cancel-id').val(id);
    $('#cancel-code').val(code);

    $('#cancelModal').on('shown.bs.modal', () => {
      $('#cancel-reason').focus();
    });

    $('#cancelModal').modal('show');
  });
}


function doCancel() {
	$('#cancel-reason').clearError();
  let id = $('#cancel-id').val();
	let code = $('#cancel-code').val();
  let reason = $('#cancel-reason').val().trim();

  if(reason.length == 0 ) {
    $('#cancel-reason').hasError();
    return false;
  }

  $('#cancelModal').modal('hide');

  load_in();

  setTimeout(() => {
    $.ajax({
      url:HOME + 'cancel',
      type:'POST',
      cache:false,
      data:{
        'id' : id,
				'code' : code,
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
