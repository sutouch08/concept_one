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
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
        <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search-box" name="name" value="<?php echo $name; ?>" />
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ขนส่ง</label>
    <input type="text" class="form-control input-sm search-box" name="sender" value="<?php echo $sender; ?>" />
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
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-bordered table-hover" style="min-width:840px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">ลำดับ</th>
					<th class="fix-width-200 middle">ลูกค้า</th>
          <th class="fix-width-200 middle">ขนส่งหลัก</th>
          <th class="fix-width-150 middle">สำรอง 1</th>
          <th class="fix-width-150 middle">สำรอง 2</th>
          <th class="min-width-100 middle"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->customer_name; ?></td>
						<td class="middle"><?php echo $rs->main_sender; ?></td>
            <td class="middle"><?php echo $rs->second_sender; ?></td>
            <td class="middle"><?php echo $rs->third_sender; ?></td>
            <td class="middle">
              <?php if($this->pm->can_edit OR $this->pm->can_add): ?>
              <button type="button" class="btn btn-minier btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
              <?php endif; ?>
              <?php if($this->pm->can_delete): ?>
              <button type="button" class="btn btn-minier btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->customer_name; ?>')"><i class="fa fa-trash"></i></button>
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

<script src="<?php echo base_url(); ?>scripts/masters/transport.js"></script>

<?php $this->load->view('include/footer'); ?>
