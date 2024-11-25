<div class="row">
  <input type="hidden" id="prev-image" value="<?php echo $image; ?>" />
  <input type="hidden" id="no-img-path" value="<?php echo $no_image_path; ?>">
  <?php $ad = empty($doc->image_path) ? '' : 'hide'; ?>
  <?php $del = empty($doc->image_path) ? 'hide' : ''; ?>
  <div class="col-lg-12 col-md-12 col-sm-12 padding-0 margin-bottom-15">
    <div class="tabable">
      <ul class="nav nav-tabs" role="tablist">
        <li class="active">
          <a href="#doc-pane" id="doc-tab" aria-expanded="true" aria-controls="doc-pane" role="tab" data-toggle="tab">เอกสาร</a>
        </li>
        <li>
          <a href="#image-pane" id="image-tab" aria-expanded="false" aria-controls="image-pane" role="tab" data-toggle="tab">รูปภาพ</a>
        </li>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content" style="">
        <div role="tabpanel" class="tab-pane active" id="doc-pane">
          <div class="row">
            <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
              <label>เลขที่</label>
              <input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled/>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
              <label>เล่ม</label>
              <select class="form-control input-sm h" id="is-term">
                <option value="">เลือก</option>
                <option value="0" <?php echo is_selected('0', $doc->is_term); ?>>ขายสด</option>
                <option value="1" <?php echo is_selected('1', $doc->is_term); ?>>ขายเชื่อ</option>
              </select>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
              <label>ชนิด VAT</label>
              <select class="form-control input-sm h" id="vat-type" onchange="toggleVatType()">
                <option value="">เลือก</option>
                <option value="E" <?php echo is_selected('E', $doc->vat_type); ?>>แยกนอก</option>
                <option value="I" <?php echo is_selected('I', $doc->vat_type); ?>>รวมใน</option>
                <option value="N" <?php echo is_selected('N', $doc->vat_type); ?>>ไม่ VAT</option>
              </select>
              <input type="hidden" id="tax-status" value="<?php echo $doc->TaxStatus; ?>">
            </div>
            <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
              <label>วันที่</label>
              <input type="text" class="form-control input-sm text-center h" id="date_add" value="<?php echo thai_date($doc->date_add); ?>" readonly/>
            </div>
            <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
              <label>รหัสลูกค้า</label>
              <input type="text" class="form-control input-sm text-center h" id="customer-code" value="<?php echo $doc->customer_code; ?>"/>
            </div>
            <div class="col-lg-4-harf col-md-5-harf col-sm-4 col-xs-6 padding-5">
              <label class="display-block not-show">ชื่อลูกค้า</label>
              <input type="text" class="form-control input-sm h" id="customer-name" value="<?php echo $doc->customer_name; ?>"/>
            </div>
            <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5">
              <label class="display-block not-show">isCompany</label>
              <label style="margin-top:0;">
                <input type="checkbox" class="ace" id="is-company" value="1" onchange="toggleBranch()" <?php echo is_checked('1', $doc->isCompany); ?> />
                <span class="lbl margin-top-5">&nbsp;&nbsp;นิติบุคคล</span>
              </label>
            </div>
            <div class="col-lg-6-harf col-md-5 col-sm-4 col-xs-6 padding-5">
              <label>ผู้ติดต่อ</label>
              <input type="text" class="form-control input-sm h" maxlength="100" id="customer-ref" value="<?php echo $doc->customer_ref; ?>" />
            </div>

            <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
              <label>เบอร์โทร</label>
              <input type="text" class="form-control input-sm h" id="phone" value="<?php echo $doc->phone; ?>"/>
            </div>
            <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
              <label>เลขผู้เสียภาษี</label>
              <input type="text" class="form-control input-sm text-center h" id="tax-id" value="<?php echo $doc->tax_id; ?>"/>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
              <label>สาขา</label>
              <input type="text" class="form-control input-sm text-center h" id="branch-code" value="<?php echo $doc->branch_code; ?>"/>
            </div>
            <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
              <label>ชื่อสาขา</label>
              <input type="text" class="form-control input-sm h" id="branch-name" value="<?php echo $doc->branch_name; ?>" />
            </div>
            <div class="col-lg-6-harf col-md-5 col-sm-5 col-xs-12 padding-5">
              <label>ที่อยู่เปิดบิล</label>
              <input type="text" class="form-control input-sm h" id="address" value="<?php echo $doc->address; ?>" />
            </div>
            <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
              <label>ตำบล</label>
              <input type="text" class="form-control input-sm h" id="sub-district" value="<?php echo $doc->sub_district; ?>" />
            </div>
            <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
              <label>อำเภอ</label>
              <input type="text" class="form-control input-sm h" id="district" value="<?php echo $doc->district; ?>" />
            </div>
            <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
              <label>จังหวัด</label>
              <input type="text" class="form-control input-sm h" id="province" value="<?php echo $doc->province; ?>" />
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-6 padding-5">
              <label>ไปรษณีย์</label>
              <input type="text" class="form-control input-sm h" id="postcode" value="<?php echo $doc->postcode; ?>" />
            </div>


            <div class="divider"></div>

            <div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
              <label>ประเภทงาน</label>
              <select class="form-control input-sm h" id="job-type" >
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

            <div class="col-lg-7 col-md-6-harf col-sm-8 col-xs-12 padding-5">
              <label>ชื่องาน</label>
              <input type="text" class="form-control input-sm h" id="job-title" value="<?php echo $doc->job_title; ?>" />
            </div>

            <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
              <label>กำหนดส่ง</label>
              <input type="text" class="form-control input-sm text-center h" id="due_date" value="<?php echo thai_date($doc->due_date); ?>" readonly />
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
              <label>ช่องทางขาย</label>
              <select class="form-control input-sm h" id="channels" >
                <option value="">เลือก</option>
                <?php echo select_channels($doc->channels_code); ?>
              </select>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
              <label>การออกแบบ</label>
              <select class="form-control input-sm h" id="design" >
                <option value="">เลือก</option>
                <option value="no" <?php echo is_selected('no', $doc->design); ?>>ไม่มีแบบ</option>
                <option value="old" <?php echo is_selected('old', $doc->design); ?>>แบบเดิม</option>
                <option value="new" <?php echo is_selected('new', $doc->design); ?>>แบบใหม่</option>
              </select>
            </div>

            <div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
              <label>คลัง</label>
              <select class="form-control input-sm h" id="warehouse" >
                <?php echo select_sell_warehouse($doc->whsCode); ?>
              </select>
            </div>

            <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
              <label>มัดจำ</label>
              <input type="text" class="form-control input-sm text-right h" id="dep-amount" value="<?php echo number($doc->DepAmount, 2); ?>" />
            </div>

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
              <label>ใบเบิก</label>
              <?php if( ! empty($wq)) : ?>
                <div class="input-group">
                  <select class="form-control input-sm" id="wq">
                    <?php foreach($wq as $q) : ?>
                      <option value="<?php echo $q->code; ?>"><?php echo $q->code; ?></option>
                    <?php endforeach; ?>
                  </select>
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-xs btn-info" onclick="viewWq()"><i class="fa fa-eye"></i></button>
                  </span>
                </div>
              <?php else : ?>
                <input type="text" class="form-control input-sm text-center" value="ไม่มี" disabled/>
              <?php endif; ?>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
              <label>ออเดอร์</label>
              <?php if( ! empty($wo)) : ?>
                <div class="input-group">
                  <select class="form-control input-sm" id="wo">
                    <?php foreach($wo as $q) : ?>
                      <option value="<?php echo $q->code; ?>"><?php echo $q->code; ?></option>
                    <?php endforeach; ?>
                  </select>
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-xs btn-info" onclick="viewWo()"><i class="fa fa-eye"></i></button>
                  </span>
                </div>
              <?php else : ?>
                <input type="text" class="form-control input-sm text-center" value="ไม่มี" disabled/>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="image-pane">
          <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
                <span class="profile-picture" id="so-img-preview">
                  <img class="editable img-responsive" id="so-image"
                  src="<?php echo $image; ?>"
                  style="width:100%; height:100%; max-width:160px; max-height:160px;">
                </span>
                <input type="hidden" id="img-blob" />
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center margin-top-5">
                <?php if($doc->status != 'D') : ?>
                  <button type="button" class="btn btn-minier btn-success <?php echo $ad; ?>" id="btn-add-img" onclick="addImage()"><i class="fa fa-plus"></i> เพิ่ม</button>
                  <button type="button" class="btn btn-minier btn-danger <?php echo $del; ?>" id="btn-del-img" onclick="deleteImage()"><i class="fa fa-trash"></i> ลบ</button>
                  <?php if($mode != 'Add') : ?>
                    <button type="button" class="btn btn-minier btn-primary hide" id="btn-save-img" onclick="saveImage()"><i class="fa fa-save"></i> Save</button>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>
          </div><!-- row -->
        </div> <!-- tab pane -->
      </div><!-- tab content -->
    </div><!-- tabable -->
  </div><!-- col-lg-12 -->
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
