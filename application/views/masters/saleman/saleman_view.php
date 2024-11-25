<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <i class="fa fa-users"></i> <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right">
        <button type="button" class="btn btn-sm btn-info" onclick="syncData()"><i class="fa fa-refresh"></i> Sync</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="title-block"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm search-box" name="name" value="<?php echo $name; ?>" />
  </div>

  <div class="col-sm-2">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="active" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="1" <?php echo is_selected('1', $active); ?>>ใช้งาน</option>
      <option value="0" <?php echo is_selected('0', $active); ?>>ไม่ใช้งาน</option>
    </select>
  </div>

  <div class="col-sm-2">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-2">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-bordered" style="min-width:550px;">
			<thead>
				<tr>
					<th class="fix-width-60 text-center"></th>
					<th class="fix-width-40 middle text-center">ลำดับ</th>
					<th class="fix-width-300 middle">พนักงานขาย</th>
					<th class="fix-width-200 middle">เบอร์โทร</th>
					<th class="min-width-100 middle">Status</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center">
						<?php if($this->pm->can_edit) : ?>
							<button type="button" class="btn btn-sm btn-warning" onclick="toggleEdit(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
						<?php endif; ?>
						</td>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle">
							<span id="phone-label-<?php echo $rs->id; ?>"><?php echo $rs->phone; ?></span>
							<input type="text" class="form-control input-sm ph hide" data-id="<?php echo $rs->id; ?>" id="phone-<?php echo $rs->id; ?>" value="<?php echo $rs->phone; ?>" />
						</td>
						<td class="middle">
							<?php echo is_active($rs->active); ?>
						</td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


<script src="<?php echo base_url(); ?>scripts/masters/saleman.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
