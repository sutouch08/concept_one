<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-sm-6">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="margin-bottom-15"/>
<div class="row">
	<form class="form-horizontal" id="addForm" method="post">
	<div class="row">
		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">รหัส</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" class="width-100" value="<?php echo $code; ?>" disabled />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">รหัสเก่า</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="old_code" id="old_code" class="width-100" value="<?php echo $old_code; ?>" placeholder="รหัสเก่า (ไม่บังคับ)" />
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ชื่อ</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="name" id="name" class="width-100" value="<?php echo $name; ?>" disabled />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">รุ่น</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="style" id="style" class="width-100" value="<?php echo $style_code; ?>" disabled />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="style-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">รุ่นเก่า</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="old_style" id="old_style" class="width-100" value="<?php echo $old_style; ?>" placeholder="รหัสรุ่นเก่า (ไม่บังคับ)"/>
			</div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">สี</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="color" id="color" class="width-100" value="<?php echo $color_code; ?>" disabled />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="color-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ไซส์</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="size" id="size" class="width-100" value="<?php echo $size_code; ?>" disabled />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="size-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">บาร์โค้ด</label>
			<div class="col-xs-12 col-sm-3">
				<input type="text" name="barcode" id="barcode" class="width-100" value="<?php echo $barcode; ?>" disabled />
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="barcode-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ราคาทุน</label>
			<div class="col-xs-12 col-sm-3">
				<input type="number" step="any" name="cost" id="cost" class="width-100" value="<?php echo $cost; ?>"  disabled/>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="cost-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ราคาขาย</label>
			<div class="col-xs-12 col-sm-3">
				<input type="number" step="any" name="price" id="price" class="width-100" value="<?php echo $price; ?>"  disabled/>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="price-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">หน่วยนับ</label>
			<div class="col-xs-12 col-sm-3">
				<select class="form-control input-sm" name="unit_code" id="unit_code" disabled>
					<option value="">โปรดเลือก</option>
					<?php echo select_unit($unit_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="unit-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ภาษีซื้อ</label>
			<div class="col-xs-12 col-sm-3">
				<select class="form-control input-sm" name="purchase_vat_code" id="purchase-vat-code" disabled>
					<option value="">เลือก</option>
					<?php echo select_purchase_vat_group($purchase_vat_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="purchase-vat-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ภาษีขาย</label>
			<div class="col-xs-12 col-sm-3">
				<select class="form-control input-sm" name="sale_vat_code" id="sale-vat-code" disabled>
					<option value="">เลือก</option>
					<?php echo select_sale_vat_group($sale_vat_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="sale-vat-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ยี่ห้อ</label>
			<div class="col-xs-12 col-sm-3">
				<select name="brand_code" id="brand" class="form-control" disabled>
					<option value="">โปรดเลือก</option>
				<?php echo select_product_brand($brand_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="brand-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">กลุ่มสินค้า</label>
			<div class="col-xs-12 col-sm-3">
				<select name="group_code" id="group" class="form-control input-sm" disabled>
					<option value="">โปรดเลือก</option>
				<?php echo select_product_group($group_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">กลุ่มหลัก</label>
			<div class="col-xs-12 col-sm-3">
				<select name="main_group_code" id="mainGroup" class="form-control" disabled>
					<option value="">โปรดเลือก</option>
				<?php echo select_product_main_group($main_group_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="mainGroup-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">เพศ</label>
			<div class="col-xs-12 col-sm-3">
				<select name="sub_group_code" id="subGroup" class="form-control" disabled>
					<option value="">โปรดเลือก</option>
				<?php echo select_product_sub_group($sub_group_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="subGroup-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">หมวดหมู่</label>
			<div class="col-xs-12 col-sm-3">
				<select name="category_code" id="category" class="form-control" disabled>
					<option value="">โปรดเลือก</option>
				<?php echo select_product_category($category_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="category-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ประเภท</label>
			<div class="col-xs-12 col-sm-3">
				<select name="kind_code" id="kind" class="form-control" disabled>
					<option value="">โปรดเลือก</option>
				<?php echo select_product_kind($kind_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="kind-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ชนิด</label>
			<div class="col-xs-12 col-sm-3">
				<select name="type_code" id="type" class="form-control" disabled>
					<option value="">โปรดเลือก</option>
				<?php echo select_product_type($type_code); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="type-error"></div>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ปี</label>
			<div class="col-xs-12 col-sm-3">
				<select name="year" id="year" class="form-control" disabled>
					<option value="">โปรดเลือก</option>
				<?php echo select_years($year); ?>
				</select>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red" id="year-error"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">นับสต็อก</label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="count_stock" class="ace ace-switch ace-switch-7" type="checkbox" id="count_stock" value="1" <?php echo is_checked($count_stock,1); ?> disabled/>
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">อนุญาติให้ขาย</label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="can_sell" class="ace ace-switch ace-switch-7" type="checkbox" id="can_sell" value="1" <?php echo is_checked($can_sell,1); ?> disabled/>
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right">ใช้งาน</label>
			<div class="col-xs-12 col-sm-3">
				<label style="padding-top:5px;">
					<input name="active" class="ace ace-switch ace-switch-7" type="checkbox" id="active" value="1" <?php echo is_checked($active,1); ?> disabled/>
					<span class="lbl"></span>
				</label>
			</div>
			<div class="help-block col-xs-12 col-sm-reset inline red"></div>
		</div>
	</div>
	</form>
</div><!--/ row  -->

<script src="<?php echo base_url(); ?>scripts/masters/items.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
