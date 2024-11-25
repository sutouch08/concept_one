<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 hidden-print">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-info" onclick="print()"><i class="fa fa-print"></i> Print</button>
		</p>
	</div>
</div>
<hr class="padding-5 hidden-print">
<div class="row">
  <div class="col-sm-12 col-xs-12 padding-5">
		<table class="table" style="margin-bottom:5px;">
			<tr>
				<td colspan="2" class="no-padding">
					<table class="table table-striped border-1" style="margin-bottom:0px;">
						<tr>
							<td>เลขที่ออเดอร์ : <?php echo $order->reference; ?></td>
							<td>วันที่ออเดอร์ : <?php echo thai_date($order->orderDate, FALSE, '/'); ?></td>
							<td>วันที่เข้า Temp : <?php echo thai_date($order->tempDate, FALSE, '/'); ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="width-50 no-padding-left">
					<table class="table table-striped table-bordered" style="margin-bottom:0px;">

			      <tr>
			      	<td colspan="2"><strong>Billing</strong></td>
						</tr>
						<tr>
							<td class="width-30">Company</td>
							<td class="width-70"><?php echo $order->bCompany; ?></td>
			      </tr>
						<tr>
							<td>Customer</td>
							<td><?php echo $order->bFirstName.' '.$order->bLastName; ?></td>
			      </tr>
						<tr>
							<td>Address</td>
							<td>
								<?php echo $order->bAddress1.' '.$order->bAddress2.' '.$order->bCity.' '.$order->bProvince.' '.$order->bZipCode.' '.$order->bCountryCode; ?>
							</td>
			      </tr>
						<tr>
							<td>Phone</td>
							<td><?php echo $order->bPhone; ?></td>
						</tr>
						<tr>
							<td>Email</td>
							<td><?php echo $order->bEmail; ?></td>
						</tr>
			    </table>
				</td>
				<td class="width-50 no-padding-right">
					<table class="table table-striped table-bordered" style="margin-bottom:0px;">

			      <tr>
			      	<td colspan="2"><strong>Shipping</strong></td>
						</tr>
						<tr>
							<td class="width-30">Company</td>
							<td class="width-70"><?php echo $order->sCompany; ?></td>
			      </tr>
						<tr>
							<td>Customer</td>
							<td><?php echo $order->sFirstName.' '.$order->sLastName; ?></td>
			      </tr>
						<tr>
							<td>Address</td>
							<td>
								<?php echo $order->sAddress1.' '.$order->sAddress2.' '.$order->sCity.' '.$order->sProvince.' '.$order->sZipCode.' '.$order->sCountryCode; ?>
							</td>
			      </tr>
						<tr>
							<td>Phone</td>
							<td><?php echo $order->sPhone; ?></td>
						</tr>
						<tr>
							<td>Email</td>
							<td><?php echo $order->sEmail; ?></td>
						</tr>
			    </table>
				</td>
			</tr>
		</table>

	</div>
</div>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-5 text-center">#</th>
					<th class="width-10">Item Code</th>
					<th class="width-15">Item Name</th>
					<th class="width-10 text-right">Tax(%)</th>
					<th class="width-10 text-right">Unit Price</th>
					<th class="width-10 text-right">Discount</th>
					<th class="width-10 text-right">Quantity</th>
					<th class="width-10 text-right">Sub Total</th>
					<th class="width-10 text-right">Tax Total</th>
					<th class="width-10 text-right">Amount</th>
				</tr>
			</thead>
			<?php if(!empty($details)) : ?>
			<tbody>

					<?php $no = 1; ?>
					<?php $total_qty = 0; ?>
					<?php $total_tax = 0; ?>
					<?php $total_sub = 0; ?>
					<?php $total_amount = 0; ?>
					<?php foreach($details as $rs) : ?>
						<tr>
							<td class="text-center"><?php echo $no; ?></td>
							<td class=""><?php echo $rs->productCode; ?></td>
							<td class=""><?php echo $rs->productName; ?></td>
							<td class="text-right"><?php echo number($rs->taxPercentage, 2).' %'; ?></td>
							<td class="text-right"><?php echo number($rs->unitPrice, 2); ?></td>
							<td class="text-right"><?php echo number($rs->discount, 2); ?></td>
							<td class="text-right"><?php echo number($rs->quantity); ?></td>
							<td class="text-right"><?php echo number($rs->subTotal, 2); ?></td>
							<td class="text-right"><?php echo number($rs->taxTotal, 2); ?></td>
							<td class="text-right"><?php echo number($rs->total, 2); ?></td>
						</tr>
						<?php $no++; ?>
						<?php $total_qty += $rs->quantity; ?>
						<?php $total_sub += $rs->subTotal; ?>
						<?php $total_tax += $rs->taxTotal; ?>
						<?php $total_amount += $rs->total; ?>
					<?php endforeach; ?>

					<tr style="color:blue; font-size:14px;">
						<td colspan="6" class="text-right"><strong>Total</strong></td>
						<td class="text-right"><strong><?php echo number($total_qty); ?></strong></td>
						<td class="text-right"><strong><?php echo number($total_sub,2); ?></strong></td>
						<td class="text-right"><strong><?php echo number($total_tax,2); ?></strong></td>
						<td class="text-right"><strong><?php echo number($total_amount, 2); ?></strong></td>
					</tr>
			</tbody>
				<?php endif; ?>
		</table>
	</div>
</div>
<?php $this->load->view('include/footer'); ?>
