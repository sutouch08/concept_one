<?php $this->load->view('include/header'); ?>
<?php if($document->status == 0) : ?>
<div class="row">
	<div class="col-sm-6">
    	<h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
    <div class="col-sm-6">
    <p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="leave()"><i class="fa fa-arrow-left"></i> กลับ</button>
    <?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-success" onclick="checkLimit()"><i class="fa fa-save"></i> บันทึก</button>
    <?php	endif; ?>
    </p>
    </div>
</div>
<hr />

<div class="row">
  <div class="col-sm-1 col-1-harf padding-5 first">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $document->code; ?>" disabled />
  </div>
	<div class="col-sm-1 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center header-box" name="date_add" id="dateAdd" value="<?php echo thai_date($document->date_add); ?>" disabled />
  </div>
	<div class="col-sm-8 col-8-harf padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm header-box" name="remark" id="remark" value="<?php echo $document->remark; ?>" disabled />
	</div>
	<div class="col-sm-1 padding-5 last">
<?php if($this->pm->can_edit && $document->status == 0) : ?>
		<label class="display-block not-show">edit</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="editHeader()">
			<i class="fa fa-pencil"></i> แก้ไข
		</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateHeader()">
			<i class="fa fa-save"></i> อัพเดต
		</button>
<?php endif; ?>
	</div>

</div>
<hr class="margin-top-10 margin-bottom-10"/>
<form id="receiveForm" method="post" action="<?php echo $this->home; ?>/save">
<div class="row">
  <div class="col-sm-3 padding-5 first">
    	<label>ผู้จำหน่าย</label>
        <input type="text" class="form-control input-sm" name="vendorName" id="vendorName" placeholder="ระบุผู้จำหน่าย" />
    </div>

	<div class="col-sm-2 padding-5">
    	<label>ใบสั่งซื้อ</label>
        <input type="text" class="form-control input-sm text-center" name="poCode" id="poCode" placeholder="ค้นหาใบสั่งซื้อ" />
        <span class="help-block red" id="po-error"></span>
    </div>
		<div class="col-sm-1 padding-5">
			<label class="display-block not-show">clear</label>
			<button type="button" class="btn btn-xs btn-info btn-block hide" id="btn-change-po" onclick="changePo()">เปลี่ยน</button>
			<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-get-po" onclick="getData()">ยืนยัน</button>
		</div>
    <div class="col-sm-2 padding-5">
    	<label>ใบส่งสินค้า</label>
        <input type="text" class="form-control input-sm text-center" name="invoice" id="invoice" placeholder="อ้างอิงใบส่งสินค้า" />
        <span class="help-block red" id="invoice-error"></span>
    </div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>Currency</label>
			<select class="form-control input-sm width-100" id="DocCur" onchange="changeRate()" disabled>
				<?php echo select_currency("THB"); ?>
			</select>
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
			<label>Rate</label>
			<input type="number" class="form-control input-sm text-center" id="DocRate" value="1.00" disabled/>
		</div>

</div>
<hr class="margin-top-15 margin-bottom-15"/>
<input type="hidden" name="vendor_code" id="vendor_code" />
<input type="hidden" name="receive_code" id="receive_code" value="<?php echo $document->code; ?>" />
<input type="hidden" name="approver" id="approver" value="" />
<input type="hidden" name="allow_over_po" id="allow_over_po" value="<?php echo $allow_over_po; ?>" />

<div class="row">
	<div class="col-sm-12">
    	<table class="table table-striped table-bordered">
        	<thead>
            	<tr class="font-size-12">
                	<th class="width-5 text-center">ลำดับ	</th>
                    <th class="width-15 text-center">บาร์โค้ด</th>
                    <th class="width-15 text-center">รหัสสินค้า</th>
                    <th class="width-35">ชื่อสินค้า</th>
                    <th class="width-10 text-center">สั่งซื้อ</th>
                    <th class="width-10 text-center">ค้างรับ</th>
                    <th class="width-10 text-center">จำนวน</th>
                </tr>
            </thead>
            <tbody id="receiveTable">

			      </tbody>
        </table>
    </div>
</div>
</form>

<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-dialog input-xlarge">
    <div class="modal-content">
      <div class="modal-header">
      	<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
		    <h4 class='modal-title-site text-center' > ผู้มีอำนาจอนุมัติรับสินค้าเกิน </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
          	<input type="password" class="form-control input-sm text-center" id="sKey" />
            <span class="help-block red text-center" id="approvError">&nbsp;</span>
          </div>
          <div class="col-sm-12">
            <button type="button" class="btn btn-sm btn-primary btn-block" onclick="doApprove()">อนุมัติ</button>
          </div>
        </div>
    	 </div>
      </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>scripts/validate_credentials.js"></script>
<script id="template" type="text/x-handlebarsTemplate">
{{#each this}}
	{{#if @last}}
        <tr>
            <td colspan="4" class="middle text-right"><strong>รวม</strong></td>
            <td class="middle text-center">{{qty}}</td>
            <td class="middle text-center">{{backlog}}</td>
            <td class="middle text-center"><span id="total-receive">0</span></td>
        </tr>
    {{else}}
        <tr class="font-size-12">
            <td class="middle text-center">{{ no }}</td>
            <td class="middle barcode" id="barcode_{{uid}}">{{barcode}}</td>
            <td class="middle">{{pdCode}}</td>
            <td class="middle">{{pdName}}</td>
            <td class="middle text-center" id="qty_{{uid}}">{{qty}} </td>
            <td class="middle text-center">{{backlog}}</td>
            <td class="middle text-center">
							{{#if isOpen}}
                <input type="text" class="form-control input-sm text-center receive-box pdCode" name="receive[{{pdCode}}]" id="receive_{{uid}}" data-uid="{{uid}}" />
								<input type="hidden" name="docEntry_{{uid}}" id="docEntry_{{uid}}" value="{{docEntry}}" />
								<input type="hidden" name="lineNum_{{uid}}" id="lineNum_{{uid}}" value="{{lineNum}}" />
								<input type="hidden" name="pdCode_{{uid}}" id="pdCode_{{uid}}" value="{{pdCode}}" />
								<input type="hidden" name="pdName_{{uid}}" id="pdName_{{uid}}" value="{{pdName}}" />
								<input type="hidden" name="currency[{{uid}}]" id="currency_{{uid}}" value="{{currency}}" />
								<input type="hidden" name="rate[{{uid}}]" id="rate_{{uid}}" value="{{Rate}}" />
								<input type="hidden" name="vatGroup[{{uid}}]" id="vatGroup_{{uid}}" value="{{vatGroup}}">
								<input type="hidden" name="vatRate[{{uid}}]" id="vatRate_{{uid}}" value="{{vatRate}}">
								<input type="hidden" id="price_{{uid}}" value="{{price}}" />
								<input type="hidden" id="limit_{{uid}}" value="{{limit}}"/>
								<input type="hidden" id="backlog_{{uid}}" value="{{backlog}}" />
	      				{{#if barcode}}
	      				<input type="hidden" class="{{barcode}}" value="{{uid}}" />
	      				{{/if}}
							{{/if}}
            </td>
        </tr>
    {{/if}}
{{/each}}
</script>

<?php else : ?>
  <?php redirect($this->home.'/view_detail/'.$document->code); ?>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po_request/receive_po_request.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_po_request/receive_po_request_add.js?v=1"></script>

<?php $this->load->view('include/footer'); ?>
