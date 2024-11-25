var HOME = BASE_URL + 'inventory/qc/';

function goBack(){
  window.location.href = HOME;
}




//--- ต้องการจัดสินค้า
function goQc(code){
  window.location.href = HOME + 'process/'+code;
}



function viewProcess(){
  window.location.href = HOME + 'view_process';
}


function viewHistory(code) {
  // $('#view-history-form').submit();
  var mapForm = document.createElement("form");
  mapForm.target = "Map";
  mapForm.method = "POST"; // or "post" if appropriate
  mapForm.action = BASE_URL + 'inventory/pack';

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


//---- กำหนดค่าการแสดงผลที่เก็บสินค้า เมื่อมีการคลิกปุ่มที่เก็บ
$(function () {
  $('.btn-pop').popover({html:true});
});
