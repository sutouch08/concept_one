<?php $this->load->view('include/header'); ?>
<script src="<?php echo base_url(); ?>assets/js/select2.js"></script>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
</div><!-- End Row -->
<hr class=""/>

<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="order_code"  value="<?php echo $order_code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm search" name="pd_code" value="<?php echo $pd_code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>โซน</label>
    <input type="text" class="form-control input-sm search" name="zone_code" value="<?php echo $zone_code; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>พนักงาน</label>
    <select class="width-100 filter" name="user" id="user">
			<option value="all">ทั้งหมด</option>
			<?php echo select_user($user); ?>
		</select>
  </div>

  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block">ค้นหา</button>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-bordered" style="min-width:1100px;">
      <tr>
        <th class="fix-width-40 text-center">ลำดับ</th>
        <th class="fix-width-150 text-center">วันที่</th>
        <th class="fix-width-120 text-center">เลขที่เอกสาร</th>
        <th class="min-width-200 text-center">สินค้า</th>
        <th class="fix-width-100 text-center">จำนวน</th>
        <th class="fix-width-100 text-center">สถานะ</th>
    		<th class="fix-width-200 text-center">โซน</th>
				<th class="fix-width-150 text-center">พนักงาน</th>
        <th class="fix-width-60 text-center"></th>
      </tr>
      <tbody>
    <?php if( !empty($data)) : ?>
    <?php $no = $this->uri->segment(4) + 1; ?>
    <?php foreach($data as $rs) : ?>
      <tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
        <td class="text-center no"><?php echo $no; ?></td>
        <td class="text-center"><?php echo thai_date($rs->date_upd, TRUE); ?></td>
        <td class="text-center"><?php echo $rs->order_code; ?></td>
        <td><?php echo $rs->product_code .' : '.$rs->product_name; ?></td>
        <td class="text-center"><?php echo number($rs->qty); ?></td>
    		<td class="text-center"><?php echo $rs->state_name; ?></td>
        <td> <?php echo $rs->zone_name; ?></td>
				<td > <?php echo $rs->user; ?></td>
    		<td class="text-right">
    			<?php if($this->pm->can_delete && $rs->state < 5) : ?>
						<button type="button" class="btn btn-minier btn-danger"
						onclick="deleteBuffer(<?php echo $rs->id;?>, '<?php echo $rs->order_code; ?>', '<?php echo $rs->product_code; ?>')">
							<i class="fa fa-trash"></i>
						</button>
					<?php endif; ?>
    		</td>
      </tr>
    <?php  $no++; ?>
    <?php endforeach; ?>
    <?php else : ?>
      <tr>
        <td colspan="8" class="text-center">--- ไม่พบข้อมูล ---</td>
      </tr>
    <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script>
	$('#user').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/buffer/buffer.js?v=<?php echo date('dmY'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
