const HOME = BASE_URL + 'masters/payment_methods/';

function addNew() {
  window.location.href = HOME + 'add_new';
}


function goBack() {
  window.location.href = HOME;
}


function getEdit(code) {
  window.location.href = HOME + 'edit/'+code;
}


function add() {
  let code = $('#code').val();
  let name = $('#name').val();
  let role = $('#role').val();
  let account = $('#account').val();

  if(code.length == 0) {
    $('#code-error').text('Required');
    $('#code').addClass('has-error');
    return false;
  }
  else {
    $('#code-error').text('');
    $('#code').removeClass('has-error');
  }

  if(name.lenght == 0) {
    $('#name-error').text('Required');
    $('#name').addClass('has-error');
    return false;
  }
  else {
    $('#name-error').text('');
    $('#name').removeClass('has-error');
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'name' : name,
      'role' : role,
      'account' : account
    },
    success:function(rs) {
      load_out();

      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer: 1000
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
        });
      }
    }
  });
}


function update() {
  let id = $('#id').val();
  let code = $('#code').val();
  let name = $('#name').val();
  let role = $('#role').val();
  let account = $('#account').val();

  if(code.length == 0) {
    $('#code-error').text('Required');
    $('#code').addClass('has-error');
    return false;
  }
  else {
    $('#code-error').text('');
    $('#code').removeClass('has-error');
  }

  if(name.lenght == 0) {
    $('#name-error').text('Required');
    $('#name').addClass('has-error');
    return false;
  }
  else {
    $('#name-error').text('');
    $('#name').removeClass('has-error');
  }

  load_in();

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'code' : code,
      'name' : name,
      'role' : role,
      'account' : account
    },
    success:function(rs) {
      load_out();

      if(rs === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer: 1000
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
    window.location.href = HOME + 'delete/' + code;
  })
}

function toggleBankAccount() {
  let role = $('#role').val();
  if(role == 2) {
    $('#bank-row').removeClass('hide');
  }
  else {
    $('#bank-row').addClass('hide');
  }
}

$('.search').keyup(function(e) {
  if(e.keyCode === 13) {
    getSearch();
  }
});


$('.filter').change(function() {
  getSearch();
});

function getSearch(){
  $('#searchForm').submit();
}

function clearFilter(){
  $.get(HOME + 'clear_filter', function(rs){ goBack() });
}
