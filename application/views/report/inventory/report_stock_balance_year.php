<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-sm-6">
    <h3 class="title">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
    </div>
		<div class="col-sm-6">
			<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
				<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
				<button type="button" class="btn btn-sm btn-info" onclick="print()"><i class="fa fa-print"></i> พิมพ์</button>
			</p>
		</div>
</div><!-- End Row -->
<hr class="hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label class="display-block">สินค้า</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-pd-all" onclick="toggleAllProduct(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-pd-range" onclick="toggleAllProduct(0)">เลือก</button>
    </div>
  </div>
  <div class="col-sm-2 padding-5">
    <label class="display-block not-show">start</label>
    <input type="text" class="form-control input-sm text-center" id="pdFrom" name="pdFrom" disabled>
  </div>
  <div class="col-sm-2 padding-5">
    <label class="display-block not-show">End</label>
    <input type="text" class="form-control input-sm text-center" id="pdTo" name="pdTo" disabled>
  </div>
  <div class="col-sm-2 padding-5">
    <label class="display-block">คลัง</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-wh-all" onclick="toggleAllWarehouse(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-wh-range" onclick="toggleAllWarehouse(0)">เลือก</button>
    </div>
  </div>

  <input type="hidden" id="allProduct" name="allProduct" value="1">
  <input type="hidden" id="allWarehouse" name="allWhouse" value="1">
</div>


<div class="modal fade" id="wh-modal" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal' style="width:500px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='title' id='modal_title'>เลือกคลัง</h4>
            </div>
            <div class='modal-body' id='modal_body' style="padding:0px;">
        <?php if(!empty($whList)) : ?>
          <?php foreach($whList as $rs) : ?>
            <div class="col-sm-12">
              <label>
                <input type="checkbox" class="chk" id="<?php echo $rs->code; ?>" name="warehouse[]" value="<?php echo $rs->code; ?>" style="margin-right:10px;" />
                <?php echo $rs->code; ?> | <?php echo $rs->name; ?>
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
	<div class="col-sm-12" id="rs">
		<blockquote>
      <p class="lead" style="color:#CCC;">
        ผลลัพธ์ที่เกิน 2,000 รายการจะไม่แสดงบนหน้าจอ กรุณาใช้การส่งออกแทน
      </p>
    </blockquote>
	</div>
</div>

<?php
$Years = array();
$fYear = getConfig('START_YEAR');
$cYear = date('Y');

while($fYear <= $cYear)
{
  $Years[] = $fYear;
  $fYear++;
}

$Years[] = '0000';

?>

<script id="template" type="text/x-handlebarsTemplate">
<table class="table table-striped table-bordered">
  <thead>
    <tr>
      <th class="width-5 text-center">ลำดับ</th>
      <th class="text-center">รหัสสินค้า</th>
      <th class="text-center">ชื่อสินค้า</th>
<?php foreach($Years as $year) : ?>
      <th class="width-5 text-center"><?php echo $year; ?></th>
<?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
  {{#each this}}
    {{#if @last}}
    <tr>
      <td colspan="3" class="middle text-right">รวม</td>
<?php foreach($Years as $year) : ?>
      <td class="middle text-center">{{<?php echo $year; ?>_sum}}</td>
<?php endforeach; ?>
    </tr>
    {{else}}
    <tr class="font-size-10">
      <th class="middle text-center">{{no}}</th>
      <th class="middle">{{pdCode}}</th>
      <th class="middle">{{pdName}}</th>
<?php foreach($Years as $year) : ?>
      <th class="middle text-center">{{<?php echo $year; ?>_qty}}</th>
<?php endforeach; ?>
    </tr>
    {{/if}}
  {{/each}}
  </tbody>
</table>
</script>


<script src="<?php echo base_url(); ?>scripts/report/inventory/stock_balance_year.js"></script>
<?php $this->load->view('include/footer'); ?>
