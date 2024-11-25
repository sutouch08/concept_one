<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" style="max-width:400px;">
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
