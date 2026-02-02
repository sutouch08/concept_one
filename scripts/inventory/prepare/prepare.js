var HOME = BASE_URL + 'inventory/prepare';


function goBack(){
    window.location.href = HOME;
}

//---- ไปหน้าจัดสินค้า
function goPrepare(code){
    window.location.href = HOME + '/process/'+code;
}


function goProcess(){
  window.location.href = HOME + '/view_process';
}


function goToProcess() {
  let code = $('#order-code').val().trim();

  if(code.length) {
    load_in();
    $.ajax({
      url:HOME + '/get_order_code',
      type:'POST',
      cache:false,
      data:{
        'reference' : code
      },
      success:function(rs) {
        load_out();
        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            goPrepare(ds.code);
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


$('#order-code').keyup(function(e) {
  if(e.keyCode === 13) {
    goToProcess();
  }
})


function viewHistory(code) {
  var mapForm = document.createElement("form");
  mapForm.target = "Map";
  mapForm.method = "POST"; // or "post" if appropriate
  mapForm.action = BASE_URL + 'inventory/buffer';

  var mapInput = document.createElement("input");
  mapInput.type = "hidden";
  mapInput.name = "order_code";
  mapInput.value = code;
  mapForm.appendChild(mapInput);

  document.body.appendChild(mapForm);

  map = window.open("", "Map", "status=0,title=0,height=600,width=1200,scrollbars=1");

  if (map) {
    mapForm.submit();
  }
}


function pullBack(code){
  $.ajax({
    url:HOME + '/pull_order_back',
    type:'POST',
    cache:'false',
    data:{
      'order_code' : code
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(rs == 'success'){
        $('#row-'+code).remove();
        reIndex();
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}


//--- ไปหน้ารายการที่กำลังจัดสินค้าอยู่
function viewProcess(){
  window.location.href = HOME + '/view_process';
}
