<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title"><?php echo $code; ?></h4>
  </div>
</div>
<hr/>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:950px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-200">รหัสสินค้า</th>
          <th class="min-width-250">ชื่อสินค้า</th>
          <th class="fix-width-150">From</th>
          <th class="fix-width-150">To</th>
          <th class="fix-width-80 text-right">Qty</th>
          <th class="fix-width-80 text-right">Bin Qty</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($details)) : ?>
          <?php $no = 1; ?>
          <?php foreach($details as $rs) : ?>
            <?php $hilight = ($rs->Quantity > $rs->onhand) ? 'color:red;' : ''; ?>
            <tr style="<?php echo $hilight; ?>">
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle"><?php echo $rs->ItemCode; ?></td>
              <td class="middle"><?php echo $rs->Dscription; ?></td>
              <td class="middle"><?php echo $rs->F_FROM_BIN; ?></td>
              <td class="middle"><?php echo $rs->F_TO_BIN; ?></td>
              <td class="middle text-right"><?php echo intval($rs->Quantity); ?></td>
              <td class="middle text-right"><?php echo $rs->onhand; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $this->load->view('include/footer'); ?>
