<?php $no = 0; ?>
<div class="row padding-5">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1 table-responsive margin-bottom-15"
  style="min-height:300px; max-height:600px; overflow:auto; border-top:solid 1px #ddd;">
		<table class="table table-bordered tableFixHead" style="min-width:1000px; margin-bottom:20px;">
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
					<th class="min-width-200">ชื่อสินค้า</th>
					<th class="fix-width-100 text-right">OpenQty</th>
					<th class="fix-width-100 text-right">จำนวน</th>
          <th class="fix-width-100 text-right">ราคา/หน่วย</th>
          <th class="fix-width-100 text-right">ส่วนลด</th>
          <th class="fix-width-120 text-right">มูลค่า</th>
				</tr>
			</thead>
			<tbody id="detail-list">
				<?php $n = 1; ?>
      <?php if( ! empty($details)) : ?>
        <?php foreach($details as $rs) : ?>
					<?php $no = $rs->lineNum; ?>
					<?php $linked = $rs->line_status == 'O' && $rs->OpenQty == $rs->qty ? 'N' : 'Y'; ?>
					<?php $disabled = $linked == 'Y' ? 'disabled' : ''; ?>
					<?php $doc->isLinked += $linked == 'Y' ? 1 : 0; ?>
          <tr id="row-<?php echo $no; ?>">
            <td class="middle text-center">
              <label><input type="checkbox" class="ace chk" value="<?php echo $no; ?>" <?php echo $disabled; ?>/><span class="lbl"></span></label>
            </td>
            <td class="middle text-center no"><?php echo $n; ?></td>
            <td class="middle"><input type="text" class="form-control input-sm item-code" value="<?php echo $rs->product_code; ?>" readonly <?php echo $disabled; ?> /></td>
            <td class="middle">
							<input type="text" class="form-control input-sm item-name"
								id="pd-name-<?php echo $no; ?>"
								data-no="<?php echo $no; ?>"
								data-code="<?php echo $rs->product_code; ?>"
								value="<?php echo $rs->product_name; ?>" <?php echo $disabled; ?>/>
						</td>
						<td class="middle text-right">
							<input type="text"
                class="form-control input-sm text-right row-open-qty"
                id="open-qty-<?php echo $no; ?>"
                data-no="<?php echo $no; ?>"
                value="<?php echo number($rs->OpenQty, 2); ?>" readonly <?php echo $disabled; ?>
								/>
						</td>
						<td class="middle text-right">
              <input type="text"
                class="form-control input-sm text-right row-qty"
                id="qty-label-<?php echo $no; ?>"
                onchange="recalQty(<?php echo $no; ?>)"
                data-no="<?php echo $no; ?>"
								data-id="<?php echo $rs->id; ?>"
                data-code="<?php echo $rs->product_code; ?>"
                data-name="<?php echo $rs->product_name; ?>"
                data-style="<?php echo $rs->style_code; ?>"
                data-uom="<?php echo $rs->unit_code; ?>"
                data-cost="<?php echo $rs->cost; ?>"
                data-vatcode="<?php echo $rs->vat_code; ?>"
                data-vatrate="<?php echo $rs->vat_rate; ?>"
                data-count="<?php echo $rs->is_count; ?>"
								data-linked="<?php echo $rs->linked; ?>"
								data-status="<?php echo $rs->line_status; ?>"
								data-openqty="<?php echo $rs->OpenQty; ?>"
								data-qty="<?php echo $rs->qty; ?>"
                value="<?php echo number($rs->qty, 2); ?>" <?php echo $disabled; ?> />
								<!-- ถ้า data-linked == "Y" จะไม่ให้แก้ไขจำนวนที่ต่ำกว่า OpenQty -->
            </td>
            <td class="middle text-right">
              <input type="text"
                class="form-control input-sm text-right row-price"
                id="price-label-<?php echo $no; ?>"
                onchange="recalAmount(<?php echo $no; ?>)"
                data-no="<?php echo $no; ?>"
                value="<?php echo number($rs->price, 2); ?>"	<?php echo $disabled; ?>
								/>

            </td>

            <td class="middle text-right">
              <input type="text"
                class="form-control input-sm text-right row-disc"
                id="disc-label-<?php echo $no; ?>"
                onfocusout="recalAmount(<?php echo $no; ?>)"
                data-no="<?php echo $no; ?>"
                value="<?php echo $rs->discount_label; ?>" <?php echo $disabled; ?>
								/>
            </td>
            <td class="middle text-right">
              <input type="text"
                class="form-control input-sm text-right row-total"
                id="total-label-<?php echo $no; ?>"
                value="<?php echo number($rs->total_amount, 2); ?>" readonly <?php echo $disabled; ?>/>
            </td>
            <input type="hidden" id="disc-amount-<?php echo $no; ?>" value="<?php echo $rs->discount_amount; ?>" />
          </tr>
					<?php $n++; ?>
        <?php endforeach; ?>
      <?php endif; ?>
			</tbody>
		</table>
  </div>
</div>

<input type="hidden" id="no" value="<?php echo $no; ?>" />
