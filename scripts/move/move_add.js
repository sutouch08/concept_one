function add(){
  var isManual = $('#manualCode').length;
  if(isManual === 1){
    getValidate();
  }else{
    addMove();
  }
}


function getValidate(){
  var prefix = $('#prefix').val();
  var runNo = parseInt($('#runNo').val());
  let code = $('#code').val();
  if(code.length == 0){
    addMove();
    return false;
  }

  let arr = code.split('-');

  if(arr.length == 2){
    if(arr[0] !== prefix){
      swal('Prefix ต้องเป็น '+prefix);
      return false;
    }else if(arr[1].length != (4 + runNo)){
      swal('Run Number ไม่ถูกต้อง');
      return false;
    }else{
      $.ajax({
        url: BASE_URL + 'inventory/move/is_exists/'+code,
        type:'GET',
        cache:false,
        success:function(rs){
          if(rs == 'not_exists'){
            addMove();
          }else{
            swal({
              title:'Error!!',
              text: rs,
              type: 'error'
            });
          }
        }
      })
    }

  }else{
    swal('เลขที่เอกสารไม่ถูกต้อง');
    return false;
  }
}


//--- เพิ่มเอกสารโอนคลังใหม่
function addMove() {
  var code = $('#code').val();
  //--- วันที่เอกสาร
  var date_add = $('#date').val();

  //--- คลังต้นทาง
  var from_warehouse = $('#from_warehouse_name').val();
  var from_warehouse_code = $('#from_warehouse_code').val();

  //--- คลังปลายทาง
  var to_warehouse = $('#to_warehouse_name').val();
  var to_warehouse_code = $('#to_warehouse_code').val();

  //--- หมายเหตุ
  var remark = $.trim($('#remark').val());
  var reqRemark = $('#require_remark').val();

  //--- ตรวจสอบวันที่
  if( ! isDate(date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  //--- ตรวจสอบคลังต้นทาง
  if(from_warehouse.length == 0 || from_warehouse_code == ''){
    swal('คลังต้นทางไม่ถูกต้อง');
    return false;
  }

  //--- ตรวจสอบคลังปลายทาง
  if(to_warehouse_code == '' || to_warehouse.length == 0){
    swal('คลังปลายทางไม่ถูกต้อง');
    return false;
  }

  //--- ตรวจสอบว่าเป็นคนละคลังกันหรือไม่ (ต้องเป็นคนละคลังกัน)
  if( from_warehouse_code != to_warehouse_code){
    swal('คลังต้นทางต้องตรงกับคลังปลายทาง');
    return false;
  }

  if(reqRemark == 1 && remark.length < 10) {
    swal({
      title:'Required',
      text:'กรุณาระบุหมายเหตุอย่างน้อย 10 ตัวอักษร',
      type:'warning'
    });

    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'date_add' : date_add,
      'from_warehouse_code' : from_warehouse_code,
      'to_warehouse_code' : to_warehouse_code,
      'remark' : remark
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          goEdit(ds.code);
        }
        else {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error'
          });
        }
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




//--- update เอกสาร
function update(){
  //--- ไอดีเอกสาร สำหรับส่งไปอ้างอิงการแก้ไข
  var code = $('#move_code').val();

  //--- คลังต้นทาง
  var from_warehouse = $('#from_warehouse_code').val();
  var old_from_wh = $('#old_from_warehouse_code').val();
  //--- คลังปลายทาง
  var to_warehouse = $('#to_warehouse_code').val();
  var old_to_wh = $('#old_to_warehouse_code').val();
  //--  วันที่เอกสาร
  var date_add = $('#date').val();

  //--- หมายเหตุ
  var remark = $('#remark').val();

  //--- ตรวจสอบไอดี
  if(code == ''){
    swal('Error !', 'ไม่พบเลขที่เอกสาร', 'error');
    return false;
  }

  //--- ตรวจสอบวันที่
  if( ! isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  //--- ตรวจสอบคลังต้นทาง
  if(from_warehouse == ''){
    swal('กรุณาเลือกคลังต้นทาง');
    return false;
  }

  //--- ตรวจสอบคลังปลายทาง
  if(to_warehouse == ''){
    swal('กรุณาเลือกคลังปลายทาง');
    return false;
  }

  //--- ตรวจสอบว่าเป็นคนละคลังกันหรือไม่ (ต้องเป็นคนละคลังกัน)
  // if( from_warehouse == to_warehouse){
  //   swal('คลังต้นทางต้องไม่ตรงกับคลังปลายทาง');
  //   return false;
  // }

  console.log('from:'+from_warehouse);
  console.log('old : '+old_from_wh);
  console.log('to :' + to_warehouse);
  console.log('old :' + old_to_wh);
  //--- ตรวจสอบหากมีการเปลี่ยนคลัง ต้องเช็คก่อนว่ามีการทำรายการไปแล้วหรือยัง
  if(from_warehouse != old_from_wh || to_warehouse != old_to_wh)
  {
    $.ajax({
      url:HOME + 'is_exists_detail/'+code,
      type:'POST',
      cache:false,
      success:function(rs)
      {
        if(rs === 'exists')
        {
          swal({
            title:'Warning !',
            text:'มีการทำรายการแล้วไม่สามารถเปลี่ยนคลังได้',
            type:'warning'
          });

          return false;
        }
        else
        {
          do_update(code, date_add, from_warehouse, to_warehouse, remark);
        }
      }
    })
  }
  else
  {
    do_update(code, date_add, from_warehouse, to_warehouse, remark);
  }
}



function do_update(code, date_add, from_warehouse, to_warehouse, remark)
{
  load_in();
  //--- ถ้าไม่มีอะไรผิดพลาด ส่งข้อมูไป update
  $.ajax({
    url: HOME + 'update/'+code,
    type:'POST',
    cache:'false',
    data:{
      'date_add'    : date_add,
      'from_warehouse' : from_warehouse,
      'to_warehouse' : to_warehouse,
      'remark'      : remark
    },
    success:function(rs){
      load_out();

      var rs = $.trim(rs)
      if( rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);

      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}



//--- แก้ไขหัวเอกสาร
function getEdit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}



//---  บันทึกเอกสาร
function save(){
  var code = $('#move_code').val();

  //--- check temp
  $.ajax({
    url:HOME + 'check_temp_exists/'+code,
    type:'POST',
    cache:'false',
    success:function(rs){
      var rs = $.trim(rs);
      //--- ถ้าไม่มียอดค้างใน temp
      if( rs == 'not_exists'){
        //--- ส่งข้อมูลไป formula
        saveMove(code);
      }else{
        swal({
          title:'ข้อผิดพลาด !',
          text:'พบรายการที่ยังไม่โอนเข้าปลายทาง กรุณาตรวจสอบ',
          type:'error'
        });
      }
    }
  });
}



function saveMove(code)
{
  load_in();
  $.ajax({
    url:HOME + 'save_move/'+code,
    type:'POST',
    cache:false,
    success:function(rs) {
      load_out();
      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        if(ds.status == 'success') {
          swal({
            title:'Saved',
            text: 'บันทึกเอกสารเรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });

          setTimeout(function() {
            goDetail(code);
          }, 1200);
        }
        else if(ds.status == 'warning') {
          swal({
            title:'Warning',
            text:ds.message,
            type:'warning'
          }, () => {
            goDetail(code);
          });
        }
        else {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error'
          });
        }
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


$('#from_warehouse_code').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');

    if(arr.length == 2) {
      $(this).val(arr[0]);
      $('#from_warehouse_name').val(arr[1]);
    }
    else {
      $(this).val('');
      $('#from_warehouse_name').val('');
    }
  }
});


$('#to_warehouse_code').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');

    if(arr.length == 2) {
      $(this).val(arr[0]);
      $('#to_warehouse_name').val(arr[1]);
    }
    else {
      $(this).val('');
      $('#to_warehouse_name').val('');
    }
  }
});



$('#from_warehouse').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $('#from_warehouse_code').val(code);
      $(this).val(name);
    }
    else
    {
      $('#from_warehouse_code').val('');
      $(this).val('');
    }
  }
});


$('#to_warehouse').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $('#to_warehouse_code').val(code);
      $(this).val(name);
    }
    else
    {
      $('#to_warehouse_code').val('');
      $(this).val('');
    }
  }
});
