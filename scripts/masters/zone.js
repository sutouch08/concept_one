var HOME = BASE_URL + 'masters/zone';


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


function toggleCheckAll() {
  if($('#chk-all').is(':checked')) {
    $('.chk').prop('checked', true);
  }
  else {
    $('.chk').prop('checked', false);
  }
}


function togglePosApi(id) {
  let is_api = $('#is-api-'+id).val();

  is_api = is_api == '1' ? '0' : '1';

  $.ajax({
    url:HOME + '/update_pos_api/',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'is_api' : is_api
    },
    success:function(rs) {
      if(rs == 'success') {
        $('#is-api-'+id).val(is_api);
        if(is_api == '1') {
          $('#pos-api-label-'+id).text('Yes');
        }
        else {
          $('#pos-api-label-'+id).html('No');
        }
      }
      else {
        swal({
          title:'Failed !',
          text:rs,
          type:'error'
        })
      }
    }
  })
}


function togglePickface(id) {
  let is_pickface = $('#is-pickface-'+id).val();

  is_pickface = is_pickface == '1' ? '0' : '1';

  $.ajax({
    url:HOME + '/update_pickface/',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'is_pickface' : is_pickface
    },
    success:function(rs) {
      if(rs == 'success') {
        $('#is-pickface-'+id).val(is_pickface);
        if(is_pickface == '1') {
          $('#pickface-label-'+id).text('Yes');
        }
        else {
          $('#pickface-label-'+id).html('No');
        }
      }
      else {
        swal({
          title:'Failed !',
          text:rs,
          type:'error'
        })
      }
    }
  })
}


function setFastMove(option) {
  let h = {
    'is_fast_move' : option == 1 ? 1 :  0,
    'zoneList' : []
  }

  if($('.chk:checked').length > 0) {
    $('.chk:checked').each(function() {
      h.zoneList.push($(this).val());
    });
  }

  if(h.zoneList.length > 0) {
    load_in();

    $.ajax({
      url:HOME + '/toggle_fast_move',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
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
            refresh();
          }, 1200);
        }
        else {
          beep();
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        showError(rs);
      }
    })
  }
}

function update() {
  let code = $('#zone_code').val();
  let user_id = $('#user_id').val();
  let pos_api = $('#pos-api').val();
  let is_pickface = $('#is-pickface').is(':checked') ? 1 : 0;
  let is_fast_move = $('#is-fast-move').is(':checked') ? 1 : 0;

  $.ajax({
    url:HOME + '/update',
    type:'POST',
    cache:false,
    data:{
      'zone_code' : code,
      'user_id' : user_id,
      'pos_api' : pos_api,
      'is_pickface' : is_pickface,
      'is_fast_move' : is_fast_move
    },
    success:function(rs) {
      if(rs == 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        $('#user_id').attr('disabled', 'disabled');
        $('#pos-api').attr('disabled', 'disabled');
        $('#is-pickface').attr('disabled', 'disabled');
        $('#btn-u-update').addClass('hide');
        $('#btn-u-edit').removeClass('hide');
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


function addEmployee(){
  let code = $('#zone_code').val();
  let id = $('#empID').val();
  let name = $('#empID option:selected').data('name');

  if(code === undefined){
    swal('ไม่พบรหัสโซน');
    return false;
  }

  if(id == '' || name.length == 0){
    swal('ชื่อพนักงานไม่ถูกต้อง');
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + '/add_employee',
    type:'POST',
    cache:false,
    data:{
      'zone_code' : code,
      'empID' : id,
      'empName' : name
    },
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'เพิ่มพนักงานเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);
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


$('#search-box').autocomplete({
  source:BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  close:function(){
    let arr = $(this).val().split(' | ');
    if(arr.length == 2){
      let code = arr[0];
      let name = arr[1];
      $(this).val(name);
      $('#customer_code').val(code);
    }else{
      $(this).val('');
      $('#customer_code').val('');
    }
  }
});


$('#search-box').keyup(function(e){
  if(e.keyCode == 13){
    addCustomer();
  }
});


function addCustomer(){
  let code = $('#zone_code').val();
  let customer_code = $('#customer_code').val();
  let customer_name = $('#search-box').val();
  if(code === undefined){
    swal('ไม่พบรหัสโซน');
    return false;
  }

  if(customer_code == '' || customer_name.length == 0){
    swal('ชื่อลูกค้าไม่ถูกต้อง');
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + '/add_customer',
    type:'POST',
    cache:false,
    data:{
      'zone_code' : code,
      'customer_code' : customer_code
    },
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'เพิ่มลูกค้าเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);
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
            text:'ลบ '+code+' เรียบร้อยแล้ว',
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


function deleteCustomer(id,code){
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
      url: HOME + '/delete_customer/' + id,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบ '+code+' เรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });
          $('#row-'+id).remove();
          reIndex();
          $('#search-box').focus();
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


function deleteEmployee(id,name){
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
      url: HOME + '/delete_employee/' + id,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบ '+name+' เรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });
          $('#emp-'+id).remove();
          reIndex();
          $('#search-box').focus();
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


function exportFilter(){
  let code = $('#code').val();
  let uname = $('#u-name').val();
  let customer = $('#customer').val();
  let warehouse = $('#warehouse').val();

  $('#export-code').val(code);
  $('#export-uname').val(uname);
  $('#export-customer').val(customer);
  $('#export-warehouse').val(warehouse);

  var token = $('#token').val();
  get_download(token);
  $('#exportForm').submit();
}


function editZone() {
  $('#user_id').removeAttr('disabled').focus();
  $('#pos-api').removeAttr('disabled');
  $('#is-pickface').removeAttr('disabled');
  $('#is-fast-move').removeAttr('disabled');
  $('#btn-u-edit').addClass('hide');
  $('#btn-u-update').removeClass('hide');
}


function generateQrcode() {
  if($('.chk:checked').length) {

    let h = [];

    $('.chk:checked').each(function() {
      let code = $(this).data('code');
      let name = $(this).data('name');

      h.push({'code' : code, 'name' : name});
    });

    if(h.length) {

      var mapForm = document.createElement('form');
      mapForm.target = "Map";
      mapForm.method = "POST";
      mapForm.action = HOME + "/generate_qrcode";

      var mapInput = document.createElement("input");
      mapInput.type = "hidden";
      mapInput.name = "data";
      mapInput.value = JSON.stringify(h);

      mapForm.appendChild(mapInput);

      document.body.appendChild(mapForm);

      map = window.open("", "Map", "status=0,title=0,height=900,width=800,scrollbars=1");

      if(map) {
        mapForm.submit();
      }
      else {
        swal('You must allow popups for this map to work.');
      }
    }
  }
}
