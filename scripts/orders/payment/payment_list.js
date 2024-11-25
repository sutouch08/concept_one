// JavaScript Document
//setInterval(function(){ getSearch(); }, 1000*60);
function viewDpm(code) {
	if(code != "" && code != null && code.length > 9) {
		//--- properties for print
		var center    = ($(document).width() - 800)/2;
		var prop 			= "width=800, height=900, left="+center+", scrollbars=yes";
		var target = BASE_URL + 'orders/order_down_payment/view_detail/'+code+'?nomenu';
		window.open(target, "_blank", prop);
	}
}

function confirmPayment() {
	let id = $('#id-payment').val();

	if(id) {
		closeModal('confirmModal');

		load_in();

		$.ajax({
			url:BASE_URL + 'orders/order_payment/confirm_payment',
			type:"POST",
	    cache:"false",
	    data:{
	      "id" : id
	    },
			success: function(rs) {
				load_out();

				if( rs.trim() == 'success' ) {
					swal({
	          title : 'เรียบร้อย',
	          text: 'Paid',
	          timer: 1000,
	          type: 'success'
	        });

					$("#row-"+id).remove();

					reIndex();
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error',
						html:true
					})
				}
			}
		});
	}
}


function unConfirmPayment() {
	let id = $('#id-payment').val();

	if(id) {
		closeModal('confirmModal');

		load_in();

		$.ajax({
			url:BASE_URL + 'orders/order_payment/un_confirm_payment',
			type:"POST",
	    cache:"false",
	    data:{
	      "id" : id
	    },
			success: function(rs) {
				load_out();

				if( rs.trim() == 'success' ){
					swal({
	          title : 'เรียบร้อย',
	          text: 'ยกเลิกการยืนยันเรียบร้อยแล้ว',
	          timer: 1000,
	          type: 'success'
	        });

					$("#row-"+id).remove();

					reIndex();
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error',
						html:true
					})
				}
			}
		});
	}
}


function viewDetail(id) {
	load_in();

	$.ajax({
		url:BASE_URL + "orders/order_payment/get_payment_detail",
		type:"POST",
    cache:"false",
    data:{
      "id" : id
    },
		success: function(rs) {
			load_out();

			if(isJson(rs)) {
				let data = JSON.parse(rs);
				if(data.status == 'success') {
					let ds = data.data;
					$('#id-payment').val(ds.id);

					if(is_true(ds.valid)) {
						$('#btn-unconfirm').removeClass('hide');
						$('#btn-confirm').addClass('hide');
					}
					else {
						$('#btn-unconfirm').addClass('hide');
						$('#btn-confirm').removeClass('hide');
					}

					var source 	= $("#detailTemplate").html();
					var output	= $("#detailBody");
					render(source, ds, output);

					$("#confirmModal").modal('show');

				}
				else {
					swal({
						title:'Error!',
						text:ds.message,
						type:'error',
						html:true
					})
				}
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error',
					html:true
				})
			}
		},
		error:function(e) {
			load_out();

			swal({
				title:'Error!',
				text:rs,
				type:'error',
				html:true
			})
		}
	});
}


function removePayment(id, name) {
	swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการลบการแจ้งชำระของ '+ name + ' หรือไม่?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: true
	},
	function() {
		setTimeout(() => {

			load_in();

			$.ajax({
				url: BASE_URL + 'orders/order_payment/remove_payment',
				type:"POST",
				cache:false,
				data:{
					"id" : id
				},
				success:function(rs) {
					load_out();

					if(rs.trim() == 'success') {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						$('#row-'+id).remove();
						reIndex();
					}
					else {
						swal({
							title:'Error!',
							text:rs,
							type:'error',
							html:true
						});
					}
				},
				error:function(e) {
					load_out();

					swal({
						title:'Error!',
						text:e.responseText,
						type:'error',
						html:true
					})
				}
			});
		}, 200);
	});
}


function createDownPayments() {
	if( $('.chk:checked').length > 0) {
		let list = [];

		$('.chk:checked').each(function() {
			list.push($(this).val());
		});

		if(list.length > 0) {

			swal({
				title:'เปิดใบรับมัดจำ',
				text:'ต้องการสร้างใบรับมัดจำจากรายการที่เลือกหรือไม่ ?',
				type:'info',
				showCancelButton:true,
				confirmButtonText:'Yes',
				cancelButtonText:'No',
				closeOnConfirm:true
			},
			function() {
				setTimeout(() => {
					load_in();

					$.ajax({
						url:BASE_URL + 'orders/order_payment/create_down_payments',
						type:'POST',
						cache:false,
						data:{
							'payments' : JSON.stringify(list)
						},
						success:function(rs) {
							load_out();

							if(rs.trim() == 'success') {
								swal({
									title:'Success',
									type:'success',
									timer:1000
								});

								setTimeout(() => {
									window.location.reload();
								}, 1200);
							}
							else {
								swal({
									title:'Error!',
									text:rs,
									type:'error',
									html:true
								})
							}
						},
						error:function(e) {
							load_out();

							swal({
								title:'Error!',
								text:e.responseText,
								type:'error',
								html:true
							})
						}
					})
				}, 200);
			})
		}//--- if list.length > 0
	}
}


$("#fromDate").datepicker({
	dateFormat:'dd-mm-yy',
	onClose: function(sd){
		$("#toDate").datepicker("option", "minDate", sd);
	}
});



$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(sd){
		$("#fromDate").datepicker("option", "maxDate", sd);
	}
});

function checkAll() {
	if($('#chk-all').is(':checked')) {
		$('.chk').prop('checked', true);
	}
	else {
		$('.chk').prop('checked', false);
	}
}
