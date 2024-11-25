<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<?php if($this->pm->can_add) : ?>
				<button type="button" class="btn btn-xs btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
			<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ธนาคาร</label>
			<select class="form-control input-sm" name="bank_code" id="bank_code" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_bank($bank_code); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ชื่อบัญชี</label>
			<input type="text" class="form-control input-sm text-center search" name="account_name" value="<?php echo $account_name; ?>" autofocus />
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>เลขที่บัญชี</label>
			<input type="text" class="form-control input-sm text-center search" name="account_no" value="<?php echo $account_no; ?>" />
		</div>


		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>สาขา</label>
			<input type="text" class="form-control input-sm text-center search" name="branch" value="<?php echo $branch; ?>" />
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
		<table class="table table-striped border-1" style="min-width:1000px;">
			<thead>
				<tr>
					<th class="fix-width-50 middle text-center">#</th>
					<th class="fix-width-50 middle text-center"></th>
					<th class="fix-width-150 middle">ธนาคาร</th>
					<th class="min-width-200 middle">ชื่อบัญชี</th>
					<th class="fix-width-150 middle">เลขบัญชี</th>
					<th class="fix-width-150 middle">สาขา</th>
					<th class="fix-width-50 middle text-center">สถานะ</th>
					<th class="fix-width-100 middle">ผังบัญชี SAP</th>
					<th class="fix-width-100 middle"></th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr>
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle text-center"><img src="<?php echo bankLogoUrl($rs->bank_code); ?>" height="30px" width="30px" /></td>
						<td class="middle"><?php echo $rs->bank_name; ?></td>
						<td class="middle"><?php echo $rs->acc_name; ?></td>
						<td class="middle"><?php echo $rs->acc_no; ?></td>
            			<td class="middle"><?php echo $rs->branch; ?></td>
						<td class="middle text-center"><?php echo is_active($rs->active); ?></td>
						<td class="middle"><?php echo $rs->sapAcctCode; ?></td>
						<td class="text-right">
							<?php if($this->pm->can_edit) : ?>
								<button type="button" class="btn btn-mini btn-warning" onclick="getEdit(<?php echo $rs->id; ?>)">
									<i class="fa fa-pencil"></i>
								</button>
							<?php endif; ?>
							<?php if($this->pm->can_delete) : ?>
								<button type="button" class="btn btn-mini btn-danger" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->acc_name; ?>')">
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


<script src="<?php echo base_url(); ?>scripts/masters/bank_account.js"></script>

<?php $this->load->view('include/footer'); ?>
