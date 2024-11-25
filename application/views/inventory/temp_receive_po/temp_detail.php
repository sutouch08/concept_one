<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title"><?php echo $code; ?></h4>
  </div>
</div>
<hr/>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:790px;">
        <tr>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-200">รหัสสินค้า</th>
          <th class="min-width-250">ชื่อสินค้า</th>
          <th class="fix-width-200">โซน</th>
          <th class="fix-width-100 text-right">จำนวน</th>
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
              <td class="middle text-right"><?php echo number(round($rs->Quantity,2)); ?></td>
            </tr>
            <?php $no++; ?>
						<?php $total += $rs->Quantity; ?>
          <?php endforeach; ?>
					<tr>
						<td colspan="4" class="text-right">รวม</td>
						<td class="text-right"><?php echo number($total); ?></td>
					</tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $this->load->view('include/footer'); ?>
