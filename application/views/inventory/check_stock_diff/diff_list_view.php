<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goToAdjust()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="loadDiff()"><i class="fa fa-upload"></i> โหลดยอดต่าง</button>
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

  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> ค้นหา</button>
  </div>
	<div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearSearch()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<input type="hidden" name="ajdust_code" id="adjust_code" value="<?php echo $adjust_code; ?>" >
<hr class="margin-top-15">
</form>

<?php echo $this->pagination->create_links(); ?>

<form id="diffForm" method="post" action="<?php echo base_url(); ?>inventory/adjust/load_check_diff/<?php echo $adjust_code; ?>">
<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped border-1">
      <tr>
        <th class="width-5 text-center">ลำดับ</th>
				<th class="width-5 text-center">
					<input type="checkbox" class="ace" id="chk-all" onchange="toggleCheckAll($(this))">
					<label class="lbl"></label>
					</label>
				</th>
        <th class="width-25">สินค้า</th>
        <th class="width-15">รหัสโซน</th>
        <th class="width-30">ชื่อโซน</th>
        <th class="width-10 text-center">ยอดต่าง</th>
      </tr>
      <tbody>
    <?php if( !empty($data)) : ?>
    <?php $no = $this->uri->segment(5) + 1; ?>
    <?php foreach($data as $rs) : ?>
      <tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
        <td class="text-center no"><?php echo $no; ?></td>
				<td class="text-center">
					<input type="checkbox" class="ace chk" name="diff[<?php echo $rs->id; ?>]" id="chk-<?php echo $rs->id; ?>" value="<?php echo $rs->id; ?>">
					<label class="lbl"></label>
				</td>
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
      </tr>
    <?php  $no++; ?>
    <?php endforeach; ?>
    <?php else : ?>
      <tr>
        <td colspan="6" class="text-center">--- ไม่พบข้อมูล ---</td>
      </tr>
    <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</form>
<script src="<?php echo base_url(); ?>scripts/inventory/check_stock_diff/check_stock_diff.js?<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
