<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="goToCheck()"><i class="fa fa-tags"></i> นับสต็อก(คีย์มือ)</button>
				<button type="button" class="btn btn-sm btn-primary" onclick="goToCheck('barcode')"><i class="fa fa-tags"></i> นับสต็อก(บาร์โค้ด)</button>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>

<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm search" id="product_code" name="product_code"  value="<?php echo $product_code; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>โซน</label>
    <input type="text" class="form-control input-sm search" id="zone_code" name="zone_code" value="<?php echo $zone_code; ?>" />
  </div>
	<div class="col-sm-2 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" id="status" name="status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $status); ?>>ยังไม่โหลด</option>
			<option value="1" <?php echo is_selected('1', $status); ?>>โหลดแล้ว</option>
			<option value="2" <?php echo is_selected('2', $status); ?>>ปรับยอดแล้ว</option>
		</select>
  </div>
  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> ค้นหา</button>
  </div>
	<div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
	<!--
  <div class="col-sm-1 col-1-harf padding-5 last">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-purple btn-block" onclick="doExport()"><i class="fa fa-file-excel-o"></i> Download</button>
  </div> -->
</div>
<hr class="margin-top-15">
</form>
<form id="exportFrom" method="post" action="<?php echo $this->home; ?>/export">
  <input type="hidden" id="product" name="product">
  <input type="hidden" id="zone" name="zone">
	<input type="hidden" id="status" name="status">
  <input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-sm-12 col-xs-12 last">
    <p class="pull-right top-p">
      <span>O</span><span class="margin-right-15"> = ยังไม่ถูกโหลด</span>
      <span class="blue">L</span><span class="margin-right-15"> = โหลดเข้าเอกสารแล้ว</span>
      <span class="green">S</span><span class=""> = ยอดต่างถูกปรับยอดแล้ว</span>
    </p>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped border-1">
      <tr>
        <th class="width-5 text-center">ลำดับ</th>
        <th class="width-25">สินค้า</th>
        <th class="width-15">รหัสโซน</th>
        <th class="width-30">ชื่อโซน</th>
        <th class="width-10 text-center">ยอดต่าง</th>
				<th class="width-5 text-center">สถานะ</th>
				<th class="width-5 text-center"></th>
      </tr>
      <tbody>
    <?php if( !empty($data)) : ?>
    <?php $no = $this->uri->segment(4) + 1; ?>
    <?php foreach($data as $rs) : ?>
      <tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
        <td class="text-center no"><?php echo $no; ?></td>
        <td>
          <?php echo $rs->product_code; ?>
          <?php
            if(!empty($rs->old_code))
            {
              echo " | ".$rs->old_code;
            }
            ?>
        </td>
        <td><?php echo $rs->zone_code; ?></td>
        <td><?php echo $rs->zone_name; ?></td>
    		<td class="text-center"><?php echo number($rs->qty); ?></td>
				<td class="text-center">
					<?php if($rs->status == 1) : ?>
						<span class="blue">L</span>
					<?php elseif($rs->status == 2) : ?>
						<span class="green">S</span>
					<?php else : ?>
						<span>O</span>
					<?php endif; ?>
				</td>
				<td class="text-right">
					<?php if($rs->status == 0 && ($this->pm->can_edit OR $this->pm->can_add)) : ?>
						<button type="button" class="btn btn-minier btn-danger" onclick="removeDiff(<?php echo $rs->id; ?>)"><i class="fa fa-trash"></i></button>
					<?php endif; ?>
				</td>
      </tr>
    <?php  $no++; ?>
    <?php endforeach; ?>
    <?php else : ?>
      <tr>
        <td colspan="7" class="text-center">--- ไม่พบข้อมูล ---</td>
      </tr>
    <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/check_stock_diff/check_stock_diff.js?<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
