<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
		<h4 class="title">
			<?php echo $this->title; ?>
		</h4>
	</div>
	<div class="col-xs-12 visible-xs padding-5">
		<h4 class="title-xs"><?php echo $title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning btn-top" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
    <?php if($this->pm->can_add) : ?>
      <button type="button" class="btn btn-sm btn-success btn-top" onclick="addInvoice()">ออกใบกำกับ</button>
    <?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 margin-bottom-15"/>
<div class="row hidden-xs">
  <div class="col-lg-2 col-lg-offset-4 col-md-2 col-sm-3 padding-5">
    <input type="text" class="form-control text-center" id="bill-code" placeholder="ค้นหาเลขที่บิลขาย" autofocus />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
    <button type="button" class="btn btn-sm btn-primary btn-block" id="btn-submit-bill">Submit</button>
  </div>
</div>
<hr class="padding-5 margin-top-15 margin-bottom-15"/>

<div class="row hidden-xs">
  <div class="col-lg-6 col-md-6 col-sm-6 padding-5" id="left-block">
    <div class="col-lg-12 col-md-12 col-sm-12 border-1 text-center" style="padding-top:15px; background-color:#eee;" id="bill-view"></div>
  </div>

	<div class="col-lg-6 col-md-6 col-sm-6 padding-5" id="right-block">
	  <div class="col-lg-12 col-md-12 col-sm-12 border-1" id="bill-div" style="padding-top:15px;  overflow:auto;">
      <div class="col-lg-6 col-md-6 col-sm-6 padding-5 first">
        <input type="text" class="form-control text-center" onkeyup="numberOnly(this)" maxlength="13" id="tax-search" placeholder="ประจำตัวผู้เสียภาษี/เลขที่บัตรประชาชน"/>
      </div>
      <div class="col-lg-3 col-md-3 col-sm-3 padding-5">
        <button type="button" class="btn btn-sm btn-info btn-block" onclick="getCustomerByTaxId()">ค้นหา (Enter)</button>
      </div>
      <div class="col-lg-3 col-md-3 col-sm-3 padding-5 last">
        <button type="button" class="btn btn-sm btn-primary btn-block" onclick="addNewCustomer()">เพิ่มใหม (F3)่</button>
      </div>
      <div class="divider-hidden"></div>
      <div class="col-lg-12 col-md-12 col-sm-12 padding-5 first last" id="cust-result-table"></div>

      <div class="divider"></div>
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
          <label>ชื่อ</label>
          <input type="text" class="form-control cust-form" id="name" maxlength="254" disabled/>
        </div>
        <div class="divider-hidden"></div>
        <div class="col-lg-6 col-md-6 col-sm-6 padding-5 first">
          <label>ประจำตัวผู้เสียภาษี</label>
          <input type="text" class="form-control text-center text-center cust-form" id="tax-id" onkeyup="numberOnly(this)" maxlength="13" disabled/>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 padding-5">
          <label>รหัสสาขา</label>
          <input type="text" class="form-control text-center cust-form" id="branch-code" maxlength="10" value="000" disabled/>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 padding-5 last">
          <label>ชื่อสาขา</label>
          <input type="text" class="form-control text-center cust-form" id="branch-name" maxlength="50" value="สำนักงานใหญ่" disabled/>
        </div>
        <div class="divider-hidden"></div>
        <div class="col-lg-12 col-md-12 col-sm-12">
          <label>ที่อยู่</label>
          <textarea class="form-control cust-form" id="address" disabled></textarea>
        </div>
        <div class="divider-hidden"></div>
        <div class="col-lg-6 col-md-6 col-sm-6">
          <label>เบอร์โทร</label>
          <input type="text" class="form-control cust-form" id="phone" onkeyup="numberOnly(this)" disabled />
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
          <label class="display-block not-show">isCompany</label>
          <label>
            <input type="checkbox" class="ace cust-form" id="is-company" value="1" checked disabled/>
            <span class="lbl margin-top-5">&nbsp;&nbsp;นิติบุคคล</span>
          </label>
        </div>
      </div>
      <input type="hidden" id="cust-id" />
    </div>
	</div>
</div>

<script id="bill-template" type="text/x-handlebarsTemplate">
  {{#if is_cancel}}
  <div style="width:100%; height:0px; font-size:80px; position:absolute; left:0; line-height:0px; top:100px; color:red; text-align:center; opacity:0.1; transform:rotate(-30deg)">
      <span class="cancleWatermark">ยกเลิก</span>
  </div>
  {{/if}}

	<table class="width-100" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px;" >
		<tr><td class="width-50"></td><td class="width-50"></td></tr>
    {{#if bill_header_1}}
    <tr><td colspan="2" class="{{header_align_1}}" style="font-size:{{header_size_1}}px;">{{bill_header_1}}</td></tr>
    {{/if}}
    {{#if bill_header_2}}
    <tr><td colspan="2" class="{{header_align_2}}" style="font-size:{{header_size_2}}px;">{{bill_header_2}}</td></tr>
    {{/if}}
    {{#if bill_header_3}}
    <tr><td colspan="2" class="{{header_align_3}}" style="font-size:{{header_size_3}}px;">{{bill_header_3}}</td></tr>
    {{/if}}
		<tr><td class="text-left">TAX ID : {{tax_id}}</td><td class="text-right">POS ID : {{pos_no}}</td></tr>
		<tr><td class="text-left">Bill No.: {{code}}</td><td class="text-right">{{date_add}}</td></tr>
		<tr><td colspan="2" class="text-left">Staff: {{staff}}</td></tr>
	</table>

	<table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; font-size:12px; margin-top:0px; margin-bottom:10px;">
		<thead>
			<th style="width:45%; border-bottom:solid 1px #DDD;">Items</th>
			<th style="width:20%; text-align:right; border-bottom:solid 1px #DDD;">Price</th>
			<th style="width:15%; text-align:right; border-bottom:solid 1px #DDD;">Qty</th>
			<th style="width:20%; text-align:right; border-bottom:solid 1px #DDD;">Amount</th>
		</thead>
		{{#each details}}
			<tr>
				<td style="padding-top:5px; white-space: nowrap; overflow-x:hidden;">
					{{product_code}} : {{product_name}}
				</td>
				<td class="text-right">{{price}}</td>
				<td class="text-right" style="padding-top:5px;">{{qty}}</td>
				<td class="text-right" style="padding-top:5px;">{{total_amount}}{{{vat_sign}}}</td>
			</tr>
		{{/each}}
	</table>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; font-size:102x; margin-top:0px; margin-bottom:10px;">
		<tr>
			<td class="width-45"></td>
			<td class="width-20"></td>
			<td class="width-15 text-right"></td>
			<td class="width-20 text-right"></td>
		</tr>
		<tr height="20px">
			<td class="text-left" colspan="2">Total</td>
			<td class="text-right">{{total_qty}}</td>
			<td class="text-right">{{total_price}}</td>
		</tr>
		<tr height="20px">
			<td class="text-left" colspan="2">Less Discount</td>
			<td class="text-right">{{total_discount}}</td>
			<td class="text-right">{{total_amount}}</td>
		</tr>

    {{#if use_vat}}
      <tr height="20px">
        <td class="text-left" colspan="2">Vatable</td>
        <td></td>
        <td class="text-right">{{vatable}}</td>
      </tr>
      <tr height="20px">
        <td class="text-left" colspan="2">VAT</td>
        <td></td>
        <td class="text-right">{{vat_amount}}</td>
      </tr>
    {{/if}}

		<tr height="20px">
			<td class="text-left" colspan="2">Paid By {{payment_name}}</td>
      <td></td>
			<td class="text-right">{{received}}</td>
		</tr>
		<tr>
			<td class="text-left" colspan="2">Change</td>
      <td></td>
			<td class="text-right">{{changed}}</td>
		</tr>
	</table>
  <div class="divider"></div>
{{#if is_cancel}}
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 text-left">
      <strong>สาเหตุ</strong> &nbsp; : &nbsp; {{cancel_reason}} <br/>
      <strong>ยกเลิกโดย</strong>&nbsp; : &nbsp; {{cancel_user}} &nbsp; วันที่ {{cancel_date}}
    </div>
  </div>
{{/if}}
{{#if invoice_code}}
<hr/>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 text-center red">
    <strong>***ออกใบกำกับภาษีแล้ว***</strong> <br/>
    <strong>ใบกำกับภาษีเลขที่ - {{invoice_code}}</strong>
  </div>
</div>
{{/if}}
<input type="hidden" id="selected-bill-code" value="{{code}}" />
<input type="hidden" id="selected-bill-id" value="{{id}}" />
<input type="hidden" id="selected-bill-invoice" value="{{invoice_code}}" />
</script>

<div class="row visible-xs">
	<div class="col-xs-12"><h1 class="text-center">Not supporrt mobile</h1></div>
</div>

<script src="<?php echo base_url(); ?>scripts/order_pos_invoice/order_pos_invoice.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_pos_invoice/order_pos_invoice_bill.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
