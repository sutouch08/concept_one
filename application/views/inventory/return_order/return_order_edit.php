<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 visible-xs padding-5">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
<?php if($doc->status == 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
				<button type="button" class="btn btn-sm btn-purple btn-100" onclick="saveUpdate(1)">Save As Draft</button>
				<button type="button" class="btn btn-sm btn-success btn-100" onclick="saveUpdate(0)">Save</button>
<?php endif; ?>
    </p>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center edit" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly />
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center edit" name="customer_code" id="customer_code" value="<?php echo $doc->customer_code; ?>"  />
	</div>
	<div class="col-lg-7-harf col-md-6-harf col-sm-5 col-xs-8 padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="form-control input-sm edit" name="customer_name" id="customer_name" value="<?php echo $doc->customer_name; ?>" />
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>อ้างอิง</label>
		<input type="text" class="form-control input-sm" name="reference" id="reference" value="<?php echo $doc->reference; ?>" />
	</div>

	<div class="col-lg-10-harf col-md-10 col-sm-10-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm edit" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>"  />
	</div>

	<div class="divider">	</div>
<?php if($doc->ref_type != 4) : ?>
			<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<?php $dsa = empty($doc->invoice) ? "" : "disabled"; ?>
				<label>ใบกำกับ[SAP]</label>
				<input type="text" class="form-control input-sm text-center"	name="invoice" id="invoice" value="<?php echo $doc->invoice; ?>" placeholder="ค้นหาใบกำกับ" <?php echo $dsa; ?>/>
				<input type="hidden" id="invoice-code" />
			</div>
			<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<?php $cfm = empty($doc->invoice) ? "" : 'hide'; ?>
		<?php $clr = empty($doc->invoice) ? "hide" : ""; ?>
				<label class="display-block not-show">confirm</label>
				<button type="button" class="btn btn-xs btn-primary btn-block <?php echo $cfm; ?>" id="btn-confirm-inv" onclick="getInvoice()">ยืนยัน</button>
				<button type="button" class="btn btn-xs btn-warning btn-block <?php echo $clr; ?>" id="btn-clear-inv" onclick="clearInvoice()">Clear</button>
			</div>
<?php endif; ?>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<label>คลังสินค้า</label>
		<select class="form-control input-sm" name="warehouse" id="warehouse" onchange="zoneInit(1)">
			<option value="">เลือก</option>
			<?php echo select_common_warehouse($doc->warehouse_code); ?>
		</select>
	</div>
	<div class="col-lg-2-harf col-md-3 col-sm-3-harf col-xs-6 padding-5">
		<label>โซนรับสินค้า</label>
		<input type="text" class="form-control input-sm" name="zone_code" id="zone_code" placeholder="รหัสโซน" value="<?php echo $doc->zone_code; ?>" />
	</div>
	<div class="col-lg-5 col-md-4 col-sm-5 col-xs-6 padding-5">
		<label class="not-show">zone</label>
		<input type="text" class="form-control input-sm zone" name="zone" id="zone" placeholder="ชื่อโซน" value="<?php echo $doc->zone_name; ?>" />
	</div>
</div>

<input type="hidden" id="return_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" name="warehouse_code" id="warehouse_code" value="<?php echo $doc->warehouse_code; ?>"/>
<input type="hidden" name="invoice_code" id="invoice_code" value="<?php echo $doc->invoice; ?>" />
<input type="hidden" name="allow_return_no_inv" id="allow-return-no-inv" value="<?php echo getConfig('ALLOW_RETURN_NO_INV'); ?>" />

<hr class="margin-top-15"/>
<?php if($doc->ref_type == 4) : ?>
	<div class="row margin-bottom-10">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center red font-size-14">
			** เอกสารนี้ถูกสร้างโดยระบบ POS จึงไม่สามารถแก้ไขรายการได้ **
		</div>
	</div>
<?php else : ?>
	<?php $this->load->view('inventory/return_order/return_order_control'); ?>
<?php endif; ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="font-size:11px; min-width:1000px;">
			<thead>
				<tr>
				<?php if($doc->ref_type != 4) : ?>
					<th class="fix-width-40 text-center">
					<input type="checkbox" id="chk-all" class="ace" onchange="toggleCheckAll($(this))"/>
					<span class="lbl"></span>
					</th>
				<?php endif; ?>
					<th class="fix-width-40 text-center">ลำดับ</th>
					<th class="fix-width-150">รหัส</th>
					<th class="min-width-150">สินค้า</th>
					<th class="fix-width-120 text-center">อ้างอิง</th>
					<th class="fix-width-120 text-center">ออเดอร์</th>
					<th class="fix-width-80 text-center">ราคา</th>
					<th class="fix-width-100 text-center">ส่วนลด(%)</th>
					<th class="fix-width-80 text-center">จำนวน</th>
					<th class="fix-width-80 text-center">คืน</th>
					<th class="fix-width-100 text-right">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="detail-table">
<?php  $total_qty = 0; ?>
<?php  $total_amount = 0; ?>
<?php if(!empty($details)) : ?>
<?php  $no = 1; ?>
<?php  foreach($details as $rs) : ?>
		<tr id="row-<?php echo $no; ?>">
	<?php if($doc->ref_type != 4) : ?>
			<td class="middle text-center">
				<label>
					<input type="checkbox" class="ace chk" value="<?php echo $no; ?>"/>
					<span class="lbl"></span>
				</label>
			</td>
	<?php endif; ?>
			<td class="middle text-center no"><?php echo $no; ?></td>
			<td class="middle"><?php echo $rs->product_code; ?></td>
			<td class="middle"><?php echo $rs->product_name; ?></td>
			<td class="middle text-center"><?php echo $doc->ref_type == 4 ? $rs->ref_code : $rs->invoice_code; ?></td>
			<td class="middle text-center"><?php echo $rs->order_code; ?></td>
			<td class="middle text-center">
				<input type="number"
				class="form-control input-sm text-right input-price r"
				id="price-<?php echo $no; ?>"
				data-no="<?php echo $no; ?>"
				data-item="<?php echo $rs->product_code; ?>"
				value="<?php echo $rs->price; ?>"
				onkeyup="recalRow('<?php echo $no; ?>')" <?php echo $doc->ref_type == 4 ? 'readonly' : ''; ?> />
			</td>
			<td class="middle text-center"><?php echo $rs->discount_percent.' %'; ?></td>
			<td class="middle text-center"><?php echo round($rs->qty); ?></td>
			<td class="middle text-center">
				<input type="number"
					class="form-control input-sm text-right input-qty r"
					id="qty-<?php echo $no; ?>"
					data-no="<?php echo $no; ?>"
					data-ordercode="<?php echo $rs->order_code; ?>"
					data-invoice="<?php echo $rs->invoice_code; ?>"
					data-item="<?php echo $rs->product_code; ?>"
					data-itemname="<?php echo $rs->product_name; ?>"
					data-limit="<?php echo $rs->qty; ?>"
					data-vatrate="<?php echo $rs->vat_rate; ?>"
					value="<?php echo $rs->receive_qty; ?>"
					onkeyup="recalRow('<?php echo $no; ?>')" <?php echo $doc->ref_type == 4 ? 'readonly' : ''; ?>/>
			</td>

			<td class="middle text-right amount-label" id="amount-<?php echo $no; ?>">
				<?php echo number($rs->amount, 2); ?>
			</td>
			<input type="hidden" id="discount-<?php echo $no; ?>" value="<?php echo $rs->discount_percent; ?>" />
		</tr>
<?php
		$no++;
		$total_qty += $rs->qty;
		$total_amount += $rs->amount;
?>
<?php  endforeach; ?>
<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="<?php echo $doc->ref_type == 4 ?'8' : '9'; ?>" class="middle text-right">รวม</td>
					<td class="middle text-right" id="total-qty"><?php echo number($total_qty); ?></td>
					<td class="middle text-right" id="total-amount"><?php echo number($total_amount, 2); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<script id="row-template" type="text/x-handlebarsTemplate">
	<tr id="row-{{no}}">
		<td class="middle text-center">
			<label>
				<input type="checkbox" class="ace chk" value="{{no}}"/>
				<span class="lbl"></span>
			</label>
		</td>
		<td class="middle text-center no">{{no}}</td>
		<td class="middle">{{product_code}}</td>
		<td class="middle">{{product_name}}</td>
		<td class="middle text-center">{{invoice_code}}</td>
		<td class="middle text-center">{{order_code}}</td>
		<td class="middle text-center">
		<input type="number"
		class="form-control input-sm text-right input-price r"
		id="price-{{no}}"
		data-no="{{no}}"
		data-item="{{product_code}}"
		value="{{price}}"
		onkeyup="recalRow('{{no}}')" />
		</td>
		<td class="middle text-center">{{discount}} %</td>
		<td class="middle text-center">-</td>
		<td class="middle">
			<input type="number"
				class="form-control input-sm text-right input-qty r"
				id="qty-{{no}}"
				data-no="{{no}}"
				data-ordercode="{{order_code}}"
				data-invoice="{{invoice_code}}"
				data-item="{{product_code}}"
				data-itemname="{{product_name}}"
				data-limit="-1"
				data-vatrate="{{vat_rate}}"
				value="{{qty}}"
				onkeyup="recalRow('{{no}}')" />
		</td>
		<td class="middle text-right amount-label" id="amount-{{no}}">{{amountLabel}}</td>

		<input type="hidden" id="discount-{{no}}" value="{{discount}}" />
	</tr>
</script>

<script id="rows-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr id="row-{{no}}">
			<td class="middle text-center">
				<label>
					<input type="checkbox" class="ace chk" value="{{no}}"/>
					<span class="lbl"></span>
				</label>
			</td>
			<td class="middle text-center no">{{no}}</td>
			<td class="middle">{{product_code}}</td>
			<td class="middle">{{product_name}}</td>
			<td class="middle text-center">{{invoice_code}}</td>
			<td class="middle text-center">{{order_code}}</td>
			<td class="middle text-center">
			<input type="number"
			class="form-control input-sm text-right input-price r"
			id="price-{{no}}"
			data-no="{{no}}"
			data-item="{{product_code}}"
			value="{{price}}"
			onkeyup="recalRow('{{no}}')" />
			</td>
			<td class="middle text-center">{{discount}} %</td>
			<td class="middle text-center">-</td>
			<td class="middle">
				<input type="number"
					class="form-control input-sm text-right input-qty r"
					id="qty-{{no}}"
					data-no="{{no}}"
					data-ordercode="{{order_code}}"
					data-invoice="{{invoice_code}}"
					data-item="{{product_code}}"
					data-itemname="{{product_name}}"
					data-limit="-1"
					data-vatrate="{{vat_rate}}"
					value="{{qty}}"
					onkeyup="recalRow('{{no}}')" />
			</td>
			<td class="middle text-right amount-label" id="amount-{{no}}">{{amountLabel}}</td>

			<input type="hidden" id="discount-{{no}}" value="{{discount}}" />
		</tr>
	{{/each}}
</script>

<script id="invoice-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr id="row-{{no}}">
			<td class="middle text-center">
				<label>
					<input type="checkbox" class="ace chk" value="{{no}}"/>
					<span class="lbl"></span>
				</label>
			</td>
			<td class="middle text-center no">{{no}}</td>
			<td class="middle">{{product_code}}</td>
			<td class="middle">{{product_name}}</td>
			<td class="middle text-center">{{invoice_code}}</td>
			<td class="middle text-center">{{order_code}}</td>
			<td class="middle text-center">
			<input type="number"
			class="form-control input-sm text-right input-price r"
			id="price-{{no}}"
			data-no="{{no}}"
			data-item="{{product_code}}"
			value="{{price}}"
			onkeyup="recalRow('{{no}}')" />
			</td>
			<td class="middle text-center">{{discount}} %</td>
			<td class="middle text-center">{{qty}}</td>
			<td class="middle">
				<input type="number"
					class="form-control input-sm text-right input-qty r"
					id="qty-{{no}}"
					data-no="{{no}}"
					data-ordercode="{{order_code}}"
					data-invoice="{{invoice_code}}"
					data-item="{{product_code}}"
					data-itemname="{{product_name}}"
					data-limit="{{qty}}"
					data-vatrate="{{vat_rate}}"
					value=""
					onkeyup="recalRow('{{no}}')" />
			</td>
			<td class="middle text-right amount-label" id="amount-{{no}}"></td>

			<input type="hidden" id="discount-{{no}}" value="{{discount}}" />
		</tr>
	{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_add.js?v=<?php echo date('Ymd');?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_order/return_order_control.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
