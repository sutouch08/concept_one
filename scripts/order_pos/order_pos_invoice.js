function createTaxInvoice() {
  $('.h').removeClass('has-error');

  let h = {
    'bill_code' : $('#code').val(),
    'billCode' : $('#code').val(),
    'refType' : 'POS',
		'vat_type' : $('#vat-type').val(),
		'is_term' : $('#is-term').val(),
		'taxStatus' : 'Y',
		'date_add' : $('#date').val(),
		'customer_code' : $('#customer-code').val(),
		'customer_name' : $('#customer-name').val(),
		'customer_ref' : $('#customer-name').val(),
		'phone' : $('#phone').val(),
		'branch_code' : $('#branch-code').val(),
		'branch_name' : $('#branch-name').val(),
		'tax_id' : $('#tax-id').val(),
		'address' : $('#address').val(),
		'sub_district' : $('#sub-district').val(),
		'district' : $('#district').val(),
		'province' : $('#province').val(),
		'postcode' : $('#postcode').val(),
    'is_company' : $('#is-company').is(':checked') ? 1 : 0,
		'sale_id' : $('#sale_id').val(),
    'remark' : "",
		'amountBfDisc' : parseDefault(parseFloat(removeCommas($('#total-amount').val())), 0),
		'billDiscPrcnt' : parseDefault(parseFloat($('#bill-disc-percent').val()), 0),
		'billDiscAmount' : parseDefault(parseFloat(removeCommas($('#bill-disc-amount').val())), 0),
		'whtPrcnt' : parseDefault(parseFloat($('#whtPrcnt').val()), 0),
		'whtAmount' : parseDefault(parseFloat($('#wht-amount').val()), 0),
		'vatSum' : parseDefault(parseFloat(removeCommas($('#vat-total').val())), 0),
		'docTotal' : parseDefault(parseFloat(removeCommas($('#doc-total').val())), 0),
    'totalDownAmount' : parseDefault(parseFloat($('#down-payment-amount').val()), 0)
	}

  if(h.customer_code.length == 0 || h.customer_name.length == 0) {
    $('#customer_code').addClass('has-error');
    $('#customer_name').addClass('has-error');
    swal("กรุณาระบุลูกค้า");
    return false;
  }

  if(h.tax_id.length == 0 || h.address.length == 0 || h.sub_district.length == 0 || h.district.length == 0 || h.province.length == 0) {
    showCustomerModal();
    return false;
  }

  let count = 0;

  $('.bill-item').each(function() {
    let qty = parseDefault(parseFloat($(this).data('qty')));
    count++;
  });

  if(count == 0) {
    swal("ไม่พบรายการขาย กรุณาตรวจสอบ");
    return false;
  }

  load_in();

  $.ajax({
    url:BASE_URL + 'orders/order_invoice/add_invoice',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          swal({
						title:'Success',
						text:'สร้าง invoice เลขที่ '+ds.invoice_code+' สำเร็จ <br/> ต้องการพิมพ์ Invoice หรือไม่ ? ',
						type:'success',
						showCancelButton:true,
						closeOnConfirm:true,
						html:true
					}, function(isConfirm) {
						if(isConfirm) {
							viewInvoice(ds.invoice_code);

							setTimeout(() => {
								window.location.reload();
							},100);
						}
						else {
							window.location.reload();
						}
					});
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
          type:'error',
          html:true
        })
      }
    },
    error:function(xhr) {
      load_out();

      swal({
        title:'Error!',
        text:xhr.responseText,
        type:'error',
        html:true
      })
    }
  })
}


function createInvoice() {
  $('.h').removeClass('has-error');

  let h = {
    'bill_code' : $('#code').val(),
    'billCode' : $('#code').val(),
    'refType' : 'POS',
		'vat_type' : $('#vat-type').val(),
		'is_term' : $('#is-term').val(),
		'taxStatus' : 'N',
		'date_add' : $('#date').val(),
		'customer_code' : $('#customer-code').val(),
		'customer_name' : $('#customer-name').val(),
		'customer_ref' : $('#customer-name').val(),
		'phone' : $('#phone').val(),
		'branch_code' : $('#branch-code').val(),
		'branch_name' : $('#branch-name').val(),
		'tax_id' : $('#tax-id').val(),
		'address' : $('#address').val(),
		'sub_district' : $('#sub-district').val(),
		'district' : $('#district').val(),
		'province' : $('#province').val(),
		'postcode' : $('#postcode').val(),
    'is_company' : $('#is-company').is(':checked') ? 1 : 0,
		'sale_id' : $('#sale_id').val(),
    'remark' : "",
		'amountBfDisc' : parseDefault(parseFloat(removeCommas($('#total-amount').val())), 0),
		'billDiscPrcnt' : parseDefault(parseFloat($('#bill-disc-percent').val()), 0),
		'billDiscAmount' : parseDefault(parseFloat(removeCommas($('#bill-disc-amount').val())), 0),
		'whtPrcnt' : parseDefault(parseFloat($('#whtPrcnt').val()), 0),
		'whtAmount' : parseDefault(parseFloat($('#wht-amount').val()), 0),
		'vatSum' : parseDefault(parseFloat(removeCommas($('#vat-total').val())), 0),
		'docTotal' : parseDefault(parseFloat(removeCommas($('#doc-total').val())), 0),
    'totalDownAmount' : parseDefault(parseFloat($('#down-payment-amount').val()), 0)
	}

  if(h.customer_code.length == 0 || h.customer_name.length == 0) {
    $('#customer_code').addClass('has-error');
    $('#customer_name').addClass('has-error');
    swal("กรุณาระบุลูกค้า");
    return false;
  }

  let count = 0;

  $('.bill-item').each(function() {
    let qty = parseDefault(parseFloat($(this).data('qty')));
    count++;
  });

  if(count == 0) {
    swal("ไม่พบรายการขาย กรุณาตรวจสอบ");
    return false;
  }

  load_in();

  $.ajax({
    url:BASE_URL + 'orders/order_invoice/add_invoice',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          swal({
						title:'Success',
						text:'สร้าง invoice เลขที่ '+ds.invoice_code+' สำเร็จ <br/> ต้องการพิมพ์ Invoice หรือไม่ ? ',
						type:'success',
						showCancelButton:true,
						closeOnConfirm:true,
						html:true
					}, function(isConfirm) {
						if(isConfirm) {
							viewInvoice(ds.invoice_code);

							setTimeout(() => {
								window.location.reload();
							},100);
						}
						else {
							window.location.reload();
						}
					});
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
          type:'error',
          html:true
        })
      }
    },
    error:function(xhr) {
      load_out();

      swal({
        title:'Error!',
        text:xhr.responseText,
        type:'error',
        html:true
      })
    }
  })
}


function createInvoiceByCheckedBill() {
  let bills = [];
  $('.chk:checked').each(function() {
    bills.push($(this).val());
  });

  if(bills.length == 0) {
    swal("กรุณาเลือกบิลอย่างน้อย 1 ใบ");
    return false;
  }

  swal({
    title:'สร้าง Invoice',
    text:'ต้องการสร้าง Invoice ตามบิลที่เลือกหรือไม่ ?',
    type:'info',
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  }, function() {
    setTimeout(() => {
      load_in();

      $.ajax({
        url:BASE_URL + 'orders/order_invoice/add_each_invoice_by_bills',
        type:'POST',
        cache:false,
        data:{
          'bills' : JSON.stringify(bills)
        },
        success:function(rs) {
          load_out();

          if(isJson(rs)) {
            let ds = JSON.parse(rs);

            if(ds.status == 'success') {
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
                text:ds.message,
                type:'error',
                html:true
              }, function() {
                window.location.reload();
              });
            }
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
        error:function(xhr) {
          load_out();
          swal({
            title:'Error!',
            text:xhr.responseText,
            type:'error',
            html:true
          });
        }
      })
    }, 100);
  })
}
