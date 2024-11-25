<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>assets/js/jquery.colorbox.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/colorbox.css" />
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right top-p">
    <button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		<?php if($doc->status != 'D' && $doc->is_interface == 1) : ?>
			<button type="button" class="btn btn-xs btn-success" onclick="sendToSap('<?php echo $doc->code; ?>')"><i class="fa fa-send"></i> Send to SAP</button>
			<button type="button" class="btn btn-xs btn-primary" onclick="createInvoice('<?php echo $doc->code; ?>')">เปิดใบกำกับภาษี</button>
		<?php endif; ?>
		<button type="button" class="btn btn-xs btn-info" onclick="printDownPayment('<?php echo $doc->code; ?>')"><i class="fa fa-print"></i> พิมพ์</button>
  </div>
</div>
<hr class=""/>
<div class="row">
	<input type="hidden" id="prev-image" value="<?php echo $image; ?>" />
  <input type="hidden" id="no-img-path" value="<?php echo $no_image_path; ?>">
	<input type="hidden" id="image-path" value="<?php echo base_url().$this->config->item('image_path').$this->img_folder.'/'.$doc->code.'.jpg'; ?>" />
  <?php $ad = empty($doc->image_path) ? '<i class="fa fa-plus"></i>&nbsp; เพิ่ม' : '<i class="fa fa-refresh"></i>&nbsp; เปลี่ยน'; ?>
  <?php $del = empty($doc->image_path) ? 'hide' : ''; ?>

	<div class="col-lg-9-harf col-md-9 col-sm-9 col-xs-12">
		<div class="row">
			<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
				<label>เลขที่เอกสาร</label>
				<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
				<input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
			</div>
			<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
				<label>วันที่</label>
				<input type="text" class="form-control input-sm text-center h" value="<?php echo thai_date($doc->date_add); ?>"  disabled/>
				<input type="hidden" id="date" value="<?php echo date('Y-m-d'); ?>" />
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
				<label>รหัสลูกค้า</label>
				<input type="text" class="form-control input-sm h" id="customer-code" value="<?php echo $doc->customer_code; ?>" disabled />
			</div>
			<div class="col-lg-6-harf col-md-6 col-sm-5 col-xs-6 padding-5">
				<label>ชื่อลูกค้า</label>
				<input type="text" class="form-control input-sm h" id="customer-name" value="<?php echo $doc->customer_name; ?>" disabled />
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
				<label>ผู้ติดต่อ</label>
				<input type="text" class="form-control input-sm h" id="customer-ref" value="<?php echo $doc->customer_ref; ?>" disabled />
			</div>
			<div class="col-lg-3 col-md-2-harf col-sm-3 col-xs-6 padding-5">
				<label>เบอร์โทร</label>
				<input type="text" class="form-control input-sm h" id="phone" value="<?php echo $doc->customer_phone; ?>" disabled/>
			</div>

			<div class="col-lg-3 col-md-3-harf col-sm-3 col-xs-6 padding-5">
				<label>จุดขาย</label>
				<input type="text" class="form-control input-sm h"  id="shop" value="<?php echo $pos->shop_name; ?>" disabled/>
			</div>
			<div class="col-lg-4 col-md-3-harf col-sm-4 col-xs-6 padding-5">
				<label>เครื่อง POS</label>
				<input type="text" class="form-control input-sm h" id="pos" value="<?php echo $pos->name; ?>" disabled/>
			</div>
			<div class="col-lg-2 col-md-2-harf col-sm-2 col-xs-6 padding-5">
				<label>ชำระโดย</label>
				<input type="text" class="form-control input-sm text-center h" id="payment_role" value="<?php echo $doc->payment_name; ?>" disabled/>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
				<label>ยอดเงิน</label>
				<input type="text" class="form-control input-sm text-center h" id="amount" value="<?php echo number($doc->amount, 2); ?>" disabled/>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
				<label>ยอดตัด</label>
				<input type="text" class="form-control input-sm text-center h" id="used" value="<?php echo number($doc->used, 2); ?>" disabled/>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
				<label>คงเหลือ</label>
				<input type="text" class="form-control input-sm text-center h" id="available" value="<?php echo number($doc->available, 2); ?>" disabled/>
			</div>
			<div class="divider-hidden">

			</div>

			<div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-5 padding-5">
		    <label>อ้างอิง</label>
				<div class="input-group">
					<input type="text" class="form-control input-sm text-center" id="reference" value="<?php echo $doc->reference; ?>" disabled />
		      <?php if($doc->ref_type == 'WO') : ?>
		  			<span class="input-group-btn">
		  				<button type="button" class="btn btn-xs btn-info"
		  				onclick="viewWo('<?php echo $doc->reference; ?>')"
		  				<?php echo (empty($doc->reference) ? 'disabled' :''); ?>>		<i class="fa fa-external-link"></i></button>
		  			</span>
		      <?php else : ?>
		        <span class="input-group-btn">
		  				<button type="button" class="btn btn-xs btn-info"
		  				onclick="viewSo('<?php echo $doc->reference; ?>')"
		  				<?php echo (empty($doc->reference) ? 'disabled' :''); ?>>		<i class="fa fa-external-link"></i></button>
		  			</span>
		      <?php endif; ?>
				</div>
		  </div>
			<div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-5 padding-5">
		    <label>ใบรับเงิน (SAP)</label>
				<div class="input-group width-100">
					<input type="text" class="form-control input-sm text-center" id="ORCT" value="<?php echo $doc->DocNum; ?>" disabled/>
					<span class="input-group-btn">
						<button type="button" class="btn btn-xs btn-info"
						onclick="printIncomming(<?php echo $doc->id; ?>)"
						<?php echo (empty($doc->DocNum) ? 'disabled' : ''); ?>><i class="fa fa-print"></i></button>
					</span>
				</div>
		  </div>
			<div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-5 padding-5">
		    <label>ใบกำกับ (SAP)</label>
				<div class="input-group width-100">
					<input type="text" class="form-control input-sm text-center" id="ODPI" value="<?php echo $doc->DpmNum; ?>" disabled />
					<span class="input-group-btn">
						<button type="button" class="btn btn-xs btn-info"
						onclick="printInvoice(<?php echo $doc->id; ?>)"
						<?php echo (empty($doc->DpmNum) ? 'disabled' :''); ?>><i class="fa fa-print"></i></button>
					</span>
				</div>
		  </div>
		  <div class="col-lg-2-harf col-md-3 col-sm-2 col-xs-3-harf padding-5">
		    <label>User</label>
		    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->user; ?>" disabled/>
		  </div>
		  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3-harf padding-5">
		    <label>สถานะ</label>
		    <input type="text" class="form-control input-sm text-center" value="<?php echo ($doc->status == 'D' ? 'Cancel' : ($doc->status == 'C' ? 'Closed' : 'Open')); ?>" disabled />
		  </div>
		</div>
	</div>
	<div class="divider-hidden visible-xs"></div>
	<div class="col-lg-2-harf col-md-3 col-sm-3 col-xs-12">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
				<span class="profile-picture" id="so-img-preview">
					<a data-rel="colorbox" id="image-link" href="<?php echo $image; ?>">
						<img class="editable img-responsive" id="so-image"
						src="<?php echo $image; ?>"
						style="width:100%; height:100%; max-width:300px; max-height:200px;">
					</a>
				</span>
				<input type="hidden" id="img-blob" />
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center margin-top-5 padding-5">
				<?php if($doc->status != 'D') : ?>
					<button type="button" class="btn btn-mini btn-success fix-width-80" id="btn-add-img" onclick="addImage()"><?php echo $ad; ?></button>
					<button type="button" class="btn btn-mini btn-primary fix-width-60 hide" id="btn-save-img" onclick="saveImage()"><i class="fa fa-save"></i> Save</button>
					<button type="button" class="btn btn-mini btn-danger fix-width-60 <?php echo $del; ?>" id="btn-del-img" onclick="deleteImage()"><i class="fa fa-trash"></i> ลบ</button>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<hr class=""/>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-40 text-center"></th>
          <th class="fix-width-120 text-center">เอกสารที่ตัดมัดจำ</th>
          <th class="fix-width-120 text-center">ใบสั่งขาย</th>
          <th class="fix-width-120 text-center">ออเดอร์</th>
          <th class="fix-width-120 text-center">บิลขาย</th>
          <th class="fix-width-120 text-right">คงเหลือก่อนตัด</th>
          <th class="fix-width-120 text-right">ยอดตัดบิลนี้</th>
          <th class="fix-width-120 text-right">คงเหลือหลังตัด</th>
        </tr>
      </thead>
      <tbody id="down-payment-table">
        <?php if( ! empty($details)) : ?>
          <?php $no = 1; ?>
          <?php $downPaymentUse = 0; ?>
          <?php foreach($details as $dp) : ?>
            <?php $co = $dp->is_cancel == 1 ? 'color:red;' : ''; ?>
            <tr style="<?php echo $co; ?>">
              <td class="text-center"><?php echo $no; ?></td>
              <td class="text-center"><?php echo $dp->is_cancel == 1 ? 'ยกเลิก' : ''; ?></td>
              <td class="text-center"><?php echo $dp->TargetRef; ?></td>
              <td class="text-center"><?php echo $dp->so_code; ?></td>
              <td class="text-center"><?php echo $dp->order_code; ?></td>
              <td class="text-center"><?php echo $dp->bill_code; ?></td>
              <td class="text-right"><?php echo number($dp->amountBfUse, 2); ?></td>
              <td class="text-right"><?php echo number($dp->amount, 2); ?></td>
              <td class="text-right"><?php echo number($dp->amountAfUse, 2); ?></td>
            </tr>
            <?php $downPaymentUse += $dp->amount; ?>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<form class="no-margin" id="imageForm">
				<div class="modal-body">
					<div style="width:75%;margin-left:12%;">
						<label id="btn-select-file" class="ace-file-input ace-file-multiple">
							<input type="file" name="image" id="image" accept="image/*" style="display:none;" />
							<span class="ace-file-container" data-title="Click to choose new Image">
								<span class="ace-file-name" data-title="No File ...">
									<i class=" ace-icon ace-icon fa fa-picture-o"></i>
								</span>
							</span>
						</label>
						<div id="block-image" style="opacity:0;">
							<div id="previewImg" class="width-100 center"></div>
							<span onClick="removeFile()" style="position:absolute; left:385px; top:1px; cursor:pointer; color:red;">
								<i class="fa fa-times fa-2x"></i>
							</span>
						</div>
					</div>
				</div>
				<div class="modal-footer center">
					<button type="button" class="btn btn-sm btn-success" onclick="getImage()"><i class="ace-icon fa fa-check"></i> Submit</button>
					<button type="button" class="btn btn-sm" data-dismiss="modal"><i class="ace-icon fa fa-times"></i> Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/order_down_payment/order_down_payment.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
