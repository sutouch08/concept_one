<?php $this->load->view('include/header'); ?>
<div class="row top-row">
  <div class="col-sm-12 top-col padding-5">
    <h4 class="title"><?php echo $code; ?></h4>
  </div>
</div>
<hr/>
<div class="row">
  <div class="col-sm-12 padding-5">
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="width-5 text-center">No.</th>
					<th class="width-20">เลขที่เอกสาร</th>
          <th class="width-25">รหัสสินค้า</th>
          <th class="width-10 text-right">Qty</th>
					<th></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($details)) : ?>
          <?php $no = 1; ?>
					<?php $total_qty = 0; ?>
          <?php foreach($details as $rs) : ?>

            <tr>
              <td class="middle text-center"><?php echo $no; ?></td>
							<td class="middle"><?php echo $rs->receive_code; ?></td>
              <td class="middle"><?php echo $rs->product_code; ?></td>
              <td class="middle text-right"><?php echo $rs->qty; ?></td>
							<td></td>
            </tr>
            <?php $no++; ?>
						<?php $total_qty += $rs->qty; ?>
          <?php endforeach; ?>
					<tr>
						<td colspan="3" class="middle text-right">Total</td>
						<td class="middle text-right"><?php echo number($total_qty); ?></td>
						<td></td>
					</tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $this->load->view('include/footer'); ?>
