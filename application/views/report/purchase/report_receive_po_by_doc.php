<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-lg-8 col-md-8 col-sm-8 padding-5 hidden-xs">
    <h3 class="title"><i class="fa fa-bar-chart"></i>  <?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
    <h4 class="title-xs"><i class="fa fa-bar-chart"></i>  <?php echo $this->title; ?></h4>
  </div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5">
		<p class="pull-right top-p">
      <button type="button" class="btn btn-xs btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
			<button type="button" class="btn btn-xs btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
    <label class="display-block">เอกสาร</label>
    <div class="btn-group btn-group-30 width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-doc-all" onclick="toggleAllDocument(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-doc-range" onclick="toggleAllDocument(0)">เลือก</button>
    </div>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
    <label class="display-block not-show">เริ่มต้น</label>
    <input type="text" class="form-control input-sm text-center" id="docFrom" name="docFrom" placeholder="เริ่มต้น" disabled>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
    <label class="display-block not-show">สิ้นสุด</label>
    <input type="text" class="form-control input-sm text-center" id="docTo" name="docTo" placeholder="สิ้นสุด" disabled>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
    <label class="display-block">ผู้ขาย</label>
    <div class="btn-group btn-group-30 width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-vendor-all" onclick="toggleAllVendor(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-vendor-range" onclick="toggleAllVendor(0)">เลือก</button>
    </div>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
    <label class="display-block not-show">เริ่มต้น</label>
    <input type="text" class="form-control input-sm text-center" id="vendorFrom" name="vendorFrom" placeholder="เริ่มต้น" disabled>
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
    <label class="display-block not-show">สิ้นสุด</label>
    <input type="text" class="form-control input-sm text-center" id="vendorTo" name="vendorTo" placeholder="สิ้นสุด" disabled>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4  padding-5">
    <label class="display-block">ใบสั่งซื้อ</label>
    <div class="btn-group btn-group-30 width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-po-all" onclick="toggleAllPO(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-po-range" onclick="toggleAllPO(0)">เลือก</button>
    </div>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">เริ่มต้น</label>
    <input type="text" class="form-control input-sm text-center" id="poFrom" name="poFrom" placeholder="เริ่มต้น" disabled>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">สิ้นสุด</label>
    <input type="text" class="form-control input-sm text-center" id="poTo" name="poTo" placeholder="สิ้นสุด" disabled>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
    <label class="display-block">ใบส่งสินค้า</label>
    <div class="btn-group btn-group-30 width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-invoice-all" onclick="toggleAllInvoice(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-invoice-range" onclick="toggleAllInvoice(0)">เลือก</button>
    </div>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">เริ่มต้น</label>
    <input type="text" class="form-control input-sm text-center" id="invoiceFrom" name="invoiceFrom" placeholder="เริ่มต้น" disabled>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">สิ้นสุด</label>
    <input type="text" class="form-control input-sm text-center" id="invoiceTo" name="invoiceTo" placeholder="สิ้นสุด" disabled>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" placeholder="เริ่มต้น" required />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" placeholder="สิ้นสุด" required/>
    </div>
  </div>

  <input type="hidden" id="allDoc" name="allDoc" value="1">
	<input type="hidden" id="allVendor" name="allVendor" value="1">
	<input type="hidden" id="allPO" name="allPO" value="1">
	<input type="hidden" id="allInvoice" name="allVoice" value="1">
	<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>">
</div>
<hr>
</form>

<div class="row">
	<div class="col-sm-12" id="rs">

    </div>
</div>




<script id="template" type="text/x-handlebars-template">
  <table class="table table-bordered table-striped">
    <tr class="font-size-12">
      <th class="width-5 middle text-center">ลำดับ</th>
      <th class="width-8 middle text-center">วันที่</th>
      <th class="width-10 middle text-center">เลขที่เอกสาร</th>
      <th class="width-10 middle text-center">ใบสั่งซื้อ</th>
			<th class="width-15 middle text-center">ใบส่งของ</th>
			<th class="width-8 middle text-center">SAP No.</th>
			<th class="middle text-center">ผู้ขาย</th>
      <th class="width-8 middle text-right">จำนวน</th>
      <th class="width-15 text-right middle">มูลค่า</th>
    </tr>
{{#each this}}
  {{#if nodata}}
    <tr>
      <td colspan="9" align="center"><h4>-----  ไม่พบเอกสารตามเงื่อนไขที่กำหนด  -----</h4></td>
    </tr>
  {{else}}
    {{#if @last}}
    <tr class="font-size-14">
      <td colspan="7" class="text-right">รวม</td>
      <td class="text-right">{{ totalQty }}</td>
      <td class="text-right">{{ totalAmount }}</td>
    </tr>
    {{else}}
    <tr class="font-size-12">
      <td class="middle text-center">{{no}}</td>
      <td class="middle text-center">{{ date }}</td>
      <td class="middle">{{ code }}</td>
			<td class="middle">{{ po }}</td>
      <td class="middle">{{ invoice }}</td>
			<td class="middle">{{ sapNo }}</td>
			<td class="middle"><input type="text" class="print-row" value="{{ vendor }}"></td>
      <td class="middle text-right">{{ qty }}</td>
      <td class="middle text-right">{{ amount }}</td>
    </tr>
    {{/if}}
  {{/if}}
{{/each}}
  </table>
</script>

<script src="<?php echo base_url(); ?>scripts/report/purchase/receive_po_by_doc.js"></script>
<?php $this->load->view('include/footer'); ?>
