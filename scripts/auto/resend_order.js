var exported = 0;
var i = 1;
var limit = 0;
var ds;
$(document).ready(function() {
  doExport();
});


function addlog(text){
  var el = document.createElement('P');
  el.innerHTML = (text);
  $('#result').prepend(el);
}

function clearlog(){
  $('#result').html('');
}

var data = [
  'WO-200509047',
  'WO-200509046',
  'WO-200509045',
  'WO-200509044',
  'WO-200509043',
  'WO-200509042',
  'WO-200509041',
  'WO-200509040',
  'WO-200509039',
  'WO-200509038',
  'WO-200509037',
  'WO-200509036',
  'WO-200509035',
  'WO-200509034',
  'WO-200509033',
  'WO-200509032',
  'WO-200509031',
  'WO-200509030',
  'WO-200509028',
  'WO-200509027',
  'WO-200509026',
  'WO-200509025',
  'WO-200509024',
  'WO-200509023',
  'WO-200509022',
  'WO-200509021',
  'WO-200509020',
  'WO-200509019',
  'WO-200509018',
  'WO-200509017',
  'WO-200509016',
  'WO-200509015',
  'WO-200509014',
  'WO-200509013',
  'WO-200509012',
  'WO-200509011',
  'WO-200509010',
  'WO-200509009',
  'WO-200509008',
  'WO-200509007',
  'WO-200509006',
  'WO-200509005',
  'WO-200509004',
  'WO-200509003',
  'WO-200509002',
  'WO-200509001',
  'WO-200509000'
];

function doExport(){
  data.forEach(confirmBill)
}


function confirmBill(value, index, array){
    $.ajax({
  		url: BASE_URL + 'inventory/delivery_order/manual_export/'+value,
  		type:'POST',
  		cache:'false',
  		// data:{
  		// 	'order_code' : value
  		// },
  		success:function(rs){
  			var rs = $.trim(rs);
        let no = i + 1;
  			addlog(no + " : " + value + " : " +rs);
        update_stat();
  		}
  	});
}


function update_stat(){
  exported++;
  $('#stat-qty').text(exported);
}


function isJson(str){
	try{
		JSON.parse(str);
	}catch(e){
		return false;
	}
	return true;
}
