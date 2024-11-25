
var i = 0;
var limit = 10;
var ds;
$(document).ready(function() {
  syncData();
});


function getData(){
  var from_date = '2019-04-01 00:00:00';
  var to_date = '2020-06-30 23:59:59';
  $.ajax({
    url: HOME + "/get_move_list",
    type:'GET',
    cache:false,
    data:{
      'from_date' : from_date,
      'to_date' : to_date,
      'limit' : limit
    },
    success:function(rs){
      if(isJson(rs)){
        ds = $.parseJSON(rs);
        limit = ds.length;
        document.write(ds.length + " document to export");
        do_export();
      }else{
        window.close();
      }
    }
  })
}

function syncData(){
  var data = getData();
  if(data !== false){

  }
}


function do_export(){
  if(i === limit){
    window.close();
  }else{
    $.ajax({
      url:HOME + '/export_move',
      type:'POST',
      cache:false,
      data:{
        'code' : ds[i]
      },
      success:function(rs){
        document.write("export "+ds[i]+ " " + rs + "<br/>");
        i++;
        do_export();
      }
    })
  }
}


function isJson(str){
	try{
		JSON.parse(str);
	}catch(e){
		return false;
	}
	return true;
}
