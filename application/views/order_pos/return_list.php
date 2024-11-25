<?php $this->load->view('include/pos_header'); ?>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row hidden-xs">
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>เลขที่รับคืน</label>
      <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>สลิปขาย</label>
      <input type="text" class="form-control input-sm search" name="order_code"  value="<?php echo $order_code; ?>" />
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>ใบลดหนี้</label>
      <input type="text" class="form-control input-sm search" name="ref_code"  value="<?php echo $ref_code; ?>" />
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
	<input type="hidden" name="shop_id" id="shop_id" value="<?php echo $shop_id; ?>" />
</form>
<hr class="padding-5 margin-top-15"/>
<div class="row hidden-xs">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5" id="left-block">
		<?php echo $this->pagination->create_links(); ?>
	  <div class="col-lg-12 col-md-12 col-sm-12 border-1 padding-0" id="bill-div" style="background-color: #eee; overflow:auto;">
			<table class="table table-striped table-hover tableFixHead" style="min-width:680px;">
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
						<th class="fix-width-80 middle text-center fix-header">สถานะ</th>
						<th class="fix-width-100 middle fix-header">เลขที่สลิปขาย</th>
            <th class="fix-width-100 middle fix-header">เลขทีใบลดหนี้</th>
					</tr>
				</thead>
				<tbody>
			<?php if( ! empty($orders)) : ?>
				<?php $no = $this->uri->segment($this->segment) + 1; ?>
				<?php foreach($orders as $rs) : ?>
          <?php $color = $rs->status == 'C' ? 'green' : ($rs->status == 'D' ? 'red' : ''); ?>
					<tr id="row-<?php echo $rs->id; ?>" class="<?php echo $color; ?>">
            <td class="middle text-center">
              <?php if($rs->status == 'O') : ?>
                <label id="chk-<?php echo $rs->id; ?>">
                  <input type="checkbox" class="ace chk" value="<?php echo $rs->id; ?>" />
                  <span class="lbl"></span>
                </label>
              <?php endif; ?>
            </td>
						<td class="middle pointer text-center" onclick="getReturnView('<?php echo $rs->code; ?>')"><?php echo number($no); ?></td>
						<td class="middle pointer text-center" onclick="getReturnView('<?php echo $rs->code; ?>')"><?php echo thai_date($rs->date_add); ?></td>
						<td class="middle pointer" onclick="getReturnView('<?php echo $rs->code; ?>')"><?php echo $rs->code; ?></td>
						<td class="middle pointer text-right" onclick="getReturnView('<?php echo $rs->code; ?>')"><?php echo number($rs->amount, 2); ?></td>
						<td class="middle pointer text-center" id="status-<?php echo $rs->id; ?>" onclick="getReturnView('<?php echo $rs->code; ?>')"><?php echo bill_status_label($rs->status); ?></td>
						<td class="middle pointer" onclick="getReturnView('<?php echo $rs->code; ?>')"><?php echo $rs->order_code; ?></td>
            <td class="middle pointer" onclick="getReturnView('<?php echo $rs->code; ?>')"><?php echo $rs->ref_code; ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
				</tbody>
			</table>
	  </div>
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 text-right" style="position:fixed; bottom:0px; left:0; padding:10px;">
      <button type="button" class="btn btn-primary" onclick="salePage()">กลับหน้าขายสินค้า</button>
      <button type="button" class="btn btn-purple" onclick="createCN()">สร้างใบลดหนี้(รวม)</button>
      <button type="button" class="btn btn-success" onclick="getReturnBill()">สร้างใบรับคืนสินค้า</button>
    </div>
	</div>

	<div class="col-lg-4 col-md-4 col-sm-4 text-center" style="margin-top:-5px; padding-top:15px; background-color:#eee;" id="bill-view"></div>

</div>

<div class="row visible-xs">
	<div class="col-xs-12"><h1 class="text-center">Not supporrt</h1></div>
</div>

<?php $this->load->view('order_pos/return_modal'); ?>
<?php $this->load->view('order_pos/return_cancel_modal'); ?>

<script id="return-view-template" type="text/x-handlebarsTemplate">
  {{#if is_cancel}}
  <div style="width:100%; height:0px; font-size:80px; position:absolute; left:0; line-height:0px; top:100px; color:red; text-align:center; opacity:0.1; transform:rotate(-30deg)">
      <span class="cancleWatermark">ยกเลิก</span>
  </div>
  {{/if}}
  <div class="width-100 text-center font-size-24 margin-bottom-15">ใบรับคืนสินค้า</div>
	<table class="width-100" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-bottom:10px;" >
		<tr><td class="width-50"></td><td class="width-50"></td></tr>
    <tr><td class="text-left" colspan="2">สาขา: {{shop_code}} : {{shop_name}}</td></tr>
    <tr><td class="text-left">เลขที่ : {{code}}</td><td class="text-right">วันที่ : {{date_add}}</td></tr>
    <tr><td class="text-left">คลัง : {{warehouse_code}}</td><td class="text-right">โซน : {{zone_name}}</td></tr>
		<tr><td class="text-left">POS ID : {{pos_no}}</td><td class="text-right">พนักงาน : {{staff}}</td></tr>
		<tr><td class="text-left">สถานะ : {{status_label}}</td><td class="text-right">อ้างอิง : {{order_code}}</td></tr>
	</table>

	<table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; font-size:12px; margin-top:0px; margin-bottom:10px;">
		<thead>
			<th style="width:35%; border-bottom:solid 1px #DDD;">รายการ</th>
			<th style="width:15%; text-align:right; border-bottom:solid 1px #DDD;">ราคา</th>
			<th style="width:15%; text-align:right; border-bottom:solid 1px #DDD;">ส่วนลด</th>
      <th style="width:15%; text-align:right; border-bottom:solid 1px #DDD;">จำนวน</th>
			<th style="width:20%; text-align:right; border-bottom:solid 1px #DDD;">มูลค่า</th>
		</thead>
		{{#each details}}
			<tr>
				<td style="padding-top:5px; white-space: nowrap; overflow-x:hidden;">
					{{product_code}} : {{product_name}}
				</td>
				<td class="text-right">{{price}}</td>
				<td class="text-right" style="padding-top:5px;">{{discount_label}}</td>
        <td class="text-right" style="padding-top:5px;">{{return_qty}}</td>
				<td class="text-right" style="padding-top:5px;">{{total_amount}}</td>
			</tr>
		{{/each}}

	</table>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; font-size:102x; margin-top:0px; margin-bottom:10px;">
		<tr>
			<td class="width-35"></td>
			<td class="width-15"></td>
			<td class="width-15 text-right"></td>
      <td class="width-15 text-right"></td>
			<td class="width-20 text-right"></td>
		</tr>
		<tr height="20px">
			<td class="text-left" colspan="3">Total</td>
      <td class="text-right">{{total_qty}}</td>
			<td class="text-right">{{total_amount}}</td>
		</tr>
	</table>
  <div class="divider"></div>
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 text-left">
      <strong>หมายเหตุ</strong> &nbsp; : &nbsp; {{remark}} <br/>
      <strong>อนุมัติโดย</strong>&nbsp; : &nbsp; {{approver}} &nbsp; วันที่ {{approve_date}}
    </div>
  </div>
{{#if is_cancel}}
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 text-left">
      <strong>สาเหตุ</strong> &nbsp; : &nbsp; {{cancel_reason}} <br/>
      <strong>ยกเลิกโดย</strong>&nbsp; : &nbsp; {{cancel_user}} &nbsp; วันที่ {{cancel_date}}
    </div>
  </div>
{{/if}}
<div class="row hidden-print">
  <div class="col-lg-12 col-md-12 col-sm-12 padding-5 margin-top-15">
    <button type="button" class="btn btn-sm btn-primary btn-100" id="btn-print" onclick="printReturn('{{code}}')">พิมพ์</button>
    {{#if allow_cancel}}
      <button type="button" class="btn btn-sm btn-danger btn-100" id="btn-cancel" onclick="cancelReturn('{{code}}', {{id}})">ยกเลิก</button>
    {{/if}}
  </div>
  <input type="hidden" id="selected-bill-code" value="{{code}}" />
  <input type="hidden" id="selected-bill-id" value="{{id}}" />
  <input type="hidden" id="selected-bill-status" value="{{status}}" />
</div>
</script>

<script id="bill-view-template" type="text/x-handlebarsTemplate">
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
			<th style="width:35%; border-bottom:solid 1px #DDD;">รายการ</th>
			<th style="width:15%; text-align:right; border-bottom:solid 1px #DDD;">ราคา</th>
			<th style="width:15%; text-align:right; border-bottom:solid 1px #DDD;">จำนวน</th>
      <th style="width:15%; text-align:right; border-bottom:solid 1px #DDD;">คืนแล้ว</th>
			<th style="width:20%; text-align:right; border-bottom:solid 1px #DDD;">มูลค่า</th>
		</thead>
		{{#each details}}
			<tr>
				<td style="padding-top:5px; white-space: nowrap; overflow-x:hidden;">
					{{product_code}} : {{product_name}}
				</td>
				<td class="text-right">{{price}}</td>
				<td class="text-right" style="padding-top:5px;">{{qty}}</td>
        <td class="text-right" style="padding-top:5px;">{{return_qty}}</td>
				<td class="text-right" style="padding-top:5px;">{{total_amount}}{{vat_sign}}</td>
			</tr>
		{{/each}}
	</table>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; font-size:102x; margin-top:0px; margin-bottom:10px;">
		<tr>
			<td class="width-35"></td>
			<td class="width-15"></td>
			<td class="width-15"></td>
      <td class="width-15"></td>
			<td class="width-20"></td>
		</tr>
		<tr height="20px">
			<td class="text-left" colspan="2">Total</td>
			<td class="text-right">{{total_qty}}</td>
      <td class="text-right">{{total_return}}</td>
			<td class="text-right">{{total_price}}</td>
		</tr>
		<tr height="20px">
			<td class="text-left" colspan="3">Less Discount</td>
			<td class="text-right">{{total_discount}}</td>
			<td class="text-right">{{total_amount}}</td>
		</tr>

		<tr height="20px">
			<td class="text-left" colspan="3">Paid By {{payment_name}}</td>
      <td></td>
			<td class="text-right">{{received}}</td>
		</tr>
		<tr>
			<td class="text-left" colspan="3">Change</td>
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
	<div class="row hidden-print">
    <input type="hidden" id="selected-bill-code" value="{{code}}" />
    <input type="hidden" id="selected-bill-id" value="{{id}}" />
	</div>
</script>

<input type="hidden" id="pos_id" value="<?php echo $pos->id; ?>">
<input type="hidden" id="zone_code" value="<?php echo $pos->zone_code; ?>">
<input type="hidden" id="shop_id" value="<?php echo $pos->shop_id; ?>">
<input type="hidden" id="customer-code" value="<?php echo $pos->customer_code; ?>" />
<input type="hidden" id="channels-code" value="<?php echo $pos->channels_code; ?>" />
<input type="hidden" id="payment-code" value="<?php echo $pos->cash_payment; ?>" />
<input type="hidden" id="role-1-code" value="<?php echo $pos->cash_payment; ?>" /> <!-- Payment_code for cash payment -->
<input type="hidden" id="role-2-code" value="<?php echo $pos->transfer_payment; ?>" /> <!-- Payment_code for bank transfer -->
<input type="hidden" id="role-3-code" value="<?php echo $pos->card_payment; ?>" /><!-- Payment_code for credit card payment -->


<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos_return.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/pos_footer'); ?>
