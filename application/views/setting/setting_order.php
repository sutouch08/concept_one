<form id="orderForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<?php
	//---- อนุญาติให้แก้ไขราคาในออเดอร์หรือไม่
	$btn_price_yes = $ALLOW_EDIT_PRICE == 1 ? 'btn-success' : '';
	$btn_price_no = $ALLOW_EDIT_PRICE == 0 ? 'btn-danger' : '';

	//--- อนุญาติให้แก้ไขส่วนลดในออเดอร์หรือไม่
	$btn_disc_yes = $ALLOW_EDIT_DISCOUNT == 1 ? 'btn-success' : '';
	$btn_disc_no  = $ALLOW_EDIT_DISCOUNT == 0 ? 'btn-danger' : '';

	//--- ไม่อนุญาติให้ขายสินค้ากับลูกค้าที่มียอดค้างชำระเกินกำหนด
	$btn_strict_yes = $STRICT_OVER_DUE == 1 ? 'btn-success' : '';
	$btn_strict_no  = $STRICT_OVER_DUE == 0 ? 'btn-danger' : '';

	$btn_credit_yes = $CONTROL_CREDIT == 1 ? 'btn-success' : '';
	$btn_credit_no  = $CONTROL_CREDIT == 0 ? 'btn-danger' : '';

	$btn_show_stock_yes = $SHOW_SUM_STOCK == 1 ? 'btn-success' : '';
	$btn_show_stock_no  = $SHOW_SUM_STOCK == 0 ? 'btn-primary' : '';

	$btn_over_stock_yes = $ORDER_OVER_STOCK == 1 ? 'btn-success' : '';
	$btn_over_stock_no = $ORDER_OVER_STOCK == 0 ? 'btn-primary' : '';
	?>
	<div class="row">
		<div class="col-sm-3"><span class="form-control left-label">อายุของออเดอร์ ( วัน )</span></div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-small text-center" name="ORDER_EXPIRATION" required value="<?php echo $ORDER_EXPIRATION; ?>" />
			<span class="help-block">กำหนดวันหมดอายุของออเดอร์ หากออเดอร์อยู่ในสถานะ รอการชำระเงิน, รอจัดสินค้า หรือ ไม่บันทึก เกินกว่าจำนวนวันที่กำหนด</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">ประเภทของออเดอร์ที่หมดอายุ</span></div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-xlarge" name="ORDER_EXPIRATION_ROLE" required value="<?php echo $ORDER_EXPIRATION_ROLE; ?>" />
			<span class="help-block">
				กำหนดประเภทของออเดอร์ที่ทำให้หมดอายุ <br>
				S = ขาย, C = ฝากขายเทียม, N = ฝากขายแท้, P = สปอนเซอร์, U = อภินันท์, L = ยืม, T = แปรสภาพ(ขาย), Q = แปรสภาพ(สต็อก)
			</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">การจำกัดการแสดงผลสต็อก</span></div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-small text-center" name="STOCK_FILTER" required value="<?php echo $STOCK_FILTER; ?>" />
			<span class="help-block">กำหนดจำนวนสินค้าคงเหลือสูงสุดที่จะแสดงใหเห็น ถ้าไม่ต้องการใช้กำหนดเป็น 0 </span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">แสดงยอดรวมสต็อก</span></div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $btn_show_stock_yes; ?>" style="width:50%;" id="btn-show-stock-yes" onClick="toggleShowStock(1)">เปิด</button>
				<button type="button" class="btn btn-sm <?php echo $btn_show_stock_no; ?>" style="width:50%;" id="btn-show-stock-no" onClick="toggleShowStock(0)">ปิด</button>
			</div>
			<input type="hidden" name="SHOW_SUM_STOCK" id="show-sum-stock" value="<?php echo $SHOW_SUM_STOCK; ?>" />
			<span class="help-block">แสดงยอดรวมสินค้าคงเหลือในหน้าออเดอร์หรือไม่ (หากเปิดไว้ระบบจะทำงานช้าลง)</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">รหัสลูกค้าเริ่มต้น</span></div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-small text-center" name="DEFAULT_CUSTOMER" required value="<?php echo $DEFAULT_CUSTOMER; ?>" />
			<span class="help-block">ลูกค้าเริ่มต้นหากไม่มีการกำหนดรหัสลูกค้า</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">ควบคุมเครดิต</span></div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $btn_credit_yes; ?>" style="width:50%;" id="btn-credit-yes" onClick="toggleControlCredit(1)">คุม</button>
				<button type="button" class="btn btn-sm <?php echo $btn_credit_no; ?>" style="width:50%;" id="btn-credit-no" onClick="toggleControlCredit(0)">ไม่คุม</button>
			</div>
			<input type="hidden" name="CONTROL_CREDIT" id="control-credit" value="<?php echo $CONTROL_CREDIT; ?>" />
			<span class="help-block">ใช้การควบคุมเครดิตหรือไม่ หากควบคุมจะไม่สามารถเปิดออเดอร์ได้ถ้าเครดิตคงเหลือไม่เพียงพอ</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">วันเพิ่มในการคุมเครดิต</span></div>
		<div class="col-sm-9">
			<input type="number" class="form-control input-sm input-small text-center" name="OVER_DUE_DATE" required value="<?php echo $OVER_DUE_DATE; ?>" />
			<span class="help-block">จำนวนวันเพิ่มจากวันครบกำหนดชำระ เช่น เครดติ 30 วัน เพิ่มอีก 30 วัน</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">มียอดค้างชำระเกินกำหนด</span></div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $btn_strict_yes; ?>" style="width:50%;" id="btn-strict-yes" onClick="toggleStrictDue(1)">ไม่ขาย</button>
				<button type="button" class="btn btn-sm <?php echo $btn_strict_no; ?>" style="width:50%;" id="btn-strict-no" onClick="toggleStrictDue(0)">ขาย</button>
			</div>
			<span class="help-block">ไม่อนุญาติให้ขายสินค้าให้ลูกค้าที่มียอดค้างชำระเกินวันที่กำหนดในการคุมเครดิต</span>
			<input type="hidden" name="STRICT_OVER_DUE" id="strict-over-due" value="<?php echo $STRICT_OVER_DUE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">การแก้ไขส่วนลดในออเดอร์</span></div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $btn_disc_yes; ?>" style="width:50%;" id="btn-disc-yes" onClick="toggleEditDiscount(1)">เปิด</button>
				<button type="button" class="btn btn-sm <?php echo $btn_disc_no; ?>" style="width:50%;" id="btn-disc-no" onClick="toggleEditDiscount(0)">ปิด</button>
			</div>
			<span class="help-block">กรณีปิดจะไม่สามารแก้ไขส่วนลดในออเดอร์ได้ ส่วนลดจะถูกคำนวณโดยระบบเท่านั้น</span>
			<input type="hidden" name="ALLOW_EDIT_DISCOUNT" id="allow-edit-discount" value="<?php echo $ALLOW_EDIT_DISCOUNT; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">การแก้ไขราคาในออเดอร์</span></div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $btn_price_yes; ?>" style="width:50%;" id="btn-price-yes" onClick="toggleEditPrice(1)">เปิด</button>
				<button type="button" class="btn btn-sm <?php echo $btn_price_no; ?>" style="width:50%;" id="btn-price-no" onClick="toggleEditPrice(0)">ปิด</button>
			</div>
			<span class="help-block">กรณีปิดจะไม่สามารแก้ไขราคาขายสินค้าในออเดอร์ได้ จะใช้ราคาขายในระบบเท่านั้น</span>
			<input type="hidden" name="ALLOW_EDIT_PRICE" id="allow-edit-price" value="<?php echo $ALLOW_EDIT_PRICE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">สั่งสินค้าเกินสต็อก</span></div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $btn_over_stock_yes; ?>" style="width:50%;" id="btn-over-yes" onClick="toggleOverStock(1)">ได้</button>
				<button type="button" class="btn btn-sm <?php echo $btn_over_stock_no; ?>" style="width:50%;" id="btn-over-no" onClick="toggleOverStock(0)">ไม่ได้</button>
			</div>
			<span class="help-block">กรณีปิดจะไม่สามารแก้ไขราคาขายสินค้าในออเดอร์ได้ จะใช้ราคาขายในระบบเท่านั้น</span>
			<input type="hidden" name="ORDER_OVER_STOCK" id="order-over-stock" value="<?php echo $ORDER_OVER_STOCK; ?>" />
		</div>
		<div class="divider-hidden"></div>		

		<div class="col-sm-3"><span class="form-control left-label">Default Transfer Payment</span></div>
		<div class="col-sm-9">
			<select class="form-control input-sm input-medium" name="DEFAULT_TRANSFER_PAYMENT" id="default-transfer-payment" >
				<?php echo select_transfer_payment($DEFAULT_TRANSFER_PAYMENT); ?>
			</select>
			<span class="help-block">เลือกช่องทางเริ่มต้นสำหรับการชำระด้วยการโอนเงิน</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3"><span class="form-control left-label">พนักงานขายเริ่มต้น</span></div>
		<div class="col-sm-9">
			<select class="form-control input-sm input-medium" name="DEFAULT_SALES_ID" id="default-sale-id" >
				<option value="">โปรดเลือก</option>
				<?php echo select_saleman($DEFAULT_SALES_ID); ?>
			</select>
			<span class="help-block">ระบุพนักงานขายเริ่มต้น หากไม่ได้ระบุชื่อพนักงานขาย ระบบจะให้พนักงานนี้ในการบันทึกขาย</span>
		</div>
		<div class="divider-hidden"></div>

		<!--
		<div class="col-sm-3"><span class="form-control left-label">ช่องทางขายเว็บไซต์</span></div>
		<div class="col-sm-9">
		<select class="form-control input-sm input-medium" name="WEB_SITE_CHANNELS_CODE" id="web-site-channels-code" >
		<?php echo select_channels($WEB_SITE_CHANNELS_CODE); ?>
	</select>
	<span class="help-block">เลือกรหัสสำหรับการขายบนเว็บไซต์(ใช้ในการ import order)</span>
</div>
<div class="divider-hidden"></div>

<div class="col-sm-3"><span class="form-control left-label">คลังเว็บไซต์</span></div>
<div class="col-sm-9">
	<select class="form-control input-sm input-medium" name="WEB_SITE_WAREHOUSE_CODE" >
		<option value="">ทั้งหมด</option>
		<?php echo select_sell_warehouse($WEB_SITE_WAREHOUSE_CODE); ?>
	</select>
	<span class="help-block">เลือกคลังสำหรับการขายบนเว็บไซต์(ใช้ในการ import order)</span>
</div>
<div class="divider-hidden"></div>

<div class="col-sm-3"><span class="form-control left-label">รหัสลูกค้าเว็บไซต์ COD</span></div>
<div class="col-sm-9">
	<input type="text" class="form-control input-sm input-small text-center" name="CUSTOMER_CODE_COD" required value="<?php echo $CUSTOMER_CODE_COD; ?>" />
	<span class="help-block">กำหนดรหัสลูกค้า สำหรับช่องทางการชำระเงินแบบ COD บนเว็บไซต์ (ใช้ในการ import order)</span>
</div>
<div class="divider-hidden"></div>

<div class="col-sm-3"><span class="form-control left-label">รหัสลูกค้าเว็บไซต์ 2C2P</span></div>
<div class="col-sm-9">
	<input type="text" class="form-control input-sm input-small text-center" name="CUSTOMER_CODE_2C2P" required value="<?php echo $CUSTOMER_CODE_2C2P; ?>" />
	<span class="help-block">กำหนดรหัสลูกค้า สำหรับช่องทางการชำระเงินแบบ 2C2P บนเว็บไซต์ (ใช้ในการ import order)</span>
</div>
<div class="divider-hidden"></div>

<div class="col-sm-3"><span class="form-control left-label">รหัสนำหน้าเลขที่จัดส่ง</span></div>
<div class="col-sm-9">
	<input type="text" class="form-control input-sm input-small text-center" name="PREFIX_SHIPPING_NUMBER" value="<?php echo $PREFIX_SHIPPING_NUMBER; ?>" />
	<span class="help-block">รหัสนำหน้าเลขที่จัดส่ง โดยใช้เลขที่ออเดอร์ของ Warrix12 แล้วเติมรหัสนี้นำหน้าและบันทึกเป็นเลขที่จัดส่งทันที ใช้ในการ import ออเดอร์จากเว็บไซต์</span>
</div>


<div class="col-sm-3"><span class="form-control left-label">รหัสรายได้ค่าจัดส่ง</span></div>
<div class="col-sm-9">
	<input type="text" class="form-control input-sm input-large text-center" name="SHIPPING_ITEM_CODE" value="<?php echo $SHIPPING_ITEM_CODE; ?>" />
	<span class="help-block">รหัสสินค้ารายได้ค่าจัดส่ง ที่จะเพิ่มเป็นรายการสินค้าให้ในออเดอร์ที่มีการคิดค่าจัดส่ง</span>
</div>

<div class="col-sm-3"><span class="form-control left-label">น้ำหนักเหมารวม(กรัม)</span></div>
<div class="col-sm-9">
	<input type="text" class="form-control input-sm input-small text-center" name="DHL_DEFAULT_WEIGHT" value="<?php echo $DHL_DEFAULT_WEIGHT; ?>" />
	<span class="help-block">น้ำหนักเหมารวมในการจัดส่ง 1 แพ็คเกจ(กรัม)</span>
</div>

<div class="col-sm-3"><span class="form-control left-label">อัพโหลดออเดอร์(รายการ)/ครั้ง</span></div>
<div class="col-sm-9">
	<input type="text" class="form-control input-sm input-small text-center" name="IMPORT_ROWS_LIMIT" value="<?php echo $IMPORT_ROWS_LIMIT; ?>" />
	<span class="help-block">จำกัดจำนวนรายการที่ออเดอร์ที่สามารถนำเข้าระบบได้ครั้งละไม่เกินรายการที่กำหนด เพื่อไม่ให้ระบบเกิดข้อผิดพลาด</span>
</div>

-->
<div class="col-sm-9 col-sm-offset-3">
	<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
		<button type="button" class="btn btn-sm btn-success" onClick="updateConfig('orderForm')"><i class="fa fa-save"></i> บันทึก</button>
	<?php endif; ?>
</div>
<div class="divider-hidden"></div>
</div>
</form>
