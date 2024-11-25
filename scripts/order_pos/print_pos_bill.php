<!DOCTYPE HTML>
<html lang="th">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />

	<title><?php echo $this->title; ?></title>
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace.css" class="ace-main-stylesheet" id="main-ace-style" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/template.css" />

  <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/jquery-ui-1.10.4.custom.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/bootstrap.js"></script>
  <script src="<?php echo base_url(); ?>assets/js/ace/ace.js"></script>
</head>

<body style='padding-top:20px; background-color:white;'>

<div style="width:<?php echo $pos->paper_size; ?>mm; padding:2px; margin-left:auto; margin-right:auto;">
    <div class="hidden-print" style="margin-bottom:25px; display:none;">
    		<button type="button" class="btn btn-primary btn-xs" id="btn_print" onClick="print_bill()" style="width:50%; float:left; margin-bottom:5px;"><i class="fa fa-print"></i>&nbsp; พิมพ์ ( space )</button>
    		<button type="button" class="btn btn-success btn-xs" id="btn_cancle" onClick="go_back()" style="width:50%; float:left; margin-bottom:5px;"><i class="fa fa-arrow-left"></i>&nbsp; กลับ ( esc )</button>
    </div>
    <center><span style="font-size:<?php echo $shop->logo_font_size; ?>px; font-weight:bold; margin-bottom:15px;"><?php echo $shop->bill_logo; ?> </span></center>

    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:9px; margin-bottom:10px;" >
			<tr><td class="text-center"><?php echo $shop->bill_header; ?></td></tr>
			<?php if($shop->use_vat && !empty($shop->tax_id)) : ?>
				<tr><td class="text-center">TAX#<?php echo $shop->tax_id; ?> (VAT Included)</td></tr>
			<?php endif; ?>
			<?php if(!empty($order->pos_code)) : ?>
				<tr><td class="text-center">POS#<?php echo $order->pos_code; ?></td></tr>
			<?php endif; ?>
      <tr><td class="text-center">Date: <?php echo thai_date($order->date_add, TRUE, '/'); ?></td></tr>
      <tr><td class="text-center">Sale No/Ref: <?php echo $order->code; ?></td></tr>
      <tr><td class="text-center">Staff: <?php echo $order->uname; ?></td></tr>
			<?php if(!empty($shop->bill_text)) : ?>
			<tr><td class="padding-top-10 middle text-center"><?php echo $shop->bill_text; ?></td></tr>
			<?php endif; ?>
    </table>
    <?php
		$total_qty = 0;
		$total_price = 0;
		$total_amount = 0;
		$total_discount = 0;
		?>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; font-size:9px; margin-top:0px; margin-bottom:10px;">
        <thead>
        <th style="width:45%; border-bottom:solid 1px #DDD;">Items</th>
        <th style="width:20%; text-align:right; border-bottom:solid 1px #DDD;">Price</th>
        <th style="width:10%; text-align:right; border-bottom:solid 1px #DDD;">Qty</th>
        <th style="width:20%; text-align:right; border-bottom:solid 1px #DDD;">Amount</th>
        </thead>
				<?php if(!empty($details)) : ?>
        <?php foreach($details as $ro) : ?>
        <tr>
            <td style="padding-top:5px; white-space: nowrap; overflow-x:hidden;">
							<?php echo $ro->product_name;	?>
						</td>
            <td class="text-right"><?php echo number($ro->price,2); ?></td>
            <td class="text-right" style="padding-top:5px;"><?php echo number($ro->qty); ?></td>
            <td class="text-right" style="padding-top:5px;"><?php echo number($ro->price * $ro->qty,2); ?></td>
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
            <td colspan="2" align="right">Total</td>
            <td align="right"><?php echo number($total_qty); ?></td>
            <td align="right"><?php echo number($total_price,2); ?></td>
        </tr>
        <?php if( $total_discount > 0 ) : ?>
        <tr height="20px">
            <td colspan="2" align="right">Discount</td>
            <td colspan="2" align="right"><?php echo number_format($total_discount, 2); ?></td>
        </tr>
        <tr height="20px">
            <td colspan="2" align="right">Grand Total</td>
            <td colspan="2" align="right"><?php echo number_format($total_amount, 2); ?></td>
        </tr>
        <?php endif; ?>

        <tr height="20px">
            <td colspan="2" align="right">Tender</td><td colspan="2" align="right"><?php echo number($order->received, 2); ?></td>
        </tr>
        <tr height="20px">
            <td colspan="2" align="right">Return</td><td colspan="2" align="right"><?php echo number($order->changed, 2); ?></td>
        </tr>
         <tr height="20px">
            <td colspan="2" align="right">Pay By</td><td colspan="2" align="right"><?php echo $pay_by; ?></td>
        </tr>
    </table>
		<?php if(! is_null($shop->bill_footer) && $shop->bill_footer != "") : ?>
    <div class="row">
    	<div class="col-sm-12 col-xs-12 padding-5 text-center padding-bottom-10">
    		<?php echo $shop->bill_footer; ?>
    	</div>
    </div>
		<?php endif; ?>
		<div class="row hidden-print">
			<div class='col-sm-12 col-xs-12 padding-5' style="border-top:solid 1px #CCC; padding-top:10px;">
				<button class='btn btn-sm btn-primary btn-block margin-bottom-10' onclick='print_bill()'>Print (Enter)</button>
				<button class='btn btn-sm btn-warning btn-block' onclick='go_back()'>Back To POS (ESC)</button>
			</div>
		</div>

	</div>

<script>
	function print_bill()
	{
		window.print();
	}

	$(document).keyup(function(e){
		if(e.keyCode == 27)
		{
			go_back();
		}

		if(e.keyCode == 13)
		{
			print_bill();
		}
	});

	$(document).ready(function(e) {
        //print_bill();
    });

	function go_back()
	{
		window.location.href = "<?php echo $this->home; ?>/main/<?php echo $order->pos_id; ?>";
	}

</script>
</body>
</html>
