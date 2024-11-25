<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 padding-5">
    <h3 class="title">
      <i class="fa fa-users"></i> <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6 padding-5">
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
	<div class="col-sm-2 padding-5">
		<label>รหัส</label>
		<input type="text" class="form-control input-sm search-box" name="code" value="<?php echo $code; ?>" />
	</div>
  <div class="col-sm-2 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm search-box" name="name" value="<?php echo $name; ?>" />
  </div>
  <div class="col-sm-2 padding-5">
    <label>เบอร์โทร</label>
    <input type="text" class="form-control input-sm search-box" name="phone" value="<?php echo $phone; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="status" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="1" <?php echo is_selected('1', $status); ?>>ใช้งาน</option>
      <option value="0" <?php echo is_selected('0', $status); ?>>ไม่ใช้งาน</option>
    </select>
  </div>

  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-sm-12 padding-5">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="width-15 middle">รหัส</th>
					<th class="width-15 middle">ชื่อ</th>
          <th class="width-40 middle">ที่อยู่</th>
          <th class="width-10 middle">เบอร์โทร</th>
					<th class="width-5 middle">สถานะ</th>
          <th class="middle"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="row-<?php echo $rs->id; ?>">
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->address1.' '.$rs->address2; ?></td>
            <td class="middle"><?php echo $rs->phone; ?></td>
            <td class="middle"><?php echo is_active($rs->status); ?></td>
            <td class="middle text-right">
              <?php if($this->pm->can_edit OR $this->pm->can_add): ?>
              <button type="button" class="btn btn-minier btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
              <?php endif; ?>
              <?php if($this->pm->can_delete): ?>
              <button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->name; ?>')"><i class="fa fa-trash"></i></button>
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

<script src="<?php echo base_url(); ?>scripts/masters/supplier.js"></script>

<?php $this->load->view('include/footer'); ?>
