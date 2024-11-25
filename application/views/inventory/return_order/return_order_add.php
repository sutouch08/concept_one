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
<?php if($this->pm->can_add) : ?>
				<button type="button" class="btn btn-sm btn-purple btn-100" onclick="save(1)">Save As Draft</button>
				<button type="button" class="btn btn-sm btn-success btn-100" onclick="save(0)">Save</button>
<?php endif; ?>
    </p>
  </div>
</div>
<hr />

<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm text-center" id="code" value="" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center h" name="date_add" id="dateAdd" value="<?php echo date('d-m-Y'); ?>" readonly />
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center h" name="customer_code" id="customer_code" value=""  />
	</div>
	<div class="col-lg-8 col-md-6-harf col-sm-5 col-xs-8 padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="form-control input-sm h" name="customer_name" id="customer_name" value="" />
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>อ้างอิง</label>
		<input type="text" class="form-control input-sm h" name="reference" id="reference" value="" />
	</div>

	<div class="col-lg-10-harf col-md-10 col-sm-10-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm h" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value=""  />
	</div>

	<div class="divider">	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>ใบกำกับ[SAP]</label>
		<input type="text" class="form-control input-sm text-center h"	name="invoice" id="invoice" value="" placeholder="ค้นหาใบกำกับ" />
		<input type="hidden" id="invoice-code" />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
		<label class="display-block not-show">confirm</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-confirm-inv" onclick="loadInvoice()">ยืนยัน</button>
		<button type="button" class="btn btn-xs btn-warning btn-block hide" id="btn-clear-inv" onclick="clearInvoice()">Clear</button>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
		<label>คลังสินค้า</label>
		<select class="form-control input-sm h" name="warehouse" id="warehouse" onchange="zoneInit(1)">
			<option value="">เลือก</option>
			<?php echo select_common_warehouse(); ?>
		</select>
	</div>
	<div class="col-lg-2-harf col-md-3 col-sm-3-harf col-xs-6 padding-5">
		<label>โซนรับสินค้า</label>
		<input type="text" class="form-control input-sm h" name="zone_code" id="zone_code" placeholder="รหัสโซน" value="" />
	</div>
	<div class="col-lg-5 col-md-4 col-sm-5 col-xs-6 padding-5">
		<label class="not-show">zone</label>
		<input type="text" class="form-control input-sm zone h" name="zone" id="zone" placeholder="ชื่อโซน" value="" />
	</div>
</div>

<input type="hidden" name="warehouse_code" id="warehouse_code" value=""/>
<input type="hidden" name="invoice_code" id="invoice_code" value="" />
<input type="hidden" name="allow_return_no_inv" id="allow-return-no-inv" value="<?php echo getConfig('ALLOW_RETURN_NO_INV'); ?>" />

<hr class="margin-top-10 margin-bottom-10"/>
<?php $this->load->view('inventory/return_order/return_order_control'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="margin-bottom:0px; min-width:1220px;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">
						<input type="checkbox" id="chk-all" class="ace" onchange="toggleCheckAll($(this))"/>
						<span class="lbl"></span>
					</th>
					<th class="fix-width-40 text-center">ลำดับ</th>
					<th class="fix-width-175">รหัส</th>
					<th class="min-width-200">สินค้า</th>
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

			</tbody>
			<tfoot>
				<tr>
					<td colspan="9" class="middle text-right">รวม</td>
					<td class="middle text-right" id="total-qty"></td>
					<td class="middle text-right" id="total-amount"></td>
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
