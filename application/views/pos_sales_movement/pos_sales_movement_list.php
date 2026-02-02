<?php $this->load->view('include/header'); ?>
<div class="row" id="page-title">
	<div class="col-lg-6 col-md-6 col-sm-5 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
</div>
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row" id="search-row">
	<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm search" name="code" id="code" value="<?php echo $code; ?>" />
	</div>
	<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>รอบการขาย</label>
		<input type="text" class="form-control input-sm search" name="round_code" id="round_code"  value="<?php echo $round_code; ?>" />
	</div>
	<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>จุดขาย</label>
		<select class="form-control input-sm filter" name="shop_id" id="shop_id">
			<option value="all">ทั้งหมด</option>
			<?php echo select_shop_id($shop_id); ?>
		</select>
	</div>
	<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เครื่อง POS</label>
		<select class="form-control input-sm filter" name="pos_id" id="pos_id">
			<option value="all">ทั้งหมด</option>
			<?php echo select_pos_id($pos_id); ?>
		</select>
	</div>

	<div class="col-lg-2 col-md-1 col-sm-2 col-xs-6 padding-5">
		<label>ประเภท</label>
		<select class="form-control input-sm filter" name="type" id="type">
			<option value="all">ทั้งหมด</option>
			<option value="S" <?php echo is_selected('S', $type); ?>>ขาย</option>
			<option value="C" <?php echo is_selected('C', $type); ?>>ยกเลิก</option>
			<option value="R" <?php echo is_selected('R', $type); ?>>คืน</option>
			<option value="CR" <?php echo is_selected('CR', $type); ?>>ยกเลิกการคืน</option>
			<option value="CI" <?php echo is_selected('CI', $type); ?>>นำเงินเข้า</option>
			<option value="CO" <?php echo is_selected('CO', $type); ?>>นำเงินออก</option>
			<option value="DP" <?php echo is_selected('DP', $type); ?>>รับมัดจำ</option>
			<option value="DC" <?php echo is_selected('DC', $type); ?>>ยกเลิกเงินมัดจำ</option>
			<option value="RO" <?php echo is_selected('RO', $type); ?>>เปิดรอบขาย</option>
			<option value="RC" <?php echo is_selected('RC', $type); ?>>ปิดรอบขาย</option>
		</select>
	</div>

	<div class="col-lg-2 col-md-1 col-sm-2 col-xs-6 padding-5">
		<label>ช่องทาง</label>
		<select class="form-control input-sm filter" name="role" id="role">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $role); ?>>เงินสด</option>
			<option value="2" <?php echo is_selected('2', $role); ?>>เงินโอน</option>
			<option value="3" <?php echo is_selected('3', $role); ?>>บัตรเครดิต</option>
			<option value="7" <?php echo is_selected('7', $role); ?>>เช็ค</option>
		</select>
	</div>

	<div class="col-lg-2 col-md-1 col-sm-2 col-xs-6 padding-5">
		<label>บัญชี</label>
		<select class="form-control input-sm filter" name="bank" id="bank">
			<option value="all">ทั้งหมด</option>
			<?php echo select_bank_account($bank); ?>
		</select>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
		<label>วันที่</label>
		<div class="input-daterange input-group">
			<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" readonly/>
			<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" readonly/>
		</div>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">Search</button>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="exportFilter()">Export</button>
	</div>
</div>
<input type="hidden" name="search" value="1" />
</form>
<hr class="padding-5 margin-top-15"/>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" id="item-div" style="overflow:auto;">
		<table class="table table-striped table-hover tableFixHead border-1" style="min-width:1290px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center fix-header">#</th>
					<th class="fix-width-150 middle text-center fix-header">วันที่</th>
					<th class="fix-width-150 middle fix-header">เลขที่</th>
					<th class="fix-width-100 middle text-center fix-header">ประเภท</th>
					<th class="fix-width-100 middle text-right fix-header">ยอดเงิน</th>
					<th class="fix-width-100 middle text-center fix-header">ช่องทาง</th>
					<th class="fix-width-150 middle fix-header">เลขที่บัญชี</th>
					<th class="fix-width-150 middle fix-header">รอบการขาย</th>
					<th class="fix-width-150 middle fix-header">POS No.</th>
					<th class="fix-width-100 middle fix-header">จุดขาย</th>
					<th class="min-width-100 middle fix-header">พนักงาน</th>
				</tr>
			</thead>
			<tbody>
				<?php if( ! empty($details)) : ?>
					<?php $no = $this->uri->segment($this->segment) + 1; ?>
					<?php foreach($details as $rs) : ?>
						<tr id="row-<?php echo $rs->id; ?>">
							<td class="middle text-center"><?php echo number($no); ?></td>
							<td class="middle text-center"><?php echo thai_date($rs->date_upd, TRUE, '/'); ?></td>
							<td class="middle"><?php echo $rs->code; ?></td>
							<td class="middle text-center"><?php echo movement_type_label($rs->type); ?></td>
							<td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
							<td class="middle text-center"><?php echo payment_role_label($rs->payment_role); ?></td>
							<td class="middle"><?php echo $rs->acc_no; ?></td>
							<td class="middle"><?php echo $rs->round_code; ?></td>
							<td class="middle"><?php echo $rs->pos_code; ?></td>
							<td class="middle"><?php echo $rs->shop_code; ?></td>
							<td class="middle"><?php echo $rs->user; ?></td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<form id="exportForm" action="<?php echo current_url(); ?>/export_filter" method="post">
	<input type="hidden" name="token" id="token" />
	<input type="hidden" name="data" id="data" />
</form>
<script src="<?php echo base_url(); ?>scripts/pos_sales_movement/pos_sales_movement.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
