var HOME = BASE_URL + 'masters/product_size/';

function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}


function getEdit(id){
  window.location.href = HOME + 'edit/'+id;
}


function add() {
  $('.req').removeClass('has-error');
  $('.help-block').text('');

  let code = $('#code').val();
  let name = $('#name').val();
  let position = $('#position').val();

  if(code.length == 0) {
    $('#code-error').text('Required');
    $('#code').addClass('has-error');
    return false;
  }

  if(name.length == 0) {
    $('#name').addClass('has-error');
    $('#name-error').text('Required');
    return false;
  }

  if(position.length == 0) {
    $('#position').addClass('has-error');
    $('#position-error').text('Required');
    return false;
  }

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'name' : name,
      'position' : position
    },
    success:function(rs) {
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          addNew();
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


function update() {
  $('.req').removeClass('has-error');
  $('.help-block').text('');

  let id = $('#id').val();
  let code = $('#code').val();
  let name = $('#name').val();
  let position = $('#position').val();

  if(code.length == 0) {
    $('#code-error').text('Required');
    $('#code').addClass('has-error');
    return false;
  }

  if(name.length == 0) {
    $('#name').addClass('has-error');
    $('#name-error').text('Required');
    return false;
  }

  if(position.length == 0) {
    $('#position').addClass('has-error');
    $('#position-error').text('Required');
    return false;
  }

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'code' : code,
      'name' : name,
      'position' : position
    },
    success:function(rs) {
      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
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



function clearFilter(){
  var url = BASE_URL + 'masters/product_size/clear_filter';
  var page = HOME;
  $.get(url, function(rs){
    window.location.href = page;
  });
}


function getDelete(id, name){
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
    $.ajax({
      url:HOME + 'delete/'+id,
      type:'POST',
      cache:false,
      success:function(rs) {
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
  })
}



function getSearch(){
  $('#searchForm').submit();
}


function export_api(){
  var code = $('#size_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'masters/product_size/export_api',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'Size exported successful',
          type:'success',
          timer:1000
        })
      }else{
        swal({
          title:'Error',
          text:rs,
          type:'error'
        });
      }
    }
  })
}
