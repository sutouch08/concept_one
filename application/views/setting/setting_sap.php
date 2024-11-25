<form id="sapForm" method="post" action="<?php echo $this->home; ?>/update_config">
	<div class="row">
		<div class="col-sm-4">
			<span class="form-control left-label">Currency (สกุลเงิน)</span>
		</div>
		<div class="col-sm-8">
			<input type="text" class="form-control input-sm input-small" name="CURRENCY"  value="<?php echo $CURRENCY; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Item group code (รหัสกลุ่มสินค้า)</span>
		</div>
		<div class="col-sm-8">
			<input type="text" class="form-control input-sm input-small" name="ITEM_GROUP_CODE" value="<?php echo $ITEM_GROUP_CODE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Purchase VAT code (รหัสภาษีซื้อ)</span>
		</div>
		<div class="col-sm-8">
			<input type="text" class="form-control input-sm input-small" name="PURCHASE_VAT_CODE" value="<?php echo $PURCHASE_VAT_CODE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Purchase VAT rate (อัตราภาษีซื้อ)</span>
		</div>
		<div class="col-sm-8">
			<input type="text" class="form-control input-sm input-small" name="PURCHASE_VAT_RATE" value="<?php echo $PURCHASE_VAT_RATE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Sell VAT code (รหัสภาษีขาย)</span>
		</div>
		<div class="col-sm-8">
			<input type="text" class="form-control input-sm input-small" name="SALE_VAT_CODE" value="<?php echo $SALE_VAT_CODE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Sell VAT rate (อัตราภาษีขาย)</span>
		</div>
		<div class="col-sm-8">
			<input type="text" class="form-control input-sm input-small" name="SALE_VAT_RATE" value="<?php echo $SALE_VAT_RATE; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Price List Number (ราคาทุน)</span>
		</div>
		<div class="col-sm-8">
			<input type="text" class="form-control input-sm input-small" name="SAP_COST_LIST" value="<?php echo $SAP_COST_LIST; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">Price List Number (ราคาขาย)</span>
		</div>
		<div class="col-sm-8">
			<input type="text" class="form-control input-sm input-small" name="SAP_PRICE_LIST" value="<?php echo $SAP_PRICE_LIST; ?>" />
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">รหัสผังบัญชีเงินสด</span>
		</div>
		<div class="col-sm-4">
			<select class="width-100" name="SAP_CASH_ACCT_CODE" id="sap-cash-acct-code">
				<option value="<?php echo $SAP_CASH_ACCT_CODE; ?>">กรุณาเลือก</option>
				<?php if( ! empty($acc_code_select)) : ?>
					<?php foreach($acc_code_select as $ac) : ?>
						<option value="<?php echo $ac->AcctCode; ?>" <?php echo is_selected(strval($ac->AcctCode), strval($SAP_CASH_ACCT_CODE)); ?>>
							<?php echo $ac->AcctCode.' | '.$ac->AcctName; ?>
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">รหัสผังบัญชีเช็ค</span>
		</div>
		<div class="col-sm-4">
			<select class="width-100" name="SAP_CHECK_ACCT_CODE" id="sap-check-acct-code">
				<option value="<?php echo $SAP_CHECK_ACCT_CODE; ?>">กรุณาเลือก</option>
				<?php if( ! empty($acc_code_select)) : ?>
					<?php foreach($acc_code_select as $ac) : ?>
						<option value="<?php echo $ac->AcctCode; ?>" <?php echo is_selected(strval($ac->AcctCode), strval($SAP_CHECK_ACCT_CODE)); ?>>
							<?php echo $ac->AcctCode.' | '.$ac->AcctName; ?>
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
		<div class="divider-hidden"></div>

		<div class="col-sm-4">
			<span class="form-control left-label">รหัสผังบัญชีบัตรเครดิต</span>
		</div>
		<div class="col-sm-4">
			<select class="width-100" name="SAP_CREDIT_CARD_ID" id="sap-card-acct-code" onchange="update_sap_credit_card_acc_code()">
				<option value="">กรุณาเลือก</option>
				<?php if( ! empty($credit_card_select)) : ?>
					<?php foreach($credit_card_select as $cd) : ?>
						<option value="<?php echo $cd->CreditCard; ?>"
							data-code="<?php echo $cd->AcctCode; ?>" <?php echo is_selected(strval($cd->CreditCard), strval($SAP_CREDIT_CARD_ID)); ?>>
							<?php echo $cd->AcctCode.' | '.$cd->CardName; ?>
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
			<input type="hidden" id="sap-credit-card-acct-code" name="SAP_CREDIT_CARD_ACCT_CODE" value="<?php echo $SAP_CREDIT_CARD_ACCT_CODE; ?>" />
		</div>
		<div class="divider-hidden"></div>



		<div class="col-sm-8 col-sm-offset-4">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-sm btn-success input-small" onClick="updateConfig('sapForm')">
					<i class="fa fa-save"></i> บันทึก
				</button>
			<?php endif; ?>
		</div>
		<div class="divider-hidden"></div>

	</div><!--/ row -->
</form>
