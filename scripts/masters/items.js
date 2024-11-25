var HOME = BASE_URL + 'masters/items/';

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


function update() {
	let error = 0;

	let data = {};

  data.id = $('#id').val();
	data.code = $('#code').val().trim();
	data.old_code = $('#old_code').val().trim();
	data.name = $('#name').val().trim(); // required
	data.style = $('#style').val().trim();
	data.old_style = $('#old_style').val().trim();
	data.color = $('#color').val().trim();
	data.size = $('#size').val().trim();
	data.barcode = $('#barcode').val().trim();
	data.cost = parseDefault(parseFloat($('#cost').val()), 0);
	data.price = parseDefault(parseFloat($('#price').val()), 0);
	data.unit_code = $('#unit_code').val();
  data.unit_id = $('#unit_code option:selected').data('id');
  data.unit_group = $('#unit_code option:selected').data('groupid');
  data.sale_vat_code = $('#sale-vat-code').val();
  data.sale_vat_rate = parseDefault(parseFloat($('#sale-vat-code option:selected').data('rate')), 0.00);
  data.purchase_vat_code = $('#purchase-vat-code').val();
  data.purchase_vat_rate = parseDefault(parseFloat($('#purchase-vat-code option:selected').data('rate')), 0.00);
	data.brand_code = $('#brand').val();
	data.group_code = $('#group').val();
	data.main_group_code = $('#mainGroup').val();
	data.sub_group_code = $('#subGroup').val();
	data.category_code = $('#category').val();
	data.kind_code = $('#kind').val();
	data.type_code = $('#type').val();
	data.year = $('#year').val();
	data.count_stock = $('#count_stock').is(':checked') ? 1 : 0;
	data.can_sell = $('#can_sell').is(':checked') ? 1 : 0;
	data.is_api = $('#is_api').is(':checked') ? 1 : 0;
	data.active = $('#active').is(':checked') ? 1 : 0;

	if(data.name.length === 0) {
		set_error($('#name'), $('#name-error'), "required");
		error++;
	}
	else {
		clear_error($('#name'), $('#name-error'));
	}

	if(error > 0) {
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			"data" : JSON.stringify(data)
		},
		success:function(rs) {
			load_out();
			var rs = rs.trim();
			if(rs == 'success') {
				swal({
					title:"Success",
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
		},
		error:function(xhr) {
			load_out();
			swal({
				title:"Error!",
				text:'Error : '+xhr.responseText,
				type:'error',
				html:true
			})
		}
	})
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
