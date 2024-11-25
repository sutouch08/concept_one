<div class="modal fade" id="cancle-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="max-width:95vw; margin-left:auto; margin-right:auto;">
   <div class="modal-content">
       <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title">ระบุสาเหตุในการยกเลิก</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <textarea class="form-control" id="cancel-reason" maxlength="100" placeholder="ระบุสาเหตุในการยกเลิก"></textarea>
            <input type="hidden" id="cancle-code" value="" />
            <input type="hidden" id="cancle-id" value="" />
            <p class="red hide" id="cancle-error"></p>
          </div>
        </div>
       </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default btn-100" onclick="closeModal('cancle-modal')">ยกเลิก</button>
        <button type="button" class="btn btn-sm btn-info btn-100" onclick="doCancle()">ยืนยัน</button>
      </div>
   </div>
 </div>
</div>
