<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-5 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
</div>
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>จุดขาย</label>
		<select class="form-control input-sm filter" name="shop_id" id="shop_id">
			<option value="all">ทั้งหมด</option>
			<?php echo select_shop_id($shop_id); ?>
		</select>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เครื่อง POS</label>
		<select class="form-control input-sm filter" name="pos_id" >
			<option value="all">ทั้งหมด</option>
			<?php echo select_pos_id($pos_id); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-2 col-xs-6 padding-5">
		<label>สถานะ</label>
		<select class="form-control input-sm filter" name="status">
			<option value="all">ทั้งหมด</option>
			<option value="O" <?php echo is_selected('O', $status); ?>>Open</option>
			<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
		</select>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
		<label>วันที่เปิด</label>
		<div class="input-daterange input-group">
			<input type="text" class="form-control input-sm width-50 text-center from-date" name="open_from_date" id="openFromDate" value="<?php echo $open_from_date; ?>" readonly/>
			<input type="text" class="form-control input-sm width-50 text-center" name="open_to_date" id="openToDate" value="<?php echo $open_to_date; ?>" readonly/>
		</div>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
		<label>วันที่ปิด</label>
		<div class="input-daterange input-group">
			<input type="text" class="form-control input-sm width-50 text-center from-date" name="close_from_date" id="closeFromDate" value="<?php echo $close_from_date; ?>" readonly/>
			<input type="text" class="form-control input-sm width-50 text-center" name="close_to_date" id="closToDate" value="<?php echo $close_to_date; ?>" readonly/>
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
</div>
<input type="hidden" name="search" value="1" />
</form>
<hr class="padding-5 margin-top-15"/>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover tableFixHead border-1" style="min-width:1050px;">
			<thead>
				<tr>
					<th class="fix-width-100 middle text-center fix-header"></th>
					<th class="fix-width-40 middle text-center fix-header">#</th>
					<th class="fix-width-100 middle text-center fix-header">วันที่เปิด</th>
					<th class="fix-width-100 middle text-center fix-header">วันที่ปิด</th>
					<th class="fix-width-150 middle fix-header">เลขที่</th>
					<th class="fix-width-120 middle fix-header">เครื่อง POS</th>
					<th class="fix-width-100 middle text-right fix-header">ยอดรับ</th>
					<th class="fix-width-50 middle fix-header">สถานะ</th>
					<th class="min-width-200 middle fix-header">จุดขาย</th>
				</tr>
			</thead>
			<tbody>
				<?php if( ! empty($orders)) : ?>
					<?php $no = $this->uri->segment($this->segment) + 1; ?>
					<?php foreach($orders as $rs) : ?>
						<?php $color = $rs->status == 'C' ? 'green' : ''; ?>
						<tr id="row-<?php echo $rs->id; ?>" class="<?php echo $color; ?>">
							<td class="middle">
								<button type="button" class="btn btn-mini btn-primary" onclick="viewDetail('<?php echo $rs->id; ?>')"><i class="fa fa-eye"></i></button>
							<?php if($rs->status == 'C') : ?>
								<button type="button" class="btn btn-mini btn-info" onclick="printPosRound('<?php echo $rs->id; ?>')"><i class="fa fa-print"></i></button>
							<?php endif; ?>
							</td>
							<td class="middle text-center"><?php echo number($no); ?></td>
							<td class="middle text-center"><?php echo thai_date($rs->open_date); ?></td>
							<td class="middle text-center"><?php echo empty($rs->close_date) ? NULL : thai_date($rs->close_date); ?></td>
							<td class="middle"><?php echo $rs->code; ?></td>
							<td class="middle"><?php echo $rs->pos_name; ?></td>
							<td class="middle text-right"><?php echo number($rs->round_total, 2); ?></td>
							<td class="middle text-center" id="status-<?php echo $rs->id; ?>"><?php echo bill_status_label($rs->status); ?></td>
							<td class="middle"><?php echo $rs->shop_name; ?></td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/order_pos_round/order_pos_round.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/pos_footer'); ?>
