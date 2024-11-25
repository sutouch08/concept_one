<div class="modal fade" id="downModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog" style="max-width:500px;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
				<h4 class="title text-center" style="margin-top:0px;" id="title">รับมัดจำ</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="col-lg-8 col-md-8 col-sm-8 padding-5 first">
							<input type="text" class="form-control text-center" id="bill-search" placeholder="เลขที่ใบสั่งขาย"/>
							<input type="hidden" id="doc-type" value="SO" /><!-- SO or WO -->
						</div>
						<div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
							<button type="button" class="btn btn-sm btn-info btn-block" onclick="getBillView()">ตรวจสอบ</button>
						</div>
						<div class="divider-hidden"></div>
						<div class="col-lg-12 col-md-12 col-sm-12 padding-5 first last" id="bill-result-table"></div>
					</div>
        </div>
        <div class="divider-hidden"></div>
        <div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 padding-5" style="max-height:400px; overflow:auto;" id="bill-view-table">
						<div class="col-lg-6 col-md-6 col-sm-6 margin-bottom-10" style="padding-right:5px;">
							<div class="input-group width-100">
								<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">วันที่</span>
								<input type="text" class="form-control text-center" id="receive-date" value="<?php echo date('d-m-Y'); ?>" readonly>
								<input type="hidden" id="to-day" value="<?php echo date('d-m-Y'); ?>" />
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 margin-bottom-10" style="padding-left:5px;">
							<div class="input-group width-100">
								<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">เลขที่</span>
								<input type="text" class="form-control text-center so" id="so-code" value="" disabled>
							</div>
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10">
							<div class="input-group width-100">
								<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">ลูกค้า</span>
								<input type="text" class="form-control so" id="customer-name" value="" disabled>
							</div>
						</div>

						<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10">
							<div class="input-group width-100">
								<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">มูลค่า</span>
								<input type="text" class="form-control text-center so" id="amount-label" value="" disabled>
								<input type="hidden" class="so" id="amount" value="0.00" />
							</div>
						</div>

						<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10" id="deposit-row">
							<div class="input-group width-100">
								<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">มัดจำ</span>
								<input type="text" class="form-control text-center so" id="depositAmount" value="0.00" placeholder="มัดจำ" >
							</div>
						</div>

						<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10">
							<div class="btn-group width-100">
								<button type="button" class="btn btn-role btn-success width-20" id="btn-role-1" onclick="setPayment('<?php echo $pos->cash_payment; ?>', 1)">เงินสด</button>
								<button type="button" class="btn btn-role width-20" id="btn-role-2" onclick="setPayment('<?php echo $pos->transfer_payment; ?>', 2)">เงินโอน</button>
								<button type="button" class="btn btn-role width-20" id="btn-role-3" onclick="setPayment('<?php echo $pos->card_payment; ?>', 3)">บัตรเครดิต</button>
								<button type="button" class="btn btn-role width-20" id="btn-role-7" onclick="setPayment('CHEQUE', 7)">เช็ค</button>
								<button type="button" class="btn btn-role width-20" id="btn-role-6" onclick="setPayment('MULTIPAYMENT', 6)">หลายทาง</button>
							</div>
						</div>

						<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10 p" id="p-cash">
							<div class="input-group width-100">
								<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">เงินสด</span>
								<input type="number" class="form-control text-center so" id="cashReceive" value="" placeholder="เงินสด">
							</div>
						</div>

						<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10 p hide" id="p-account">
							<div class="input-group width-100">
								<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">เข้าบัญชี</span>
								<?php $account = $this->bank_model->get_active_bank(); ?>
								<?php if( ! empty($account)) : ?>
									<?php if(count($account) == 1) : ?>
										<?php $ac = $account[0]; ?>
										<select class="form-control" id="acc-id">
											<option value="<?php echo $ac->id; ?>" selected><?php echo $ac->acc_name.' #'.$ac->acc_no; ?></option>
										</select>
									<?php else : ?>
										<select class="form-control so" id="acc-id">
											<option value="">เลือกบัญชี</option>
								  	<?php foreach($account as $ac) : ?>
											<option value="<?php echo $ac->id; ?>"><?php echo $ac->acc_name.' #'.$ac->acc_no; ?></option>
										<?php endforeach; ?>
										</select>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>

						<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10 p hide" id="p-transfer">
							<div class="input-group width-100">
								<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">เงินโอน</span>
								<input type="number" class="form-control text-center so" id="transferAmount" value="" placeholder="เงินโอน">
							</div>
						</div>

						<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10 p hide" id="p-card">
							<div class="input-group width-100">
								<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">บัตรเครดิต</span>
								<input type="number" class="form-control text-center so" id="cardAmount" value="" placeholder="บัตรเครดิต">
							</div>
						</div>

						<div class="col-lg-12 col-md-12 col-sm-12 margin-bottom-10 p hide" id="p-cheque">
							<div class="input-group width-100">
								<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">เช็ค</span>
								<input type="number" class="form-control text-center so" id="chequeAmount" value="" placeholder="เช็ค">
							</div>
						</div>

						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="input-group width-100">
								<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">คงเหลือ</span>
								<input type="text" class="form-control text-center so" id="balanceAmount" value="0.00" placeholder="" disabled>
							</div>
						</div>
						<div class="divider-hidden"></div>

						<div class="col-lg-12 col-md-12 col-sm-12" id="change">
							<div class="input-group width-100">
								<span class="input-group-addon fix-width-100" style="border:solid 1px #d5d5d5 !important; border-right:0 !important; background-color:#f5f5f5 !important;">เงินทอน</span>
								<input type="text" class="form-control text-center so" id="changeAmount" placeholder="เงินทอน" value="0.00" disabled>
							</div>
						</div>


					</div>
					<div class="divider-hidden"></div>
					<div class="col-lg-12 col-md-12 col-sm-12">
						<input type="text" class="form-control input-xs text-center so" id="payment-error"
						style="color:#f44336; background-color: transparent !important; font-size: 14px; border:none;" value="" readonly>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default btn-100" onclick="closeModal('downModal')">ยกเลิก</button>
				<button class="btn btn-primary btn-100" id="btn-add-return" onclick="submitPayment()">ตกลง</button>
			</div>
		</div>
	</div>
</div>
