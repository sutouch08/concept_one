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
			<button type="button" class="btn btn-sm btn-info" onclick="syncData()"><i class="fa fa-refresh"></i> Sync</button>
			<button type="button" class="btn btn-sm btn-info" onclick="syncAllData()"><i class="fa fa-refresh"></i> Sync all</button>
			<button type="button" class="btn btn-sm btn-purple" onclick="exportFilter()"><i class="fa fa-file-excel-o"></i> Export</button>
    </p>
  </div>
</div><!-- End Row -->
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>รหัส</label>
    <input type="text" class="form-control input-sm" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
    <label>ประเภท</label>
    <select class="form-control input-sm filter" name="role" id="role" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_warehouse_role($role); ?>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>คลังเทียม</label>
    <select class="form-control input-sm filter" name="is_consignment" id="is_consignment" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $is_consignment); ?>>YES</option>
			<option value="0" <?php echo is_selected('0', $is_consignment); ?>>NO</option>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm filter" name="active" id="active" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
			<option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>ขาย</label>
    <select class="form-control input-sm filter" name="sell" id="sell" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $sell); ?>>YES</option>
			<option value="0" <?php echo is_selected('0', $sell); ?>>NO</option>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>จัด</label>
    <select class="form-control input-sm filter" name="prepare" id="prepare" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $prepare); ?>>YES</option>
			<option value="0" <?php echo is_selected('0', $prepare); ?>>NO</option>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>ติดลบ</label>
    <select class="form-control input-sm filter" name="auz" id="auz" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $auz); ?>>YES</option>
			<option value="0" <?php echo is_selected('0', $auz); ?>>NO</option>
		</select>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
		<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>

</div>
</form>
<hr class="padding-5 margin-top-15">
<form class="hidden" id="exportForm" method="post" action="<?php echo $this->home; ?>/export_filter">
	<input type="hidden" name="whCode" id="export-code" >
	<input type="hidden" name="whName" id="export-name" >
	<input type="hidden" name="whRole" id="export-role">
	<input type="hidden" name="whIsConsignment" id="export-is-consignment">
	<input type="hidden" name="whSell" id="export-sell">
	<input type="hidden" name="whPrepare" id="export-prepare">
	<input type="hidden" name="whActive" id="export-active">
	<input type="hidden" name="whAuz" id="export-auz">
	<input type="hidden" name="token" id="token" value="<?php echo genUid(); ?>">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-hover border-1" style="min-width:1000px;">
			<thead>
				<tr style="font-size:11px;">
					<th class="fix-width-40 middle text-center">ลำดับ</th>
					<th class="fix-width-100 middle">รหัสคลัง</th>
					<th class="min-width-250 middle">ชื่อคลัง</th>
					<th class="fix-width-100 middle">ประเภทคลัง</th>
					<th class="fix-width-80 middle text-center">โซน</th>
					<th class="fix-width-60 middle text-center">ขาย</th>
					<th class="fix-width-60 middle text-center">จัด</th>
					<th class="fix-width-60 middle text-center">ติดลบ</th>
					<th class="fix-width-60 middle text-center">ใช้งาน</th>
					<th class="fix-width-60 middle text-center">ฝาขายเทียม</th>
					<th class="fix-width-120 middle text-right">มูลค่าสูงสุด</th>
					<th class="fix-width-100 middle text-center">แก้ไข</th>
					<th class="fix-width-100 middle"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($list)) : ?>
				<?php $no = $this->uri->segment($this->segment) + 1; ?>
				<?php foreach($list as $rs) : ?>
					<tr style="font-size:11px;" id="row-<?php echo $rs->code; ?>">
						<td class="middle text-center no"><?php echo $no; ?></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle"><?php echo $rs->role_name; ?></td>
						<td class="middle text-center"><?php echo number($rs->zone_count); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->sell); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->prepare); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->auz); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle text-center"><?php echo is_active($rs->is_consignment); ?></td>
						<td class="middle text-right"><?php echo number($rs->limit_amount, 2); ?></td>
						<td class="middle text-center"><?php echo $rs->update_user; ?></td>
						<td class="text-right">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit('<?php echo $rs->code; ?>')">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete('<?php echo $rs->code; ?>')" <?php echo ($rs->zone_count > 0 ? 'disabled' :''); ?>>
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

<script src="<?php echo base_url(); ?>scripts/masters/warehouse.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
