<form id="inventoryForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<?php $receive_due_yes = $RECEIVE_OVER_DUE == 1 ? 'btn-success' : ''; ?>
	<?php $receive_due_no  = $RECEIVE_OVER_DUE == 0 ? 'btn-danger' : ''; ?>
	<?php $auz_no = $ALLOW_UNDER_ZERO == 0 ? 'btn-success' : ''; ?>
	<?php $auz_yes = $ALLOW_UNDER_ZERO == 1 ? 'btn-danger' : ''; ?>
	<?php $over_po_yes = $ALLOW_RECEIVE_OVER_PO == 1 ? 'btn-success' : ''; ?>
	<?php $over_po_no = $ALLOW_RECEIVE_OVER_PO == 0 ? 'btn-success' : ''; ?>
	<?php $strict_receive_yes = $STRICT_RECEIVE_PO == 1 ? 'btn-success' : ''; ?>
	<?php $strict_receive_no = $STRICT_RECEIVE_PO == 0 ? 'btn-success' : ''; ?>
	<?php $system_bin_yes = $SYSTEM_BIN_LOCATION == 1 ? 'btn-success' : ''; ?>
	<?php $system_bin_no = $SYSTEM_BIN_LOCATION == 0 ? 'btn-success' : ''; ?>
	<?php $wt_btn_yes = is_true($LIMIT_CONSIGN) ? 'btn-success' : ''; ?>
	<?php $wt_btn_no = is_true($LIMIT_CONSIGN) ? '' : 'btn-danger'; ?>
	<?php $wc_btn_yes = is_true($LIMIT_CONSIGNMENT) ? 'btn-success' : ''; ?>
	<?php $wc_btn_no = is_true($LIMIT_CONSIGNMENT) ? '' : 'btn-danger'; ?>
	<?php $strict_transfer_yes = is_true($STRICT_TRANSFER) ? 'btn-success' : ''; ?>
	<?php $strict_transfer_no = is_true($STRICT_TRANSFER) ? '' : 'btn-success'; ?>
	<?php $eom_yes = is_true($TRANSFER_EXPIRE_EOM) ? 'btn-success' : ''; ?>
	<?php $eom_no = is_true($TRANSFER_EXPIRE_EOM) ? '' : 'btn-success'; ?>

	<div class="row">
		<div class="col-sm-3">
			<span class="form-control left-label">สต็อกติดลบได้</span>
		</div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $auz_no; ?>" style="width:50%;" id="btn-auz-no" onClick="toggleAuz(0)">ไม่ได้</button>
				<button type="button" class="btn btn-sm <?php echo $auz_yes; ?>" style="width:50%;" id="btn-auz-yes" onClick="toggleAuz(1)">ได้</button>
			</div>
			<span class="help-block">อนุญาติให้สต็อกติดลบได้</span>
			<input type="hidden" name="ALLOW_UNDER_ZERO" id="allow-under-zero" value="<?php echo $ALLOW_UNDER_ZERO; ?>" />
		</div>
		<div class="divider-hidden"></div>


		<div class="col-sm-3">
			<span class="form-control left-label">รับสินค้าเกินใบสั่งซื้อ</span>
		</div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $over_po_yes; ?>" style="width:50%;" id="btn-ovpo-yes" onClick="toggleOverPo(1)">ได้</button>
				<button type="button" class="btn btn-sm <?php echo $over_po_no; ?>" style="width:50%;" id="btn-ovpo-no" onClick="toggleOverPo(0)">ไม่ได้</button>
			</div>
			<span class="help-block">อนุญาติให้รับสินค้าเกินใบสั่งซื้อหรือไม่</span>
			<input type="hidden" name="ALLOW_RECEIVE_OVER_PO" id="allow-receive-over-po" value="<?php echo $ALLOW_RECEIVE_OVER_PO; ?>" />
		</div>
		<div class="divider-hidden"></div>


		<div class="col-sm-3">
			<span class="form-control left-label">รับสินค้าเกินไปสั่งซื้อ(%)</span>
		</div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-small text-center" name="RECEIVE_OVER_PO"  value="<?php echo $RECEIVE_OVER_PO; ?>" />
		</div>
		<div class="divider-hidden"></div>


		<div class="col-sm-3">
			<span class="form-control left-label">ต้องอนุมัติก่อนรับสินค้าทุกครั้ง</span>
		</div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $strict_receive_yes; ?>" style="width:50%;" id="btn-request-yes" onClick="toggleRequest(1)">ทุกครั้ง</button>
				<button type="button" class="btn btn-sm <?php echo $strict_receive_no; ?>" style="width:50%;" id="btn-request-no" onClick="toggleRequest(0)">ไม่ต้อง</button>
			</div>
			<span class="help-block">หากระบุเป็น "ทุกครั้ง" จะไม่สามารถดึงใบสั่งซื้อมารับสินค้าตรงๆได้ ต้องรับสินค้าผ่านใบขออนุมัติรับสินค้าเท่านั้นและต้องได้รับอนุมัติก่อน</span>
			<input type="hidden" name="STRICT_RECEIVE_PO" id="strict-receive-po" value="<?php echo $STRICT_RECEIVE_PO; ?>" />
		</div>
		<div class="divider-hidden"></div>


		<div class="col-sm-3">
			<span class="form-control left-label">การรับสินค้าเกิน Due</span>
		</div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $receive_due_yes; ?>" style="width:50%;" id="btn-receive-yes" onClick="toggleReceiveDue(1)">รับ</button>
				<button type="button" class="btn btn-sm <?php echo $receive_due_no; ?>" style="width:50%;" id="btn-receive-no" onClick="toggleReceiveDue(0)">ไม่รับ</button>
			</div>
			<span class="help-block">รับหรือไม่รับสินค้าจากใบสั่งซื้อที่เกิน Due date ในใบสั่งซื้อ</span>
			<input type="hidden" name="RECEIVE_OVER_DUE" id="receive-over-due" value="<?php echo $RECEIVE_OVER_DUE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3">
			<span class="form-control left-label">เกินกำหนดรับได้(วัน)</span>
		</div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-small text-center" name="PO_VALID_DAYS"  value="<?php echo $PO_VALID_DAYS; ?>" />
			<span class="help-block">รับสินค้าเกิน Due date ในใบสั่งซื้อได้ไม่เกินจำนวนวันที่กำหนด เช่น กำหนด 30 วัน กำหนดรับวันที่ 30/09 จะรับสินค้าได้ไม่เกินวันที่ 30/10</span>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3">
			<span class="form-control left-label">รหัสคลังซื้อ-ขาย เริ่มต้น</span>
		</div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-large" id="default-warehouse" name="DEFAULT_WAREHOUSE" value="<?php echo $DEFAULT_WAREHOUSE; ?>" required/>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3">
			<span class="form-control left-label">รหัสคลังสินค้าระหว่างทำ</span>
		</div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-large" id="transform-warehouse" name="TRANSFORM_WAREHOUSE" value="<?php echo $TRANSFORM_WAREHOUSE; ?>" required/>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3">
			<span class="form-control left-label">รหัสคลังยืมสินค้า</span>
		</div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-large" id="lend-warehouse" name="LEND_WAREHOUSE" value="<?php echo $LEND_WAREHOUSE; ?>" />
		</div>
		<div class="divider-hidden"></div>


		<div class="col-sm-3">
			<span class="form-control left-label">คุมสต็อกฝากขายแท้</span>
		</div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $wt_btn_no; ?>" style="width:50%;" id="btn-wt-no" onClick="toggleLimitWT(0)">ไม่คุม</button>
				<button type="button" class="btn btn-sm <?php echo $wt_btn_yes; ?>" style="width:50%;" id="btn-wt-yes" onClick="toggleLimitWT(1)">คุม</button>
			</div>
			<span class="help-block">ควมคุมมูลค่า(ทุน)สินค้าคงเหลือในคลังฝากขายแท้ไม่ให้เกินกว่าที่กำหนด</span>
			<input type="hidden" name="LIMIT_CONSIGN" id="limit-consign" value="<?php echo $LIMIT_CONSIGN; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3">
			<span class="form-control left-label">คุมสต็อกฝากขายแท้เทียม</span>
		</div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $wc_btn_no; ?>" style="width:50%;" id="btn-wc-no" onClick="toggleLimitWC(0)">ไม่คุม</button>
				<button type="button" class="btn btn-sm <?php echo $wc_btn_yes; ?>" style="width:50%;" id="btn-wc-yes" onClick="toggleLimitWC(1)">คุม</button>
			</div>
			<span class="help-block">ควมคุมมูลค่า(ทุน)สินค้าคงเหลือในคลังฝากขายแท้ไม่ให้เกินกว่าที่กำหนด</span>
			<input type="hidden" name="LIMIT_CONSIGNMENT" id="limit-consignment" value="<?php echo $LIMIT_CONSIGNMENT; ?>" />
		</div>
		<div class="divider-hidden"></div>


		<div class="col-sm-3">
			<span class="form-control left-label">SYSTEM BIN LOCATION</span>
		</div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $system_bin_yes; ?>" style="width:50%;" id="btn-sys-bin-yes" onClick="toggleSysBin(1)">ใช้</button>
				<button type="button" class="btn btn-sm <?php echo $system_bin_no; ?>" style="width:50%;" id="btn-sys-bin-no" onClick="toggleSysBin(0)">ไม่ใช้</button>
			</div>
			<span class="help-block">ใช้งาน SYSTEM_BIN_LOCATION หรือไม่</span>
			<input type="hidden" name="SYSTEM_BIN_LOCATION" id="system-bin-location" value="<?php echo $SYSTEM_BIN_LOCATION; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3">
			<span class="form-control left-label">ต้องอนุมัติก่อนโอนสินค้าทุกครั้ง</span>
		</div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $strict_transfer_yes; ?>" style="width:50%;" id="btn-transfer-yes" onClick="toggleTransfer(1)">ทุกครั้ง</button>
				<button type="button" class="btn btn-sm <?php echo $strict_transfer_no; ?>" style="width:50%;" id="btn-transfer-no" onClick="toggleTransfer(0)">ไม่ต้อง</button>
			</div>
			<span class="help-block"></span>
			<input type="hidden" name="STRICT_TRANSFER" id="strict-transfer" value="<?php echo $STRICT_TRANSFER; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3">
			<span class="form-control left-label">เอกสารกลุ่มโอนคลังหมดอายุทุกสิ้นเดือน</span>
		</div>
		<div class="col-sm-9">
			<div class="btn-group input-medium">
				<button type="button" class="btn btn-sm <?php echo $eom_yes; ?>" style="width:50%;" id="btn-eom-yes" onClick="toggleTransferEOM(1)">เปิด</button>
				<button type="button" class="btn btn-sm <?php echo $eom_no; ?>" style="width:50%;" id="btn-eom-no" onClick="toggleTransferEOM(0)">ปิด</button>
			</div>
			<span class="help-block">กำหนดให้เอกสารหมดอายุทุกๆ สิ้นเดือนหรือไม่</span>
			<input type="hidden" name="TRANSFER_EXPIRE_EOM" id="transfer-eom" value="<?php echo $TRANSFER_EXPIRE_EOM; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-3">
			<span class="form-control left-label">อายุเอกสารกลุ่มโอนคลัง(วัน)</span>
		</div>
		<div class="col-sm-9">
			<input type="number" class="form-control input-sm input-small text-center" name="TRANSFER_EXPIRATION"  value="<?php echo $TRANSFER_EXPIRATION; ?>" />
			<span class="help-block">เอกสารจะหมดอายุภายในจำนวนวันที่กำหนด กำหนดเป็น 0 หากไม่ต้องการใช้งาน</span>
		</div>
		<div class="divider-hidden"></div>


		<div class="col-sm-9 col-sm-offset-3">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('inventoryForm')">
					<i class="fa fa-save"></i> บันทึก
				</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>



	</div><!--/ row -->
</form>
