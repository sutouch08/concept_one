<?php $this->load->view('include/pos_header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning top-btn" onclick="returnList()"><i class="fa fa-arrow-left"></i> Back</button>
      <button type="button" class="btn btn-sm btn-success top-btn" onclick="saveReturn()"><i class="fa fa-save"></i>&nbsp;&nbsp;บันทึก</button>
    </p>
  </div>
</div>
<hr/>
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" disabled/>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" id="date_add" value="<?php echo date('d-m-Y'); ?>" disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่บิล</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->customer_code; ?>" disabled/>
  </div>
  <div class="col-lg-5 col-md-6 col-sm-4-harf col-xs-6 padding-5">
    <label class="not-show">ลูกค้า</label>
    <input type="text" class="form-control input-sm" value="<?php echo $order->customer_name; ?>" disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>คลัง(รับคืน)</label>
    <input type="text" class="form-control input-sm" id="warehouse-code" value="<?php echo $pos->warehouse_code; ?>" disabled/>
  </div>
  <div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>โซน(รับคืน)</label>
    <input type="text" class="form-control input-sm" id="zone-code" value="<?php echo $pos->zone_code; ?>" disabled/>
  </div>
  <div class="col-lg-4-harf col-md-7-harf col-sm-7 col-xs-6 padding-5">
    <label class="not-show">โซน(รับคืน)</label>
    <input type="text" class="form-control input-sm" id="zone-name" value="<?php echo $pos->zone_name; ?>" disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>การคืนเงิน</label>
    <select class="form-control input-sm" id="payment-role">
      <option value="1" <?php echo is_selected('1', $order->payment_role); ?>>เงินสด</option>
      <option value="2" <?php echo is_selected('2', $order->payment_role); ?>>เงินโอน</option>
      <option value="3" <?php echo is_selected('3', $order->payment_role); ?>>บัตรเครดิต</option>
    </select>
  </div>
  <div class="col-lg-3-harf col-md-10-harf col-sm-10 col-xs-12 padding-5">
    <label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" id="remark" value="" placeholder="หมายเหตุในการคืนอย่างน้อย 10 ตัวอักษร"/>
  </div>

  <input type="hidden" id="bill-id" value="<?php echo $order->id; ?>" />
  <input type="hidden" id="bill-code" value="<?php echo $order->code; ?>" />
</div>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
  <div class="col-lg-2 col-lg-offset-4 col-md-2 col-md-offset-4 col-sm-3 col-sm-offset-3 col-xs-6 padding-5">
    <input type="text" class="form-control input-sm text-center" id="barcode" placeholder="Scan barcode" autofocus />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="doRecieve()">ตกลง</button>
  </div>
</div>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 padding-5 table-responsive">
    <table class="table table-bordered table-striped">
      <thead>
        <tr >
          <td class="fix-width-40 text-center">#</td>
          <td class="fix-width-120 text-center">บาร์โค้ด</td>
          <td class="min-width-200 text-center">รายละเอียด</td>
          <td class="fix-width-60 text-center">หน่วย</td>
          <td class="fix-width-80 text-center">ราคา</td>
          <td class="fix-width-100 text-center">ส่วนลด</td>
          <td class="fix-width-80 text-center">จำนวน</td>
          <td class="fix-width-80 text-center">คืนแล้ว</td>
          <td class="fix-width-80 text-center">คงเหลือ</td>
          <td class="fix-width-80 text-center">รับคืน</td>
          <td class="fix-width-100 text-center">มูลค่า</td>
        </tr>
      </thead>
      <tbody id="item-table">

        <?php if( ! empty($details)) : ?>
          <?php $no = 1; ?>
          <?php $total_qty = 0; ?>
          <?php $total_returned = 0; ?>
          <?php $total_balance = 0; ?>
          <?php foreach($details as $rs) : ?>
            <?php $limit = $rs->qty - $rs->return_qty; ?>
            <?php $active = $limit > 0 ? '' : 'disabled'; ?>
            <?php $color = $limit > 0 ? '' : 'color:lightgrey'; ?>
            <tr class="font-size-11 pos-rows" style="<?php echo $color; ?>" id="row-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>">
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo $rs->barcode; ?></td>
              <td><?php echo $rs->product_code." : ".$rs->product_name; ?></td>
              <td class="text-center"><?php echo $rs->unit_code; ?></td>
              <td class="text-center"><?php echo number($rs->price, 2); ?></td>
              <td class="text-center"><?php echo $rs->discount_label; ?></td>
              <td class="text-center"><?php echo number($rs->qty, 2); ?></td>
              <td class="text-center"><?php echo number($rs->return_qty, 2); ?></td>
              <td class="text-center"><?php echo number($limit, 2); ?></td>
              <td class="text-center">
                <input type="number"
                class="form-control input-xs text-center return-qty no-<?php echo $no; ?> <?php echo (empty($rs->bc_code) ? '' : $rs->bc_code); ?>"
                id="return-qty-<?php echo $rs->id; ?>"
                data-pd="<?php echo $rs->product_code; ?>"
                data-price="<?php echo $rs->final_price; ?>"
                data-id="<?php echo $rs->id; ?>"
                data-no="<?php echo $no; ?>"
                data-limit="<?php echo $limit; ?>" value="" <?php echo $active; ?>/>
              </td>
              <td class="text-right total" id="return-amount-<?php echo $rs->id; ?>">0.00</td>
            </tr>
            <?php $no++; ?>
            <?php $total_qty += $rs->qty; ?>
            <?php $total_returned += $rs->return_qty; ?>
            <?php $total_balance += $limit; ?>
          <?php endforeach; ?>
        <?php endif; ?>
        <tr>
          <td colspan="6" class="text-right">รวม</td>
          <td class="text-center"><?php echo number($total_qty, 2); ?></td>
          <td class="text-center"><?php echo number($total_returned, 2); ?></td>
          <td class="text-center"><?php echo number($total_balance, 2); ?></td>
          <td class="text-center" id="total-qty">0.00</td>
          <td class="text-right" id="total-amount">0.00</td>
        </tr>
      </tbody>
    </table>

    <?php if( ! empty($bcList)) : ?>
      <?php foreach($bcList as $bc) : ?>
        <input type="hidden" class="bc"
        id="bc-<?php echo $bc->code; ?>"
        data-barcode="<?php echo $bc->barcode; ?>"
        data-pd="<?php echo $bc->pdCode; ?>"
        value="<?php echo $bc->code; ?>" />
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php $this->load->view('validate_credentials'); ?>

<input type="hidden" id="shop_id" value="<?php echo $pos->shop_id; ?>" />
<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos_return.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>
<script src="<?php echo base_url(); ?>assets/js/md5.min.js"></script>

<?php $this->load->view('include/pos_footer'); ?>
