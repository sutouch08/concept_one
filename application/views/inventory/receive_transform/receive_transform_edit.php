<?php $this->load->view('include/header'); ?>
<?php if($doc->status == 0) : ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?> </h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
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
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
  	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center header-box" name="date_add" id="dateAdd" value="<?php echo thai_date($doc->date_add); ?>" disabled />
  </div>

	<div class="col-lg-8 col-md-7-harf col-sm-6-harf col-xs-9 padding-5">
		<label>หมายเหตุ</label>
		<input type="text" class="form-control input-sm header-box" name="remark" id="remark" value="<?php echo $doc->remark; ?>" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
<?php if($this->pm->can_edit && $doc->status == 0) : ?>
		<label class="display-block not-show">edit</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="editHeader()">
			<i class="fa fa-pencil"></i> แก้ไข
		</button>
		<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="updateHeader()">
			<i class="fa fa-save"></i> อัพเดต
		</button>
<?php endif; ?>
	</div>

	<input type="hidden" id="required-remark" value="<?php echo $this->required_remark ? 1 : 0; ?>" />

</div>
<hr class="margin-top-10 margin-bottom-10"/>
<form id="receiveForm" method="post" action="<?php echo $this->home; ?>/save">
<div class="row">
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
  	<label>ใบเบิกสินค้า</label>
    <input type="text" class="form-control input-sm text-center" name="order_code" id="order_code" placeholder="ค้นหาใบสั่งซื้อ" value="<?php echo $doc->order_code; ?>" <?php echo ($doc->order_code == "" ? "" : "disabled"); ?>/>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
  	<label>ใบสั่งงาน</label>
    <input type="text" class="form-control input-sm text-center" name="so_code" id="so_code" placeholder="อ้างอิงใบสั่งงาน" value="<?php echo $doc->so_code; ?>" />
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-2 padding-5">
		<label class="display-block not-show">clear</label>
		<button type="button" class="btn btn-xs btn-info btn-block <?php echo ($doc->order_code == '' ? 'hide' : ''); ?>" id="btn-change-po" onclick="changePo()">เปลี่ยน</button>
		<button type="button" class="btn btn-xs btn-primary btn-block <?php echo ($doc->order_code == '' ? '' : 'hide'); ?>" id="btn-get-po" onclick="getData()">ยืนยัน</button>
	</div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
  	<label>ใบส่งสินค้า</label>
    <input type="text" class="form-control input-sm text-center" name="invoice" id="invoice" placeholder="อ้างอิงใบส่งสินค้า" value="<?php echo $doc->invoice_code; ?>" />
  </div>
	<div class="col-lg-2-harf col-md-3 col-sm-2-harf col-xs-6 padding-5">
  	<label>โซนรับสินค้า</label>
    <input type="text" class="form-control input-sm" name="zone_code" id="zone_code" placeholder="รหัสโซน" value="<?php echo $doc->zone_code; ?>"/>
  </div>
  <div class="col-lg-4 col-md-5 col-sm-4 col-xs-6 padding-5">
  	<label class="not-show">ชื่อโซน</label>
    <input type="text" class="form-control input-sm zone" name="zoneName" id="zoneName" placeholder="ชื่อโซน"  value="<?php echo $doc->zone_name; ?>"/>
  </div>
</div>
<hr class="margin-top-15"/>
<div class="row">
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label>จำนวน</label>
    <input type="text" class="form-control input-sm text-center" id="qty" value="1.00" />
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
  	<label>บาร์โค้ดสินค้า</label>
    <input type="text" class="form-control input-sm text-center" inputmode="none" id="barcode" placeholder="ยิงบาร์โค้ดเพื่อรับสินค้า" autocomplete="off"  />
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
  	<label class="display-block not-show">ok</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="checkBarcode()"><i class="fa fa-check"></i> ตกลง</button>
  </div>
  <input type="hidden" name="receive_code" id="receive_code" value="<?php echo $doc->code; ?>" />
  <input type="hidden" name="approver" id="approver" value="" />
</div>
<hr class="margin-top-15 margin-bottom-15"/>


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
  	<table class="table table-striped table-bordered" style="min-width:1000px;">
    	<thead>
      	<tr class="font-size-12">
        	<th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-120 text-center hide">บาร์โค้ด</th>
          <th class="fix-width-200 text-center">รหัสสินค้า</th>
          <th class="fix-width-200">ชื่อสินค้า</th>
					<th class="fix-width-100 text-right">ต้นทุน(เฉลี่ย)</th>
          <th class="fix-width-80 text-center">เบิก</th>
					<th class="fix-width-80 text-center">รับแล้ว</th>
					<th class="fix-width-80 text-center">รอยืนยัน</th>
          <th class="fix-width-80 text-center">ค้างรับ</th>
          <th class="fix-width-100 text-center">จำนวน</th>
					<th class="fix-width-100 text-center">มูลค่า</th>
        </tr>
      </thead>
      <tbody id="receiveTable">
				<?php if( ! empty($details)) : ?>
					<?php foreach($details as $rs) : ?>
						<?php $no = $rs->no; ?>
							<tr class="font-size-12">
		            <td class="middle text-center"><?php echo $no; ?></td>
		            <td class="middle"><?php echo $rs->product_code; ?></td>
		            <td class="middle"><input type="text" class="print-row" style="background:transparent; border:none;" value="<?php echo $rs->product_name; ?>" /></td>
								<td class="middle text-right">
									<input type="number" class="form-control input-sm text-right input-price" id="price_<?php echo $no; ?>" value="<?php echo $rs->price; ?>" <?php echo $rs->disabled; ?> />
								</td>
		            <td class="middle text-center" id="qty_<?php echo $no; ?>"><?php echo $rs->qty; ?></td>
								<td class="middle text-center"><?php echo $rs->received; ?></td>
								<td class="middle text-center"><?php echo $rs->uncomplete; ?></td>
		            <td class="middle text-center"><?php echo $rs->backlog; ?></td>
		            <td class="middle text-center">
		                <input type="number" class="form-control input-sm text-center receive-box pdCode"
										name="receive[<?php echo $no; ?>]" id="receive_<?php echo $no; ?>"
										data-no="<?php echo $no; ?>" value="<?php echo $rs->receive_qty; ?>"
										onchange="sumReceive()"
										/>
		            </td>
								<td class="middle text-right line-amount" id="line-amount-<?php echo $no; ?>"><?php echo number($rs->amount, 2); ?></td>
								<input type="hidden" id="product_<?php echo $no; ?>" value="<?php echo $rs->product_code; ?>"/>
								<input type="hidden" id="product_name_<?php echo $no; ?>" value="<?php echo $rs->product_name; ?>" />
								<input type="hidden" id="limit_<?php echo $no; ?>" value="<?php echo $rs->limit; ?>"/>
								<?php if(! empty($rs->barcode)) : ?>
									<input type="hidden" id="<?php echo $rs->barcode; ?>" value="<?php echo $no; ?>" />
								<?php endif; ?>
								<input type="hidden" id="backlog_<?php echo $no; ?>" value="<?php echo $rs->backlog; ?>" />
							</tr>
						<?php endforeach; ?>
						<tr>
		            <td colspan="4" class="middle text-right"><strong>รวม</strong></td>
		            <td class="middle text-center"><?php echo $totalQty; ?></td>
								<td class="middle text-center"><?php echo $totalReceived; ?></td>
								<td class="middle text-center"><?php echo $totalUncomplete; ?></td>
		            <td class="middle text-center"><?php echo $totalBacklog; ?></td>
		            <td class="middle text-center"><span id="total-receive"><?php echo $totalInputQty; ?></span></td>
								<td class="middle text-center"><span id="total-amount"><?php echo $totalAmount; ?></span></td>
		        </tr>
					<?php endif; ?>
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
						<td class="middle text-center">{{received}}</td>
						<td class="middle text-center">{{uncomplete}}</td>
            <td class="middle text-center">{{backlog}}</td>
            <td class="middle text-center"><span id="total-receive">0</span></td>
						<td class="middle text-center"><span id="total-amount">0.00</span></td>
        </tr>
    {{else}}
        <tr class="font-size-12">
            <td class="middle text-center">{{no}}</td>
            <td class="middle">{{pdCode}}</td>
            <td class="middle">{{pdName}}</td>
						<td class="middle text-right">
							<input type="number" class="form-control input-sm text-right input-price" id="price_{{no}}" value="{{price}}" {{disabled}} />
						</td>
            <td class="middle text-center" id="qty_{{no}}">{{qty}}</td>
						<td class="middle text-center">{{received}}</td>
						<td class="middle text-center">{{uncomplete}}</td>
            <td class="middle text-center">{{backlog}}</td>
            <td class="middle text-center">
                <input type="number" class="form-control input-sm text-center receive-box pdCode"
								name="receive[{{no}}]" id="receive_{{no}}"
								data-no="{{no}}"
								onchange="sumReceive()"
								/>
            </td>
						<td class="middle text-right line-amount" id="line-amount-{{no}}">0.00</td>
						<input type="hidden" id="product_{{no}}" value="{{pdCode}}"/>
						<input type="hidden" id="product_name_{{no}}" value="{{pdName}}" />
						<input type="hidden" id="limit_{{no}}" value="{{limit}}"/>
						{{#if barcode}}
						<input type="hidden" id="{{barcode}}" value="{{no}}" />
						{{/if}}
						<input type="hidden" id="backlog_{{no}}" value="{{backlog}}" />
        </tr>
    {{/if}}
{{/each}}
</script>

<?php else : ?>
  <?php redirect($this->home.'/view_detail/'.$doc->code); ?>
<?php endif; ?>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_transform/receive_transform.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_transform/receive_transform_add.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
