
<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    	<label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>เล่มเอกสาร</label>
      <select class="form-control input-sm h" id="is-term" disabled>
        <option value="">เลือก</option>
        <option value="0" <?php echo is_selected('0', $order->is_term); ?>>ขายเงินสด</option>
        <option value="1" <?php echo is_selected('1', $order->is_term); ?>>ขายเงินเชื่อ</option>
      </select>
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>ชนิด VAT</label>
      <select class="form-control input-sm h" id="vat-type" onchange="toggleVatType()" disabled>
        <option value="">เลือก</option>
        <option value="E" <?php echo is_selected('E', $order->vat_type); ?>>แยกนอก</option>
        <option value="I" <?php echo is_selected('I', $order->vat_type); ?>>รวมใน</option>
        <option value="N" <?php echo is_selected('N', $order->vat_type); ?>>ไม่ VAT</option>
      </select>
      <input type="hidden" id="tax-status" value="<?php echo $order->TaxStatus; ?>">
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    	<label>วันที่</label>
			<input type="text" class="form-control input-sm text-center h" name="date" id="date" value="<?php echo thai_date($order->date_add); ?>"  readonly disabled />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
			<label>รหัสลูกค้า</label>
			<input type="text" class="form-control input-sm text-center h" id="customer_code" name="customer_code" value="<?php echo $order->customer_code; ?>" disabled />
		</div>
    <div class="col-lg-4-harf col-md-3 col-sm-6-harf col-xs-8 padding-5">
    	<label>ชื่อลูกค้า</label>
			<input type="text" class="form-control input-sm h" id="customer" name="customer" value="<?php echo $order->customer_name; ?>"  disabled />
    </div>
		<div class="col-lg-5-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
	    <label>ผู้ติดต่อ</label>
      <input type="text" class="form-control input-sm h" id="customer_ref" name="customer_ref" value="<?php echo str_replace('"', '&quot;',$order->customer_ref); ?>" disabled />
    </div>
		<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
	    <label>เบอร์โทร</label>
			<input type="text" class="form-control input-sm h" name="phone" id="phone" value="<?php echo $order->phone; ?>" disabled />
	  </div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>เลขที่ผู้เสียภาษี</label>
			<input type="text" class="form-control input-sm h" id="tax-id" value="<?php echo $order->tax_id; ?>" disabled/>
		</div>
		<div class="col-lg-1 col-md-2 col-sm-3 col-xs-6 padding-5">
			<label>รหัสสาขา</label>
			<input type="text" class="form-control input-sm h" id="branch-code" value="<?php echo $order->branch_code; ?>" disabled/>
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
			<label>ชื่อสาขา</label>
			<input type="text" class="form-control input-sm h" id="branch-name" value="<?php echo $order->branch_name; ?>" disabled/>
		</div>
		<div class="col-lg-5 col-md-10-harf col-sm-7 col-xs-12 padding-5">
			<label>ที่อยู่เปิดบิล</label>
			<input type="text" class="form-control input-sm h" id="address" value="<?php echo $order->address; ?>" disabled/>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
			<label>ตำบล</label>
			<input type="text" class="form-control input-sm h" id="sub-district" value="<?php echo $order->sub_district; ?>" disabled/>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
			<label>อำเภอ</label>
			<input type="text" class="form-control input-sm h" id="district" value="<?php echo $order->district; ?>" disabled/>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
			<label>จังหวัด</label>
			<input type="text" class="form-control input-sm h" id="province" value="<?php echo $order->province; ?>" disabled/>
		</div>
		<div class="col-lg-1 col-md-2 col-sm-3 col-xs-6 padding-5">
			<label>รหัสไปรษณีย์</label>
			<input type="text" class="form-control input-sm h" id="postcode" value="<?php echo $order->postcode; ?>" disabled/>
		</div>

		<div class="divider"></div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>อ้างอิง</label>
			<input type="text" class="form-control input-sm text-center h" name="reference" id="reference" value="<?php echo $order->reference; ?>" disabled />
		</div>

    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    	<label>ช่องทางขาย</label>
			<select class="form-control input-sm h" name="channels" id="channels" disabled>
				<option value="">เลือกรายการ</option>
				<?php echo select_channels($order->channels_code); ?>
			</select>
    </div>

		<div class="col-lg-2 col-md-2-harf col-sm-4 col-xs-6 padding-5">
			<label>คลัง</label>
	    <select class="form-control input-sm h" name="warehouse" id="warehouse" disabled>
				<option value="">เลือกคลัง</option>
				<?php echo select_sell_warehouse($order->warehouse_code); ?>
			</select>
	  </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		 	<label>SAP No.</label>
		  <input type="text" class="form-control input-sm" value="<?php echo $order->inv_code; ?>" disabled />
		</div>

		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
			<label>ใบสั่งขาย</label>
	    <input type="text" class="form-control input-sm text-center" id="so-code" placeholder="ใบสั่งขาย" value="<?php echo $order->so_code; ?>" disabled />
	  </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
		 	<label>วันที่จัดส่ง</label>
		  <input type="text" class="form-control input-sm text-center" id="ship-date" value="<?php echo thai_date($order->shipped_date, FALSE); ?>" disabled />
		</div>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label class="display-block not-show">x</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit-ship-date" onclick="activeShipDate()">เปลี่ยนวันที่จัดส่ง</button>
			<button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update-ship-date" onclick="updateShipDate()">Update</button>
		</div>
</div>
<hr class="margin-top-15"/>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
    <?php if( $this->pm->can_edit || $this->pm->can_add ) : ?>
      <button type="button" class="btn btn-sm btn-primary" id="btn-confirm-order" onclick="confirmOrder()">เปิดบิลและตัดสต็อก</button>
    <?php endif; ?>
  </div>
</div>
<hr/>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered" style="min-width:800px;">
      <thead>
        <tr class="font-size-12">
          <th class="width-5 text-center">ลำดับ</th>
          <th class="width-35 text-center">สินค้า</th>
          <th class="width-10 text-center">ราคา</th>
					<th class="width-10 text-center">ส่วนลด</th>
          <th class="width-10 text-center">ออเดอร์</th>
          <th class="width-10 text-center">จัด</th>
				<?php if($useQc) : ?>
          <th class="width-10 text-center">ตรวจ</th>
				<?php endif; ?>
          <th class="width-10 text-center">มูลค่า</th>
        </tr>
      </thead>
      <tbody>
<?php
	$no = 1;
	$totalQty = 0;
	$totalPrepared = 0;
	$totalQc = 0;
	$totalAmount = 0;
	$billQty = 0;
	$vatSum = 0;
	$discSum = 0;
	$vat_type = $order->vat_type;
?>

<?php if(!empty($details)) : ?>
<?php   foreach($details as $rs) :  ?>
	<?php $color = ""; ?>
	<?php if($useQc) : ?>
		<?php $color = $rs->is_count != 0 && ($rs->order_qty != $rs->qc) ? "red" : $color; ?>
	<?php else : ?>
		<?php $color = $rs->is_count != 0 && ($rs->order_qty != $rs->prepared) ? "red" : $color; ?>
	<?php endif; ?>

        <tr class="font-size-12 <?php echo $color; ?>">
          <td class="text-center">
            <?php echo $no; ?>
          </td>

          <!--- รายการสินค้า ที่มีการสั่งสินค้า --->
          <td>
            <?php echo limitText($rs->product_code.' : '. $rs->product_name, 100); ?>
          </td>

          <!--- ราคาสินค้า  --->
          <td class="text-center">
            <?php echo number($rs->price, 2); ?>
          </td>

					<!--- ส่วนลด  --->
					<td class="text-center">
						<?php echo discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
					</td>

          <!---   จำนวนที่สั่ง  --->
          <td class="text-center">
            <?php echo number($rs->order_qty); ?>
          </td>

          <!--- จำนวนที่จัดได้  --->
          <td class="text-center">
            <?php echo $rs->is_count == 0 ? number($rs->order_qty) : number($rs->prepared); ?>
          </td>

          <!--- จำนวนที่ตรวจได้ --->
				<?php if($useQc) : ?>
          <td class="text-center">
            <?php echo $rs->is_count == 0 ? number($rs->order_qty) : number($rs->qc); ?>
          </td>
				<?php endif; ?>

          <td class="text-right">
					<?php if($useQc) : ?>
            <?php echo $rs->is_count == 0 ? number($rs->final_price * $rs->order_qty) : number( $rs->final_price * $rs->qc , 2); ?>
					<?php else : ?>
						<?php echo $rs->is_count == 0 ? number($rs->final_price * $rs->order_qty) : number( $rs->final_price * $rs->prepared , 2); ?>
					<?php endif; ?>
          </td>

        </tr>
<?php
      $totalQty += $rs->order_qty;
      $totalPrepared += ($rs->is_count == 0 ? $rs->order_qty : $rs->prepared);

			if($useQc)
			{
				$totalQc += ($rs->is_count == 0 ? $rs->order_qty : $rs->qc);
			}

			if($useQc)
      {
				$qty = $rs->order_qty > $rs->qc ? $rs->qc : $rs->order_qty;
				$amount = ($rs->is_count == 0 ? $rs->final_price * $rs->order_qty : $rs->final_price * $rs->qc);
				$sumBillDiscAmount = $amount * $rs->avgBillDiscAmount;
				$amountAfDisc = $amount - $sumBillDiscAmount;
				$vat_amount = get_vat_amount($amountAfDisc, $rs->vat_rate, $vat_type);
				$billQty += $qty;
        $totalAmount += $amount;
				$vatSum += $vat_amount;
				$discSum += $sumBillDiscAmount;
      }
      else
      {
				$qty = $rs->order_qty > $rs->prepared ? $rs->prepared : $rs->order_qty;
				$amount = ($rs->is_count == 0 ? $rs->final_price * $rs->order_qty : $rs->final_price * $rs->prepared);
				$sumBillDiscAmount = $amount * $rs->avgBillDiscAmount;
				$amountAfDisc = $amount - $sumBillDiscAmount;
				$vat_amount = get_vat_amount($amountAfDisc, $rs->vat_rate, $vat_type);
				$billQty += $qty;
				$totalAmount += $amount;
				$vatSum += $vat_amount;
				$discSum += $sumBillDiscAmount;
      }

      $no++;
?>
<?php   endforeach; ?>
        <tr class="font-size-12">
          <td colspan="4" class="text-right font-size-14">
            รวม
          </td>

          <td class="text-center">
            <?php echo number($totalQty); ?>
          </td>

          <td class="text-center">
            <?php echo number($totalPrepared); ?>
          </td>
					<?php if($useQc) : ?>
          <td class="text-center">
            <?php echo number($totalQc); ?>
          </td>
					<?php endif; ?>

          <td class="text-right">
            <?php echo number($totalAmount, 2); ?>
          </td>
        </tr>
<?php else : ?>
      <tr><td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td></tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php
	$discPrcnt = $order->bDiscText > 0 ? $order->bDiscText * 0.01 : ($totalQty > 0 ? round($billQty/$totalQty, 2) : 0);
	$bDiscAmount = $totalAmount * $discPrcnt;
	$amountAfDisc = $totalAmount - $discSum;
	$WhtAmount = $order->WhtPrcnt > 0 ? ($vat_type == 'E' ? $amountAfDisc * ($order->WhtPrcnt * 0.01) : ($amountAfDisc - $vatSum) * ($order->WhtPrcnt * 0.01)) : 0;
	$docTotal = $vat_type == 'E' ? $amountAfDisc + $vatSum : $amountAfDisc;
?>
<div class="divider-hidden"></div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">พนักงานขาย</label>
      <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
        <select class="width-100 edit" id="sale-id" name="sale_id" disabled>
          <?php echo select_saleman($order->sale_code); ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">Owner</label>
      <div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
        <input type="text" class="form-control input-sm" value="<?php echo $order->user; ?>" disabled>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">Remark</label>
      <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
        <textarea id="remark" maxlength="254" rows="3" class="form-control" onchange="updateRemark()" disabled><?php echo $order->remark; ?></textarea>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-lg-3 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">จำนวน</label>
      <div class="col-lg-2-harf col-md-4 col-sm-5 col-xs-6 padding-5">
        <input type="text" class="form-control input-sm text-center" id="total-qty" value="<?php echo number($billQty); ?>" disabled>
      </div>
      <label class="col-lg-2-harf col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">มูลค่ารวม</label>
      <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
        <input type="text" class="form-control input-sm text-right" id="total-amount" value="<?php echo number($totalAmount, 2); ?>" disabled>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-6 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ส่วนลด</label>
      <div class="col-lg-2 col-md-4 col-sm-5 col-xs-6 padding-5">
        <span class="input-icon input-icon-right">
          <input type="number" id="bill-disc-percent" class="form-control input-sm text-center" value="<?php echo number($order->bDiscText, 2); ?>" disabled />
          <i class="ace-icon fa fa-percent"></i>
        </span>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
        <input type="text" class="form-control input-sm text-right" id="bill-disc-amount" value="<?php echo number($bDiscAmount, 2); ?>" disabled />
      </div>
    </div>

    <div class="form-group <?php echo ($order->TaxStatus == 'Y' ? '' : 'hide'); ?>" id="bill-wht">
      <label class="col-lg-6 col-md-5-harf col-sm-4 col-xs-3 control-label no-padding-right">หัก ณ ที่จ่าย</label>
      <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-3 padding-5">
        <span class="input-icon input-icon-right">
        <input type="number" id="whtPrcnt" class="form-control input-sm text-center" value="<?php echo number($order->WhtPrcnt, 2); ?>" disabled/>
        <i class="ace-icon fa fa-percent"></i>
        </span>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
        <input type="hidden" id="wht-amount" value="<?php echo $order->WhtAmount; ?>" />
        <input type="text" id="wht-amount-label" class="form-control input-sm text-right" value="<?php echo number($WhtAmount, 2); ?>" disabled />
      </div>
    </div>

    <div class="form-group <?php echo ($order->TaxStatus == 'Y' ? '' : 'hide'); ?>" id="bill-vat">
      <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">VAT</label>
      <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
        <input type="hidden" id="vat-total" value="<?php echo $order->VatSum; ?>"/>
        <input type="text" id="vat-total-label" class="form-control input-sm text-right" value="<?php echo number($vatSum, 2); ?>" disabled >
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
      <div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
        <input type="text" class="form-control input-sm text-right" id="doc-total" value="<?php echo number($docTotal, 2); ?>" disabled/>
      </div>
    </div>
  </div> <!-- form horizontal -->
</div>

<script>
	$('#ship-date').datepicker({
		'dateFormat' : 'dd-mm-yy'
	});

	function activeShipDate() {
		$('#ship-date').removeAttr('disabled');
		$('#btn-edit-ship-date').addClass('hide');
		$('#btn-update-ship-date').removeClass('hide');
	}

	function updateShipDate() {
		let shipDate = $('#ship-date').val();
		let order = $('#order_code').val();

		$.ajax({
			url:HOME + 'update_shipped_date',
			type:'POST',
			cache:false,
			data:{
				'order_code' : order,
				'shipped_date' : shipDate
			},
			success:function(rs) {
				rs = $.trim(rs);
				if(rs === 'success') {
					$('#ship-date').attr('disabled', 'disabled');
					$('#btn-update-ship-date').addClass('hide');
					$('#btn-edit-ship-date').removeClass('hide');
				}
				else {
					swal({
						title:'Error!',
						type:'error',
						text:rs
					});
				}
			}
		})
	}
</script>
