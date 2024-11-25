<?php $this->load->view('include/header'); ?>
<div class="row" id="title-row">
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
			<button type="button" class="btn btn-white btn-success" onclick="addNew()">เพิ่มใหม่</button>
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
			<label>ใบรับมัดจำ</label>
			<input type="text" class="form-control input-sm search" name="baseDpm" value="<?php echo $baseDpm; ?>" />
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ใบสั่งขาย</label>
			<input type="text" class="form-control input-sm search" name="baseRef" value="<?php echo $baseRef; ?>" />
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
			<label>สถานะ</label>
			<select class="form-control input-sm filter" name="status">
				<option value="all">ทั้งหมด</option>
				<option value="O" <?php echo is_selected('O', $status); ?>>Open</option>
				<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
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
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" id="bill-div" style="padding:0px; margin-left:5px; outline:solid 1px #ccc;">
		<table class="table table-striped tableFixHead" style="font-size:12px; min-width:1400px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center fix-header">#</th>
					<th class="fix-width-80 middle fix-header">วันที่</th>
					<th class="fix-width-100 middle fix-header">เลขที่</th>
					<th class="fix-width-100 middle fix-header">ใบรับมัดจำ</th>
					<th class="fix-width-100 middle fix-header">ใบสั่งขาย</th>
					<th class="fix-width-80 middle fix-header">SAP No</th>
					<th class="fix-width-80 middle text-right fix-header">มูลค่า</th>
					<th class="fix-width-60 middle text-center fix-header">สถานะ</th>
					<th class="min-width-200 middle fix-header">ลูกค้า</th>
					<th class="fix-width-100 middle fix-header">User</th>
					<th class="fix-width-300 middle fix-header">พนักงานขาย</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment($this->segment) + 1; ?>
          <?php foreach($orders as $rs) : ?>
            <tr id="row-<?php echo $rs->id; ?>" title="ดับเบิ้ลคลิ๊กเพื่อเปิด" class="order-row pointer" data-id="<?php echo $rs->id; ?>" style="<?php echo statusBgColor($rs->status); ?>">
              <td class="middle text-center no" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $no; ?></td>
              <td class="middle" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo thai_date($rs->DocDate, FALSE, '.'); ?></td>
              <td class="middle" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->code; ?></td>
              <td class="middle" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->BaseDpm; ?></td>
							<td class="middle" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->BaseRef; ?></td>
							<td class="middle" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->DocNum; ?></td>
							<td class="middle text-right" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo number($rs->DocTotal, 2); ?></td>
							<td class="middle text-center" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->status == 'D' ? 'Canceled' : 'Open'; ?></td>
							<td class="middle" ondblclick="viewDetail('<?php echo $rs->code; ?>')">
								<?php echo $rs->CardCode; ?> : <?php echo $rs->CardName; ?>
							</td>
							<td class="middle" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo $rs->user; ?></td>
							<td class="middle" ondblclick="viewDetail('<?php echo $rs->code; ?>')"><?php echo get_sale_name($rs->SlpCode); ?></td>
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

	$('.order-row').click(function() {
		if($(this).hasClass('active-row')) {
			$(this).removeClass('active-row');
		}
		else {
			$(this).addClass('active-row');
		}
	})
</script>

<script src="<?php echo base_url(); ?>scripts/down_payment_invoice/down_payment_invoice.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
