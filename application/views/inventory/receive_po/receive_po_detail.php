
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 border-1 table-responsive" id="receiveTable" style="min-height:200px; padding-left:0; padding-right:0;">
		<table class="table table-bordered" style="font-size:11px; min-width:1030px; margin-bottom:0;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">
						<label>
							<input type="checkbox" class="ace" id="chk-all" onchange="toggleCheckAll($(this))">
							<span class="lbl"></span>
						</label>
					</th>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="min-width-250">ชื่อสินค้า</th>
					<th class="fix-width-100 text-center">หน่วยนับ</th>
					<th class="fix-width-80 text-right">ค้างรับ</th>
					<th class="fix-width-100 text-right">ราคาก่อนส่วนลด</th>
					<th class="fix-width-100 text-right">ส่วนลด(%)</th>
					<th class="fix-width-100 text-right">ราคาหลังส่วนลดก่อนภาษี</th>
					<th class="fix-width-100 text-right">จำนวน</th>
					<th class="fix-width-120 text-right">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="receive-list">
<?php $totalQty = 0; ?>
<?php $totalAmount = 0; ?>
<?php if( ! empty($details)) : ?>
	<?php $no = 1; ?>
	<?php foreach($details as $rs) : ?>
		<?php $active = empty($rs->LineStatus) ? NULL : ($rs->LineStatus == 'O' ? NULL : 'disabled'); ?>
		<?php $limit = empty($rs->limit) ? -1 : $rs->limit; ?>
				<tr id="row-<?php echo $no; ?>">
					<td class="middle text-center">
						<label><input type="checkbox" class="ace chk" value="<?php echo $no; ?>" /><span class="lbl"></span></label>
					</td>
					<td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->product_code; ?></td>
					<td class="middle"><?php echo $rs->product_name; ?></td>
					<td class="middle text-center"><?php echo $rs->unitMsr; ?></td>
					<td class="middle text-right"><?php echo number($rs->before_backlogs, 2); ?></td>
					<td class="middle text-right">
						<input type="text" class="form-control input-sm text-right row-bprice e"
							id="row-bprice-<?php echo $no; ?>" onchange="recalAmount(<?php echo $no; ?>)"
							data-id="<?php echo $no; ?>" value="<?php echo number($rs->PriceBefDi, 3); ?>" <?php echo $active; ?>/>
					</td>
					<td class="middle text-right">
						<input type="number" class="form-control input-sm text-right row-disc e"
							id="row-disc-<?php echo $no; ?>" onchange="recalAmount(<?php echo $no; ?>)"
							data-id="<?php echo $no; ?>" value="<?php echo number($rs->DiscPrcnt, 2); ?>" <?php echo $active; ?>/>
					</td>
					<td class="middle text-right">
						<input type="text" class="form-control input-sm text-right row-price e"
							id="row-price-<?php echo $no; ?>" onchange="recalPrice(<?php echo $no; ?>)"
							data-id="<?php echo $no; ?>" value="<?php echo number($rs->price, 3); ?>" <?php echo $active; ?>/>
					</td>
					<td class="middle text-right">
						<input type="text" class="form-control input-sm text-right row-qty e"
							id="row-qty-<?php echo $no; ?>"
							onchange="recalAmount(<?php echo $no; ?>)"
							data-id="<?php echo $no; ?>"
							data-code="<?php echo $rs->product_code; ?>"
							data-name="<?php echo $rs->product_name; ?>"
							data-vatcode="<?php echo $rs->vatGroup; ?>"
							data-vatrate="<?php echo $rs->vatRate; ?>"
							data-limit="<?php echo $limit; ?>"
							data-backlogs="<?php echo $rs->before_backlogs; ?>"
							data-basecode="<?php echo $rs->baseCode; ?>"
							data-baseline="<?php echo $rs->baseLine; ?>"
							data-baseentry="<?php echo $rs->baseEntry; ?>"
							data-unit="<?php echo $rs->unit_code; ?>"
							data-unitmsr="<?php echo $rs->unitMsr; ?>"
							data-numpermsr="<?php echo $rs->NumPerMsr; ?>"
							data-unitmsr2="<?php echo $rs->unitMsr2; ?>"
							data-numpermsr2="<?php echo $rs->NumPerMsr2; ?>"
							data-uomentry="<?php echo $rs->UomEntry; ?>"
							data-uomentry2="<?php echo $rs->UomEntry2; ?>"
							data-uomcode="<?php echo $rs->UomCode; ?>"
							data-uomcode2="<?php echo $rs->UomCode2; ?>"
							value="<?php echo number($rs->receive_qty, 2); ?>" <?php echo $active; ?>/>
					</td>
					<td class="middle text-right">
						<input type="text" class="form-control input-sm text-right row-total" id="row-total-<?php echo $no; ?>" value="<?php echo number($rs->amount, 2); ?>" disabled />
						<input type="hidden" id="row-vat-amount-<?php echo $no; ?>" value="<?php echo $rs->vatAmount; ?>" />
					</td>
				</tr>
				<?php $no++; ?>
				<?php $totalQty += $rs->receive_qty; ?>
				<?php $totalAmount += $rs->amount; ?>
			<?php endforeach; ?>
		<?php endif; ?>
			</tbody>
		</table>
  </div>

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <div class="form-horizontal">

      <div class="form-group">
        <label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">เจ้าของ</label>
        <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
          <input type="text" class="form-control input-sm" value="<?php echo $this->user_model->get_name($doc->user); ?>" disabled />
  				<input type="hidden" id="owner" value="<?php echo $doc->user; ?>" />
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">หมายเหตุ</label>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
          <textarea id="remark" maxlength="254" rows="3" class="form-control"><?php echo $doc->remark; ?></textarea>
        </div>
      </div>

    </div>
  </div>


	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <div class="form-horizontal">
      <div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">จำนวนรวม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" class="form-control input-sm text-right" id="total-qty" value="<?php echo number($totalQty, 2); ?>" disabled>
        </div>
      </div>

			<div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">มูลค่าก่อนส่วนลด</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="total-amount" class="form-control input-sm text-right" value="<?php echo number($totalAmount, 2); ?>" disabled/>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-6 col-md-5-harf col-sm-4 col-xs-3 control-label no-padding-right">ส่วนลด</label>
        <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-3 padding-5">
          <span class="input-icon input-icon-right">
          <input type="number" id="discPrcnt" class="form-control input-sm" value="<?php echo number($doc->DiscPrcnt, 2); ?>" disabled/>
          <i class="ace-icon fa fa-percent"></i>
          </span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="disc-amount" class="form-control input-sm text-right" onchange="reCalDiscAmount()" value="<?php echo number($doc->DiscAmount, 2); ?>" disabled>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ภาษีมูลค่าเพิ่ม</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="vat-sum" class="form-control input-sm text-right" value="<?php echo number($doc->VatSum, 2); ?>" disabled />
        </div>
      </div>

      <div class="form-group" style="margin-bottom:5px;">
        <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
          <input type="text" id="doc-total" class="form-control input-sm text-right" value="<?php echo number($doc->DocTotal, 2); ?>" disabled/>
        </div>
      </div>
    </div>
  </div>

</div> <!-- row -->


<script id="receive-template" type="text/x-handlebarsTemplate">
	{{#each this}}
		<tr id="row-{{no}}">
			<td class="middle text-center">
				<label><input type="checkbox" class="ace chk" value="{{no}}" /><span class="lbl"></span></label>
			</td>
			<td class="middle text-center no"></td>
			<td class="middle">{{product_code}}</td>
			<td class="middle">{{product_name}}</td>
			<td class="middle text-center">{{unitMsr}}</td>
			<td class="middle text-right">{{backlogs}}</td>
			<td class="middle text-right">
				<input type="text" class="form-control input-sm text-right row-bprice e"
					id="row-bprice-{{no}}" onchange="recalAmount({{no}})"
					data-id="{{no}}" value="{{priceBefDiLabel}}"/>
			</td>
			<td class="middle text-right">
				<input type="number" class="form-control input-sm text-right row-disc e"
					id="row-disc-{{no}}" onchange="recalAmount({{no}})"
					data-id="{{no}}" value="{{discPrcnt}}"/>
			</td>
			<td class="middle text-right">
				<input type="text" class="form-control input-sm text-right row-price e"
					id="row-price-{{no}}" onchange="recalPrice({{no}})"
					data-id="{{no}}" value="{{priceLabel}}" />
			</td>
			<td class="middle text-right">
				<input type="text" class="form-control input-sm text-right row-qty"
					id="row-qty-{{no}}"
          onchange="recalAmount({{no}})"
					data-id="{{no}}"
					data-code="{{product_code}}"
          data-name="{{product_name}}"
					data-vatcode="{{vatCode}}"
          data-vatrate="{{vatRate}}"
					data-unit="{{unitCode}}"
					data-unitmsr="{{unitMsr}}"
					data-numpermsr="{{NumPerMsr}}"
					data-unitmsr2="{{unitMsr2}}"
					data-numpermsr2="{{NumPerMsr2}}"
					data-uomentry="{{UomEntry}}"
					data-uomentry2="{{UomEntry2}}"
					data-uomcode="{{UomCode}}"
					data-uomcode2="{{UomCode2}}"
          data-limit="{{limit}}"
					data-backlogs="{{backlogs}}"
          data-basecode="{{baseCode}}"
          data-baseline="{{baseLine}}"
          data-baseentry="{{baseEntry}}"
          value="{{qtyLabel}}" />
			</td>
			<td class="middle text-right">
				<input type="text" class="form-control input-sm text-right row-total" id="row-total-{{no}}" value="{{amountLabel}}" disabled />
				<input type="hidden" id="row-vat-amount-{{no}}" value="{{vatAmount}}" />
			</td>
		</tr>
	{{/each}}
</script>

<script id="po-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{uid}}">
      <td class="middle text-center">{{no}}</td>
      <td class="middle">{{product_code}}</td>
      <td class="middle">{{product_name}}</td>
			<td class="middle text-right">{{PriceAfDiscLabel}}</td>
      <td class="middle text-right">{{qtyLabel}} {{unitMsr}}</td>
      <td class="middle text-right">
        <input type="text" class="form-control input-sm text-right po-qty"
          id="po-qty-{{uid}}"
          data-uid="{{uid}}"
          data-code="{{product_code}}"
          data-name="{{product_name}}"
          data-basecode="{{baseCode}}"
          data-baseline="{{baseLine}}"
          data-baseentry="{{baseEntry}}"
          data-limit="{{limit}}"
          data-qty="{{qty}}"
					data-backlogs="{{backlogs}}"
          data-price="{{Price}}"
					data-pricebefdi="{{PriceBefDi}}"
					data-discprcnt="{{DiscPrcnt}}"
          data-vatcode="{{vatCode}}"
          data-vatrate="{{vatRate}}"
					data-unit="{{unitCode}}"
					data-unitmsr="{{unitMsr}}"
					data-numpermsr="{{NumPerMsr}}"
					data-unitmsr2="{{unitMsr2}}"
					data-numpermsr2="{{NumPerMsr2}}"
					data-uomentry="{{UomEntry}}"
					data-uomentry2="{{UomEntry2}}"
					data-uomcode="{{UomCode}}"
					data-uomcode2="{{UomCode2}}"
          data-no="{{no}}"
          value="" />
        <input type="hidden" id="uid-{{no}}" value="{{uid}}" />
      </td>
    </tr>
  {{/each}}
</script>
