<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
		<h4 class="title">
			<?php echo $this->title; ?>
		</h4>
	</div>
	<div class="col-xs-12 visible-xs padding-5">
		<h4 class="title-xs"><?php echo $this->title; ?></h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-success" onclick="addNew()">เพิ่มใหม่</button>
		<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row" id="search-row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่อ้างอิง</label>
		<input type="text" class="form-control input-sm search" name="reference" value="<?php echo $reference; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ใบสั่งขาย</label>
		<input type="text" class="form-control input-sm search" name="so_code" value="<?php echo $so_code; ?>" />
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

	<div class="col-lg-2 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>พนักงานขาย</label>
		<select class="width-100 filter" name="sale_id" id="sale_id">
			<option value="all">ทั้งหมด</option>
			<?php echo select_saleman($sale_id); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>User</label>
		<select class="width-100 filter" name="user" id="user">
			<option value="all">ทั้งหมด</option>
			<?php echo select_user($user); ?>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>เล่มเอกสาร</label>
		<select class="form-control input-sm filter" name="bookcode">
			<option value="all">ทั้งหมด</option>
			<option value="C" <?php echo is_selected('C', $bookcode); ?>>เงินสด</option>
			<option value="T" <?php echo is_selected('T', $bookcode); ?>>เงินเชื่อ</option>
			<option value="P" <?php echo is_selected('P', $bookcode); ?>>POS</option>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>สถานะ</label>
		<select class="form-control input-sm filter" name="status">
			<option value="all">ทั้งหมด</option>
			<option value="O" <?php echo is_selected('O', $status); ?>>Open</option>
			<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
			<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
		</select>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Tax Status</label>
		<select class="form-control input-sm filter" name="tax_status">
			<option value="all">ทั้งหมด</option>
			<option value="Y" <?php echo is_selected('Y', $tax_status); ?>>Yes</option>
			<option value="N" <?php echo is_selected('N', $tax_status); ?>>No</option>
		</select>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>การส่งออก</label>
    <select class="form-control input-sm filter" name="is_export">
			<option value="all">ทั้งหมด</option>
			<option value="N" <?php echo is_selected('N', $is_export); ?>>ยังไม่ส่ง</option>
			<option value="Y" <?php echo is_selected('Y', $is_export); ?>>ส่งออกแล้ว</option>
			<option value="E" <?php echo is_selected('E', $is_export); ?>>ส่งไม่สำเร็จ</option>
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15 margin-bottom-15" />
<?php echo $this->pagination->create_links(); ?>
<div class="row" style="margin-right:0px;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" id="bill-div" style="padding:0px; margin-left:5px; outline:solid 2px #ccc;">
		<table class="table table-striped border-1 tableFixHead" style="font-size:11px; min-width:1390px;">
			<thead>
				<tr>
					<th class="fix-width-80 middle fix-header"></th>
					<th class="fix-width-40 middle text-center fix-header">#</th>
					<th class="fix-width-80 middle fix-header">วันที่</th>
					<th class="fix-width-100 middle fix-header">เลขที่</th>
					<th class="fix-width-100 middle fix-header">อ้างอิง</th>
					<th class="fix-width-100 middle fix-header">ใบสั่งขาย</th>
					<th class="fix-width-100 middle text-right fix-header">มูลค่า</th>
					<th class="fix-width-50 middle text-center fix-header">สถานะ</th>
					<th class="fix-width-50 middle text-center fix-header">เล่ม</th>
					<th class="fix-width-50 middle text-center fix-header">Tax</th>
					<th class="fix-width-120 middle fix-header">พนักงานขาย</th>
					<th class="fix-width-120 middle fix-header">User</th>
					<th class="fix-width-100 middle text-center fix-header">คลังสินค้า</th>
					<th class="fix-width-200 middle fix-header">ลูกค้า</th>
					<th class="fix-width-100 middle fix-header">SAP No</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment($this->segment) + 1; ?>
          <?php foreach($orders as $rs) : ?>
            <tr id="row-<?php echo $rs->id; ?>" style="<?php echo statusBgColor($rs->status); ?>">
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')">
									<i class="fa fa-eye"></i>
								</button>
							<?php if($rs->status != 'D' && ($this->pm->can_delete OR $this->_SuperAdmin)) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getCancel(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
							</td>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle"><?php echo thai_date($rs->DocDate, FALSE, '.'); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
              <td class="middle"><?php echo $rs->BaseRef; ?></td>
							<td class="middle"><?php echo $rs->so_code; ?></td>
              <td class="middle text-right"><?php echo number($rs->DocTotal + $rs->VatSum, 2); ?></td>
							<td class="middle text-center"><?php echo bill_status_label($rs->status); ?></td>
							<td class="middle text-center"><?php echo bookcode_name($rs->bookcode); ?></td>
							<td class="middle text-center"><?php echo $rs->TaxStatus == 'Y' ? 'Yes' : 'No'; ?></td>
							<td class="middle"><?php echo get_sale_name($rs->SlpCode); ?></td>
							<td class="middle"><?php echo $rs->user; ?></td>
              <td class="middle text-center"><?php echo $rs->WhsCode; ?></td>
              <td class="middle"><?php echo $rs->CardName; ?></td>
							<td class="middle"><?php echo $rs->DocNum; ?></td>
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
	$('#sale_id').select2();
	$('#user').select2();
</script>

<script src="<?php echo base_url(); ?>scripts/order_invoice/order_invoice.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
