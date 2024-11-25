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

<body style='padding-top:20px; background-color:white;'>

<div style="width:<?php echo $pos->paper_size; ?>mm; padding:2px; margin-left:auto; margin-right:auto;">
	<table class="width-100" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:<?php echo $pos->font_size; ?>; margin-bottom:10px;" >
		<tr><td class="width-50"></td><td class="width-50"></td></tr>

		<tr><td class="width-50"></td><td class="width-50"></td></tr>
		<tr><td colspan="2" class="text-center" style="font-size:20px;">ใบรับเงินมัดจำ</td></tr>

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

		<tr><td class="text-left">เลขที่ : <?php echo $code; ?></td><td class="text-right">วันที่ : <?php echo thai_date($date_add, TRUE, '/'); ?></td></tr>
		<tr><td class="text-left">Staff : <?php echo $staff; ?></td><td class="text-right">POS ID : <?php echo $pos->pos_no; ?></td></tr>
		<tr><td class="text-left">ลูกค้า : <?php echo $customer_name; ?></td><td class="text-right">พนักงานขาย : <?php echo $sale_name; ?></td></tr>
	</table>
    <?php
		$total_qty = 0;
		$total_price = 0;
		$total_amount = 0;
		$total_discount = 0;
		?>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; font-size:<?php echo $pos->font_size; ?>px; margin-top:0px; margin-bottom:15px;">
			<thead>
				<th class="width-80 text-left" style="border-bottom:solid 1px #DDD;">รายการ</th>
				<th class="width-20 text-right" style="border-bottom:solid 1px #DDD;">จำนวนเงิน</th>
			</thead>
				<tr>
	        <td class="text-left"><?php echo $item; ?></td>
					<td class="text-right" style="padding-top:5px;"><?php echo $amount; ?></td>
				</tr>
				<tr>
	        <td colspan="2" class="text-left"><?php echo $customer_ref; ?></td>
				</tr>
				<tr>
	        <td colspan="2" class="text-left"><?php echo $customer_phone; ?></td>
				</tr>
			</table>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; font-size:102x; margin-top:0px; margin-bottom:10px;">
				<tr>
					<td class="width-45"></td>
					<td class="width-20"></td>
					<td class="width-15 text-right"></td>
					<td class="width-20 text-right"></td>
				</tr>
				<tr height="20px">
					<td class="text-left" colspan="2">Total</td>
					<td class="text-right"></td>
					<td class="text-right"><?php echo $amount; ?></td>
				</tr>

				<tr height="20px">
					<td class="text-left" colspan="2">Paid By <?php echo $payment_name; ?></td>
		      <td></td>
					<td class="text-right"><?php echo $received; ?></td>
				</tr>
<?php if($payment_role == 6 && ! empty($payments)) : ?>
	<?php foreach($payments as $rs) : ?>
					<tr height="20px">
						<td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp; - <?php echo $rs->role_name; ?></td>
						<td class="text-right"><?php echo number($rs->amount, 2); ?></td>
					</tr>
	<?php endforeach; ?>
<?php endif; ?>
				<tr>
					<td class="text-left" colspan="2">Change</td>
		      <td></td>
					<td class="text-right"><?php echo $changed; ?></td>
				</tr>
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
    		<?php echo barcodeImage($code, 10, NULL, 12); ?>
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
