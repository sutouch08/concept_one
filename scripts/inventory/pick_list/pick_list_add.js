
$('#date').datepicker({
  dateFormat:'dd-mm-yy'
})


function add() {
  clearErrorByClass('e');

  let h = {
    'date_add' : $('#date').val().trim(),
    'warehouse_code' : $('#warehouse').val(),
    'channels_code' : $('#channels').val(),
    'remark' : $('#remark').val().trim()
  };

  if( ! isDate(h.date_add)) {
    $('#date').hasError();
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if(h.warehouse_code == "") {
    $('#warehouse').hasError();
    swal("กรุณาเลือกคลัง");
    return false;
  }

  load_in()

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out()

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          goEdit(ds.code);
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError();
    }
  })
}


function getEdit() {
  $('.e').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function update() {
  clearErrorByClass('e');

  let h = {
    'code' : $('#code').val(),
    'date_add' : $('#date').val().trim(),
    'warehouse_code' : $('#warehouse').val(),
    'channels_code' : $('#channels').val(),
    'remark' : $('#remark').val().trim()
  };

  if( ! isDate(h.date_add)) {
    $('#date').hasError();
    swal("วันที่ไม่ถูกต้อง");
    return false;
  }

  if(h.warehouse_code == "") {
    $('#warehouse').hasError();
    swal("กรุณาเลือกคลัง");
    return false;
  }

  load_in()

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out()

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          goEdit(ds.code);
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError();
    }
  })
}


function save() {
  let code = $('#code').val();
  let count = $('.pick-detail').length;

  if(count == 0) {
    swal("ไม่พบรายการใน Pick List");
    return false;
  }

  swal({
    title:'Save',
    text:'ต้องการบันทึก Pick List นี้หรือไม่ ?',
    type:'info',
    html:true,
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  }, function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'save',
        type:'POST',
        cache:false,
        data:{
          'code' : code
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
              viewDetail(code);
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
    }, 100)
  })
}


function rollback(code) {
  swal({
    title:'ย้อนสถานะ',
    text:'ต้องการย้อนสถานะกลับมาแก้ไขหรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  }, function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'rollback/'+code,
        type:'GET',
        cache:false,
        success:function(rs) {
          load_out();

          if(rs.trim() === 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              goEdit(code);
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
