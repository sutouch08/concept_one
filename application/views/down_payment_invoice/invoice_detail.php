<?php $this->load->view('include/header'); ?>
<?php $this->load->view('down_payment_invoice/style'); ?>
<div class="row hidden-xs">
  <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 padding-5 text-right top-p">
    <button type="button" class="btn btn-white btn-warning btn-top" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
    <?php if($doc->status != 'D') : ?>
      <div class="btn-group">
        <button data-toggle="dropdown" class="btn btn-info btn-white dropdown-toggle margin-top-5" aria-expanded="false">
          <i class="ace-icon fa fa-list-ul icon-on-left"></i>
          ตัวเลือก
          <i class="ace-icon fa fa-angle-down icon-on-right"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
          <li class="success">
            <a href="javascript:sendToSap('<?php echo $doc->code; ?>')"><i class="fa fa-send"></i> Export to SAP</a>
          </li>
          <li class="primary">
            <a href="javascript:printDownPaymentInvoice('<?php echo $doc->code; ?>', 'RTI')"><i class="fa fa-print"></i> ใบเสร็จรับเงิน/ใบกำกับภาษี</a>
          </li>
          <li class="primary">
            <a href="javascript:printDownPaymentInvoice('<?php echo $doc->code; ?>', 'RTIN')"><i class="fa fa-print"></i> ใบเสร็จรับเงิน/ใบกำกับภาษี (ไม่แสดงวันที่)</a>
          </li>
        <?php if($this->pm->can_delete) : ?>
          <li class="danger">
            <a href="javascript:getCancel(<?php echo $doc->id; ?>, '<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> ยกเลิก</a>
          </li>
        <?php endif; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>
</div>
<hr class="hidden-xs"/>
<div class="row header-row hidden-xs">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf  padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf  padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo thai_date($doc->DocDate); ?>"  disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf  padding-5">
    <label>รหัสลูกค้า</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->CardCode; ?>" disabled />
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6  padding-5">
    <label>ชื่อลูกค้า</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->CardName; ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5">
		<label class="display-block not-show">isCompany</label>
		<label style="margin-top:0;">
			<input type="checkbox" class="ace" id="is-company" value="1" <?php echo is_checked('1', $doc->isCompany); ?> disabled />
			<span class="lbl margin-top-5">&nbsp;&nbsp;นิติบุคคล</span>
		</label>
	</div>
  <div class="col-lg-6 col-md-4-harf col-sm-5 padding-5">
		<label>ผู้ติดต่อ</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="customer-ref" value="<?php echo $doc->NumAtCard; ?>" disabled/>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2  padding-5">
		<label>เบอร์โทร</label>
		<input type="text" class="form-control input-sm h" maxlength="32" id="phone" value="<?php echo $doc->phone; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2  padding-5">
		<label>เลขที่ผู้เสียภาษี</label>
		<input type="text" class="form-control input-sm h" maxlength="13" id="tax-id" value="<?php echo $doc->tax_id; ?>" disabled/>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1 padding-5">
		<label>สาขา</label>
		<input type="text" class="form-control input-sm text-center h" maxlength="10" id="branch-code" value="<?php echo $doc->branch_code; ?>" disabled/>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2  padding-5">
		<label>ชื่อสาขา</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="branch-name" value="<?php echo $doc->branch_name; ?>" disabled/>
	</div>

	<div class="col-lg-4-harf col-md-4-harf col-sm-6 padding-5">
		<label>ที่อยู่</label>
		<input type="text" class="form-control input-sm h" maxlength="254"id="address" value="<?php echo $doc->address; ?>" disabled/>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-1-harf  padding-5">
		<label>ตำบล</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="sub-district" value="<?php echo $doc->sub_district; ?>" disabled/>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-1-harf  padding-5">
		<label>อำเภอ</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="district" value="<?php echo $doc->district; ?>" disabled/>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-1-harf  padding-5">
		<label>จังหวัด</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="province" value="<?php echo $doc->province; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf  padding-5">
		<label>รหัสไปรษณีย์</label>
		<input type="text" class="form-control input-sm text-center h" maxlength="12" id="postcode" value="<?php echo $doc->postcode; ?>" disabled/>
	</div>
  <div class="divider"></div>
  <div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>ใบรับมัดจำ</label>
    <div class="input-group">
			<input type="text" class="form-control input-sm text-center" id="baseDpm" value="<?php echo $doc->BaseDpm; ?>" disabled />
      <span class="input-group-btn">
        <button type="button" class="btn btn-xs btn-info"
        onclick="viewDpm('<?php echo $doc->BaseDpm; ?>')"
        <?php echo (empty($doc->BaseDpm) ? 'disabled' :''); ?>>		<i class="fa fa-external-link"></i></button>
      </span>
		</div>
  </div>
  <div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>ใบสั่งขาย</label>
		<div class="input-group">
			<input type="text" class="form-control input-sm text-center" id="baseRef" value="<?php echo $doc->BaseRef; ?>" disabled />
			<?php if($doc->BaseType == 'WO') : ?>
				<span class="input-group-btn">
					<button type="button" class="btn btn-xs btn-info"
					onclick="viewWo('<?php echo $doc->BaseRef; ?>')"
					<?php echo (empty($doc->BaseRef) ? 'disabled' :''); ?>>		<i class="fa fa-external-link"></i></button>
				</span>
			<?php else : ?>
				<span class="input-group-btn">
					<button type="button" class="btn btn-xs btn-info"
					onclick="viewSo('<?php echo $doc->BaseRef; ?>')"
					<?php echo (empty($doc->BaseRef) ? 'disabled' :''); ?>>		<i class="fa fa-external-link"></i></button>
				</span>
			<?php endif; ?>
		</div>
	</div>
  <div class="col-lg-5-harf col-md-4 col-sm-4 hidden-xs">&nbsp;</div>
	<div class="divider-hidden visible-xs"></div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>SAP No.</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->DocNum; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>สถานะ</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo ($doc->status == 'D' ? 'Cancel' : ($doc->status == 'C' ? 'Closed' : 'Open')); ?>" disabled />
	</div>

  <input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
</div>
<hr class="margin-top-15 margin-bottom-15 hidden-xs">
<?php if($doc->status == 'D') : ?>
  <?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>

<div class="row hidden-xs">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1 table-responsive" style="height:200px; overflow:auto; border-top:solid 1px #ccc;">
    <table class="table table-bordered tableFixHead" style="min-width:660px; margin-bottom:20px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center fix-header">#</th>
          <th class="fix-width-100 fix-header">รหัสสินค้า</th>
          <th class="min-width-200 fix-header">รายละเอียด</th>
          <th class="fix-width-100 text-right fix-header">จำนวน</th>
          <th class="fix-width-100 text-right fix-header">ราคา/หน่วย</th>
          <th class="fix-width-120 text-right fix-header">มูลค่า</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; ?>
        <?php if( ! empty($details)) : ?>
          <?php foreach($details as $rs) : ?>
            <tr>
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle"><?php echo $rs->ItemCode; ?></td>
              <td class="middle"><?php echo $rs->Dscription; ?></td>
              <td class="middle text-right"><?php echo number($rs->Qty, 2); ?></td>
              <td class="middle text-right"><?php echo number($rs->PriceAfVAT, 2); ?></td>
              <td class="middle text-right"><?php echo number($rs->PriceAfVAT, 2); ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div><!-- row -->


<div class="row hidden-xs">
	<div class="divider-hidden"></div>
	<div class="col-lg-7 col-md-7 col-sm-7 padding-5">
		<div class="form-horizontal">
			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-2 col-md-3 col-sm-3 control-label no-padding-right">พนักงานขาย</label>
				<div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
					<select class="form-control edit" id="sale-id" name="sale_id" disabled>
						<?php echo select_saleman($doc->SlpCode); ?>
					</select>
				</div>
			</div>

			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-2 col-md-3 col-sm-3 control-label no-padding-right">Owner</label>
				<div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
					<input type="text" class="form-control input-sm" id="owner" value="<?php echo $doc->user; ?>" disabled>
				</div>
			</div>

			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Remark</label>
				<div class="col-lg-10 col-md-9 col-sm-9 col-xs-12">
					<textarea id="remark" maxlength="254" rows="3" class="form-control" disabled><?php echo $doc->Comments; ?></textarea>
				</div>
			</div>
		</div>
	</div>

  <div class="col-lg-5 col-md-5 col-sm-5 padding-5">
		<div class="form-horizontal" >
			<div class="form-group" >
				<label class="col-lg-8 col-md-8 col-sm-6 control-label no-padding-right">มูลค่าก่อนภาษี</label>
				<div class="col-lg-4 col-md-4 col-sm-6  padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="total-amount" value="<?php echo number($doc->DocTotal - $doc->VatSum, 2); ?>" disabled>
				</div>
			</div>
			<div class="form-group" id="bill-vat" >
        <label class="col-lg-8 col-md-8 col-sm-6 control-label no-padding-right">VAT</label>
				<div class="col-lg-4 col-md-4 col-sm-6  padding-5 last">
					<input type="text" id="vat-total" class="form-control input-sm text-right" value="<?php echo number($doc->VatSum, 2); ?>" disabled />
				</div>
			</div>

			<div class="form-group" >
				<label class="col-lg-8 col-md-8 col-sm-6 control-label no-padding-right">รวมทั้งสิ้น</label>
				<div class="col-lg-4 col-md-4 col-sm-6  padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="doc-total" value="<?php echo number($doc->DocTotal, 2); ?>" disabled>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if($doc->status == 'D') : ?>
  <div class="row hidden-xs">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 red">
      ยกเลิกโดย : <?php echo $doc->cancel_user; ?> @<?php echo thai_date($doc->cancel_date, TRUE, '/'); ?><br/>
      หมายเหตุ : <?php echo $doc->cancel_reason; ?>
    </div>
  </div>
<?php endif; ?>

<?php $this->load->view('down_payment_invoice/cancel_modal'); ?>

<div class="row visible-xs">
	<div class="col-xs-12"><h1 class="text-center">Not supporrt</h1></div>
</div>

<script src="<?php echo base_url(); ?>scripts/down_payment_invoice/down_payment_invoice.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_down_payment_invoice.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
