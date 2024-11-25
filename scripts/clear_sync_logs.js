$(document).ready(function() {
  clear_logs();
});


function clear_logs(){
  setTimeout(function(){
    clearSyncLogs();
  },1000);
}

//--- 10. sync OIGN (AJ-IGN)
function clearSyncLogs(){
  $.get(BASE_URL + 'sync_data/clear_old_logs/'+ days, function(rs){
    $('body').append(rs);
    window.close();
  });
}
