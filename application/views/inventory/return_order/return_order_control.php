<!--  Search Product -->
<?php $dsa = $this->allow_no_inv == FALSE ? "disabled" : (empty($doc->invoice) ? "" : "disabled"); ?>
<div class="row">
	<div class="col-sm-2 col-2-harf col-xs-8 padding-5 margin-bottom-10">
		<label>รุ่นสินค้า</label>
    <input type="text" class="form-control input-sm text-center item-control" id="model-code" placeholder="รุ่นสินค้า" <?php echo $dsa; ?>/>
  </div>
  <div class="col-sm-1 col-1-harf col-xs-4 padding-5 margin-bottom-10">
		<label class="display-block not-show">x</label>
  	<button type="button" class="btn btn-xs btn-primary btn-block item-control" <?php echo $dsa; ?> onclick="getItemGrid()">OK</button>
  </div>

	<div class="divider visible-xs"></div>

  <div class="col-sm-2 col-2-harf col-xs-6 padding-5 margin-bottom-10">
		<label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm text-center item-control" id="item-code" placeholder="รหัสสินค้า" <?php echo $dsa; ?>>
		<input type="hidden" id="item-data" />
  </div>
  <div class="col-sm-1 col-xs-2 padding-5 margin-bottom-10">
		<label>ราคา</label>
    <input type="number" class="form-control input-sm text-center item-control" id="input-price" placeholder="ราคา" <?php echo $dsa; ?>>
  </div>
  <div class="col-sm-1 col-xs-2 padding-5 margin-bottom-10">
		<label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center item-control" id="input-qty" placeholder="จำนวน" <?php echo $dsa; ?>>
  </div>
  <div class="col-sm-1 col-xs-2 padding-5 margin-bottom-10">
		<label class="display-block not-show">x</label>
    <button type="button" class="btn btn-xs btn-primary btn-block item-control" <?php echo $dsa; ?> onclick="addItem()">เพิ่ม</button>
  </div>

	<div class="divider visible-xs"></div>

	<div class="col-lg-1-harf col-md-1 col-sm-1 col-xs-8">&nbsp;</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show">x</label>
		<button type="button" class="btn btn-xs btn-danger btn-block" onclick="removeChecked()">ลบรายการ</button>
	</div>
</div>


<div class="modal fade" id="itemGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="min-width:300px; min-height:400px; max-width:95vw; max-height:95vh;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
			 </div>
			 <div class="modal-body">
         <div class="row">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5"
           id="modalBody"
           style="position:relative; min-width:250px; min-height:400px; max-width:100%; max-height:60vh; overflow:auto;">

           </div>
         </div>
       </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="addItems()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>


<div class="modal fade" id="poGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:800px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <center style="margin-bottom:10px;"><h4 class="modal-title" id="po-title">ใบสั่งซื้อ</h4></center>
      </div>
      <div class="modal-body" style="max-width:94vw; min-height:300px; max-height:70vh; overflow:auto;">
        <table class="table table-striped table-bordered" style="table-layout: fixed; min-width:740px;">
          <thead>
            <th class="fix-width-40 text-center">#</th>
            <th class="fix-width-200 text-center">รหัส</th>
            <th class="fix-width-200 text-center">สินค้า</th>
            <th class="fix-width-100 text-center">ราคา</th>
            <th class="fix-width-100 text-center">ค้างรับ</th>
            <th class="fix-width-100 text-center">จำนวน</th>
          </thead>
          <tbody id="po-body">

          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default top-btn" id="btn_close" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-yellow top-btn" onclick="receiveAll()">รับยอดค้างทั้งหมด</button>
				<button type="button" class="btn btn-purple top-btn" onclick="clearAll()">เคลียร์ตัวเลขทั้งหมด</button>
        <button type="button" class="btn btn-primary top-btn" onclick="addPoItems()">เพิ่มในรายการ</button>
       </div>
    </div>
  </div>
</div>


<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-dialog input-xlarge">
    <div class="modal-content">
      <div class="modal-header">
      	<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
		    <h4 class='modal-title-site text-center' > ผู้มีอำนาจอนุมัติรับสินค้าเกิน </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
          	<input type="password" class="form-control input-sm text-center" id="sKey" />
            <span class="help-block red text-center" id="approvError">&nbsp;</span>
          </div>
          <div class="col-sm-12">
            <button type="button" class="btn btn-sm btn-primary btn-block" onclick="doApprove()">อนุมัติ</button>
          </div>
        </div>
    	 </div>
      </div>
    </div>
</div>
