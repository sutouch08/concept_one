var HOME = BASE_URL + 'masters/job_type/';

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

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'name' : name
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


  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'code' : code,
      'name' : name
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
  var url = BASE_URL + 'masters/job_type/clear_filter';
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
