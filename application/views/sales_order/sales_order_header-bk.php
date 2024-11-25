<div class="row">
  <input type="hidden" id="prev-image" value="<?php echo $image; ?>" />
  <input type="hidden" id="no-img-path" value="<?php echo $no_image_path; ?>">
  <?php $ad = empty($doc->image_path) ? '' : 'hide'; ?>
  <?php $del = empty($doc->image_path) ? 'hide' : ''; ?>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
			<span class="profile-picture" id="so-img-preview">
				<img class="editable img-responsive" id="so-image"
        src="<?php echo $image; ?>"
        style="width:100%; height:100%; max-width:160px; max-height:160px;">
			</span>
      <input type="hidden" id="img-blob" />
		</div>
		<div class="col-sm-12 col-xs-12 text-center margin-top-5">
<?php if($doc->status != 'D') : ?>
			<button type="button" class="btn btn-minier btn-success <?php echo $ad; ?>" id="btn-add-img" onclick="addImage()"><i class="fa fa-plus"></i> เพิ่ม</button>
			<button type="button" class="btn btn-minier btn-danger <?php echo $del; ?>" id="btn-del-img" onclick="deleteImage()"><i class="fa fa-trash"></i> ลบ</button>
      <button type="button" class="btn btn-minier btn-primary hide" id="btn-save-img" onclick="saveImage()"><i class="fa fa-save"></i> Save</button>
<?php endif; ?>
		</div>
  </div>

  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 padding-5">
    <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
      <label>เลขที่</label>
      <input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled/>
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>วันที่</label>
      <input type="text" class="form-control input-sm text-center h" id="date_add" value="<?php echo thai_date($doc->date_add); ?>" readonly />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>รหัสลูกค้า</label>
      <input type="text" class="form-control input-sm text-center h" id="customer-code" value="<?php echo $doc->customer_code; ?>" />
    </div>
    <div class="col-lg-6 col-md-5 col-sm-5-harf col-xs-6 padding-5">
      <label class="display-block not-show">ชื่อลูกค้า</label>
      <input type="text" class="form-control input-sm h" id="customer-name" value="<?php echo $doc->customer_name; ?>" />
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5 hide">
      <label>VAT</label>
      <select class="form-control input-sm h" id="vat-type" onchange="toggleVatType()">
        <option value="I" data-rate="7" <?php echo is_selected('I', $doc->vat_type); ?>>VAT ใน</option>
        <option value="E" data-rate="7" <?php echo is_selected('E', $doc->vat_type); ?>>VAT นอก</option>
      </select>
    </div>

    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>VAT</label>
      <select class="form-control input-sm h" id="tax-status" onchange="toggleVat()">
        <option value="">เลือก</option>
        <?php echo select_tax_status($doc->TaxStatus); ?>
      </select>
    </div>

    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>ประเภทงาน</label>
      <select class="form-control input-sm h" id="job-type">
        <option value="">เลือก</option>
        <option value="A" <?php echo is_selected('A', $doc->job_type); ?>>งานปัก</option>
        <option value="B" <?php echo is_selected('B', $doc->job_type); ?>>งานรีด</option>
        <option value="C" <?php echo is_selected('C', $doc->job_type); ?>>งานสกรีน</option>
        <option value="D" <?php echo is_selected('D', $doc->job_type); ?>>งานปัก + รีด</option>
        <option value="E" <?php echo is_selected('E', $doc->job_type); ?>>งานปัก + สกรีน</option>
        <option value="F" <?php echo is_selected('F', $doc->job_type); ?>>งานรีด + สกรีน</option>
        <option value="G" <?php echo is_selected('G', $doc->job_type); ?>>งานปักผสมรีด</option>
        <option value="H" <?php echo is_selected('H', $doc->job_type); ?>>งานGTX</option>
        <option value="I" <?php echo is_selected('I', $doc->job_type); ?>>งานสั่งผลิด</option>
      </select>
    </div>

    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 padding-5">
      <label>ชื่องาน</label>
      <input type="text" class="form-control input-sm h" id="job-title" value="<?php echo $doc->job_title; ?>" />
    </div>

    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>วันที่ส่งของ</label>
      <input type="text" class="form-control input-sm text-center h" id="due_date" value="<?php echo thai_date($doc->due_date); ?>" readonly/>
    </div>

    <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
      <label>ช่องทางขาย</label>
      <select class="form-control input-sm h" id="channels">
        <option value="">เลือก</option>
        <?php echo select_channels($doc->channels_code); ?>
      </select>
    </div>

    <div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
      <label>คลัง</label>
      <select class="form-control input-sm h" id="warehouse">
        <?php echo select_sell_warehouse($doc->whsCode); ?>
      </select>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6-harf col-xs-6 padding-5">
      <label>ลูกค้า</label>
      <input type="text" class="form-control input-sm h" maxlength="100" id="customer-ref" value="<?php echo $doc->customer_ref; ?>" />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
      <label>เบอร์โทร</label>
      <input type="text" class="form-control input-sm h" id="phone" value="<?php echo $doc->phone; ?>" />
    </div>

    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>มัดจำ</label>
      <input type="text" class="form-control input-sm text-right h" id="dep-amount" value="<?php echo number($doc->DepAmount, 2); ?>"/>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
      <label>ใบเบิก</label>
      <select class="form-control input-sm h" id="ref-code">
        <?php echo select_wq($doc->code, NULL); ?>
      </select>
    </div>

    <div class="divider-hidden"></div>
  </div>


</div>

<hr class="margin-top-10 margin-bottom-10"/>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h4 class="blue">Add Image</h4>
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
