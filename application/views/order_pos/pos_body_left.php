<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5" id="left-block">
  <div class="col-lg-12 col-md-12 col-sm-12 padding-0" id="item-div" style="overflow-y:scroll; margin-bottom:5px;">
    <table class="table table-striped border-1 tableFixHead">
      <thead>
        <tr >
          <td class="fix-width-30 text-center fix-header">#</td>
          <td class="min-width-200 text-center fix-header">รายละเอียด</td>
          <td class="fix-width-40 text-center fix-header hide">หน่วย</td>
          <td class="fix-width-60 text-right fix-header">จำนวน</td>
          <td class="fix-width-60 text-right fix-header">ราคา</td>
          <td class="fix-width-60 text-right fix-header">ส่วนลด</td>
          <td class="fix-width-80 text-right fix-header">จำนวนเงิน</td>
          <td class="fix-width-30 text-center fix-header">
            <input type="checkbox" class="ace" id="chk-all" onchange="checkAll()"/>
            <label class="lbl"></label>
          </td>
        </tr>
      </thead>
      <tbody id="item-table">

        <?php if( ! empty($details)) : ?>
          <?php $no = 1; ?>
          <?php $disabled = empty($order->so_code) ? '' : 'readonly'; ?>
          <?php foreach($details as $rs) : ?>
            <tr class="font-size-11 pos-rows" id="row-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>">
              <input type="hidden" class="sell-item" data-id="<?php echo $rs->id; ?>" id="pdCode-<?php echo $rs->id; ?>" value="<?php echo $rs->product_code; ?>">
              <input type="hidden" id="pdName-<?php echo $rs->id; ?>" value="<?php echo $rs->product_name; ?>">
              <input type="hidden" id="taxRate-<?php echo $rs->id; ?>" value="<?php echo $rs->vat_rate; ?>">
              <input type="hidden" id="taxAmount-<?php echo $rs->id; ?>" value="<?php echo $rs->vat_amount; ?>">
              <input type="hidden" id="stdPrice-<?php echo $rs->id; ?>" value="<?php echo $rs->std_price; ?>">
              <input type="hidden" id="sellPrice-<?php echo $rs->id; ?>" value="<?php echo $rs->final_price; ?>">
              <input type="hidden" id="discAmount-<?php echo $rs->id; ?>" value="<?php echo $rs->discount_amount; ?>">
              <input type="hidden" id="unitCode-<?php echo $rs->id; ?>" value="<?php echo $rs->unit_code; ?>">
              <input type="hidden" id="itemType-<?php echo $rs->id; ?>" value="<?php echo $rs->item_type; ?>">
              <input type="hidden" id="currentQty-<?php echo $rs->id; ?>" value="<?php echo $rs->qty; ?>">
              <input type="hidden" id="currentPrice-<?php echo $rs->id; ?>" value="<?php echo $rs->price; ?>">
              <input type="hidden" id="currentDisc-<?php echo $rs->id; ?>" value="<?php echo $rs->discount_label; ?>">
              <input type="hidden" id="isEdit-<?php echo $rs->id; ?>" value="<?php echo $rs->is_edit; ?>" />
              <input type="hidden" id="baseCode-<?php echo $rs->id; ?>" value="<?php echo $rs->baseCode; ?>" />
              <input type="hidden" id="baseLineId-<?php echo $rs->id; ?>" value="<?php echo $rs->line_id; ?>" />
              <input type="hidden" id="avgBillDiscAmount-<?php echo $rs->id; ?>" value="<?php echo $rs->avgBillDiscAmount; ?>" />


              <td class="middle text-center no"><?php echo $no; ?></td>
              <td><input type="text" class="form-control input-xs" value="<?php echo $rs->product_name; ?>" readonly/></td>
              <td class="hide"><input type="text" class="form-control input-xs text-center" value="<?php echo $rs->unit_code; ?>" readonly /></td>
              <td>
                <input type="text" class="form-control input-xs text-right line-qty focus"
                data-id="<?php echo $rs->id; ?>" id="qty-<?php echo $rs->id; ?>"  value="<?php echo number($rs->qty, 2); ?>"
                onchange="updateItem('<?php echo $rs->id; ?>')" onclick="$(this).select()"/>
              </td>
              <td>
                <input type="text" class="form-control input-xs text-right line-price focus"
                data-id="<?php echo $rs->id; ?>" id="price-<?php echo $rs->id; ?>" value="<?php echo number($rs->price, 2); ?>"
                onchange="updateItem('<?php echo $rs->id; ?>')" onclick="$(this).select()" <?php echo $disabled; ?>/>
              </td>

              <td>
                <input type="text" class="form-control input-xs text-right line-disc focus"
                data-id="<?php echo $rs->id; ?>" id="disc-<?php echo $rs->id; ?>" value="<?php echo $rs->discount_label; ?>"
                onchange="updateItem('<?php echo $rs->id; ?>')" onclick="$(this).select();"  <?php echo $disabled; ?>/>
              </td>
              <td>
                <input type="text"
                class="form-control input-xs text-right line-total" id="total-<?php echo $rs->id; ?>"
                value="<?php echo number($rs->total_amount, 2); ?>" readonly />
              </td>
              <td class="middle text-center">
                  <input type="checkbox" class="ace chk-row" value="<?php echo $rs->id; ?>" />
                  <label class="lbl"></label>
              </td>
            </tr>
            <?php $no++; ?>
            <?php $totalQty += $rs->qty; ?>
            <?php $totalBfDisc += $rs->total_amount; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="downPaymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="true">
	<div class="modal-dialog" style="width:700px;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
				<h3 class="title text-center">เงินมัดจำ</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"
					style="min-height:100px; overflow:auto;">
						<table class="table table-bordered" style="margin-bottom:20px;">
							<thead>
								<tr>
									<th class="fix-width-40 text-center">#</th>
									<th class="fix-width-100 text-center">เลขที่</th>
									<th class="fix-width-100 text-center">ช่องทาง</th>
									<th class="fix-width-80 text-center">ยอดเงิน</th>
									<th class="fix-width-80 text-center">ตัดแล้ว</th>
									<th class="fix-width-80 text-center ">คงเหลือ</th>
									<th class="fix-width-100 text-center">ยอดตัดบิลนี้</th>
								</tr>
							</thead>
							<tbody id="down-payment-table">
                <?php if( ! empty($downPaymentList)) : ?>
                  <?php $no = 1; ?>
                  <?php foreach($downPaymentList as $dp) : ?>
                    <tr>
                      <td class="text-center"><?php echo $no; ?></td>
                      <td><?php echo $dp->code; ?></td>
                      <td><?php echo payment_role_name($dp->payment_role); ?></td>
                      <td class="text-right"><?php echo number($dp->amount, 2); ?></td>
                      <td class="text-right"><?php echo number($dp->used, 2); ?></td>
                      <td class="text-right"><?php echo number($dp->available, 2); ?></td>
                      <td class="text-right">
                        <input type="number" class="form-control input-sm text-right down-amount focus"
                					id="down-<?php echo $dp->id; ?>"
                					data-id="<?php echo $dp->id; ?>"
                					data-code="<?php echo $dp->code; ?>"
                					data-reference="<?php echo $dp->reference; ?>"
                					data-reftype="<?php echo $dp->ref_type; ?>"
                					data-available="<?php echo $dp->available; ?>"
                					value="<?php echo $dp->use_amount; ?>"
                					onchange="recalDownPayment()" <?php echo $dp->disabled; ?>/>
                      </td>
                    </tr>
                    <?php $no++; ?>
                  <?php endforeach; ?>
                <?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-info" onclick="closeModal('downPaymentModal')">ตกลง</button>
			</div>
		</div>
	</div>
</div>
