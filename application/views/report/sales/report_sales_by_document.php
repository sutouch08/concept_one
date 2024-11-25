<?php $this->load->view('include/header'); ?>
<div class="row hidden-print" id="header-row">
	<div class="col-lg-10 col-md-8 col-sm-8 padding-5 hidden-xs">
    <h3 class="title">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
    <h3 class="title-xs">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
			<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row" id="search-row">
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="display-block">พนักงานขาย</label>
    <div class="btn-group width-100" style="height:30px;">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-sale-all" onclick="toggleAllSale(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-sale-select" onclick="toggleAllSale(0)">เลือก</button>
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

  <div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" placeholder="เริ่มต้น" required />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" placeholder="สิ้นสุด" required/>
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

	<input type="hidden" id="all-sale" name="allSale" value="1" />
	<input type="hidden" id="token" name="token"  value="<?php echo uniqid(); ?>">

	<div class="modal fade" id="saleModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:500px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="title">เลือกพนักงานขาย</h4>
				</div>
				<div class="modal-body" style="padding:0px;">
					<?php if( ! empty($saleList)) : ?>
						<?php foreach($saleList as $rs) : ?>
							<div class="col-sm-12">
								<label>
									<input type="checkbox" class="ace sale-chk" name="sale[]" value="<?php echo $rs->id; ?>" style="margin-right:10px;" />
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
</div>
<hr>
</form>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="report-div">
		<table class="table table-bordered tableFixHead" style="min-width:2060px;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center fix-header">#</th>
					<th class="fix-width-100 text-center fix-header">วันที่เอกสาร</th>
					<th class="fix-width-120 text-center fix-header">วันที่บันทึกขาย</th>
					<th class="fix-width-200 text-center fix-header">พนักงานขาย</th>
					<th class="fix-width-80 text-center fix-header">เล่ม</th>
					<th class="fix-width-120 text-center fix-header">Invoice</th>
					<th class="fix-width-120 text-center fix-header">อ้างอิง</th>
					<th class="fix-width-120 text-center fix-header">ใบสั่งขาย</th>
					<th class="fix-width-80 text-center fix-header">เครดิต ?</th>
					<th class="fix-width-100 text-center fix-header">มูลค่า</th>
					<th class="fix-width-100 text-center fix-header">มัดจำ</th>
					<th class="fix-width-100 text-center fix-header">ชำระด้วยเงินสด</th>
					<th class="fix-width-100 text-center fix-header">ชำระด้วยเงินโอน</th>
					<th class="fix-width-100 text-center fix-header">ชำระด้วยบัตรเครดิต</th>
					<th class="fix-width-100 text-center fix-header">ชำระด้วยเช็ค</th>
					<th class="fix-width-100 text-center fix-header">รวมชำระ</th>
					<th class="fix-width-100 text-center fix-header">สลิปโอน</th>
					<th class="fix-width-150 text-center fix-header">มัดจำ + ชำระ + โอน</th>
					<th class="fix-width-100 text-center fix-header">คงค้าง</th>
					<th class="fix-width-100 text-center fix-header">ค่าขนส่ง</th>
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
				<td colspan="9" class="text-right">รวม</td>
				<td class="text-right">{{total_amount}}</td>
				<td class="text-right">{{total_down}}</td>
				<td class="text-right">{{total_cash}}</td>
				<td class="text-right">{{total_transfer}}</td>
				<td class="text-right">{{total_card}}</td>
				<td class="text-right">{{total_cheque}}</td>
				<td class="text-right">{{total_paid}}</td>
				<td class="text-right">{{total_slip}}</td>
				<td class="text-right">{{total_sales}}</td>
				<td class="text-right">{{total_outstanding}}</td>
				<td class="text-right">{{total_ship_cost}}</td>
			</tr>
		{{else}}
			<tr>
				<td class="text-center">{{no}}</td>
				<td>{{DocDate}}</td>
				<td>{{shipped_date}}</td>
				<td>{{sale_name}}</td>
				<td class="text-center">{{book}}</td>
				<td>{{code}}</td>
				<td>{{BaseRef}}</td>
				<td>{{so_code}}</td>
				<td class="text-center">{{isCredit}}</td>
				<td class="text-right">{{DocTotal}}</td>
				<td class="text-right">{{downPayment}}</td>
				<td class="text-right">{{cash}}</td>
				<td class="text-right">{{transfer}}</td>
				<td class="text-right">{{card}}</td>
				<td class="text-right">{{cheque}}</td>
				<td class="text-right">{{paidAmount}}</td>
				<td class="text-right">{{transferSlip}}</td>
				<td class="text-right">{{salesAmount}}</td>
				<td class="text-right">{{outstanding}}</td>
				<td class="text-right">{{shipCost}}</td>
			</tr>
		{{/if}}
	{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/report/sales/sales_by_document.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
