<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6 ">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr style="margin-bottom:30px;"/>
<script src="<?php echo base_url(); ?>assets/js/fuelux/fuelux.wizard.js"></script>
<div class="widget-box">
  <div class="widget-header widget-header-blue widget-header-flat">
		<h4 class="widget-title lighter">สร้างรายการสินค้ารุ่น  : <?php echo $style->code; ?> (<?php echo $style->name; ?>)</h4>
	</div>

	<div class="widget-body">
		<div class="widget-main">
			<!-- #section:plugins/fuelux.wizard -->
			<div id="items-wizard" class="">
				<div><!-- #section:plugins/fuelux.wizard.steps -->
					<ul class="steps">
						<li data-step="1" class="active">
							<span class="step">1</span>
							<span class="title">กำหนด สี/ไซส์</span>
						</li>
						<li data-step="2" class="">
							<span class="step">2</span>
							<span class="title">จับคู่รูปภาพ</span>
						</li>
						<li data-step="3" class="">
							<span class="step">3</span>
							<span class="title">สร้างรายการ</span>
						</li>
					</ul>
					<!-- /section:plugins/fuelux.wizard.steps -->
				</div>
				<hr>
<div class="form-horizontal">

  <input type="hidden" name="id" id="id" value="<?php echo $style->id; ?>" />
	<input type="hidden" name="code" id="code" value="<?php echo $style->code; ?>" />
	<input type="hidden" name="name" id="name" value="<?php echo $style->name; ?>" />
  <input type="hidden" id="cost" value="<?php echo number($style->cost, 2); ?>" />
  <input type="hidden" id="price" value="<?php echo number($style->price, 2); ?>" />
			<!-- #section:plugins/fuelux.wizard.container -->
				<div class="step-content pos-rel" style="/*height:450px;*/">
					<div class="step-pane active" data-step="1">
              <div class="row" style="min-height:400px;">
                <div class="col-xs-12 col-sm-6">
  								<div class="widget-box">
  									<div class="widget-header">
  										<h4 class="widget-title">กำหนดสี</h4>
  									</div>
  									<div class="widget-body">
  										<div class="widget-main" style="height: 350px; overflow:scroll;">
              <?php  if(!empty($colors)) : ?>
                <?php foreach($colors as $color) : ?>
                  <div class="col-sm-12">
                    <label>
                      <input type="checkbox" class="ace colorBox" name="colors[]"
											data-id="<?php echo $color->id; ?>"
											data-code="<?php echo $color->code; ?>"
											data-name="<?php echo $color->name; ?>"
											value="<?php echo $color->code; ?>" />
                      <span class="lbl" id="co-<?php echo $color->id; ?>">   <?php echo $color->code; ?> | <?php echo $color->name; ?></span>
                    </label>
                  </div>

                <?php endforeach; ?>
              <?php endif; ?>
  										</div>
  									</div>
  								</div>
  							</div>

                <div class="col-xs-12 col-sm-6">
  								<div class="widget-box">
  									<div class="widget-header">
  										<h4 class="widget-title">กำหนดไซส์</h4>
  									</div>
  									<div class="widget-body">
  										<div class="widget-main" style="height: 350px; overflow:scroll;">
                        <?php  if(!empty($sizes)) : ?>
                          <?php foreach($sizes as $size) : ?>
                            <div class="col-sm-12">
                              <label>
                                <input type="checkbox" class="ace sizeBox" name="sizes[]"
																data-id="<?php echo $size->id; ?>"
																data-code="<?php echo $size->code; ?>"
																data-name="<?php echo $size->name; ?>"
																value="<?php echo $size->code; ?>" />
                                <span class="lbl" id="si-<?php echo $size->id; ?>">   <?php echo $size->code; ?> </span>
                              </label>
                            </div>

                          <?php endforeach; ?>
                        <?php endif; ?>
  										</div>
  									</div>
  								</div>
  							</div>
              </div>
            </div>

						<div class="step-pane" data-step="2">
              <div class="hide" id="colorBox"></div>
              <div class="hide" id="sizeBox"></div>
              <div class="hide" id="imageSet"></div>
                    <div class="row" style="min-height:400px;">
                <?php if(!empty($images)) : ?>
                  <?php foreach($images as $img) : ?>
                    <div class="col-sm-1 col-1-harf">
                      <p class="text-center">
                        <img src="<?php echo get_image_path($img->id, 'medium'); ?>" data-id="<?php echo $img->id; ?>" id="img-<?php echo $img->id; ?>" class="width-100" />
                      </p>
                      <p class="text-center">
                        <select name="image[<?php echo $img->id; ?>]" id="<?php echo $img->id; ?>" data-id="<?php echo $img->id; ?>" class="form-control imageBox">
                          <option value="">เลือกสี</option>
                        </select>
                      </p>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>

						</div>

						<div class="step-pane" data-step="3">
              <div class="row" style="min-height:400px;">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
                  <table class="table border-1">
                    <thead>
                      <tr>
                        <th class="width-10 text-center">รูปภาพ</th>
                        <th class="width-25">รหัสสินค้า</th>
												<th class="width-45">ชื่อสินค้า</th>
												<th class="width-10">ราคาทุน</th>
												<th class="width-10">ราคาขาย</th>
                      </tr>
                    </thead>
                    <tbody id="preGen">

                    </tbody>
                  </table>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 hide">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th class="text-center">size</td>
                        <th class="">ทุน</td>
                        <th class="">ราคา</td>
                      </tr>
                    </thead>
                   <tbody id="setCostPrice">

                   </tbody>
                  </table>
                </div>
              </div> <!-- row -->
						</div>
					</div>
				</div><!-- /section:plugins/fuelux.wizard.container -->
				<hr>
</div>
				<div class="wizard-actions">	<!-- #section:plugins/fuelux.wizard.buttons -->
					<button class="btn btn-prev"> Prev	</button>
					<button class="btn btn-success btn-next" data-last="Finish">Next</button>
				</div><!-- /section:plugins/fuelux.wizard.buttons -->
		</div><!-- /.widget-main -->
	</div><!-- /.widget-body -->
</div>

<script id="row-template" type="text/x-handlebarsTemplate">
	<tr id="{{no}}">
		<td class="middle text-center td-{{colorId}}" data-no="{{no}}">img</td>
		<td class="middle">{{code}}
			<input type="hidden" class="item-gen" id="item-{{no}}"
			data-no="{{no}}"
			data-code="{{code}}"
			data-name="{{name}}"
			data-color="{{color}}"
			data-size="{{size}}"
			data-img="{{img_id}}"
			data-cost="{{cost}}"
			data-price="{{price}}"
			data-style="{{style}}" value="{{code}}" />
		</td>
		<td class="middle">{{name}}</td>
		<td class="middle">
			<input type="number" id="cost-{{no}}" class="form-control input-sm text-right cost" data-item="{{code}}" value="{{cost}}" onchange="updateItemCost({{no}})" />
		</td>
		<td class="middle">
			<input type="number" id="price-{{no}}" class="form-control input-sm text-right price" data-item="{{code}}" value="{{price}}" onchange="updateItemPrice({{no}})" />
		</td>
	</tr>
</script>
<script src="<?php echo base_url(); ?>scripts/masters/product_generater.js?v=<?php echo date('YmdH'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
