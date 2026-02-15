<div role="tabpanel" class="tab-pane" id="image-pane" style="min-height:255px;">
  <div class="row">
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 text-center">
      <button type="button" class="btn btn-white btn-primary btn-block" id="btn-show-upload" onclick="showUploadBox()"><i class="fa fa-cloud-upload"></i>&nbsp; เพิ่มรูปภาพสำหรับออเดอร์นี้</button>
    </div>
    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12 padding-5">
      <span class="help-block" style="margin-bottom:0px; font-style:italic;">** ไฟล์ : jpg, png, gif ขนาดสูงสุด 2 MB</span>
    </div>
  </div><!--/ row -->
  <hr/>
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" id="images-table">
      <?php if( ! empty($images)) : ?>
        <?php foreach($images as $img) : ?>
          <?php $image_path = get_image_path($img->image_path, 'orders'); ?>
          <span class="profile-picture" id="img-preview-<?php echo $img->id; ?>">
            <img class="img-responsive" id="order-image-<?php echo $img->id; ?>"
            src="<?php echo $image_path; ?>"
            style="width:100%; height: 100%; max-width:160px; max-height:160px;"
            onclick="viewImage('<?php echo $image_path; ?>')">
            <span class="remove-img" title="delete image" onclick="removeImage(<?php echo $img->id; ?>)"><i class="fa fa-times"></i></span>
          </span>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div><!-- row -->
</div> <!-- tab pane -->

<div class="modal fade" id="uploadBox" tabindex="-1" role="dialog" aria-labelledby="uploader" aria-hidden="true">
	<div class="modal-dialog" style="width:800px">
  	<div class="modal-content">
    	<div class="modal-header">
        <h4 class="modal-title">อัพโหลดรูปภาพสำหรับสินค้านี้</h4>
      </div>
      <div class="modal-body">
      	<form class="dropzone" id="imageForm" action="">
        </form>
      </div>
      <div class="modal-footer">
      	<button type="button" class="btn btn-sm btn-default" onClick="clearUploadBox()">ปิด</button>
        <button type="button" class="btn btn-sm btn-primary" onClick="doUpload()">Upload</button>
      </div>
    </div>
  </div>
</div>

<!-- <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:500px; max-width:95%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
			</div>
			<div class="modal-body" id="imageBody">

			</div>
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div> -->

<script id="images-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <span class="profile-picture" id="img-preview-{{id}}">
      <img class="img-responsive" id="order-image-{{id}}"
      src="{{image_path}}"
      style="width:100%; height: 100%; max-width:160px; max-height:160px;"
      onclick="viewImage('{{image_path}}')">
      <span class="remove-img" title="delete image" onclick="removeImage({{id}})"><i class="fa fa-times"></i></span>
    </span>
  {{/each}}
</script>
