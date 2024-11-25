<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog" style="max-width:500px;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
				<h3 class="text-center" style="margin:0;">ข้อมูลลูกค้า</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-3 col-md-3 col-sm-3 padding-5">

					</div>
          <div class="col-lg-6 col-md-6 col-sm-6 padding-5">
						<input type="text" class="form-control input-sm text-center" maxlength="13" id="tax-search" placeholder="ประจำตัวผู้เสียภาษี/เลขที่บัตรประชาชน"/>
          </div>					
					<div class="col-lg-12 col-md-12 col-sm-12 padding-5 margin-top-10 first last" id="cust-result-table"></div>
        </div>

        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" style="max-height:450px; overflow:auto;">
            <div class="col-lg-12 col-md-12 col-sm-12">
              <label>ชื่อ</label>
              <input type="text" class="form-control input-sm cust-form" id="form-name" maxlength="100" />
            </div>
            <div class="divider-hidden"></div>
            <div class="col-lg-6 col-md-6 col-sm-6">
              <label>เบอร์โทร</label>
              <input type="text" class="form-control input-sm cust-form" maxlength="32" id="form-phone" onkeyup="numberOnly(this)"  />
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
              <label class="display-block not-show">isCompany</label>
              <label>
                <input type="checkbox" class="ace cust-form" id="form-is-company" value="1" onchange="toggleFormBranch()" />
                <span class="lbl margin-top-5">&nbsp;&nbsp;นิติบุคคล</span>
              </label>
            </div>
            <div class="divider-hidden"></div>
            <div class="col-lg-5 col-md-6 col-sm-6 padding-5 first">
              <label>ประจำตัวผู้เสียภาษี</label>
              <input type="text" class="form-control input-sm text-center cust-form" id="form-tax-id" onkeyup="numberOnly(this)" maxlength="13" />
            </div>
            <div class="col-lg-2-harf col-md-3 col-sm-3 padding-5">
              <label>รหัสสาขา</label>
              <input type="text" class="form-control input-sm text-center cust-form" id="form-branch-code" maxlength="10" value="" />
            </div>
            <div class="col-lg-4-harf col-md-3 col-sm-3 padding-5 last">
              <label>ชื่อสาขา</label>
              <input type="text" class="form-control input-sm cust-form" id="form-branch-name" maxlength="100" value="" />
            </div>
            <div class="divider-hidden"></div>
            <div class="col-lg-12 col-md-12 col-sm-12">
              <label>ที่อยู่</label>
              <textarea class="form-control input-sm cust-form" id="form-address" ></textarea>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
              <label>ตำบล</label>
              <input type="text" class="form-control input-sm cust-form" id="form-subDistrict" />
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
              <label>อำเภอ</label>
              <input type="text" class="form-control input-sm cust-form" id="form-district" />
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
              <label>จังหวัด</label>
              <input type="text" class="form-control input-sm cust-form" id="form-province" />
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
              <label>รหัสไปรษณีย์</label>
              <input type="text" class="form-control input-sm cust-form" id="form-postcode" />
            </div>
          </div>
				</div>
				<input type="hidden" id="cust-id" />
			</div>
			<div class="modal-footer">
				<button class="btn btn-default btn-100" onclick="closeModal('customerModal')">ยกเลิก</button>
        <button class="btn btn-warning btn-100" onclick="clearForm()">เคลียร์</button>
				<button class="btn btn-primary btn-100" onclick="addCustomer()">เพิ่มใหม่/บันทึก</button>
        <button class="btn btn-success btn-100" onclick="addToBill()">ตกลง</button>
			</div>
		</div>
	</div>
</div>


<script>
$('#form-subDistrict').autocomplete({
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
      $('#form-subDistrict').val(adr[0]);
      $('#form-district').val(adr[1]);
      $('#form-province').val(adr[2]);
      $('#form-postcode').val(adr[3]);
    }
  }
});


$('#form-district').autocomplete({
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
      $('#form-district').val(adr[0]);
      $('#form-province').val(adr[1]);
      $('#form-postcode').val(adr[2]);
    }
  }
});


$('#form-province').autocomplete({
  source:BASE_URL + 'auto_complete/province',
  autoFocus:true,
  open:function(event){
    var $ul = $(this).autocomplete('widget');
    $ul.css('width', 'auto');
  }
})



$('#form-postcode').autocomplete({
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
      $('#form-subDistrict').val(adr[0]);
      $('#form-district').val(adr[1]);
      $('#form-province').val(adr[2]);
      $('#form-postcode').val(adr[3]);
      $('#form-postcode').focus();
    }
  }
})

</script>

<script>
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

</script>
