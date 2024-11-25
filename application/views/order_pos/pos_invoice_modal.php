<div class="modal fade" id="invoiceCustomerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog" style="max-width:500px;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
				<h3 class="title text-center" >ข้อมูลลูกค้า</h3>
			</div>
			<div class="modal-body">
				<div class="row">
          <div class="col-lg-6 col-md-6 col-sm-6 padding-5 first">
						<input type="text" class="form-control text-center" onkeyup="numberOnly(this)" maxlength="13" id="tax-search" placeholder="ประจำตัวผู้เสียภาษี/เลขที่บัตรประชาชน"/>
          </div>
					<div class="col-lg-3 col-md-3 col-sm-3 padding-5">
						<button type="button" class="btn btn-sm btn-info btn-block" onclick="getCustomerByTaxId()">ค้นหา</button>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 padding-5 last">
						<button type="button" class="btn btn-sm btn-primary btn-block" onclick="addNewCustomer()">เพิ่มใหม่</button>
					</div>
					<div class="divider-hidden"></div>
					<div class="col-lg-12 col-md-12 col-sm-12 padding-5 first last" id="cust-result-table"></div>
        </div>
        <div class="divider"></div>
        <div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<label>ชื่อ</label>
						<input type="text" class="form-control cust-form" id="name" maxlength="254" disabled/>
					</div>
					<div class="divider-hidden"></div>
					<div class="col-lg-6 col-md-6 col-sm-6 padding-5 first">
						<label>ประจำตัวผู้เสียภาษี</label>
						<input type="text" class="form-control text-center cust-form" id="tax-id" onkeyup="numberOnly(this)" maxlength="13" disabled/>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 padding-5">
						<label>รหัสสาขา</label>
						<input type="text" class="form-control text-center cust-form" id="branch-code" maxlength="10" value="000" disabled/>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-3 padding-5 last">
						<label>ชื่อสาขา</label>
						<input type="text" class="form-control cust-form" id="branch-name" maxlength="50" value="สำนักงานใหญ่" disabled/>
					</div>
					<div class="divider-hidden"></div>
					<div class="col-lg-12 col-md-12 col-sm-12">
						<label>ที่อยู่</label>
						<textarea class="form-control cust-form" id="address" disabled></textarea>
					</div>
					<div class="divider-hidden"></div>
					<div class="col-lg-6 col-md-6 col-sm-6">
						<label>เบอร์โทร</label>
						<input type="text" class="form-control cust-form" id="phone" onkeyup="numberOnly(this)" disabled />
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6">
						<label class="display-block not-show">isCompany</label>
						<label>
							<input type="checkbox" class="ace cust-form" id="is-company" value="1" checked disabled/>
							<span class="lbl margin-top-5">&nbsp;&nbsp;นิติบุคคล</span>
						</label>
					</div>
				</div>
				<input type="hidden" id="cust-id" />
			</div>
			<div class="modal-footer">
				<button class="btn btn-default btn-100" onclick="closeModal('invoiceCustomerModal')">ยกเลิก</button>
				<button class="btn btn-primary btn-100" id="btn-add-invoice" onclick="addInvoice()">บันทึก</button>
			</div>
		</div>
	</div>
</div>
