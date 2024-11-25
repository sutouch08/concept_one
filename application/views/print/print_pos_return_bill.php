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
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/pos.css" />

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
		<div class="width-100 text-center font-size-24">ใบรับคืนสินค้า</div>

    <table class="width-100" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:10px; margin-bottom:10px;" >
			<tr><td class="width-50"></td><td class="width-50"></td></tr>

		<?php if( ! empty($pos->bill_header_2)) : ?>
				<tr>
					<td colspan="2" class="<?php echo $pos->header_align_2; ?>" style="font-size:<?php echo $pos->header_size_2; ?>px;">
						<?php echo $pos->bill_header_2; ?>
					</td>
				</tr>
		<?php endif; ?>
			<tr><td class="text-center font-size-12" colspan="2">สาขา : <?php echo $pos->shop_code; ?> - <?php echo $pos->shop_name; ?></td></tr>
			<tr><td>เลขที่ : <?php echo $order->code; ?></td><td class="text-right">วันที่ : <?php echo thai_date($order->date_add, TRUE, '/'); ?></td></tr>
			<tr><td>คลัง: <?php echo $pos->warehouse_code; ?></td><td class="text-right">โซน : <?php echo $order->zone_name; ?></td></tr>
			<tr><td>POS ID: <?php echo $order->pos_no; ?></td><td class="text-right">พนักงาน : <?php echo $order->emp_name; ?></td></tr>
			<tr>
				<td>บิลขาย : <?php echo $order->order_code; ?></td>
				<td class="text-right"><?php if( ! empty($order->ref_code)) : ?>ใบลดหนี้ : <?php echo $order->ref_code; ?><?php endif; ?></td>
			</tr>
    </table>
    <?php
		$total_qty = 0;
		$total_price = 0;
		$total_amount = 0;
		$total_discount = 0;
		?>
    <table class="slip-table">
			<thead>
				<th class="width-35">รายการ</th>
				<th class="width-15 text-right">ราคา</th>
				<th class="width-15 text-right">ส่วนลด</th>
				<th class="width-15 text-right">จำนวน</th>
				<th class="width-20 text-right">มูลค่า</th>
			</thead>
			<tbody>
			<?php if(!empty($details)) : ?>
        <?php foreach($details as $ro) : ?>
					<tr>
						<td style="padding-top:5px; white-space: nowrap; overflow-x:hidden;">
							<?php echo $ro->product_code ." : ".$ro->product_name;	?>
						</td>
						<td class="text-right"><?php echo number($ro->price,2); ?></td>
						<td class="text-right"><?php echo $ro->discount_label; ?></td>
						<td class="text-right" style="padding-top:5px;"><?php echo number($ro->return_qty); ?></td>
						<td class="text-right" style="padding-top:5px;"><?php echo number($ro->total_amount,2); ?><?php echo $pos->use_vat ? 'V' : ''; ?></td>
					</tr>
					<?php
					$total_qty += $ro->return_qty;
					$total_amount += $ro->total_amount;
					?>
        <?php endforeach; ?>
			<?php endif; ?>
			<tr height="20px">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr height="20px">
				<td colspan="3">Total</td>
				<td class="text-right"><?php echo number($total_qty, 2); ?></td>
				<td class="text-right"><?php echo number($total_amount,2); ?></td>
			</tr>
			</tbody>
		</table>
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
