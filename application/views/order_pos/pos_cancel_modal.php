<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" style="max-width:550px;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
				<h3 class="title text-center" >ระบุสาเหตุในการยกเลิก</h3>
			</div>
			<div class="modal-body">
				<div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
						<textarea class="form-control" id="cancel-reason" placeholder="ระบุสาเหตุในการยกเลิก"></textarea>
          </div>
        </div>
				<div class="divider"></div>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 font-size-16">
						คืนเงิน : <span class="red" id="cancel-bill-amount"></span>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 font-size-16">
						คืนมัดจำ : <span class="red" id="cancel-down-amount"></span>
						<p class="font-size-10 blue">**ยอดมัดจำคงเหลือจะถูกคำนวนใหม่</p></div>
					<div class="divider-hidden"></div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						ช่องทางการคืนเงิน
					</div>
					<div class="divider-hidden"></div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
						<label>
							<input type="radio" class="ace" name="payment_role" id="payment-role-1" value="1" />
							<span class="lbl">&nbsp;&nbsp;<i class="fa fa-money"></i> เงินสด</span>
						</label>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
						<label>
							<input type="radio" class="ace" name="payment_role" id="payment-role-2" value="2" />
							<span class="lbl">&nbsp;&nbsp;<i class="fa fa-qrcode"></i> โอนเงิน</span>
						</label>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
						<label>
							<input type="radio" class="ace" name="payment_role" id="payment-role-3" value="3" />
							<span class="lbl">&nbsp;&nbsp;<i class="fa fa-credit-card"></i> บัตรเครดิต</span>
						</label>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
						<label>
							<input type="radio" class="ace" name="payment_role" id="payment-role-7" value="7" />
							<span class="lbl">&nbsp;&nbsp;<i class="fa fa-money"></i> เช็ค</span>
						</label>
					</div>
				</div>
        <div class="divider"></div>
				<input type="hidden" id="cancel-id" />
				<input type="hidden" id="cancel-code" />
			</div>
			<div class="modal-footer">
				<button class="btn btn-default btn-100" onclick="closeModal('cancelModal')">ยกเลิก</button>
				<button class="btn btn-danger btn-100"  onclick="doCancel()">ยืนยัน</button>
			</div>
		</div>
	</div>
</div>
