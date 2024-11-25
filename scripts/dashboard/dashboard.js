function change_state(order_code){
  $.ajax({
    url:BASE_URL + 'orders/orders/order_state_change',
    type:'POST',
    cache:false,
    data:{
      'order_code' : order_code,
      'state' : 3
    },
    success:function(rs){
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'ปล่อยจัดสินค้าเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1500);
      }else{
        swal({
          title:'Error!!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}
