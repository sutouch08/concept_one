<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
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
			<button type="button" class="btn btn-white btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
			<button type="button" class="btn btn-white btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
			<button type="button" class="btn btn-white btn-info" onclick="printReport()"><i class="fa fa-print"></i> พิมพ์</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 "/>
<div class="row">
	<div class="col-lg-2-harf col-md-3 col-sm-3-harf col-xs-6 padding-5">
		<label>วันที่เอกสาร</label>
		<div class="input-daterange input-group width-100">
			<input type="text" class="form-control input-sm width-50 text-center from-date r" name="fromDate" id="fromDate" placeholder="เริ่มต้น" required />
			<input type="text" class="form-control input-sm width-50 text-center r" name="toDate" id="toDate" placeholder="สิ้นสุด" required/>
		</div>
	</div>

	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>คลังสินค้า</label>
		<select class="width-100 r" id="warehouse" name="warehouse">
			<option value="">เลือกคลัง</option>
			<?php echo select_sell_warehouse(getConfig('DEFAULT_WAREHOUSE')); ?>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="display-block">ประเภทเอกสาร</label>
		<div class="btn-group width-100" style="height:30px;">
			<button type="button" class="btn btn-sm btn-primary width-50" id="btn-role-all" onclick="toggleAllRole(1)">ทั้งหมด</button>
			<button type="button" class="btn btn-sm width-50" id="btn-role-range" onclick="toggleAllRole(0)">เลือก</button>
		</div>
	</div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="display-block">ช่องทางการขาย</label>
		<div class="btn-group width-100" style="height:30px;">
			<button type="button" class="btn btn-sm btn-primary width-50" id="btn-channels-all" onclick="toggleAllChannels(1)">ทั้งหมด</button>
			<button type="button" class="btn btn-sm width-50" id="btn-channels-range" onclick="toggleAllChannels(0)">เลือก</button>
		</div>
	</div>
</div>
<hr class="margin-top-15 margin-bottom-15">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5"  style="min-height: 300px; max-height: 500px; overflow:auto;">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="fix-width-60 text-center">#</th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="min-width-350">สินค้า</th>
					<th class="fix-width-100 text-right">จำนวน</th>
					<th class="fix-width-100 text-right">สต็อก</th>
				</tr>
			</thead>
			<tbody id="result">
			</tbody>
		</table>
	</div>
</div>

<input type="hidden" id="allRole" name="allRole" value="1" />
<input type="hidden" id="allChannels" name="allChannels" value="1" />


<div class="modal fade" id="channels-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:300px; max-width:95%; margin-left:auto; margin-right:auto;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="title">ระบุช่องทางการขาย</h4>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top:15px;">
					<?php if(!empty($channels_list)) : ?>
						<?php foreach($channels_list as $rs) : ?>
							<label class="display-block">
								<input type="checkbox" class="ace ch-chk" name="channels[]" value="<?php echo $rs->code; ?>"/>
								<span class="lbl">&nbsp; <?php echo $rs->name; ?></span>
							</label>
						<?php endforeach; ?>
					<?php endif;?>
				</div>
				<div class="divider" ></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-block" data-dismiss="modal">ตกลง</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="role-modal" tabindex="-1" role="dialog" aria-labelledby="role-modal" aria-hidden="true">
	<div class="modal-dialog" style="width:250px; max-width:95%; margin-left:auto; margin-right:auto;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="title">เลือกประเภทเอกสาร</h4>
			</div>
			<div class="modal-body" style="padding:0px;">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top:15px;">
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="S" /><span class="lbl">  WO</span></label>
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="C" /><span class="lbl">  WC</span></label>
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="N" /><span class="lbl">  WT</span></label>
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="P" /><span class="lbl">  WS</span></label>
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="U" /><span class="lbl">  WU</span></label>
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="T" /><span class="lbl">  WQ</span></label>
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="Q" /><span class="lbl">  WV</span></label>
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="L" /><span class="lbl">  WL</span></label>
				</div>
				<div class="divider" ></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-block" data-dismiss="modal">ตกลง</button>
			</div>
		</div>
	</div>
</div>


<form class="hidden-print" id="exportForm" method="post" action="<?php echo $this->home; ?>/do_export">
	<input type="hidden" name="data" id="data" value="">
	<input type="hidden" id="token" name="token"  value="<?php echo uniqid(); ?>">
</form>



<script id="template" type="text/x-handlebars-template">
	{{#each this}}
		{{#if nodata}}
			<tr><td colspan="5" class="text-center">---- ไม่พบข้อมูลตามเงื่อนไขที่กำหนด ----</td></tr>
		{{else}}
			<tr>
				<td class="middle text-center">{{no}}</td>
				<td class="middle">{{product_code}}</td>
				<td class="middle">{{product_name}}</td>
				<td class="middle text-right">{{order_qty}}</td>
				<td class="middle text-right">{{stock_qty}}</td>
			</tr>
		{{/if}}
	{{/each}}
</script>


<script src="<?php echo base_url(); ?>scripts/report/audit/products_backlogs_summary.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
