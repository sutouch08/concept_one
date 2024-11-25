<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
  	</h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
  	<p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
      <?php endif; ?>
    </p>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ใบสั่งซื้อ</label>
    <input type="text" class="form-control input-sm search" name="po" value="<?php echo $po; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ใบส่งสินค้า</label>
    <input type="text" class="form-control input-sm search" name="invoice" value="<?php echo $invoice; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ผู้จำหน่าย</label>
		<input type="text" class="form-control input-sm search" name="vendor" value="<?php echo $vendor; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>พนักงาน</label>
		<input type="text" class="form-control input-sm search" name="user" value="<?php echo $user; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>สถานะ</label>
		<select name="status" class="form-control input-sm" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $status); ?>>ยังไม่บันทึก</option>
			<option value="1" <?php echo is_selected('1', $status); ?>>บันทึกแล้ว</option>
			<option value="2" <?php echo is_selected('2', $status); ?>>ยกเลิก</option>
			<option value="5" <?php echo is_selected('5', $status); ?>>หมดอายุ</option>
		</select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>SAP</label>
		<select name="sap" class="form-control input-sm" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $sap); ?>>ยังไม่เข้า</option>
			<option value="1" <?php echo is_selected('1', $sap); ?>>เข้าแล้ว</option>
		</select>
  </div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>

	<input type="hidden" name="search" value="1" />
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-sm-12 padding-5">
    <p class="pull-right">
      สถานะ :
			<span class="green">OK</span> = บันทึกแล้ว, &nbsp;
			<span class="blue">DF</span> = ยังไม่บันทึก, &nbsp;
      <span class="red">CN</span> = ยกเลิก, &nbsp;
			<span class="dark">EXP</span> = หมดอายุ &nbsp;
    </p>
  </div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:1200px;">
			<thead>
				<tr>
					<th class="fix-width-100"></th>
					<th class="fix-width-40 middle text-center">สถานะ</th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-100 middle">เลขที่เอกสาร</th>
					<th class="fix-width-150 middle">ใบส่งสินค้า</th>
					<th class="fix-width-100 middle">ใบสั่งซื้อ</th>
					<th class="fix-width-100 middle">SAP No.</th>
					<th class="fix-width-250 middle">ผู้จำหน่าย</th>
					<th class="fix-width-100 middle text-center">จำนวน</th>
					<th class="min-width-100 middle text-center">พนักงาน</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($document)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($document as $rs) : ?>
            <tr id="row-<?php echo $rs->code; ?>" style="font-size:12px; <?php echo statusBackgroundColor($rs->is_expire, $rs->status); ?>">
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
								<?php if(($this->pm->can_edit OR $this->pm->can_delete) && $rs->status == 0) : ?>
									<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
									<button type="button" class="btn btn-minier btn-danger" onclick="goDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
								<?php endif; ?>
								<?php if($this->_SuperAdmin) : ?>
									<button type="button" class="btn btn-minier btn-primary" onclick="sendToSap('<?php echo $rs->code; ?>')"><i class="fa fa-send"></i></button>
								<?php endif; ?>
							</td>
							<td class="middle text-center">
								<?php if($rs->is_expire OR $rs->status == 2) : ?>
									<?php if($rs->status == 2) : ?>
										<span class="red"><strong>CN</strong></span>
									<?php else : ?>
										<span class="dark">EXP</span>
									<?php endif; ?>
								<?php else : ?>
									<?php if($rs->status == 0 ) : ?>
										<span class="blue"><strong>DF</strong></span>
									<?php endif; ?>
									<?php if($rs->status == 1 ) : ?>
										<span class="green"><strong>OK</strong></span>
									<?php endif; ?>
								<?php endif; ?>
							</td>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE, '/'); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
              <td class="middle"><?php echo $rs->invoice_code; ?></td>
              <td class="middle"><?php echo $rs->po_code; ?></td>
							<td class="middle"><?php echo $rs->inv_code; ?></td>
              <td class="middle"><?php echo $rs->vendor_name; ?></td>
              <td class="middle text-center"><?php echo number($rs->totalQty); ?></td>
							<td class="middle text-center">
                <?php echo $rs->display_name; ?>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/receive_po/receive_po.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
