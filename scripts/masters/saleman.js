var HOME = BASE_URL + 'masters/saleman/';

function getSearch(){
  $('#searchForm').submit();
}


$('.search-box').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


function clearFilter(){
  $.get(HOME + 'clear_filter', function(){
    goBack();
  });
}

function goBack(){
  window.location.href = HOME;
}


function toggleEdit(id) {
  $('#phone-label-'+id).addClass('hide');
  $('#phone-'+id).removeClass('hide');
  $('#phone-'+id).focus().select();
}

$('.ph').keyup(function(e) {
  if(e.keyCode === 13) {
    let id = $(this).data('id');
    let input = $(this);
    let phone = input.val();
    let label = $('#phone-label-'+id);

    $.ajax({
      url:HOME + 'update_phone',
      type:'POST',
      cache:false,
      data:{
        'id' : id,
        'phone' : phone
      },
      success:function(rs) {
        if(rs == 'success') {
          label.text(phone);
          input.addClass('hide');
          label.removeClass('hide');
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
});


function syncData(){
  load_in();
  $.ajax({
    url:HOME + 'syncData',
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      if(rs == 'success'){
        swal({
          title:'Completed',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          goBack();
        }, 1500);
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}
