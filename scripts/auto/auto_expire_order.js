function run(){
  $.ajax({
    url:HOME + '/auto_expire_order',
    type:'POST',
    cache:false,
    success:function(rs){
      window.close();
    }
  });
}


$(document).ready(function(){
  run();
})
