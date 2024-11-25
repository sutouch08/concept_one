<?php $this->load->view('include/header'); ?>
<input type="hidden" id="required_remark" value="<?php echo $this->required_remark; ?>" />
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 visible-xs padding-5">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning top-btn" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($doc->status != 2 && $this->pm->can_delete) : ?>
				<button type="button" class="btn btn-sm btn-danger top-btn" onclick="goDelete('<?php echo $doc->code; ?>')"><i class="fa fa-trash"></i> ยกเลิก</button>
			<?php endif; ?>
			<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-success top-btn" onclick="save()"><i class="fa fa-save"></i> บันทึก</button>
			<?php	endif; ?>
    </p>
  </div>
</div>
<hr />

<form id="addForm">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-3 padding-5">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled/>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
  	<label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add) ?>" readonly />
  </div>
	<div class="col-lg-3 col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
		<label>ผู้ยืม</label>
		<input type="text" class="form-control input-sm edit" name="empName" id="empName" value="<?php echo $doc->empName; ?>" placeholder="ชื่อผู้ยืม(พนักงาน)" />
	</div>
	<div class="col-lg-6 col-md-5 col-sm-5 col-xs-12 padding-5">
  	<label>หมายเหตุ</label>
      <input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $doc->remark; ?>" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" />
  </div>
	<div class="divider-hidden"></div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>ใบยืมสินค้า</label>
		<input type="text" class="form-control input-sm text-center" name="lend_code" id="lend_code" value="<?php echo $doc->lend_code; ?>" placeholder="ระบุเลขที่ใบยืมสินค้า" disabled >
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">doc</label>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-set-code" onclick="load_lend_details()">ดึงข้อมูล</button>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-change-code" onclick="change_lend_code()">เปลี่ยน</button>
	</div>
	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-5 padding-5">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm edit" name="zone_code" id="zone_code" value="<?php echo $doc->to_zone; ?>" disabled />
	</div>
	<div class="col-lg-6-harf col-md-4-harf col-sm-4-harf col-xs-9 padding-5">
		<label>โซน[รับคืน]</label>
		<input type="text" class="form-control input-sm edit" name="zone" id="zone" value="<?php echo $doc->zone_name; ?>" placeholder="กำหนดโซนที่จะรับสินค้าเข้า" disabled >
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">chang</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-change-zone" onclick="changeZone()">เปลี่ยนโซน</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-set-zone" onclick="setZone()">ตกลง</button>
	</div>
</div>
<div class="divider"></div>
<div class="row">
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>จำนวน</label>
		<input type="number" class="form-control input-sm text-center" id="qty" value="1">
	</div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>บาร์โค้ดสินค้า</label>
		<input type="text" class="form-control input-sm text-center" id="barcode" placeholder="ยิงบาร์โค้ดสินค้า">
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">barcode</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="doReceive()">ตกลง</button>
	</div>

	<div class="col-lg-5 col-md-3 col-sm-3 col-xs-4">&nbsp;</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">add</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="receiveAll()">คืนทั้งหมด</button>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">clear</label>
		<button type="button" class="btn btn-xs btn-danger btn-block" onclick="clearAll()">เคลียร์ทั้งหมด</button>
	</div>
</div>
<hr class="margin-top-15"/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:800px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-150 middle">บาร์โคด</th>
					<th class="min-width-200 middle">สินค้า</th>
					<th class="fix-width-100 middle text-center">ยืม</th>
					<th class="fix-width-100 middle text-center">คืนแล้ว</th>
					<th class="fix-width-100 middle text-center">ค้าง</th>
					<th class="fix-width-100 middle text-center">ครั้งนี้</th>
				</tr>
			</thead>
			<tbody id="result">
<?php if(!empty($details)) : ?>
<?php 	$no = 1; ?>
<?php 	$total_lend = 0; ?>
<?php 	$total_receive = 0; ?>
<?php   $total_backlogs = 0; ?>
<?php   $total_qty = 0; ?>
<?php 	foreach($details as $rs) : ?>
				<tr>
					<input type="hidden" class="<?php echo $rs->barcode; ?>" data-no="<?php echo $no; ?>" value="<?php echo $no; ?>">
					<input type="hidden" id="lendQty-<?php echo $no; ?>" value="<?php echo $rs->lend_qty; ?>" />
					<input type="hidden" id="receivedQty-<?php echo $no; ?>" value="<?php echo $rs->receive; ?>" />
					<input type="hidden" id="backlogs-<?php echo $no; ?>" value="<?php echo $rs->backlogs; ?>" />
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle">
						<?php if($rs->barcode != "" && $rs->barcode != NULL) : ?>
							<span class="barcode" onclick="addToBarcode('<?php echo $rs->barcode; ?>')"><?php echo $rs->barcode; ?></span>
						<?php endif; ?>
					</td>
					<td class="middle"><?php echo $rs->product_code; ?></td>
					<td class="middle text-center"><?php echo number($rs->lend_qty, 2); ?></td>
					<td class="middle text-center"><?php echo number($rs->receive, 2); ?></td>
					<td class="middle text-center"><?php echo number($rs->backlogs, 2); ?></td>
					<td class="middle text-center">
						<input type="number"
						class="form-control input-sm text-right qty"
						data-product="<?php echo $rs->product_code; ?>"
						id="receiveQty-<?php echo $no; ?>" data-no="<?php echo $no; ?>"
						value="" />
					</td>
				</tr>
<?php
				$no++;
				$total_lend += $rs->lend_qty;
				$total_receive += $rs->receive;
				$total_backlogs += $rs->backlogs;
				$total_qty += $rs->qty;
?>
<?php 	endforeach; ?>
				<tr class="font-size-14">
					<td colspan="3" class="middle text-right">รวม</td>
					<td class="middle text-center"><?php echo $total_lend; ?></td>
					<td class="middle text-center"><?php echo $total_receive; ?></td>
					<td class="middle text-center"><?php echo $total_backlogs; ?></td>
					<td class="middle text-center" id="totalQty"><?php echo $total_qty; ?></td>
				</tr>
<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<input type="hidden" name="empID" id="empID" value="<?php echo $doc->empID; ?>">
<input type="hidden" name="lendCode" id="lendCode" value="<?php echo $doc->lend_code; ?>">
<input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
</form>

<?php $this->load->view('cancle_modal'); ?>

<script id="template" type="text/x-handlebarsTemplate">
{{#each details}}
	{{#if nodata}}
		<tr>
			<td colspan="7" class="middle text-center">ไม่พบข้อมูล</td>
		</tr>
	{{else}}
		{{#if @last}}
			<tr class="font-size-14">
				<td colspan="3" class="middle text-right">รวม</td>
				<td class="middle text-center">{{totalLend}}</td>
				<td class="middle text-center">{{totalReceived}}</td>
				<td class="middle text-right">{{totalBacklogs}}</td>
				<td class="middle text-center" id="totalQty">0</td>
			</tr>
		{{else}}
			<tr>
				<input type="hidden" class="{{barcode}}" data-no="{{no}}" value="{{no}}">
				<input type="hidden" id="lendQty-{{no}}" value="{{lendQty}}" />
				<input type="hidden" id="receivedQty-{{no}}" value="{{received}}" />
				<input type="hidden" id="backlogs-{{no}}" value="{{backlogs}}" />

				<td class="middle text-center no">{{no}}</td>
				<td class="middle">
					{{#if barcode}}
					<span class="barcode" onClick="addToBarcode('{{barcode}}')">{{barcode}}</span>
					{{/if}}
				</td>
				<td class="middle">{{itemCode}}</td>
				<td class="middle text-center">{{lendQtyLabel}}</td>
				<td class="middle text-center">{{receivedLabel}}</td>
				<td class="middle text-center">{{backlogsLabel}}</td>
				<td class="middle text-center">
				{{#if backlogs}}
					<input type="number"
					class="form-control input-sm text-right qty"
					data-product="{{itemCode}}"
					id="receiveQty-{{no}}"
					data-no="{{no}}"
					value="" />
				{{/if}}
				</td>
			</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>



<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
