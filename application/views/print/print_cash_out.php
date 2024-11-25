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

  <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/jquery-ui-1.10.4.custom.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/bootstrap.js"></script>
  <script src="<?php echo base_url(); ?>assets/js/ace/ace.js"></script>
</head>

<body style='padding-top:20px; background-color:white;'>

<div style="width:<?php echo $pos->paper_size; ?>mm; padding:2px; margin-left:auto; margin-right:auto;">
		<center class="padding-bottom-10 margin-bottom-10 border-bottom-1 font-size-24">นำเงินออก</center>

    <table class="width-100" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:16px; margin-bottom:10px;" >
			<tr><td class="width-40">POS ID</td><td class="width-60"><?php echo $pos->code; ?></td></tr>
			<tr><td class="width-40">วันที่</td><td class="width-60"><?php echo thai_date($movement->date_upd, TRUE, '/'); ?></td></tr>
			<tr><td class="width-40">จำนวนเงินนำเข้า</td><td class="width-60"><?php echo number($movement->amount, 2); ?></td></tr>
			<tr><td class="width-40">จำนวนคงเหลือปัจจุบัน</td><td class="width-60"><?php echo number($pos->cash_amount, 2); ?></td></tr>
			<tr><td class="width-40">นำเข้าโดย</td><td class="width-60"><?php echo $movement->emp_name; ?></td></tr>
			<tr style="height:10mm;"><td colspan="2" class="text-center">--- END --</td></tr>
    </table>

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
