<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>assets/js/jquery.colorbox.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/colorbox.css" />
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-white btn-purple hide" onclick="exportFilter()">Export Filter</button>
			<button type="button" class="btn btn-white btn-success" onclick="exportToSap()">Export to SAP</button>
		</p>
	</div>
</div>
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm search" name="code" id="code" value="<?php echo $code; ?>" />
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ใบสั่งงาน</label>
			<input type="text" class="form-control input-sm search" name="order_code" id="order_code" value="<?php echo $order_code; ?>" />
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>บิลขาย</label>
			<input type="text" class="form-control input-sm search" name="bill_code" id="bill_code"  value="<?php echo $bill_code; ?>" />
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
			<select class="form-control input-sm filter" name="pos_id" id="pos_id">
				<option value="all">ทั้งหมด</option>
				<?php echo select_pos_id($pos_id); ?>
			</select>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>การชำระเงิน</label>
			<select class="form-control input-sm filter" name="payment" id="payment">
				<option value="all">ทั้งหมด</option>
				<?php echo select_pos_payment_method($payment); ?>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>แนบสลิป</label>
			<select class="form-control input-sm filter" name="has_slip" id="has-slip">
				<option value="all">ทั้งหมด</option>
				<option value="Y" <?php echo is_selected('Y', $has_slip); ?>>Yes</option>
				<option value="N" <?php echo is_selected('N', $has_slip); ?>>No</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>สถานะ</label>
			<select class="form-control input-sm filter" name="status" id="status">
				<option value="all">ทั้งหมด</option>
				<option value="O" <?php echo is_selected('O', $status); ?>>Open</option>
				<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
				<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
			</select>
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>Exported</label>
			<select class="form-control input-sm filter" name="is_exported">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $is_exported); ?>>ส่งแล้ว</option>
				<option value="0" <?php echo is_selected('0', $is_exported); ?>>ยังไม่ส่ง</option>
				<option value="3" <?php echo is_selected('3', $is_exported); ?>>ส่งไม่สำเร็จ</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" readonly/>
				<input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" readonly/>
			</div>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">btn</label>
			<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">Search</button>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="display-block not-show">btn</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<form id="exportForm" method="post" action="<?php echo current_url(); ?>/export_filter">
	<input type="hidden" name="exCode" id="ex-code" />
	<input type="hidden" name="exOrderCode" id="ex-order-code" />
	<input type="hidden" name="exBillCode" id="ex-bill-code" />
	<input type="hidden" name="exShopId" id="ex-shop-id" />
	<input type="hidden" name="exPosId" id="ex-pos-id" />
	<input type="hidden" name="exPayment" id="ex-payment" />
	<input type="hidden" name="exFromDate" id="ex-from-date" />
	<input type="hidden" name="exToDate" id="ex-to-date" />
	<input type="hidden" name="token" id="token" />
</form>
<hr class="padding-5 margin-top-15"/>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover tableFixHead border-1" style="min-width:1940px;">
			<thead>
				<tr>
					<!-- <th class="fix-width-60 middle text-center fix-header"></th> -->
					<th class="fix-width-40 middle text-center fix-header">
						<label>
							<input type="checkbox" class="ace" id="chk-all" onchange="checkAll()"/>
							<span class="lbl"></span>
						</label>
					</th>
					<th class="fix-width-40 middle text-center fix-header">#</th>
					<th class="fix-width-100 middle text-center fix-header">วันที่</th>
					<th class="fix-width-100 middle fix-header">เลขที่เอกสาร</th>
					<th class="fix-width-120 middle fix-header">ใบสั่งขาย</th>
					<th class="fix-width-120 middle fix-header">ใบกำกับภาษี</th>
					<th class="fix-width-80 middle fix-header">SAP No.</th>
					<th class="fix-width-120 middle fix-header">บิลขาย</th>
					<th class="fix-width-80 middle text-right fix-header">ยอดเงิน</th>
					<th class="fix-width-80 middle text-right fix-header">ใช้ไป</th>
					<th class="fix-width-80 middle text-right fix-header">คงเหลือ</th>
					<th class="fix-width-60 middle text-center fix-header">สถานะ</th>
					<th class="fix-width-60 middle text-center fix-header">Export</th>
					<th class="fix-width-100 middle fix-header">จุดขาย</th>
					<th class="fix-width-100 middle fix-header">เครื่อง POS</th>
					<th class="fix-width-150 middle fix-header">การชำระเงิน</th>
					<th class="fix-width-150 middle fix-header">เจ้าหน้าที่</th>
					<th class="fix-width-300 middle fix-header">พนักงานขาย</th>
				</tr>
			</thead>
			<tbody>
				<?php if( ! empty($orders)) : ?>
					<?php $no = $this->uri->segment($this->segment) + 1; ?>
					<?php foreach($orders as $rs) : ?>
						<?php $color = $rs->status == 'C' ? 'green' : ($rs->status == 'D' ? 'red' : ''); ?>
						<tr id="row-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>" class="order-row <?php echo $color; ?>" title="ดับเบิลคลิกเพื่อเปิด">
							<!-- <td class="middle">
								<button type="button" class="btn btn-mini btn-info" onclick="printDownPayment('<?php echo $rs->code; ?>')"><i class="fa fa-print"></i></button>
								<?php if( ! empty($rs->image_path)) : ?>
									<button type="button" class="btn btn-mini btn-primary" onclick="viewSlip('<?php echo base_url().$rs->image_path; ?>')"><i class="fa fa-file-image-o"></i></button>
								<?php endif; ?>
							</td> -->
							<td class="middle text-center pointer">
								<?php if($rs->status != 'D' && $rs->is_exported != 1 && $rs->is_interface) : ?>
									<label>
										<input type="checkbox" class="ace chk" value="<?php echo $rs->code; ?>" data-id="<?php echo $rs->id; ?>"/>
										<span class="lbl"></span>
									</label>
								<?php endif; ?>
							</td>
							<td class="middle text-center pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo number($no); ?></td>
							<td class="middle text-center pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo thai_date($rs->date_add); ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->code; ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->reference; ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->invoice_code; ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->DocNum; ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->ref_code; ?></td>
							<td class="middle text-right pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo number($rs->amount, 2); ?></td>
							<td class="middle text-right pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo number($rs->used, 2); ?></td>
							<td class="middle text-right pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo number($rs->available, 2); ?></td>
							<td class="middle text-center pointer" id="status-<?php echo $rs->id; ?>" ondblclick="viewDetail('<?php echo $rs->code; ?>')">
								<?php echo bill_status_label($rs->status); ?>
							</td>
							<td class="middle text-center pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')">
							<?php if($rs->is_interface) : ?>
								<?php echo $rs->is_exported == 1 ? 'Yes' : ($rs->is_exported == 3 ? 'Error' : 'No'); ?>
							<?php endif; ?>
							</td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->shop_name; ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->pos_name; ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')">
							<?php if($rs->payment_role == 6) : ?>
									<?php $payments = $this->order_pos_model->get_order_payment_details($rs->code); ?>
									<?php if( ! empty($payments)) : ?>
										<?php foreach($payments as $pm) : ?>
											<?php echo $pm->role_name. " : ". number($pm->amount, 2); ?><br/>
										<?php endforeach; ?>
									<?php endif; ?>
							<?php else : ?>
								<?php echo $rs->payment_name; ?>
							<?php endif; ?>
							</td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $this->user_model->get_name($rs->user); ?></td>
							<td class="middle pointer" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $this->slp_model->get_name($rs->sale_id); ?></td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            </div>
            <div class="modal-body" id="imageBody">

            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<script>
	$('.order-row').click(function() {
		if($(this).hasClass('active-row')) {
			$(this).removeClass('active-row');
		}
		else {
			$(this).addClass('active-row');
		}
	})
</script>
<script src="<?php echo base_url(); ?>scripts/order_down_payment/order_down_payment.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
