<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-xs-12 padding-5 visible-xs">
    <h3 class="title-xs"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    <p class="pull-right top-p">
  <?php if($this->pm->can_add) : ?>
      <button type="button" class="btn btn-sm btn-success btn-top btn-100" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
  <?php endif; ?>
    </p>
  </div>
</div>
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row" id="search-row">
  <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code" value="<?php echo $code; ?>" />
  </div>
  <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>ใบเบิกแปรสภาพ</label>
    <input type="text" class="form-control input-sm search" name="ref_code" value="<?php echo $ref_code; ?>" />
  </div>
  <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>บิลขาย/ออเดอร์</label>
    <input type="text" class="form-control input-sm search" name="bill_code" value="<?php echo $bill_code; ?>" />
  </div>
  <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>ชื่อลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer_ref" value="<?php echo $customer_ref; ?>" />
  </div>
  <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>เบอร์โทร</label>
    <input type="text" class="form-control input-sm search" name="phone" value="<?php echo $phone; ?>" />
  </div>
  <div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>ชื่องาน</label>
    <input type="text" class="form-control input-sm search" name="job_title" value="<?php echo $job_title; ?>" />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-1-harf col-xs-6 padding-5">
    <label>ประเภทงาน</label>
    <select class="form-control input-sm filter" name="job_type">
      <option value="all">เลือก</option>
      <option value="A" <?php echo is_selected('A', $job_type); ?>>งานปัก</option>
      <option value="B" <?php echo is_selected('B', $job_type); ?>>งานรีด</option>
      <option value="C" <?php echo is_selected('C', $job_type); ?>>งานสกรีน</option>
    </select>
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-1-harf col-xs-6 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm filter" name="status">
      <option value="all">ทั้งหมด</option>
      <option value="P" <?php echo is_selected('P', $status); ?>>Draft</option>
      <option value="O" <?php echo is_selected('O', $status); ?>>Open</option>
      <option value="C" <?php echo is_selected('C', $status); ?>>Close</option>
      <option value="D" <?php echo is_selected('D', $status); ?>>Cancelled</option>
    </select>
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-1-harf col-xs-6 padding-5">
    <label>State</label>
    <select class="form-control input-sm filter" name="state">
      <option value="all">ทั้งหมด</option>
      <option value="1" <?php echo is_selected('1', $state); ?>>รอดำเนินการ</option>
      <option value="2" <?php echo is_selected('2', $state); ?>>รอชำระเงิน</option>
      <option value="3" <?php echo is_selected('3', $state); ?>>รอแบบ</option>
      <option value="31" <?php echo is_selected('31', $state); ?>>ออกแบบเสร็จ</option>
      <option value="32" <?php echo is_selected('32', $state); ?>>รอปริ้นเฟล็ก</option>
      <option value="33" <?php echo is_selected('33', $state); ?>>ปริ้นเฟล็กเสร็จ</option>
      <option value="4" <?php echo is_selected('4', $state); ?>>รอผลิต</option>
      <option value="5" <?php echo is_selected('5', $state); ?>>กำลังผลิต</option>
      <option value="6" <?php echo is_selected('6', $state); ?>>ผลิตเสร็จ</option>
      <option value="7" <?php echo is_selected('7', $state); ?>>รอจัดส่ง</option>
      <option value="8" <?php echo is_selected('8', $state); ?>>จัดส่งแล้ว</option>
    </select>
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>กำหนดรับ</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="due_from_date" id="dueFromDate" value="<?php echo $due_from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="due_to_date" id="dueToDate" value="<?php echo $due_to_date; ?>" />
    </div>
  </div>

  <div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>พนักงาน</label>
    <select class="width-100 filter" name="user" id="user">
      <option value="all">ทั้งหมด</option>
      <?php echo select_user($user); ?>
    </select>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">onlyMe</label>
    <button type="button" id="btn-only-me" class="btn btn-xs btn-block <?php echo $btnOnlyMe; ?>" onclick="toggleOnlyMe()">เฉพาะฉัน</button>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">search</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">Search</button>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">reset</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>
</div>

<input type="hidden" name="onlyMe" id="onlyMe" value="<?php echo $onlyMe; ?>" />
<input type="hidden" name="search" value="1" />
</form>
<hr/>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="bill-div">
    <table class="table tableFixHead border-1" style="min-width:1430px; font-size:12px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center fix-header">#</th>
          <th class="fix-width-100 text-center fix-header">วันที่</th>
          <th class="fix-width-100 fix-header">เลขที่เอกสาร</th>
          <th class="fix-width-60 text-center fix-header">สถานะ</th>
          <th class="fix-width-100 fix-header">ใบเบิกสินค้า</th>
          <th class="fix-width-100 fix-header">บิลขาย</th>
          <th class="min-width-150 fix-header">ชื่องาน</th>
          <th class="fix-width-100 text-center fix-header">สเตท</th>
          <th class="min-width-150 fix-header">ชื่อลูกค้า</th>
          <th class="fix-width-100 fix-header">เบอร์โทร</th>
          <th class="fix-width-100 fix-header">พนักงาน</th>
          <th class="fix-width-100 text-right fix-header">มูลค่า</th>
        </tr>
      </thead>
      <tbody>
  <?php if( ! empty($orders)) : ?>
    <?php $no = 1; ?>
    <?php foreach($orders as $rs) : ?>
        <tr id="row-<?php echo $rs->id; ?>" style="<?php echo state_color($rs->state, $rs->status); ?>">
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle text-center pointer" onclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo thai_date($rs->date_add, FALSE); ?></td>
          <td class="middle pointer" onclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->code; ?></td>
          <td class="middle text-center pointer" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php if($rs->status == 'O') : ?>
              <span class="">Open</span>
            <?php elseif($rs->status == 'C') : ?>
              <span class="">Closed</span>
            <?php elseif($rs->status == 'D') : ?>
              <span class="">Canceled</span>
            <?php else : ?>
              <span class="">Draft</span>
            <?php endif; ?>
          </td>
          <td class="middle pointer" onclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->ref_code; ?></td>
          <td class="middle pointer" onclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->bill_code; ?></td>
          <td class="middle pointer" onclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->job_title; ?></td>
          <td class="middle text-center pointer" onclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo get_state_name($rs->state); ?></td>
          <td class="middle pointer" onclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->prefix.' '.$rs->customer_ref; ?></td>
          <td class="middle pointer" onclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->phone; ?></td>
          <td class="middle" onclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->user; ?></td>
          <td class="middle text-right pointer" onclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo number($rs->DocTotal, 2); ?></td>
        </tr>
        <?php $no++; ?>
    <?php endforeach; ?>
  <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script>
  $('#user').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/sales_order/sales_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sales_order/sales_order_list.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
