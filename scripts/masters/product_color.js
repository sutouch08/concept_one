var HOME = BASE_URL + 'masters/product_color/';

function addNew(){
  window.location.href =HOME + 'add_new';
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
  let group = $('#color_group').val();

  if(code.length == 0) {
    $('#code').addClass('has-error');
    $('#code-error').text('Required');
    return false;
  }

  if(name.length == 0) {
    $('#name').addClass('has-error');
    $('#name-error').text('Required');
    return false;
  }

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'name' : name,
      'color_group' : group
    },
    success:function(rs) {
      if(rs == 'success') {
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
  let group = $('#color_group').val();

  if(code.length == 0) {
    $('#code').addClass('has-error');
    $('#code-error').text('Required');
    return false;
  }

  if(name.length == 0) {
    $('#name').addClass('has-error');
    $('#name-error').text('Required');
    return false;
  }

  $.ajax({
    url:HOME + 'update/'+id,
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'name' : name,
      'color_group' : group
    },
    success:function(rs) {
      if(rs == 'success') {
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
        });
      }
    }
  })
}


function clearFilter(){
  var url = BASE_URL + 'masters/product_color/clear_filter';
  var page = BASE_URL + 'masters/product_color';
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
    window.location.href = BASE_URL + 'masters/product_color/delete/' + id;
  })
}


function toggleActive(option, id)
{
  $.ajax({
    url:BASE_URL + 'masters/product_color/set_active',
    type:'POST',
    cache:'false',
    data:{
      'id' : id,
      'active' : option
    },
    success:function(rs){
      if(rs != ''){
        $('#'+id).html(rs);
      }
    }
  });
}


function getSearch(){
  $('#searchForm').submit();
}


function export_api(){
  var code = $('#color_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'masters/product_color/export_api',
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
          text:'Color exported successful',
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
