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
      <button type="button" class="btn btn-sm btn-info btn-top" onclick="printPosRound('<?php echo $round->id; ?>')"><i class="fa fa-print"></i> พิมพ์</button>
    </p>
  </div>
</div>
<hr class=""/>
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $round->code; ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-1-harf col-xs-6 padding-5">
    <label>POS No.</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $pos->code; ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่เปิด</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo thai_date($round->open_date, TRUE, '/'); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-1-harf col-xs-6 padding-5">
    <label>เปิดโดย</label>
    <input type="text" class="form-control input-sm" value="<?php echo $this->user_model->get_name($round->open_user); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่ปิด</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo ($round->status == 'C' ? thai_date($round->close_date, TRUE, '/') : ''); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-1-harf col-xs-6 padding-5">
    <label>ปิดโดย</label>
    <input type="text" class="form-control input-sm" value="<?php echo $round->status == 'C' ? $this->user_model->get_name($round->open_user) : ''; ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>สถานะ</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo bill_status_label($round->status); ?>" readonly />
  </div>
</div>
<hr class="margin-top-15 margin-bottom-15">
<?php $cash_expected = $round->open_cash + $round->cash_in + $round->cash_out + $round->total_cash + $round->down_cash + $round->return_cash; ?>
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เงินสดตั้งต้น</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo number($round->open_cash, 2); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>นำเงินเข้า</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo number($round->cash_in, 2); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>นำเงินออก</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo number($round->cash_out, 2); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>คาดการณ์เงินสด</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo number($cash_expected, 2); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เงินสดปิดรอบ</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo number($round->close_cash, 2); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ยอดขายเงินสด</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo number($round->total_cash, 2); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ยอดขายเงินโอน</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo number($round->total_transfer, 2); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ยอดขายบัตรเครดิต</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo number($round->total_card, 2); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>มัดจำเงินสด</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo number($round->down_cash, 2); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>มัดจำเงินโอน</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo number($round->down_transfer, 2); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>มัดจำบัตรเครดิต</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo number($round->down_card, 2); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>คืนเงินสด</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo number($round->return_cash, 2); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>คืนด้วยการโอน</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo number($round->return_transfer, 2); ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>รวมสุทธิ</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo number($round->round_total, 2); ?>" readonly />
  </div>
<?php if($round->status == 'C') : ?>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="display-block not-show">recal</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="recalSummary(<?php echo $round->id; ?>)">สรุปยอดใหม่</button>
  </div>
<?php endif; ?>
</div>
<hr class="margin-top-15 margin-bottom-15">
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-150">เลขที่</th>
          <th class="fix-width-150">ประเภท</th>
          <th class="fix-width-100 text-right">เงินสด</th>
          <th class="fix-width-100 text-right">เงินโอน</th>
          <th class="fix-width-100 text-right">บัตรเครดิต</th>
          <th class="min-width-100">พนักงาน</th>
        </tr>
      </thead>
      <tbody>
    <?php if( ! empty($details)) : ?>
      <?php $no = $this->uri->segment($this->segment) + 1; ?>
      <?php $total_cash = 0; ?>
      <?php $total_transfer = 0; ?>
      <?php $total_card = 0; ?>
      <?php foreach($details as $rs) : ?>
        <tr>
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->code; ?></td>
          <td class="middle"><?php echo movement_type_label($rs->type); ?></td>
          <td class="middle text-right"><?php echo $rs->payment_role == 1 ? number($rs->amount, 2) : '-'; ?></td>
          <td class="middle text-right"><?php echo $rs->payment_role == 2 ? number($rs->amount, 2) : '-'; ?></td>
          <td class="middle text-right"><?php echo $rs->payment_role == 3 ? number($rs->amount, 2) : '-'; ?></td>
          <td class="middle"><?php echo $rs->user; ?></td>
        </tr>
        <?php $no++; ?>
        <?php $total_cash += $rs->payment_role == 1 ? $rs->amount : 0; ?>
        <?php $total_transfer += $rs->payment_role == 2 ? $rs->amount : 0; ?>
        <?php $total_card += $rs->payment_role == 3 ? $rs->amount : 0; ?>
      <?php endforeach; ?>
    <?php endif; ?>
        <tr>
          <td colspan="3" class="text-right">รวม</td>
          <td class="text-right"><?php echo number($total_cash, 2); ?></td>
          <td class="text-right"><?php echo number($total_transfer, 2); ?></td>
          <td class="text-right"><?php echo number($total_card, 2); ?></td>
          <td class="">รวมสุทธิ : <?php echo number($total_cash + $total_transfer + $total_card, 2); ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/order_pos_round/order_pos_round.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
