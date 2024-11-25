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
      <button type="button" class="btn btn-sm btn-success btn-top" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
    <?php endif; ?>
		<button type="button" class="btn btn-sm btn-info btn-top" onclick="export_filter()"><i class="fa fa-file-excel-o"></i> Export</button>
    </p>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>รหัส</label>
    <input type="text" class="width-100" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="width-100" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>กลุ่ม</label>
    <select class="form-control" name="group" id="group" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="NULL" <?php echo is_selected('NULL', $group); ?>>ไม่ระบุ</option>
			<?php echo select_product_group($group); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>กลุ่มหลัก</label>
		<select class="form-control" name="main_group" id="main_group" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="NULL" <?php echo is_selected('NULL', $main_group); ?>>ไม่ระบุ</option>
			<?php echo select_product_main_group($main_group); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เพศ</label>
		<select class="form-control" name="sub_group" id="sub_group" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="NULL" <?php echo is_selected('NULL', $sub_group); ?>>ไม่ระบุ</option>
			<?php echo select_product_sub_group($sub_group); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>หมวดหมู่</label>
		<select class="form-control" name="category" id="category" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="NULL" <?php echo is_selected('NULL', $category); ?>>ไม่ระบุ</option>
			<?php echo select_product_category($category); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ประเภท</label>
		<select class="form-control" name="kind" id="kind" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="NULL" <?php echo is_selected('NULL', $kind); ?>>ไม่ระบุ</option>
			<?php echo select_product_kind($kind); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ชนิด</label>
		<select class="form-control" name="type" id="type" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="NULL" <?php echo is_selected('NULL', $type); ?>>ไม่ระบุ</option>
			<?php echo select_product_type($type); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ยี่ห้อ</label>
		<select class="form-control" name="brand" id="brand" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="NULL" <?php echo is_selected('NULL', $brand); ?>>ไม่ระบุ</option>
			<?php echo select_product_brand($brand); ?>
		</select>
  </div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ปี</label>
		<select class="form-control" name="year" id="year" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0000" <?php echo is_selected('0000', $year); ?>>0000</option>
			<?php echo select_years($year); ?>
		</select>
  </div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 padding-5">
		<label>ขาย</label>
		<select class="form-control" name="sell" id="sell" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $sell); ?>>Yes</option>
			<option value="0" <?php echo is_selected('0', $sell); ?>>No</option>
		</select>
	</div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 padding-5">
		<label>Active</label>
		<select class="form-control" name="active" id="active" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $active); ?>>Yes</option>
			<option value="0" <?php echo is_selected('0', $active); ?>>No</option>
		</select>
	</div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-sm btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
	<input type="hidden" name="search" value="1" />
</div>
</form>
<hr class="margin-top-15">
<form id="export_filter_form" action="<?php echo $this->home; ?>/export_filter" method="post">
	<input type="hidden" name="export_code" id="export_code">
	<input type="hidden" name="export_name" id="export_name">
	<input type="hidden" name="export_group" id="export_group">
	<input type="hidden" name="export_main_group" id="export_main_group">
	<input type="hidden" name="export_sub_group" id="export_sub_group">
	<input type="hidden" name="export_category" id="export_category">
	<input type="hidden" name="export_kind" id="export_kind">
	<input type="hidden" name="export_type" id="export_type">
	<input type="hidden" name="export_brand" id="export_brand">
	<input type="hidden" name="export_year" id="export_year">
	<input type="hidden" name="export_sell" id="export_sell">
	<input type="hidden" name="export_active" id="export_active">
	<input type="hidden" name="token" id="token">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-bordered table-hover" style="min-width:1750px;">
			<thead>
				<tr>
					<th class="fix-width-120"></th>
					<th class="fix-width-50 middle text-center">#</th>
					<th class="fix-width-60 middle text-center">รูปภาพ</th>
					<th class="min-width-200 middle text-center">รหัส</th>
					<th class="fix-width-80 middle text-center">ราคา</th>
					<th class="fix-width-150 middle text-center">กลุ่ม</th>
					<th class="fix-width-150 middle text-center">กลุ่มหลัก</th>
					<th class="fix-width-150 middle text-center">กลุ่มย่อย</th>
					<th class="fix-width-150 middle text-center">หมวดหมู่</th>
					<th class="fix-width-150 middle text-center">ประเภท</th>
					<th class="fix-width-150 middle text-center">ชนิด</th>
					<th class="fix-width-150 middle text-center">ยี่ห้อ</th>
					<th class="fix-width-80 middle text-center">ปี</th>
					<th class="fix-width-50 middle text-center">ขาย</th>
					<th class="fix-width-60 middle text-center">Active</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="row-<?php echo $rs->code; ?>">
						<td class="middle">
							<button type="button" class="btn btn-mini btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)">
								<i class="fa fa-eye"></i>
							</button>
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->code; ?>', '<?php echo $rs->name; ?>', <?php echo $rs->id; ?>)">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="middle text-center"><?php echo number($no); ?></td>
						<td class="middle text-center">
							<img src="<?php echo get_cover_image($rs->code, 'mini'); ?>" width="50" />
						</td>
						<td class="middle">	<?php echo $rs->code; ?></td>
						<td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
						<td class="middle text-center"><?php echo $rs->group; ?></td>
						<td class="middle text-center"><?php echo $rs->main_group; ?></td>
						<td class="middle text-center"><?php echo $rs->sub_group; ?></td>
						<td class="middle text-center"><?php echo $rs->category; ?></td>
						<td class="middle text-center"><?php echo $rs->kind; ?></td>
						<td class="middle text-center"><?php echo $rs->type; ?></td>
						<td class="middle text-center"><?php echo $rs->brand; ?></td>
						<td class="middle text-center"><?php echo $rs->year; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->sell); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/products.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
