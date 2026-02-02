<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-info top-btn" onclick="syncData()"><i class="fa fa-refresh"></i> Sync</button>
  </div>
</div><!-- End Row -->
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>Zone</label>
    <input type="text" class="form-control input-sm" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>Customer</label>
    <input type="text" class="form-control input-sm" name="customer" id="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-4 col-md-6 col-sm-4-harf col-xs-6 padding-5">
    <label>Warehouse</label>
    <select class="width-100 filter" name="warehouse" id="warehouse" onchange="getSearch()">
			<option value="">ทั้งหมด</option>
			<?php echo select_warehouse($warehouse); ?>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>Pickface</label>
    <select class="form-control input-sm filter" name="is_pickface" id="is_pickface">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $is_pickface); ?>>Yes</option>
			<option value="0" <?php echo is_selected('0', $is_pickface); ?>>No</option>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>Status</label>
    <select class="form-control input-sm filter" name="active" id="active" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
			<option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
		</select>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
		<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>

<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:1440px;">
			<thead>
				<tr>
					<th class="fix-width-60 middle text-center">
						<label>
							<input type="checkbox" id="chk-all" class="ace" onchange="toggleCheckAll()"/>
							<span class="lbl"></span>
						</label>
					</th>
					<th class="fix-width-80 middle"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-200 middle">Code</th>
					<th class="min-width-200 middle">Name</th>
					<th class="fix-width-300 middle">Warehosue</th>
					<th class="fix-width-80 middle">Pickface</th>
					<th class="fix-width-80 middle text-center">Status</th>
					<th class="fix-width-80 middle text-center">Customer</th>
					<th class="fix-width-200 middle text-center">Old code</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($list)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($list as $rs) : ?>
					<tr style="font-size:11px;" id="row-<?php echo $rs->code; ?>">
						<td class="middle text-center">
							<label>
								<input type="checkbox" class="ace chk"
								value="<?php echo $rs->code; ?>"
								data-code="<?php echo $rs->code; ?>"
								data-name="<?php echo $rs->name; ?>" />
								<span class="lbl"></span>
							</label>
						</td>
						<td class="middle">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-minier btn-warning" onclick="getEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getDelete('<?php echo $rs->code; ?>')" <?php echo ($rs->customer_count > 0 ? 'disabled' :''); ?>>
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->warehouse_name; ?></td>						
						<td class="middle text-center">
							<?php if($this->_SuperAdmin) : ?>
								<span class="pointer" id="pickface-label-<?php echo $rs->id; ?>" onclick="togglePickface(<?php echo $rs->id; ?>)">
									<?php echo $rs->is_pickface ? 'Yes' : 'No'; ?>
								</span>
								<input type="hidden" id="is-pickface-<?php echo $rs->id; ?>" value="<?php echo $rs->is_pickface; ?>" />
							<?php else : ?>
								<?php echo $rs->is_pickface ? 'Yes' : 'No'; ?>
							<?php endif; ?>
						</td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle text-center"><?php echo number($rs->customer_count); ?></td>
						<td class="middle text-center"><?php echo $rs->old_code; ?></td>

					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="8" class="text-center">--- No zone ---</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


<script>
	$('#user_id').select2();
	$('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/masters/zone.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
