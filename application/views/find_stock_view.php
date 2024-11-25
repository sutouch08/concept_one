<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-12">
    <h3 class="title"><?php echo $this->title; ?></h3>
    </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>รหัส</label>
    <input type="text" class="form-control input-sm text-center search" name="code" id="code" value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>ชื่อ</label>
    <input type="text" class="form-control input-sm text-center search" name="name" id="name" value="<?php echo $name; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>ราคา</label>
		<div class="input-reduction">
			<select class="form-control input-sm input-discount" name="operater">
				<option value="equal">เท่ากับ</option>
				<option value="less_than" <?php echo is_selected('less_than', $operater); ?>> ไม่เกิน </option>
				<option value="more_than" <?php echo is_selected('more_than', $operater); ?>> มากกว่า </option>
			</select>
			<input type="number" class="form-control input-sm input-unit search text-center" name="price" id="price" value="<?php echo $price; ?>" />
		</div>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>ไซส์</label>
    <input type="text" class="form-control input-sm text-center search" name="size" id="size" value="<?php echo $size; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>สี</label>
    <select class="form-control input-sm" name="color_group" id="color_group">
      <option value="">ทั้งหมด</option>
      <?php echo select_color_group($color_group); ?>
    </select>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>คลัง</label>
    <select class="form-control input-sm" id="warehouse" name="warehouse">
      <option value="">ทั้งหมด</option>
      <?php echo select_warehouse($warehouse); ?>
    </select>
  </div>


	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>กลุ่ม</label>
    <select class="form-control input-sm" name="group">
			<option value="">ทั้งหมด</option>
			<?php echo select_product_group($group); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>หมวดหมู่</label>
		<select class="form-control input-sm" name="category">
			<option value="">ทั้งหมด</option>
			<?php echo select_product_category($category); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>ประเภท</label>
		<select class="form-control input-sm" name="kind">
			<option value="">ทั้งหมด</option>
			<?php echo select_product_kind($kind); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>ชนิด</label>
		<select class="form-control input-sm" name="type">
			<option value="">ทั้งหมด</option>
			<?php echo select_product_type($type); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>ยี่ห้อ</label>
		<select class="form-control input-sm" name="brand">
			<option value="">ทั้งหมด</option>
			<?php echo select_product_brand($brand); ?>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label>ปี</label>
		<select class="form-control input-sm" name="year">
			<option value="">ทั้งหมด</option>
			<?php echo select_years($year); ?>
		</select>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-bordered table-hover" style="min-width:970px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-60 middle text-center"></th>
					<th class="fix-width-200 middle text-center">รหัส</th>
					<th class="min-width-200 middle text-center">สินค้า</th>
					<th class="fix-width-60 middle text-center">สี</th>
					<th class="fix-width-60 middle text-center">ไซส์</th>
					<th class="fix-width-80 middle text-center">ราคา</th>
					<th class="fix-width-80 middle text-center">สต็อก</th>
					<th class="fix-width-80 middle text-center">จอง</th>
					<th class="fix-width-80 middle text-center">คงเหลือ</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($data)) : ?>
				<?php $no = $this->uri->segment(4) + 1; ?>
				<?php foreach($data as $rs) : ?>
					<tr id="row-<?php echo $rs->code; ?>" class="font-size-12">
						<td class="middle text-center"><?php echo $no; ?></td>
						<td class="middle text-center"><img src="<?php echo get_product_image($rs->code, 'mini'); ?>" /></td>
						<td class="middle"><?php echo $rs->code; ?></td>
						<td class="middle"><?php echo $rs->name; ?></td>
						<td class="middle text-center"><?php echo $rs->color_code; ?></td>
						<td class="middle text-center"><?php echo $rs->size_code; ?></td>
						<td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
						<td class="middle text-center blue"><?php echo number($rs->OnHand); ?></td>
						<td class="middle text-center green"><?php echo number($rs->ordered); ?></td>
						<td class="middle text-center red"><?php echo number($rs->balance); ?></td>
					</tr>
					<?php $no++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script>
function clearFilter(){
  $.get(BASE_URL+'find_stock/clear_filter', function(){
    window.location.href = BASE_URL + 'find_stock';
  })
}


function getSearch()
{
	load_in();
	$('#searchForm').submit();
}


$('.search').keyup(function(e){
	if(e.keyCode == 13){
		getSearch();
	}
})
</script>

<?php $this->load->view('include/footer'); ?>
