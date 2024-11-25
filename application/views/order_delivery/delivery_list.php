<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h4 class="title">
      <?php echo $this->title; ?>
    </h4>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
      	<button type="button" class="btn btn-sm btn-purple pull-right" id="btn-invoice" onclick="createInvoiceByCheckedBill()"><i class="fa fa-file"></i> เปิดใบกำกับ</button>
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

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ช่องทางขาย</label>
			<select class="form-control input-sm" name="channels" onchange="getSearch()">
				<option value="">ทั้งหมด</option>
				<?php echo select_channels($channels); ?>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>เล่ม</label>
			<select class="form-control input-sm filter" name="is_term">
				<option value="all">ทั้งหมด</option>
				<option value="0" <?php echo is_selected('0', $is_term); ?>>ขายสด</option>
				<option value="1" <?php echo is_selected('1', $is_term); ?>>ขายเชื่อ</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>VAT</label>
			<select class="form-control input-sm filter" name="tax_status">
				<option value="all">ทั้งหมด</option>
				<option value="Y" <?php echo is_selected('Y', $tax_status); ?>>Yes</option>
				<option value="N" <?php echo is_selected('N', $tax_status); ?>>No</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Status</label>
			<select class="form-control input-sm filter" name="status">
				<option value="all">ทั้งหมด</option>
				<option value="O" <?php echo is_selected('O', $status); ?>>Open</option>
				<option value="C" <?php echo is_selected('C', $status); ?>>Close</option>
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
				<option value="">ทั้งหมด</option>
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
	<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15 padding-5">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="bill-div">
		<table class="table tableFixHead border-1" style="font-size:12px; min-width:1470px; border-collapse:inherit;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center fix-header">
						<label>
							<input type="checkbox" class="ace" onchange="toggleCheckAll($(this))" />
							<span class="lbl"></span>
						</label>
					</th>
					<th class="fix-width-40 middle text-center fix-header"></th>
					<th class="fix-width-40 middle text-center fix-header">#</th>
					<th class="fix-width-100 middle text-center fix-header">วันที่</th>
					<th class="fix-width-100 middle fix-header">เลขที่เอกสาร</th>
					<th class="fix-width-60 middle text-center fix-header">สถานะ</th>
					<th class="fix-width-100 middle fix-header">ใบกำกับ</th>
					<th class="fix-width-100 middle fix-header">เลขที่อ้างอิง</th>
					<th class="fix-width-80 middle text-center fix-header">เล่มเอกสาร</th>
					<th class="fix-width-100 middle fix-header">รหัสลูกค้า</th>
					<th class="fix-width-250 middle fix-header">ลูกค้า</th>
					<th class="fix-width-150 middle fix-header">ผู้ติดต่อ</th>
					<th class="fix-width-100 middle text-right fix-header">ยอดเงิน</th>
					<th class="fix-width-100 middle text-center fix-header">User</th>
					<th class="fix-width-200 middle fix-header">พนักงานขาย</th>
					<th class="fix-width-120 middle fix-header">ช่องทางขาย</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($orders as $rs) : ?>
            <tr class="order-rows" id="row-<?php echo $rs->id; ?>" style="<?php echo empty($rs->invoice_code) ? '' : 'background-color:#d5ebca;'; ?>" onclick="hilightRow(<?php echo $rs->id; ?>)">
							<td class="middle text-center">
								<?php if(empty($rs->invoice_code)) : ?>
									<label id="chk-<?php echo $rs->id; ?>">
										<input type="checkbox" class="ace chk" data-id="<?php echo $rs->id; ?>" value="<?php echo $rs->code; ?>" />
										<span class="lbl"></span>
									</label>
								<?php endif; ?>
							</td>
							<td class="middle text-center">
								<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
							</td>
							<td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle text-center pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo thai_date($rs->date_add); ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->code; ?></td>
							<td class="middle text-center pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo empty($rs->invoice_code) ? 'Open' : 'Closed'; ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->invoice_code; ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->reference; ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->is_term == 1 ? 'ขายเชื่อ' : 'ขายสด'; ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->customer_code; ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->customer_name; ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->customer_ref; ?></td>
							<td class="middle text-right pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo number($rs->doc_total, 2); ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->user; ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo get_sale_name($rs->sale_code); ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo channels_name($rs->channels_code); ?></td>
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

<script src="<?php echo base_url(); ?>scripts/order_delivery/order_delivery.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
