<?php $this->load->view('include/header'); ?>
<div class="row top-row">
  <div class="col-sm-12 top-col">
    <h4 class="title"><?php echo $code; ?></h4>
  </div>
</div>
<hr/>
<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="width-5 text-center">No.</th>
          <th class="width-20">รหัสสินค้า</th>
          <th class="">ชื่อสินค้า</th>
          <th class="width-20">Bin location</th>
          <th class="width-10 text-right">Return Qty</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($details)) : ?>
          <?php $no = 1; ?>
					<?php $total = 0; ?>
          <?php foreach($details as $rs) : ?>
            <tr>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle"><?php echo $rs->ItemCode; ?></td>
              <td class="middle"><?php echo $rs->Dscription; ?></td>
              <td class="middle"><?php echo $rs->BinCode; ?></td>
              <td class="middle text-right"><?php echo round($rs->Quantity,2); ?></td>
            </tr>
            <?php $no++; ?>
						<?php $total += $rs->Quantity; ?>
          <?php endforeach; ?>
					<tr>
						<td colspan="4" class="text-right"></td>
						<td class="text-right" style="font-size:16px;"><?php echo number($total, 2); ?></td>
					</tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $this->load->view('include/footer'); ?>
