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
	<div class="col-sm-2 col-xs-6 padding-5">
    <label>รหัส</label>
    <input type="text" class="form-control input-sm text-center search-box" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-2 col-xs-6 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm text-center search-box" name="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-sm-2 col-xs-6 padding-5">
    <label>โซน</label>
    <input type="text" class="form-control input-sm text-center search-box" name="zone" value="<?php echo $zone; ?>" />
  </div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    <label>สถานะ</label>
		<select class="form-control input-sm" name="status" id="status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $status); ?>>ใช้งาน</option>
			<option value="0" <?php echo is_selected('0', $status); ?>>ไม่ใช้งาน</option>
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
	<div class="col-sm-12 padding-5">
		<table class="table table-striped table-bordered table-hover" style="min-width:990px;">
			<thead>
				<tr>
					<th class="fix-width-120 middle"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-60 middle text-center">สถานะ</th>
					<th class="fix-width-100 middle">รหัส</th>
					<th class="fix-width-200 middle">ชื่อ</th>
					<th class="fix-width-200 middle">โซน</th>
					<th class="min-width-200 middle">ลูกค้า</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($list)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($list as $rs) : ?>
					<tr id="row-<?php echo $no; ?>">
						<td class="middle">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->name; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
							<button type="button" class="btn btn-mini btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>
						</td>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->zone_name; ?></td>
						<td class="middle"><?php echo $rs->customer_code; ?> : <?php echo $rs->customer_name; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/shop.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
