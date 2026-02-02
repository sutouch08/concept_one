var warehouse_code = $('#warehouse').val();

$('#zone-code').autocomplete({
  source:BASE_URL + 'auto_complete/get_zone_code_and_name/' + warehouse_code,
  autoFocus:true,
  close:function() {
    let zone = $(this).val().trim().split(' | ');

    if(zone.length == 2) {
      $(this).val(zone[0]);
      $('#zone-name').val(zone[1]);
    }
    else {
      $(this).val('');
      $('#zone-name').val('');
    }
  }
})


$('#zone-code').keyup(function(e) {
  if(e.keyCode === 13) {
    let code = $(this).val().trim();

    if(code.length) {
      setTimeout(() => {
        get_zone_code(code);
      }, 100);
    }
  }
})


$('#barcode-item').keyup(function(e) {
  if(e.keyCode === 13) {
    doPicking();
  }
})


function get_zone_code() {
  let warehouse_code = $('#warehouse').val();
  let zone_code = $('#zone-code').val();

  if(zone_code.length) {
    load_in();
    $.ajax({
      url:HOME + 'get_zone_code',
      type:'GET',
      cache:false,
      data:{
        'zone_code' : zone_code,
        'warehouse_code' : warehouse_code
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            $('#zone-code').val(ds.zone.code)
            $('#zone-name').val(ds.zone.name);
            $('#item-qty').val(1);
            $('#barcode-item').val('').focus();
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
}


function changeZone() {
  $('#zone-code').val('')
  $('#zone-name').val('')
  $('#item-qty').val(1);
  $('#barcode-item').val('');
  $('#zone-code').focus();
}


function doPicking() {
  let warehouse_code = $('#warehouse').val();
  let zone_code = $('#zone-code').val();
  let qty = parseDefault(parseInt($('#item-qty').val()), 1);
  let barcode = $('#barcode-item').val().trim();
  let el = $('#'+barcode);

  if(warehouse_code.length == 0) {
    beep();
    swal("ไม่พบคลังสินค้า");
    return false;
  }

  if(zone_code.length == 0) {
    beep();
    swal("กรุณาระบุโซน");
    return false;
  }

  if(el === undefined) {
    beep();
    swal("ไม่พบรายการสินค้า");
    return false;
  }

  let id = el.data('id');
  let product_code = el.data('code');
  let product_name = el.data('name');
  let releaseQty = parseDefault(parseInt($('#release-qty-'+id).val()), 0);
  let pickQty = parseDefault(parseInt($('#pick-qty-'+id).val()), 0);
  let sumQty = pickQty + qty;

  // console.log('id:'+id+', Sum:'+sumQty+', qty:'+qty+', pick:'+pickQty+', release:'+releaseQty);

  if(sumQty > releaseQty) {
    beep();

    swal({
      title:"จำนวนเกินที่กำหนด",
      type:'warning'
    }, function() {
      setTimeout(() => {
        $('#item-qty').val(1);
        $('#barcode-item').val('').focus();
      }, 200);
    });

    return false;
  }

  let h = {
    'id' : $('#id').val(),
    'code' : $('#code').val(),
    'zone_code' : zone_code,
    'qty' : qty,
    'row_id' : id
  }

  $.ajax({
    url:HOME + 'do_picking',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let pickQtty = ds.pick_qty;
          let balance = releaseQty - pickQtty;

          $('.incomplete').removeClass('highlight');

          $('#pick-qty-'+id).val(pickQtty);
          $('#balance-qty-'+id).val(balance);

          if(balance == 0) {

            $('#incomplete-'+id).remove();

            let data = {
              'id' : id,
              'product_code' : product_code,
              'product_name' : product_name,
              'releaseQty' : releaseQty,
              'pickQtty' : pickQtty
            };

            let source = $('#complete-row-template').html();
            let output = $('#complete-table');
            render_append(source, data, output);
            reIndex('c-no');
          }
          else {
            $('#incomplete-'+id).addClass('highlight').prependTo($('#incomplete-table'));
          }

          reIndex('i-no');
          $('#item-qty').val(1);
          $('#barcode-item').val('').focus();

          recalTotalPicked();
        }
        else {
          beep();
          showError(ds.message);
        }
      }
      else {
        beep();
        showError();
      }
    },
    error:function(rs) {
      beep()
      showError(rs)
    }
  })
}


function reloadPickRow(id) {
  let whsCode = $('#warehouse').val();
  load_in();
  $.ajax({
    url:HOME + 'get_pick_row',
    type:'POST',
    cache:false,
    data:{
      'row_id' : id,
      'warehouse_code' : whsCode
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          $('#pick-qty-'+id).val(ds.data.pick_qty);
          $('#balance-qty-'+id).val(ds.data.balance_qty);
          $('#stock-in-zone-'+id).html(ds.data.stock_in_zone);

          recalTotalPicked();
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


function reloadInComplete() {
  let code = $('#code').val();

  load_in();

  $.ajax({
    url:HOME + 'get_incomplete_table/'+code,
    type:'GET',
    cache:false,
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $('#incomplete-template').html();
          let output = $('#incomplete-table');

          render(source, ds.data, output);

          recalTotalPicked();
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


function reloadComplete() {
  let code = $('#code').val();

  load_in();

  $.ajax({
    url:HOME + 'get_complete_table/'+code,
    type:'GET',
    cache:false,
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $('#complete-template').html();
          let output = $('#complete-table');

          render(source, ds.data, output);

          recalTotalPicked();
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


function reloadTransection() {
  let code = $('#code').val();

  load_in();

  $.ajax({
    url:HOME + 'get_transection_table/'+code,
    type:'GET',
    cache:false,
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $('#transections-template').html();
          let output = $('#transection-table');

          render(source, ds.data, output);

          recalTotalPicked();
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


function removeTransection(id, qty, product_code, zone_code) {
  swal({
    title:'Delete Transection',
    text:'คุณแน่ใจว่าต้องการลบ Transection <br/>'+product_code+' : '+ zone_code + '<br/>สต็อกจะถูกดีดกลับเข้าโซน '+zone_code+' จำนวน '+qty+' pcs.<br/>ต้องการดำเนินการต่อหรือไม่ ?',
    type:'warning',
    html:true,
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    confirmButtonColor:'red',
    closeOnConfirm:true
  }, function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'delete_transection',
        type:'GET',
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

            $('#trans-'+id).remove();

            reIndex('t-no');
            reloadInComplete();
            reloadComplete();
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


function recalTotalPicked() {
  let picked = 0;

  $('.picked-qty').each(function() {
    let qty = parseDefault(parseInt($(this).val()), 0);

    picked += qty;
  });

  $('#total-picked').text(addCommas(picked));
}
