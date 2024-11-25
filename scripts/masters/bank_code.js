var HOME = BASE_URL + 'masters/bank_code/';

function goBack(){
  window.location.href = HOME;
}

function addNew(){
  $('#addModal').modal('show');
  $('#addModal').on('shown.bs.modal', function() {
    $('#add-bank-code').focus();
  });
}


 async function add() {
  clearErrorByClass('e');

  let error = 0;
  let code = $('#add-bank-code').val().trim();
  let name = $('#add-bank-name').val().trim();
  let active = $('#add-bank-active').is(':checked') ? 1 : 0;

  if(code.length == 0) {
    $('#add-bank-code').hasError('Required');
    error++;
  }

  if(name.length == 0) {
    $('#add-bank-name').hasError('Required');
    error++;
  }

  if(error > 0) {
    return false;
  }

  let exists = await is_exists(code);

  if(exists) {
    $('#add-bank-code').hasError('Duplicated bank code');
    return false;
  }
  else {
    closeModal('addModal');
    load_in();

    $.ajax({
      url:HOME + 'add',
      type:'POST',
      cache:false,
      data:{
        'code' : code,
        'name' : name,
        'active' : active
      },
      success:function(rs) {
        load_out();

        if(rs.trim() == 'success') {
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
            type:'error',
            html:true
          }, function() {
            $('#addModal').modal('show');
          });
        }
      },
      error:function(rs) {
        load_out();
        swal({
          title:'Error!',
          text:rs.responseText,
          type:'error',
          html:true
        }, function() {
          $('#addModal').modal('show');
        })
      }
    })
  }
}


function is_exists(code) {
  return new Promise((resolve) => {
    let uri = HOME + 'is_exists_code';
    let option = new FormData();
    option.append('code', code);

    let request = {
      method:'POST',
      body:option
    };

    fetch(uri, request)
    .then(response => response.text())
    .then((ds) => {
      if(ds == 'exists') {
        resolve(true);
      }
      else {
        resolve(false);
      }
    })
  });
}


function getEdit(id) {
  load_in();

  $.ajax({
    url:HOME + 'get/'+id,
    type:'GET',
    cache:false,
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let data = JSON.parse(rs);

        if(data.status === 'success') {
          let ds = data.data;
          $('#edit-bank-id').val(ds.id);
          $('#edit-bank-code').val(ds.code);
          $('#edit-bank-name').val(ds.name);

          if(ds.active == 1) {
            $('#edit-bank-active').prop('checked', true);
          }
          else {
            $('#edit-bank-active').prop('checked', false);
          }

          $('#editModal').on('shown.bs.modal', () => { $('#edit-bank-name').focus();});
          $('#editModal').modal('show');
        }
        else {
          showError(data.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  })
}


function update() {
  clearErrorByClass('e');
  let d = {
    'id' : $('#edit-bank-id').val(),
    'code' : $('#edit-bank-code').val(),
    'name' : $('#edit-bank-name').val().trim(),
    'active' : $('#edit-bank-active').is(':checked') ? 1 : 0
  };

  if(d.name.length == 0) {
    $('#edit-bank-name').hasError('Required');
    return false;
  }

  $('#editModal').modal('hide');

  load_in();

  reqUri = HOME + 'update';
  header = new Headers();
  header.append('Content-type', 'application/json');

  options = {
    method : 'POST',
    headers : new Headers(),
    body: JSON.stringify(d)
  }

  fetch(reqUri, options)
  .then(response => response.text())
  .then((rs) => {
    load_out();
    if(isJson(rs)) {
      let ds = JSON.parse(rs);

      if(ds.status === 'success') {
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
        showError(ds.message);
      }
    }
    else {
      showError(rs);
    }
  })
  .catch(error => {
    showError(error);
  })
}


function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(){
      goBack();
  });
}


$('.search').keyup(function(e){
	if(e.keyCode == 13){
		getSearch();
	}
});


function getSearch()
{
	$('#searchForm').submit();
}



function delete_bank(id) {

	$.ajax({
		url:HOME + 'delete/'+id,
		type:'POST',
		cache:false,
		success:function(rs){
			var rs = $.trim(rs);
			if(rs === 'success'){
				swal({
					title:'Deleted',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					goBack();
				}, 1300);
			}
			else
			{
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		}
	})
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
  },function() {

    setTimeout(() => {
      load_in();

      $.ajax({
        url:HOME + 'delete',
        type:'POST',
        cache:false,
        data:{
          'id' : id
        },
        success:function(rs) {
          load_out();

          if(rs.trim() === 'success') {
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
            showError(rs);
          }
        },
        error:function(rs) {
          showError(rs);
        }
      })
    }, 100);
  })
}
