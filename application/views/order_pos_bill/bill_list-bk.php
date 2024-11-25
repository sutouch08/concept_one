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
			<button type="button" class="btn btn-sm btn-success hide" onclick="createDelivery()">สร้างใบตัดยอดขาย</button>
			<button type="button" class="btn btn-sm btn-primary" onclick="createInvoice()">เปิด Invoice</button>
			<button type="button" class="btn btn-sm btn-purple" onclick="createTaxInvoice()">ออกใบกำกับภาษี</button>
		</p>
	</div>
</div>
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row hidden-xs">
    <div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
    </div>
    <div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>ใบตัดยอด</label>
      <input type="text" class="form-control input-sm search" name="ref_code"  value="<?php echo $ref_code; ?>" />
    </div>
    <div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>ใบสั่งขาย</label>
      <input type="text" class="form-control input-sm search" name="reference"  value="<?php echo $reference; ?>" />
    </div>
		<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>จุดขาย</label>
  		<select class="form-control input-sm filter" name="shop_id" id="shop_id">
  			<option value="all">ทั้งหมด</option>
  			<?php echo select_shop_id($shop_id); ?>
  		</select>
    </div>
		<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>เครื่อง POS</label>
  		<select class="form-control input-sm filter" name="pos_id" >
  			<option value="all">ทั้งหมด</option>
  			<?php echo select_pos_id($pos_id); ?>
  		</select>
    </div>
    <div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>การชำระเงิน</label>
  		<select class="form-control input-sm filter" name="payment" >
  			<option value="all">ทั้งหมด</option>
  			<?php echo select_pos_payment_method($payment); ?>
  		</select>
    </div>
		<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>คลัง</label>
  		<select class="form-control input-sm filter" name="warehouse" >
  			<option value="all">ทั้งหมด</option>
  			<?php echo select_pos_payment_method($payment); ?>
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
<div class="row hidden-xs">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5" id="left-block">
		<?php echo $this->pagination->create_links(); ?>
	  <div class="col-lg-12 col-md-12 col-sm-12 border-1 padding-0" id="bill-div" style="background-color: #eee; overflow:auto;">
			<table class="table table-striped table-hover tableFixHead" style="min-width:850px;">
				<thead>
					<tr>
            <th class="fix-width-40 middle text-center fix-header">
              <label>
                <input type="checkbox" class="ace" onchange="toggleCheckAll($(this))" />
                <span class="lbl"></span>
              </label>
            </th>
						<th class="fix-width-40 middle text-center fix-header">#</th>
						<th class="fix-width-100 middle text-center fix-header">วันที่</th>
						<th class="fix-width-120 middle fix-header">เลขที่เอกสาร</th>
						<th class="fix-width-100 middle text-right fix-header">ยอดเงิน</th>
						<th class="fix-width-100 middle text-right fix-header">มัดจำ</th>
						<th class="fix-width-50 middle text-center fix-header">สถานะ</th>
						<th class="fix-width-120 middle fix-header">ใบสั่งขาย</th>
            <th class="fix-width-120 middle fix-header">ใบตัดยอด</th>
						<th class="min-width-100 middle fix-header">การชำระเงิน</th>
					</tr>
				</thead>
				<tbody>
			<?php if( ! empty($orders)) : ?>
				<?php $no = $this->uri->segment($this->segment) + 1; ?>
				<?php foreach($orders as $rs) : ?>
          <?php $color = $rs->status == 'C' ? 'green' : ($rs->status == 'D' ? 'red' : ''); ?>
					<tr id="row-<?php echo $rs->id; ?>" class="bill-row <?php echo $color; ?>">
            <td class="middle text-center">
              <?php if($rs->status == 'O') : ?>
                <label id="chk-<?php echo $rs->id; ?>">
                  <input type="checkbox" class="ace chk" value="<?php echo $rs->id; ?>" />
                  <span class="lbl"></span>
                </label>
              <?php endif; ?>
            </td>
						<td class="middle pointer text-center" onclick="getBillView('<?php echo $rs->code; ?>', <?php echo $rs->id; ?>)"><?php echo number($no); ?></td>
						<td class="middle pointer text-center" onclick="getBillView('<?php echo $rs->code; ?>', <?php echo $rs->id; ?>)"><?php echo thai_date($rs->date_add); ?></td>
						<td class="middle pointer" onclick="getBillView('<?php echo $rs->code; ?>', <?php echo $rs->id; ?>)"><?php echo $rs->code; ?></td>
						<td class="middle pointer text-right" onclick="getBillView('<?php echo $rs->code; ?>', <?php echo $rs->id; ?>)"><?php echo number($rs->payAmount, 2); ?></td>
						<td class="middle pointer text-right" onclick="getBillView('<?php echo $rs->code; ?>', <?php echo $rs->id; ?>)"><?php echo number($rs->down_payment_amount, 2); ?></td>
						<td class="middle pointer text-center" id="status-<?php echo $rs->id; ?>" onclick="getBillView('<?php echo $rs->code; ?>', <?php echo $rs->id; ?>)"><?php echo bill_status_label($rs->status); ?></td>
						<td class="middle pointer" onclick="getBillView('<?php echo $rs->code; ?>', <?php echo $rs->id; ?>)"><?php echo $rs->so_code; ?></td>
						<td class="middle pointer" onclick="getBillView('<?php echo $rs->code; ?>', <?php echo $rs->id; ?>)"><?php echo $rs->ref_code; ?></td>
						<td class="middle pointer" onclick="getBillView('<?php echo $rs->code; ?>', <?php echo $rs->id; ?>)"><?php echo $rs->payment_name; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
				</tbody>
			</table>
	  </div>
	</div>

	<div class="col-lg-4 col-md-4 col-sm-4 text-center border-1"
	style="margin-top:-5px; padding-top:15px; background-color:#eee; border-top:0 !important; overflow:auto;" id="bill-view"></div>

</div>
<?php $this->load->view('order_pos/pos_cancel_modal'); ?>
<?php $this->load->view('order_pos/pos_invoice_modal'); ?>

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
			<td class="width-40"></td>
			<td class="width-25"></td>
			<td class="width-15 text-right"></td>
			<td class="width-20 text-right"></td>
		</tr>
		<tr height="20px">
			<td class="text-left">Total</td>
			<td class="text-right">{{total_qty}} หน่วย</td>
			<td class="text-right" colspan="2">{{total_price}}</td>
		</tr>
		<tr height="20px">
			<td class="text-left">Less Discount</td>
			<td class="text-right">{{total_discount}}</td>
			<td class="text-right" colspan="2">{{total_amount}}</td>
		</tr>

    {{#if use_vat}}
      <tr height="20px">
        <td class="text-left" colspan="2">Vatable</td>
        <td class="text-right" colspan="2">{{vatable}}</td>
      </tr>
      <tr height="20px">
        <td class="text-left" colspan="2">VAT</td>
        <td class="text-right" colspan="2">{{vat_amount}}</td>
      </tr>
    {{/if}}

		<tr height="20px">
			<td class="text-left" colspan="2">Paid By {{payment_name}}</td>
			<td class="text-right" colspan="2">{{received}}</td>
		</tr>
		<tr>
			<td class="text-left" colspan="2">Change</td>
			<td class="text-right" colspan="2">{{changed}}</td>
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
	<div class="row hidden-print">
      <div class="col-lg-12 col-md-12 col-sm-12 padding-5">
      {{#unless invoice_code}}
        {{#if allow_print}}
        <button type="button" class="btn btn-sm btn-primary btn-100" id="btn-print" onclick="printBill('{{code}}')">พิมพ์</button>
        {{/if}}

        {{#if allow_cancel}}
        <button type="button" class="btn btn-sm btn-danger btn-100" id="btn-cancel" onclick="cancelBill('{{code}}', {{id}})">ยกเลิก</button>
        {{/if}}

				{{#if allow_invoice}}
			 <button type="button" class="btn btn-sm btn-purple btn-100" id="btn-invoice" onclick="showInvoiceCustomer()">ออกใบกำกับ</button>
			 {{/if}}
      {{/unless}}
			{{#if invoice_code}}
        <button type="button" class="btn btn-sm btn-info btn-100" id="btn-invoice" onclick="printInvoice('{{invoice_code}}')">พิมพ์ใบกำกับ</button>
      {{/if}}
      </div>
    <input type="hidden" id="selected-bill-code" value="{{code}}" />
    <input type="hidden" id="selected-bill-id" value="{{id}}" />
    <input type="hidden" id="selected-customer-id" value="" />
    <input type="hidden" id="payment-role" value="{{payment_role}}" />
    <input type="hidden" id="bill-amount" value="{{total_amount}}" />
	</div>
</script>

<div class="row visible-xs">
	<div class="col-xs-12"><h1 class="text-center">Not supporrt mobile</h1></div>
</div>

<script src="<?php echo base_url(); ?>scripts/order_pos_bill/order_pos_bill.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/pos_footer'); ?>
