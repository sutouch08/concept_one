<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <i class="fa fa-desktop"></i> <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>รหัส</label>
    <input type="text" class="form-control input-sm text-center search-box" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm text-center search-box" name="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>จุดขาย</label>
    <input type="text" class="form-control input-sm text-center search-box" name="shop" value="<?php echo $shop; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>POS NO.</label>
    <input type="text" class="form-control input-sm text-center search-box" name="pos_no" value="<?php echo $pos_no; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>สถานะ</label>
		<select class="form-control input-sm" name="status" id="status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $status); ?>>Active</option>
			<option value="0" <?php echo is_selected('0', $status); ?>>Inactive</option>
		</select>
  </div>

  <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
</form>
<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:1160px;">
			<thead>
				<tr>
					<th class="fix-width-120 middle"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-100 middle">รหัส</th>
					<th class="fix-width-200 middle">ชื่อ</th>
					<th class="fix-width-50 middle text-center">สถานะ</th>
					<th class="fix-width-100 middle text-center">Bill Prefix</th>
					<th class="fix-width-100 middle text-center">Return Prefix</th>
					<th class="fix-width-150 middle">POS NO</th>
					<th class="min-width-150 middle">จุดขาย</th>
					<th class="fix-width-150 middle">DeviceId</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($list)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($list as $rs) : ?>
					<tr id="row-<?php echo $no; ?>">
						<td class="middle">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->code; ?>', '<?php echo $rs->name; ?>', '<?php echo $no; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>							
						</td>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle text-center"><?php echo $rs->prefix; ?></td>
						<td class="middle text-center"><?php echo $rs->return_prefix; ?></td>
						<td class="middle"><?php echo $rs->pos_no; ?></td>
						<td class="middle"><?php echo $rs->shop_name; ?></td>
						<td class="middle"><?php echo $rs->deviceId; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/pos.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
