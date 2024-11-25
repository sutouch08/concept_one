$(document).ready(function() {
  syncData();
});


function syncData(){
  setTimeout(function(){
    syncWarehouse();
  },1000);
}

//--1 Sync Warehouse
function syncWarehouse(){
    $('body').append('start  importing : Warehouse ...<br/>');
  $.get(BASE_URL +'sync_data/syncWarehouse', function(){
    $('body').append('finish import : Warehouse ...<br/>');
    $('body').append('============================================ <br/>');
    setTimeout(function(){
      syncZone();
    },1000);
  });
}


//--- 2. Sync zone
function syncZone(){
    $('body').append('start  importing : Zone ...<br/>');
  $.get(BASE_URL+'sync_data/syncZone', function(){
    $('body').append('finish import : Zone ...<br/>');
    $('body').append('============================================ <br/>');
    syncCustomer();
  });
}




//--- 3. Sync customer
function syncCustomer(){
    $('body').append('start  importing : Customers ...<br/>');
  $.get(BASE_URL+'sync_data/syncCustomer', function(){
    $('body').append('finish import : Customers ...<br/>');
    $('body').append('============================================ <br/>');
    syncGoodReceivePo();
  });
}

//--- 4. sync OPDN
function syncGoodReceivePo(){
    $('body').append('start    updating WR .... <br/>');
  $.get(BASE_URL + 'sync_data/syncReceivePoInvCode', function(rs){
    $('body').append('finished update WR : '+rs+'<br/>');
    $('body').append('============================================ <br/>');
    syncReceiveTranformInvCode();
  })
}


//--- 4. sync OIGN
function syncReceiveTranformInvCode(){
    $('body').append('start    updating RT .... <br/>');
  $.get(BASE_URL + 'sync_data/syncReceiveTransformInvCode', function(rs){
    $('body').append('finished update RT : ' + rs + ' <br/>');
    $('body').append('============================================ <br/>');
    syncDeliveryOrder();
  })
}


//--- 5. sync ODLN
function syncDeliveryOrder(){
    $('body').append('start    updating WO .... <br/>');
  $.get(BASE_URL + 'sync_data/syncOrderInvCode', function(rs){
    $('body').append('finished update WO : '+ rs +' <br/>');
    $('body').append('============================================ <br/>');
    syncDeliveryPosInvoice();
  })
}

//--- 5. sync ODLN
function syncDeliveryPosInvoice(){
    $('body').append('start    updating POS Invoice .... <br/>');
  $.get(BASE_URL + 'sync_data/syncOrderPosInvCode', function(rs){
    $('body').append('finished update Pos Invoice : '+ rs +' <br/>');
    $('body').append('============================================ <br/>');
    syncDeliverySponsor();
  })
}

//--- 5. sync ODLN
function syncDeliverySponsor(){
    $('body').append('start    updating WS, WU .... <br/>');
  $.get(BASE_URL + 'sync_data/syncSponsorInvCode', function(rs){
    $('body').append('finished update WS, WU '+ rs +' <br/>');
    $('body').append('============================================ <br/>');
    syncDeliveryConsignment();
  })
}


//--- 5. sync ODLN
function syncDeliveryConsignment(){
    $('body').append('start    updating WC .... <br/>');
  $.get(BASE_URL + 'sync_data/syncConsignmentInvCode', function(rs){
    $('body').append('finished update WC '+ rs +' <br/>');
    $('body').append('============================================ <br/>');
    syncOrderTransform();
  })
}


//--- 6.1 sync OWTR (WQ, WV)
function syncOrderTransform(){
  $('body').append('start    updating WQ, WV .... <br/>');
  $.get(BASE_URL + 'sync_data/syncOrderTransformInvCode', function(rs){
    $('body').append('finished update WQ, WV : '+ rs + ' <br/>');
    $('body').append('============================================ <br/>');
    syncOrderTransfer();
  })
}


//--- 6.2 sync OWTR (WT)
function syncOrderTransfer(){
  $('body').append('start    updating WT .... <br/>');
  $.get(BASE_URL + 'sync_data/syncOrderTransferInvCode', function(rs){
    $('body').append('finished update WT : '+ rs + ' <br/>');
    $('body').append('============================================ <br/>');
    syncOrderLend();
  })
}


//--- 6.3 sync OWTR (WL)
function syncOrderLend(){
  $('body').append('start    updating WL .... <br/>');
  $.get(BASE_URL + 'sync_data/syncOrderLendInvCode', function(rs){
    $('body').append('finished update WL : '+ rs + ' <br/>');
    $('body').append('============================================ <br/>');
    syncTransfer();
  })
}


//--- 7. sync OWTR (WW)
function syncTransfer(){
    $('body').append('start    updating WW .... <br/>');
  $.get(BASE_URL + 'sync_data/syncTransferInvCode', function(rs){
    $('body').append('finished update WW : '+ rs + ' <br/>');
    $('body').append('============================================ <br/>');
    syncMoveInvCode();
  })
}


//--- 8. sync OWTR (MV)
function syncMoveInvCode(){
    $('body').append('start    updating MV .... <br/>');
  $.get(BASE_URL + 'sync_data/syncMoveInvCode', function(rs){
    $('body').append('finished update MV : '+ rs + ' <br/>');
    $('body').append('============================================ <br/>');
    syncAdjustGoodsIssueCode();
  })
}


//--- 9. sync OIGE (AJ-IGE)
function syncAdjustGoodsIssueCode(){
    $('body').append('start    updating AJ-IGE .... <br/>');
  $.get(BASE_URL + 'sync_data/syncAdjustGoodsIssueCode', function(rs){
    $('body').append('finished update AJ-IGE : '+ rs + ' <br/>');
    $('body').append('============================================ <br/>');
    syncAdjustGoodsReceiveCode()
  })
}

//--- 10. sync OIGN (AJ-IGN)
function syncAdjustGoodsReceiveCode(){
    $('body').append('start    updating AJ-IGN .... <br/>');
  $.get(BASE_URL + 'sync_data/syncAdjustGoodsReceiveCode', function(rs){
    $('body').append('finished update AJ-IGN : '+ rs + ' <br/>');
    $('body').append('============================================ <br/>');
    syncReturnOrderCode();
  })
}


//--- 11. sync ORDN (SM)
function syncReturnOrderCode(){
    $('body').append('start    updating SM .... <br/>');
  $.get(BASE_URL + 'sync_data/syncReturnOrderCode', function(rs){
    $('body').append('finished update SM : '+ rs + ' <br/>');
    $('body').append('============================================ <br/>');
    syncTransformGoodsIssueCode();
  })
}


//--- 12. sync OIGE (WG)
function syncTransformGoodsIssueCode(){
    $('body').append('start    updating WG .... <br/>');
  $.get(BASE_URL + 'sync_data/syncTransformGoodsIssueCode', function(rs){
    $('body').append('finished update WG : '+ rs + ' <br/>');
    $('body').append('============================================ <br/>');
    syncConsignSoldInvCode();
  })
}

//--- 13. sync ODLN (WM)
function syncConsignSoldInvCode(){
    $('body').append('start    updating WM .... <br/>');
  $.get(BASE_URL + 'sync_data/syncConsignSoldInvCode', function(rs){
    $('body').append('finished update WM : '+ rs + ' <br/>');
    $('body').append('============================================ <br/>');
    syncConsignmentSoldInvCode();
  })
}

//--- 14. sync ODLN (CN.WD) //-- ก้อน consignment
function syncConsignmentSoldInvCode(){
    $('body').append('start    updating WD .... <br/>');
  $.get(BASE_URL + 'sync_data/syncConsignmentSoldInvCode', function(rs){
    $('body').append('finished update WD : '+ rs + ' <br/>');
    $('body').append('============================================ <br/>');
    $('body').append('All done!');
    window.close();
  })
}
