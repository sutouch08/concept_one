<?php $this->load->view('include/header'); ?>
<style>
	.hilight {
		background-color: #c7ecff !important;
	}
</style>
<div class="row hidden-print" id="header-row">
	<div class="col-lg-8 col-md-8 col-sm-8 padding-5 hidden-xs">
    <h3 class="title">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h4 class="title-xs"><i class="fa fa-bar-chart"></i> <?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
			<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
	<div class="row" id="search-row">
		<div class="col-lg-2-harf col-md-2-harf col-sm-3-harf col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date e" name="fromDate" id="fromDate" placeholder="เริ่มต้น" required />
				<input type="text" class="form-control input-sm width-50 text-center e" name="toDate" id="toDate" placeholder="สิ้นสุด" required/>
			</div>
		</div>
		<div class="col-lg-1 col-md-1 col-sm-2 col-xs-6 padding-5">
			<label class="display-block">สถานะ</label>
			<select class="form-control input-sm e" name="status" id="status">
				<option value="all">ทั้งหมด</option>
				<option value="O">Open</option>
				<option value="C">Closed</option>
			</select>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>เลขที่</label>
			<input type="text" class="form-control input-sm text-center e" name="code" id="code" />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>รหัสลูกค้า</label>
			<input type="text" class="form-control input-sm text-center" name="customer_code" id="customer-code" />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>ชื่อลูกค้า</label>
			<input type="text" class="form-control input-sm text-center e" name="customer_name" id="customer-name" />
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>เบอร์โทร</label>
			<input type="text" class="form-control input-sm text-center e" name="phone" id="phone" />
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>ใบสั่งขาย</label>
			<input type="text" class="form-control input-sm text-center e" name="reference" id="reference" />
		</div>

		<div class="col-lg-1 col-md-1 col-sm-2 col-xs-6 padding-5">
			<label class="display-block not-show">clear</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clear_form()">Clear</button>
		</div>
	</div>
	<input type="hidden" id="token" name="token"  value="<?php echo uniqid(); ?>">
</form>
<hr>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" id="result" style="overflow:auto;">

	</div>
</div>

<script id="template" type="text/x-handlebars-template">
{{#if data}}
	<table class="table table-striped border-1 tableFixHead" style="min-width:1800px;">
		<thead>
			<tr>
				<th class="fix-width-60 text-center fix-header">#</th>
				<th class="fix-width-100 text-center fix-header">วันที่รับ</th>
				<th class="fix-width-100 text-center fix-header">วันที่ใช้</th>
				<th class="fix-width-120 fix-header">เลขที่</th>
				<th class="fix-width-120 fix-header">อ้างอิง</th>
				<th class="fix-width-120 fix-header">บิลขาย</th>
				<th class="fix-width-120 fix-header">ใบกำกับ</th>
        <th class="fix-width-100 text-right fix-header">ก่อนใช้</th>
        <th class="fix-width-100 text-right fix-header">ใช้ไป</th>
        <th class="fix-width-100 text-right fix-header">คงเหลือ</th>
				<th class="fix-width-60 text-center fix-header">สถานะ</th>
        <th class="fix-width-100 fix-header">รหัสลูกค้า</th>
        <th class="fix-width-200 fix-header">ชื่อลูกค้า</th>
				<th class="fix-width-150 fix-header">เบอร์โทร</th>
				<th class="min-width-250 fix-header">อ้างอิงลูกค้า</th>
			</tr>
		</thead>
		<tbody>
			{{#each data}}
				<tr class="pointer {{color}}" onclick="toggleHilight($(this))">
					<td class="middle text-center">{{no}}</td>
					<td class="middle text-center">{{date_add}}</td>
					<td class="middle text-center">{{use_date}}</td>
					<td class="middle">{{code}}</td>
					<td class="middle">{{so_code}}</td>
          <td class="middle">{{bill_code}}</td>
					<td class="middle">{{invoice_code}}</td>
					<td class="middle text-right">{{amountBfUse}}</td>
					<td class="middle text-right">{{usedAmount}}</td>
					<td class="middle text-right">{{available}}</td>
					<td class="middle text-center">{{statusLabel}}</td>
					<td class="middle">{{customer_code}}</td>
					<td class="middle">{{customer_name}}</td>
					<td class="middle">{{customer_phone}}</td>
					<td class="middle">{{customer_ref}}</td>
				</tr>
			{{/each}}
		</tbody>
	</table>
	{{else}}
		<div class="alert alert-info margin-top-30">--- ไม่พบรายการตามเงื่อนไขที่กำหนด ---</div>
	{{/if}}
</script>


<script src="<?php echo base_url(); ?>scripts/report/audit/down_payment_details.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
