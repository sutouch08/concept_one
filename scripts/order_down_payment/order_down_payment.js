var HOME = BASE_URL + 'orders/order_down_payment/';

window.addEventListener('load', () => {
	setColorbox();
})


function cancelDownPayment(code, id) {
	swal({
		title:'ยกเลิก',
		text:'ต้องการยกเลิก '+code+' หรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		cancelButtonText:'No',
		confirmButtonText:'Yes',
		confirmButtonColor:'#d15b47',
		closeOnConfirm:true
	},
	function() {
		$('#cancel-id').val(id);
		$('#cancel-code').val(code);
		$('#cancel-reason').val('');

		setTimeout(() => {
			cancelReason();
		}, 200);
	});
}


function cancelReason() {
	$('#cancelModal').modal('show');
}

$('#cancelModal').on('shown.bs.modal', function() {
	$('#cancel-reason').focus();
});


function doCancel() {
	$('#cancel-reason').clearError();

	let id = $('#cancel-id').val();
	let code = $('#cancel-code').val();
	let reason = $('#cancel-reason').val().trim();

	if(reason.length == 0) {
		$('#cancel-reason').hasError().focus();
		return false;
	}

	$('#cancelModal').modal('hide');

	load_in();

	setTimeout(() => {
		$.ajax({
			url:BASE_URL + 'orders/order_down_payment/cancel_payment',
			type:'POST',
			cache:false,
			data:{
				'id' : id,
				'code' : code,
				'reason' : reason
			},
			success:function(rs) {
				load_out();

				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status == 'success') {
						swal({
							title:'Success',
							text:'ยกเลิกเอกสารเรียบร้อยแล้ว',
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
							text:ds.message,
							type:'error'
						});
					}
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			},
			error:function(xhr) {
				load_out();
				swal({
					title:'Error!',
					text:xhr.responseText,
					type:'error',
					html:true
				});
			}
		});
	}, 200);
}


$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#toDate').datepicker("option", "minDate", sd);
  }
});

$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#fromDate').datepicker("option", "maxDate", sd);
  }
});


function getSearch() {
	$('#searchForm').submit();
}

function goBack() {
	window.location.href = HOME;
}

function viewDetail(code) {
  window.location.href = HOME + 'view_detail/'+code;
}

function viewSo(code) {
	let width = 1000;
	let height = 600;
	let center = (window.innerWidth - width)/2;
	let prop = "width="+width+", height="+height+", left="+center+", scrollbars=yes";
	let target = BASE_URL + 'orders/sales_order/view_detail/'+code+'?nomenu';
	window.open(target, '_blank', prop);
}

function viewWo(code) {
	if(code != "" && code != null && code.length > 9) {
		//--- properties for print
		var center    = ($(document).width() - 800)/2;
		var prop 			= "width=800, height=900, left="+center+", scrollbars=yes";
		var target = BASE_URL + 'orders/orders/edit_order/'+code;
		window.open(target, "_blank", prop);
	}
}


function viewInvoice(code) {
	if(code != "" && code != null && code.length > 9) {
		//--- properties for print
		var center    = ($(document).width() - 800)/2;
		var prop 			= "width=800, height=900, left="+center+", scrollbars=yes";
		var target = BASE_URL + 'orders/down_payment_invoice/view_detail/'+code+'?nomenu';
		window.open(target, "_blank", prop);
	}
}


function printDownPayment(code) {
	let width = 400;
	let height = 600;
	let center = (window.innerWidth - width)/2;
	let prop = "width="+width+", height="+height+", left="+center+", top=100, scrollbars=yes";
	let target = HOME + 'print_down_payment/'+code;
	window.open(target, '_blank', prop);
}

function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs) {
		goBack();
	});
}

function exportFilter() {
  let count = 0;
  let code = $('#code').val();
  let order_code = $('#order_code').val();
  let bill_code = $('#bill_code').val();
  let shop_id = $('#shop_id').val();
  let pos_id = $('#pos_id').val();
  let payment = $('#payment').val();
  let status = $('#status').val();
  let from_date = $('#fromDate').val();
  let to_date = $('#toDate').val();

  if( ! isDate(from_date) || ! isDate(to_date)) {
    swal({
      title:'Required',
      text:'กรุณาระบุวันที่',
      type:'warning'
    });

    return false;
  }

  $('#ex-code').val(code);
  $('#ex-order-code').val(order_code);
  $('#ex-bill-code').val(bill_code);
  $('#ex-shop-id').val(shop_id);
  $('#ex-pos-id').val(pos_id);
  $('#ex-payment').val(payment);
  $('#ex-from-date').val(from_date);
  $('#ex-to-date').val(to_date);

  let token = new Date().getTime();
  $('#token').val(token);

  get_download(token);

  $('#exportForm').submit();
}

//---- images
function addImage() {
	$('#imageModal').modal('show');
}

$("#image").change(function(){
	if($(this).val() != '')
	{
		var file 		= this.files[0];
		var name		= file.name;
		var type 		= file.type;
		var size		= file.size;

		if(file.type != 'image/png' && file.type != 'image/jpg' && file.type != 'image/gif' && file.type != 'image/jpeg' )
		{
			swal("รูปแบบไฟล์ไม่ถูกต้อง", "กรุณาเลือกไฟล์นามสกุล jpg, jpeg, png หรือ gif เท่านั้น", "error");
			$(this).val('');
			return false;
		}

		if( size > 2000000 )
		{
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 2 MB", "error");
			$(this).val('');
			return false;
		}

		readURL(this);

		$("#btn-select-file").css("display", "none");
		$("#block-image").animate({opacity:1}, 1000);
	}
});


function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
			$('#previewImg').html('<img id="previewImg" src="'+e.target.result+'" width="200px" alt="รูปสินค้า" />');

      $('#img-blob').val(e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

function getImage() {
	var blob = $('#img-blob').val();
	$("#imageModal").modal('hide');

	if(blob.length) {
    $('#so-image').attr('src', blob);
		$('#btn-add-img').addClass('hide');
		$('#btn-del-img').removeClass('hide');
		$('#btn-save-img').removeClass('hide');
	}
}


function saveImage() {
	let code = $('#code').val();
	let blobImage = $('#img-blob').val();

	if( blobImage == '') {
		swal('ข้อผิดพลาด', 'ไม่สามารถอ่านข้อมูลรูปภาพที่แนบได้ กรุณาแนบไฟล์ใหม่อีกครั้ง', 'error');
		return false;
	}

	$.ajax({
		url:HOME + 'save_image',
		type:'POST',
		cache:false,
		data:{
			'code' : code,
			'imageData' : blobImage
		},
		success:function(rs) {
			if(rs == 'success') {
				swal({
					title:'Success!',
					type:'success',
					timer:1000
				});

        let path = $('#image-path').val();
        $('#so-image').attr('src', path);
        $('#image-link').attr('href', path);
				$('#btn-save-img').addClass('hide');
				$('#btn-add-img').html('<i class="fa fa-refresh"></i>').removeClass('hide');
				$('#btn-del-img').removeClass('hide');

        setColorbox();
			}
			else {
				swal({
					title:'Error!',
					type:'error',
					text:rs,
					html:true
				});
			}
		}
	})
}


function removeFile()
{
	let img = $('#prev-image').val();
	$("#previewImg").html('');
	$('#img-blob').val('');
	$("#block-image").css("opacity","0");
	$("#btn-select-file").css('display', '');
	$("#image").val('');
}

function removeImage() {
	let img = $('#prev-image').val();
	$('#so-img-preview').html('<img class="editable img-responsive" id="so-image" src="'+img+'" style="width-100%; height:100%; max-width:160px; max-height:160px; border-radius:10px;" alt="Item image" />');
	$("#previewImg").html('');
	$('#img-blob').val('');
	$("#block-image").css("opacity","0");
	$("#btn-select-file").css('display', '');
	$("#image").val('');
	$('#btn-add-img').removeClass('hide');
	$('#btn-del-img').addClass('hide');
}

function deleteImage()
{
	var code = $('#code').val();

  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบรูปภาพ หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#FA5858",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function() {
			setTimeout(() => {
				$.ajax({
					url: HOME + 'delete_image',
					type:"POST",
					cache:"false",
					data:{
						"code" : code
					},
					success: function(rs) {
						if( rs == 'success' )
						{
							swal({
								title:'Success',
								type:'success',
								timer:1000
							})

							let path = $('#no-img-path').val();
							$('#so-image').attr('src', path);
              $('#image-link').attr('href', path);
							$('#btn-save-img').addClass('hide');
							$('#btn-add-img').html('<i class="fa fa-plus"></i>').removeClass('hide');
							$('#btn-del-img').addClass('hide');

              setColorbox();
						}
						else
						{
							swal({
								title:'Error!',
								text:rs,
								type:'error'
							})
						}
					},
					error: function(rs) {
						swal({
							title:'Error!',
							text:"Error-" + rs.status + ": "+rs.statusText,
							type:"error"
						})
					}
				});
			}, 200)
	});
}

function setColorbox()
{
  var colorbox_params = {
    rel: 'colorbox',
    reposition: true,
    scalePhotos: true,
    scrolling: false,
    previous: '<i class="fa fa-arrow-left"></i>',
    next: '<i class="fa fa-arrow-right"></i>',
    close: 'X',
    current: '{current} of {total}',
    maxWidth: '800px',
    maxHeight: '800px',
    opacity:0.5,
    speed: 500,
    onComplete: function(){
      $.colorbox.resize();
    }
  }

  $('[data-rel="colorbox"]').colorbox(colorbox_params);
}


function viewSlip(imageUrl)
{
	var image = '<img src="'+imageUrl+'" width="100%" />';
	$("#imageBody").html(image);
	$("#imageModal").modal('show');
}


function createInvoice() {
	$('.h').clearError();
	let code = $('#code').val();
	let tax_id = $('#tax-id').val().trim();
	let addr = $('#address').val().trim();
	let err = 0;

	if(tax_id.length == 0 || addr.length == 0) {
		showCustomerModal();
	}
	else {
		let h = {
			'code' : code,
			'customer_name' : $('#customer-name').val().trim(),
			'tax_id' : $('#tax-id').val().trim(),
			'branch_code' : $('#branch-code').val().trim(),
			'branch_name' : $('#branch-name').val().trim(),
			'address' : $('#address').val().trim(),
			'sub_district' : $('#sub-district').val().trim(),
			'district' : $('#district').val().trim(),
			'province' : $('#province').val().trim(),
			'postcode' : $('#postcode').val().trim(),
			'phone' : $('#phone').val().trim(),
			'is_company' : $('#is-company').is(':checked') ? 1 : 0
		};

		if(h.customer_name.length ==  0) {
			$('#customer-name').hasError();
			err++;
		}

		if(h.tax_id.length < 10) {
			$('#tax-id').hasError();
			err++;
		}

		if(h.address.length == 0) {
			$('#address').hasError();
			err++;
		}

		if(h.sub_district.length == 0) {
			$('#sub-district').hasError();
			err++;
		}

		if(h.district.length == 0) {
			$('#district').hasError();
			err++;
		}

		if(h.province.length == 0) {
			$('#province').hasError();
			err++;
		}

		if(h.is_company == 1 && (h.branch_code.length == 0 || h.branch_name.length == 0))
		{
			$('#branch-code').hasError();
			$('#branch-name').hasError();
			err++;
		}

		if(err > 0) {
			return false;
		}

		swal({
			title:'ยืนยันข้อมูล',
			text:'ตรวจสอบข้อมูลลูกค้าว่าถูกต้องแล้วใช่หรือไม่ ?',
			type:'warning',
			showCancelButton:true,
			cancelButtonText:'No',
			confirmButtonText:'Yes',
			closeOnConfirm:true
		}, function() {
			setTimeout(() => {
				load_in();

				$.ajax({
					url:HOME + 'create_invoice',
					type:'POST',
					cache:false,
					data:{
						'code' : code
					},
					success:function(rs) {
						load_out();

						if( isJson(rs)) {
							let ds = JSON.parse(rs);

							if(ds.status == 'success') {
								if(ds.ex == 1) {
									printDownPaymentInvoice(ds.code);
									setTimeout(() => {
										window.location.reload();
									}, 200);
								}
								else {
									setTimeout(() => {
										swal({
											title:'ข้อผิดพลาด',
											text:'สร้างเอกสารสำเร็จแต่ส่งเข้าระบบ SAP ไม่สำเร็จ กรุณากดส่งใหม่ภายหลัง<br/>ต้องการพิมพ์ใบกำกับภาษีหรือไม่ ?',
											type:'info',
											html:true,
											showCancelButton:true,
											cancelButtonText:'No',
											confirmButtonText:'Yes',
											closeOnConfirm:true
										}, function() {
											setTimeout(() => {
												printDownPaymentInvoice(ds.code);

												setTimeout(() => {
													window.location.reload();
												}, 200);
											}, 500);
										});
									}, 100);
								}
							}
							else {
								setTimeout(() => {
									swal({
										title:'Error!',
										text:ds.message,
										type:'error',
										html:true
									})
								}, 100);
							}
						}
						else {
							setTimeout(() => {
								swal({
									title:'Error!',
									text:rs,
									type:'error',
									html:true
								})
							}, 100);
						}
					},
					error:function(rs) {
						load_out();

						swal({
							title:'Error!',
							text:rs.responeText,
							type:'error',
							html:true
						});
					}
				})
			}, 200);
		})
	}
}


function updateHeader() {
	$('.h').clearError();
	let code = $('#code').val();
	let tax_id = $('#tax-id').val().trim();
	let addr = $('#address').val().trim();
	let invoice_code = $('#invoice-code').val();
	let err = 0;

	if(tax_id.length == 0 || addr.length == 0) {
		showCustomerModal();
	}
	else {
		let h = {
			'code' : code,
			'customer_name' : $('#customer-name').val().trim(),
			'tax_id' : $('#tax-id').val().trim(),
			'branch_code' : $('#branch-code').val().trim(),
			'branch_name' : $('#branch-name').val().trim(),
			'address' : $('#address').val().trim(),
			'sub_district' : $('#sub-district').val().trim(),
			'district' : $('#district').val().trim(),
			'province' : $('#province').val().trim(),
			'postcode' : $('#postcode').val().trim(),
			'phone' : $('#phone').val().trim(),
			'is_company' : $('#is-company').is(':checked') ? 1 : 0
		};

		if(h.customer_name.length ==  0) {
			$('#customer-name').hasError();
			err++;
		}

		if(h.tax_id.length < 10) {
			$('#tax-id').hasError();
			err++;
		}

		if(h.address.length == 0) {
			$('#address').hasError();
			err++;
		}

		if(h.sub_district.length == 0) {
			$('#sub-district').hasError();
			err++;
		}

		if(h.district.length == 0) {
			$('#district').hasError();
			err++;
		}

		if(h.province.length == 0) {
			$('#province').hasError();
			err++;
		}

		if(err > 0) {
			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'update_header',
			type:'POST',
			cache:false,
			data:{
				'data' : JSON.stringify(h)
			},
			success:function(rs) {
				load_out();

				if(rs.trim() == 'success') {

					if(invoice_code.length == 0) {
						swal({
							title:'สำเร็จ',
							text:'ปรับปรุงข้อมูลสำเร็จ ต้องการสร้างใบกำกับภาษีต่อหรือไม่',
							type:'success',
							showCancelButton:true,
							cancelButtonText:'No',
							confirmButtonText:'Yes',
							closeOnConfirm:true
						}, function() {
							setTimeout(() => {
								createInvoice();
							}, 200);
						})
					}
				}
				else {
					load_out();

					swal({
						title:'Error!',
						text:rs,
						type:'error',
						html:true
					})
				}
			},
			error:function(rs) {
				load_out();

				swal({
					title:'Error!',
					text:rs.responeText,
					type:'error',
					html:true
				})
			}
		})
	}
}

function checkAll() {
	if($('#chk-all').is(':checked')) {
		$('.chk').prop('checked', true);
	}
	else {
		$('.chk').prop('checked', false);
	}
}


function exportToSap() {
	if( $('.chk:checked').length > 0) {
		let list = [];

		$('.chk:checked').each(function() {
			list.push($(this).val());
		});

		if(list.length > 0) {

			swal({
				title:'Export to SAP',
				text:'ต้องการส่งใบรับมัดจำตามรายการที่เลือกไปยังระบบ SAP หรือไม่ ?',
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
						url:HOME + 'export_to_sap',
						type:'POST',
						cache:false,
						data:{
							'list' : JSON.stringify(list)
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

function exportIncomming(code) {
	load_in();

	$.ajax({
		url:HOME + 'export_incomming',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();

			if(rs.trim() === 'success') {
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
		error:function(rs) {
			load_out();

			swal({
				title:'Error!',
				text:rs.responseText,
				type:'error',
				html:true
			})
		}
	})
}


function exportDownpayment(code) {
	load_in();

	$.ajax({
		url:HOME + 'export_down_payment_invoice',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();

			if(rs.trim() === 'success') {
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
		error:function(rs) {
			load_out();

			swal({
				title:'Error!',
				text:rs.responseText,
				type:'error',
				html:true
			})
		}
	})
}


function toggleFormBranch() {
	if($('#form-is-company').is(':checked')) {
		$('#form-branch-code').val('00000');
		$('#form-branch-name').val('สำนักงานใหญ่');
	}
	else {
		$('#form-branch-code').val('');
		$('#form-branch-name').val('');
	}
}

function toggleBranch() {
	let bCode = $('#branch-code').val();
	if($('#is-company').is(':checked')) {
		if(bCode.length == 0) {
			$('#branch-code').val('00000');
			$('#branch-name').val('สำนักงานใหญ่');
		}
	}
	else {
		$('#branch-code').val('');
		$('#branch-name').val('');
	}
}

function showCustomerModal() {
	$('#customerModal').modal('show');
	$('#customerModal').on('shown.bs.modal', () => {
		$('#tax-search').focus();
	})
}

$('#tax-search').autocomplete({
	source:BASE_URL + 'auto_complete/get_invoice_customer_by_tax',
	autoFocus:true,
	open:function(event) {
		let ul = $(this).autocomplete('widget');
		ul.css('width', 'auto');
	},
	select:function(event, ui) {
		$('#cust-id').val(ui.item.id);
		$('#form-name').val(ui.item.name);
		$('#form-tax-id').val(ui.item.tax_id);
		$('#form-branch-code').val(ui.item.branch_code);
		$('#form-branch-name').val(ui.item.branch_name);
		$('#form-address').val(ui.item.address);
		$('#form-subDistrict').val(ui.item.sub_district);
		$('#form-district').val(ui.item.district);
		$('#form-province').val(ui.item.province);
		$('#form-postcode').val(ui.item.postcode);
		$('#form-phone').val(ui.item.phone);

		if(ui.item.is_company == '1') {
			$('#form-is-company').prop('checked', true);
		}
		else {
			$('#form-is-company').prop('checked', false);
		}
	},
	close:function() {
		let arr = $(this).val().split(' | ');
		$(this).val(arr[0]);
	}
})

$('#phone-search').autocomplete({
	source:BASE_URL + 'auto_complete/get_invoice_customer_by_phone',
	autoFocus:true,
	open:function(event) {
		let ul = $(this).autocomplete('widget');
		ul.css('width', 'auto');
	},
	select:function(event, ui) {
		$('#cust-id').val(ui.item.id);
		$('#form-name').val(ui.item.name);
		$('#form-tax-id').val(ui.item.tax_id);
		$('#form-branch-code').val(ui.item.branch_code);
		$('#form-branch-name').val(ui.item.branch_name);
		$('#form-address').val(ui.item.address);
		$('#form-subDistrict').val(ui.item.sub_district);
		$('#form-district').val(ui.item.district);
		$('#form-province').val(ui.item.province);
		$('#form-postcode').val(ui.item.postcode);
		$('#form-phone').val(ui.item.phone);

		if(ui.item.is_company == '1') {
			$('#form-is-company').prop('checked', true);
		}
		else {
			$('#form-is-company').prop('checked', false);
		}
	},
	close:function() {
		let arr = $(this).val().split(' | ');
		$(this).val(arr[0]);
	}
})


function addCustomer() {
	$('.cust-form').removeClass('has-error');

	let h = {
		'customer_id' : $('#cust-id').val(),
		'customer_name' : $.trim($('#form-name').val()),
		'tax_id' : $('#form-tax-id').val(),
		'branch_code' : $.trim($('#form-branch-code').val()),
		'branch_name' : $.trim($('#form-branch-name').val()),
		'address' : $.trim($('#form-address').val()),
		'sub_district' : $.trim($('#form-subDistrict').val()),
		'district' : $.trim($('#form-district').val()),
		'province' : $.trim($('#form-province').val()),
		'postcode' : $.trim($('#form-postcode').val()),
		'phone' : $.trim($('#form-phone').val()),
		'is_company' : $('#form-is-company').is(':checked') ? 1 : 0
	};

	if(h.customer_name.length == 0) {
		$('#form-name').addClass('has-error');
		return false;
	}

	if(h.tax_id.length < 13) {
		$('#form-tax-id').addClass('has-error');
		return false;
	}

	if(h.is_company && (h.branch_code.length == 0 || h.branch_name.length == 0)) {
		if(h.branch_code.length == 0) {
			$('#form-branch-code').addClass('has-error');
		}

		if(h.branch_name.length == 0) {
			$('#form-branch-name').addClass('has-error');
		}

		return false;
	}

	if(h.address.length == 0) {
		$('#form-address').addClass('has-error');
		return false;
	}

	$('#customerModal').modal('hide');

	load_in();

	$.ajax({
		url:BASE_URL + 'orders/order_invoice/add_invoice_customer',
		type:'POST',
		cache:false,
		data:{
			"data" : JSON.stringify(h)
		},
		success:function(rs) {
			load_out();

			if(isJson(rs)) {
				let ds = JSON.parse(rs);

				if(ds.status == 'success') {
					$('#customer-name').val(h.customer_name);
					$('#tax-id').val(h.tax_id);
					$('#branch-code').val(h.branch_code);
					$('#branch-name').val(h.branch_name);
					$('#address').val(h.address);
					$('#sub-district').val(h.sub_district);
					$('#district').val(h.district);
					$('#province').val(h.province);
					$('#postcode').val(h.postcode);
					$('#phone').val(h.phone);

					if(h.is_company) {
						$('#is-company').prop('checked', true);
					}
					else {
						$('#is-company').prop('checked', false);
					}

					updateHeader();
				}
				else {
					message = '<h4 class="title-xs red text-center">'+ds.message+'</h4>';
					$('#cust-result-table').html(message);
					$('.cust-form').val('').attr('disabled', 'disabled');
					$('#tax-search').focus();
				}
			}
			else {
				message = '<h4 class="title-xs red text-center">'+rs+'</h4>';
				$('#cust-result-table').html(message);
				$('#tax-search').focus();
			}
		}
	})
}


function clearForm() {
	$('.cust-form').val('');
	$('#phone-search').val('');
  $('#tax-search').val('').focus();
}
