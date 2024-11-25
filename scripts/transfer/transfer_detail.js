function doExport()
{
	var code = $('#transfer_code').val();
	load_in();
	$.ajax({
		url:HOME + 'export_transfer/' + code,
		type:'POST',
		cache:false,
		success:function(rs){
			load_out();
			if(rs == 'success'){
				swal({
					title:'Success',
					text:'ส่งข้อมูลไป SAP เรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});
			}else{
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
		}
	});
}


function removeTemp(id, item_code) {
	swal({
		title:'คุณแน่ใจ ?',
		text:'ต้องการลบ '+item_code+' หรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonColor:'#d15b47',
		confirmButtonText:'Yes',
		cancelButtonText:'No',
		closeOnConfirm:true
	}, function() {
		load_in();

		setTimeout(() => {
			$.ajax({
				url:HOME + 'delete_temp',
				type:'POST',
				cache:false,
				data:{
					'id' : id
				},
				success:function(rs) {
					load_out();

					if(rs === 'success') {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						$('#row-temp-'+id).remove();
						reIndex('tmp-no');
					}
					else {
						swal({
							title:'Error!',
							text:rs,
							type:'error'
						});
					}
				}
			})
		}, 200);
	})
}


function checkAll(el) {
	if(el.is(':checked')) {
		$('.chk').prop('checked', true);
	}
	else {
		$('.chk').prop('checked', false);
	}
}


function removeChecked() {
	let code = $('#transfer_code').val();
	let ids = [];

	if($('.chk:checked').length) {
		$('.chk:checked').each(function() {
			ids.push($(this).val());
		});

		if(ids.length > 0) {
			swal({
				title: 'คุณแน่ใจ ?',
				text: 'ต้องการลบ '+ ids.length +' รายการที่เลือก หรือไม่ ?',
				type: 'warning',
				showCancelButton: true,
				comfirmButtonColor: '#DD6855',
				confirmButtonText: 'ใช่ ฉันต้องการ',
				cancelButtonText: 'ไม่ใช่',
				closeOnConfirm: true
			},
			function() {
				load_in();

				setTimeout(() => {
					$.ajax({
						url:HOME + 'delete_detail',
						type:'POST',
						cache:false,
						data:{
							'transfer_code' : code,
							'ids' : JSON.stringify(ids)
						},
						success:function(rs) {
							load_out();

							if(rs == 'success') {
								swal({
									title:'Success',
									type:'success',
									timer:1000
								});

								$('.chk:checked').each(function() {
									let id = $(this).val();
									$('#row-'+id).remove();
								});
								
								reIndex();
								recal();
							}
							else {
								swal({
									title:'Error!',
									text:rs,
									type:'error'
								});
							}
						}
					})
				}, 200);
			});
		}
	}
}


function reCal(){
	var total = 0;
	$('.qty').each(function(){
		var qty = parseInt(removeCommas($(this).text()));
		if(!isNaN(qty))
		{
			total += qty;
		}
	});

	$('#total').text(addCommas(total));
}


//------------  ตาราง transfer_detail
function getTransferTable(){
	var code	= $("#transfer_code").val();

	$.ajax({
		url: HOME + 'get_transfer_table/'+ code,
		type:"GET",
    cache:"false",
		success: function(rs){
			if( isJson(rs) ){
				var source 	= $("#transferTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#transfer-list");
				render(source, data, output);
			}
		}
	});
}




function getTempTable(){
	var code = $("#transfer_code").val();
	$.ajax({
		url: HOME + 'get_temp_table/'+code,
		type:"GET",
    cache:"false",
		success: function(rs){
			if( isJson(rs) ){
				var source 	= $("#tempTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#temp-list");
				render(source, data, output);
			}
		}
	});
}




//--- เพิ่มรายการลงใน transfer detail
//---	เพิ่มลงใน transfer_temp
//---	update stock ตามรายการที่ใส่ตัวเลข
function addToTransfer(){
	var code	= $('#transfer_code').val();

	//---	โซนต้นทาง
	var from_zone = $("#from_zone_code").val();

	if(from_zone.length == 0)
	{
		swal('โซนต้นทางไม่ถูกต้อง');
		return false;
	}

	//--- โซนปลายทาง
	var to_zone = $('#to_zone_code').val();

	if(to_zone.length == 0)
	{
		swal('โซนปลายทางไม่ถูกต้อง');
		return false;
	}

	//---	จำนวนช่องที่มีการป้อนตัวเลขเพื่อย้ายสินค้าออก
	var count  = countInput();

	if(count == 0)
	{
		swal('ข้อผิดพลาด !', 'กรุณาระบุจำนวนในรายการที่ต้องการย้าย อย่างน้อย 1 รายการ', 'warning');
		return false;
	}

	//---	ตัวแปรสำหรับเก็บ ojbect ข้อมูล
	var ds  = {};
	var items = [];

	ds.transfer_code = code;
	ds.from_zone = from_zone;
	ds.to_zone = to_zone;



	$('.input-qty').each(function(index, element) {
	    let qty = parseDefault(parseInt($(this).val()),0);

			if(qty > 0) {
				let pd_code  = $(this).data('sku'); //$(this).attr('id')
				items.push({"item_code" : pd_code, "qty" : qty});
			}
    });

	ds.items = items;

	if( count > 0 ) {
		load_in();
		setTimeout(function(){
			$.ajax({
				url: HOME + 'add_to_transfer',
				type:"POST",
				cache:"false",
				data: {
					"data" : JSON.stringify(ds)
				},
				success: function(rs){
					load_out();
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
							title: 'success',
							text: 'เพิ่มรายการเรียบร้อยแล้ว',
							type: 'success',
							timer: 1000
						});

						setTimeout( function(){
							showTransferTable();
							recalZoneQty();
						}, 1200);

					}else{

						swal("ข้อผิดพลาด", rs, "error");
					}
				}
			});
		}, 500);
	}
	else
	{

		swal('ข้อผิดพลาด !', 'กรุณาระบุจำนวนในรายการที่ต้องการย้าย อย่างน้อย 1 รายการ', 'warning');

	}
}

function recalZoneQty() {
	$('.input-qty').each(function() {
		if($(this).val() != 0) {
			let no = $(this).data('no');
			let limit = parseDefault(parseFloat($(this).data('limit')), 0);
			let qty = parseDefault(parseFloat($(this).val()), 0);

			if(qty > 0 && limit > 0) {
				let nQty = limit - qty;

				$('#qty-label-'+no).text(addCommas(nQty));
				$(this).data('limit', nQty);
				$(this).val('');

				if(nQty <= 0) {
					$('#zone-row-'+no).remove();
					reIndex('zone-no');
				}
			}
		}
	})
}



function selectAll(){
	$('.input-qty').each(function(index, el){
		var qty = $(this).attr('max');
		$(this).val(qty);
	});
}


function clearAll(){
	$('.input-qty').each(function(index, el){
		$(this).val('');
	});
}


//----- นับจำนวน ช่องที่มีการใส่ตัวเลข
function countInput(){
	var count = 0;
	$(".input-qty").each(function(index, element) {
        count += ($(this).val() == "" ? 0 : 1 );
    });
	return count;
}
