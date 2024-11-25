var percent = 0;
//var progress = setInterval(update_progress, 1000);

var count_style = 0;
var updated_style = 0;

var count_color = 0;
var updated_color = 0;

var count_size = 0;
var updated_size = 0;

var count_group = 0;
var updated_group = 0;

var count_sub_group = 0;
var updated_sub_group = 0;

var count_cate = 0;
var updated_cate = 0;

var count_kind = 0;
var updated_kind = 0;

var count_type = 0;
var updated_type = 0;

var count_brand = 0;
var updated_brand = 0;

var label = $('#txt-label');
var allow_sync = true;
var state;


var step = ['color','size', 'group', 'sub_group', 'cate', 'kind', 'type', 'brand', 'style'];

function syncData(){
  $("#btn-sync").addClass('hide');
  $('#btn-stop').removeClass('hide');
  $('#progress').removeClass('hide');
  $('#txt-percent').addClass('active');
  allow_sync = true;

  if(state === 'count_style'){
    count_update_style();
  }else if(state === 'update_style'){
    get_update_style();
  }else if(state === 'count_size'){
    count_update_size();
  }else if(state === 'update_size') {
    get_update_size();
  }else if(state === 'count_color'){
    count_update_color();
  }else if(state === 'update_color') {
    get_update_color();
  }else if(state === 'count_group'){
    count_update_group();
  }else if(state === 'update_group') {
    get_update_group();
  }else if(state === 'count_sub_group'){
    count_update_sub_group();
  }else if(state === 'update_sub_group') {
    get_update_sub_group();
  }else if(state === 'count_cate'){
    count_update_cate();
  }else if(state === 'update_cate') {
    get_update_cate();
  }else if(state === 'count_kind'){
    count_update_kind();
  }else if(state === 'update_kind') {
    get_update_kind();
  }else if(state === 'count_type'){
    count_update_type();
  }else if(state === 'update_type') {
    get_update_type();
  }else if(state === 'count_brand'){
    count_update_brand();
  }else if(state === 'update_brand') {
    get_update_brand();
  }else if(state === 'count_model'){
    count_update_model();
  }else if(state === 'update_model') {
    get_update_model();
  }else{
    count_update_color();
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

function count_update_color(){
  state = 'count_color';
  label.text('Collecting to update');
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  $.ajax({
    url:BASE_URL + 'sync_data/count_update_color' ,
    type:'GET',
    cache:false,
    data:{
      'date_upd' : color_date
    },
    success:function(rs){
      if(rs == 0){
        label.text('No need to update');
        count_update_size();
      }else{
        count_color = rs;
        label.text(rs + ' need to update');
        get_update_color();
      }
    }
  });
}


function get_update_color(){
  state = 'update_color';
  label.text('Updating Color '+ updated_color+' of '+ count_color);
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  if(updated_color < count_color){
    $.ajax({
      url:BASE_URL + 'sync_data/get_update_color/'+updated_color,
      type:'GET',
      cache:false,
      data:{
        'date_upd' : color_date
      },
      success:function(rs){
        updated_color += parseInt(rs);
        update_progress('color');
        if(updated_color == count_color){
          count_update_size();
        }else{
          get_update_color();
        }
      }
    })
  }else{
    count_update_size();
  }
}



function count_update_size(){
  state = 'count_size';
  label.text('Collecting to update');
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  $.ajax({
    url:BASE_URL + 'sync_data/count_update_size' ,
    type:'GET',
    cache:false,
    data:{
      'date_upd' : size_date
    },
    success:function(rs){
      if(rs == 0){
        label.text('No need to update');
        count_update_group();
      }else{
        count_size = rs;
        label.text(rs + ' need to update');
        get_update_size();
      }
    }
  });
}


function get_update_size(){
  state = 'update_size';
  label.text('Updating size '+ updated_size+' of '+ count_size);
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  if(updated_size < count_size){
    $.ajax({
      url:BASE_URL + 'sync_data/get_update_size/'+ updated_size,
      type:'GET',
      cache:false,
      data:{
        'date_upd' : size_date
      },
      success:function(rs){
        updated_size += parseInt(rs);
        update_progress('size');
        if(updated_size == count_size){
          count_update_group();
        }else{
          get_update_size();
        }
      }
    })
  }else{
    count_update_group();
  }
}



function count_update_group(){
  state = 'count_group';
  label.text('Collecting data to update');
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  $.ajax({
    url:BASE_URL + 'sync_data/count_update_group' ,
    type:'GET',
    cache:false,
    data:{
      'date_upd' : group_date
    },
    success:function(rs){
      if(rs == 0){
        label.text('No need to update');
        count_update_group();
      }else{
        count_group = rs;
        label.text(rs + ' need to update');
        get_update_group();
      }
    }
  });
}


function get_update_group(){
  state = 'update_group';
  label.text('Updating group '+ updated_group+' of '+ count_group);
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  if(updated_group < count_group){
    $.ajax({
      url:BASE_URL + 'sync_data/get_update_group/'+ updated_group,
      type:'GET',
      cache:false,
      data:{
        'date_upd' : group_date
      },
      success:function(rs){
        updated_group += parseInt(rs);
        update_progress('group');
        if(updated_group == count_group){
          count_update_sub_group();
        }else{
          get_update_group();
        }
      }
    })
  }else{
    count_update_sub_group();
  }
}



function count_update_sub_group(){
  state = 'count_sub_group';
  label.text('Collecting data to update');
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  $.ajax({
    url:BASE_URL + 'sync_data/count_update_sub_group' ,
    type:'GET',
    cache:false,
    data:{
      'date_upd' : sub_group_date
    },
    success:function(rs){
      if(rs == 0){
        label.text('No need to update');
        count_update_cate();
      }else{
        count_sub_group = rs;
        label.text(rs + ' need to update');
        get_update_sub_group();
      }
    }
  });
}


function get_update_sub_group(){
  state = 'update_sub_group';
  label.text('Updating sub_group '+ updated_sub_group+' of '+ count_sub_group);
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  if(updated_sub_group < count_sub_group){
    $.ajax({
      url:BASE_URL + 'sync_data/get_update_sub_group/'+ updated_sub_group,
      type:'GET',
      cache:false,
      data:{
        'date_upd' : sub_group_date
      },
      success:function(rs){
        updated_sub_group += parseInt(rs);
        update_progress('sub_group');
        if(updated_sub_group == count_sub_group){
          count_update_cate();
        }else{
          get_update_sub_group();
        }
      }
    })
  }else{
    count_update_cate();
  }
}



function count_update_cate(){
  state = 'count_cate';
  label.text('Collecting data to update');
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  $.ajax({
    url:BASE_URL + 'sync_data/count_update_cate' ,
    type:'GET',
    cache:false,
    data:{
      'date_upd' : cate_date
    },
    success:function(rs){
      if(rs == 0){
        label.text('No need to update');
        count_update_kind();
      }else{
        count_cate = rs;
        label.text(rs + ' need to update');
        get_update_cate();
      }
    }
  });
}


function get_update_cate(){
  state = 'update_cate';
  label.text('Updating Category '+ updated_cate + ' of '+ count_cate);
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  if(updated_cate < count_cate){
    $.ajax({
      url:BASE_URL + 'sync_data/get_update_cate/'+ updated_cate,
      type:'GET',
      cache:false,
      data:{
        'date_upd' : cate_date
      },
      success:function(rs){
        updated_cate += parseInt(rs);
        update_progress('cate');
        if(updated_cate == count_cate){
          count_update_kind();
        }else{
          get_update_cate();
        }
      }
    })
  }else{
    count_update_kind();
  }
}



function count_update_kind(){
  state = 'count_kind';
  label.text('Collecting data to update');
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  $.ajax({
    url:BASE_URL + 'sync_data/count_update_kind' ,
    type:'GET',
    cache:false,
    data:{
      'date_upd' : kind_date
    },
    success:function(rs){
      if(rs == 0){
        label.text('No need to update');
        count_update_kind();
      }else{
        count_kind = rs;
        label.text(rs + ' need to update');
        get_update_kind();
      }
    }
  });
}


function get_update_kind(){
  state = 'update_kind';
  label.text('Updating Kind '+ updated_kind + ' of '+ count_kind);
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  if(updated_kind < count_kind){
    $.ajax({
      url:BASE_URL + 'sync_data/get_update_kind/'+ updated_kind,
      type:'GET',
      cache:false,
      data:{
        'date_upd' : kind_date
      },
      success:function(rs){
        updated_kind += parseInt(rs);
        update_progress('kind');
        if(updated_kind == count_kind){
          count_update_type();
        }else{
          get_update_kind();
        }
      }
    })
  }else{
    count_update_type();
  }
}


function count_update_type(){
  state = 'count_type';
  label.text('Collecting data to update');
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  $.ajax({
    url:BASE_URL + 'sync_data/count_update_type' ,
    type:'GET',
    cache:false,
    data:{
      'date_upd' : type_date
    },
    success:function(rs){
      if(rs == 0){
        label.text('No need to update');
        count_update_type();
      }else{
        count_type = rs;
        label.text(rs + ' need to update');
        get_update_type();
      }
    }
  });
}


function get_update_type(){
  state = 'update_type';
  label.text('Updating type '+ updated_type + ' of '+ count_type);
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  if(updated_type < count_type){
    $.ajax({
      url:BASE_URL + 'sync_data/get_update_type/'+ updated_type,
      type:'GET',
      cache:false,
      data:{
        'date_upd' : type_date
      },
      success:function(rs){
        updated_type += parseInt(rs);
        update_progress('type');
        if(updated_type == count_type){
          count_update_brand();
        }else{
          get_update_type();
        }
      }
    })
  }else{
    count_update_brand();
  }
}


function count_update_brand(){
  state = 'count_brand';
  label.text('Collecting data to update');
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  $.ajax({
    url:BASE_URL + 'sync_data/count_update_brand' ,
    brand:'GET',
    cache:false,
    data:{
      'date_upd' : brand_date
    },
    success:function(rs){
      if(rs == 0){
        label.text('No need to update');
        count_update_brand();
      }else{
        count_brand = rs;
        label.text(rs + ' need to update');
        get_update_brand();
      }
    }
  });
}


function get_update_brand(){
  state = 'update_brand';
  label.text('Updating brand '+ updated_brand + ' of '+ count_brand);
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  if(updated_brand < count_brand){
    $.ajax({
      url:BASE_URL + 'sync_data/get_update_brand/'+ updated_brand,
      brand:'GET',
      cache:false,
      data:{
        'date_upd' : brand_date
      },
      success:function(rs){
        updated_brand += parseInt(rs);
        update_progress('brand');
        if(updated_brand == count_brand){
          count_update_style();
        }else{
          get_update_brand();
        }
      }
    })
  }else{
    count_update_style();
  }
}



function count_update_style(){
  state = 'count_style';
  label.text('Collecting data to update');
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  $.ajax({
    url:BASE_URL + 'sync_data/count_update_style' ,
    style:'GET',
    cache:false,
    data:{
      'date_upd' : style_date
    },
    success:function(rs){
      if(rs == 0){
        label.text('No need to update');
        finish_sync();
        swal({
          title:'Complete',
          text:'Sync Completed',
          type:'success',
          timer:2000
        });
      }else{
        count_style = rs;
        label.text(rs + ' need to update');
        get_update_style();
      }
    }
  });
}


function get_update_style(){
  state = 'update_style';
  label.text('Updating style '+ updated_style + ' of '+ count_style);
  if(allow_sync == false){
    finish_sync();
    return false;
  }

  if(updated_style < count_style){
    $.ajax({
      url:BASE_URL + 'sync_data/get_update_style/'+ updated_style,
      style:'GET',
      cache:false,
      data:{
        'date_upd' : style_date
      },
      success:function(rs){
        updated_style += parseInt(rs);
        update_progress('style');
        if(updated_style == count_style){
          finish_sync();
          swal({
            title:'Complete',
            text:'Sync Completed',
            type:'success',
            timer:2000
          });
        }else{
          get_update_style();
        }
      }
    })
  }else{
    finish_sync();
    swal({
      title:'Complete',
      text:'Sync Completed',
      type:'success',
      timer:2000
    });
  }
}









function update_progress(type){
  if(type === 'color'){
    percent = (updated_color/count_color) * 100;
  }else if(type === 'size'){
    percent = (updated_size/count_size) * 100;
  }else if(type === 'group'){
    percent = (updated_group/count_group) * 100;
  }else if(type === 'sub_group'){
    percent = (updated_sub_group/count_sub_group) * 100;
  }else if(type === 'cate'){
    percent = (updated_cate/count_cate) * 100;
  }else if(type === 'kind'){
    percent = (updated_kind/count_kind) * 100;
  }else if(type === 'type'){
    percent = (updated_type/count_type) * 100;
  }else if(type === 'brand'){
    percent = (updated_brand/count_brand) * 100;
  }else if(type === 'style'){
    percent = (updated_style/count_style) * 100;
  }

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
