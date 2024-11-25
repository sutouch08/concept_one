<div class="row">
	<input type="hidden" id="prev-image" value="<?php echo $image; ?>" />
  <input type="hidden" id="no-img-path" value="<?php echo $no_image_path; ?>">
	<input type="hidden" id="image-path" value="<?php echo base_url().$this->config->item('image_path').$this->img_folder.'/'.$doc->code.'.jpg'; ?>" />
  <?php $ad = empty($doc->image_path) ? '<i class="fa fa-plus"></i>' : '<i class="fa fa-refresh"></i>'; ?>
  <?php $del = empty($doc->image_path) ? 'hide' : ''; ?>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 margin-bottom-15">
		<div class="tabable">
			<ul class="nav nav-tabs" role="tablist">
        <li class="active">
          <a href="#doc-pane" id="doc-tab" aria-expanded="true" aria-controls="doc-pane" role="tab" data-toggle="tab">เอกสาร</a>
        </li>
				<li class="">
          <a href="#payment-pane" id="payment-tab" aria-expanded="false" aria-controls="payment-pane" role="tab" data-toggle="tab">การชำระเงิน</a>
        </li>
        <li class="">
          <a href="#image-pane" id="image-tab" aria-expanded="false" aria-controls="image-pane" role="tab" data-toggle="tab">รูปภาพ</a>
        </li>
      </ul>

			<div class="tab-content" style="min-height:210px;">
        <div role="tabpanel" class="tab-pane active" id="doc-pane" style="padding-left:10px; padding-right:10px;">
					<div class="row">
						<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
							<label>เลขที่เอกสาร</label>
							<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
							<input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
						</div>
						<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
							<label>วันที่</label>
							<input type="text" class="form-control input-sm text-center h" value="<?php echo thai_date($doc->date_add); ?>"  disabled/>
							<input type="hidden" id="date" value="<?php echo date('Y-m-d'); ?>" />
						</div>
						<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
							<label>รหัสลูกค้า</label>
							<input type="text" class="form-control input-sm h" id="customer-code" value="<?php echo $doc->customer_code; ?>" disabled />
						</div>
						<div class="col-lg-6 col-md-6 col-sm-5 col-xs-6 padding-5">
							<label>ชื่อลูกค้า</label>
							<input type="text" class="form-control input-sm h" id="customer-name" value="<?php echo $doc->customer_name; ?>" disabled />
						</div>

						<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3-harf padding-5">
          		<label class="display-block not-show">isCompany</label>
          		<label style="margin-top:0;">
          			<input type="checkbox" class="ace" id="is-company" value="1" <?php echo is_checked($doc->isCompany, '1'); ?> disabled>
          			<span class="lbl margin-top-5">&nbsp;&nbsp;นิติบุคคล</span>
          		</label>
          	</div>

						<div class="col-lg-5 col-md-6 col-sm-6 col-xs-8-harf padding-5">
							<label>ผู้ติดต่อ</label>
							<input type="text" class="form-control input-sm h" id="customer-ref" value="<?php echo $doc->customer_ref; ?>" disabled />
						</div>
						<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
							<label>เบอร์โทร</label>
							<input type="text" class="form-control input-sm h" id="phone" value="<?php echo $doc->customer_phone; ?>" disabled/>
						</div>

						<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
							<label>เลขผู้เสียภาษี</label>
							<input type="text" class="form-control input-sm h" id="tax-id" value="<?php echo $doc->tax_id; ?>" disabled />
						</div>

						<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
							<label>สาขา</label>
							<input type="text" class="form-control input-sm h" id="branch-code" value="<?php echo $doc->branch_code; ?>" disabled />
						</div>

						<div class="col-lg-2 col-md-3-harf col-sm-3 col-xs-6 padding-5">
							<label>ชื่อสาขา</label>
							<input type="text" class="form-control input-sm h"  id="branch-name" value="<?php echo $doc->branch_name; ?>" disabled/>
						</div>

						<div class="col-lg-6 col-md-6-harf col-sm-7 col-xs-12 padding-5">
							<label>ที่อยู่</label>
							<input type="text" class="form-control input-sm h" id="address" value="<?php echo $doc->address; ?>" disabled />
						</div>

						<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
							<label>ตำบล</label>
							<input type="text" class="form-control input-sm h" id="sub-district" value="<?php echo $doc->sub_district; ?>" disabled />
						</div>
						<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
							<label>อำเภอ</label>
							<input type="text" class="form-control input-sm h" id="district" value="<?php echo $doc->district; ?>" disabled />
						</div>
						<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
							<label>จังหวัด</label>
							<input type="text" class="form-control input-sm h" id="province" value="<?php echo $doc->province; ?>" disabled />
						</div>
						<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
							<label>รหัสไปรษณีย์</label>
							<input type="text" class="form-control input-sm h" id="postcode" value="<?php echo $doc->postcode; ?>" disabled />
						</div>
					</div>
				</div><!-- tab-pane -->

				<div role="tabpanel" class="tab-pane" id="payment-pane">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
							<table class="table table-bordered border-1" style="min-width:800px;">
								<thead>
									<tr>
										<th class="fix-width-150">วันที่บันทึก</th>
										<th class="fix-width-100">วันที่ชำระ</th>
										<th class="fix-width-100">ช่องทาง</th>
										<th class="min-width-150">อ้างอิง</th>
										<th class="fix-width-150 text-right">ยอดเงิน</th>
										<th class="fix-width-150 text-center">พนักงาน</th>
									</tr>
								</thead>
								<tbody>
							<?php if( ! empty($payments)) : ?>
								<?php foreach($payments as $pm) : ?>
									<?php $acc = empty($pm->acc_id) ? NULL : $this->bank_model->get($pm->acc_id); ?>
									<tr>
										<td class=""><?php echo thai_date($pm->date_upd, TRUE); ?></td>
										<td class=""><?php echo thai_date($pm->payment_date); ?></td>
										<td class=""><?php echo $pm->role_name; ?></td>
										<td class=""><?php echo (empty($acc) ? "-" : $acc->acc_name." #".$acc->acc_no); ?></td>
										<td class="text-right"><?php echo number($pm->amount, 2); ?></td>
										<td class="text-center"><?php echo $pm->uname; ?></td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div><!-- tab-pane -->


				<div role="tabpanel" class="tab-pane" id="image-pane">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div style="float:left; width:200px; max-height:210px; padding:5px; text-align:center;">
								<span class="profile-picture text-center" id="so-img-preview">
									<a data-rel="colorbox" id="image-link" href="<?php echo $image; ?>">
										<img class="editable img-responsive" id="so-image"
										src="<?php echo $image; ?>"
										style="max-width:200px; max-height:150px;">
									</a>
								</span>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
									<?php if($doc->status != 'D') : ?>
										<button type="button" class="btn btn-mini btn-success top-btn" id="btn-add-img" onclick="addImage()"><?php echo $ad; ?></button>
										<button type="button" class="btn btn-mini btn-primary top-btn hide" id="btn-save-img" onclick="saveImage()"><i class="fa fa-save"></i></button>
										<button type="button" class="btn btn-mini btn-danger top-btn <?php echo $del; ?>" id="btn-del-img" onclick="deleteImage()"><i class="fa fa-trash"></i></button>
									<?php endif; ?>
								</div>
							</div>
							<input type="hidden" id="img-blob" />
						</div>
					</div>
				</div><!-- tab-pane -->
			</div><!-- tab-content -->
		</div><!-- tabable -->
	</div>
</div>
