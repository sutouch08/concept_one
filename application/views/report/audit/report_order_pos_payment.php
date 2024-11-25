<?php $this->load->view('include/header'); ?>
<div class="row hidden-print" id="page-title">
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
<div class="row" id="filter">
	<div class="col-lg-2-harf col-md-3 col-sm-3-harf col-xs-6 padding-5">
    <label>วันที่ทำรายการ</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" placeholder="เริ่มต้น" required />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" placeholder="สิ้นสุด" required/>
    </div>
  </div>

  <div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>จุดขาย</label>
		<select class="form-control input-sm" name="shop_id" id="shop_id">
			<option value="all">ทั้งหมด</option>
			<?php echo select_shop_id(); ?>
		</select>
	</div>
	<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เครื่อง POS</label>
		<select class="form-control input-sm" name="pos_id" id="pos_id">
			<option value="all">ทั้งหมด</option>
			<?php echo select_pos_id(); ?>
		</select>
	</div>

  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>การชำระเงิน</label>
    <select class="form-control input-sm" name="role" id="role">
      <option value="all">ทั้งหมด</option>
			<option value="1">เงินสด</option>
			<option value="2">เงินโอน</option>
			<option value="3">บัตรเครดิต</option>
    </select>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>ประเภท</label>
    <select class="form-control input-sm" name="ref_type" id="ref-type">
      <option value="all">ทั้งหมด</option>
			<option value="DP">มัดจำ</option>
			<option value="SP">บิลขาย</option>
    </select>
  </div>
</div>
	<input type="hidden" id="token" name="token"  value="<?php echo uniqid(); ?>">

</form>
<hr>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 border-1" id="result" style="overflow:auto;">

	</div>
</div>

<script id="doc-template" type="text/x-handlebars-template">
{{#if data}}
	<table class="table table-striped border-1 tableFixHead">
		<thead>
			<tr>
				<th class="fix-width-60 text-center fix-header">#</th>
				<th class="fix-width-100 text-center fix-header">วันที่</th>
				<th class="fix-width-120 fix-header">เลขที่</th>
				<th class="fix-width-120 fix-header">ใบสั่งขาย</th>
        <th class="fix-width-120 fix-header">บิลขาย</th>
        <th class="fix-width-100 text-right fix-header">เงินสด</th>
        <th class="fix-width-100 text-right fix-header">เงินโอน</th>
        <th class="fix-width-100 text-right fix-header">บัตรเครดิต</th>
        <th class="fix-width-100 text-right fix-header">ยอดรวม</th>
        <th class="fix-width-120 fix-header">จุดขาย</th>
        <th class="fix-width-120 fix-header">เครื่อง POS</th>
			</tr>
		</thead>
		<tbody>
			{{#each data}}
				<tr>
					<td class="middle text-center">{{no}}</td>
					<td class="middle text-center">{{date_add}}</td>
					<td class="middle">{{code}}</td>
					<td class="middle">{{so_code}}</td>
          <td class="middle">{{bill_code}}</td>
					<td class="middle text-right">{{cash_amount}}</td>
					<td class="middle text-right">{{transfer_amount}}</td>
					<td class="middle text-right">{{card_amount}}</td>
          <td class="middle text-right">{{amount}}</td>
					<td class="middle text-center">{{shop_name}}</td>
					<td class="middle text-center">{{pos_name}}</td>
				</tr>
			{{/each}}
		</tbody>
	</table>
	{{else}}
		<div class="alert alert-info margin-top-30">--- ไม่พบรายการตามเงื่อนไขที่กำหนด ---</div>
	{{/if}}
</script>


<script id="date-template" type="text/x-handlebars-template">
{{#if data}}
	<table class="table table-striped border-1 tableFixHead">
		<thead>
			<tr>
				<th class="fix-width-60 text-center fix-header">#</th>
				<th class="fix-width-100 text-center fix-header">วันที่</th>
        <th class="fix-width-150 text-right fix-header">เงินสด</th>
        <th class="fix-width-150 text-right fix-header">เงินโอน</th>
        <th class="fix-width-150 text-right fix-header">บัตรเครดิต</th>
        <th class="fix-width-150 text-right fix-header">ยอดรวม</th>
        <th class="min-width-100 text-right fix-header"></th>
			</tr>
		</thead>
		<tbody>
			{{#each data}}
				<tr>
					<td class="middle text-center">{{no}}</td>
					<td class="middle text-center">{{date_add}}</td>
					<td class="middle text-right">{{cash_amount}}</td>
					<td class="middle text-right">{{transfer_amount}}</td>
					<td class="middle text-right">{{card_amount}}</td>
          <td class="middle text-right">{{amount}}</td>
          <td></td>
				</tr>
			{{/each}}
		</tbody>
	</table>
	{{else}}
		<div class="alert alert-info margin-top-30">--- ไม่พบรายการตามเงื่อนไขที่กำหนด ---</div>
	{{/if}}
</script>


<script src="<?php echo base_url(); ?>scripts/report/audit/order_pos_payment.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
