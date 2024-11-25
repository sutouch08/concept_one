<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
					<?php if(!empty($zone_code)) : ?>
						<button type="button" class="btn btn-sm btn-success" id="btn-save" onclick="save_all()"><i class="fa fa-save"></i> บันทึก</button>
					<?php endif; ?>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>
<?php $url = $enable_barcode ? $this->home.'/check_barcode' : $this->home.'/check'; ?>
<form id="searchForm" method="post" action="<?php echo $url; ?>">
<div class="row">
	<div class="col-sm-2 padding-5 first">
		<label>รหัสโซน</label>
		<input type="text" class="form-control input-sm" id="zone_code" value="<?php echo $zone_code; ?>" <?php echo (!empty($zone_code) ? 'disabled': ''); ?>>
		<input type="hidden" name="zone_code" id="zone-code" value="<?php echo $zone_code; ?>">
	</div>
	<div class="col-sm-6 padding-5">
		<label>ชื่อโซน</label>
		<input type="text" class="form-control input-sm" id="zone_name" value="<?php echo $zone_name; ?>" disabled>
	</div>
	<div class="col-sm-1 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-info btn-block <?php echo (!empty($zone_code) ? '' : 'hide'); ?>" id="btn-change-zone" onclick="change_zone()">เปลี่ยนโซน</button>
		<button type="button" class="btn btn-xs btn-primary btn-block <?php echo (empty($zone_code) ? '' : 'hide'); ?>" id="btn-set-zone" onclick="set_zone()"> ตรวจนับ</button>
	</div>
</div>
<?php if($enable_search) : ?>
<hr class="margin-top-15 margin-bottom-15"/>
<div class="row">
	<div class="col-sm-3 padding-5 first">
		<input type="text" class="form-control input-sm text-center search" id="product_code" name="product_code" value="<?php echo $product_code; ?>">
	</div>
	<div class="col-sm-1 padding-5">
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
	</div>
	<div class="col-sm-1 padding-5">
		<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearSearch()"><i class="fa fa-retweet"></i> เคลียร์</button>
	</div>
</div>
<?php endif; ?>
</form>
<hr class="margin-top-15 margin-bottom-15" />
<?php if($enable_barcode) : ?>
<div class="row">
	<div class="col-sm-1 padding-5 first">
		<label>จำนวน</label>
		<input type="number" class="form-control input-sm text-center" id="qty" value="1">
	</div>
	<div class="col-sm-2 padding-5">
		<label>บาร์โค้ดสินค้า</label>
		<input type="text" class="form-control input-sm" id="barcode" autofocus>
	</div>
	<div class="col-sm-1 padding-5">
		<label class="display-block not-show">OK</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="check_barcode()">OK</button>
	</div>
</div>
<hr class="margin-top-15 margin-bottom-15" />
<?php endif; ?>
<form id="checkForm" method="post" action="<?php echo $this->home; ?>/save_all">
<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped border-1">
      <tr>
        <th class="width-5 text-center">ลำดับ</th>
        <th class="width-50">สินค้า</th>
        <th class="width-10 text-center">ในระบบ</th>
        <th class="width-10 text-center">นับจริง</th>
				<th class="width-5 text-center"></th>
        <th class="width-10 text-center">ยอดต่าง</th>
				<th class="width-10"></th>
      </tr>
      <tbody id="stock-table">
				<?php $no = 1; ?>
		<?php if(!empty($details)) : ?>
			<?php foreach($details as $rs) : ?>
				<tr>
					<td class="middle text-center">
						<?php echo $no; ?>
					</td>
					<td class="middle">
						<?php echo $rs->product_code; ?>
						<?php if(!empty($rs->old_code)) : ?>
							<?php  echo " | {$rs->old_code}"; ?>
						<?php endif; ?>
						<input type="hidden" name="item[<?php echo $no; ?>]" id="item_<?php echo $no; ?>" value="<?php echo $rs->product_code; ?>">
					</td>
					<td class="middle text-center">
						<span><?php echo number($rs->OnHandQty); ?></span>
						<input type="hidden" id="stock_<?php echo $no;?>" name="stock[<?php echo $no; ?>]" value="<?php echo $rs->OnHandQty; ?>">
					</td>
					<td class="middle text-center">
						<input type="number"
						class="form-control input-sm text-center count_qty"
						data-barcode="<?php echo $rs->barcode; ?>"
						name="qty[<?php echo $no; ?>]"
						id="qty_<?php echo $no; ?>"
						value="<?php echo $rs->count_qty; ?>"
						onkeyup="cal_diff(<?php echo $no; ?>)">
					</td>
					<td class="middle text-center" id="check-no-<?php echo $no; ?>">
						<?php if($enable_barcode) : ?>
							<?php if(!empty($checked)) : ?>
								<span><i class="fa fa-check green"></i></span>
							<?php else :  ?>
							<input type="checkbox" class="check-no" id="check_<?php echo $no; ?>" value="1" <?php echo ($rs->count_qty > 0 ? 'checked' : ''); ?> />
							<?php endif; ?>
						<?php else : ?>
							<?php if(!empty($checked) OR $rs->diff_qty != 0) : ?>
								<span><i class="fa fa-check green"></i></span>
							<?php endif; ?>
						<?php endif; ?>
					</td>
					<td class="middle text-center">
						<span id="diff_<?php echo $no; ?>">
						<?php echo number($rs->diff_qty); ?>
						</span>
					</td>
					<td class="middle">
						<button type="button" class="btn btn-xs btn-info btn-block" id="btn-<?php echo $no; ?>" onclick="save_checked(<?php echo $no; ?>)">
							<i class="fa fa-save"></i> บันทึก
						</button>
					</td>
				</tr>
				<?php $no++; ?>
			<?php endforeach; ?>
		<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<input type="hidden" name="zoneCode" value="<?php echo $zone_code; ?>">
<input type="hidden" name="topRow" id="topRow" value="<?php echo $no; ?>">
<input type="hidden" name="is_barcode" id="is_barcode" value="<?php echo ($enable_barcode) ? 1 : 0; ?>">
</form>

<script id="row-template" type="text/x-handlebars-template">
<tr>
	<td class="middle text-center">{{no}}</td>
	<td class="middle"> {{item}}
		<input type="hidden" name="item[{{no}}]" id="item_{{no}}" value="{{itemCode}}">
	</td>
	<td class="middle text-center">
		<span>{{onHandQty}}</span>
		<input type="hidden" id="stock_{{no}}" name="stock[{{no}}]" value="{{onHandQty}}">
	</td>
	<td class="middle text-center">
		<input type="number"
		class="form-control input-sm text-center count_qty"
		data-barcode="{{barcode}}"
		name="qty[{{no}}]"
		id="qty_{{no}}"
		value="{{qty}}"
		onkeyup="cal_diff({{no}})" readonly>
	</td>
	<td class="middle text-center" id="check-no-{{no}}">
		<input type="checkbox" class="check-no" id="check_{{no}}" value="1" checked />
	</td>
	<td class="middle text-center">
		<span id="diff_{{no}}">
		{{qty}}
		</span>
	</td>
	<td class="middle">
		<button type="button" class="btn btn-xs btn-info btn-block" id="btn-{{no}}" onclick="save_checked({{no}})">
			<i class="fa fa-save"></i> บันทึก
		</button>
	</td>
</tr>
</script>

<script src="<?php echo base_url();?>scripts/inventory/check_stock_diff/check_stock_diff.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url();?>scripts/inventory/check_stock_diff/check_process.js?v=<?php echo date('YmdH'); ?>"></script>


<?php $this->load->view('include/footer'); ?>
