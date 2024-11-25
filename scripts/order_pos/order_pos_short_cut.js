var map = {}; // You could also use an array

onkeydown = onkeyup = function(e){
  e = e || event; // to deal with IE

  map[e.keyCode] = e.type == 'keydown';

  if(map[17] && (map[97] || map[49])) {  //-- CTRL + Numpad 1 OR Keyboard 1
    e.preventDefault();
    let payment_code = $('#role-1-code').val();
    setPayment(payment_code, 1);
  }
  else if(map[17] && (map[98] || map[50])) {  //-- CTRL + Numpad 2 OR Keyboard 2
    e.preventDefault();
    let payment_code = $('#role-2-code').val();
    setPayment(payment_code, 2);
  }
  else if(map[17] && (map[99] || map[51])) {  //-- CTRL + Numpad 3 OR Keyboard 3
    e.preventDefault();
    let payment_code = $('#role-3-code').val();
    setPayment(payment_code, 3);
  }
  else if(map[17] && (map[100] || map[52])) {  //-- CTRL + Numpad 4 OR Keyboard 4
    e.preventDefault();
    let payment_code = $('#role-6-code').val();
    setPayment(payment_code, 6);
  }
  else if(map[17] && (map[101] || map[53])) {  //-- CTRL + Numpad 5 OR Keyboard 5
    e.preventDefault();
    let payment_code = $('#role-7-code').val();
    setPayment(payment_code, 7);
  }
  else if(map[17] && map[46]) {  //--- CTRL + Delete
    e.preventDefault();
    removeItems();
  }
  else if(map[112]) { //-- F1
    e.preventDefault();
    holdBill();
  }
  else if(map[113]) { //-- F2
    e.preventDefault();
    showHoldBill();
  }
  else if(map[114]) { //-- F3
    e.preventDefault();
    returnList();
  }
  else if(map[115]) { //-- F4
    e.preventDefault();
    billList();
  }
  else if(map[117]) {  //--- F6
    e.preventDefault();
    cashIn();
  }
  else if(map[118]) { //--- F7
    e.preventDefault();
    cashOut();
  }
  else if(map[119]) { //--- F8
    e.preventDefault();
    changeEmployee();
  }
  else if(map[120]) { //--- F9
    e.preventDefault();
    findItem();
  }
  else if(map[9]) { //--- tab
    e.preventDefault();
    $('#bill-disc-label').focus()
    $('#bill-disc-label').select()
  }
  if(map[121]) { //-- F10
    e.preventDefault();
    reCalDiscount();
  }
  else if(map[123]) { //-- F12
    e.preventDefault();
    showPayment();
  }
  else if(map[36]) { //--- Home
    e.preventDefault();
    barcodeFocus();
  }
}



$('#item-barcode').keydown(function(e) {
	//---- กดลูกศรซ้าย ไป focus ที่ช่องจำนวน
	if(e.keyCode == 37) {
    e.preventDefault();
		$('#item-qty').focus().select();
		return;
	}

	//---- กดลูกสรขึ้น เพิ่มจำนวน
	if(e.keyCode === 38) {
		let qty = parseDefault(parseFloat($('#item-qty').val()), 1.00);
		qty++;

		$('#item-qty').val(qty);

		return;
	}

	//--- กดลูกศรลง เพื่อลดจำนวน
	if(e.keyCode === 40) {
		let qty = parseDefault(parseFloat($('#item-qty').val()), 1.00);

		if(qty > 1) {
			qty--;
			$('#item-qty').val(qty);
		}

		return;
	}

  //--- กดลูกศรขวา ไป focus ที่ช่อง ของแถม
  if(e.keyCode === 39) {
    e.preventDefault();
    $('#free-item-barcode').focus().select();
		return;
  }
});


$('#free-item-barcode').keydown(function(e) {
  if(e.keyCode === 37) {
    e.preventDefault();
    $('#item-barcode').focus().select();

    return;
  }

  //--- กดลูกศรขวา ไป focus ที่ช่อง ของแถม
  if(e.keyCode === 39) {
    e.preventDefault();
    $('#pd-box').focus().select();
    return;
  }

  //---- กดลูกสรขึ้น เพิ่มจำนวน
	if(e.keyCode === 38) {
		let qty = parseDefault(parseFloat($('#item-qty').val()), 1.00);
		qty++;

		$('#item-qty').val(qty);

		return;
	}

	//--- กดลูกศรลง เพื่อลดจำนวน
	if(e.keyCode === 40) {
		let qty = parseDefault(parseFloat($('#item-qty').val()), 1.00);

		if(qty > 1) {
			qty--;
			$('#item-qty').val(qty);
		}

		return;
	}

  if(e.keyCode === 13) {
    getItemDetail();
  }
});


$('#pd-box').keydown(function(e) {
  if(e.keyCode === 37) {
    e.preventDefault();
    $('#item-barcode').focus().select();

    return;
  }

  if(e.keyCode === 13) {
    getItemDetail();
  }
});



$('#item-qty').keydown(function(e) {

  //--- กด enter ไป focus ที่ช่องสแกนบาร์โค้ด
  if(e.keyCode === 13 || e.keyCode === 39) {
    $('#item-barcode').focus();

    return;
  }

  //---- กดลูกสรขึ้น เพิ่มจำนวน
	if(e.keyCode === 38) {
    e.preventDefault();
		let qty = parseDefault(parseFloat($('#item-qty').val()), 1.00);
		qty++;

		$('#item-qty').val(qty);

		return;
	}

	//--- กดลูกศรลง เพื่อลดจำนวน
	if(e.keyCode === 40) {
    e.preventDefault();
		let qty = parseDefault(parseFloat($('#item-qty').val()), 1.00);

		if(qty > 1) {
			qty--;
			$('#item-qty').val(qty);
		}

		return;
	}
});


function activeNextRow() {
  let rows = $('#item-table tr');
  let current = rows.filter('.active-row').index();
  if(current < rows.length -1) {
    rows.removeClass('active-row');
    rows.eq(current + 1).addClass('active-row');
  }

  return;
}


function activePrevRow() {
  let rows = $('#item-table tr');
  let current = rows.filter('.active-row').index();
  if(current > 0) {
    rows.removeClass('active-row');
    rows.eq(current - 1).addClass('active-row');
  }

  return;
}
