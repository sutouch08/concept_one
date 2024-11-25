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
  <style> .page_layout{ border: solid 1px #AAA; border-radius:5px; 	} @media print{ 	.page_layout{ border: none; } } 	</style>
</head>

<body style="padding-top:20px; background-color:white;">
  <div class='hidden-print' style='margin-top:10px; padding-bottom:10px; padding-right:5mm; width:200mm; margin-left:auto; margin-right:auto; text-align:right'>
	   <button class='btn btn-primary' onclick='print()'><i class='fa fa-print'></i>&nbspพิมพ์</button>
	</div>

  <div style='width:100%'>
    <div class="page_layout" style="width:200mm; padding-top:5mm; height:282mm; margin:auto; page-break-after:always;" >
      <div style="width:190mm; padding:2px; margin-left:auto; margin-right:auto;">
        <table class="table table-bordered border-1" align="center" cellpadding="0" cellspacing="0" style="font-size:28px; margin-bottom:10px;" >
          <tr class="hide"><td class="width-50"></td><td class="width-50"></td></tr>
          <tr>
            <td colspan="2" class="text-center" style="font-size:20px;">ใบนำส่งสินค้า</td>
          </tr>
          <tr>
            <td>
              <span style="font-size:14px;">ใบรับสินค้า</span>
              <p class="text-center" style="font-size:32px;"><?php echo $order->code; ?></p>
            </td>
            <td>
              <span style="font-size:14px;">วันที่</span>
              <p class="text-center" style="font-size:32px;"><?php echo thai_date($order->date_add, FALSE, '/'); ?></p>
            </td>
          </tr>
          <tr>
            <td>
              <span style="font-size:14px;">ใบเบิกแปรสภาพ</span>
              <p class="text-center" style="font-size:32px;"><?php echo $order->order_code; ?></p>
            </td>
            <td class="">
              <span style="font-size:14px;">ใบสั่งงาน</span>
              <p class="text-center" style="font-size:32px;"><?php echo $order->so_code; ?></p>
            </td>
          </tr>
          <tr>
            <td>
              <span style="font-size:14px;">โซน</span>
              <p class="text-center" style="font-size:14px;"><?php echo $order->zone_code; ?></p>
            </td>
            <td class="">
              <span style="font-size:14px;">คลัง</span>
              <p class="text-center" style="font-size:14px;"><?php echo $order->warehouse_code; ?></p>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="text-left">
              <span style="font-size:14px;">ชื่องาน</span>
              <p class="text-center" style="font-size:28px;"><?php echo $so->job_title; ?></p>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="text-left">
              <span style="font-size:14px;">ลูกค้า</span>
              <p class="text-center" style="font-size:28px;"><?php echo $so->customer_ref; ?> (<?php echo $so->phone; ?>)</p>
            </td>
          </tr>
          <tr>
            <td colspan="2" class="">
              <span style="font-size:14px;">ที่อยู๋</span>
              <p class="" style="font-size:20px;"><?php echo $so->customer_address; ?></p>
            </td>
          </tr>
        </table>
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
