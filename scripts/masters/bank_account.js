var HOME = BASE_URL + 'masters/bank/';

function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}


function getEdit(code){
  window.location.href = HOME + 'edit/'+code;
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


function add() {
  clearErrorByClass('e');

  let d = {
    'bank_code' : $('#bank-code').val(),
    'account_name' : $('#acc-name').val().trim(),
    'account_no' : $('#acc-no').val(),
    'branch' : $('#branch').val().trim(),
    'sap_code' : $('#sap-code').val().trim(),
    'active' : $('#active').is(':checked') ? 1 : 0
  };

  if(d.bank_code == "") {
    $('#bank-code').hasError('โปรดเลือกธนาคาร');
    return false;
  }

  if(d.account_name.length == 0) {
    $('#acc-name').hasError('โปรดระบุชื่อบัญชี');
    return false;
  }

	if(d.account_no.length == 0) {
    $('#acc-no').hasError('โปรดระบุเลขที่บัญชี');
    return false;
  }

	if(d.branch === ""){
		$('#branch').hasError('โปรดระบุสาขา');
		return false;
	}

  if(d.sap_code.length == 0) {
    $('#sap-code').hasError('โปรดระบุเลขผังบัญชี');
    return false;
  }

	load_in();

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			'data' : JSON.stringify(d)
		},
		success:function(rs){
			load_out();

			if(rs.trim() === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					addNew();
				}, 1200);
			}
      else {
				showError(rs);
			}
		},
    error:function(rs) {
      showError(rs);
    }
	});
}


function update() {
  clearErrorByClass('e');

  let d = {
    'id' : $('#id').val(),
    'bank_code' : $('#bank-code').val(),
    'account_name' : $('#acc-name').val().trim(),
    'account_no' : $('#acc-no').val(),
    'branch' : $('#branch').val().trim(),
    'sap_code' : $('#sap-code').val().trim(),
    'active' : $('#active').is(':checked') ? 1 : 0
  };

  if(d.bank_code == "") {
    $('#bank-code').hasError('โปรดเลือกธนาคาร');
    return false;
  }

  if(d.account_name.length == 0) {
    $('#acc-name').hasError('โปรดระบุชื่อบัญชี');
    return false;
  }

	if(d.account_no.length == 0) {
    $('#acc-no').hasError('โปรดระบุเลขที่บัญชี');
    return false;
  }

	if(d.branch === ""){
		$('#branch').hasError('โปรดระบุสาขา');
		return false;
	}

  if(d.sap_code.length == 0) {
    $('#sap-code').hasError('โปรดระบุเลขผังบัญชี');
    return false;
  }

	load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			'data' : JSON.stringify(d)
		},
		success:function(rs){
			load_out();

			if(rs.trim() === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});
			}
      else {
				showError(rs);
			}
		},
    error:function(rs) {
      showError(rs);
    }
	});
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
		closeOnConfirm: true
  },
  function() {
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
              title:'Deleted',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              window.location.reload();
            }, 1100);
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
