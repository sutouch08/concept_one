<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h3 class="title">
      <i class="fa fa-lock"></i> <?php echo $this->title; ?>
    </h3>
  </div>
</div><!-- End Row -->
<hr class="padding-5">
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-5 padding-5">
    <label>Profile Name</label>
    <input type="text" class="width-100" name="name" id="name" value="<?php echo $name; ?>" />
  </div>
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-7 padding-5">
		<label>Menu</label>
		<select class="width-100" id="menu-x" name="menu">
			<option value="all">ทั้งหมด</option>
			<?php $groups = $this->menu->get_active_menu_groups();		?>
			<?php 	if(!empty($groups)) : ?>
			<?php			 foreach($groups as $group) : ?>
			<?php 			if($group->pm == 1) : ?>
			<?php 			$menu_list = $this->menu->get_valid_menus_by_group($group->code); ?>
			<?php 			if(!empty($menu_list)) : ?>
			<?php 			foreach($menu_list as $rs) : ?>
			<?php echo '<option value="'.$rs->code.'" '.is_selected($rs->code, $menu).'>'.$rs->name.'</option>'; ?>
			<?php 			endforeach; ?>
			<?php 			endif; ?>
			<?php 		endif; ?>
			<?php 	endforeach; ?>
			<?php endif;?>
		</select>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>Permission</label>
		<select class="form-control input-sm" name="permission" id="permission">
			<option value="all">ทั้งหมด</option>
			<option value="view" <?php echo is_selected('view', $permission); ?>>ดู</option>
			<option value="add" <?php echo is_selected('add', $permission); ?>>เพิ่ม</option>
			<option value="edit" <?php echo is_selected('edit', $permission); ?>>แก้ไข</option>
			<option value="delete" <?php echo is_selected('delete', $permission); ?>>ลบ</option>
			<option value="approve" <?php echo is_selected('approve', $permission); ?>>อนุมัติ</option>
		</select>
	</div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">

</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="width-5 middle text-center">ลำดับ</th>
					<th class="">ชื่อ</th>
					<th class="width-10 text-center">สมาชิก</th>
					<th class="width-15"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle text-center"><?php echo number($rs->member); ?></td>
						<td class="text-right">
							<?php if($this->pm->can_edit && $rs->id > 0) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)">
									<i class="fa fa-lock"></i> กำหนดสิทธิ์
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


<script src="<?php echo base_url(); ?>scripts/users/permission.js?v=<?php echo date('Ymd'); ?>"></script>

<script>
	$('#menu-x').select2();
</script>

<?php $this->load->view('include/footer'); ?>
