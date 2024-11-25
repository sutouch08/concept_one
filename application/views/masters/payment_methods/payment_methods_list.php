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
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>รหัส</label>
			<input type="text" class="form-control input-sm search" name="code" id="code" value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ชื่อ</label>
			<input type="text" class="form-control input-sm search" name="name" id="name" value="<?php echo $name; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ประเภท</label>
			<select class="form-control input-sm filter" name="role" id="role">
				<option value="all">ทั้งหมด</option>
				<?php echo select_payment_role($role); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>เครดิต</label>
			<select class="form-control input-sm filter" name="has_term" id="has_term">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $has_term); ?>>เครดิต</option>
				<option value="0" <?php echo is_selected('0', $has_term); ?>>ไม่ใช่เครดิต</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-bordered table-hover" style="min-width:860px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">ลำดับ</th>
					<th class="fix-width-150 middle">รหัส</th>
					<th class="fix-width-200 middle">ชื่อ</th>
					<th class="fix-width-120 middle">ประเภท</th>
					<th class="fix-width-100 middle text-center">เครติด</th>
          <th class="fix-width-150 middle">ปรับปรุงล่าสุด</th>
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
						<td class="middle"><?php echo $rs->role_name; ?></td>
						<td class="middle text-center">
							<?php if($rs->has_term) : ?>
								<i class="fa fa-check green"></i>
							<?php endif; ?>
						</td>
            <td class="middle"><?php echo thai_date($rs->date_upd,TRUE, '/'); ?></td>
						<td class="">
						<?php if($rs->id > 0) : ?>
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

<script src="<?php echo base_url(); ?>scripts/masters/payment_methods.js"></script>

<?php $this->load->view('include/footer'); ?>
