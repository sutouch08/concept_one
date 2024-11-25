var HOME = BASE_URL + 'orders/order_pos_round/';

function goBack() {
  window.location.href = HOME;
}

function viewDetail(id) {
  window.location.href = HOME + 'view_detail/'+id;
}


function getSearch() {
  $('#searchForm').submit();
}


function clearFilter() {
  $.get(HOME + 'clear_filter', function() {
    goBack();
  });
}

function printPosRound(id) {
  let width = 400;
	let height = 600;
	let center = (window.innerWidth - width)/2;
	let middle = (window.innerHeight - height)/2;
	let prop = "width="+width+", height="+height+", left="+center+", top="+middle+", scrollbars=yes";
	let target = HOME + 'print_pos_round/'+id;
	window.open(target, '_blank', prop);
}

function recalSummary(id) {
  load_in();

  $.ajax({
    url:HOME + 'recalSummary/'+id,
    type:'POST',
    cache:false,
    success:function(rs) {
      load_out();

      if(rs === 'success') {
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
          text:rs,
          type:'error'
        })
      }
    }
  })
}


$('#openFromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#closeFromDate').datepicker('option', 'minDate', sd)
  }
});

$('#openToDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#openFromDate').datepicker('option', 'maxDate', sd)
  }
});


$('#closeFromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#closeToDate').datepicker('option', 'minDate', sd)
  }
});

$('#closeToDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#closeFromDate').datepicker('option', 'maxDate', sd)
  }
});
