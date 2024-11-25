var HOME = BASE_URL + 'masters/warehouse';

function goBack(){
  window.location.href = HOME;
}

function getSearch(){
  $('#searchForm').submit();
}


function clearFilter(){
  $.get(HOME +'/clear_filter', function(){
    goBack();
  });
}



function getEdit(code){
  window.location.href = HOME + '/edit/'+code;
}



function getDelete(code){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: HOME + '/delete/' + code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบคลัง '+code+' เรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });
          $('#row-'+code).remove();
          reIndex();
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })

  })
}

function toggleConsignment(option)
{
  $('#is_consignment').val(option);
  if(option == 1){
    $('#btn-cm-yes').addClass('btn-success');
    $('#btn-cm-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-cm-yes').removeClass('btn-success');
    $('#btn-cm-no').addClass('btn-danger');
  }
}


function toggleSell(option)
{
  $('#sell').val(option);
  if(option == 1){
    $('#btn-sell-yes').addClass('btn-success');
    $('#btn-sell-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-sell-yes').removeClass('btn-success');
    $('#btn-sell-no').addClass('btn-danger');
  }
}


function togglePrepare(option)
{
  $('#prepare').val(option);
  if(option == 1){
    $('#btn-prepare-yes').addClass('btn-success');
    $('#btn-prepare-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-prepare-yes').removeClass('btn-success');
    $('#btn-prepare-no').addClass('btn-danger');
  }
}


function toggleAuz(option)
{
  $('#auz').val(option);
  if(option == 1){
    $('#btn-auz-yes').addClass('btn-success');
    $('#btn-auz-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-auz-yes').removeClass('btn-success');
    $('#btn-auz-no').addClass('btn-danger');
  }
}


function toggleActive(option)
{
  $('#active').val(option);
  if(option == 1){
    $('#btn-active-yes').addClass('btn-success');
    $('#btn-active-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-active-yes').removeClass('btn-success');
    $('#btn-active-no').addClass('btn-danger');
  }
}


function syncData(){
  load_in();
  $.get(HOME +'/syncData', function(){
    load_out();
    swal({
      title:'Completed',
      type:'success',
      timer:1000
    });
    setTimeout(function(){
      goBack();
    }, 1500);
  });
}


function syncAllData() {
  load_in();

  $.get(HOME + '/syncAllData', function() {
    load_out();
    swal({
      title:'Completed',
      type:'success',
      timer:1000
    });

    setTimeout(function() {
      goBack();
    }, 1500);
  });
}

function exportFilter(){
  let code = $('#code').val();
  let name = $('#name').val();
  let role = $('#role').val();
  let is_consignment = $('#is_consignment').val();
  let sell = $('#sell').val();
  let prepare = $('#prepare').val();
  let active = $('#active').val();
  let auz = $('#auz').val();

  $('#export-code').val(code);
  $('#export-name').val(name);
  $('#export-role').val(role);
  $('#export-is-consignment').val(is_consignment);
  $('#export-sell').val(sell);
  $('#export-prepare').val(prepare);
  $('#export-active').val(active);
  $('#export-auz').val(auz);

  var token = $('#token').val();
  get_download(token);
  $('#exportForm').submit();
}
