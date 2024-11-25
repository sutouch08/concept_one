var wms_warehouse = "";
var HOME = BASE_URL + 'setting/configs/';

function updateConfig(formName)
{
	load_in();
	var formData = $("#"+formName).serialize();
	$.ajax({
		url: BASE_URL + "setting/configs/update_config",
		type:"POST",
    cache:"false",
    data: formData,
		success: function(rs){
			load_out();
      rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Updated',
          type:'success',
          timer:1000
        });
      }else{
        swal('Error!', rs, 'error');
      }
		}
	});
}



function openSystem()
{
	$("#closed").val(0);
	$("#btn-close").removeClass('btn-danger');
	$('#btn-freze').removeClass('btn-warning');
	$("#btn-open").addClass('btn-success');
}



function closeSystem()
{
	$("#closed").val(1);
	$("#btn-open").removeClass('btn-success');
	$('#btn-freze').removeClass('btn-warning');
	$("#btn-close").addClass('btn-danger');
}


function frezeSystem()
{
	$("#closed").val(2);
	$("#btn-open").removeClass('btn-success');
	$("#btn-close").removeClass('btn-danger');
	$('#btn-freze').addClass('btn-warning');
}



function toggleManualCode(option)
{
	$('#manual-doc-code').val(option);
	if(option == 1){
		$('#btn-manual-yes').addClass('btn-success');
		$('#btn-manual-no').removeClass('btn-danger');
		return;
	}
	if(option == 0){
		$('#btn-manual-yes').removeClass('btn-success');
		$('#btn-manual-no').addClass('btn-danger');
		return;
	}
}


function toggleStrongPWD(option) {
	$('#use-strong-pwd').val(option);

	if(option == 1) {
		$('#btn-strong-on').addClass('btn-primary');
		$('#btn-strong-off').removeClass('btn-primary');
	}
	else {
		$('#btn-strong-on').removeClass('btn-primary');
		$('#btn-strong-off').addClass('btn-primary');
	}
}


function toggleNotiBars(option)
{
	$('#noti-bar').val(option);
	if(option == 1){
		$('#btn-noti-yes').addClass('btn-success');
		$('#btn-noti-no').removeClass('btn-danger');
		return;
	}
	if(option == 0){
		$('#btn-noti-yes').removeClass('btn-success');
		$('#btn-noti-no').addClass('btn-danger');
		return;
	}
}




//--- เปิด/ปิด การ sync ข้อมูลระหว่างเว็บไซต์กับระบบหลัก
function toggleWebApi(option){
	$('#web-api').val(option);
	if(option == 1){
		$('#btn-api-yes').addClass('btn-success');
		$('#btn-api-no').removeClass('btn-danger');
		return;
	}else if(option == 0){
		$('#btn-api-yes').removeClass('btn-success');
		$('#btn-api-no').addClass('btn-danger');
		return;
	}
}


//---- ไม่ขายสินค้าให้ลูกค้าที่มียอดค้างเกินกำหนด
function toggleStrictDue(option)
{
	$('#strict-over-due').val(option);
	if(option == 1){
		$('#btn-strict-yes').addClass('btn-success');
		$('#btn-strict-no').removeClass('btn-danger');
		return;
	}
	if(option == 0){
		$('#btn-strict-yes').removeClass('btn-success');
		$('#btn-strict-no').addClass('btn-danger');
		return;
	}
}



//---- ไม่ขายสินค้าให้ลูกค้าที่มียอดค้างเกินกำหนด
function toggleAuz(option)
{
	$('#allow-under-zero').val(option);
	if(option == 1){
		$('#btn-auz-yes').addClass('btn-danger');
		$('#btn-auz-no').removeClass('btn-success');
		return;
	}
	if(option == 0){
		$('#btn-auz-yes').removeClass('btn-danger');
		$('#btn-auz-no').addClass('btn-success');
		return;
	}
}


//---- ไม่ขายสินค้าให้ลูกค้าที่มียอดค้างเกินกำหนด
function toggleOverPo(option)
{
	$('#allow-receive-over-po').val(option);
	if(option == 1){
		$('#btn-ovpo-yes').addClass('btn-success');
		$('#btn-ovpo-no').removeClass('btn-success');
		return;
	}
	if(option == 0){
		$('#btn-ovpo-yes').removeClass('btn-success');
		$('#btn-ovpo-no').addClass('btn-success');
		return;
	}
}


//---- ไม่ขายสินค้าให้ลูกค้าที่มียอดค้างเกินกำหนด
function toggleOverStock(option)
{
	$('#order-over-stock').val(option);

	if(option == 1){
		$('#btn-over-yes').addClass('btn-success');
		$('#btn-over-no').removeClass('btn-primary');
		return;
	}
	if(option == 0){
		$('#btn-over-yes').removeClass('btn-success');
		$('#btn-over-no').addClass('btn-primary');
		return;
	}
}



function toggleRequest(option)
{
	$('#strict-receive-po').val(option);
	if(option == 1){
		$('#btn-request-yes').addClass('btn-success');
		$('#btn-request-no').removeClass('btn-success');
		return;
	}
	if(option == 0){
		$('#btn-request-yes').removeClass('btn-success');
		$('#btn-request-no').addClass('btn-success');
		return;
	}
}


function toggleTransfer(option)
{
	$('#strict-transfer').val(option);

	if(option == 1){
		$('#btn-transfer-yes').addClass('btn-success');
		$('#btn-transfer-no').removeClass('btn-success');
		return;
	}
	if(option == 0){
		$('#btn-transfer-yes').removeClass('btn-success');
		$('#btn-transfer-no').addClass('btn-success');
		return;
	}
}


function toggleControlCredit(option)
{
	$('#control-credit').val(option);
	if(option == 1){
		$('#btn-credit-yes').addClass('btn-success');
		$('#btn-credit-no').removeClass('btn-danger');
		return;
	}
	if(option == 0){
		$('#btn-credit-yes').removeClass('btn-success');
		$('#btn-credit-no').addClass('btn-danger');
		return;
	}
}


function toggleShowStock(option)
{
	$('#show-sum-stock').val(option);
	if(option == 1){
		$('#btn-show-stock-yes').addClass('btn-success');
		$('#btn-show-stock-no').removeClass('btn-primary');
		return;
	}
	if(option == 0){
		$('#btn-show-stock-yes').removeClass('btn-success');
		$('#btn-show-stock-no').addClass('btn-primary');
		return;
	}
}



function toggleReceiveDue(option)
{
	$('#receive-over-due').val(option);
	if(option == 1){
		$('#btn-receive-yes').addClass('btn-success');
		$('#btn-receive-no').removeClass('btn-danger');
		return;
	}
	if(option == 0){
		$('#btn-receive-yes').removeClass('btn-success');
		$('#btn-receive-no').addClass('btn-danger');
		return;
	}
}



function toggleEditDiscount(option)
{
	$('#allow-edit-discount').val(option);
	if(option == 1){
		$('#btn-disc-yes').addClass('btn-success');
		$('#btn-disc-no').removeClass('btn-danger');
		return;
	}

	if(option == 0){
		$('#btn-disc-yes').removeClass('btn-success');
		$('#btn-disc-no').addClass('btn-danger');
		return;
	}
}


function toggleEditPrice(option){
	$('#allow-edit-price').val(option);

	if(option == 1){
		$('#btn-price-yes').addClass('btn-success');
		$('#btn-price-no').removeClass('btn-danger');
		return;
	}

	if(option == 0){
		$('#btn-price-yes').removeClass('btn-success');
		$('#btn-price-no').addClass('btn-danger');
		return;
	}
}


function toggleEditCost(option){
	$('#allow-edit-cost').val(option);

	if(option == 1){
		$('#btn-cost-yes').addClass('btn-success');
		$('#btn-cost-no').removeClass('btn-danger');
		return;
	}

	if(option == 0){
		$('#btn-cost-yes').removeClass('btn-success');
		$('#btn-cost-no').addClass('btn-danger');
		return;
	}
}



function toggleAutoClose(option){
	$('#po-auto-close').val(option);

	if(option == 1){
		$('#btn-po-yes').addClass('btn-success');
		$('#btn-po-no').removeClass('btn-danger');
		return;
	}

	if(option == 0){
		$('#btn-po-yes').removeClass('btn-success');
		$('#btn-po-no').addClass('btn-danger');
		return;
	}
}

//--- เปิด/ปิด WMS API
function toggleWmsApi(option){
	$('#wms-api').val(option);
	if(option == 1){
		$('#btn-api-on').addClass('btn-success');
		$('#btn-api-off').removeClass('btn-danger');
		return;
	}else if(option == 0){
		$('#btn-api-on').removeClass('btn-success');
		$('#btn-api-off').addClass('btn-danger');
		return;
	}
}


function toggleFullMode(option) {
	$('#wms-full-mode').val(option);

	if(option == 1) {
		$('#btn-full-off').removeClass('btn-danger');
		$('#btn-full-on').addClass('btn-success');
		return;
	}

	if(option == 0) {
		$('#btn-full-on').removeClass('btn-success');
		$('#btn-full-off').addClass('btn-danger');
		return;
	}
}


function toggleExportItem(option) {
	$('#wms-export-item').val(option);

	if(option == 1) {
		$('#btn-item-off').removeClass('btn-danger');
		$('#btn-item-on').addClass('btn-success');
		return;
	}

	if(option == 0) {
		$('#btn-item-on').removeClass('btn-success');
		$('#btn-item-off').addClass('btn-danger');
		return;
	}
}


function toggleLogXml(option) {

	$('#log-xml').val(option);

	if(option == 1) {
		$('#btn-xml-off').removeClass('btn-danger');
		$('#btn-xml-on').addClass('btn-success');
		return;
	}

	if(option == 0) {
		$('#btn-xml-on').removeClass('btn-success');
		$('#btn-xml-off').addClass('btn-danger');
		return;
	}
}


function toggleFastExport(option) {

	$('#wms-instant-export').val(option);

	if(option == 1) {
		$('#btn-ex-off').removeClass('btn-danger');
		$('#btn-ex-on').addClass('btn-success');
		return;
	}

	if(option == 0) {
		$('#btn-ex-on').removeClass('btn-success');
		$('#btn-ex-off').addClass('btn-danger');
		return;
	}
}


function checkCompanySetting(){
	vat = parseFloat($('#VAT').val());
	year = parseInt($('#startYear').val());

	if(isNaN(year)){
		swal('ปีที่เริ่มต้นกิจการไม่ถูกต้อง');
		return false;
	}

	if(year < 1970){
		swal('ปีที่เริ่มต้นกิจการไม่ถูกต้อง');
		return false;
	}

	if(year > 2100){
		year = year - 543;
		$('#startYear').val(year);
	}


	updateConfig('companyForm');
}

$('#default-warehouse').autocomplete({
	source: BASE_URL + 'auto_complete/get_warehouse_by_role/1',
	autoFocus:true,
	close:function(){
		let rs = $(this).val();
		let arr = rs.split(' | ');

		if(arr[0] === 'not found'){
			$(this).val('');
		}else{
			$(this).val(arr[0]);
		}
	}
})

$('#lend-warehouse').autocomplete({
	source: BASE_URL + 'auto_complete/get_warehouse_by_role/8',
	autoFocus:true,
	close:function(){
		let rs = $(this).val();
		let arr = rs.split(' | ');

		if(arr[0] === 'not found'){
			$(this).val('');
		}else{
			$(this).val(arr[0]);
		}
	}
})


$('#transform-warehouse').autocomplete({
	source: BASE_URL + 'auto_complete/get_warehouse_by_role/7',
	autoFocus:true,
	close:function(){
		let rs = $(this).val();
		let arr = rs.split(' | ');

		if(arr[0] === 'not found'){
			$(this).val('');
		}else{
			$(this).val(arr[0]);
		}
	}
})


$('#wms-warehouse').autocomplete({
	source: BASE_URL + 'auto_complete/get_warehouse_by_role/1',
	autoFocus:true,
	close:function(){
		let rs = $(this).val();
		let arr = rs.split(' | ');

		if(arr[0] === 'not found'){
			$(this).val('');
		}else{
			$(this).val(arr[0]);
			set_wms_warehouse(arr[0]);
		}
	}
})


$(document).ready(function(){
	wms_warehouse = $('#wms-warehouse').val();
	set_wms_warehouse(wms_warehouse);
});

function set_wms_warehouse(wms_wh_code) {
	$('#wms-zone').autocomplete({
		source: BASE_URL + 'auto_complete/get_zone_code_and_name/'+ wms_wh_code,
		autoFocus:true,
		close:function() {
			let rs = $(this).val();
			let arr = rs.split(' | ');

			if(arr[0] === 'ไม่พบรายการ') {
				$(this).val('');
			}
			else {
				$(this).val(arr[0]);
			}
		}
	})
}


//--- เปิด/ปิด การ sync ข้อมูลระหว่างเว็บไซต์กับระบบหลัก
function toggleChatbotApi(option){
	$('#chatbot-api').val(option);

	if(option == 1){
		$('#btn-chatbot-api-on').addClass('btn-success');
		$('#btn-chatbot-api-off').removeClass('btn-danger');
		return;

	}else if(option == 0){
		$('#btn-chatbot-api-on').removeClass('btn-success');
		$('#btn-chatbot-api-off').addClass('btn-danger');
		return;
	}
}


function toggleSyncStock(option) {

	$('#sync-chatbot-stock').val(option);

	if(option == 1) {
		$('#btn-stock-off').removeClass('btn-danger');
		$('#btn-stock-on').addClass('btn-success');
		return;
	}

	if(option == 0) {
		$('#btn-stock-on').removeClass('btn-success');
		$('#btn-stock-off').addClass('btn-danger');
		return;
	}
}



function toggleLimitWC(option) {
	$('#limit-consignment').val(option);

	if(option == 1) {
		$('#btn-wc-no').removeClass('btn-danger');
		$('#btn-wc-yes').addClass('btn-success');
		return;
	}

	if(option == 0) {
		$('#btn-wc-yes').removeClass('btn-success');
		$('#btn-wc-no').addClass('btn-danger');
		return;
	}
}


function toggleLimitWT(option) {
	$('#limit-consign').val(option);

	if(option == 1) {
		$('#btn-wt-no').removeClass('btn-danger');
		$('#btn-wt-yes').addClass('btn-success');
		return;
	}

	if(option == 0) {
		$('#btn-wt-yes').removeClass('btn-success');
		$('#btn-wt-no').addClass('btn-danger');
		return;
	}
}


function toggleTransferEOM(option) {
	$('#transfer-eom').val(option);

	if(option == 1) {
		$('#btn-eom-no').removeClass('btn-success');
		$('#btn-eom-yes').addClass('btn-success');
		return;
	}

	if(option == 0) {
		$('#btn-eom-yes').removeClass('btn-success');
		$('#btn-eom-no').addClass('btn-success');
		return;
	}
}



function toggleLogJson(option) {

	$('#chatbot-log-json').val(option);

	if(option == 1) {
		$('#btn-log-off').removeClass('btn-danger');
		$('#btn-log-on').addClass('btn-success');
		return;
	}

	if(option == 0) {
		$('#btn-log-on').removeClass('btn-success');
		$('#btn-log-off').addClass('btn-danger');
		return;
	}
}



function toggleSysBin(option) {
	$('#system-bin-location').val(option);

	if(option == 1) {
		$('#btn-sys-bin-yes').addClass('btn-success');
		$('#btn-sys-bin-no').removeClass('btn-success');
		return;
	}

	if(option == 0) {
		$('#btn-sys-bin-no').addClass('btn-success');
		$('#btn-sys-bin-yes').removeClass('btn-success');
		return;
	}
}


function changeURL(tab)
{
	var url = HOME + 'index/'+ tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'setting', url);
}

$('#sap-cash-acct-code').select2();
$('#sap-card-acct-code').select2();
$('#sap-check-acct-code').select2();

function update_sap_credit_card_acc_code()
{
	let card_id = $('#sap-card-acct-code').val();
	let acct_code = $('#sap-card-acct-code option:selected').data('code');

	$('#sap-credit-card-acct-code').val(acct_code);
}
