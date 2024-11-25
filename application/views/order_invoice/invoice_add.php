<?php $this->load->view('include/header'); ?>
<?php $this->load->view('order_invoice/style'); ?>
<div class="row hidden-xs">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
		<h4 class="title">
			<?php echo $this->title; ?>
		</h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-warning btn-top" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
    <?php if($this->pm->can_add) : ?>
      <button type="button" class="btn btn-sm btn-success btn-top" onclick="showCustomerModal()">ข้อมูลลูกค้า</button>
    <?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr class="hidden-xs"/>
<div class="row header-row hidden-xs">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center" disabled />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เล่มเอกสาร</label>
		<select class="form-control input-sm h" id="is-term">
			<option value="">เลือก</option>
			<option value="0">ขายสด</option>
			<option value="1">ขายเชื่อ</option>
		</select>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>ชนิด VAT</label>
		<select class="form-control input-sm h" id="vat-type" onchange="toggleVatType()">
			<option value="">เลือก</option>
			<option value="E">แยกนอก</option>
			<option value="I">รวมใน</option>
			<option value="N">ไม่ VAT</option>
		</select>
		<input type="hidden" id="tax-status" value="">
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center h" name="date" id="date" style="position:relative; z-index:10;" value="<?php echo date('d-m-Y'); ?>" required readonly />
  </div>
	<div class="divider-hidden visible-xs">		</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center h" maxlength="15" id="customer-code" value="" />
	</div>
	<div class="col-lg-5 col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="customer-name" value="" />
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
		<label class="display-block not-show">isCompany</label>
		<label style="margin-top:0;">
			<input type="checkbox" class="ace" id="is-company" value="1" onchange="toggleBranch()" />
			<span class="lbl margin-top-5">&nbsp;&nbsp;นิติบุคคล</span>
		</label>
	</div>
	<div class="col-lg-6 col-md-4-harf col-sm-3-harf col-xs-6 padding-5">
		<label>ผู้ติดต่อ</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="customer-ref" value="" />
	</div>
	<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เบอร์โทร</label>
		<input type="text" class="form-control input-sm h" maxlength="32" id="phone" value="" />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		<label>เลขที่ผู้เสียภาษี</label>
		<input type="text" class="form-control input-sm h" maxlength="13" id="tax-id" value="" />
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
		<label>สาขา</label>
		<input type="text" class="form-control input-sm text-center h" maxlength="10" id="branch-code" value="" />
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>ชื่อสาขา</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="branch-name" value="" />
	</div>

	<div class="col-lg-4-harf col-md-4-harf col-sm-6-harf col-xs-12 padding-5">
		<label>ที่อยู่</label>
		<input type="text" class="form-control input-sm h" maxlength="254"id="address" value="" />
	</div>

	<div class="col-lg-2 col-md-2 col-sm-1-harf col-xs-6 padding-5">
		<label>ตำบล</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="sub-district" value="" />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-1-harf col-xs-6 padding-5">
		<label>อำเภอ</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="district" value="" />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-1-harf col-xs-6 padding-5">
		<label>จังหวัด</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="province" value="" />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1 col-xs-6 padding-5">
		<label>รหัสไปรษณีย์</label>
		<input type="text" class="form-control input-sm text-center h" maxlength="12" id="postcode" value="" />
	</div>
</div>


<hr class="hidden-xs margin-top-15 margin-bottom-15"/>

<div class="row hidden-xs">
	<div class="col-lg-1-harf col-md-2 col-sm-3 padding-5">
		<select class="form-control input-sm h" id="ref-type" onchange="toggleRefType()">
			<option value="">ค้นเอกสาร</option>
			<option value="POS">POS</option>
			<option value="WO">ออเดอร์</option>			
		</select>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-3 padding-5">
    <input type="text" class="form-control input-sm text-center" id="bill-code" placeholder="ค้นบิลขาย" />
		<input type="hidden" id="billCode" />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
    <button type="button" class="btn btn-xs btn-primary btn-block" id="btn-submit-bill" onclick="getOrderDetails()">Submit</button>
		<button type="button" class="btn btn-xs btn-warning btn-block hide" id="btn-change-bill" onclick="changeBill()">Change</button>
  </div>
</div>
<hr class="padding-5 margin-top-15 margin-bottom-15 hidden-xs"/>


<div class="row hidden-xs">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 margin-bottom-15">
		<div class="tabable">
			<ul class="nav nav-tabs" role="tablist">
        <li class="active">
        	<a href="#items-list" id="item-tab" aria-expanded="true" aria-controls="items-list" role="tab" data-toggle="tab">รายการสินค้า</a>
        </li>
      	<li>
          <a href="#down-payment" id="down-tab" aria-expanded="false" aria-controls="down-payment" role="tab" data-toggle="tab">เงินมัดจำ</a>
        </li>
      </ul>

			<!-- Tab panes -->
      <div class="tab-content" style="margin:0px; padding:0px; border:none;">
				<div role="tabpanel" class="tab-pane active" id="items-list">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1 table-responsive"
					style="height:300px; overflow:auto; border-top:solid 1px #ccc;">
						<table class="table table-bordered tableFixHead" style="min-width:1000px; margin-bottom:20px;">
							<thead>
								<tr>
									<th class="fix-width-40 text-center fix-header">#</th>
									<th class="fix-width-200 fix-header">รหัสสินค้า</th>
									<th class="min-width-200 fix-header">ชื่อสินค้า</th>
									<th class="fix-width-100 text-center fix-header">จำนวน</th>
									<th class="fix-width-100 text-center fix-header">ราคา/หน่วย</th>
									<th class="fix-width-100 text-center fix-header">ส่วนลด(%)</th>
									<th class="fix-width-120 text-right fix-header">มูลค่า</th>
								</tr>
							</thead>
							<tbody id="detail-table">

							</tbody>
						</table>
					</div>
				</div>

				<div role="tabpanel" class="tab-pane fade" id="down-payment">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1 table-responsive"
					id="down-payment-table"
					style="height:300px; overflow:auto; border-top:solid 1px #ccc;">
						<table class="table table-bordered tableFixHead" style="margin-bottom:20px;">
							<thead>
								<tr>
									<th class="fix-width-40 text-center fix-header">#</th>
									<th class="fix-width-200 fix-header">เลขที่</th>
									<th class="min-width-200 fix-header">ช่องทาง</th>
									<th class="fix-width-100 text-right fix-header">ยอดเงิน</th>
									<th class="fix-width-100 text-right fix-header">ตัดแล้ว</th>
									<th class="fix-width-100 text-right fix-header">คงเหลือ</th>
									<th class="fix-width-100 text-right fix-header">ยอดตัดบิลนี้</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
			</div><!-- tab-content -->
		</div><!-- tabable -->

	</div>

</div>


<?php $default_sale_id = getConfig('DEFAULT_SALES_ID'); ?>
<div class="row hidden-xs">
	<div class="divider-hidden"></div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-lg-3 col-md-3 col-sm-3 control-label no-padding-right">พนักงานขาย</label>
				<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
					<select class="width-100 edit" id="sale-id" name="sale_id" disabled>
						<?php echo select_saleman($default_sale_id); ?>
					</select>
				</div>
			</div>

			<div class="form-group" >
				<label class="col-lg-3 col-md-3 col-sm-3 control-label no-padding-right">Owner</label>
				<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
					<input type="text" class="form-control input-sm" id="owner" value="" disabled>
				</div>
			</div>

			<div class="form-group" >
				<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Remark</label>
				<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
					<textarea id="remark" maxlength="254" rows="3" class="form-control" ></textarea>
				</div>
			</div>

		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<div class="form-horizontal" >
			<div class="form-group" >
				<label class="col-lg-3 col-md-2-harf hidden-sm control-label no-padding-right">จำนวนรวม</label>
				<label class="col-sm-2 visible-sm control-label no-padding-right">จำนวน</label>
				<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
					<input type="text" class="form-control input-sm text-center" id="total-qty" value="" disabled>
				</div>
				<label class="col-lg-3 col-md-3 hidden-sm control-label no-padding-right">มูลค่าก่อนส่วนลด</label>
				<label class="col-sm-2 visible-sm control-label no-padding-right">มูลค่า</label>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="total-amount" value="" disabled>
				</div>
			</div>
			<div class="form-group" >
				<label class="col-lg-5-harf col-md-5 col-sm-4 col-xs-6 control-label no-padding-right">ส่วนลด</label>
				<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
					<span class="input-icon input-icon-right">
						<input type="number" id="bill-disc-percent" class="form-control input-sm" value="" disabled/>
						<i class="ace-icon fa fa-percent"></i>
					</span>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="bill-disc-amount" value=""disabled >
					<input type="hidden" id="total-after-disc" value="0" />
				</div>
			</div>

			<div class="form-group" id="bill-wht" >
        <label class="col-lg-5-harf col-md-5 col-sm-4 col-xs-6 control-label no-padding-right">หัก ณ ที่จ่าย</label>
        <div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-6 padding-5">
          <span class="input-icon input-icon-right">
          <input type="number" id="whtPrcnt" class="form-control input-sm" onchange="recalTotal()" value="" disabled/>
          <i class="ace-icon fa fa-percent"></i>
          </span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="hidden" id="wht-amount" value="" />
          <input type="text" id="wht-amount-label" class="form-control input-sm text-right" value="" disabled />
        </div>
      </div>

			<div class="form-group" id="bill-vat" >
				<label class="col-lg-6-harf col-md-6 col-sm-5 col-xs-6 control-label no-padding-right">VAT</label>
				<div class="col-lg-1-harf col-md-2 col-sm-2 padding-5">
					<input type="text" id="vat-type-label" class="form-control input-sm text-center" value="Include" disabled/>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
					<input type="text" id="vat-total" class="form-control input-sm text-right" value="" disabled />
				</div>
			</div>

			<div class="form-group" >
				<label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="doc-total" value="" disabled>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">หักมัดจำ</label>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="down-amount" value="" disabled>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ยอดชำระ</label>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="doc-balance" value="" disabled>
				</div>
			</div>
		</div>
	</div>

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning btn-100" onclick="leave()">Cancel</button>
		<button type="button" class="btn btn-sm btn-primary btn-100" id="btn-save" onclick="addInvoice()">Save</button>
	</div>
</div>

<input type="hidden" id="default_sale_id" value="<?php echo $default_sale_id; ?>" />

<?php $this->load->view('order_invoice/customer_modal'); ?>
<?php $this->load->view('order_invoice/address_modal'); ?>

<script id="wo-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr id="row-{{no}}">
			<td class="text-center no">{{no}}</td>
			<td class="">{{product_code}}</td>
			<td class="">{{product_name}}</td>
			<td class="text-right">
				<input type="text" class="form-control input-xs text-center line-qty"
					style="border:0px;"
					id="qty-{{id}}"
					data-id="{{id}}"
					data-itemcode="{{product_code}}"
					data-itemname="{{product_name}}"
					data-unit="{{unitMsr}}"
					data-baseref="{{reference}}"
					data-baseline="{{order_detail_id}}"
					data-solineid="{{so_line_id}}"
					data-discount="{{DiscPrcnt}}"
					data-price="{{price}}"
					data-qty="{{qty}}"
					data-pricebefdi="{{PriceBefDi}}"
					data-priceafvat="{{PriceAfVAT}}"
					data-vatcode="{{vat_code}}"
					data-vatrate="{{vat_rate}}"
					data-vatsum="{{VatSum}}"
					data-avgbilldiscamount="{{avgBillDiscAmount}}"
					data-sumbilldiscamount="{{sumBillDiscAmount}}"
					data-linetotal="{{LineTotal}}"
					data-whscode="{{warehouse_code}}"
					data-zonecode="{{zone_code}}"
					data-linetext="{{line_text}}"
					data-iscount="{{is_count}}"
					value="{{qty_label}}" readonly	/>
			</td>
			<td class="text-right">
				<input type="text" class="form-control input-xs text-center" style="border:0px;" id="price-{{id}}" value="{{price_label}}" readonly	/>
			</td>
			<td class="text-right">
				<input type="text" class="form-control input-xs text-center" style="border:0px;"	value="{{discount_label}}" readonly	/>
			</td>
			<td class="text-right">
				<input type="text" class="form-control input-xs text-right"	style="border:0px;"	id="total-{{id}}"	value="{{line_total_label}}" readonly	/>
			</td>
		</tr>
	{{/each}}
</script>

<script id="pos-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr id="row-{{no}}">
			<td class="text-center no">{{no}}</td>
			<td class="">{{product_code}}</td>
			<td class="">{{product_name}}</td>
			<td class="text-right">
				<input type="text" class="form-control input-xs text-center line-qty"
					style="border:0px;"
					id="qty-{{id}}"
					data-id="{{id}}"
					data-itemcode="{{product_code}}"
					data-itemname="{{product_name}}"
					data-unit="{{unitMsr}}"
					data-baseref="{{reference}}"
					data-baseline="{{id}}"
					data-solineid="{{so_line_id}}"
					data-discount="{{DiscPrcnt}}"
					data-price="{{price}}"
					data-qty="{{qty}}"
					data-pricebefdi="{{PriceBefDi}}"
					data-priceafvat="{{PriceAfVAT}}"
					data-vatcode="{{vat_code}}"
					data-vatrate="{{vat_rate}}"
					data-vatsum="{{VatSum}}"
					data-avgbilldiscamount="{{avgBillDiscAmount}}"
					data-sumbilldiscamount="{{sumBillDiscAmount}}"
					data-linetotal="{{LineTotal}}"
					data-whscode="{{warehouse_code}}"
					data-zonecode="{{zone_code}}"
					data-linetext="{{line_text}}"
					data-iscount="{{is_count}}"
					value="{{qty_label}}" readonly	/>
			</td>
			<td class="text-right">
				<input type="text" class="form-control input-xs text-center" style="border:0px;" id="price-{{id}}" value="{{price_label}}" readonly	/>
			</td>
			<td class="text-right">
				<input type="text" class="form-control input-xs text-center" style="border:0px;"	value="{{discount_label}}" readonly	/>
			</td>
			<td class="text-right">
				<input type="text" class="form-control input-xs text-right"	style="border:0px;"	id="total-{{id}}"	value="{{line_total_label}}" readonly	/>
			</td>
		</tr>
	{{/each}}
</script>

<script id="pos-down-payment-template" type="text/x-handlebarsTemplate">
	<table class="table table-bordered tableFixHead" style="margin-bottom:20px;">
		<thead>
			<tr>
				<th class="fix-width-40 text-center fix-header">#</th>
				<th class="fix-width-200 fix-header">เลขที่</th>
				<th class="min-width-200 fix-header">ช่องทาง</th>
				<th class="fix-width-120 text-right fix-header">คงเหลือก่อนตัด</th>
				<th class="fix-width-120 text-right fix-header">ยอดตัดบิลนี้</th>
				<th class="fix-width-120 text-right fix-header">คงเหลือหลังตัด</th>
			</tr>
		</thead>
		<tbody id="down-payment-table">
			{{#each this}}
				<tr id="dp-{{no}}">
					<td class="text-center dp-no">{{no}}</td>
					<td class="">{{code}}</td>
					<td class="">{{payment_role_name}}</td>
					<td class="text-right dp-amount">{{amountBfUse_label}}</td>
					<td class="text-right dp-available">{{amount_label}}</td>
					<td class="text-right dp-used">{{amountAfUse_label}}</td>
					<td class="hide">
					<input type="hidden" class="down-amount"
						id="down-{{id}}"
						data-id="{{id}}"
						data-code="{{code}}"
						data-reference="{{reference}}"
						data-reftype="{{ref_type}}"
						data-available="{{amount}}"
						value="{{amount}}" />
				</tr>
			{{/each}}
		</tbody>
	</table>
</script>

<script id="down-payment-template" type="text/x-handlebarsTemplate">
	<table class="table table-bordered tableFixHead" style="margin-bottom:20px;">
		<thead>
			<tr>
				<th class="fix-width-40 text-center fix-header">#</th>
				<th class="fix-width-200 fix-header">เลขที่</th>
				<th class="min-width-200 fix-header">ช่องทาง</th>
				<th class="fix-width-100 text-right fix-header">ยอดเงิน</th>
				<th class="fix-width-100 text-right fix-header">ตัดแล้ว</th>
				<th class="fix-width-100 text-right fix-header">คงเหลือ</th>
				<th class="fix-width-100 text-right fix-header">ยอดตัดบิลนี้</th>
			</tr>
		</thead>
		<tbody>
		{{#each this}}
			<tr id="dp-{{no}}">
				<td class="text-center dp-no">{{no}}</td>
				<td class="">{{code}}</td>
				<td class="">{{payment_role_name}}</td>
				<td class="text-right dp-amount">{{amount_label}}</td>
				<td class="text-right dp-used">{{used_label}}</td>
				<td class="text-right dp-available">{{available_label}}</td>
				<td class="text-right dp-use-amount">
					<input type="number" class="form-control input-sm text-right down-amount"
						id="down-{{id}}"
						data-id="{{id}}"
						data-code="{{code}}"
						data-reference="{{reference}}"
						data-reftype="{{ref_type}}"
						data-available="{{available}}"
						value="{{use_amount}}"
						onchange="recalDownPayment()" {{disabled}}/>
				</td>
			</tr>
		{{/each}}
		</tbody>
	</table>
</script>

<div class="row visible-xs">
	<div class="col-xs-12"><h1 class="text-center">Not support mobile</h1></div>
</div>

<script src="<?php echo base_url(); ?>scripts/order_invoice/order_invoice.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_invoice/order_invoice_bill.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
