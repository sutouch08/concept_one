<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h4 class="title">
			<i class="fa fa-credit-card"></i> <?php echo $this->title; ?>
		</h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<?php if($this->pm->can_add) : ?>
				<button type="button" class="btn btn-xs btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
			<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->

<hr class="padding-5"/>

<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>รหัส/ชื่อ</label>
			<input type="text" class="form-control input-sm search" name="code" value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>สถานะ</label>
			<select class="form-control input-sm filter" name="active">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $active); ?>>Active</option>
				<option value="0" <?php echo is_selected('0', $active); ?>>Inactive</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
		</div>
	</div>
</form>
<hr class="margin-top-15 padding-5">

<?php echo $this->pagination->create_links(); ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="fix-width-50 middle text-center">#</th>
					<th class="fix-width-60 middle text-center"></th>
					<th class="fix-width-100 middle">รหัส</th>
					<th class="fix-width-250 middle">ชื่อ</th>
					<th class="fix-width-100 middle text-center">สถานะ</th>
					<th class="min-width-100"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment($this->segment) + 1; ?>

				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle text-center"><img src="<?php echo bankLogoUrl($rs->code); ?>" height="30px" width="30px" /></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->name; ?>')">
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


<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog" style="width:400px; max-width:90vw;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
				<h3 class="text-center" style="margin:0;">เพิ่มธนาคาร</h3>
			</div>
			<div class="modal-body">
        <div class="form-horizontal">
					<div class="form-group">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label no-padding-right">รหัสธนาคาร</label>
						<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
							<input type="text" class="form-control input-sm input-medium e" maxlength="20" id="add-bank-code" />
							<div class="help-block col-xs-12 col-sm-reset inline red" id="add-bank-code-error"></div>
						</div>
					</div>
				</div>
				<div class="form-horizontal">
					<div class="form-group">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label no-padding-right">ชื่อธนาคาร</label>
						<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
							<input type="text" class="form-control input-sm e" maxlength="100" id="add-bank-name" />
							<div class="help-block col-xs-12 col-sm-reset inline red" id="add-bank-name-error"></div>
						</div>
					</div>
				</div>
				<div class="form-horizontal">
					<div class="form-group">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label no-padding-right">Active</label>
						<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" style="padding-right:10px; padding-top:7px;">
							<label>
								<input type="checkbox" class="ace input-lg" id="add-bank-active" value="1" checked />
								<span class="lbl"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-xs btn-default btn-100" onclick="closeModal('addModal')">ยกเลิก</button>
        <button class="btn btn-xs btn-success btn-100" onclick="add()">เพิ่ม</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog" style="width:400px; max-width:90vw;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
				<h3 class="text-center" style="margin:0;">แก้ไขธนาคาร</h3>
			</div>
			<div class="modal-body">
        <div class="form-horizontal">
					<div class="form-group">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label no-padding-right">รหัสธนาคาร</label>
						<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
							<input type="text" class="form-control input-sm input-medium" maxlength="20" id="edit-bank-code" disabled/>
							<input type="hidden" id="edit-bank-id" />
							<div class="help-block col-xs-12 col-sm-reset inline red" id="edit-bank-code-error"></div>
						</div>
					</div>
				</div>
				<div class="form-horizontal">
					<div class="form-group">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label no-padding-right">ชื่อธนาคาร</label>
						<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
							<input type="text" class="form-control input-sm e" maxlength="100" id="edit-bank-name" />
							<div class="help-block col-xs-12 col-sm-reset inline red" id="edit-bank-name-error"></div>
						</div>
					</div>
				</div>
				<div class="form-horizontal">
					<div class="form-group">
						<label class="col-lg-3 col-md-3 col-sm-3 col-xs-3 control-label no-padding-right">Active</label>
						<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" style="padding-right:10px; padding-top:7px;">
							<label>
								<input type="checkbox" class="ace input-lg" id="edit-bank-active" value="1" checked />
								<span class="lbl"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-xs btn-default btn-100" onclick="closeModal('editModal')">ยกเลิก</button>
        <button class="btn btn-xs btn-success btn-100" onclick="update()">บันทึก</button>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/masters/bank_code.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
