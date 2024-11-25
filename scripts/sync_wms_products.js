var percent = 0;
//var progress = setInterval(update_progress, 1000);

var count_items = 0;
var updated_items = 0;
var label = $('#txt-label');

var item_last_sync;

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
  }else if(state === 'update_items') {
    get_update_items();
  }else{
    count_update_items();
  }
}


function stopSync(){
  allow_sync = false;
}

function finish_sync(end){
  $('#btn-stop').addClass('hide');
  $("#btn-sync").removeClass('hide');
  $('#txt-percent').removeClass('active');
  if(end !== undefined){

    swal({
      title:'Sync Completed',
      text:'Items : '+ updated_items,
      type:'success',
      html:true
    });

    count_items = 0;
    updated_itmes = 0;
  }

}


function count_update_items(){
  state = 'count_items';
  label.text('Collecting Items to update');
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  $.ajax({
    url:BASE_URL + 'sync_wms_items/count_update_items',
    type:'GET',
    cache:false,
    success:function(rs){
      if(rs == 0){
        label.text('No Item to update');
        finish_sync();
      }else{
        count_items = rs;
        label.text(rs + ' items need to update');
        get_update_items();
      }
    }
  });
}


function get_update_items(){
  state = 'update_items';
  label.text('Items Updating '+ updated_items +' of '+ count_items);

  if(allow_sync == false){
    finish_sync();
    return false;
  }

  if(updated_items < count_items){
    $.ajax({
      url:BASE_URL + 'sync_wms_items/get_update_items/'+ updated_items,
      type:'GET',
      cache:false,
      data:{
        'last_sync' : item_last_sync
      },
      success:function(rs){
        if(!isNaN(parseInt(rs))){
          updated_items += parseInt(rs);
          update_progress('item');
          if((updated_items + 1) == count_items){
            swal({
              title:'Complete',
              text:'All items updated',
              type:'success',
              timer:1000
            });

            finish_sync('end');

          }else{
            get_update_items();
          }
        }else{
          swal({
            title:'Error',
            text:'Something went wrong',
            type:'error'
          });

          finish_sync('end');
        }

      }
    })
  }else{
    finish_sync('end');
  }
}


function update_progress() {

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



function get_count_items(){
  $.get(BASE_URL + 'sync_wms_items/count_items', function(rs){
    count_items = rs;
  });
}
