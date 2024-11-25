<?php $this->load->view('include/header'); ?>
<style>
  label {
    margin-top:5px;
  }

  .border-right-1 {
    border-right:solid 1px #ddd;
  }
</style>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-xs-12 padding-5 visible-xs">
    <h4 class="title-xs"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning btn-top" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
<?php if($order->status != 'D') : ?>
  <?php if($this->pm->can_delete) : ?>
      <button type="button" class="btn btn-sm btn-danger btn-top" onclick="getCancel(<?php echo $order->id; ?>, '<?php echo $order->code; ?>')">ยกเลิก</button>
  <?php endif; ?>
      <button type="button" class="btn btn-sm btn-success btn-top" onclick="sendToSap()"><i class="fa fa-send"></i> Send to SAP</button>
      <button type="button" class="btn btn-sm btn-info btn-top" onclick="printInvoice('<?php echo $order->code; ?>')"><i class="fa fa-print"></i> พิมพ์</button>
<?php endif; ?>
    </p>
  </div>
</div>
<hr class=""/>
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" readonly />
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo thai_date($order->doc_date); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>รหัสลูกค้า</label>
    <input type="text" class="form-control input-sm" value="<?php echo $order->customer_code; ?>" readonly />
  </div>
  <div class="col-lg-6-harf col-md-6-harf col-sm-4 col-xs-6 padding-5">
    <label>ชื่อลูกค้า</label>
    <input type="text" class="form-control input-sm" value="<?php echo $order->customer_name; ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>สาขา</label>
    <input type="text" class="form-control input-sm" value="<?php echo $order->branch_name; ?>" readonly />
  </div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <label>ที่อยู่</label>
    <input type="text" class="form-control input-sm" value="<?php echo $order->customer_address; ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่ผู้เสียภาษี</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->tax_id; ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>โทร.</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->phone; ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>อ้างอิง</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->reference; ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>DO No.</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->inv_code; ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>จุุดขาย</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $pos->shop_code; ?>" readonly/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เครื่อง POS</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $pos->code; ?>" readonly/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>User</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->uname; ?>" readonly/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>สถานะ</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo bill_status_label($order->status); ?>" readonly />
  </div>

  <input type="hidden" id="code" value="<?php echo $order->code; ?>" />
</div>
<hr class="margin-top-15 margin-bottom-15">
<?php if($order->status == 'D') : ?>
  <?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-150">สินค้า</th>
          <th class="min-width-200">รายละเอียด</th>
          <th class="fix-width-100 text-right">ราคา</th>
          <th class="fix-width-100 text-right">ส่วนลด</th>
          <th class="fix-width-100 text-right">จำนวน</th>
          <th class="fix-width-120 text-right">มูลค่า</th>
        </tr>
      </thead>
      <tbody>
    <?php if( ! empty($details)) : ?>
      <?php $no = $this->uri->segment($this->segment) + 1; ?>
      <?php $total_qty = 0; ?>
      <?php foreach($details as $rs) : ?>
        <tr>
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->product_code; ?></td>
          <td class="middle"><?php echo $rs->product_name; ?></td>
          <td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
          <td class="middle text-right"><?php echo $rs->discount_label; ?></td>
          <td class="middle text-right"><?php echo number($rs->qty, 2); ?></td>
          <td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
        </tr>
        <?php $no++; ?>
        <?php $total_qty += $rs->qty; ?>
      <?php endforeach; ?>
    <?php endif; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" rowspan="3" class="border-right-1 text-center">**จำนวนรวม <?php echo number($total_qty, 2); ?> หน่วย**</td>
          <td colspan="2" class="text-right">มูลค่ารวมก่อนภาษี</td>
          <td colspan="2" class="text-right"><?php echo number($order->amount - $order->vat_amount, 2); ?></td>
        </tr>
        <tr>
          <td colspan="2" class="text-right">ภาษีมูล่ค่าเพิ่ม <?php echo getConfig('SALE_VAT_RATE'); ?> %</td>
          <td colspan="2" class="text-right"><?php echo number($order->vat_amount, 2); ?></td>
        </tr>
        <tr>
          <td colspan="2" class="text-right">จำนวนเงินรวมทั้งสิ้น</td>
          <td colspan="2" class="text-right"><?php echo number($order->amount, 2); ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<?php if($order->status == 'D') : ?>
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 red">
      ยกเลิกโดย : <?php echo $order->cancel_user; ?> @<?php echo thai_date($order->cancel_date, TRUE, '/'); ?><br/>
      หมายเหตุ : <?php echo $order->cancel_reason; ?>
    </div>    
  </div>
<?php endif; ?>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/order_pos_invoice/order_pos_invoice.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
