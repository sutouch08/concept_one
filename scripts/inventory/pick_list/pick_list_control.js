$('#order-from-date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#order-to-date').datepicker('option', 'minDate', sd)
  }
})

$('#order-to-date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#order-from-date').datepicker('option', 'maxDate', sd)
  }
})

$('#due-from-date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#due-to-date').datepicker('option', 'minDate', sd)
  }
})

$('#due-to-date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#due-from-date').datepicker('option', 'maxDate', sd)
  }
})


$('#item-code').autocomplete({
  source:HOME + 'get_item_code',
  autoFocus:true,
  close:function() {
    if($(this).val() === 'not found') {
      $(this).val('');
    }
  }
});

$('#order-ref').keyup(function(e) {
  if(e.keyCode === 13) {
    let ref = $(this).val().trim();

    if(ref.length > 0) {
      addOrderByRef();
    }
  }
});


function addOrderByRef() {
  let code = $('#code').val();
  let ref = $('#order-ref').val().trim();

  if(ref.length) {
    $.ajax({
      url:HOME + 'add_order_by_reference',
      type:'POST',
      cache:false,
      data:{
        'code' : code,
        'reference' : ref,
      },
      success:function(rs) {
        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            let source = $('#order-tab-template').html();
            let output = $('#order-tab-table');

            render_prepend(source, ds.data, output);
            reIndex('o-no');
            $('#order-ref').val('').focus();
          }
          else {
            beep();
            swal({
              title:'Error !',
              text:ds.message,
              type:'error',
              html:true
            }, function() {
              $('#order-ref').val('').focus();
            })
          }
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


function chkOrderTabAll(el) {
  if(el.is(':checked')) {
    $('.chk-od').prop('checked', true)
  }
  else {
    $('.chk-od').prop('checked', false)
  }
}


function checkOrderAll(el) {
  if(el.is(':checked')) {
    $('.chk-list').prop('checked', true);
  }
  else {
    $('.chk-list').prop('checked', false);
  }
}


function clearOrderList() {
  let channels = $('#channels').val();
  $('#order-from-date').val('');
  $('#order-to-date').val('');
  $('#channels-code').val(channels).change();
  $('#customer').val('');
  $('#order-code').val('');
  $('#is-pick-list').val('0');
}


function getOrderList() {
  let h = {
    'from_date' : $('#order-from-date').val(),
    'to_date' : $('#order-to-date').val(),
    'channels' : $('#channels-code').val(),
    'customer' : $('#customer').val().trim(),
    'order_code' : $('#order-code').val().trim(),
    'is_pick_list' : $('#is-pick-list').val(),
    'warehouse_code' : $('#warehouse').val(),
    'limit' : $('#limit').val()
  }

  load_in();

  $.ajax({
    url:HOME + 'get_order_list',
    type:'POST',
    cache:false,
    data:{
      'filter' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs)

        if(ds.status === 'success') {
          let source = $('#order-template').html();
          let output = $('#order-list');

          render(source, ds.data, output);

          $('#orderListModal').modal('show');
        }
        else {
          showError(ds.message)
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


function addToPickList() {
  if($('.chk-list:checked').length) {
    $('#orderListModal').modal('hide');

    let h = {
      'code' : $('#code').val(),
      'orders' : []
    }

    $('.chk-list:checked').each(function() {
      if($(this).is(':checked')) {
        h.orders.push({'code' : $(this).val(), 'reference' : $(this).data('reference')});
      }
    });

    if(h.orders.length == 0) {
      swal({
        title:'Error!',
        text:'ไม่พบรายการที่เลือก',
        type:'error'
      }, function() {
        $('#orderListModal').modal('show');
      })

      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'add_to_pick_list',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            })

            setTimeout(() => {
              window.location.reload();
            }, 1200);
          }
          else {
            showError(ds.message)
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
}


function deleteOrders() {
  let code = $('#code').val();

  if($('.chk-od:checked').length) {
    let h = {
      'code' : code,
      'orders' : []
    }

    let rows = [];

    $('.chk-od:checked').each(function() {
      h.orders.push($(this).val());
      rows.push($(this).data('row'));
    });

    if(h.orders.length) {
      swal({
        title:'ลบออเดอร์',
        text:'ต้องการลบออเดอร์ที่เลือกออกหรือไม่ ?',
        type:'warning',
        html:true,
        showCancelButton:true,
        cancelButtonText:'No',
        confirmButtonText:'Yes',
        closeOnConfirm:true
      }, function() {
        load_in();

        setTimeout(() => {
          $.ajax({
            url:HOME + 'delete_orders',
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

                rows.forEach(function(id) {
                  $('#row-'+id).remove();
                });

                reIndex('o-no');
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
        }, 100)
      })
    }
  }
}


function reloadDetails() {
  let code = $('#code').val();
  load_in();
  $.ajax({
    url:HOME + 'get_details_table/' + code,
    type:'GET',
    cache:false,
    success:function(rs) {
      load_out();
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $('#details-template').html();
          let output = $('#details-table');

          render(source, ds.data, output);
        }
        else {
          beep();
          showError(ds.message);
        }
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
