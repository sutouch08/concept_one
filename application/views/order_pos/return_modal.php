<div class="modal fade" id="returnModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog" style="max-width:500px;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #f4f4f4;">
				<h3 class="title text-center" >สร้างเอกสารใหม่</h3>
			</div>
			<div class="modal-body">
				<div class="row">
          <div class="col-lg-8 col-md-8 col-sm-8 padding-5 first">
						<input type="text" class="form-control text-center" id="bill-search" placeholder="เลขที่บิลขาย"/>
          </div>
					<div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
						<button type="button" class="btn btn-sm btn-info btn-block" onclick="getBillView()">ตรวจสอบ (Enter)</button>
					</div>
					<div class="divider-hidden"></div>
					<div class="col-lg-12 col-md-12 col-sm-12 padding-5 first last" id="bill-result-table"></div>
        </div>
        <div class="divider"></div>
        <div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12" style="max-height:400px; overflow:auto;" id="bill-view-table">

					</div>
				</div>
				<input type="hidden" id="bill-id" />
			</div>
			<div class="modal-footer">
				<button class="btn btn-default btn-100" onclick="closeModal('returnModal')">ยกเลิก</button>
				<button class="btn btn-primary btn-100" id="btn-add-return" onclick="newReturn()">ตกลง</button>
			</div>
		</div>
	</div>
</div>
