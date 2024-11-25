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
			<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
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
</div>

<input type="hidden" id="token" name="token"  value="<?php echo uniqid(); ?>">

<hr class="margin-top-15 margin-bottom-15">
</form>

<div class="row">
  <div class="col-sm-12" id="result">
    <blockquote>
      <p class="lead" style="color:#CCC;">
        รายงานจะไม่แสดงข้อมูลการจัดส่งทางหน้าจอ เนื่องจากข้อมูลมีจำนวนคอลัมภ์ที่ยาวเกินกว่าที่จะแสดงผลทางหน้าจอได้ทั้งหมด หากต้องการข้อมูลทั้งหมดให้ export ข้อมูลเป็นไฟล์ Excel แทน
      </p>
    </blockquote>
  </div>
</div>


<script src="<?php echo base_url(); ?>scripts/report/sales/sales_analyze.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
