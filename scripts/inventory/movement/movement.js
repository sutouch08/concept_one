var HOME = BASE_URL + 'inventory/movement/';

$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose: (sd) => {
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});

$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose: (sd) => {
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
});


function getSearch() {
  load_in();
  $('#searchForm').submit();
}


$('.search-box').keyup((e) => {
  if(e.keyCode == 13) {
    getSearch();
  }
});


function clearFilter() {
  load_in();

  $.ajax({
    url : HOME + 'clear_filter',
    type:'POST',
    cache:false,
    success:() => {
      window.location.href = HOME;
    }
  });
}
