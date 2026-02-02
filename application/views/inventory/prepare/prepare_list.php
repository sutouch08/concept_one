<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
		<h3 class="title">ออเดอร์รอจัด</h3>
	</div>
	<div class="col-xs-12 visible-xs padding-5">
		<h4 class="title-xs">ออเดอร์รอจัด</h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
		<button type="button" class="btn btn-xs btn-primary btn-100" onclick="goProcess()">กำลังจัด</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>ใบสั่งขาย</label>
			<input type="text" class="form-control input-sm search" name="so_code"  value="<?php echo $so_code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
		</div>

		<div class="col-lg-2 col-md-3 col-sm-3-harf col-xs-6 padding-5">
			<label>พนักงาน</label>
			<select class="width-100 filter" name="user" id="user">
				<option value="all">ทั้งหมด</option>
				<?php echo select_user($user); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>ช่องทางขาย</label>
			<select class="form-control input-sm" name="channels" onchange="getSearch()">
				<option value="">ทั้งหมด</option>
				<?php echo select_channels($channels); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ประเภท</label>
			<select class="form-control input-sm" name="role" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="S" <?php echo is_selected($role, 'S'); ?>>ขาย (WO)</option>
				<!-- <option value="C" <?php echo is_selected($role, 'C'); ?>>ฝากขาย (WC)</option> -->
				<option value="N" <?php echo is_selected($role, 'N'); ?>>ฝากขาย (WT)</option>
				<option value="P" <?php echo is_selected($role, 'P'); ?>>สปอนเซอร์ (WS)</option>
				<option value="U" <?php echo is_selected($role, 'U'); ?>>อภินันท์ (WU)</option>
				<!-- <option value="Q" <?php echo is_selected($role, 'Q'); ?>>แปรสภาพ(สต็อก)</option> -->
				<option value="T" <?php echo is_selected($role, 'T'); ?>>แปรสภาพ (WQ)</option>
				<option value="L" <?php echo is_selected($role, 'L'); ?>>ยืม (WL)</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>

		</div>

		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5">
			<label>คลัง</label>
			<select class="width-100" name="warehouse" id="warehouse" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_common_warehouse($warehouse); ?>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">search</label>
			<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">ค้นหา</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">clear</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
	</div>
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-3 col-md-4 col-sm-4 padding-5 hidden-xs">
		<div class="input-group width-100">
			<span class="input-group-addon">จัดออเดอร์</span>
			<input type="text" class="form-control input-sm text-center" id="order-code" autofocus />
		</div>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5 hidden-xs">
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="goToProcess()">จัดสินค้า</button>
	</div>
</div>
<hr class="margin-top-15 hidden-xs">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-sm-12 padding-5 table-responsive">
		<table class="table table-striped table-hover tableFixHead border-1" style="min-width:1100px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-60 middle text-center"></th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-100 middle">เลขที่เอกสาร</th>
					<th class="fix-width-150 middle">เลขที่อ้างอิง</th>
					<th class="fix-width-100 middle">ใบสั่งขาย</th>
					<th class="min-width-150 middle">ลูกค้า/ผู้เบิก</th>
					<th class="fix-width-100 middle text-center">จำนวน</th>
          <th class="fix-width-150 middle">ช่องทาง</th>
					<th class="fix-width-150 middle">พนักงาน</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($orders as $rs) : ?>
            <?php $customer_name = (!empty($rs->customer_ref)) ? $rs->customer_ref : $rs->customer_name; ?>
            <tr class="font-size-11" id="row-<?php echo $rs->code; ?>">
              <td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle text-center">
								<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
									<button type="button" class="btn btn-mini btn-info" onClick="goPrepare('<?php echo $rs->code; ?>')">จัดสินค้า</button>
								<?php endif; ?>
							</td>
							<td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE,'/'); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
							<td class="middle"><?php echo $rs->reference; ?></td>
							<td class="middle"><?php echo $rs->so_code; ?></td>
              <td class="middle">
								<?php if($rs->role == 'L' OR $rs->role == 'R') : ?>
									<?php echo $rs->empName; ?>
								<?php else : ?>
									<?php echo $customer_name; ?>
								<?php endif; ?>
							</td>
							<td class="middle text-center"><?php echo number($rs->qty); ?></td>
              <td class="middle"><?php echo $rs->channels_name; ?></td>
              <td class="middle">
								<?php echo $rs->user_name; ?>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="10" class="text-center">--- No content ---</td>
          </tr>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script>
	$('#user').select2();
	$('#warehouse').select2();
</script>

<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare.js?v=<?php echo date('YmdHis'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare/prepare_list.js?v=<?php echo date('YmdHis'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
