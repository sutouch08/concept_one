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

$('#uname').autocomplete({
  source: BASE_URL + 'auto_complete/get_active_user_by_uname',
  autoFocus:true,
  select:function(event, ui) {
    $('#dname').val(ui.item.dname);
    $('#user_id').val(ui.item.id);
  }
});

$('#uname').focusout(function() {
  if($(this).val() == "") {
    $('#dname').val("");
    $('#user_id').val("");
  }
});


function saveUser() {
  let code = $('#zone_code').val();
  let user_id = $('#user_id').val();
  let uname = $('#uname').val();
  let dname = $('#dname').val();

  if(user_id == "" && (uname.length > 0 || dname.length > 0)) {
    swal({
      title: "Warning",
      text: "Invalid Username",
      text:'warning'
    });

    return false;
  }

  if((uname.length == 0 && dname.length != 0) || (uname.length != 0 && dname.length == 0)) {
    swal({
      title: "Warning",
      text: "Invalid Username",
      text:'warning'
    });

    return false;
  }

  if(uname.length == 0 && dname.length == 0) {
    user_id = 0;
  }

  $.ajax({
    url:HOME + '/update_owner',
    type:'POST',
    cache:false,
    data: {
      'zone_code' : code,
      'user_id' : user_id
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
        });
      }
    }
  });
}


$("#empName").autocomplete({
	source: BASE_URL + 'auto_complete/get_employee',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var empName = arr[0];
			var empID = arr[1];
			$("#empName").val(empName);
			$("#empID").val(empID);
		}else{
			$("#empID").val('');
			$(this).val('');
		}
	}
});


$('#empName').keyup(function(e){
  if(e.keyCode == 13){
    addEmployee();
  }
});



function addEmployee(){
  let code = $('#zone_code').val();
  let empName = $('#empName').val();
  let empID = $('#empID').val();
  if(code === undefined){
    swal('ไม่พบรหัสโซน');
    return false;
  }

  if(empID == '' || empName.length == 0){
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
      'empID' : empID,
      'empName' : empName
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


function uEdit() {
  $('#uname').removeAttr('disabled').focus();
  $('#btn-u-edit').addClass('hide');
  $('#btn-u-update').removeClass('hide');
}
