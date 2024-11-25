<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
		<h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
	<div class="col-xs-12 visible-xs padding-5">
		<h3 class="title-xs" ><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>

			<?php if($doc->status == 1 && $doc->is_approved == 0 && $this->pm->can_edit == 1) : ?>
				<button type="button" class="btn btn-sm btn-danger" onclick="unsave()"><i class="fa fa-refresh"></i> ยกเลิกการบันทึก</button>
			<?php endif; ?>
			<?php if($doc->status == 1 && $doc->is_approved == 0 && $this->pm->can_approve == 1) : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="approve()"><i class="fa fa-check"></i> อนุมัติ</button>
			<?php endif; ?>
			<?php if($doc->status == 1 && $doc->is_approved == 1 && $this->pm->can_approve == 1 && (empty($doc->issue_code) && empty($doc->receive_code))) : ?>
				<button type="button" class="btn btn-sm btn-danger" onclick="unapprove()"><i class="fa fa-refresh"></i> ไม่อนุมัติ</button>
			<?php endif; ?>
			<?php if($doc->status == 1 && $doc->is_approved == 1 && (empty($doc->issue_code) OR empty($doc->receive_code))) : ?>
				<button type="button" class="btn btn-sm btn-success" onclick="send_to_sap()"><i class="fa fa-send"></i> ส่งข้อมูลไป SAP</button>
			<?php endif; ?>
		</p>
	</div>
</div>
<hr />

<div class="row">
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" id="date_add" value="<?php echo thai_date($doc->date_add) ?>" readonly disabled/>
    </div>
		<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 padding-5">
			<label>อ้างถึง</label>
			<input type="text" class="form-control input-sm" id="reference" value="<?php echo $doc->reference; ?>" disabled />
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Goods Issue</label>
			<input type="text" class="form-control input-sm text-center" id="issue_code" value="<?php echo $doc->issue_code; ?>" disabled />
		</div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
			<label>Goods Receive</label>
			<input type="text" class="form-control input-sm" id="receive_code" value="<?php echo $doc->receive_code; ?>" disabled />
		</div>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-5">
			<label>พนักงาน</label>
			<input type="text" class="form-control input-sm" id="user" value="<?php echo $doc->user_name; ?>" disabled />
		</div>
		<div class="col-lg-12 col-md-12 col-sm-9 col-xs-12 padding-5">
	   	<label>หมายเหตุ</label>
	    <input type="text" class="form-control input-sm" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled/>
	  </div>

		<?php if($doc->status == 2) : ?>
			<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 padding-5">
		   	<label>เหตุผลในการยกเลิก</label>
		    <input type="text" class="form-control input-sm" id="remark" value="<?php echo $doc->cancle_reason; ?>" disabled/>
		  </div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5">
		   	<label>ยกเลิกโดย</label>
		    <input type="text" class="form-control input-sm"  value="<?php echo $doc->cancle_user; ?>" disabled/>
		  </div>
		<?php endif; ?>

    <input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
</div>

<hr class="margin-top-15 margin-bottom-15"/>

<?php
if($doc->status == 2)
{
  $this->load->view('cancle_watermark');
}
?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<p class="pull-right top-p">
		<?php if(! empty($doc->issue_code) OR ! empty($doc->receive_code)) : ?>
			<span class="red">** เอกสารเข้าระบบ SAP แล้วไม่สามารถแก้ไขได้</span>
		<?php endif; ?>
		</p>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:990px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-200">รหัสสินค้า</th>
          <th class="min-width-300">สินค้า</th>
          <th class="fix-width-250">โซน</th>
          <th class="fix-width-100 text-center">เพิ่ม</th>
          <th class="fix-width-100 text-center">ลด</th>
        </tr>
      </thead>
      <tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php   $no = 1;    ?>
<?php   foreach($details as $rs) : ?>
      <tr class="font-size-12 rox" id="row-<?php echo $rs->id; ?>">
        <td class="middle text-center no">
          <?php echo $no; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_code; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_name; ?>
        </td>
        <td class="middle text-center">
          <?php echo $rs->zone_name; ?>
        </td>
        <td class="middle text-center" id="qty-up-<?php echo $rs->id; ?>">
          <?php echo $rs->qty > 0 ? ($rs->qty * 1) : 0 ; ?>
        </td>
        <td class="middle text-center" id="qty-down-<?php echo $rs->id; ?>">
          <?php echo $rs->qty < 0 ? ($rs->qty * -1) : 0 ; ?>
        </td>
      </tr>
<?php     $no++; ?>
<?php   endforeach; ?>
<?php endif; ?>
      </tbody>
    </table>
  </div>

	<?php if(!empty($approve_list)) :?>
		<?php foreach($approve_list as $appr) : ?>
			<div class="col-sm-12 text-right">
				<?php if($appr->approve == 1) : ?>
					<span class="green">
						อนุมัติโดย : <?php echo $appr->approver; ?> @ <?php echo thai_date($appr->date_upd, TRUE); ?>
					</span>
				<?php endif; ?>
				<?php if($appr->approve == 0) : ?>
					<span class="red">
						ยกเลิกการอนุมัติโดย : <?php echo $appr->approver; ?> @ <?php echo thai_date($appr->date_upd, TRUE); ?>
					</span>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>

</div>



<script id="detail-template" type="text/x-handlebars-template">
<tr class="font-size-12 rox" id="row-{{id}}">
  <td class="middle text-center no">{{no}}</td>
  <td class="middle">{{ pdCode }}</td>
  <td class="middle">{{ pdName }}</td>
  <td class="middle text-center">{{ zoneName }}</td>
  <td class="middle text-center" id="qty-up-{{id}}">{{ up }}</td>
  <td class="middle text-center" id="qty-down-{{id}}">{{ down }}</td>
  <td class="middle text-center">
    {{#if valid}}
    <i class="fa fa-times red"></i>
    {{else}}
    <i class="fa fa-check green"></i>
    {{/if}}
  </td>
  <td class="middle text-right">
  <?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 0) : ?>
    <button type="button" class="btn btn-xs btn-danger" onclick="deleteDetail({{ id }}, '{{ pdCode }}')">
      <i class="fa fa-trash"></i>
    </button>
  <?php endif; ?>
  </td>
</tr>
</script>


<script src="<?php echo base_url(); ?>scripts/inventory/adjust_consignment/adjust_consignment.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust_consignment/adjust_consignment_add.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
