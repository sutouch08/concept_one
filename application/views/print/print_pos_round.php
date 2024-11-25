<!DOCTYPE HTML>
<html lang="th">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />

	<title>รายงานปิดรอบการขาย</title>
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
			<tr>
				<td>Doc No.: <?php echo $order->code; ?></td>
				<td>POS No : <?php echo $pos->code; ?></td>
			</tr>
			<tr>
				<td>Open by : <?php echo $this->user_model->get_name($order->open_user); ?></td>
				<td>Date: <?php echo thai_date($order->open_date, TRUE, '/'); ?></td>
			</tr>
			<tr>
				<td>Close by : <?php echo $this->user_model->get_name($order->close_user); ?></td>
				<td>Date: <?php echo empty($order->close_date) ? NULL : thai_date($order->close_date, TRUE, '/'); ?></td>
			</tr>
    </table>

		<?php $cash_expected = $order->open_cash + $order->cash_in + $order->cash_out + $order->total_cash + $order->down_cash + $order->return_cash; ?>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; font-size:<?php echo $pos->font_size; ?>px; margin-top:0px; margin-bottom:15px;">
			<thead>
				<th style="width:70%;"></th>
				<th style="width:30%;"></th>
			</thead>
			<tbody>
			<tr><td colspan="2" class="text-center font-size-16" style="border-bottom:dashed 2px #ddd;">สรุปเงินสด</td></tr>
			<tr height="20px">
				<td class="text-left">เงินสดตั้งต้น</td>
				<td class="text-right"><?php echo number($order->open_cash,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">ยอดขายเงินสด</td>
				<td class="text-right"><?php echo number($order->total_cash,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">มัดจำเงินสด</td>
				<td class="text-right"><?php echo number($order->down_cash,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">คืนเงินสด</td>
				<td class="text-right"><?php echo number($order->return_cash,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">นำเงินสดเข้า</td>
				<td class="text-right"><?php echo number($order->cash_in,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">นำเงินสดออก</td>
				<td class="text-right"><?php echo number($order->cash_out,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">คาดการณ์เงินสด</td>
				<td class="text-right"><?php echo number($cash_expected,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">เงินสดปิดรอบ</td>
				<td class="text-right"><?php echo number($order->close_cash,2); ?></td>
			</tr>
			<tr><td colspan="2" style="border-top:dashed 2px #ddd;">&nbsp;</td></tr>

			<tr><td colspan="2" class="text-center font-size-16" style="border-bottom:dashed 2px #ddd;">สรุปทั้งหมด</td></tr>
			<tr height="20px">
				<td class="text-left">ยอดขายเงินสด</td>
				<td class="text-right"><?php echo number($order->total_cash,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">ยอดขายเงินโอน</td>
				<td class="text-right"><?php echo number($order->total_transfer,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">ยอดขายบัตรเครดิต</td>
				<td class="text-right"><?php echo number($order->total_card,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">มัดจำเงินสด</td>
				<td class="text-right"><?php echo number($order->down_cash,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">มัดจำเงินโอน</td>
				<td class="text-right"><?php echo number($order->down_transfer,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">มัดจำบัตรเครดิต</td>
				<td class="text-right"><?php echo number($order->down_card,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">คืนเงินสด</td>
				<td class="text-right"><?php echo number($order->return_cash,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">คืนด้วยการโอน</td>
				<td class="text-right"><?php echo number($order->return_transfer,2); ?></td>
			</tr>
			<tr height="20px">
				<td class="text-left">ยอดรวมสุทธิ</td>
				<td class="text-right"><?php echo number($order->round_total,2); ?></td>
			</tr>
			</tbody>
		</table>
	<?php if( ! empty($details)) : ?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; font-size:<?php echo $pos->font_size; ?>px; margin-top:0px; margin-bottom:15px;">
			<thead>
				<th class="width-40" style="border-bottom:solid 1px #333;">DocNum</th>
				<th class="width-15 text-center" style="border-bottom:solid 1px #333;">TY</th>
				<th class="width-15 text-center" style="border-bottom:solid 1px #333;">PM</th>
				<th class="width-15 text-right" style="border-bottom:solid 1px #333;">AM</th>
				<th class="width-15 text-right" style="border-bottom:solid 1px #333;">EMP</th>
			</thead>
			<tbody>
	<?php foreach($details as $rs) : ?>
				<tr height="20px">
					<td class="text-left"><?php echo $rs->code; ?></td>
					<td class="text-center"><?php echo $rs->type; ?></td>
					<td class="text-center"><?php echo $rs->payment_role; ?></td>
					<td class="text-right"><?php echo number($rs->amount, 2); ?></td>
					<td class="text-right"><?php echo $rs->user; ?></td>
				</tr>
	<?php endforeach; ?>
			</tbody>
		</table>
		<div class="col-lg-12 col-md-12 col-sm-12 padding-5">
			TY :&nbsp;&nbsp;&nbsp;	S = ขาย, C = ยกเลิก, R = รับคืน, CR = ยกเลิกคืน, CI = นำเงินเข้า,
			<br/>CO = นำเงินออก, DP = รับมัดจำ, DC = ยกเลิกมัดจำ, RO = เปิดรอบ,
			<br/>RC = ปิดรอบ
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12 padding-5 margin-top-10">
			PM : &nbsp;&nbsp;&nbsp;1 = เงินสด, 2 = เงินโอน, 3 = บัตรเครดิต
		</div>
	<?php endif; ?>
		<div class="row hidden-print">
			<div class='col-sm-12 col-xs-12 padding-5 text-center' style="border-top:solid 1px #CCC; padding-top:10px;">
				<button class='btn btn-sm btn-primary margin-bottom-10' onclick='print_bill()'>Press Enter to Print</button>
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
			localStorage.setItem('printState', 1);
			window.close();
		}

		if(e.keyCode == 13)
		{
			print_bill();
		}
	});

	window.onafterprint = function() {
		localStorage.setItem('printState', 1);
		window.close();
	};

</script>
</body>
</html>
