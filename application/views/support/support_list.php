<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
  	<p class="pull-right top-p">
    <?php if($this->pm->can_add) : ?>
      <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
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
    <label>ผู้เบิก</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ผู้รับสินค้า</label>
    <input type="text" class="form-control input-sm search" name="user_ref" value="<?php echo $user_ref; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>คลังสินค้า</label>
		<select class="form-control input-sm" name="warehouse" id="warehouse" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<?php echo select_warehouse($warehouse); ?>
		</select>
	</div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>การอนุมัติ</label>
		<select class="form-control input-sm" name="isApprove" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected($isApprove, "0"); ?>>รออนุมัติ</option>
			<option value="1" <?php echo is_selected($isApprove, "1"); ?>>อนุมัติแล้ว</option>
		</select>
	</div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>SAP</label>
		<select class="form-control input-sm" name="sap_status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $sap_status); ?>>ยังไม่ส่งออก</option>
			<option value="1" <?php echo is_selected('1', $sap_status); ?>>ยังไม่เข้า SAP</option>
			<option value="2" <?php echo is_selected('2', $sap_status); ?>>เข้า SAP แล้ว</option>
			<option value="3" <?php echo is_selected('3', $sap_status); ?>>ส่งออกไม่สำเร็จ</option>
		</select>
	</div>

  <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>

<div class="divider-hidden"></div>

<div class="row margin-top-10">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<button type="button" id="btn-state-1" class="btn btn-sm btn-block margin-top-5 margin-bottom-5 <?php echo $btn['state_1']; ?>" onclick="toggleState(1)">รอดำเนินการ</button>
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
		<button type="button" id="btn-state-3" class="btn btn-sm btn-block margin-top-5 margin-bottom-5 <?php echo $btn['state_3']; ?>" onclick="toggleState(3)">รอจัด</button>
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
		<button type="button" id="btn-state-4" class="btn btn-sm btn-block margin-top-5 margin-bottom-5 <?php echo $btn['state_4']; ?>" onclick="toggleState(4)">กำลังจัด</button>
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
		<button type="button" id="btn-state-5" class="btn btn-sm btn-block margin-top-5 margin-bottom-5 <?php echo $btn['state_5']; ?>" onclick="toggleState(5)">รอตรวจ</button>
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
		<button type="button" id="btn-state-6" class="btn btn-sm btn-block margin-top-5 margin-bottom-5 <?php echo $btn['state_6']; ?>" onclick="toggleState(6)">กำลังตรวจ</button>
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
		<button type="button" id="btn-state-7" class="btn btn-sm btn-block margin-top-5 margin-bottom-5 <?php echo $btn['state_7']; ?>" onclick="toggleState(7)">รอเปิดบิล</button>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-4 padding-5">
		<button type="button" id="btn-state-8" class="btn btn-sm btn-block margin-top-5 margin-bottom-5 <?php echo $btn['state_8']; ?>" onclick="toggleState(8)">เปิดบิลแล้ว</button>
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
		<button type="button" id="btn-state-9" class="btn btn-sm btn-block margin-top-5 margin-bottom-5 <?php echo $btn['state_9']; ?>" onclick="toggleState(9)">ยกเลิก</button>
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
		<button type="button" id="btn-not-save" class="btn btn-sm btn-block margin-top-5 margin-bottom-5 <?php echo $btn['not_save']; ?>" onclick="toggleNotSave()">ไม่บันทึก</button>
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
		<button type="button" id="btn-expire" class="btn btn-sm btn-block margin-top-5 margin-bottom-5 <?php echo $btn['is_expire']; ?>" onclick="toggleIsExpire()">หมดอายุ</button>
	</div>
	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-4 padding-5">
		<button type="button" id="btn-only-me" class="btn btn-sm btn-block margin-top-5 margin-bottom-5 <?php echo $btn['only_me']; ?>" onclick="toggleOnlyMe()">เฉพาะฉัน</button>
	</div>
</div>
<input type="hidden" name="state_1" id="state_1" value="<?php echo $state[1]; ?>" />
<input type="hidden" name="state_2" id="state_2" value="<?php echo $state[2]; ?>" />
<input type="hidden" name="state_3" id="state_3" value="<?php echo $state[3]; ?>" />
<input type="hidden" name="state_4" id="state_4" value="<?php echo $state[4]; ?>" />
<input type="hidden" name="state_5" id="state_5" value="<?php echo $state[5]; ?>" />
<input type="hidden" name="state_6" id="state_6" value="<?php echo $state[6]; ?>" />
<input type="hidden" name="state_7" id="state_7" value="<?php echo $state[7]; ?>" />
<input type="hidden" name="state_8" id="state_8" value="<?php echo $state[8]; ?>" />
<input type="hidden" name="state_9" id="state_9" value="<?php echo $state[9]; ?>" />
<input type="hidden" name="notSave" id="notSave" value="<?php echo $notSave; ?>" />
<input type="hidden" name="onlyMe" id="onlyMe" value="<?php echo $onlyMe; ?>" />
<input type="hidden" name="isExpire" id="isExpire" value="<?php echo $isExpire; ?>" />
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:1000px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">ลำดับ</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-120 middle">เลขที่เอกสาร</th>
					<th class="min-width-300 middle">ลูกค้า</th>
					<th class="fix-width-120 middle text-right">ยอดเงิน</th>
					<th class="fix-width-150 middle text-center">สถานะ</th>
					<th class="fix-width-100 middle text-center">พนักงาน</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($orders)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($orders as $rs) : ?>
            <tr id="row-<?php echo $rs->code; ?>" style="<?php echo state_color($rs->state, $rs->status, $rs->is_expired); ?>">
              <td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $no; ?></td>
              <td class="middle text-center pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->code; ?></td>
              <td class="middle pointer" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->customer_name; ?></td>
              <td class="middle pointer text-right" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo number($rs->total_amount, 2); ?></td>
              <td class="middle pointer text-center" onclick="editOrder('<?php echo $rs->code; ?>')">
								<?php if($rs->is_expired) : ?>
									หมดอายุ
								<?php else : ?>
									<?php echo $rs->state_name; ?>
								<?php endif; ?>
							</td>
							<td class="middle pointer text-center" onclick="editOrder('<?php echo $rs->code; ?>')"><?php echo $rs->user; ?></td>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/support/support.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
