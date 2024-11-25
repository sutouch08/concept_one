var HOME = BASE_URL + 'inventory/temp_delivery_consignment/';

function goBack(){
  window.location.href = HOME;
}



function getSearch(){
  $("#searchForm").submit();
}



function clearFilter(){
  $.get(HOME + 'clear_filter', function(){ goBack(); });
}


$(".search").keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


$("#fromDate").datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $("#toDate").datepicker("option", "minDate", sd);
  }
});


$("#toDate").datepicker({
  dateFormat: 'dd-mm-yy',
  onClose:function(sd){
    $("#fromDate").datepicker("option", "maxDate", sd);
  }
});


function get_detail(id)
{
  //--- properties for print
  var prop 			= "width=1100, height=900. left="+center+", scrollbars=yes";
  var center 	= ($(document).width() - 1100)/2;
	var target 	= HOME + 'get_detail/'+id+'?nomenu';
	window.open(target, "_blank", prop );
}



function getInvoice(code)
{
  //--- properties for print
  var prop 			= "width=1100, height=900. left="+center+", scrollbars=yes";
  var center 	= ($(document).width() - 1100)/2;
	var target 	= BASE_URL + 'account/consignment_order/view_detail/'+code+'?nomenu';
	window.open(target, "_blank", prop );
}



function deleteRow(docEntry, code)
{
  swal({
    title:"Confirmation",
    text:"ต้องการลบ "+code+" หรือไม่ ?",
    type:"warning",
    showCancelButton:true,
    confirmButtonColor:'#d15b47',
    confirmButtonText:'ยืนยัน',
    cancelButtonText:'ยกเลิก',
    closeOnConfirm:true
  }, function() {

    load_in();

    $.ajax({
      url:HOME + 'removeRow',
      type:'POST',
      cache:false,
      data:{
        'DocEntry' : docEntry
      },
      success:function(rs) {
        load_out();

        if(rs == 'success') {
          setTimeout(() => {
            $('#row-'+docEntry).remove();

            swal({
              title:'Deleted',
              type:'success',
              timer:1000
            })
          }, 200)
        }
        else {
          setTimeout(() => {
            swal({
              title:'Error',
              text:rs,
              type:'error'
            });
          }, 200)
        }
      }
    })
  })
}

function export_diff()
{
  var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();
}
