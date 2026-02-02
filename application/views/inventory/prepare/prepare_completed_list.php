<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <table class="table table-striped border-1">
      <thead>
        <tr class="font-size-11"><td colspan="7" align="center">รายการที่ครบแล้ว</td></tr>
        <tr class="font-size-11">
          <th class="fix-width-150 middle">บาร์โค้ด</th>
          <th class="min-width-350 middle">สินค้า</th>
          <th class="fix-width-100 middle text-center">จำนวน</th>
          <th class="fix-width-100 middle text-center">จัดแล้ว</th>
          <th class="fix-width-100 middle text-center">คงเหลือ</th>
          <th class="fix-width-250 text-right">จัดจากโซน</th>
          <th class="fix-width-20"></th>
        </tr>
      </thead>
      <tbody id="complete-table">

<?php  if(!empty($complete_details)) : ?>
<?php   foreach($complete_details as $rs) : ?>
    <tr class="font-size-11">
      <td class="middle"><?php echo $rs->barcode; ?></td>
      <td class="middle"><b class="blue"><?php echo $rs->product_code; ?></b>|<?php  echo $rs->product_name; ?></td>
      <td class="middle text-center"><?php echo number($rs->qty); ?></td>
      <td class="middle text-center"><?php echo number($rs->prepared); ?></td>
      <td class="middle text-center"><?php echo number($rs->qty - $rs->prepared); ?></td>
      <td class="middle text-right"><?php echo $rs->from_zone; ?></td>
      <td class="middle text-center">
        <?php if($rs->is_count) : ?>
          <a href="javascript:removeBuffer('<?php echo $order->code; ?>', '<?php echo $rs->product_code; ?>', '<?php echo $rs->id; ?>')">
            <i class="fa fa-times red"></i>
          </a>
        <?php endif; ?>
      </td>
    </tr>
<?php endforeach; ?>
<?php endif; ?>

        </tbody>
      </table>
    </div>
  </div>
