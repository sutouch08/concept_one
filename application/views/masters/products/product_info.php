<form class="form-horizontal" id="addForm" method="post" action="<?php echo $this->home."/add_style"; ?>">
<div class="row">
	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">รหัสรุ่นสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<input type="text" name="code" id="code" class="width-100" value="" onkeyup="validCode(this)" autofocus required />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline grey" id="code-error">Allow only [a-z, A-Z, 0-9, "-", "_" ]</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ชื่อรุ่นสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<input type="text" name="name" id="name" class="width-100" value="" required />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
	</div>

	<div class="form-group hide">
		<label class="col-sm-3 control-label no-padding-right">รหัสรุ่นเก่า</label>
		<div class="col-xs-12 col-sm-3">
			<input type="text" name="old_style" id="old_style" class="width-100" value="" placeholder="รหัสรุ่นเก่า (ไม่บังคับ)" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="code-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ทุน</label>
		<div class="col-xs-12 col-sm-3">
			<input type="number" step="any" name="cost" id="cost" class="width-100" value="" required />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ราคา</label>
		<div class="col-xs-12 col-sm-3">
			<input type="number" step="any" name="price" id="price" class="width-100" value="" required />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">หน่วยนับ</label>
		<div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm" name="unit_code" id="unit_code" onchange="updateUnit()" required>
				<option value="">เลือกรายการ</option>
				<?php echo select_unit($unit_code); ?>
			</select>
			<input type="hidden" name="unit_id" id="unit_id" value="12" />
			<input type="hidden" name="unit_group" id="unit_group" value="12" />
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="name-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ภาษีซื้อ</label>
		<div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm" name="purchase_vat_code" id="purchase-vat-code" required>
				<option value="">เลือก</option>
				<?php echo select_purchase_vat_group(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="purchase-vat-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ภาษีขาย</label>
		<div class="col-xs-12 col-sm-3">
			<select class="form-control input-sm" name="sale_vat_code" id="sale-vat-code" required>
				<option value="">เลือก</option>
				<?php echo select_sale_vat_group(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="sale-vat-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ยี่ห้อ</label>
		<div class="col-xs-12 col-sm-3">
			<select name="brand_code" id="brand" class="form-control" required>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_brand(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="brand-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">กลุ่มสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="group_code" id="group" class="form-control" required>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_group(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="group-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">กลุ่มหลักสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="main_group_code" id="mainGroup" class="form-control" required>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_main_group(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="mainGroup-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">เพศ</label>
		<div class="col-xs-12 col-sm-3">
			<select name="sub_group_code" id="subGroup" class="form-control">
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_sub_group(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="subGroup-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">หมวดหมู่สินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="category_code" id="category" class="form-control" required>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_category(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="category-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ประเภทสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="kind_code" id="kind" class="form-control" required>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_kind(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="kind-error"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ชนิดสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="type_code" id="type" class="form-control" required>
				<option value="">กรุณาเลือก</option>
			<?php echo select_product_type(); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="type-error"></div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">ปีสินค้า</label>
		<div class="col-xs-12 col-sm-3">
			<select name="year" id="year" class="form-control" required>
				<option value="">กรุณาเลือก</option>
			<?php echo select_years(date('Y')); ?>
			</select>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red" id="year-error"></div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">แถบแสดงสินค้า</label>
		<div class="col-xs-12 col-sm-reset">
			<?php echo productTabsTree(); ?>
		</div>
	</div>


	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">นับสต็อก</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="count_stock" class="ace ace-switch ace-switch-7" type="checkbox" value="1" checked />
				<span class="lbl"></span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">อนุญาติให้ขาย</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="can_sell" class="ace ace-switch ace-switch-7" type="checkbox" value="1" checked />
				<span class="lbl"></span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>


	<div class="form-group hide">
		<label class="col-sm-3 control-label no-padding-right">API</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="is_api" class="ace ace-switch ace-switch-7" type="checkbox" value="1" />
				<span class="lbl"></span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label no-padding-right">เปิดใช้งาน</label>
		<div class="col-xs-12 col-sm-3">
			<label style="padding-top:5px;">
				<input name="active" class="ace ace-switch ace-switch-7" type="checkbox" value="1" checked />
				<span class="lbl"></span>
			</label>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label not-show">บันทึก</label>
		<div class="col-xs-12 col-sm-3">
			<button type="submit" class="btn btn-sm btn-success btn-block"><i class="fa fa-save"></i> บันทึก</button>
		</div>
		<div class="help-block col-xs-12 col-sm-reset inline red"></div>
	</div>

	<input type="hidden" name="valid" id="valid" value=""/>
</div>
</form>
