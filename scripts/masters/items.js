var HOME = BASE_URL + 'masters/items/';
var click = 0;

function addNew(){
  window.location.href = HOME + 'add_new';
}

function goBack(){
  window.location.href = HOME;
}

function getEdit(id){
  window.location.href = HOME + 'edit/'+id;
}

function duplicate(id){
  window.location.href = HOME + 'duplicate/'+id;
}

function viewDetail(id) {
  window.location.href = HOME + 'view_detail/'+id;
}


function add() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('r');

    let error = 0;

    let item = {
      'code' : $('#code').val().trim(),
      'name' : $('#name').val().trim(),
      'style' : $('#style').val().trim(),
      'color' : $('#color').val().trim(),
      'size' : $('#size').val().trim(),
      'barcode' : $('#barcode').val().trim(),
      'cost' : parseDefaultFloat($('#cost').val(), 0),
      'price' : parseDefaultFloat($('#price').val(), 0),
      'unit_code' : $('#unit_code').val(),
      'unit_id' : $('#unit_code option:selected').data('id'),
      'unit_group' : $('#unit_code option:selected').data('groupid'),
      'sale_vat_code' : $('#sale-vat-code').val(),
      'sale_vat_rate' : parseDefaultFloat($('#sale-vat-code option:selected').data('rate'), 0.00),
      'purchase_vat_code' : $('#purchase-vat-code').val(),
      'purchase_vat_rate' : parseDefaultFloat($('#purchase-vat-code option:selected').data('rate'), 0.00),
      'brand_code' : $('#brand').val(),
      'group_code' : $('#group').val(),
      'main_group_code' : $('#mainGroup').val(),
      'sub_group_code' : $('#subGroup').val(),
      'category_code' : $('#category').val(),
      'kind_code' : $('#kind').val(),
      'type_code' : $('#type').val(),
      'year' : $('#year').val(),
      'count_stock' : $('#count_stock').is(':checked') ? 1 : 0,
      'can_sell' : $('#can_sell').is(':checked') ? 1 : 0,
      'active' : $('#active').is(':checked') ? 1 : 0
    };

    if(item.code.length == 0) {
      $('#code').hasError('Required');
      click = 0;
      return false;
    }

    if(item.name.length == 0) {
      $('#name').hasError('Required');
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'add',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(item)
      },
      success:function(rs) {
        click = 0;
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            if(ds.ex == 1) {
              swal({
                title:'Info',
                text:ds.message,
                type:'info',
                html:true
              }, function() {
                addNew();
              });
            }
            else {
              swal({
                title:'Success',
                type:'success',
                timer:1000
              });

              setTimeout(() => {
                addNew();
              }, 1200);
            }
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
        click = 0;
        showError(rs);
      }
    })
  }
}


function update() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('r');

    let item = {
      'id' : $('#id').val(),
      'code' : $('#code').val().trim(),
      'name' : $('#name').val().trim(),
      'style' : $('#style').val().trim(),
      'color' : $('#color').val().trim(),
      'size' : $('#size').val().trim(),
      'barcode' : $('#barcode').val().trim(),
      'cost' : parseDefaultFloat($('#cost').val(), 0),
      'price' : parseDefaultFloat($('#price').val(), 0),
      'unit_code' : $('#unit_code').val(),
      'unit_id' : $('#unit_code option:selected').data('id'),
      'unit_group' : $('#unit_code option:selected').data('groupid'),
      'sale_vat_code' : $('#sale-vat-code').val(),
      'sale_vat_rate' : parseDefaultFloat($('#sale-vat-code option:selected').data('rate'), 0.00),
      'purchase_vat_code' : $('#purchase-vat-code').val(),
      'purchase_vat_rate' : parseDefaultFloat($('#purchase-vat-code option:selected').data('rate'), 0.00),
      'brand_code' : $('#brand').val(),
      'group_code' : $('#group').val(),
      'main_group_code' : $('#mainGroup').val(),
      'sub_group_code' : $('#subGroup').val(),
      'category_code' : $('#category').val(),
      'kind_code' : $('#kind').val(),
      'type_code' : $('#type').val(),
      'year' : $('#year').val(),
      'count_stock' : $('#count_stock').is(':checked') ? 1 : 0,
      'can_sell' : $('#can_sell').is(':checked') ? 1 : 0,
      'active' : $('#active').is(':checked') ? 1 : 0
    };

    if(item.code.length == 0) {
      $('#code').hasError('Required');
      click = 0;
      return false;
    }

    if(item.name.length == 0) {
      $('#name').hasError('Required');
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'update',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(item)
      },
      success:function(rs) {
        click = 0;
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            if(ds.ex == 1) {
              swal({
                title:'Info',
                text:ds.message,
                type:'info',
                html:true
              }, function() {
                refresh()
              });
            }
            else {
              swal({
                title:'Success',
                type:'success',
                timer:1000
              });

              setTimeout(() => {
                refresh();
              }, 1200);
            }
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
        click = 0;
        showError(rs);
      }
    })
  }
}



$('#style').autocomplete({
  source: BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true,
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');
    if(arr.length == 2) {
      $(this).val(arr[0]);
    }
    else {
      $(this).val('');
    }
  }
});


$('#color').autocomplete({
  source: BASE_URL + 'auto_complete/get_color_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var err = rs.split(' | ');
    if(err.length == 2){
      $(this).val(err[0]);
    }else{
      $(this).val('');
    }
  }
});


$('#size').autocomplete({
  source:BASE_URL + 'auto_complete/get_size_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var err = rs.split(' | ');
    if(err.length == 2){
      $(this).val(err[0]);
    }else{
      $(this).val('');
    }
  }
});


function checkAdd(){
  var code = $('#code').val();
  if(code.length > 0){
    $.ajax({
      url:HOME + 'is_exists_code/'+code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs != 'ok'){
          set_error($('#code'), $('#code-error'), rs);
          return false;
        }else{
          clear_error($('#code'), $('#code-error'));
          $('#btn-submit').click();
        }
      }
    })
  }
}



function clearFilter(){
  var url = HOME + 'clear_filter';
  var page = BASE_URL + 'masters/products';
  $.get(url, function(){
    goBack();
  });
}


function getDelete(id, code, no){
  let url = BASE_URL + 'masters/items/delete_item/';// + encodeURIComponent(code);
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
      url: url,
      type:'GET',
      cache:false,
      data:{
        'id' : id
      },
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            type:'success',
            timer:1000
          });

          $('#row-'+no).remove();
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


function updateUnit() {
  let unit_id = $('#unit_code option:selected').data('id');
  let unit_group = $('#unit_code option:selected').data('groupid');

  $('#unit_id').val(unit_id);
  $('#unit_group').val(unit_group);
}


function getTemplate(){
  var token	= new Date().getTime();
	get_download(token);
	window.location.href = BASE_URL + 'masters/items/download_template/'+token;
}

function getSearch(){
  $('#searchForm').submit();
}


function sendToSap(id) {
  load_in();

  $.ajax({
    url:BASE_URL + 'masters/items/send_to_sap/'+id,
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
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
