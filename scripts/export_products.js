var percent = 0;
//var progress = setInterval(update_progress, 1000);

var count_items = 0;
var updated_items = 0;

var label = $('#txt-label');
var allow_sync = true;
var state;


function syncData(){
  $("#btn-sync").addClass('hide');
  $('#btn-stop').removeClass('hide');
  $('#progress').removeClass('hide');
  $('#txt-percent').addClass('active');
  allow_sync = true;

  if(state === 'count_items'){
    count_update_items();
  }else if(state === 'update_items'){
    get_update_items();
  }else {
    count_update_items();
  }
}


function stopSync(){
  allow_sync = false;
}

function finish_sync(){
  $('#btn-stop').addClass('hide');
  $("#btn-sync").removeClass('hide');
  $('#txt-percent').removeClass('active');
}

function count_update_items(){
  state = 'count_items';
  label.text('Collecting data to update');
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  $.ajax({
    url:BASE_URL + 'sync_data/count_update_items' ,
    type:'GET',
    cache:false,
    data:{
      'date_upd' : items_date
    },
    success:function(rs){
      if(rs == 0){
        label.text('No need to update');
        finish_sync();
        swal({
          title:'Completed',
          text:'ข้อมูลเป็นปัจจุบันแล้ว',
          type:'success',
          timer: 1500
        });
      }else{
        count_items = rs;
        label.text(rs + ' need to update');
        get_update_items();
      }
    }
  });
}


function get_update_items(){
  state = 'update_items';
  label.text('Updating Items '+ updated_items +' of '+ count_items);
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  if(updated_items < count_items){
    $.ajax({
      url:BASE_URL + 'sync_data/get_update_items/' + updated_items,
      type:'GET',
      cache:false,
      data:{
        'date_upd' : items_date
      },
      success:function(rs){
        updated_items += parseInt(rs);
        update_progress('items');
        if(updated_items == count_items){
          finish_sync();
          swal({
            title:'Completed',
            text:'ข้อมูลเป็นปัจจุบันแล้ว',
            type:'success',
            timer: 1500
          });
        }else{
          get_update_items();
        }
      }
    })
  }else{
    finish_sync();
    swal({
      title:'Completed',
      text:'ข้อมูลเป็นปัจจุบันแล้ว',
      type:'success',
      timer: 1500
    });
  }
}


function update_progress(type){
  percent = (updated_items/count_items) * 100;
  var percentage;
  if(percent > 100){
    percentage = 100;
  }else{
    percentage = parseInt(percent);
  }

  $('#txt-percent').attr("data-percent", percentage + "%");
  $('#progress-bar').css("width", percentage+"%");

}


function clear_progress(){
  percent = 0;
  $('#txt-percent').attr("data-percent", percent + "%");
  $('#progress-bar').css("width", percent+"%");
}
