
var click = 0;

function saveAdd(isDraft = 0) {
  if(click > 0) {
    return false;
  }

  $('#btn-save').attr('disabled', 'disabled');
  $('#btn-draft').attr('disabled', 'disabled');

  click = 1;

  setTimeout(() => {
    let err = 0;
    let totalAmount = parseDefault(parseFloat(removeCommas($('#total-amount-label').val())), 0.00);
    let discPrcnt = parseDefault(parseFloat($('#discPrcnt').val()), 0.00);
    let billDiscAmount = parseDefault(parseFloat(removeCommas($('#disc-amount-label').val())), 0.00);
    let whtPrcnt = parseDefault(parseFloat($('#whtPrcnt').val()), 0.00);
    let whtAmount = parseDefault(parseFloat(removeCommas($('#wht-amount-label').val())), 0.00);
    let vatSum = parseDefault(parseFloat(removeCommas($('#vat-total-label').val())), 0.00);
    let docTotal = parseDefault(parseFloat(removeCommas($('#doc-total-label').val())), 0.00);
    let depAmount = parseDefault(parseFloat(removeCommas($('#dep-amount').val())), 0.00);
    let img = $('#img-blob').val();
    let address = $('#address').val();
    let sub_district = $('#sub-district').val();
    let district = $('#district').val();
    let province = $('#province').val();
    let postcode = $('#postcode').val();

    let h = {
      'date_add' : $('#date_add').val(),
      'due_date' : $('#due_date').val(),
      'customer_code' : $('#customer-code').val(),
      'customer_name' : $('#customer-name').val(),
      'branch_code' : $('#branch-code').val(),
      'branch_name' : $('#branch-name').val(),
      'tax_id' : $('#tax-id').val(),
      'job_title' : $('#job-title').val(),
      'channels_code' : $('#channels').val(),
      'vat_type' : $('#vat-type').val(),
      'TaxStatus' : $('#tax-status').val(),
      'vat_rate' : 0.00,
      'job_type' : $('#job-type').val(),
      'whsCode' : $('#warehouse').val(),
      'customer_ref' : $('#customer-ref').val(),
      'address' : address,
      'sub_district' : sub_district,
      'district' : district,
      'province' : province,
      'postcode' : postcode,
      'customer_address' : parseAddress(address, sub_district, district, province, postcode),
      'phone' : $('#phone').val(),
      'sale_id' : $('#sale_id').val(),
      'remark' : $('#remark').val(),
      'totalAmount' : totalAmount,
      'discPrcnt' : discPrcnt,
      'discAmount' : billDiscAmount,
      'vatSum' : vatSum,
      'docTotal' : docTotal,
      'depAmount' : depAmount,
      'whtPrcnt' : whtPrcnt,
      'whtAmount' : whtAmount,
      'is_term' : $('#is-term').val(),
      'isDraft' : isDraft,
      'design' : $('#design').val(),
      'img' : img
    }


    $('.h').removeClass('has-error'); //--- clear header error

    if(h.is_term === "") {
      swal("กรุณาเลือกเล่มเอกสาร");
      $('#is-term').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if(h.vat_type == "" || h.TaxStatus == "") {
      swal("กรุณาเลือกชนิต VAT");
      $('#vat-type').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if( ! isDate(h.date_add)) {
      swal("วันที่ไม่ถูกต้อง");
      $('#date_add').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if(h.customer_code == '' || h.customer_code == undefined) {
      swal("รหัสลูกค้าไม่ถูกต้อง");
      $('#customer-code').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if( ! isDate(h.due_date)) {
      swal("วันที่ส่งของไม่ถูกต้อง");
      $('#due_date').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if(h.job_type == "") {
      swal("กรุณาเลือกประเภทงาน");
      $('#job-type').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if(h.job_title == "") {
      swal("กรุณาระบุชื่องาน");
      $('#job-title').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if(h.customer_ref == '') {
      swal("กรุณาระบุลูกค้า");
      $('#customer-ref').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }


    if(h.channels_code == "") {
      swal("กรุณาระบุช่องทางขาย");
      $('#channels').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if(h.design === "") {
      swal("กรุณาเลือกการออกแบบ");
      $('#design').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if($('.row-qty').length == 0) {
      swal("ไม่พบรายการสินค้า");

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    $('.row-qty').removeClass('has-error');
    $('.row-price').removeClass('has-error');
    $('.row-disc').removeClass('has-error');

    let items = [];

    $('.row-qty').each(function() {
      let el = $(this);
      let no = el.data('no')
      let price = parseDefault(parseFloat(removeCommas($('#price-label-'+no).val())), 0.00);
      let qty = parseDefault(parseFloat(removeCommas(el.val())), 0.00);
      let total = price * qty;
      let discLabel = $('#disc-label-'+no).val();
      let discAmount = parseDefault(parseFloat($('#disc-amount-'+no).val()), 0.00);
      let totalAmount = parseDefault(parseFloat(removeCommas($('#total-label-'+no).val())), 0.00);
      let product_name = $.trim($('#pd-name-'+no).val());
      product_name = product_name.length ? product_name : el.data('name');

      if(price < 0) {
        err++;
        $('#price-label-'+no).addClass('has-error');
      }

      if(qty <= 0) {
        err++;
        el.addClass('has-error');
      }


      if(discAmount < 0 || discAmount > price) {
        err++;
        $('#disc-label-'+no).addClass('has-error');
      }

      if(err == 0) {
        let row = {
          'product_code' : el.data('code'),
          'product_name' : product_name,
          'style_code' : el.data('style'),
          'unit_code' : el.data('uom'),
          'cost' : el.data('cost'),
          'price' : price,
          'qty' : qty,
          'vat_code' : el.data('vatcode'),
          'vat_rate' : el.data('vatrate'),
          'discLabel' : discLabel,
          'discAmount' : discAmount,
          'totalAmount' : totalAmount,
          'is_count' : el.data('count')
        }

        items.push(row);
      }
    });

    $('#disc-amount-label').removeClass('has-error');



    if(billDiscAmount > totalAmount) {
      err++;
      $('#disc-amount-label').addClass('has-error');
    }

    if(err > 0) {
      swal({
        title:'Error!',
        text:"พบรายการที่ไม่ถูกต้อง กรุณาตรวจสอบ",
        type:'error'
      });


      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    let data = {
      'header' : h,
      'items' : items
    }

    load_in();

    $.ajax({
      url:HOME + 'add',
      type:'POST',
      cache:false,
      data: {
        'data' : JSON.stringify(data)
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
              viewDetail(ds.code);
            }, 1200);
          }
          else {
            swal({
              title:'Error!',
              text:ds.message,
              type:'error'
            });

            $('#btn-save').removeAttr('disabled');
            $('#btn-draft').removeAttr('disabled');
            click = 0;

          }
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error',
            html:true
          });

          $('#btn-save').removeAttr('disabled');
          $('#btn-draft').removeAttr('disabled');
          click = 0;

        }
      },
      error:function(xhr) {
        load_out();
        swal({
          title:'Error!',
          text:xhr.responeText,
          type:'error'
        });

        $('#btn-save').removeAttr('disabled');
        $('#btn-draft').removeAttr('disabled');
        click = 0;

      }
    })
  }, 200);
}


function saveUpdate(isDraft = 0) {
  if(click > 0) {
    return false;
  }

  $('#btn-save').attr('disabled', 'disabled');
  $('#btn-draft').attr('disabled', 'disabled');

  click = 1;

  setTimeout(() => {
    let err = 0;
    let code = $('#code').val();
    let totalAmount = parseDefault(parseFloat(removeCommas($('#total-amount-label').val())), 0.00);
    let discPrcnt = parseDefault(parseFloat($('#discPrcnt').val()), 0.00);
    let billDiscAmount = parseDefault(parseFloat(removeCommas($('#disc-amount-label').val())), 0.00);
    let whtPrcnt = parseDefault(parseFloat($('#whtPrcnt').val()), 0.00);
    let whtAmount = parseDefault(parseFloat(removeCommas($('#wht-amount-label').val())), 0.00);
    let vatSum = parseDefault(parseFloat(removeCommas($('#vat-total-label').val())), 0.00);
    let docTotal = parseDefault(parseFloat(removeCommas($('#doc-total-label').val())), 0.00);
    let depAmount = parseDefault(parseFloat(removeCommas($('#dep-amount').val())), 0.00);
    let img = $('#img-blob').val();
    let address = $('#address').val();
    let sub_district = $('#sub-district').val();
    let district = $('#district').val();
    let province = $('#province').val();
    let postcode = $('#postcode').val();

    if(depAmount > docTotal) {
      swal({
        title:'Oop !',
        text:'ยอดเงินมัดจำเกินมูลค่ารวม กรุณาตรวจสอบ',
        type:'warning'
      });

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');

      click = 0;

      return false;
    }

    let h = {
      'date_add' : $('#date_add').val(),
      'due_date' : $('#due_date').val(),
      'customer_code' : $('#customer-code').val(),
      'customer_name' : $('#customer-name').val(),
      'branch_code' : $('#branch-code').val(),
      'branch_name' : $('#branch-name').val(),
      'tax_id' : $('#tax-id').val(),
      'job_title' : $('#job-title').val(),
      'channels_code' : $('#channels').val(),
      'vat_type' : $('#vat-type').val(),
      'TaxStatus' : $('#tax-status').val(),
      'vat_rate' : $('#vat-type option:selected').data('rate'),
      'job_type' : $('#job-type').val(),
      'whsCode' : $('#warehouse').val(),
      'customer_ref' : $('#customer-ref').val(),
      'address' : address,
      'sub_district' : sub_district,
      'district' : district,
      'province' : province,
      'postcode' : postcode,
      'customer_address' : parseAddress(address, sub_district, district, province, postcode),
      'phone' : $('#phone').val(),
      'sale_id' : $('#sale_id').val(),
      'remark' : $('#remark').val(),
      'totalAmount' : totalAmount,
      'discPrcnt' : discPrcnt,
      'discAmount' : billDiscAmount,
      'vatSum' : vatSum,
      'docTotal' : docTotal,
      'depAmount' : depAmount,
      'whtPrcnt' : whtPrcnt,
      'whtAmount' : whtAmount,
      'is_term' : $('#is-term').val(),
      'design' : $('#design').val(),
      'isDraft' : isDraft,
      'img' : img
    }


    $('.h').removeClass('has-error'); //--- clear header error

    if(h.is_term === "") {
      swal("กรุณาเลือกเล่มเอกสาร");
      $('#is-term').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if(h.vat_type == "" || h.TaxStatus == "") {
      swal("กรุณาเลือกชนิต VAT");
      $('#vat-type').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if( ! isDate(h.date_add)) {
      swal("วันที่ไม่ถูกต้อง");
      $('#date_add').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if(h.customer_code == '' || h.customer_code == undefined) {
      swal("รหัสลูกค้าไม่ถูกต้อง");
      $('#customer-code').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if( ! isDate(h.due_date)) {
      swal("วันที่ส่งของไม่ถูกต้อง");
      $('#due_date').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }


    if(h.job_type == "") {
      swal("กรุณาเลือกประเภทงาน");
      $('#job-type').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if(h.job_title == "") {
      swal("กรุณาระบุชื่องาน");
      $('#job-title').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if(h.customer_ref == '') {
      swal("กรุณาระบุลูกค้า");
      $('#customer-ref').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }


    if(h.channels_code == "") {
      swal("กรุณาระบุช่องทางขาย");
      $('#channels').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if(h.design === "") {
      swal("กรุณาเลือกการออกแบบ");
      $('#design').addClass('has-error');

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    if($('.row-qty').length == 0) {
      swal("ไม่พบรายการสินค้า");

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');
      click = 0;

      return false;
    }

    $('.row-qty').removeClass('has-error');
    $('.row-price').removeClass('has-error');
    $('.row-disc').removeClass('has-error');

    let items = [];

    $('.row-qty').each(function() {
      let el = $(this);
      let no = el.data('no')
      let price = parseDefault(parseFloat(removeCommas($('#price-label-'+no).val())), 0.00);
      let qty = parseDefault(parseFloat(removeCommas(el.val())), 0.00);
      let openQty = parseDefault(parseFloat(removeCommas($('#open-qty-'+no).val())), qty);
      let total = price * qty;
      let discLabel = $('#disc-label-'+no).val();
      let discAmount = parseDefault(parseFloat($('#disc-amount-'+no).val()), 0.00);
      let totalAmount = parseDefault(parseFloat(removeCommas($('#total-label-'+no).val())), 0.00);
      let product_name = $.trim($('#pd-name-'+no).val());

      product_name = product_name.length ? product_name : el.data('name');

      if(price < 0) {
        err++;
        $('#price-label-'+no).addClass('has-error');
      }

      if(qty <= 0) {
        err++;
        el.addClass('has-error');
      }

      if(discAmount < 0 || discAmount > price) {
        err++;
        $('#disc-label-'+no).addClass('has-error');
      }

      if(err == 0) {
        let row = {
          'id' : el.data('id'),
          'no' : no,
          'line_status' : el.data('status'),
          'product_code' : el.data('code'),
          'product_name' : product_name,
          'style_code' : el.data('style'),
          'unit_code' : el.data('uom'),
          'cost' : el.data('cost'),
          'price' : price,
          'qty' : qty,
          'openQty' : openQty,
          'vat_code' : el.data('vatcode'),
          'vat_rate' : el.data('vatrate'),
          'discLabel' : discLabel,
          'discAmount' : discAmount,
          'totalAmount' : totalAmount,
          'is_count' : el.data('count')
        }

        items.push(row);
      }
    });

    $('#disc-amount-label').removeClass('has-error');

    if(billDiscAmount > totalAmount) {
      err++;
      $('#disc-amount-label').addClass('has-error');
    }

    if(err > 0) {
      swal({
        title:'Error!',
        text:"พบรายการที่ไม่ถูกต้อง กรุณาตรวจสอบ",
        type:'error'
      });

      $('#btn-save').removeAttr('disabled');
      $('#btn-draft').removeAttr('disabled');

      click = 0;

      return false;
    }

    let data = {
      'header' : h,
      'items' : items
    }

    load_in();

    $.ajax({
      url:HOME + 'update',
      type:'POST',
      cache:false,
      data: {
        'code' : code,
        'data' : JSON.stringify(data)
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
              viewDetail(code);
            }, 1200);
          }
          else {
            swal({
              title:'Error!',
              text:ds.message,
              type:'error'
            });

            $('#btn-save').removeAttr('disabled');
            $('#btn-draft').removeAttr('disabled');
            click = 0;

          }
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error',
            html:true
          });

          $('#btn-save').removeAttr('disabled');
          $('#btn-draft').removeAttr('disabled');
          click = 0;

        }
      },
      error:function(xhr) {
        load_out();
        swal({
          title:'Error!',
          text:xhr.responeText,
          type:'error'
        });

        $('#btn-save').removeAttr('disabled');
        $('#btn-draft').removeAttr('disabled');
        click = 0;

      }
    })
  }, 200);
}


function toggleVatType() {
  let type = $('#vat-type').val();
  let taxStatus = type == 'N' ? 'N' : 'Y';
  $('#tax-status').val(taxStatus);

  if(type == 'N') {
    $('#bill-vat').addClass('hide');
    $('#bill-wht').addClass('hide');
  }
  else {
    $('#bill-vat').removeClass('hide');
    $('#bill-wht').removeClass('hide');
  }

  item_init();
  recalTotal();
}


$('#customer-code').autocomplete({
  source:BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  open:function(event) {
    var $ul = $(this).autocomplete('widget')
    $ul.css('width', 'auto')
  },
  select:function(event, ui) {
    let code = ui.item.code
    let name = ui.item.name
    let tax_id = ui.item.tax_id

    if(code.length) {
      $('#customer-code').val(code);
      $('#customer-name').val(name);
      $('#tax-id').val(tax_id);

      get_bill_to_address(code);
    }
    else {
      $('#customer-code').val('');
      $('#customer-name').val('');
      $('#tax-id').val('');
    }
  },
  close:function() {
    let label = $(this).val();
    let arr = label.split(' | ');

    $(this).val(arr[0]);
  }
})


function get_bill_to_address(code) {
  if(code.length) {
    $.ajax({
      url:HOME + 'get_customer_bill_to_address',
      type:'GET',
      cach:false,
      data:{
        'code' : code
      },
      success:function(rs) {
        if(isJson(rs)) {
          let ds = JSON.parse(rs);
					if(ds.address != null && ds.address != undefined) {
						if(ds.status == 'success') {
							if(ds.address.length == 1) {
								let adr = ds.address[0];
								$('#phone').val(adr.phone);
								$('#branch-code').val(adr.branch_code);
								$('#branch-name').val(adr.branch_name);
								$('#address').val(adr.address);
								$('#sub-district').val(adr.sub_district);
								$('#district').val(adr.district);
								$('#province').val(adr.province);
								$('#postcode').val(adr.postcode);
							}
							else {
								let source = $('#bill-to-template').html();
								let output = $('#bill-to-table');

								render(source, ds.address, output);

								$('#billToModal').modal('show');
							}
						}
					}
        }
      }
    })
  }
}


$('#customer-ref').autocomplete({
  source:BASE_URL + 'auto_complete/get_sales_order_customer',
  autoFocus:true,
  open:function(event) {
    var $ul = $(this).autocomplete('widget')
    $ul.css('width', 'auto')
  },
  select:function(event, ui) {
    let name = ui.item.name

    if(name.length) {
      $('#customer-ref').val(ui.item.name)
      $('#branch-code').val(ui.item.branch_code);
      $('#branch-name').val(ui.item.branch_name);
      $('#address').val(ui.item.address);
      $('#phone').val(ui.item.phone);
      $('#sub-district').val(ui.item.sub_district);
      $('#district').val(ui.item.district);
      $('#province').val(ui.item.province);
      $('#postcode').val(ui.item.postcode);
    }
  },
  close:function() {
    let ad = $(this).val();
    let arr = ad.split(' | ');
    $('#customer-ref').val(arr[0]);
  }
})

$('#phone').autocomplete({
  source:BASE_URL + 'auto_complete/get_sales_order_customer_by_phone',
  autoFocus:true,
  open:function(event) {
    var $ul = $(this).autocomplete('widget')
    $ul.css('width', 'auto')
  },
  select:function(event, ui) {
    let name = ui.item.name

    if(name.length) {
      $('#customer-ref').val(ui.item.name);
      $('#tax-id').val(ui.item.tax_id);
      $('#branch-code').val(ui.item.branch_code);
      $('#branch-name').val(ui.item.branch_name);
      $('#address').val(ui.item.address);
      $('#phone').val(ui.item.phone);
      $('#sub-district').val(ui.item.sub_district);
      $('#district').val(ui.item.district);
      $('#province').val(ui.item.province);
      $('#postcode').val(ui.item.postcode);
    }
  },
  close:function() {
    let ph = $(this).val();
    let arr = ph.split(' | ');
    $('#phone').val(arr[0]);
  }
})

$('#customer-ref').keyup(function(e) {
  if(e.keyCode == 13) {
    $('#address').focus();
  }
})


$('#address').keyup(function(e) {
  if(e.keyCode == 13) {
    $('#phone').focus();
  }
})


function changeState(){
    var code = $("#code").val();
    var state = $("#stateList").val();


    if( state != 0){
      load_in();
        $.ajax({
            url:BASE_URL + 'orders/sales_order/change_state',
            type:"POST",
            cache:"false",
            data:{
              "code" : code,
              "state" : state
            },
            success:function(rs){
              load_out();
                var rs = $.trim(rs);
                if(rs == 'success'){
                    swal({
                      title:'success',
                      text:'status updated',
                      type:'success',
                      timer: 1000
                    });

                    setTimeout(function(){
                      window.location.reload();
                    }, 1500);

                }else{
                    swal("Error !", rs, "error");
                }
            }
        });
    }
}


function getSaleOrderDetail()
{
  let code = $('#code').val();

  load_in();

  $.ajax({
    url:HOME + 'get_details',
    type:'GET',
    cache: false,
    data:{
      'code' : code
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let source = $('#so-template').html();
          let data = ds.details;
          let output = $('#so-table');

          render(source, data, output);

          $('#soGrid').modal('show');
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
        });
      }
    }
  })
}

function takeAll() {
  $('.so-qty').each(function() {
    available = $(this).data('available');
    $(this).val(available);
  });
}

function clearAll() {
  $('.so-qty').each(function() {
    $(this).val('');
  })
}


function createWo()
{
  if(click > 0) {
    return false;
  }

  click = 1;

  $('#btn-create-wo').attr('disabled', 'disabled');

  $('.so-qty').removeClass('has-error');

  let err = 0;

  let rows = [];

  $('.so-qty').each(function() {
    let id = $(this).data('id');
    let pdCode = $(this).data('code');
    let qty = parseDefault(parseFloat($(this).val()), 0);
    let available = parseDefault(parseFloat($(this).data('available')), 0);

    if(qty > available) {
      $(this).addClass('has-error');
      err++;
    }
    else {
      if(qty > 0) {
        let row = {
          'qty' : qty,
          'product_code' : pdCode,
          'id' : id
        };

        rows.push(row);
      }
    }
  });

  if(err > 0 || rows.length == 0) {
    click = 0;
    $('#btn-create-wo').removeAttr('disabled');
    return false;
  }

  let code = $('#code').val();

  $('#soGrid').modal('hide');

  load_in();

  $.ajax({
    url:HOME + 'createWo',
    type:'POST',
    cache:false,
    data:{
      'so_code' : code,
      'details' : JSON.stringify(rows)
    },
    success:function(rs) {
      load_out();

      if( isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let code = ds.code;

          openWo(code);
          window.location.reload();
        }
        else {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error'
          });

          click = 0;
          $('#btn-create-wo').removeAttr('disabled');
        }
      }
      else {
        swal({
          title: "Error!",
          text:rs,
          type:'error',
          html:true
        })

        click = 0;
        $('#btn-create-wo').removeAttr('disabled');
      }
    }
  })
}


function createWq()
{
  if(click > 0) {
    return false;
  }

  click = 1;

  $('#btn-create-wq').attr('disabled', 'disabled');

  $('.so-qty').removeClass('has-error');

  let err = 0;

  let rows = [];

  $('.so-qty').each(function() {
    let id = $(this).data('id');
    let pdCode = $(this).data('code');
    let qty = parseDefault(parseFloat($(this).val()), 0);
    let available = parseDefault(parseFloat($(this).data('available')), 0);
    let isCount = $(this).data('iscount') == '1' ? 1 : 0;

    if(qty > available) {
      $(this).addClass('has-error');
      err++;
    }
    else {
      if(qty > 0) {
        if(isCount) {
          let row = {
            'qty' : qty,
            'product_code' : pdCode,
            'id' : id
          };

          rows.push(row);
        }
      }
    }
  });

  if(err > 0 || rows.length == 0) {
    click = 0;
    swal("ไม่พบรายการที่ต้องเบิก");
    $('#btn-create-wq').removeAttr('disabled');
    return false;
  }

  let code = $('#code').val();

  $('#soGrid').modal('hide');

  load_in();

  $.ajax({
    url:HOME + 'createWq',
    type:'POST',
    cache:false,
    data:{
      'so_code' : code,
      'details' : JSON.stringify(rows)
    },
    success:function(rs) {
      load_out();

      if( isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          let code = ds.code;

          openWq(code);
          window.location.reload();
        }
        else {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error'
          });

          click = 0;
          $('#btn-create-wq').removeAttr('disabled');
        }
      }
      else {
        swal({
          title: "Error!",
          text:rs,
          type:'error',
          html:true
        })

        click = 0;
        $('#btn-create-wq').removeAttr('disabled');
      }
    }
  })
}


$('#sub-district').autocomplete({
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
      $('#sub-district').val(adr[0]);
      $('#district').val(adr[1]);
      $('#province').val(adr[2]);
      $('#postcode').val(adr[3]);
    }
  }
});


$('#district').autocomplete({
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
      $('#district').val(adr[0]);
      $('#province').val(adr[1]);
      $('#postcode').val(adr[2]);
    }
  }
});


$('#province').autocomplete({
  source:BASE_URL + 'auto_complete/province',
  autoFocus:true,
  open:function(event){
    var $ul = $(this).autocomplete('widget');
    $ul.css('width', 'auto');
  }
})



$('#postcode').autocomplete({
  source:BASE_URL + 'auto_complete/postcode',
  autoFocus:true,
  open:function(event){
    var $ul = $(this).autocomplete('widget');
    $ul.css('width', 'auto');
  },
  close:function(){
    var rs = $.trim($(this).val());
    var adr = rs.split('>>');
    if(adr.length == 4){
      $('#sub-district').val(adr[0]);
      $('#district').val(adr[1]);
      $('#province').val(adr[2]);
      $('#postcode').val(adr[3]);
      $('#postcode').focus();
    }
  }
})

function showLogs() {
  $('#logModal').modal('show');
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
		$('#phone-search').focus();
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

function addToBill() {
	$('.cust-form').removeClass('has-error');

	let h = {
		'customer_id' : $('#cust-id').val(),
		'customer_name' : $.trim($('#form-name').val()),
		'tax_id' : $('#form-tax-id').val(),
		'branch_code' : $.trim($('#form-branch-code').val()),
		'branch_name' : $.trim($('#form-branch-name').val()),
		'address' : $.trim($('#form-address').val()),
		'subDistrict' : $.trim($('#form-subDistrict').val()),
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

	if(h.tax_id.length < 10) {
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

  $('#customer-ref').val(h.customer_name);
	$('#tax-id').val(h.tax_id);
	$('#branch-code').val(h.branch_code);
	$('#branch-name').val(h.branch_name);
	$('#address').val(h.address);
	$('#sub-district').val(h.subDistrict);
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
}


function clearForm() {
	$('.cust-form').val('');
  $('#tax-search').val('');
  $('#phone-search').val('').focus();
}

function closeOrder(code) {
  swal({
    title:'ปิดใบสั่งขาย',
    text:'ต้องการปิดใบส่งขายนี้หรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  }, function() {
    setTimeout(() => {
      load_in();

      $.ajax({
        url:HOME + 'close_order',
        type:'POST',
        cache:false,
        data:{
          'code' : code
        },
        success:function(rs) {
          load_out();

          if(rs === 'success') {
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
            }, function() {
              window.location.reload();
            });
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
    }, 200)
  });
}
