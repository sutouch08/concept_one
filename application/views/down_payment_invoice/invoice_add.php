<?php $this->load->view('include/header'); ?>
<?php $this->load->view('down_payment_invoice/style'); ?>
<div class="row hidden-xs">
	<div class="col-lg-6 col-md-6 col-sm-6 hidden-xs padding-5">
		<h4 class="title">
			<?php echo $this->title; ?>
		</h4>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<p class="pull-right top-p">
      <button type="button" class="btn btn-white btn-warning btn-top" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
    <?php if($this->pm->can_add) : ?>
      <button type="button" class="btn btn-white btn-success btn-top" onclick="showCustomerModal()">ข้อมูลลูกค้า</button>
    <?php endif; ?>
		</p>
	</div>
</div><!-- End Row -->
<hr class="hidden-xs"/>
<div class="row header-row hidden-xs">
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5">
		<label>เลขที่</label>
		<input type="text" class="form-control input-sm text-center" disabled />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf  padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center h" name="date" id="date" style="position:relative; z-index:10;" value="<?php echo date('d-m-Y'); ?>" required readonly />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf  padding-5">
		<label>รหัสลูกค้า</label>
		<input type="text" class="form-control input-sm text-center h" maxlength="15" id="customer-code" value="" />
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6  padding-5">
		<label>ชื่อลูกค้า</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="customer-name" value="" />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5">
		<label class="display-block not-show">isCompany</label>
		<label style="margin-top:0;">
			<input type="checkbox" class="ace" id="is-company" value="1" onchange="toggleBranch()" />
			<span class="lbl margin-top-5">&nbsp;&nbsp;นิติบุคคล</span>
		</label>
	</div>
	<div class="col-lg-6 col-md-4-harf col-sm-5 padding-5">
		<label>ผู้ติดต่อ</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="customer-ref" value="" />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2  padding-5">
		<label>เบอร์โทร</label>
		<input type="text" class="form-control input-sm h" maxlength="32" id="phone" value="" />
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2  padding-5">
		<label>เลขที่ผู้เสียภาษี</label>
		<input type="text" class="form-control input-sm h" maxlength="13" id="tax-id" value="" />
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1 padding-5">
		<label>สาขา</label>
		<input type="text" class="form-control input-sm text-center h" maxlength="10" id="branch-code" value="" />
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2  padding-5">
		<label>ชื่อสาขา</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="branch-name" value="" />
	</div>

	<div class="col-lg-4-harf col-md-4-harf col-sm-6 padding-5">
		<label>ที่อยู่</label>
		<input type="text" class="form-control input-sm h" maxlength="254"id="address" value="" />
	</div>

	<div class="col-lg-2 col-md-2 col-sm-1-harf  padding-5">
		<label>ตำบล</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="sub-district" value="" />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-1-harf  padding-5">
		<label>อำเภอ</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="district" value="" />
	</div>
	<div class="col-lg-2 col-md-2 col-sm-1-harf  padding-5">
		<label>จังหวัด</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="province" value="" />
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf  padding-5">
		<label>รหัสไปรษณีย์</label>
		<input type="text" class="form-control input-sm text-center h" maxlength="12" id="postcode" value="" />
	</div>

	<div class="divider"></div>

	<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>ใบรับมัดจำ</label>
    <div class="input-group">
			<input type="text" class="form-control input-sm text-center" id="bill-code" value="" placeholder="ค้นใบรับมัดจำ" autofocus/>
      <span class="input-group-btn">
        <button type="button" class="btn btn-xs btn-primary"  onclick="getDpm()">Submit</button>
      </span>
		</div>
		<input type="hidden" id="billCode" value="" />
  </div>
</div><!-- row header-row-->

<hr class="padding-5 margin-top-15 margin-bottom-15 hidden-xs"/>

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
      <tbody id="result">

      </tbody>
    </table>
  </div>
</div><!-- row -->

<?php $default_sale_id = getConfig('DEFAULT_SALES_ID'); ?>
<div class="row hidden-xs">
	<div class="divider-hidden"></div>
	<div class="col-lg-7 col-md-7 col-sm-7 padding-5">
		<div class="form-horizontal">
			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-2 col-md-3 col-sm-3 control-label no-padding-right">พนักงานขาย</label>
				<div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
					<select class="form-control edit" id="sale-id" name="sale_id">
						<?php echo select_saleman($default_sale_id); ?>
					</select>
				</div>
			</div>

			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-2 col-md-3 col-sm-3 control-label no-padding-right">Owner</label>
				<div class="col-lg-6 col-md-9 col-sm-9 col-xs-12">
					<input type="text" class="form-control input-sm" id="owner" value="">
				</div>
			</div>

			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-2 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Remark</label>
				<div class="col-lg-10 col-md-9 col-sm-9 col-xs-12">
					<textarea id="remark" maxlength="254" rows="3" class="form-control"></textarea>
				</div>
			</div>
		</div>
	</div>

  <div class="col-lg-5 col-md-5 col-sm-5 padding-5">
		<div class="form-horizontal" >
			<div class="form-group" >
				<label class="col-lg-8 col-md-8 col-sm-6 control-label no-padding-right">มูลค่าก่อนภาษี</label>
				<div class="col-lg-4 col-md-4 col-sm-6  padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="total-amount" value="0.00" disabled>
				</div>
			</div>
			<div class="form-group" id="bill-vat" >
        <label class="col-lg-8 col-md-8 col-sm-6 control-label no-padding-right">VAT</label>
				<div class="col-lg-4 col-md-4 col-sm-6  padding-5 last">
					<input type="text" id="vat-total" class="form-control input-sm text-right" value="0.00" disabled />
				</div>
			</div>

			<div class="form-group" >
				<label class="col-lg-8 col-md-8 col-sm-6 control-label no-padding-right">รวมทั้งสิ้น</label>
				<div class="col-lg-4 col-md-4 col-sm-6  padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="doc-total" value="0.00" disabled>
				</div>
			</div>
		</div>
	</div>

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning btn-100" onclick="leave()">Cancel</button>
		<button type="button" class="btn btn-sm btn-primary btn-100" id="btn-save" onclick="createInvoice()">Save</button>
	</div>
</div>

<input type="hidden" id="default_sale_id" value="<?php echo $default_sale_id; ?>" />

<?php $this->load->view('down_payment_invoice/customer_modal'); ?>

<script id="template" type="text/x-handlebarsTemplate">
	<tr>
		<td class="text-center no">{{no}}</td>
		<td class="">{{ItemCode}}</td>
		<td class="">{{Dscription}}</td>
		<td class="text-right">{{Qty}}</td>
		<td class="text-right">{{Price}}</td>
		<td class="text-right">{{Amount}}</td>
	</tr>
</script>

<div class="row visible-xs">
	<div class="col-xs-12"><h1 class="text-center">Not support mobile</h1></div>
</div>

<script src="<?php echo base_url(); ?>scripts/down_payment_invoice/down_payment_invoice.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/down_payment_invoice/down_payment_invoice_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_down_payment_invoice.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
