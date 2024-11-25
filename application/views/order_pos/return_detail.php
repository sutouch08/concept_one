<?php $this->load->view('include/pos_header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning top-btn" onclick="returnList()"><i class="fa fa-arrow-left"></i> Back</button>
      <button type="button" class="btn btn-sm btn-danger top-btn" onclick="saveReturn()"><i class="fa fa-times"></i>&nbsp;&nbsp;ยกเลิก</button>
      <button type="button" class="btn btn-sm btn-info top-btn" onclick="printReturn('<?php echo $doc->code; ?>')"><i class="fa fa-print"></i>&nbsp;&nbsp;พิมพ์</button>
    </p>
  </div>
</div>
<hr/>
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled/>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" id="date_add" value="<?php echo thai_date($doc->date_add) ?>" disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่บิล</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->order_code; ?>" disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->customer_code; ?>" disabled/>
  </div>
  <div class="col-lg-5 col-md-6 col-sm-4-harf col-xs-6 padding-5">
    <label class="not-show">ลูกค้า</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->customer_name; ?>" disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>คลัง(รับคืน)</label>
    <input type="text" class="form-control input-sm" id="warehouse-code" value="<?php echo $doc->warehouse_code; ?>" disabled/>
  </div>
  <div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>โซน(รับคืน)</label>
    <input type="text" class="form-control input-sm" id="zone-code" value="<?php echo $doc->zone_code; ?>" disabled/>
  </div>
  <div class="col-lg-4-harf col-md-7-harf col-sm-7 col-xs-6 padding-5">
    <label class="not-show">โซน(รับคืน)</label>
    <input type="text" class="form-control input-sm" id="zone-name" value="<?php echo $doc->zone_name; ?>" disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>การคืนเงิน</label>
    <select class="form-control input-sm" id="payment-role" disabled>
      <option value="1" <?php echo is_selected('1', $doc->payment_role); ?>>เงินสด</option>
      <option value="2" <?php echo is_selected('2', $doc->payment_role); ?>>เงินโอน</option>
      <option value="3" <?php echo is_selected('3', $doc->payment_role); ?>>บัตรเครดิต</option>
    </select>
  </div>
  <div class="col-lg-3-harf col-md-10-harf col-sm-10 col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
  </div>

  <input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
  <input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
</div>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 padding-5 table-responsive">
    <table class="table table-bordered table-striped">
      <thead>
        <tr >
          <td class="fix-width-40 text-center">#</td>
          <td class="min-width-200 text-center">รายละเอียด</td>
          <td class="fix-width-60 text-center">หน่วย</td>
          <td class="fix-width-80 text-center">ราคา</td>
          <td class="fix-width-100 text-center">ส่วนลด</td>
          <td class="fix-width-80 text-center">รับคืน</td>
          <td class="fix-width-100 text-center">มูลค่า</td>
        </tr>
      </thead>
      <tbody id="item-table">

        <?php if( ! empty($details)) : ?>
          <?php $no = 1; ?>
          <?php foreach($details as $rs) : ?>
            <tr class="font-size-11 pos-rows" id="row-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>">
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td><input type="text" class="form-control input-xs" value="<?php echo $rs->product_code." : ".$rs->product_name; ?>" readonly/></td>
              <td class="text-center"><?php echo $rs->unit_code; ?></td>
              <td class="text-center"><?php echo number($rs->price, 2); ?></td>
              <td class="text-center"><?php echo $rs->discount_label; ?></td>
              <td class="text-center"><?php echo number($rs->return_qty, 2); ?></td>
              <td class="text-right"><?php echo number($rs->total_amount, 2); ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
        <tr>
          <td colspan="5" class="text-right">รวม</td>
          <td class="text-center"><?php echo number($doc->qty, 2); ?></td>
          <td class="text-right"><?php echo number($doc->amount, 2); ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
  <p class="green">อนุมัติโดย : <?php echo $doc->approver; ?> @ <?php echo thai_date($doc->approve_date, TRUE, '/'); ?></p>
</div>
<input type="hidden" id="shop_id" value="<?php echo $doc->shop_id; ?>" />
<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos_return.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/pos_footer'); ?>
