<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" style="min-height:100px;">
    <table class="table table-bordered border-1" style="min-width:920px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-40 text-center"></th>
          <th class="fix-width-120 text-center">เอกสารตัดมัดจำ</th>
          <th class="fix-width-120 text-center">ใบสั่งขาย</th>
          <th class="fix-width-120 text-center">ออเดอร์</th>
          <th class="fix-width-120 text-center">บิลขาย</th>
          <th class="fix-width-120 text-right">คงเหลือก่อนตัด</th>
          <th class="fix-width-120 text-right">ยอดตัดบิลนี้</th>
          <th class="fix-width-120 text-right">คงเหลือหลังตัด</th>
        </tr>
      </thead>
      <tbody id="down-payment-table">
        <?php if( ! empty($details)) : ?>
          <?php $no = 1; ?>
          <?php $downPaymentUse = 0; ?>
          <?php foreach($details as $dp) : ?>
            <?php $co = $dp->is_cancel == 1 ? 'color:red;' : ''; ?>
            <tr style="<?php echo $co; ?>">
              <td class="text-center"><?php echo $no; ?></td>
              <td class="text-center"><?php echo $dp->is_cancel == 1 ? 'ยกเลิก' : ''; ?></td>
              <td class="text-center"><?php echo $dp->TargetRef; ?></td>
              <td class="text-center"><?php echo $dp->so_code; ?></td>
              <td class="text-center"><?php echo $dp->order_code; ?></td>
              <td class="text-center"><?php echo $dp->bill_code; ?></td>
              <td class="text-right"><?php echo number($dp->amountBfUse, 2); ?></td>
              <td class="text-right"><?php echo number($dp->amount, 2); ?></td>
              <td class="text-right"><?php echo number($dp->amountAfUse, 2); ?></td>
            </tr>
            <?php $downPaymentUse += $dp->amount; ?>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="9" class="text-center"> --- No data ---</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
