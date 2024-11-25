<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
    <p class="pull-right top-p">
      <?php if($this->pm->can_add) : ?>
        <button type="button" class="btn btn-sm btn-success" onclick="goAdd()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
      <?php endif; ?>
    </p>
  </div>
</div>
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row">
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="form-control input-sm text-center search" name="code" value="<?php echo $code; ?>" />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
      <label>ใบยืมสินค้า</label>
      <input type="text" class="form-control input-sm text-center search" name="lend_code" value="<?php echo $lend_code; ?>" />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
      <label>ผู้ยืม</label>
      <input type="text" class="form-control input-sm  search" name="empName" value="<?php echo $empName; ?>" />
    </div>
		<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 padding-5">
			<label>โซน</label>
      <input type="text" class="form-control input-sm search" name="zone" value="<?php echo $zone; ?>" />
		</div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>สถานะ</label>
      <select class="form-control input-sm" name="status" onchange="getSearch()">
  			<option value="all">ทั้งหมด</option>
  			<option value="0" <?php echo is_selected('0', $status); ?>>ยังไม่บันทึก</option>
  			<option value="1" <?php echo is_selected('1', $status); ?>>สำเร็จ</option>
  			<option value="2" <?php echo is_selected('2', $status); ?>>ยกเลิก</option>
				<option value="3" <?php echo is_selected('3', $status); ?>>WMS Process</option>
        <option value="4" <?php echo is_selected('4', $status); ?>>รอการยืนยัน</option>
        <option value="5" <?php echo is_selected('5', $status); ?>>หมดอายุ</option>
  		</select>
    </div>
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>การยืนยัน</label>
      <select class="form-control input-sm" name="must_accept" onchange="getSearch()">
        <option value="all">ทั้งหมด</option>
        <option value="1" <?php echo is_selected('1', $must_accept); ?>>ต้องยืนยัน</option>
        <option value="0" <?php echo is_selected('0', $must_accept); ?>>ไม่ต้องยืนยัน</option>
      </select>
    </div>
    <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
      <label>วันที่</label>
      <div class="input-daterange input-group">
        <input type="text" class="form-control input-sm width-50 from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
        <input type="text" class="form-control input-sm width-50" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
      </div>
    </div>

    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
      <label>SAP</label>
  		<select name="sap" class="form-control input-sm" onchange="getSearch()">
  			<option value="all">ทั้งหมด</option>
  			<option value="0" <?php echo is_selected('0', $sap); ?>>ยังไม่เข้า</option>
  			<option value="1" <?php echo is_selected('1', $sap); ?>>เข้าแล้ว</option>
  		</select>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
      <label class="display-block not-show">btn</label>
      <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
      <label class="display-block not-show">btn</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
    </div>
  </div>
</form>
<hr class="margin-top-15"/>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <p class="pull-right top-p">
      สถานะ : ว่างๆ = ปกติ,&nbsp;
      <span class="purple bold">OP</span> = รอรับที่ WMS, &nbsp;
      <span class="orange bold">WC</span> = รอการยืนยัน, &nbsp;
      <span class="red bold">CN</span> = ยกเลิก, &nbsp;
      <span class="dark bold">EXP</span> = หมดอายุ
    </p>
    <table class="table table-striped border-1" style="min-width:1200px;">
      <thead>
        <tr>
          <th class="fix-width-80"></th>
          <th class="fix-width-60 text-center">สถานะ</th>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-100 text-center">วันที่</th>
          <th class="fix-width-120">เลขที่เอกสาร</th>
          <th class="fix-width-120">เลขที่บิล</th>
					<th class="fix-width-150">โซน</th>
          <th class="fix-width-100 text-right">จำนวน</th>
          <th class="fix-width-120 text-right">มลูค่า</th>
          <th class="fix-width-200">ผู้ยืม</th>
          <th class="min-width-100">ผู้รับคืน</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($docs)) : ?>
<?php   $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($docs as $rs) : ?>
          <tr class="font-size-12" id="row-<?php $rs->code; ?>" style="<?php echo statusBackgroundColor($rs->is_expire, $rs->status); ?>">
            <td class="middle">
              <button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
              <?php if($this->pm->can_delete && $rs->status != 2) : ?>
                <button type="button" class="btn btn-minier btn-danger" onclick="goDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
              <?php endif; ?>
            </td>
            <td class="middle text-center">
              <?php if($rs->is_expire) : ?>
                <span class="dark">EXP</span>
              <?php else : ?>
                <?php if($rs->status == 2) : ?>
                  <span class="red">CN</span>
                <?php endif;?>
                <?php if($rs->status == 3) : ?>
                  <span class="purple">OP</span>
                <?php endif; ?>
                <?php if($rs->status == 4) : ?>
                  <span class="orange">WC</span>
                <?php endif; ?>
              <?php endif; ?>
            </td>
            <td class="middle text-center no"><?php echo $no; ?></td>
            <td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE); ?></td>
            <td class="middle"><?php echo $rs->code; ?></td>
            <td class="middle"><?php echo $rs->lend_code; ?></td>
						<td class="middle"><?php echo $rs->to_zone; ?></td>
            <td class="middle text-right"><?php echo number($rs->qty); ?></td>
            <td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
            <td class="middle"><?php echo $rs->empName; ?></td>
            <td class="middle"><?php echo $rs->display_name; ?></td>
          </tr>
<?php     $no++; ?>
<?php   endforeach; ?>
<?php else : ?>
        <tr>
          <td colspan="11" class="text-center">
            --- ไม่พบรายการ ---
          </td>
        </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/return_lend/return_lend.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
