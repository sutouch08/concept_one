<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <table class="table table-striped border-1">
      <tr>
        <th class="fix-width-40 text-center">#</th>
        <th class="fix-width-150">Items</th>
        <th class="fix-width-200">Description</th>
        <th class="fix-width-100">Warehouse</th>
        <th class="fix-width-100 text-right">On Hand</th>
				<th class="fix-width-100 text-right">On Order</th>
				<th class="fix-width-100 text-right">On POS</th>
				<th class="fix-width-100 text-right">On WM</th>
				<th class="fix-width-100 text-right">Available</th>
      </tr>
      <tbody>
    <?php if( !empty($data)) : ?>
    <?php $no = 1; ?>
		<?php $totalQty = 0; ?>
		<?php $totalOrder = 0; ?>
		<?php $totalPos = 0; ?>
		<?php $totalConsign = 0; ?>
		<?php $totalBalance = 0; ?>
    <?php foreach($data as $rs) : ?>
      <tr class="font-size-12">
        <td class="text-center no"><?php echo $no; ?></td>
        <td><?php echo $rs->ItemCode; ?></td>
        <td><?php echo $rs->ItemName; ?></td>
        <td><?php echo $rs->WhsCode; ?></td>
    		<td class="text-right"><?php echo number($rs->OnHandQty, 2); ?></td>
				<td class="text-right"><?php echo number($rs->OrderQty, 2); ?></td>
				<td class="text-right"><?php echo number($rs->PosQty, 2); ?></td>
				<td class="text-right"><?php echo number($rs->ConsignQty, 2); ?></td>
				<td class="text-right"><?php echo number($rs->Available, 2); ?></td>
      </tr>
    <?php  $no++; ?>
		<?php $totalQty += $rs->OnHandQty; ?>
		<?php $totalOrder += $rs->OrderQty; ?>
		<?php $totalPos += $rs->PosQty; ?>
		<?php $totalConsign += $rs->ConsignQty; ?>
		<?php $totalBalance += $rs->Available; ?>
    <?php endforeach; ?>
			<tr>
				<td colspan="4" class="text-right">Total</td>
				<td class="text-right"><?php echo number($totalQty, 2); ?></td>
				<td class="text-right"><?php echo number($totalOrder, 2); ?></td>
				<td class="text-right"><?php echo number($totalPos, 2); ?></td>
				<td class="text-right"><?php echo number($totalConsign, 2); ?></td>
				<td class="text-right"><?php echo number($totalBalance, 2); ?></td>
			</tr>
    <?php else : ?>
      <tr>
        <td colspan="9" class="text-center">--- ไม่พบข้อมูล ---</td>
      </tr>
    <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->load->view('include/footer'); ?>
