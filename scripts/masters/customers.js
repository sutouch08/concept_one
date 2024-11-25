function addNew(){
  window.location.href = BASE_URL + 'masters/customers/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'masters/customers';
}


function getEdit(code){
  window.location.href = BASE_URL + 'masters/customers/edit/'+code;
}

function changeURL(style, tab)
{

	var url = BASE_URL + 'masters/customers/edit/' + style + '/' + tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'customers', url);
}



function clearFilter(){
  var url = BASE_URL + 'masters/customers/clear_filter';
  var page = BASE_URL + 'masters/customers';
  $.get(url, function(rs){
    window.location.href = page;
  });
}


function getDelete(code, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    window.location.href = BASE_URL + 'masters/customers/delete/' + code;
  })
}



$('.filter').change(function(){
  getSearch();
});


$('#date').datepicker();


function getSearch(){
  $('#searchForm').submit();
}



function syncData(){
  load_in();
  $.ajax({
    url: BASE_URL + 'masters/customers/syncData',
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      setTimeout(function(){
        goBack();
      },500);
    }
  });
}



function syncAllData(){
  load_in();
  $.ajax({
    url: BASE_URL + 'masters/customers/syncAllData',
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      setTimeout(function(){
        goBack();
      },500);
    }
  });
}


function doExport(){
  var code = $('#customers_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'masters/customers/export_customer/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'ส่งข้อมูลไป SAP เรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });
      }else{
        swal({
          title:'Error!',
          text: rs,
          type:'error'
        });
      }
    }
  })
}
