<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
  	<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-info" onclick="syncData()"><i class="fa fa-refresh"></i> Sync</button>
    </p>
  </div>
</div><!-- End Row -->
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Zone</label>
    <input type="text" class="form-control input-sm" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Customer</label>
    <input type="text" class="form-control input-sm" name="customer" id="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>User</label>
    <input type="text" class="form-control input-sm" name="uname" id="u-name" value="<?php echo $uname; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Warehouse</label>
    <select class="form-control input-sm filter" name="warehouse" id="warehouse" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_warehouse($warehouse); ?>
		</select>
  </div>

	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
    <label>Status</label>
    <select class="form-control input-sm filter" name="active" id="active" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
			<option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
		</select>
  </div>

  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
		<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
  </div>

	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
		<button type="button" class="btn btn-xs btn-purple btn-block" onclick="exportFilter()">
			<i class="fa fa-file-excel-o"></i> Export
		</button>
  </div>

</div>
<hr class="margin-top-15">
</form>
<form class="hidden" id="exportForm" method="post" action="<?php echo $this->home; ?>/export_filter">
	<input type="hidden" name="zone_code" id="export-code" >
	<input type="hidden" name="zone_uname" id="export-uname" >
	<input type="hidden" name="zone_customer" id="zone-customer">
	<input type="hidden" name="zone_warehouse" id="zone-warehouse">
	<input type="hidden" name="token" id="token" value="<?php echo date('YmdHis'); ?>">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:900px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">ลำดับ</th>
					<th class="fix-width-150 middle">รหัสโซน</th>
					<th class="min-width-200 middle">ชื่อโซน</th>
					<th class="fix-width-200 middle">คลังสินค้า</th>
					<th class="fix-width-100 middle">เจ้าของโซน</th>
					<th class="fix-width-60 middle text-center">สถานะ</th>
					<th class="fix-width-60 middle text-center">ลูกค้า</th>
					<th class="fix-width-100 middle text-center">รหัสเก่า</th>
					<th class="fix-width-100"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($list)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($list as $rs) : ?>
					<tr style="font-size:11px;" id="row-<?php echo $rs->code; ?>">
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->warehouse_name; ?></td>
						<td class="middle"><?php echo $rs->uname; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle text-center"><?php echo number($rs->customer_count); ?></td>
						<td class="middle text-center"><?php echo $rs->old_code; ?></td>
						<td class="text-right">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->code; ?>')" <?php echo ($rs->customer_count > 0 ? 'disabled' :''); ?>>
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="7" class="text-center">--- No zone ---</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/zone.js?v=<?php echo date('YmdHis'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
