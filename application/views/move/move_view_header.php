<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center edit" name="date" id="date" value="<?php echo thai_date($doc->date_add); ?>" readonly required disabled />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>คลังต้นทาง</label>
    <input type="text" class="form-control input-sm" id="from_warehouse_code" value="<?php echo $doc->from_warehouse; ?>" disabled />
  </div>

  <div class="col-lg-3 col-md-3 col-sm-2-harf col-xs-8 padding-5">
    <label class="not-show">คลังต้นทาง</label>
    <input type="text" class="form-control input-sm edit" name="from_warehouse" id="from_warehouse" value="<?php echo $doc->from_warehouse_name; ?>" required disabled/>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>คลังปลายทาง</label>
    <input type="text" class="form-control input-sm" id="to_warehouse_code" value="<?php echo $doc->to_warehouse; ?>" disabled />
  </div>

	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-8 padding-5">
    <label class="not-show">คลังปลายทาง</label>
		<input type="text" class="form-control input-sm edit" name="to_warehouse" id="to_warehouse" value="<?php echo $doc->to_warehouse_name; ?>" required disabled/>
  </div>

  <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm edit" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>สถานะ</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo showStatus($doc->is_expire, $doc->status); ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>SAP</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->inv_code; ?>" disabled/>
  </div>

  <?php if($doc->status == 2 && ! empty($doc->cancle_reason)) : ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
      <label>เหตุผลในการยกเลิก</label>
      <input type="text" class="form-control input-sm" value="<?php echo $doc->cancle_reason; ?>" disabled>
    </div>
  <?php endif; ?>
</div>
<input type="hidden" id="move_code" value="<?php echo $doc->code; ?>" />
<hr class="margin-top-15"/>
<?php if($doc->must_accept == 1) : ?>
<div class="row margin-bottom-10">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <span class="">รายชื่อเจ้าของโซน : </span>
    <?php if( ! empty($accept_list)) : ?>
      <?php foreach($accept_list AS $ac) : ?>
        <?php if($ac->is_accept == 1) : ?>
          <span class="label label-success label-white middle"><i class="fa fa-check-circle"></i> <?php echo $ac->display_name; ?></span>
        <?php else : ?>
          <span class="label label-default label-white middle"><?php echo $ac->display_name; ?></span>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php endif; ?>
<?php
function showStatus($is_expire, $status)
{
  if($is_expire == 1 OR $status == 2)
  {
    return $status == 2 ? "ยกเลิก" : "หมดอายุ";
  }

  $text = "Unknow";

  switch($status)
  {
    case 0 :
      $text = "ยังไม่บันทึก";
      break;
    case 1 :
      $text = "บันทึกแล้ว";
      break;
    case 2 :
      $text = "ยกเลิก";
      break;
    case 4 :
      $text = "รอยืนยัน";
      break;
  }

  return $text;
}


if($doc->is_expire == 1 OR $doc->status == 2)
{
  if($doc->status == 2)
  {
    $this->load->view('cancle_watermark');
  }
  else
  {
    $this->load->view('expire_watermark');
  }
}
else
{
  if($doc->status == 4)
  {
    $this->load->view('accept_watermark');
  }
}

?>
