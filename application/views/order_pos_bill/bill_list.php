<?php $this->load->view('include/header'); ?>
<style>
	.active-row {
		background-color: #c6e3f0 !important;
	}
</style>
<div class="row hidden-xs">
	<div class="col-lg-6 col-md-6 col-sm-5 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-purple pull-right" id="btn-invoice" onclick="createInvoiceByCheckedBill()">เปิดใบกำกับ</button>
		</p>
	</div>
</div>
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row hidden-xs" id="search-row">
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
    </div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>ใบกำกับ</label>
      <input type="text" class="form-control input-sm search" name="invoice_code"  value="<?php echo $invoice_code; ?>" />
    </div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ใบสั่งขาย</label>
			<input type="text" class="form-control input-sm search" name="so_code"  value="<?php echo $so_code; ?>" />
		</div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>ใบตัดยอด</label>
      <input type="text" class="form-control input-sm search" name="ref_code"  value="<?php echo $ref_code; ?>" />
    </div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>จุดขาย</label>
  		<select class="form-control input-sm filter" name="shop_id" id="shop_id">
  			<option value="all">ทั้งหมด</option>
  			<?php echo select_shop_id($shop_id); ?>
  		</select>
    </div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>เครื่อง POS</label>
  		<select class="form-control input-sm filter" name="pos_id" >
  			<option value="all">ทั้งหมด</option>
  			<?php echo select_pos_id($pos_id); ?>
  		</select>
    </div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>ช่องทางขาย</label>
      <select class="width-100 filter" name="channels" >
        <option value="all">ทั้งหมด</option>
        <?php echo select_channels($channels); ?>
      </select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>การชำระเงิน</label>
  		<select class="form-control input-sm filter" name="payment" >
  			<option value="all">ทั้งหมด</option>
  			<?php echo select_pos_payment_method($payment); ?>
  		</select>
    </div>

		<div class="col-lg-1 col-md-1 col-sm-2 col-xs-6 padding-5">
      <label>ชนิด VAT</label>
  		<select class="form-control input-sm filter" name="vat_type">
  			<option value="all">ทั้งหมด</option>
  			<option value="E" <?php echo is_selected('E', $vat_type); ?>>แยกนอก</option>
        <option value="I" <?php echo is_selected('I', $vat_type); ?>>รวมใน</option>
        <option value="N" <?php echo is_selected('N', $vat_type); ?>>ไม่ VAT</option>
  		</select>
    </div>

    <div class="col-lg-1 col-md-1 col-sm-2 col-xs-6 padding-5">
      <label>สถานะ</label>
  		<select class="form-control input-sm filter" name="status">
  			<option value="all">ทั้งหมด</option>
  			<option value="O" <?php echo is_selected('O', $status); ?>>Open</option>
        <option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
        <option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
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
  </div>
	<input type="hidden" name="search" value="1" />
</form>
<hr class="padding-5 margin-top-15"/>
<?php echo $this->pagination->create_links(); ?>
<div class="row hidden-xs">
	<div class="col-lg-12 col-md-12 col-sm-12 border-1 padding-0" id="bill-div" style="background-color: #eee; overflow:auto;">
		<table class="table table-striped table-hover tableFixHead" style="font-size:11px; min-width:1070px;">
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
					<th class="fix-width-120 middle fix-header">เลขที่เอกสาร</th>
					<th class="fix-width-100 middle text-right fix-header">ยอดขาย</th>
					<th class="fix-width-100 middle text-right fix-header">มัดจำ</th>
					<th class="fix-width-100 middle text-right fix-header">หัก ณ</th>
					<th class="fix-width-100 middle text-right fix-header">ยอดรับเงิน</th>
					<th class="fix-width-80 middle text-center fix-header">สถานะ</th>
					<th class="fix-width-100 middle fix-header">ใบสั่งขาย</th>
					<th class="fix-width-100 middle fix-header">ใบกำกับ</th>
					<th class="fix-width-100 middle fix-header">การชำระเงิน</th>
					<th class="fix-width-100 middle fix-header">ช่องทางขาย</th>
					<th class="fix-width-120 middle fix-header">User</th>
					<th class="fix-width-120 middle fix-header">พนักงานขาย</th>
					<th class="fix-width-120 middle fix-header">ใบตัดยอด</th>
				</tr>
			</thead>
			<tbody>
		<?php if( ! empty($orders)) : ?>
			<?php $no = $this->uri->segment($this->segment) + 1; ?>
			<?php foreach($orders as $rs) : ?>
				<?php $color = $rs->status == 'C' ? 'green' : ($rs->status == 'D' ? 'red' : ''); ?>
				<tr id="row-<?php echo $rs->id; ?>" class="pos-rows <?php echo $color; ?>" onclick="hilightRow(<?php echo $rs->id; ?>)">
					<td class="middle text-center">
						<?php if($rs->status == 'O') : ?>
							<label id="chk-<?php echo $rs->id; ?>">
								<input type="checkbox" class="ace chk" data-id="<?php echo $rs->id; ?>" value="<?php echo $rs->code; ?>" />
								<span class="lbl"></span>
							</label>
						<?php endif; ?>
					</td>
					<td class="middle text-center">
						<button type="button" class="btn btn-mini btn-info" onclick="printBill('<?php echo $rs->code; ?>')"><i class="fa fa-print"></i></button>
					</td>
					<td class="middle text-center pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')"><?php echo number($no); ?></td>
					<td class="middle text-center pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')"><?php echo thai_date($rs->date_add); ?></td>
					<td class="middle pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')"><?php echo $rs->code; ?></td>
					<td class="middle text-right pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')"><?php echo number($rs->amount, 2); ?></td>
					<td class="middle text-right pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')"><?php echo number($rs->down_payment_amount, 2); ?></td>
					<td class="middle text-right pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')"><?php echo number($rs->WhtAmount, 2); ?></td>
					<td class="middle text-right pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')"><?php echo number($rs->payAmount, 2); ?></td>
					<td class="middle text-center pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')" id="status-<?php echo $rs->id; ?>"><?php echo bill_status_label($rs->status); ?></td>
					<td class="middle pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')"><?php echo $rs->so_code; ?></td>
					<td class="middle pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')"><?php echo $rs->invoice_code; ?></td>
					<td class="middle pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')"><?php echo $rs->payment_name; ?></td>
					<td class="middle pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')"><?php echo $rs->channels_name; ?></td>
					<td class="middle pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')"><?php echo $rs->uname; ?></td>
					<td class="middle pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')"><?php echo $rs->sale_name; ?></td>
					<td class="middle pointer" onclick="viewBillDetail('<?php echo $rs->id; ?>')"><?php echo $rs->ref_code; ?></td>
				</tr>
				<?php $no++; ?>
			<?php endforeach; ?>
		<?php endif; ?>
			</tbody>
		</table>
	</div>

</div>
<?php $this->load->view('order_pos/pos_cancel_modal'); ?>

<div class="row visible-xs">
	<div class="col-xs-12"><h1 class="text-center">Not supporrt mobile</h1></div>
</div>

<script src="<?php echo base_url(); ?>scripts/order_pos_bill/order_pos_bill.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos_invoice.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
