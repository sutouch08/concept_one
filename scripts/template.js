
window.addEventListener('load', () => {
  let uuid = get_uuid();

  if(uuid == "" || uuid == null || uuid == undefined) {
    uid = generateUID();

		localStorage.setItem('ix_uuid', uid);
  }
});


function get_uuid() {
	return localStorage.getItem('ix_uuid');
}

function go_to(page){
	window.location.href = BASE_URL + page;
}


function checkError(){
	if($('#error').length){
		swal({
			title:'Error!',
			text: $('#error').val(),
			type:'error'
		})
	}

	if($('#success').length){
			swal({
				title:'Success',
				text:$('#success').val(),
				type:'success',
				timer:1500
			});
	}
}


//--- save side bar layout to cookie
function toggle_layout(){
	var sidebar_layout = getCookie('sidebar_layout');
	if(sidebar_layout == 'menu-min'){
		setCookie('sidebar_layout', '', 90);
	}else{
		setCookie('sidebar_layout', 'menu-min', 90);
	}
}


function load_in(){
	$("#loader").css("display","block");
	$('#loader-backdrop').css('display', 'block');
	$("#loader").animate({opacity:0.8},300);
}



function load_out(){
	$("#loader").animate({
		opacity:0
	},300,
	function() {
		$("#loader").css("display","none");
		$('#loader-backdrop').css('display', 'none');
	});
}



function set_error(el, label, message){
	el.addClass('has-error');
	label.text(message);
}


function clear_error(el, label){
	el.removeClass('has-error');
	label.text('');
}



function isDate(txtDate){
	 var currVal = txtDate;
	 if(currVal == '')
	    return false;
	  //Declare Regex
	  var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
	  var dtArray = currVal.match(rxDatePattern); // is format OK?
	  if (dtArray == null){
		     return false;
	  }
	  //Checks for mm/dd/yyyy format.
	  dtDay= dtArray[1];
	  dtMonth = dtArray[3];
	  dtYear = dtArray[5];
	  if (dtMonth < 1 || dtMonth > 12){
	      return false;
	  }else if (dtDay < 1 || dtDay> 31){
	      return false;
	  }else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31){
	      return false;
	  }else if (dtMonth == 2){
	     var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
	     if (dtDay> 29 || (dtDay ==29 && !isleap)){
	          return false;
		 }
	  }
	  return true;
	}



	function removeCommas(str) {
	    while (str.search(",") >= 0) {
	        str = (str + "").replace(',', '');
	    }
	    return str;
	}




	function addCommas(number){
		 return (
		 	number.toString()).replace(/^([-+]?)(0?)(\d+)(.?)(\d+)$/g, function(match, sign, zeros, before, decimal, after) {
		 		var reverseString = function(string) { return string.split('').reverse().join(''); };
		 		var insertCommas  = function(string) {
						var reversed   = reverseString(string);
						var reversedWithCommas = reversed.match(/.{1,3}/g).join(',');
						return reverseString(reversedWithCommas);
						};
					return sign + (decimal ? insertCommas(before) + decimal + after : insertCommas(before + after));
					});
	}




//**************  Handlebars.js  **********************//
function render(source, data, output){
	var template = Handlebars.compile(source);
	var html = template(data);
	output.html(html);
}

function render_prepend(source, data, output){
	var template = Handlebars.compile(source);
	var html = template(data);
	output.prepend(html);
}


function render_append(source, data, output){
	var template = Handlebars.compile(source);
	var html = template(data);
	output.append(html);
}


function render_after(source, data, output) {
	var template = Handlebars.compile(source);
	var html = template(data);
	$(html).insertAfter(output);
}

function render_before(source, data, output) {
	var template = Handlebars.compile(source);
	var html = template(data);
	$(html).insertBefore(output);
}



function set_rows()
{
	var rows = $('#set_rows').val();
	$.ajax({
		url:BASE_URL+'tools/set_rows',
		type:'POST',
		cache:false,
		data:{
			'set_rows' : rows
		},
		success:function(){
			window.location.reload();
		}
	});
}




$('#set_rows').keyup(function(e){
	if(e.keyCode == 13 && $(this).val() > 0){
		set_rows();
	}
});




function reIndex(className){
  if(className === undefined || className === null) {
    $('.no').each(function(index, el) {
      no = index +1;
      $(this).text(addCommas(no));
    });
  }
  else {
    $('.'+className).each(function(index, el) {
      no = index +1;
      $(this).text(addCommas(no));
    })
  }
}



var downloadTimer;
function get_download(token)
{
	load_in();
	downloadTimer = window.setInterval(function(){
		var cookie = getCookie("file_download_token");
		if(cookie == token)
		{
			finished_download();
		}
	}, 1000);
}



function finished_download()
{
	window.clearInterval(downloadTimer);
	deleteCookie("file_down_load_token");
	load_out();
}



function isJson(str){
	try{
		JSON.parse(str);
	}catch(e){
		return false;
	}
	return true;
}



function printOut(url)
{
	var center = ($(document).width() - 800) /2;
	window.open(url, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}



function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = "expires="+d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
  var name = cname + "=";
  var ca = document.cookie.split(';');
  for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function deleteCookie( name ) {
  document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}


function parseDefault(value, def){
	if(isNaN(value)){
		return def; //--- return default value
	}

	return value;
}

function parseDefaultInt(value, def) {
  let val = parseInt(value);
  return isNaN(val) ? def : val;
}


function parseDefaultFloat(value, def) {
  let val = parseFloat(value);
  return isNaN(val) ? def : val;
}


function parseDiscountPercent(price, discAmount)
{
  if(price > 0 && discAmount > 0 && price > discAmount)
  {
    return (discAmount/price) * 100
  }

  return 0.00;
}


function parseDiscountAmount(discount_label, price)
{
	var discAmount = 0;
  var bPrice = price;

	if(discount_label != '' && discount_label != 0)
	{
		var arr = discount_label.split('+');
		arr.forEach(function(item, index){
			var i = index + 1;
			if(i < 4){
				var disc = item.split('%');
				var value = parseDefault(parseFloat(disc[0]), 0);

				if(disc.length == 2) {
          var cPrice = price == 0 ? bPrice : price;
					var amount = (value * 0.01) * cPrice;
					discAmount += amount;
					price -= amount;
				}
        else {
					discAmount += value;
					price -= value;
				}
			}
		});
	}

	return discAmount;
}


//--- return discount array
function parseDiscount(discount_label, price)
{
	var discLabel = {
		"discLabel1" : 0,
		"discUnit1" : '',
		"discLabel2" : 0,
		"discUnit2" : '',
		"discLabel3" : 0,
		"discUnit3" : '',
		"discountAmount" : 0
	};

	if(discount_label != '' && discount_label != 0)
	{
		var arr = discount_label.split('+');
		arr.forEach(function(item, index){
			var i = index + 1;
			if(i < 4){
				var disc = item.split('%');
				var value = parseDefault(parseFloat(disc[0]), 0);
				discLabel["discLabel"+i] = value;
				if(disc.length == 2){
					var amount = (value * 0.01) * price;
					discLabel["discUnit"+i] = '%';
					discLabel["discountAmount"] += amount;
					price -= amount;
				}else{
					discLabel["discountAmount"] += value;
					price -= value;
				}
			}
		});
	}

	return discLabel;
}


function sort(field){
	var sort_by = "";
	if(field === 'date_add'){
		el = $('#sort_date_add');
		sort_by = el.hasClass('sorting_desc') ? 'ASC' : 'DESC';
		sort_class = el.hasClass('sorting_desc') ? 'sorting_asc' : 'sorting_desc';
	}else{
		el = $('#sort_code');
		sort_by = el.hasClass('sorting_desc') ? 'ASC' : 'DESC';
		sort_class = el.hasClass('sorting_desc') ? 'sorting_asc' : 'sorting_desc';
	}

	$('.sorting').removeClass('sorting_desc');
	$('.sorting').removeClass('sorting_asc');

	el.addClass(sort_class);
	$('#sort_by').val(sort_by);
	$('#order_by').val(field);

	getSearch();
}


$('.search').keyup((e) => {
  if(e.keyCode === 13) {
    getSearch();
  }
});

$('.filter').change(() => {
  getSearch();
});

function generateUID() {
  return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
}

function getDeviceId() {
  let deviceId = localStorage.getItem('DeviceId');

  if( ! deviceId) {
    deviceId = generateUID();
    localStorage.setItem('DeviceId', deviceId);
  }

  return deviceId;
}


function validInput(input, regex){
  var regex = regex === undefined ? /[^a-z0-9-_.]+/gi : regex;
  input.value = input.value.replace(regex, '');
}

function numberOnly(input){
  var regex = /[^0-9]+/gi;
  input.value = input.value.replace(regex, '');
}

function closeModal(modalName) {
  $('#'+modalName).modal('hide');
}


function roundNumber(num, digit)
{
	if(digit === undefined) {
		digit = 2;
	}
	else {
		ditit = parseDefault(parseInt(digit), 2);
	}

	return Number(parseFloat(num).toFixed(digit));
}


function getVatAmount(amount, rate, type) {
  vatAmount = 0.00;
  amount = parseDefault(parseFloat(amount), 0.00)
  rate = parseDefault(parseFloat(rate), 0.00)

  if(amount > 0 && rate > 0 && type != 'N') {
    vatRate = type == 'I' ? (rate + 100) * 0.01 : rate * 0.01

    vatAmount = type == 'I' ? amount - (amount / vatRate) : amount * vatRate
  }

  return vatAmount;
}

//--- คำนวนราคารวม vat
function addVat(amount, rate, type)
{
  amount = parseDefault(parseFloat(amount), 0.00)
  rate = parseDefault(parseFloat(rate), 0.00)

  amount = type == 'E' ? amount + (amount * rate) : amount;

  return amount;
}

//--- คำนวนราคาที่ถอด vat
function removeVat(amount, rate, type)
{
  amount = parseDefault(parseFloat(amount), 0.00)
  rate = parseDefault(parseFloat(rate), 0.00)

  if(amount > 0 && rate > 0 && type != 'N') {
    rate = (rate + 100) * 0.01
    amount = amount / rate
  }

  return amount;
}

function parseAddress(addr, sub_district, district, province, postcode) {
  province = parseProvince(province);
  address = addr + " " + parseSubDistrict(sub_district, province) + " " + parseDistrict(district, province) + " " + province + " " + postcode;
  return address;
}


function parseSubDistrict(ad, province)
{
	if(ad != null && ad != undefined && ad != "")
	{
		if(province == 'จ. กรุงเทพมหานคร' || province === 'จังหวัดกรุงเทพมหานคร' || province === 'กรุงเทพ' || province == 'กรุงเทพฯ' || province == 'กรุงเทพมหานคร' || province == 'กทม' || province == 'กทม.' || province == 'ก.ท.ม.')
		{
      ad = ad.replace(' ', '');
      ad = ad.replace('แขวง', '');
      ad = "แขวง" + ad;
			return ad;
		}
		else
		{
      ad = ad.replace(' ', '');
      ad = ad.replace('ต.', '');
      ad = ad.replace('ตำบล', '');
      ad = "ต. " + ad;
			return ad;
		}

	}

	return ad;
}

function parseDistrict(ad, province)
{
	if(ad != null && ad != undefined && ad != "")
	{
		if(province == 'จ. กรุงเทพมหานคร' || province === 'จังหวัดกรุงเทพมหานคร' || province === 'กรุงเทพ' || province == 'กรุงเทพฯ' || province == 'กรุงเทพมหานคร' || province == 'กทม' || province == 'กทม.' || province == 'ก.ท.ม.')
		{
      ad = ad.replace(' ', '');
      ad = ad.replace('เขต', '');
      ad = "เขต" + ad;
			return ad;
		}
		else
		{
      ad = ad.replace(' ', '');
      ad = ad.replace('อ..', '');
      ad = ad.replace('อำเภอ', '');
      ad = "อ. " + ad;
			return ad;
		}
	}

	return ad;
}


function parseProvince(ad)
{
	if(ad != null && ad != undefined && ad != "")
	{
    ad = ad.replace(' ', '');
    ad = ad.replace('จ.', '');
    ad = ad.replace('จังหวัด', '');

		if(ad === 'กรุงเทพ' || ad == 'กรุงเทพฯ' || ad == 'กรุงเทพมหานคร' || ad == 'กทม' || ad == 'กทม.' || ad == 'ก.ท.ม.')
		{
			ad = 'กรุงเทพมหานคร';
		}

		return "จ. " + ad;
	}

	return ad;
}

function hilightRow(id) {
	$('.order-rows').removeClass('active-row');
	$('#row-'+id).addClass('active-row');
}

$.fn.hasError = function(msg) {
  let name = this.attr('id');
  $('#'+name+'-error').text(msg);
  return this.addClass('has-error');
};

$.fn.clearError = function() {
  this.removeClass('has-error');
  let name = this.attr('id');
  return $('#'+name+'-error').text('');
};

function clearErrorByClass(className) {
  $('.'+className).each(function() {
    let name = $(this).attr('id');
    $('#'+name+'-error').text('');
    $(this).removeClass('has-error');
  })
}

function showError(response) {
  load_out();

  setTimeout(() => {
    swal({
      title:'Error!',
      text:(typeof response === 'object') ? response.responseText : response,
      type:'error',
      html:true
    })
  }, 100);
}

function is_true(val) {
  if(typeof(val) === 'string') {
    val = val.trim().toLowerCase();
  }

  switch (val) {
    case true:
    case "true":
    case 1:
    case "1":
      return true;
    default :
      return false;
  }
}

function refresh() {
  window.location.reload();
}
