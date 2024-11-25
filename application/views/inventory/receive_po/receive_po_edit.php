<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>assets/js/xlsx.full.min.js"></script>
<?php if($doc->status == 0) : ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?> </h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-xs btn-warning btn-100" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
			<?php if($this->pm->can_edit) : ?>
				<button type="button" class="btn btn-xs btn-purple btn-100" onclick="saveAsDraft()">Save As Draft</button>
				<button type="button" class="btn btn-xs btn-success btn-100" onclick="validateData()">Save</button>
			<?php	endif; ?>
    </p>
  </div>
</div>
<hr />
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เลขที่เอกสาร</label>
		<input type="text" class="form-control input-sm" value="<?php echo $doc->code; ?>" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<input type="text" class="form-control input-sm text-center" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add); ?>" readonly/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>รหัสผู้ขาย</label>
		<input type="text" class="form-control input-sm text-center" name="vendor_code" id="vendor_code" value="<?php echo $doc->vendor_code; ?>" placeholder="รหัสผู้ขาย" autofocus/>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-4-harf col-xs-6 padding-5">
		<label>ชื่อผู้ขาย</label>
		<input type="text" class="form-control input-sm" name="vendorName" id="vendorName" value="<?php echo $doc->vendor_name; ?>" placeholder="ชื่อผู้ขาย" />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label>ใบส่งสินค้า</label>
		<input type="text" class="form-control input-sm text-center" name="invoice" id="invoice" value="<?php echo $doc->invoice_code; ?>" placeholder="ใบส่งสินค้า" />
	</div>
	<div class="divider">	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>ใบสั่งซื้อ</label>
		<input type="text" class="form-control input-sm text-center"	name="poCode" id="poCode" value="<?php echo $doc->po_code; ?>" placeholder="ค้นหาใบสั่งซื้อ" <?php echo empty($doc->po_code) ? "" : "disabled"; ?>/>
		<input type="hidden" id="po-code" />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">confirm</label>
		<button type="button" class="btn btn-xs btn-primary btn-block <?php echo empty($doc->po_code) ? "" : "hide"; ?>" id="btn-confirm-po" onclick="confirmPO()">ยืนยัน</button>
		<button type="button" class="btn btn-xs btn-primary btn-block <?php echo empty($doc->po_code) ? "hide" : ""; ?>" id="btn-get-po" onclick="getPoDetail()">แสดงรายการ</button>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">confirm</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-clear-po" onclick="clearPo()">Clear</button>
	</div>

	<div class="col-lg-2-harf col-md-3 col-sm-3-harf col-xs-6 padding-5">
		<label>โซนรับสินค้า</label>
		<input type="text" class="form-control input-sm" name="zone_code" id="zone_code" placeholder="รหัสโซน" value="<?php echo $doc->zone_code; ?>" />
	</div>
	<div class="col-lg-4 col-md-5 col-sm-3-harf col-xs-6 padding-5">
		<label class="not-show">zone</label>
		<input type="text" class="form-control input-sm zone" name="zoneName" id="zoneName" placeholder="ชื่อโซน" value="<?php echo $doc->zone_name; ?>" />
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Currency</label>
		<select class="form-control input-sm width-100" id="DocCur" onchange="changeRate()">
			<?php echo select_currency($doc->currency); ?>
		</select>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Rate</label>
		<input type="number" class="form-control input-sm text-center" id="DocRate" value="<?php echo $doc->rate; ?>" />
	</div>


	<input type="hidden" name="receive_code" id="receive_code" value="<?php echo $doc->code; ?>" />
	<input type="hidden" id="zone-code" value="<?php echo $doc->zone_code; ?>" />
	<input type="hidden" name="approver" id="approver" value="" />
	<input type="hidden" id="allow_over_po" value="<?php echo getConfig('ALLOW_RECEIVE_OVER_PO'); ?>">
	<input type="hidden" id="purchase-vat-code" value="<?php echo getConfig('PURCHASE_VAT_CODE'); ?>" />
	<input type="hidden" id="purchase-vat-rate" value="<?php echo getConfig('PURCHASE_VAT_RATE'); ?>" />
	<input type="hidden" id="no" value="<?php echo (empty($details) ? '0' : count($details)); ?>" />
</div>
<hr class="margin-top-10 margin-bottom-10"/>

<?php $this->load->view('inventory/receive_po/receive_control'); ?>
<?php $this->load->view('inventory/receive_po/receive_po_detail'); ?>



<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:500px;">
	 <div class="modal-content">
			 <div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			 <h4 class="modal-title">Import File</h4>
			</div>
			<div class="modal-body">
				<form id="upload-form" name="upload-form" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="col-sm-9">
						<button type="button" class="btn btn-sm btn-primary btn-block" id="show-file-name" onclick="getFile()">กรุณาเลือกไฟล์ Excel</button>
					</div>

					<div class="col-sm-3">
						<button type="button" class="btn btn-sm btn-info" onclick="uploadfile()"><i class="fa fa-cloud-upload"></i> นำเข้า</button>
					</div>
				</div>
				<input type="file" class="hide" name="uploadFile" id="uploadFile" accept=".xlsx" />
				</form>
			 </div>
			<div class="modal-footer">

			</div>
	 </div>
 </div>
</div>


<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/validate_credentials.js"></script>


<script id="template" type="text/x-handlebarsTemplate">
<table class="table table-striped table-bordered" style="min-width:1110px;">
	<thead>
		<tr class="font-size-12">
			<th class="fix-width-40 text-center">ลำดับ</th>
			<th class="fix-width-200">รหัสสินค้า</th>
			<th class="" style="min-width:250px; max-width:350px;">ชื่อสินค้า</th>
			<th class="fix-width-100 text-center">ราคา (PO)</th>
			<th class="fix-width-100 text-center">สั่งซื้อ</th>
			<th class="fix-width-100 text-center">ค้างรับ</th>
			<th class="fix-width-100 text-center" style="width:100px !important;">จำนวน</th>
			<th calss="fix-width-120 text-center" style="width:120px !important;">มูลค่า</th>
		</tr>
	</thead>
	<tbody>
	{{#each this}}
	{{#if @last}}
		<tr>
			<td colspan="4" class="middle text-right"><strong>รวม</strong></td>
			<td class="middle text-center">{{qty}}</td>
			<td class="middle text-center">{{backlog}}</td>
			<td class="middle text-center"><span id="total-receive"></span></td>
			<td class="middle text-right"><span id="total-amount"></span></td>
		</tr>
	{{else}}
		<tr class="font-size-12">
			<td class="middle text-center no">{{no}}</td>
			<td class="middle">{{pdCode}}</td>
			<td class="middle">{{pdName}}</td>
			<td class="middle text-right">{{price_label}} <span style="font-size:10px;">{{currency}}</span></td>
			<td class="middle text-center" id="qty_{{uid}}">{{qty_label}}</td>
			<td class="middle text-center">{{backlog_label}}</td>
			<td class="middle text-center">
				<input type="number" class="form-control input-sm text-center receive-box pdCode" name="receive[{{uid}}]" id="receive_{{uid}}" data-uid="{{uid}}" value="" />
			</td>
			<td class="middle text-right" id="line_total_{{uid}}">0.00</td>
				<input type="hidden" id="limit_{{uid}}" value="{{limit}}"/>
				<input type="hidden" id="backlog_{{uid}}" value="{{backlog}}" />
				{{#if barcode}}
					<input type="hidden" class="{{barcode}}" id="bc-{{uid}}" data-limit="{{limit}}" value="{{uid}}" />
				{{/if}}
				{{#if isOpen}}
					<input type="hidden" name="items[{{uid}}]" id="item_{{uid}}" value="{{pdCode}}" />
					<input type="hidden" name="prices[{{uid}}]" id="price_{{uid}}" value="{{price}}" />
					<input type="hidden" name="currency[{{uid}}]" id="currency_{{uid}}" value="{{currency}}" />
					<input type="hidden" name="rate[{{uid}}]" id="rate_{{uid}}" value="{{Rate}}" />
					<input type="hidden" name="vatGroup[{{uid}}]" id="vatGroup_{{uid}}" value="{{vatGroup}}">
					<input type="hidden" name="vatRate[{{uid}}]" id="vatRate_{{uid}}" value="{{vatRate}}">
					<input type="hidden" name="docEntry-{{uid}}" id="docEntry_{{uid}}" value="{{docEntry}}" />
					<input type="hidden" name="lineNum-{{uid}}" id="lineNum_{{uid}}" value="{{lineNum}}" />
				{{/if}}
		</tr>
	{{/if}}
	{{/each}}
	</tbody>
</table>
</script>

<script id="request-template" type="text/x-handlebarsTemplate">
<table class="table table-striped table-bordered" style="min-width:1000px;">
	<thead>
		<tr class="font-size-12">
			<th class="fix-width-40 text-center">ลำดับ</th>
			<th class="fix-width-120 text-center">บาร์โค้ด</th>
			<th class="fix-width-150">รหัสสินค้า</th>
			<th class="min-width-200">ชื่อสินค้า</th>
			<th class="fix-width-100 text-center">สั่งซื้อ</th>
			<th class="fix-width-100 text-center">อนุมัติรับ</th>
			<th class="fix-width-100 text-center">ค้างรับ</th>
			<th class="fix-width-100 text-center">จำนวน</th>
		</tr>
	</thead>
	<tbody id="receiveTable">
	{{#each this}}
	{{#if @last}}
		<tr>
			<td colspan="4" class="middle text-right"><strong>รวม</strong></td>
			<td class="middle text-center">{{totalQty}}</td>
			<td class="middle text-center">{{totalRequest}}</td>
			<td class="middle text-center">{{totalBacklog}}</td>
			<td class="middle text-center"><span id="total-receive"></span></td>
		</tr>
	{{else}}
		<tr class="font-size-12">
			<td class="middle text-center no">{{ no }}</td>
			<td class="middle barcode" id="barcode_{{uid}}">{{barcode}}</td>
			<td class="middle">{{pdCode}}</td>
			<td class="middle">{{pdName}}</td>
			<td class="middle text-center" id="qty_{{uid}}">{{qty_label}}</td>
			<td class="middle text-center">{{request_qty_label}}</td>
			<td class="middle text-center">{{backlog_label}}</td>
			<td class="middle text-center">
				<input type="hidden" id="backlog_{{uid}}" value="{{backlog}}" />
				<input type="hidden" id="limit_{{uid}}" value="{{limit}}"/>
				{{#if barcode}}
					<input type="hidden" class="{{barcode}}" id="bc-{{uid}}" data-limit="{{limit}}" value="{{uid}}" />
				{{/if}}

				{{#if isOpen}}
					<input type="number" class="form-control input-sm text-center receive-box pdCode" name="receive[{{uid}}]" id="receive_{{uid}}" data-uid="{{uid}}" value="{{receive_qty}}" />
					<input type="hidden" name="items[{{uid}}]" id="item_{{uid}}" value="{{pdCode}}" />
					<input type="hidden" name="prices[{{uid}}]" id="price_{{uid}}" value="{{price}}" />
					<input type="hidden" name="currency[{{uid}}]" id="currency_{{uid}}" value="{{currency}}" />
					<input type="hidden" name="rate[{{uid}}]" id="rate_{{uid}}" value="{{Rate}}" />
					<input type="hidden" name="vatGroup[{{uid}}]" id="vatGroup_{{uid}}" value="{{vatGroup}}">
					<input type="hidden" name="vatRate[{{uid}}]" id="vatRate_{{uid}}" value="{{vatRate}}">
					<input type="hidden" name="docEntry-{{uid}}" id="docEntry_{{uid}}" value="{{docEntry}}" />
					<input type="hidden" name="lineNum-{{uid}}" id="lineNum_{{uid}}" value="{{lineNum}}" />
				{{/if}}
			</td>
		</tr>
	{{/if}}
	{{/each}}

	</tbody>
</table>
</script>


<?php else : ?>
  <?php redirect($this->home.'/view_detail/'.$document->code); ?>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<?php $this->load->view('include/footer'); ?>
