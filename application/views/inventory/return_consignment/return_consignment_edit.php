<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    	<h3 class="title" >
        <?php echo $this->title; ?>
      </h3>
	</div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="save()"><i class="fa fa-save"></i> บันทึก</button>
			<?php endif; ?>
	<?php if($doc->status == 1 && $this->pm->can_approve) : ?>
				<button type="button" class="btn btn-sm btn-primary" id="btn-approve" onclick="approve()"><i class="fa fa-save"></i> อนุมัติ</button>
	<?php endif; ?>
      </p>
    </div>
</div>
<hr />


<div class="row">
    <div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
    </div>
		<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center edit" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled/>
    </div>
		<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
			<label>รหัสลูกค้า</label>
			<input type="text" class="form-control input-sm text-center edit" id="customerCode" value="<?php echo $doc->customer_code; ?>" disabled />
		</div>
		<div class="col-sm-5 col-xs-12 padding-5">
			<label>ลูกค้า</label>
			<input type="text" class="form-control input-sm edit" name="customer" id="customer" value="<?php echo $doc->customer_name; ?>" disabled/>
		</div>
		<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
			<label>เลขที่บิล[SAP]</label>
			<input type="text" class="form-control input-sm text-center edit" name="invoice" id="invoice" value="<?php echo $doc->invoice; ?>" disabled />
		</div>
		<div class="col-sm-1 col-xs-6 padding-5">
			<label>GP(%)</label>
			<input type="number" class="form-control input-sm text-center edit" name="gp" id="gp" value="<?php echo $doc->gp; ?>" disabled />
		</div>

		<div class="col-sm-6 col-xs-12 padding-5">
			<label>โซนฝากขาย</label>
			<input type="text" class="form-control input-sm edit" name="fromZone" id="fromZone" value="<?php echo $doc->from_zone_name; ?>" disabled />
		</div>

		<div class="col-sm-6 col-xs-12 padding-5">
			<label>โซน[รับคืน]</label>
			<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" disabled />
		</div>
		<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
			<label>รับที่</label>
			<select class="form-control input-sm edit" name="is_wms" id="is_wms" onchange="toggleInterface()" disabled>
				<option value="1" <?php echo is_selected("1", $doc->is_wms); ?>>Pioneer</option>
				<option value="0" <?php echo is_selected("0", $doc->is_wms); ?>>Warrix</option>
			</select>
		</div>
		<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
			<label>Interface</label>
			<select class="form-control input-sm edit" name="is_api" id="is_api" disabled>
				<option value="1" <?php echo is_selected("1", $doc->is_api); ?>>ส่ง</option>
				<option value="0" <?php echo is_selected("0", $doc->is_api); ?>>ไม่ส่ง</option>
			</select>
		</div>
    <div class="col-sm-8 col-xs-12 padding-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm edit" name="remark" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled />
    </div>
		<div class="col-sm-1 col-xs-6 padding-5">
			<label class="display-block not-show">save</label>
			<?php 	if($doc->status == 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
							<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="editHeader()">แก้ไข</button>
							<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateHeader()">ปรับปรุง</button>
			<?php	endif; ?>
		</div>


</div>

<input type="hidden" id="return_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="code" name="code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="customer_code" name="customer_code" value="<?php echo $doc->customer_code; ?>" />
<input type="hidden" name="zone_code" id="zone_code" value="<?php echo $doc->zone_code; ?>" />
<input type="hidden" name="from_zone_code" id="from_zone_code" value="<?php echo $doc->from_zone_code; ?>" />
<input type="hidden" name="invoice_code" id="invoice_code" value="<?php echo $doc->invoice; ?>" />

<hr class="margin-top-15"/>
<div class="row">
	<div class="col-sm-4 col-xs-12 padding-5">
		<label>เลขที่บิล</label>
		<span id="invoice_list" class="form-control input-sm" disabled><?php echo $doc->invoice_list; ?></span>
	</div>
	<div class="col-sm-2 col-xs-6 padding-5">
		<label>มูลค่าบิล</label>
		<input type="number" class="form-control input-sm text-center" name="bill_amount" id="bill_amount" value="<?php echo $doc->invoice_amount; ?>" disabled />
	</div>
	<div class="col-sm-2 col-xs-6 padding-5">
		<label>เพิ่มบิล[SAP]</label>
		<input type="text" class="form-control input-sm text-center" id="invoice-box" placeholder="ดึงใบกำกับเพิ่มเติม" />
	</div>
	<div class="col-sm-1 col-xs-12 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-info btn-block" onclick="add_invoice()">เพิ่มบิล</button>
	</div>
	<div class="col-md-1 col-sm-1 hidden-xs"></div>

	<div class="divider visible-xs"></div>

	<div class="col-md-2 col-sm-3 col-xs-12 padding-5">
		<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
			<label class="display-block not-show">btn</label>
			<button type="button" class="btn btn-xs btn-primary btn-block" onclick="load_stock_in_zone()">
				โหลดสินค้าในโซน
			</button>
		<?php endif; ?>
	</div>
</div>
<div class="row">


</div>
<hr class="margin-top-10 margin-bottom-10"/>
<?php $this->load->view('inventory/return_consignment/return_consignment_control'); ?>

<form id="detailsForm" method="post" action="<?php echo $this->home.'/add_details/'.$doc->code; ?>">
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="margin-bottom:0px; min-width:900px;">
			<thead>
				<tr>
					<th class="width-5 text-center">ลำดับ</th>
					<th class="width-5 text-center">
					<input type="checkbox" id="chk-all" class="ace" onchange="toggleCheckAll($(this))"/>
					<span class="lbl"></span>
					</th>
					<th class="width-10">บาร์โค้ด</th>
					<th class="">สินค้า</th>
					<th class="width-8 text-center">อ้างอิง</th>
					<th class="width-8 text-right">ราคา</th>
					<th class="width-8 text-right">ส่วนลด(%)</th>
					<th class="width-8 text-right">คืน</th>
					<th class="width-8 text-right">มูลค่า</th>
					<th class="width-5"></th>
				</tr>
			</thead>
			<tbody id="detail-table">
<?php  $total_qty = 0; ?>
<?php  $total_amount = 0; ?>
<?php  $no = 0; ?>
<?php if(!empty($details)) : ?>
<?php  foreach($details as $rs) : ?>
	<?php $no++; ?>
				<tr id="row_<?php echo $no; ?>">
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle text-center">
						<input type="checkbox" class="chk ace" data-id="<?php echo $rs->id; ?>" value="<?php echo $no; ?>">
						<span class="lbl"></span>
					</td>
					<td class="middle <?php echo $no; ?>"><?php echo $rs->barcode; ?></td>
					<td class="middle"><?php echo $rs->product_code .' : '.$rs->product_name; ?></td>
					<td class="middle text-center"><?php echo $rs->invoice_code; ?>	</td>
					<td class="middle text-right">
						<input type="number"
							class="form-control input-sm text-right"
							name="price[<?php echo $no; ?>]"
							id="price_<?php echo $no; ?>"
							value="<?php echo $rs->price; ?>"
							onkeyup="recalRow(<?php echo $no; ?>)" />
					</td>
					<td class="middle text-right">
						<input type="number"
							class="form-control input-sm text-right"
							name="discount[<?php echo $no; ?>]"
							id="discount_<?php echo $no; ?>"
							value="<?php echo round($rs->discount_percent, 2); ?>"
							onkeyup="recalRow(<?php echo $no; ?>)"
						/>
					</td>
					<td class="middle">
						<input type="number"
							class="form-control input-sm text-right input-qty"
							name="qty[<?php echo $no; ?>]"
							id="qty_<?php echo $no; ?>"
							value="<?php echo $rs->qty; ?>"
							data-no = "<?php echo $no; ?>"
							onkeyup="recalRow(<?php echo $no; ?>)"
						/>
					</td>
					<td class="middle text-right amount-label" id="amount_<?php echo $no; ?>">
						<?php echo number($rs->amount,2); ?>
					</td>
					<td class="middle text-center">
						<button type="button" class="btn btn-minier btn-danger"	onclick="removeRow(<?php echo $no; ?>, <?php echo $rs->id; ?>)">
							<i class="fa fa-trash"></i>
						</button>
					</td>
					<input type="hidden" id="barcode_<?php echo (empty($rs->barcode) ? $rs->product_code : $rs->barcode); ?>" value="<?php echo $no; ?>"/>
					<input type="hidden" name="item[<?php echo $no; ?>]" id="item_<?php echo $no; ?>" value="<?php echo $rs->product_code; ?>"/>
					<input type="hidden" name="item_name[<?php echo $no; ?>]" id="item_name_<?php echo $no; ?>" value="<?php echo $rs->product_name; ?>" />
					<!--
					<input type="hidden" class="input-price" name="price[<?php echo $no; ?>]" id="price_<?php echo $no; ?>" value="<?php echo $rs->price; ?>" />
				-->

				</tr>
<?php
	$total_qty += $rs->qty;
	$total_amount += $rs->amount;
?>
<?php  endforeach; ?>
<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="7" class="middle text-right">รวม</td>
					<td class="middle widht-10 text-right" id="total-qty"><?php echo number($total_qty); ?></td>
					<td class="middle width-10 text-right" id="total-amount"><?php echo number($total_amount, 2); ?></td>
					<td class="width-5"></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<input type="hidden" id="no" value="<?php echo $no; ?>"/>
</form>


<script type="text/x-handlebarsTemplate" id="row-template">
	<tr id="row_{{no}}">
		<td class="middle text-center no"></td>
		<td class="middle text-center">
			<input type="checkbox" class="chk ace" data-id="" value="{{no}}">
			<span class="lbl"></span>
		</td>
		<td class="middle {{no}}">{{barcode}}</td>
		<td class="middle">{{code}} : {{name}}</td>
		<td class="middle text-center invoice">{{invoice}}</td>
		<td class="middle text-right">
			<input type="number"
				class="form-control input-sm text-right"
				name="price[{{no}}]"
				id="price_{{no}}"
				value="{{price}}"
				onkeyup="recalRow({{no}})" />
		</td>
		<td class="middle text-right">
			<input type="number"
			class="form-control input-sm text-right"
			name="discount[{{no}}]"
			id="discount_{{no}}"
			value="{{discount}}"
			onkeyup="recalRow({{no}})"
			/>
		</td>
		<td class="middle">
			<input type="number"
			class="form-control input-sm text-right input-qty"
			name="qty[{{no}}]"
			id="qty_{{no}}"
			value="{{qty}}"
			data-no = "{{no}}"
			onkeyup="recalRow({{no}})"
			/>
		</td>
		<td class="middle text-right amount-label" id="amount_{{no}}">{{amount}}</td>
		<td class="middle text-center">
			<button type="button" class="btn btn-minier btn-danger" onclick="removeRow({{no}}, 0)">
			<i class="fa fa-trash"></i>
			</button>
		</td>
		<input type="hidden" id="barcode_{{barcode}}" value="{{no}}"/>
		<input type="hidden" name="item[{{no}}]" id="item_{{no}}" value="{{code}}"/>
		<input type="hidden" name="item_name[{{no}}]" id="item_name_{{no}}" value="{{name}}"/>		
	</tr>
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_consignment/return_consignment_control.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
