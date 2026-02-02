<script id="row-template" type="text/x-handlebarsTemplate">
	<tr class="font-size-11 pos-rows" id="row-{{id}}" data-id="{{id}}">
		<input type="hidden" class="sell-item" data-id="{{id}}" id="pdCode-{{id}}" value="{{product_code}}">
		<input type="hidden" id="pdName-{{id}}" value="{{product_name}}">
		<input type="hidden" id="taxRate-{{id}}" value="{{vat_rate}}">
		<input type="hidden" id="taxAmount-{{id}}" value="{{vat_amount}}">
		<input type="hidden" id="stdPrice-{{id}}" value="{{std_price}}">
		<input type="hidden" id="sellPrice-{{id}}" value="{{final_price}}">
		<input type="hidden" id="discAmount-{{id}}" value="{{discount_amount}}">
		<input type="hidden" id="unitCode-{{id}}" value="{{unit_code}}">
		<input type="hidden" id="itemType-{{id}}" value="{{item_type}}">
		<input type="hidden" id="currentQty-{{id}}" value="{{qty}}">
		<input type="hidden" id="currentPrice-{{id}}" value="{{price}}">
		<input type="hidden" id="currentDisc-{{id}}" value="{{discount_label}}">
		<input type="hidden" id="isEdit-{{id}}" valu="{{is_edit}}">
		<input type="hidden" id="baseCode-{{id}}" value="{{baseCode}}" />
		<input type="hidden" id="baseLineId-{{id}}" value="{{line_id}}" />

		<td class="middle text-center no"></td>
		<td><input type="text" class="form-control input-xs" value="{{product_name}}" readonly/></td>
		<td class="hide"><input type="text" class="form-control input-xs text-center hide" value="{{unit_code}}" readonly /></td>
		<td>
			<input type="text" class="form-control input-xs text-right line-qty focus" data-id="{{id}}"
			id="qty-{{id}}" onchange="updateItem('{{id}}')" onclick="$(this).select();" value="{{qty_label}}" />
		</td>
		<td>
			<input type="text" class="form-control input-xs text-right line-price focus"
			data-id="{{id}}" id="price-{{id}}"	value="{{price_label}}"
			onchange="updateItem('{{id}}')"	onclick="$(this).select();" />
		</td>
		<td>
			<input type="text" class="form-control input-xs text-right line-disc focus"
			data-id="{{id}}" 	id="disc-{{id}}" value="{{discount_label}}"
			onchange="updateItem('{{id}}')" onclick="$(this).select();" />
		</td>
		<td>
			<input type="text" class="form-control input-xs text-right line-total"
			id="total-{{id}}" data-id="{{id}}" value="{{total_amount_label}}" readonly />
		</td>
		<td class="middle text-center" style="padding-left:5px; padding-right:5px;">
			<input type="checkbox" class="ace chk-row" value="{{id}}" />
			<label class="lbl"></label>
		</td>
	</tr>
</script>

<script id="update-template" type="text/x-handlebarsTemplate">
	<input type="hidden" class="sell-item" data-id="{{id}}" id="pdCode-{{id}}" value="{{product_code}}">
	<input type="hidden" id="pdName-{{id}}" value="{{product_name}}">
	<input type="hidden" id="taxRate-{{id}}" value="{{vat_rate}}">
	<input type="hidden" id="taxAmount-{{id}}" value="{{vat_amount}}">
	<input type="hidden" id="stdPrice-{{id}}" value="{{std_price}}">
	<input type="hidden" id="sellPrice-{{id}}" value="{{final_price}}">
	<input type="hidden" id="discAmount-{{id}}" value="{{discount_amount}}">
	<input type="hidden" id="unitCode-{{id}}" value="{{unit_code}}">
	<input type="hidden" id="itemType-{{id}}" value="{{item_type}}">
	<input type="hidden" id="currentQty-{{id}}" value="{{qty}}">
	<input type="hidden" id="currentPrice-{{id}}" value="{{price}}">
	<input type="hidden" id="currentDisc-{{id}}" value="{{discount_label}}">
	<input type="hidden" id="isEdit-{{id}}" valu="{{is_edit}}">
	<input type="hidden" id="baseCode-{{id}}" value="{{baseCode}}" />
	<input type="hidden" id="baseLineId-{{id}}" value="{{line_id}}" />

	<td class="middle text-center no"></td>
	<td><input type="text" class="form-control input-xs" value="{{product_name}}" readonly/></td>
	<td class="hide"><input type="text" class="form-control input-xs text-center" value="{{unit_code}}" readonly /></td>
	<td>
		<input type="text" class="form-control input-xs text-right line-qty" data-id="{{id}}"
		id="qty-{{id}}" onchange="updateItem('{{id}}')" onclick="$(this).select();" value="{{qty_label}}" />
	</td>
	<td>
		<input type="text" class="form-control input-xs text-right line-price focus"
		data-id="{{id}}" id="price-{{id}}"	value="{{price_label}}"
		onchange="updateItem('{{id}}')"	onclick="$(this).select();" />
	</td>
	<td>
		<input type="text" class="form-control input-xs text-right line-disc focus"
		data-id="{{id}}" 	id="disc-{{id}}" value="{{discount_label}}"
		onchange="updateItem('{{id}}')" onclick="$(this).select();" />
	</td>
	<td>
		<input type="text" class="form-control input-xs text-right line-total"
		id="total-{{id}}" data-id="{{id}}" value="{{total_amount_label}}" readonly />
	</td>
	<td class="middle text-center" style="padding-left:5px; padding-right:5px;">
		<input type="checkbox" class="ace chk-row" value="{{id}}" />
		<label class="lbl"></label>
	</td>
</script>

<script id="details-template" type="text/x-handlebarsTemplate">
	{{#each this}}
	<tr class="font-size-11 pos-rows" id="row-{{id}}" data-id="{{id}}">
		<input type="hidden" class="sell-item" data-id="{{id}}" id="pdCode-{{id}}" value="{{product_code}}">
		<input type="hidden" id="pdName-{{id}}" value="{{product_name}}">
		<input type="hidden" id="taxRate-{{id}}" value="{{vat_rate}}">
		<input type="hidden" id="taxAmount-{{id}}" value="{{vat_amount}}">
		<input type="hidden" id="stdPrice-{{id}}" value="{{std_price}}">
		<input type="hidden" id="sellPrice-{{id}}" value="{{final_price}}">
		<input type="hidden" id="discAmount-{{id}}" value="{{discount_amount}}">
		<input type="hidden" id="unitCode-{{id}}" value="{{unit_code}}">
		<input type="hidden" id="itemType-{{id}}" value="{{item_type}}">
		<input type="hidden" id="currentQty-{{id}}" value="{{qty}}">
		<input type="hidden" id="currentPrice-{{id}}" value="{{price}}">
		<input type="hidden" id="currentDisc-{{id}}" value="{{discount_label}}">
		<input type="hidden" id="isEdit-{{id}}" valu="{{is_edit}}">
		<input type="hidden" id="baseCode-{{id}}" value="{{baseCode}}" />
		<input type="hidden" id="baseLineId-{{id}}" value="{{line_id}}" />

		<td class="middle text-center no"></td>
		<td><input type="text" class="form-control input-xs" value="{{product_name}}" readonly/></td>
		<td class="hide"><input type="text" class="form-control input-xs text-center" value="{{unit_code}}" readonly /></td>
		<td>
			<input type="text" class="form-control input-xs text-right line-qty" data-id="{{id}}"
			id="qty-{{id}}" onchange="updateItem('{{id}}')" onclick="$(this).select();" value="{{qty_label}}"  />
		</td>
		<td>
			<input type="text" class="form-control input-xs text-right line-price focus"
			data-id="{{id}}" id="price-{{id}}"	value="{{price_label}}"
			onchange="updateItem('{{id}}')"	onclick="$(this).select();" />
		</td>
		<td>
			<input type="text" class="form-control input-xs text-right line-disc focus"
			data-id="{{id}}" 	id="disc-{{id}}" value="{{discount_label}}"
			onchange="updateItem('{{id}}')" onclick="$(this).select();" />
		</td>
		<td>
			<input type="text" class="form-control input-xs text-right line-total"
			id="total-{{id}}" data-id="{{id}}" value="{{total_amount_label}}" readonly />
		</td>
		<td class="middle text-center" style="padding-left:5px; padding-right:5px;">
			<input type="checkbox" class="ace chk-row" value="{{id}}" />
			<label class="lbl"></label>
		</td>
	</tr>
	{{/each}}
</script>

<script id="down-payment-template" type="text/x-handlebarsTemplate">
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
</script>
<style>
	.datepicker {
		z-index: 10000;
	}
</style>
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="true">
	<div class="modal-dialog" style="max-width:500px;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
				<h3 class="title text-center" >รับเงิน</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10">
						<div class="input-group width-100">
							<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">วันที่</span>
							<input type="text" class="form-control input-lg text-center focus" id="receive-date" value="<?php echo date('d-m-Y'); ?>">
							<input type="hidden" value="<?php echo date('d-m-Y'); ?>" id="to-day" />
						</div>
					</div>

					<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10 c hide" id="p-cash">
						<div class="input-group width-100">
							<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">เงินสด</span>
							<input type="number" class="form-control input-lg text-center focus p" id="cashReceive" value="" placeholder="เงินสด">
						</div>
					</div>

					<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10 c hide" id="p-transfer">
						<div class="input-group width-100">
							<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">เงินโอน</span>
							<input type="number" class="form-control input-lg text-center focus p" id="transferAmount" value="" placeholder="เงินโอน">
						</div>
					</div>

					<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10 c hide" id="p-card">
						<div class="input-group width-100">
							<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">บัตรเครดิต</span>
							<input type="number" class="form-control input-lg text-center focus p" id="cardAmount" value="" placeholder="บัตรเครดิต">
						</div>
					</div>

					<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10 c hide" id="p-cheque">
						<div class="input-group width-100">
							<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">เช็ค</span>
							<input type="number" class="form-control input-lg text-center focus p" id="chequeAmount" value="" placeholder="เช็ค">
						</div>
					</div>

					<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10 c hide" id="p-account">
						<div class="input-group width-100">
							<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">เข้าบัญชี</span>
							<?php $account = $this->bank_model->get_active_bank(); ?>
							<?php if( ! empty($account)) : ?>
								<?php if(count($account) == 1) : ?>
									<?php $ac = $account[0]; ?>
									<select class="form-control input-lg" id="acc-id">
										<option value="<?php echo $ac->id; ?>" selected><?php echo $ac->acc_name.' #'.$ac->acc_no; ?></option>
									</select>
								<?php else : ?>
									<select class="form-control input-lg focus" id="acc-id" onchange="focusTransfer()">
										<option value="">เลือกบัญชี</option>
									<?php foreach($account as $ac) : ?>
										<option value="<?php echo $ac->id; ?>" <?php echo is_selected($pos->account_id, $ac->id); ?>><?php echo $ac->acc_name.' #'.$ac->acc_no; ?></option>
									<?php endforeach; ?>
									</select>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					</div>

					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="input-group width-100">
							<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">คงเหลือ</span>
							<input type="text" class="form-control input-lg text-center" id="balanceAmount" placeholder="ยอดคงเลือ" disabled>
						</div>
					</div>
					<div class="divider-hidden"></div>

					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="input-group width-100">
							<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">เงินทอน</span>
							<input type="text" class="form-control input-lg text-center" id="changeAmount" placeholder="เงินทอน" disabled>
						</div>
					</div>
					<div class="divider-hidden"></div>
					<div class="col-lg-12 col-md-12 col-sm-12">
						<input type="text" class="form-control input-xs text-center" id="payment-error"
						style="color:#f44336; background-color: transparent !important; font-size: 14px; border:none;" value="" readonly>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-info btn-block" id="btn-submit" onclick="submitPayment()">ตกลง &nbsp; (Enter)</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="holdListModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:300px;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="title text-center" >บิลที่พักไว้</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12 col-xs-12">
						<table class="table table-bordered">
							<tbody id="hold-list"></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script id="list-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr>
			<td class="width-80 middle">{{date_upd}}</td>
			<td class="width-20 middle text-right">
				<button type="button" class="btn btn-sm btn-primary" onclick="unHoldBill({{temp_id}}, {{pos_id}})">เรียกบิล</button>
			</td>
		</tr>
	{{/each}}
</script>


<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="max-width:500px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="title text-center">ข้อมูลสินค้า</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="width-100">
						<table class="table">
							<tbody id="item-data"></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script id="item-template" type="text/x-handlebarsTemplate">
	<tr>
		<td rowspan="7" class="fix-width-120"><img class="img-responsive border-1" src="{{img}}" /></td>
		<td class="fix-width-100">SKU</td>
		<td class="min-width-150">{{item_code}}</td>
	</tr>
	<tr>
		<td>Description</td>
		<td>{{item_name}}</td>
	</tr>
	<tr>
		<td>Type</td>
		<td>{{item_type}}</td>
	</tr>
	<tr>
		<td>Price</td>
		<td>{{price}}</td>
	</tr>
	<tr>
		<td>Tax Rate</td>
		<td>{{vat_rate}}</td>
	</tr>
	<tr>
		<td>Quantity</td>
		<td>{{qty}}</td>
	</tr>
</script>


<div class="modal fade" id="cashInModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="max-width:300px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="title text-center">นำเงินเข้า</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<input type="number" class="form-control input-lg text-center focus" inputmode="none" id="cash-in-amount" value="" placeholder="ยอดเงินนำเข้าลิ้นชัก">
					</div>
					<div class="divider-hidden"></div>
					<div class="col-lg-12 col-md-12 col-sm-12">
						<input type="text" class="form-control input-xs text-center" id="cash-in-error"
						style="color:#f44336; background-color: transparent !important; font-size: 14px; border:none;" value="" readonly>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-info btn-block" onclick="doCashIn()">ตกลง &nbsp; (Enter)</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="cashOutModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="max-width:300px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="title text-center">นำเงินออก</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<input type="number" class="form-control input-lg text-center focus" inputmode="none" id="cash-out-amount" value="" placeholder="ยอดเงินนำออกจากลิ้นชัก">
					</div>
					<div class="divider-hidden"></div>
					<div class="col-lg-12 col-md-12 col-sm-12">
						<input type="text" class="form-control input-xs text-center" id="cash-out-error"
						style="color:#f44336; background-color: transparent !important; font-size: 14px; border:none;" value="" readonly>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-info btn-block" onclick="doCashOut()">ตกลง &nbsp; (Enter)</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="openRoundModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="true">
	<div class="modal-dialog" style="max-width:300px;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
				<h3 class="title text-center" >เปิดรอบการขาย</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="input-group width-100">
							<span class="input-group-addon fix-width-80" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">เงินในลิ้นชัก</span>
							<input type="number" class="form-control input-lg text-center focus" id="open-amount" value="" placeholder="เงินในลิ้นชัก">
						</div>
					</div>
					<div class="divider-hidden"></div>
					<div class="col-lg-12 col-md-12 col-sm-12">
						<input type="text" class="form-control input-xs text-center" id="open-round-error"
						style="color:#f44336; background-color: transparent !important; font-size: 14px; border:none;" value="" readonly>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-info btn-block" onclick="openRound()">ตกลง &nbsp; (Enter)</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="closeRoundModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="true">
	<div class="modal-dialog" style="max-width:300px;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
				<h3 class="title text-center" >ปิดรอบการขาย</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 text-center">
						<p class="font-size-16">ยอดเงินในลิ้นชัก</p>
						<input type="number" class="form-control input-lg text-center focus" id="close-amount" value="" placeholder="เงินในลิ้นชัก">
					</div>
					<div class="divider-hidden"></div>
					<div class="col-lg-12 col-md-12 col-sm-12">
						<input type="text" class="form-control input-xs text-center" id="close-round-error"
						style="color:#f44336; background-color: transparent !important; font-size: 14px; border:none;" value="" readonly>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary btn-block margin-bottom-10" onclick="preCloseRound()">ตกลง &nbsp; (Enter)</button>
				<button class="btn btn-default btn-block" onclick="closeModal('closeRoundModal')">ยกเลิก</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="roundModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" style="max-width:300px;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
				<h3 class="title text-center" id="round-title">POS-1-240214-1</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 text-center">
						<p class="font-size-16"><span class="bold">เปิด : </span><span id="open-time">14/02/2024 09:00:32</span></p>
						<p class="font-size-16"><span class="bold">ปิด : </span><span id="close-time">14/02/2024 19:00:32</span></p>
					</div>
					<div class="divider-hidden"></div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 text-center">
						<p class="font-size-15">ยอดรับทั้งหมด</p>
						<p class="font-size-24 bold blue" id="total-round">25,432.00</p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary btn-block margin-bottom-10" onclick="printPosRound()">พิมพ์ใบสรุปรอบการขาย</button>
				<button class="btn btn-default btn-block" onclick="closeAndGo()">ปิด</button>
			</div>
		</div>
	</div>
</div>
