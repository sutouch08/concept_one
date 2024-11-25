<?php $this->load->view("include/header"); ?>
<div class="row hidden-print">
	<div class="col-lg-8 col-md-8 col-sm-8 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
    <h3 class="title-xs">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
			<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>จุดขาย</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-shop-all" onclick="toggleAllShop(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-shop-select" onclick="toggleAllShop(0)">เลือก</button>
    </div>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เครื่อง POS</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-pos-all" onclick="toggleAllPos(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-pos-select" onclick="toggleAllPos(0)">เลือก</button>
    </div>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>พนักงาน</label>
    <select class="width-100" name="uname" id="uname">
    	<option value="all">ทั้งหมด</option>
			<?php echo select_shop_user(); ?>
    </select>
  </div>

  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" value="<?php echo date('d-m-Y'); ?>" placeholder="เริ่มต้น" />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" value="<?php echo date('d-m-Y'); ?>" placeholder="สิ้นสุด" />
    </div>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>เลขที่บิล</label>
    <input type="text" class="form-control input-sm text-center" id="billFrom" name="billFrom" placeholder="เริ่มต้น">
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label class="display-block not-show">End</label>
    <input type="text" class="form-control input-sm text-center" id="billTo" name="billTo" placeholder="สิ้นสุด">
  </div>

  <input type="hidden" id="allShop" name="allShop" value="1">
	<input type="hidden" id="allPos" name="allPos" value="1">
	<input type="hidden" id="token" name="token"  value="<?php echo uniqid(); ?>">
</div>
<hr>

<div class="modal fade" id="shop-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="width:400px; max-width:95%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="title">ระบุจุดขาย</h4>
			</div>
			<div class="modal-body" style="padding:0px;">
				<?php if(!empty($shop_list)) : ?>
					<?php foreach($shop_list as $rs) : ?>
						<div class="col-sm-12">
							<label>
								<input type="checkbox" class="shop-chk" name="shop[]" value="<?php echo $rs->id; ?>" style="margin-right:10px;" />
								<span class="lbl">
									<?php echo $rs->name; ?>
								</span>
							</label>
						</div>
					<?php endforeach; ?>
				<?php endif;?>
				<div class="divider" ></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-block" data-dismiss="modal">ตกลง</button>
			</div>
		</div>
    </div>
</div>

<div class="modal fade" id="pos-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="width:400px; max-width:95%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="title">ระบุเครื่อง POS</h4>
			</div>
			<div class="modal-body" style="padding:0px;">
				<?php if(!empty($pos_list)) : ?>
					<?php foreach($pos_list as $rs) : ?>
						<div class="col-sm-12">
							<label>
								<input type="checkbox" class="pos-chk" name="pos[]" value="<?php echo $rs->id; ?>" style="margin-right:10px;" />
								<span class="lbl">
									<?php echo $rs->name; ?>
								</span>
							</label>
						</div>
					<?php endforeach; ?>
				<?php endif;?>
				<div class="divider" ></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-block" data-dismiss="modal">ตกลง</button>
			</div>
		</div>
    </div>
</div>
</form>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped tableFixHead border-1" style="min-width:900px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center fix-header">#</th>
					<th class="fix-width-100 middle fix-header">วันที่</th>
					<th class="fix-width-120 middle fix-header">เลขที่เอกสาร</th>
					<th class="fix-width-120 middle fix-header">ใบตัดยอดขาย</th>
					<th class="fix-width-120 middle fix-header">จุดขาย</th>
					<th class="fix-width-120 middle fix-header">เครื่อง POS</th>
					<th class="fix-width-120 middle fix-header">พนักงาน</th>
					<th class="fix-width-100 middle text-right fix-header">ยอดเงิน</th>
				</tr>
			</thead>
			<tbody id="result">

			</tbody>
		</table>
  </div>
</div>

<script id="report-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		{{#if @last}}
		<tr>
			<td colspan="7" class="text-right">รวม</td>
			<td class="text-right">{{totalAmount}}</td>
		</tr>
		{{else}}
			<tr>
				<td class="text-center no">{{no}}</td>
				<td class="">{{date}}</td>
				<td class="">{{code}}</td>
				<td class="">{{ref_code}}</td>
				<td class="">{{shop_name}}</td>
				<td>{{pos_name}}</td>
				<td>{{emp_name}}</td>
				<td class="text-right">{{payAmount}}</td>
			</tr>
		{{/if}}
	{{/each}}
</script>


<script src="<?php echo base_url(); ?>scripts/report/sales/pos_sales_report.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view("include/footer"); ?>
