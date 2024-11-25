var exported = 0;
var i = 0;
var limit = 0;
var ds;
$(document).ready(function() {
  getData();
});


function addlog(text){
  var el = document.createElement('P');
  el.innerHTML = (text);
  $('#result').prepend(el);
}

function clearlog(){
  $('#result').html('');
}

function getData(){
  var from_date = '2020-01-01 00:00:00';
  var to_date = '2020-06-30 23:59:59';
  addlog("Get Order Between " + from_date + " AND " + to_date);
  i = 0;
  limit = 100;
  $.ajax({
    url: HOME + "/get_delivery_list",
    type:'GET',
    cache:false,
    data:{
      'from_date' : from_date,
      'to_date' : to_date,
      'limit' : limit
    },
    success:function(rs){
      if(isJson(rs)){
        clearlog();
        ds = $.parseJSON(rs);
        limit = ds.length;
        addlog("พบ "+ limit + " ออเดอร์");
        confirmBill();
      }else{
        addlog("พบ 0 ออเดอร์");
        window.close();
      }
    }
  })
}



function confirmBill(){
  if(i === limit){
    //clearlog();
    getData();
    //window.close();
  }else{
    $.ajax({
  		url: BASE_URL + 'inventory/delivery_order/confirm_order',
  		type:'POST',
  		cache:'false',
  		data:{
  			'order_code' : ds[i]
  		},
  		success:function(rs){
  			var rs = $.trim(rs);
        let no = i + 1;
  			addlog(no + " : " + ds[i] + " : " +rs);
        update_stat();
        i++;
        confirmBill();
  		}
  	});
  }

}



function update_stat(){
  exported++;
  $('#stat-qty').text(exported);
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
