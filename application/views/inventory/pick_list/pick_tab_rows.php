<div id="rows-tab" class="tab-pane fade" style="height:400px; overflow:auto;">
  <table class="table table-striped tableFixHead">
    <thead>
      <tr class="font-size-11">
        <th class="fix-width-40 text-center fix-header">#</th>
        <th class="fix-width-200 fix-header">รหัส</th>
        <th class="min-width-200 fix-header">สินค้า</th>
        <th class="fix-width-100 text-center fix-header">Order Qty</th>
        <th class="fix-width-100 text-center fix-header">Stock Qty</th>
      </tr>
    </thead>
    <tbody>
  <?php if( ! empty($rows)) : ?>
    <?php $no = 1; ?>
    <?php $totalQty = 0; ?>
    <?php $totalStock = 0; ?>
    <?php foreach($rows as $rs) : ?>
      <tr class="font-size-11">
        <td class="text-center"><?php echo $no; ?></td>
        <td><?php echo $rs->product_code; ?></td>
        <td class="hide-text"><?php echo $rs->product_name; ?></td>
        <td class="text-center"><?php echo number($rs->qty); ?></td>
        <td class="text-center"><?php echo number($rs->stock); ?></td>
      </tr>
      <?php $no++; ?>
      <?php $totalQty += $rs->qty; ?>
      <?php $totalStock += $rs->stock; ?>
    <?php endforeach; ?>
    <tr class="font-size-11">
      <td colspan="3" class="text-right">Total</td>
      <td class="text-center"><?php echo number($totalQty); ?></td>
      <td class="text-center"><?php echo number($totalStock); ?></td>
    </tr>
  <?php endif; ?>
    </tbody>
  </table>
</div>
