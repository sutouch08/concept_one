<!DOCTYPE HTML>
<html lang="th">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />

	<title><?php echo $this->title; ?></title>
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.ico">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace.css" class="ace-main-stylesheet" id="main-ace-style" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/template.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/font-sarabun.css" />

  <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/jquery-ui-1.10.4.custom.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/bootstrap.js"></script>
  <script src="<?php echo base_url(); ?>assets/js/ace/ace.js"></script>
	<style>
		body {
			font-family: Sarabun, sans-serif;
			font-size:<?php echo $pos->font_size; ?>px;
		}
	</style>
</head>

<?php if($order->status == 'D') : ?>
	<?php $this->load->view('pos_cancle_watermark'); ?>
<?php endif; ?>
<body style='padding-top:20px; background-color:white;'>

<div style="width:<?php echo $pos->paper_size; ?>mm; padding:2px; margin-left:auto; margin-right:auto;">

    <table class="width-100" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:<?php echo $pos->font_size; ?>; margin-bottom:10px;" >
			<tr><td class="width-50"></td><td class="width-50"></td></tr>

		<?php if( ! empty($pos->bill_header_1)) : ?>
				<tr>
					<td colspan="2" class="<?php echo $pos->header_align_1; ?>" style="font-size:<?php echo $pos->header_size_1; ?>px;">
						<?php echo $pos->bill_header_1; ?>
					</td>
				</tr>
		<?php endif; ?>

		<?php if( ! empty($pos->bill_header_2)) : ?>
				<tr>
					<td colspan="2" class="<?php echo $pos->header_align_2; ?>" style="font-size:<?php echo $pos->header_size_2; ?>px;">
						<?php echo $pos->bill_header_2; ?>
					</td>
				</tr>
		<?php endif; ?>
		<?php if(! empty($pos->bill_header_3)) : ?>
				<tr>
					<td colspan="2" class="<?php echo $pos->header_align_3; ?>" style="font-size:<?php echo $pos->header_size_2; ?>px;">
						<?php echo $pos->bill_header_3; ?>
					</td>
				</tr>
		<?php endif; ?>
			<tr><td>TAX ID: <?php echo $pos->tax_id; ?></td><td class="text-right">POS ID: <?php echo $order->pos_no; ?></td></tr>
      <tr><td>Bill No.: <?php echo $order->code; ?></td><td class="text-right"><?php echo thai_date($order->date_add, TRUE, '/'); ?></td></tr>
			<tr>
				<td class="text-left">Staff: <?php echo $order->emp_name; ?></td>
				<td class="text-right"><?php if( ! empty($so_code)) : ?>Reference : <?php echo $order->so_code; ?><?php endif; ?></td></tr>
    </table>
    <?php
		$total_qty = 0;
		$total_price = 0;
		$total_amount = 0;
		$total_discount = 0;
		?>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; font-size:<?php echo $pos->font_size; ?>px; margin-top:0px; margin-bottom:15px;">
			<thead>
				<th style="width:40%; border-bottom:solid 1px #DDD;">Items</th>
				<th style="width:20%; text-align:right; border-bottom:solid 1px #DDD;">Price</th>
				<th style="width:10%; text-align:right; border-bottom:solid 1px #DDD;">Qty</th>
				<th style="width:25%; text-align:right; border-bottom:solid 1px #DDD;">Amount</th>
			</thead>
			<tbody>
			<?php if(!empty($details)) : ?>
        <?php foreach($details as $ro) : ?>
					<tr>
						<td style="padding-top:5px; white-space: nowrap; overflow-x:hidden;">
							<?php echo $ro->is_free ? 'Free-' : ''; ?>
							<?php echo $ro->product_code ." : ".$ro->product_name;	?>
						</td>
						<td class="text-right"><?php echo number($ro->price,2); ?></td>
						<td class="text-right" style="padding-top:5px;"><?php echo number($ro->qty); ?></td>
						<td class="text-right" style="padding-top:5px;"><?php echo number($ro->price * $ro->qty,2); ?><?php echo $ro->use_vat ? 'V' : ''; ?></td>
					</tr>
					<?php
					$total_qty += $ro->qty;
					$total_price += $ro->qty * $ro->price;
					$total_amount += $ro->total_amount;
					$total_discount += $ro->discount_amount;
					?>
        <?php endforeach; ?>
			<?php endif; ?>
			<tr height="20px">
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr height="20px">
				<td colspan="2">รวม</td>
				<td class="text-right"><?php echo number($total_qty); ?></td>
				<td class="text-right"><?php echo number($total_price,2); ?></td>
			</tr>
			<tr height="20px">
				<td colspan="2">หักส่วนลด</td>
				<td class="text-right">-<?php echo number($total_discount + $order->disc_amount, 2); ?></td>
				<td class="text-right"><?php echo number($total_amount - $order->disc_amount,2); ?></td>
			</tr>

		<?php if($order->use_vat OR ($order->so_code && $order->vat_type != 'N')) : ?>
			<tr height="20px">
				<td colspan="3">Vatable</td>
				<td class="text-right"><?php echo number($order->amount - $order->vat_amount,2); ?></td>
			</tr>
			<tr height="20px">
				<td colspan="3">VAT</td>
				<td class="text-right"><?php echo number($order->vat_amount,2); ?></td>
			</tr>
		<?php endif; ?>
		<?php if($order->down_payment_amount > 0) : ?>
			<tr height="20px">
				<td colspan="3">หักมัดจำ</td>
				<td class="text-right">-<?php echo number($order->down_payment_amount,2); ?></td>
			</tr>
		<?php endif; ?>
		<tr height="20px">
			<td colspan="3">ยอดชำระ</td>
			<td class="text-right"><?php echo number($order->payAmount, 2); ?></td>
		</tr>

			<tr height="20px">
				<td colspan="3">Paid By <?php echo $order->payment_name; ?></td>
				<td class="text-right"><?php echo number($order->received, 2); ?></td>
			</tr>
<?php if($order->payment_role == 6 && ! empty($payments)) : ?>
	<?php foreach($payments as $rs) : ?>
					<tr height="20px">
						<td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp; - <?php echo $rs->role_name; ?></td>
						<td class="text-right"><?php echo number($rs->amount, 2); ?></td>
					</tr>
	<?php endforeach; ?>
<?php endif; ?>
			<tr>
				<td colspan="3">Change</td>
				<td class="text-right"><?php echo number($order->changed, 2); ?></td>
			</tr>
			</tbody>
		</table>
		<?php if(! is_null($pos->bill_footer) && $pos->bill_footer != "") : ?>
			<div class="row">
				<div class="col-sm-12 col-xs-12 padding-5 text-center padding-bottom-10" style="font-size:<?php echo $pos->footer_size; ?>px;">
					<?php echo $pos->bill_footer; ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if($pos->barcode) : ?>
		<div class="row margin-bottom-15">
    	<div class="col-sm-12 col-xs-12 padding-5 text-center padding-bottom-10">
    		<?php echo barcodeImage($order->code, 10, NULL, 12); ?>
    	</div>
    </div>
	<?php endif; ?>
		<div class="row hidden-print">
			<div class='col-sm-12 col-xs-12 padding-5 text-center' style="border-top:solid 1px #CCC; padding-top:10px;">
				<button class='btn btn-sm btn-primary margin-bottom-10' onclick='print_bill()'>Press Enter to Print</button>
			</div>
		</div>

	</div>

<script>
	var count = 0;
	function print_bill()
	{
		while(count < 2) {
			window.print(0);
			count++
		}
	}

	$(document).keyup(function(e){
		if(e.keyCode == 27)
		{
			localStorage.setItem('printState', 1);
			window.close();
		}

		if(e.keyCode == 13)
		{
			print_bill();
		}
	});

	window.onafterprint = function() {
		if(count == 1) {
			localStorage.setItem('printState', 1);
			window.close();
		}
	};

</script>
</body>
</html>
