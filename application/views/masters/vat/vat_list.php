<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <i class="fa fa-bolt"></i> <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 col-xs-4 padding-5">
    <label for="code">รหัส</label>
    <input type="text" class="form-control input-sm search-box" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-sm-2 col-xs-4 padding-5">
    <label for="name">ชื่อ</label>
    <input type="text" class="form-control input-sm search-box" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-sm-2">
		<label for="active">สถานะ</label>
		<select class="form-control input-sm" id="active" name="active" onchange="getSearch()">
			<option value="all" <?php echo is_selected('all', $active); ?>>ทั้งหมด</option>
			<option value="1" <?php echo is_selected("1", $active); ?>>ใช้งาน</option>
			<option value="0" <?php echo is_selected("0", $active); ?>>ไม่ใช้งาน</option>
		</select>
	</div>

  <div class="col-sm-1 col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1">
			<thead>
				<tr>
					<th class="fix-width-100 middle text-center">ใช้งาน</th>
					<th class="fix-width-40 middle text-center">ลำดับ</th>
					<th class="fix-width-100 middle">รหัส</th>
					<th class="fix-width-200 middle">ชื่อ</th>
					<th class="fix-width-100 middle">ภาษี(%)</th>

					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->code; ?>', '<?php echo $rs->name; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo number($rs->rate,2); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>

					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/vat.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
