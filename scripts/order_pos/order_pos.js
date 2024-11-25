var HOME = BASE_URL + 'orders/order_pos/';

function getPosDeviceId() {
	let data = localStorage.getItem('IXPOSDATA');
	if(data !== null && data !== undefined) {
    pos = JSON.parse(data);
		return pos.deviceId;
  }
	else {
		swal({
			title:'Error!',
			text:'ไม่พบข้อมูลที่ Register ไว้',
			type:'error'
		});

		return false;
	}
}

function goToPOS(deviceId) {
	window.location.href = HOME + 'main/'+deviceId;
}


function salePage() {
	let deviceId = getPosDeviceId();

	if(deviceId.length) {
		window.location.href = HOME + 'main/'+deviceId;
	}
}

function billList() {
	let shop_id = $('#shop_id').val();
	target = HOME + 'bill_list/'+shop_id;
	window.location.href = target;
}

function returnList() {
	let shop_id = $('#shop_id').val();
	let deviceId = getPosDeviceId();

	if(deviceId.length) {
		window.location.href = HOME + 'return_list/'+shop_id+'/'+deviceId;
	}
}


function downPaymentList() {
	let shop_id = $('#shop_id').val();
	let deviceId = getPosDeviceId();

	if(deviceId.length) {
		window.location.href = HOME + 'down_payment_list/'+shop_id+'/'+deviceId;
	}
}

function newBill() {
	let deviceId = getPosDeviceId();
	goToPOS(deviceId);
}


function printBill(code) {
	let width = 400;
	let height = 600;
	let center = (window.innerWidth - width)/2;
	let middle = (window.innerHeight - height)/2;
	let prop = "width="+width+", height="+height+", left="+center+", top="+middle+", scrollbars=yes";
	let target = HOME + 'print_slip/'+code;
	window.open(target, '_blank', prop);
}


function changeEmployee() {
	let deviceId = getPosDeviceId();

	$.ajax({
		url:BASE_URL + 'users/authentication/pos_logout',
		type:'POST',
		cache:false,
		success:function(rs) {
			window.location.href = BASE_URL + 'users/authentication/pos_login/'+deviceId;
		}
	});
}
