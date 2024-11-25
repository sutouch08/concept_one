<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h4 class="title">
      <?php echo $this->title; ?>
    </h4>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-xs btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row" id="search-row">
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>ใบสั่งขาย</label>
			<input type="text" class="form-control input-sm search" name="so_code"  value="<?php echo $so_code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>ใบกำกับ</label>
			<input type="text" class="form-control input-sm search" name="invoice_code" value="<?php echo $invoice_code; ?>" />
		</div>
		<div class="col-lg-1-harf col-md-2-harf col-sm-2 col-xs-6 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>เลขที่อ้างอิง</label>
			<input type="text" class="form-control input-sm search" name="reference" value="<?php echo $reference; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>เลขที่จัดส่ง</label>
			<input type="text" class="form-control input-sm search" name="shipCode" value="<?php echo $ship_code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ช่องทางขาย</label>
			<select class="form-control input-sm" name="channels" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_channels($channels); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>เล่มเอกสาร</label>
			<select class="form-control input-sm filter" name="is_term">
				<option value="all">ทั้งหมด</option>
				<option value="0" <?php echo is_selected('0', $is_term); ?>>ขายสด</option>
				<option value="1" <?php echo is_selected('1', $is_term); ?>>ขายเชื่อ</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
			<label>User</label>
			<select class="width-100 filter" id="user" name="user">
				<option value="all">ทั้งหมด</option>
				<?php echo select_uname($user); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
			<label>พนักงานขาย</label>
			<select class="width-100 filter" id="sale_code" name="sale_code">
				<option value="all">ทั้งหมด</option>
				<?php echo select_saleman($sale_code); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-2-harf col-md-2 col-sm-3-harf col-xs-6 padding-5">
			<label>คลัง</label>
			<select class="form-control input-sm" name="warehouse" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<?php echo select_warehouse($warehouse); ?>
			</select>
		</div>

		<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">Search</button>
		</div>
		<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
	</div>
	<hr>
	<div class="row margin-top-10" id="button-row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
			<button type="button" id="btn-state-1" class="btn btn-xs margin-bottom-5 <?php echo $btn['state_1']; ?>" onclick="toggleState(1)">รอดำเนินการ</button>
			<button type="button" id="btn-state-2" class="btn btn-xs margin-bottom-5 <?php echo $btn['state_2']; ?>" onclick="toggleState(2)">รอชำระเงิน</button>
			<button type="button" id="btn-state-3" class="btn btn-xs margin-bottom-5 <?php echo $btn['state_3']; ?>" onclick="toggleState(3)">รอจัด</button>
			<button type="button" id="btn-state-4" class="btn btn-xs margin-bottom-5 <?php echo $btn['state_4']; ?>" onclick="toggleState(4)">กำลังจัด</button>
			<button type="button" id="btn-state-7" class="btn btn-xs margin-bottom-5 <?php echo $btn['state_7']; ?>" onclick="toggleState(7)">รอเปิดบิล</button>
			<button type="button" id="btn-state-8" class="btn btn-xs margin-bottom-5 <?php echo $btn['state_8']; ?>" onclick="toggleState(8)">เปิดบิลแล้ว</button>
			<button type="button" id="btn-state-9" class="btn btn-xs margin-bottom-5 <?php echo $btn['state_9']; ?>" onclick="toggleState(9)">ยกเลิก</button>
			<button type="button" id="btn-not-save" class="btn btn-xs margin-bottom-5 <?php echo $btn['not_save']; ?>" onclick="toggleNotSave()">ไม่บันทึก</button>
			<button type="button" id="btn-only-me" class="btn btn-xs margin-bottom-5 <?php echo $btn['only_me']; ?>" onclick="toggleOnlyMe()">เฉพาะฉัน</button>
		</div>
	</div>

	<input type="hidden" name="state_1" id="state_1" value="<?php echo $state[1]; ?>" />
	<input type="hidden" name="state_2" id="state_2" value="<?php echo $state[2]; ?>" />
	<input type="hidden" name="state_3" id="state_3" value="<?php echo $state[3]; ?>" />
	<input type="hidden" name="state_4" id="state_4" value="<?php echo $state[4]; ?>" />
	<input type="hidden" name="state_7" id="state_7" value="<?php echo $state[7]; ?>" />
	<input type="hidden" name="state_8" id="state_8" value="<?php echo $state[8]; ?>" />
	<input type="hidden" name="state_9" id="state_9" value="<?php echo $state[9]; ?>" />
	<input type="hidden" name="notSave" id="notSave" value="<?php echo $notSave; ?>" />
	<input type="hidden" name="onlyMe" id="onlyMe" value="<?php echo $onlyMe; ?>" />
</form>
<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="bill-div">
		<table class="table tableFixHead border-1" style="font-size:12px; min-width:1470px; border-collapse:inherit;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center fix-header">#</th>
					<th class="fix-width-100 middle text-center fix-header">วันที่</th>
					<th class="fix-width-100 middle fix-header">เลขที่เอกสาร</th>
					<th class="fix-width-80 middle text-center fix-header">เล่มเอกสาร</th>
					<th class="fix-width-100 middle fix-header">เลขที่อ้างอิง</th>
					<th class="fix-width-100 middle fix-header">รหัสลูกค้า</th>
					<th class="fix-width-250 middle fix-header">ลูกค้า</th>
					<th class="fix-width-150 middle fix-header">ผู้ติดต่อ</th>
					<th class="fix-width-100 middle text-right fix-header">ยอดเงิน</th>
					<th class="fix-width-100 middle text-center fix-header">User</th>
					<th class="fix-width-200 middle fix-header">พนักงานขาย</th>
					<th class="fix-width-120 middle fix-header">ช่องทางขาย</th>
					<th class="fix-width-100 middle fix-header">ใบกำกับ</th>
					<th class="fix-width-100 middle fix-header">สถานะ</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($orders as $rs) : ?>
						<?php $ref = empty($rs->reference) ? '' :' ['.$rs->reference.']'; ?>
            <tr id="row-<?php echo $rs->code; ?>" style="<?php echo state_color($rs->state, $rs->status, $rs->is_expired); ?>">
              <td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $no; ?></td>
              <td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->code; ?></td>
							<td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->is_term == 1 ? 'ขายเชื่อ' : 'ขายสด'; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->reference; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->customer_code; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->customer_name; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->customer_ref; ?></td>
							<td class="middle pointer text-right" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo number($rs->doc_total, 2); ?></td>
							<td class="middle pointer text-center" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->user; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->sale_name; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->channels_name; ?></td>
							<td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->invoice_code; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo ($rs->is_expired ? 'หมดอายุ' : $rs->state_name); ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script>
	$('#user').select2();
	$('#sale_code').select2();
</script>

<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
