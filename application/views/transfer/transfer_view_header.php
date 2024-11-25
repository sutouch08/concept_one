<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($doc->date_add); ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-3 padding-5">
    <label>รหัส</label>
    <input type="text" class="form-control input-sm edit" name="from_warehouse_code" id="from_warehouse_code" value="<?php echo $doc->from_warehouse; ?>" disabled />
  </div>

  <div class="col-lg-3 col-md-3 col-sm-6-harf col-xs-9 padding-5">
    <label>คลังต้นทาง</label>
    <input type="text" class="form-control input-sm edit" name="from_warehouse" id="from_warehouse" value="<?php echo $doc->from_warehouse_name; ?>" disabled/>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-3 padding-5">
    <label>รหัส</label>
    <input type="text" class="form-control input-sm edit" name="to_warehouse_code" id="to_warehouse_code" value="<?php echo $doc->to_warehouse; ?>" disabled />
  </div>

	<div class="col-lg-3 col-md-3 col-sm-8 col-xs-9 padding-5">
    <label>คลังปลายทาง</label>
		<input type="text" class="form-control input-sm edit" name="to_warehouse" id="to_warehouse" value="<?php echo $doc->to_warehouse_name; ?>" disabled/>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>สถานะ</label>
		<select class="form-control input-sm edit" disabled>
			<option>Unknow</option>
      <option <?php echo is_selected('-1', $doc->status); ?>>ยังไม่บันทึก</option>
      <option <?php echo is_selected('0', $doc->status); ?>>รออนุมัติ</option>
      <option <?php echo is_selected('4', $doc->status); ?>>รอยืนยัน</option>
      <option <?php echo is_selected('3', $doc->status); ?>>WMS Process</option>
      <option <?php echo is_selected('1', $doc->status); ?>>สำเร็จ</option>
      <option <?php echo is_selected('2', $doc->status); ?>>ยกเลิก</option>
		</select>
	</div>

  <div class="col-xs-6 padding-5 visible-xs">
		<label>SAP No</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->inv_code; ?>" disabled >
	</div>

  <div class="col-lg-9 col-md-9 col-sm-10 col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 padding-5 hidden-xs">
		<label>SAP</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->inv_code; ?>" disabled >
	</div>
</div>
<input type="hidden" id="transfer_code" value="<?php echo $doc->code; ?>" />
<hr class="margin-top-15 margin-bottom-15"/>
