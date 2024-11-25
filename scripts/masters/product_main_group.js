function addNew(){
  window.location.href = BASE_URL + 'masters/product_main_group/add_new';
}



function goBack(){
  window.location.href = BASE_URL + 'masters/product_main_group';
}


function getEdit(code){
  window.location.href = BASE_URL + 'masters/product_main_group/edit/'+code;
}


function clearFilter(){
  var url = BASE_URL + 'masters/product_main_group/clear_filter';
  var page = BASE_URL + 'masters/product_main_group';
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
    window.location.href = BASE_URL + 'masters/product_main_group/delete/' + code;
  })
}



function getSearch(){
  $('#searchForm').submit();
}
