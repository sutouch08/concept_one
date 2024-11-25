<?php $disabled = $style->edit ? '' : 'disabled'; ?>
<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/update_style"; ?>">
<div class="row">
	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">รหัสรุ่นสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<label class="form-control width-100" disabled="disabled"><?php echo $style->code; ?></label>
			<input type="hidden" name="code" id="code" value="<?php echo $style->code; ?>" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">ชื่อรุ่นสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100" value="<?php echo $style->name; ?>" required <?php echo $disabled; ?>/>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
	</div>

	<div class="form-group hide">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">รหัสรุ่นเก่า</label>
		<div class="col-xs-12 col-sm-3">
			<input type="text" name="old_style" id="old_style" class="width-100" value="<?php echo $style->old_code; ?>" placeholder="รหัสรุ่นเก่า (ไม่บังคับ)" <?php echo $disabled; ?> />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="oldcode-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">ทุน</label>
		<div class="col-xs-5 col-sm-3">
			<input type="number" step="any" name="cost" id="cost" class="width-100" value="<?php echo $style->cost; ?>"  <?php echo $disabled; ?>/>
		</div>
	<?php if($style->edit) : ?>
		<div class="col-sm-3 col-xs-7">
			<label>
				<input type="checkbox" class="ace" id="cost-update" name="cost_update" value="Y"/>
				<span class="lbl">  อัพเดตทุนในรายการด้วย</span>
			</label>
		</div>
	<?php endif; ?>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="cost-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">ราคา</label>
		<div class="col-xs-5 col-sm-3">
			<input type="number" step="any" name="price" id="price" class="width-100" value="<?php echo $style->price; ?>" <?php echo $disabled; ?>/>
		</div>
	<?php if($style->edit) : ?>
		<div class="col-sm-3 col-xs-7">
			<label>
				<input type="checkbox" class="ace" id="price-update" name="price_update" value="Y"/>
				<span class="lbl">  อัพเดตราคาในรายการด้วย</span>
			</label>
		</div>
	<?php endif; ?>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="price-error"></div>

	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">หน่วยนับ</label>
		<div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm" name="unit_code" id="unit_code" onchange="updateUnit()" required <?php echo $disabled; ?>>
				<option value="">เลือกรายการ</option>
				<?php echo select_unit($style->unit_code); ?>
			</select>
			<input type="hidden" name="unit_id" id="unit_id" value="12" />
			<input type="hidden" name="unit_group" id="unit_group" value="12" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="unit-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ภาษีซื้อ</label>
		<div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm" name="purchase_vat_code" id="purchase-vat-code" required <?php echo $disabled; ?>>
				<option value="">เลือก</option>
				<?php echo select_purchase_vat_group($style->purchase_vat_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="purchase-vat-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ภาษีขาย</label>
		<div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm" name="sale_vat_code" id="sale-vat-code" required <?php echo $disabled; ?>>
				<option value="">เลือก</option>
				<?php echo select_sale_vat_group($style->sale_vat_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="sale-vat-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">ยี่ห้อ</label>
		<div class="col-xs-12 col-sm-3">
			<select name="brand_code" id="brand" class="form-control" required <?php echo $disabled; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_brand($style->brand_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="brand-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">กลุ่มสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="group_code" id="group" class="form-control" required <?php echo $disabled; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_group($style->group_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">กลุ่มหลักสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="main_group_code" id="mainGroup" class="form-control" required <?php echo $disabled; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_main_group($style->main_group_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="mainGroup-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">กลุ่มย่อยสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="sub_group_code" id="subGroup" class="form-control" <?php echo $disabled; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_sub_group($style->sub_group_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="subGroup-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">หมวดหมู่สินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="category_code" id="category" class="form-control" required <?php echo $disabled; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_category($style->category_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="category-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">ประเภทสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="kind_code" id="kind" class="form-control" required <?php echo $disabled; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_kind($style->kind_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="kind-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">ชนิดสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="type_code" id="type" class="form-control" required <?php echo $disabled; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_type($style->type_code); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="type-error"></div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 col-xs-12 control-label no-padding-right">ปีสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="year" id="year" class="form-control" required <?php echo $disabled; ?>>
				<option value="">กรุณาเลือก</option>
			<?php echo select_years($style->year); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="year-error"></div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">แถบแสดงสินค้า</label>
		<div class="col-xs-12 col-sm-reset">
			<?php echo productTabsTree($style->code, TRUE, $style->edit); ?>
		</div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">นับสต็อก</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="count_stock" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($style->count_stock, 1); ?> <?php echo $disabled; ?>/>
				<span class="lbl"></span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">อนุญาติให้ขาย</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="can_sell" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($style->can_sell, 1); ?> <?php echo $disabled; ?>/>
				<span class="lbl"></span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>


	<div class="form-group hide">
		<label class="col-sm-3 control-label no-padding-right">API</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="is_api" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($style->is_api, 1); ?> <?php echo $disabled; ?>/>
				<span class="lbl"></span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">เปิดใช้งาน</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="active" class="ace ace-switch ace-switch-7" type="checkbox" value="1" <?php echo is_checked($style->active, 1); ?> <?php echo $disabled; ?>/>
				<span class="lbl"></span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>
<?php if($style->edit) : ?>
	<div class="form-group">
		<label class="col-sm-3 control-label not-show">บันทึก</label>
		<div class="col-xs-12 col-sm-3">
			<button type="submit" class="btn btn-sm btn-success btn-block"><i class="fa fa-save"></i> บันทึก</button>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>
<?php endif; ?>
</div>

<input type="hidden" id="style" value="<?php echo $style->id; ?>" />
<input type="hidden" id="style_id" value="<?php echo $style->id; ?>" />
</form>
