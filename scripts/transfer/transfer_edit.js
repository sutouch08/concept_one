
setInterval(() => {
  let code = $('#transfer_code').val();
  let uuid = localStorage.getItem('ix_uuid');

  console.log(code, uuid);

  $.ajax({
    url:HOME + 'update_uuid',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'uuid' : uuid
    }
  })
}, 30000);
