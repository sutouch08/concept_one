<?php $this->load->view('include/header'); ?>
<style>
	/*.b0p3 {
		border:0px !important;
		padding:3px !important;
	}*/
</style>
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<button type="button" class="btn btn-white btn-success" onclick="createDownPayments()">เปิดใบรับมัดจำ</button>
			<?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>เลขใบรับมัดจำ</label>
    <input type="text" class="form-control input-sm search" name="dp_code"  value="<?php echo $dp_code; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>ใบรับมัดจำ</label>
    <select class="form-control input-sm filter" name="is_export">
			<option value="all">ทั้งหมด</option>
			<option value="Y" <?php echo is_selected('Y', $is_export); ?>>สร้างแล้ว</option>
			<option value="N" <?php echo is_selected('N', $is_export); ?>>ยังไม่สร้าง</option>
		</select>
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-2-harf col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>พนักงาน</label>
    <select class="width-100 filter" name="user" id="user">
    	<option value="all">ทั้งหมด</option>
			<?php echo select_user_code_and_name($user); ?>
    </select>
  </div>

	<div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>ช่องทาง</label>
    <select class="form-control input-sm" name="channels" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_channels($channels); ?>
		</select>
  </div>

	<div class="col-lg-2 col-md-3-harf col-sm-4 col-xs-6 padding-5">
    <label>เลขที่บัญชี</label>
		<select class="form-control input-sm" name="account" onchange="getSearch()">
      <option value="">ทั้งหมด</option>
      <?php echo select_bank_account($account); ?>
    </select>
  </div>
	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>

  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>สถานะ</label>
		<select class="form-control input-sm" name="valid" onchange="getSearch()">
      <option value="0" <?php echo is_selected($valid, '0'); ?>>รอตรวจสอบ</option>
      <option value="1" <?php echo is_selected($valid, '1'); ?>>ยืนยันแล้ว</option>
			<option value="all" <?php echo is_selected($valid, 'all'); ?>>ทั้งหมด</option>
    </select>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:1380px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">
						<label>
							<input type="checkbox" class="ace chk-all" id="chk-all" onchange="checkAll()"/>
							<span class="lbl"></span>
						</label>
					</th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-80"></th>
					<th class="fix-width-120 middle">เลขที่เอกสาร</th>
					<th class="fix-width-120 middle">ใบรับมัดจำ</th>
          <th class="fix-width-100 middle">ช่องทาง</th>
					<th class="min-width-200 middle">ลูกค้า</th>
          <th class="fix-width-120 middle hidden-md">พนักงาน</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-100 middle text-center">เวลา</th>
					<th class="fix-width-100 middle text-right">ยอดเงิน</th>
					<th class="fix-width-300 middle text-center">เลขที่บัญชี</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
					<?php $channels = channels_array(); ?>
					<?php $account = account_name_array(); ?>
          <?php foreach($orders as $rs) : ?>
            <?php $customer_name = (!empty($rs->customer_ref)) ? $rs->customer_ref : $rs->customer_name; ?>
            <tr id="row-<?php echo $rs->id; ?>" class="font-size-12">
							<td class="middle text-center">
								<?php if($rs->valid == 1 && $rs->dp_code === NULL) : ?>
									<label>
										<input type="checkbox" class="ace chk" value="<?php echo $rs->id; ?>" />
										<span class="lbl"></span>
									</label>
								<?php endif; ?>
							</td>
              <td class="middle text-center no"><?php echo $no; ?></td>
							<td class="middle">
                <button type="button" class="btn btn-minier btn-info" onClick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>
          <?php if($this->pm->can_delete) : ?>
                <button type="button" class="btn btn-minier btn-danger" onClick="removePayment(<?php echo $rs->id; ?>, '<?php echo $rs->order_code; ?>')"><i class="fa fa-trash"></i></button>
          <?php endif; ?>
              </td>
              <td class="middle"><?php echo $rs->order_code; ?></td>
							<td class="middle">
								<?php if( ! empty($rs->dp_code)) : ?>
									<a href="javascript:void(0)" onclick="viewDpm('<?php echo $rs->dp_code; ?>')">
										<?php echo $rs->dp_code; ?>&nbsp; <i class="fa fa-external-link"></i>
									</a>
								<?php endif; ?>
							</td>
              <td class="middle"><?php echo empty($channels[$rs->channels_code]) ? $rs->channels_code : $channels[$rs->channels_code]; ?></td>
              <td class="middle"><?php echo $customer_name; ?></td>
              <td class="middle"><?php echo $rs->user; ?></td>
							<td class="middle text-center"><?php echo thai_date($rs->pay_date, FALSE); ?></td>
							<td class="middle text-center"><?php echo date('H:i:s', strtotime($rs->pay_date)); ?></td>
              <td class="middle text-right" style="font-size:12px;"><?php echo number($rs->pay_amount,2); ?></td>
              <td class="middle text-center" style="font-size:12px;"><?php echo empty($account[$rs->id_account]) ? $rs->acc_no : $account[$rs->id_account]." #".$rs->acc_no; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:400px; max-width:95vw;">
		<div class="modal-content">
			<div class="modal-header" style="border-bottom:solid 1px #ddd;">
				<h4 class="text-center">ข้อมูลการชำระเงิน</h4>
			</div>
			<div class="modal-body" id="detailBody">

			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
						<button type="button" class="btn btn-sm btn-default btn-100" id="btn-cancel" onclick="closeModal('confirmModal')">ยกเลิก</button>
					<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
						<button type="button" class="btn btn-sm btn-danger btn-100 hide" id="btn-unconfirm" onclick="unConfirmPayment()">ยกเลิกการยืนยัน</button>
						<button type="button" class="btn btn-sm btn-primary btn-100" id="btn-confirm" onclick="confirmPayment()">ยืนยันการชำระเงิน</button>
					<?php endif; ?>
					</div>
				</div>
				<input type="hidden" id="id-payment" value="" />
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:500px; max-width:95%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
			</div>
			<div class="modal-body" id="imageBody">

			</div>
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div>

<script id="detailTemplate" type="text/x-handlebars-template">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
			<table class="table table-bordered table-striped" style="margin-bottom:0px;">
				<tr><td class="width-35 text-right b0p3">เลขที่เอกสาร</td><td class="b0p3">{{order_code}}</td></tr>
				<tr><td class="width-35 text-right b0p3">ยอดที่ต้องชำระ</td><td class="b0p3">{{orderAmount}}</td></tr>
				<tr><td class="text-right b0p3">ยอดโอนชำระ</td><td class="b0p3"><span style="font-weight:bold; color:#E9573F;">฿ {{ payAmount }}</span></td></tr>
				<tr><td class="text-right b0p3">วันที่โอน</td><td class="b0p3">{{ payDate }}</td></tr>
				<tr><td class="text-right b0p3">ธนาคาร</td><td class="b0p3">{{ bankName }}</td></tr>
				<tr><td class="text-right b0p3">สาขา</td><td class="b0p3">{{ branch }}</td></tr>
				<tr><td class="text-right b0p3">เลขที่บัญชี</td><td class="b0p3"><span style="font-weight:bold; color:#E9573F;">{{ accNo }}</td></tr>
				<tr><td class="text-right b0p3">ชื่อบัญชี</td><td class="b0p3">{{ accName }}</td></tr>
				<tr>
					<td colspan="2" class="text-center b0p3">
						{{#if imageUrl}}
						<a href="javascript:void(0)" onClick="viewImage('{{ imageUrl }}')">
							รูปสลิปแนบ	<i class="fa fa-paperclip fa-rotate-90"></i>
						</a>
						{{else}}
						---  ไม่พบไฟล์แนบ  ---
						{{/if}}
					</td>
				</tr>
			</table>
		</div>
	</div>
</script>

<script id="orderTableTemplate" type="text/x-handlebars-template">
	{{#each this}}
	<tr id="{{ id }}" class="font-size-12">
		<td class="text-center">{{ no }}</td>
		<td> {{ reference }}</td>
		<td align="center"> {{ channels }}</td>
		<td>{{ customer }}</td>
		<td>{{ employee }}</td>
		<td align="center">{{ payDate }}</td>
		<td align="center">{{ payTime }}</td>
		<td align="center">{{ orderAmount }}</td>
		<td align="center">{{ payAmount }}</td>
		<td align="center">{{ accNo }}</td>
		<td align="right">
			<button type="button" class="btn btn-xs btn-warning" onClick="viewDetail({{ id }})"><i class="fa fa-eye"></i></button>
			<button type="button" class="btn btn-xs btn-danger" onClick="removePayment({{ id }}, '{{ reference }}')"><i class="fa fa-trash"></i></button>
		</td>
	</tr>
	{{/each}}
</script>

<script>
	$('#user').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/orders/payment/payment.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/payment/payment_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
