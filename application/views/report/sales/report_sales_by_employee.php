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
			<label>พนักงานขาย</label>
			<div class="btn-group width-100">
				<button type="button" class="btn btn-sm btn-primary width-50" id="btn-slp-all" onclick="toggleAllSlp(1)">ทั้งหมด</button>
				<button type="button" class="btn btn-sm width-50" id="btn-slp-selected" onclick="toggleAllSlp(0)">เลือก</button>
			</div>
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
	    <label>เล่มเอกสาร</label>
	    <select class="form-control input-sm" name="bookcode" id="bookcode">
				<option value="all">ทั้งหมด</option>
				<option value="C">เงินสด</option>
				<option value="T">เครดิต</option>
				<option value="P">POS</option>
			</select>
	  </div>

		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" value="" placeholder="เริ่มต้น" />
				<input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" value="" placeholder="สิ้นสุด" />
			</div>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
	    <label class="display-block not-show">date type</label>
			<label>
				<input type="radio" class="ace" name="date_type" id="date-type-s" value="S" checked />
				<span class="lbl"> วันที่บันทึกขาย</span>
			</label>
			<!-- S = shipped date, D = Doc date -->
	  </div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
	    <label class="display-block not-show">date type</label>
			<label>
				<input type="radio" class="ace" name="date_type" id="date-type-d" value="D" />
				<span class="lbl"> วันที่เอกสาร</span>
			</label>
			<!-- S = shipped date, D = Doc date -->
	  </div>

		<input type="hidden" id="allSlp" name="allSlp" value="1">
		<input type="hidden" id="token" name="token"  value="<?php echo uniqid(); ?>">
	</div>
	<hr>

	<div class="modal fade" id="slp-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" id="modal" style="width:400px; max-width:95%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="title">พนักงานขาย</h4>
				</div>
				<div class="modal-body" style="padding:0px;">
					<?php if(!empty($slpList)) : ?>
						<?php foreach($slpList as $rs) : ?>
							<div class="col-sm-12">
								<label>
									<input type="checkbox" class="slp-chk" name="slp[]" value="<?php echo $rs->id; ?>" style="margin-right:10px;" />
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
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
		** รายงานนี้รวม ค่าขนส่ง และ VAT **
	</div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="result">

  </div>
</div>



<script id="sale-template" type="text/x-handlebarsTemplate">
	<table class="table table-striped tableFixHead border-1">
		<thead>
			<tr>
				<th class="fix-width-40 middle text-center fix-header">#</th>
				<th class="min-width-200 middle fix-header">พนักงานขาย</th>
				<th class="fix-width-150 middle text-right fix-header">ยอดเงิน</th>
			</tr>
		</thead>
		<tbody>
		{{#each this}}
			{{#if @last}}
			<tr>
				<td colspan="2" class="text-right">รวม</td>
				<td class="text-right">{{totalAmount}}</td>
			</tr>
			{{else}}
				<tr>
					<td class="text-center no">{{no}}</td>
					<td>{{name}}</td>
					<td class="text-right">{{amount}}</td>
				</tr>
			{{/if}}
		{{/each}}
		</tbody>
	</table>
</script>


<script src="<?php echo base_url(); ?>scripts/report/sales/sales_by_employee.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view("include/footer"); ?>
