<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-sm-8 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
		<div class="col-sm-4 padding-5">
			<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
				<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
			</p>
		</div>
</div><!-- End Row -->
<hr class="hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
	<div class="col-sm-1 col-1-harf padding-5">
		<label class="display-block">ผู้ยืม</label>
		<div class="btn-group width-100">
			<button type="button" class="btn btn-sm btn-primary width-50" id="btn-emp-all" onclick="toggleAllEmp(1)">ทั้งหมด</button>
			<button type="button" class="btn btn-sm width-50" id="btn-emp-use" onclick="toggleAllEmp(0)">ระบุ</button>
		</div>
	</div>
	<div class="col-sm-3 padding-5">
		<label class="display-block not-show">&nbsp;</label>
		<input type="text" class="form-control input-sm" name="empName" id="empName" placeholder="ระบุชื่อพนักงาน" disabled>
	</div>
	<div class="col-sm-1 col-1-harf padding-5">
		<label>สินค้า</label>
		<div class="btn-group width-100">
			<button type="button" class="btn btn-sm btn-primary width-50" id="btn-pd-all" onclick="toggleAllProduct(1)">ทั้งหมด</button>
			<button type="button" class="btn btn-sm width-50" id="btn-pd-range" onclick="toggleAllProduct(0)">ระบุ</button>
		</div>
	</div>
	<div class="col-sm-2 padding-5">
		<label>เริ่มต้น</label>
		<input type="text" class="form-control input-sm" id="pdFrom" name="pdFrom" placeholder="เริ่มต้น" disabled>
	</div>
	<div class="col-sm-2 padding-5">
		<label>สิ้นสุด</label>
		<input type="text" class="form-control input-sm" id="pdTo" name="pdTo" placeholder="สิ้นสุด" disabled>
	</div>
	<div class="col-sm-2 padding-5">
		<label>วันที่</label>
		<div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" readonly value="">
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" readonly value="">
    </div>
	</div>
</div>
<input type="hidden" id="allEmp" name="allEmp" value="1" />
<input type="hidden" id="empId" name="empId" value="" />
<input type="hidden" id="allProduct" name="allProduct" value="1" />
<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>">
<hr>
</form>

<div class="row">
  <div class="col-sm-12 padding-5">
		<table class="table table-bordered">
			<thead>
				<tr class="font-size-12">
					<th class="width-5 text-center">#</th>
					<th class="width-15 text-center">ผู้ยืม</th>
					<th class="width-15 text-center">ผู้รับ</th>
					<th class="width-10 text-center">ผู้ทำรายการ</th>
					<th class="width-10 text-center">เลขที่เอกสาร</th>
					<th class="width-10 text-center">รหัสสินค้า</th>
					<th class="width-8 text-center">ราคา</th>
					<th class="width-5 text-center">ยืม</th>
					<th class="width-5 text-center">คืน</th>
					<th class="width-5 text-center">ค้าง</th>
					<th class="width-8 text-center">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="result">

			</tbody>
		</table>
  </div>
</div>

<script id="template" type="text/x-handlebarsTemplate">
	{{#each this}}
		{{#if @last}}
			<tr class="font-size-12">
				<td colspan="7" class="middle text-right">รวม</td>
				<td class="middle text-right">{{total_lend}}</td>
				<td class="middle text-right">{{total_return}}</td>
				<td class="middle text-right">{{total_balance}}</td>
				<td class="middle text-right">{{total_amount}}</td>
			</tr>
		{{else}}
			<tr class="font-size-10">
				<td class="middle text-center">{{no}}</td>
				<td class="middle">{{emp_name}}</td>
				<td class="middle">{{user_ref}}</td>
				<td class="middle">{{user}}</td>
				<td class="middle">{{order_code}}</td>
				<td class="middle">{{product_code}}</td>
				<td class="middle text-right">{{price}}</td>
				<td class="middle text-right">{{lend}}</td>
				<td class="middle text-right">{{return}}</td>
				<td class="middle text-right">{{balance}}</td>
				<td class="middle text-right">{{amount}}</td>
			</tr>
		{{/if}}
	{{/each}}
</script>



<script src="<?php echo base_url(); ?>scripts/report/audit/report_lend_backlogs.js"></script>
<?php $this->load->view('include/footer'); ?>
