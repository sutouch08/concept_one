<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-9 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-3 padding-5">
    	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> เพิมใหม่</button>
      <?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>ใบเบิกแปรสภาพ</label>
    <input type="text" class="form-control input-sm search" name="order_code" value="<?php echo $order_code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>ใบสั่งงาน</label>
    <input type="text" class="form-control input-sm search" name="so_code" value="<?php echo $so_code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>รหัสโซนรับเข้า</label>
    <input type="text" class="form-control input-sm search" name="zone" id="zone" value="<?php echo $zone; ?>" />
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-3 col-xs-6 padding-5">
    <label>สถานะ</label>
		<select name="status" class="form-control input-sm" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $status); ?>>ยังไม่บันทึก</option>
			<option value="1" <?php echo is_selected('1', $status); ?>>บันทึกแล้ว</option>
			<option value="2" <?php echo is_selected('2', $status); ?>>ยกเลิก</option>
			<option value="4" <?php echo is_selected('4', $status); ?>>รอยืนยัน</option>
			<option value="5" <?php echo is_selected('5', $status); ?>>หมดอายุ</option>
		</select>
  </div>


	<div class="col-lg-1 col-md-1-harf col-sm-3 col-xs-6 padding-5">
    <label>SAP</label>
		<select name="sap_status" class="form-control input-sm" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $sap_status); ?>>ยังไม่เข้า</option>
			<option value="1" <?php echo is_selected('1', $sap_status); ?>>เข้าแล้ว</option>
		</select>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>

  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>

	<input type="hidden" name="search" value="1" />
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:900px;">
			<thead>
				<tr>
					<th class="fix-width-100"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-120 middle">เลขที่เอกสาร</th>
					<th class="fix-width-120 middle">ใบส่งสินค้า</th>
					<th class="fix-width-120 middle">ใบเบิกแปรสภาพ</th>
					<th class="fix-width-120 middle">ใบสั่งงาน</th>
					<th class="fix-width-100 middle">SAP No.</th>
					<th class="fix-width-100 middle text-center">จำนวน</th>
					<th class="fix-width-80 middle text-center">สถานะ</th>
					<th class="min-width-100 middle">พนักงาน</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($document)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($document as $rs) : ?>
						<?php $color = $rs->is_expire == 1 ? "light-grey" : ($rs->status == 0 ? "purple" : ($rs->status == 2 ? "red" : ($rs->status == 4 ? "orange" : ""))); ?>
            <tr id="row-<?php echo $rs->code; ?>" class="<?php echo $color; ?>" style="font-size:12px;">
							<td class="middle text-left">
								<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
								<?php if($rs->is_expire == 0 && ($this->pm->can_edit OR $this->pm->can_add) && $rs->status == 0) : ?>
									<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
								<?php endif; ?>
								<?php if($rs->is_expire == 0 && $this->pm->can_delete && $rs->status != 2 && ($rs->status == 0 OR $rs->status == 1 OR $this->_SuperAdmin)) : ?>
									<button type="button" class="btn btn-minier btn-danger" onclick="goDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
								<?php endif; ?>
								<?php if($rs->status == 1 && $this->_SuperAdmin) : ?>
									<button type="button" class="btn btn-minier btn-success" onclick="sendToSap('<?php echo $rs->code; ?>')"><i class="fa fa-send"></i></button>
								<?php endif; ?>
							</td>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE, '/'); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
              <td class="middle"><?php echo $rs->invoice_code; ?></td>
              <td class="middle"><?php echo $rs->order_code; ?></td>
							<td class="middle"><?php echo $rs->so_code; ?></td>
							<td class="middle"><?php echo $rs->inv_code; ?></td>
              <td class="middle text-center"><?php echo $rs->qty; ?></td>
							<td class="middle text-center">
								<?php if($rs->is_expire == 0) : ?>
									<?php if($rs->status == 0 ) : ?>
										<span class="purple"><strong>ดราฟ</strong></span>
									<?php endif; ?>
									<?php if($rs->status == 2) : ?>
										<span class="red"><strong>ยกเลิก</strong></span>
									<?php endif; ?>
									<?php if($rs->status == 1) : ?>
										<span class="green"><strong>บันทึกแล้ว</strong></span>
									<?php endif; ?>
									<?php if($rs->status == 4) : ?>
										<span class="orange"><strong>รอยืนยัน</strong></span>
									<?php endif; ?>
								<?php else : ?>
									<span class="dark"><strong>หมดอายุ</strong></span>
								<?php endif; ?>
							</td>
							<td class="middle"><?php echo $rs->user; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_transform/receive_transform.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
