<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  	<p class="pull-right top-p">
    <?php if($this->pm->can_add) : ?>
      <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> Add New</button>
    <?php endif; ?>
    </p>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Code</label>
    <input type="text" class="form-control input-sm" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Name</label>
    <input type="text" class="form-control input-sm" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Group</label>
		<select class="form-control input-sm" id="color_group" name="color_group" onchange="getSearch()">
			<option value="all">All</option>
			<option value="NULL" <?php echo is_selected("NULL", $color_group); ?>>No Group</option>
			<?php echo select_color_group($color_group); ?>
		</select>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Active</label>
    <select class="form-control input-sm" name="status" id="status" onchange="getSearch()">
			<option value="all">All</option>
			<option value="1" <?php echo is_selected('1', $status); ?>>Yes</option>
			<option value="0" <?php echo is_selected('0', $status); ?>>No</option>
		</select>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
<input type="hidden" name="search" value="1" />
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-100 middle">Code</th>
					<th class="fix-width-200 middle">Description</th>
					<th class="fix-width-120 middle">Group</th>
					<th class="fix-width-100 middle text-center">Items</th>
					<th class="fix-width-50 middle text-center">Active</th>
					<th class="min-width-100"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->group_name; ?></td>
						<td class="middle text-center"><?php echo number($rs->member); ?></td>
						<td class="middle text-center" id="<?php echo $rs->id; ?>">
							<?php if($this->pm->can_edit): ?>
								<span class="pointer" onclick="toggleActive(<?php echo $rs->active; ?>, '<?php echo $rs->id; ?>')">
									<?php echo is_active($rs->active); ?>
								</span>
							<?php else : ?>
								<?php echo is_active($rs->active); ?>
							<?php endif; ?>
						</td>
						<td class="text-right">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit('<?php echo $rs->id; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->id; ?>', '<?php echo $rs->name; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/product_color.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
