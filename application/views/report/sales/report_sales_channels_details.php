<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-lg-10 col-md-10 col-sm-10 padding-5 hidden-xs">
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
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ช่องทางการขาย</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-channels-all" onclick="toggleAllChannels(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-channels-range" onclick="toggleAllChannels(0)">เลือก</button>
    </div>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>สินค้า</label>
    <input type="text" class="form-control input-sm text-center" id="pdFrom" name="pdFrom" placeholder="เริ่มต้น">
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label class="display-block not-show">End</label>
    <input type="text" class="form-control input-sm text-center" id="pdTo" name="pdTo" placeholder="สิ้นสุด">
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label class="display-block">เลขที่อ้างอิง</label>
    <input type="text" class="form-control input-sm text-center" id="refCodeFrom" name="refCodeFrom" placeholder="เริ่มต้น">
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label class="display-block not-show">End</label>
    <input type="text" class="form-control input-sm text-center" id="refCodeTo" name="refCodeTo" placeholder="สิ้นสุด">
  </div>


  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" placeholder="เริ่มต้น" required />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" placeholder="สิ้นสุด" required/>
    </div>
  </div>

  <input type="hidden" id="allChannels" name="allChannels" value="1">
	<input type="hidden" id="token" name="token"  value="<?php echo uniqid(); ?>">
</div>


<div class="modal fade" id="channels-modal" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal' style="width:400px; max-width:95%;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='title' id='modal_title'>ระบุช่องทางการขาย</h4>
            </div>
            <div class='modal-body' id='modal_body' style="padding:0px;">
        <?php if(!empty($channels_list)) : ?>
          <?php foreach($channels_list as $rs) : ?>
            <div class="col-sm-12">
              <label>
                <input type="checkbox" class="chk" name="channels[]" value="<?php echo $rs->code; ?>" style="margin-right:10px;" />
                <span class="lbl">
                <?php echo $rs->name; ?>
                </span>
              </label>

            </div>
          <?php endforeach; ?>
        <?php endif;?>

        		<div class="divider" ></div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-block' data-dismiss='modal'>ตกลง</button>
            </div>
        </div>
    </div>
</div>
<hr>
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


<script src="<?php echo base_url(); ?>scripts/report/sales/sales_channels_details.js"></script>
<?php $this->load->view('include/footer'); ?>
