<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
		<h4 class="title">
			<?php echo $this->title; ?>
		</h4>
	</div>
	<div class="col-xs-12 visible-xs padding-5">
		<h4 class="title-xs"><?php echo $title; ?></h4>
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
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ชื่อลูกค้าลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Tax ID</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่อ้างอิง</label>
		<input type="text" class="form-control input-sm search" name="reference" value="<?php echo $reference; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>จุดขาย</label>
		<select class="form-control input-sm" name="shop_id" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_shop_id($shop_id); ?>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เครื่อง POS</label>
		<select class="form-control input-sm" name="pos_id" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_pos_id($pos_id); ?>
		</select>
	</div>

	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label>คลัง</label>
		<select class="form-control input-sm" name="warehouse" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_warehouse($warehouse); ?>
		</select>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>


	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Status</label>
		<select class="form-control input-sm" name="status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="O" <?php echo is_selected('O', $status); ?>>Open</option>
			<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
			<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>DO No.</label>
    <input type="text" class="form-control input-sm search" name="inv_code" value="<?php echo $inv_code; ?>" />
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
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:1040px;">
			<thead>
				<tr>
					<th class="fix-width-80 middle"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-100 middle">วันที่</th>
					<th class="fix-width-100 middle">เลขที่เอกสาร</th>
					<th class="fix-width-120 middle">เลขที่อ้างอิง</th>
					<th class="fix-width-100 middle">DO No</th>
					<th class="fix-width-100 middle text-right">ยอดเงิน</th>
					<th class="fix-width-50 middle">สถานะ</th>
					<th class="fix-width-150 middle">คลังสินค้า</th>
					<th class="min-width-200 middle">ลูกค้า</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment($this->segment) + 1; ?>
          <?php foreach($orders as $rs) : ?>
            <tr id="row-<?php echo $rs->id; ?>" style="<?php echo statusBgColor($rs->status); ?>">
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)">
									<i class="fa fa-eye"></i>
								</button>
							<?php if($rs->status != 'D' && $this->pm->can_delete) : ?>
								<button type="button" class="btn btn-minier btn-danger" onclick="getCancel(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>')">
									<i class="fa fa-trash"></i>
								</button>
							<?php endif; ?>
							</td>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
              <td class="middle"><?php echo $rs->reference; ?></td>
              <td class="middle"><?php echo $rs->inv_code; ?></td>
              <td class="middle text-right"><?php echo number($rs->amount, 2); ?></td>
							<td class="middle text-center"><?php echo bill_status_label($rs->status); ?></td>
              <td class="middle"><?php echo $rs->warehouse_code; ?></td>
              <td class="middle"><?php echo $rs->customer_name; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/order_pos_invoice/order_pos_invoice.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
