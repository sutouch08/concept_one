<div class="modal fade" id="accept-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="max-width:95%; margin-left:auto; margin-right:auto;">
   <div class="modal-content">
       <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title">หมายเหตุ</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <textarea class="form-control" id="accept-note" placeholder="กรุณาระบุหมายเหตุ(บังคับ)"></textarea>
          </div>
          <div class="help-block col-xs-12 col-sm-reset inline red" id="accept-error"></div>
        </div>
       </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">ยกเลิก</button>
        <button type="button" class="btn btn-sm btn-info btn-100" onclick="acceptConfirm()">ยืนยัน</button>
      </div>
   </div>
 </div>
</div>
