<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pakking-5">
    <h4 class="title">
      <?php echo $this->title; ?>
    </h4>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search-box" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search-box" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>พนักงาน</label>
    <input type="text" class="form-control input-sm search-box" name="user" value="<?php echo $user; ?>" />
  </div>

	<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>รูปแบบ</label>
		<select class="form-control input-sm" name="role" onchange="getSearch()">
      <option value="">ทั้งหมด</option>
      <?php echo select_order_role($role); ?>
    </select>
  </div>

	<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ช่องทางขาย</label>
		<select class="form-control input-sm" name="channels" onchange="getSearch()">
      <option value="">ทั้งหมด</option>
      <?php echo select_channels($channels); ?>
    </select>
  </div>

	<div class="col-lg-2 col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>คลังสินค้า</label>
		<select class="form-control input-sm" name="warehouse" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <?php echo select_sell_warehouse($warehouse); ?>
    </select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>การยืนยัน</label>
		<select class="form-control input-sm" name="is_valid" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="1" <?php echo is_selected($is_valid, '1'); ?>>ยืนยันแล้ว</option>
			<option value="0" <?php echo is_selected($is_valid, '0'); ?>>ยังไม่ยืนยัน</option>
    </select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>Temp</label>
		<select class="form-control input-sm" name="is_exported" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="1" <?php echo is_selected($is_exported, '1'); ?>>ส่งแล้ว</option>
			<option value="0" <?php echo is_selected($is_exported, '0'); ?>>ยังไม่ส่ง</option>
    </select>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> Search</button>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">btn</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
	</div>
</div>

<hr class="margin-top-15">
<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>">
<input type="hidden" name="sort_by" id="sort_by" value="<?php echo $sort_by; ?>">
</form>
<?php echo $this->pagination->create_links(); ?>
<?php $sort_date = $order_by === 'date_add' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''; ?>
<?php $sort_code = $order_by === 'code' ? ($sort_by === 'DESC' ? 'sorting_desc' : 'sorting_asc') : ''; ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1 dataTable" style="min-width:900px;">
      <thead>
        <tr>
          <th class="width-5 text-center">ลำดับ</th>
          <th style="min-width:100px;" class="width-8 sorting <?php echo $sort_date; ?> text-center" id="sort_date_add" onclick="sort('date_add')">วันที่</th>
          <th class="width-20 sorting <?php echo $sort_code; ?>" id="sort_code" onclick="sort('code')">เลขที่เอกสาร</th>
          <th class="">ลูกค้า/ผู้รับ/ผู้เบิก</th>
          <th class="width-10 text-center">ยอดเงิน</th>
          <th class="width-10 text-center">รูปแบบ</th>
          <th class="width-10 text-center">พนักงาน</th>
					<th class="width-10 text-center"></th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($orders as $rs)  : ?>

        <tr class="font-size-12">

          <td class="text-center pointer" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo $no; ?>
          </td>

          <td class="pointer text-center" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo thai_date($rs->date_add); ?>
          </td>

          <td class="pointer" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->code; ?>
            <?php echo ($rs->reference != '' ? ' ['.$rs->reference.']' : ''); ?>
          </td>

          <td class="pointer hide-text" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->customer_name; ?>
          </td>

          <td class="pointer text-center" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo number($rs->total_amount,2); ?>
          </td>

          <td class="pointer text-center" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo role_name($rs->role); ?>
          </td>

          <td class="pointer text-center hide-text" onclick="viewDetail('<?php echo $rs->code; ?>')">
            <?php echo $rs->user; ?>
          </td>
					<td class="pointer text-center hide-text">
						<?php if(($rs->role != 'S' && $rs->role != 'P' && $rs->role != 'U' && $rs->role != 'C') OR $rs->isNew == 0) : ?>
	            <button type="button" class="btn btn-minier btn-success" onclick="do_export('<?php echo $rs->code; ?>')">
								<i class="fa fa-send"></i> SAP
							</button>
						<?php endif; ?>
						<?php if($rs->isNew == 0 && $this->_SuperAdmin) : ?>
							<button type="button" class="btn btn-minier btn-primary" onclick="do_export_update('<?php echo $rs->code; ?>')">
								Update SAP
							</button>
						<?php endif; ?>
          </td>

        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/order_closed/closed.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/order_closed/closed_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
