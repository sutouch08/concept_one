$('#bill_sub_district').autocomplete({
	source:BASE_URL + 'auto_complete/sub_district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 4){
			$('#bill_sub_district').val(adr[0]);
			$('#bill_district').val(adr[1]);
			$('#bill_province').val(adr[2]);
			$('#bill_postcode').val(adr[3]);
		}
	}
});


$('#bill_district').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 3){
			$('#bill_district').val(adr[0]);
			$('#bill_province').val(adr[1]);
			$('#bill_postcode').val(adr[2]);
		}
	}
});


$('#bill_province').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 2){
			$('#bill_province').val(adr[0]);
			$('#bill_postcode').val(adr[1]);
		}
	}
})



function reloadShipToTable()
{
	var customer_code = $('#customers_code').val();
	$.ajax({
		url:BASE_URL + 'masters/customers/get_ship_to_table',
		type:"POST",
		cache:"false",
		data:{
			'customer_code' : customer_code
		},
		success: function(rs){
			var rs = $.trim(rs);
			if(isJson(rs)){
				var source 	= $("#addressTableTemplate").html();
				var data 		= $.parseJSON(rs);
				var output 	= $("#adrs");
				render(source, data, output);
			}else{
				$("#adrs").html('<tr><td colspan="7" align="center">ไม่พบที่อยู่</td></tr>');
			}
		}
	});
}


function saveShipTo()
{
	var code 			= $('#customers_code').val();
	var name			= $("#Fname").val();
	var addr			= $("#address1").val();
	var subdistrict = $('#sub_district').val();
	var district  = $('#district').val();
	var province  = $('#province').val();
	var email			= $("#email").val();
	var alias 		= $("#alias").val();
	var cust_ref = $('#customer_ref').val();

	if(code == ''){
		swal('กรุณาระบุชื่อลูกค้า');
		return false;
	}

	if( name == '' ){
		swal('กรุณาระบุชื่อผู้รับ');
		return false;
	}

	if( addr.length == 0 ){
		swal('กรุณาระบุที่อยู่');
		return false;
	}

	if(subdistrict.length == 0){
		swal('กรุณาระบุตำบล');
		return false;
	}


	if(district.length == 0){
		swal('กรุณาระบุอำเภอ');
		return false;
	}

	if(province.length == 0){
		swal('กรุณาระบุจังหวัด');
		return false;
	}


	if( alias == '' ){
		swal('กรุณาตั้งชื่อให้ที่อยู่');
		return false;
	}

	if( email != '' && ! validEmail(email) ){
		swal("อีเมล์ไม่ถูกต้องกรุณาตรวจสอบ");
		return false;
	}

	var ds = [];

	ds.push( {"name" : "id_address", "value" : $("#id_address").val() } );
	ds.push( {"name" : "customer_code", "value" : code});
	ds.push( {"name" : "customer_ref", "value" : cust_ref});
	ds.push( {"name" : "name", "value" : name } );
	ds.push( {"name" : "address", "value" : $("#address1").val() } );
	ds.push( {"name" : "sub_district", "value" : $("#sub_district").val() } );
	ds.push( {"name" : "district", "value" : $("#district").val() } );
	ds.push( {"name" : "province", "value" : $("#province").val() } );
	ds.push( {"name" : "postcode", "value" : $("#postcode").val() } );
	ds.push( {"name" : "phone", "value" : $("#phone").val() } );
	ds.push( {"name" : "email", "value" : $("#email").val() } );
	ds.push( {"name" : "alias", "value" : $("#alias").val() } );

	$("#addressModal").modal('hide');

	load_in();
	$.ajax({
		url:BASE_URL + 'orders/orders/save_address',
		type:"POST",
		cache:"false",
		data: ds,
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success'){
				reloadShipToTable();
				clearAddressField();
			}else{
				swal({
					title:'ข้อผิดพลาด',
					text:rs,
					type:'error'
				});
				$("#addressModal").modal('show');
			}
		}
	});
}


function removeShipTo(id)
{
	swal({
		title: 'ต้องการลบที่อยู่ ?',
		text: 'คุณแน่ใจว่าต้องการลบที่อยู่นี้ โปรดจำไว้ว่าการกระทำนี้ไม่สามารถกู้คืนได้',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ลบเลย',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url:BASE_URL + 'masters/customers/delete_shipping_address',
				type:"POST",
				cache:"false",
				data:{
					"id_address" : id
				},
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title : "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });
						reloadShipToTable();
					}else{
						swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
					}
				}
			});
		});
}
