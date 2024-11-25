<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-sm-6 padding-5">
    <h3 class="title">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
    </div>
		<div class="col-sm-6 padding-5">
			<p class="pull-right top-p">

			</p>
		</div>
</div><!-- End Row -->
<hr class="padding-5 hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
	<div class="col-sm-2 col-2-harf padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" placeholder="เริ่มต้น" required />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" placeholder="สิ้นสุด" required/>
    </div>
  </div>
  <div class="col-sm-1 col-1-harf padding-5">
    <label class="display-block">เอกสาร</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-doc-all" onclick="toggleAllDocument(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-doc-range" onclick="toggleAllDocument(0)">เลือก</button>
    </div>
  </div>
  <div class="col-sm-1 col-1-harf padding-5">
    <label class="display-block not-show">เริ่มต้น</label>
    <input type="text" class="form-control input-sm text-center" id="docFrom" name="docFrom" placeholder="เริ่มต้น" disabled>
  </div>
  <div class="col-sm-1 col-1-harf padding-5">
    <label class="display-block not-show">สิ้นสุด</label>
    <input type="text" class="form-control input-sm text-center" id="docTo" name="docTo" placeholder="สิ้นสุด" disabled>
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label class="display-block">ประเภท</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-role-all" onclick="toggleAllRole(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-role-range" onclick="toggleAllRole(0)">เลือก</button>
    </div>
  </div>

	<div class="col-sm-1 col-1-harf padding-5">
    <label class="display-block">สถานะ(IX)</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-state-all" onclick="toggleAllState(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-state-range" onclick="toggleAllState(0)">เลือก</button>
    </div>
  </div>

	<div class="col-sm-1 padding-5">
		<label class="display-block not-show">report</label>
		<button type="button" class="btn btn-xs btn-success btn-block" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
	</div>

	<div class="col-sm-1 padding-5">
		<label class="display-block not-show">export</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
	</div>


  <input type="hidden" id="allDoc" name="allDoc" value="1">
	<input type="hidden" id="allRole" name="allRole" value="1">
	<input type="hidden" id="allState" name="allState" value="1">
	<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>">
</div>

<div class="modal fade" id="role-modal" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal' style="width:300px;">
        <div class='modal-content'>
            <div class='modal-header' style="border-bottom:solid 1px #e5e5e5;">
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='title' id='modal_title'>เลือกประเภทเอกสาร</h4>
            </div>
            <div class='modal-body'>
            	<div class="row">
								<div class="col-sm-12">
		              <label>
		                <input type="checkbox" class="chk" id="role-rt" name="role[]" value="RT" data-prefix="RT" style="margin-right:10px;" />
		                RT - รับเข้าจากการแปรสภาพ
		              </label>
								</div>
								<div class="col-sm-12">
									<label>
		                <input type="checkbox" class="chk" id="role-rn" name="role[]" value="RN" data-prefix="RN" style="margin-right:10px;" />
		                RN - รับคืนจากการยืม
		              </label>
								</div>
								<div class="col-sm-12">
									<label>
		                <input type="checkbox" class="chk" id="role-sm" name="role[]" value="SM" data-prefix="SM" style="margin-right:10px;" />
		                SM - ลดหนี้ขาย
		              </label>
								</div>
								<div class="col-sm-12">
									<label>
		                <input type="checkbox" class="chk" id="role-wr" name="role[]" value="WR" data-prefix="WR" style="margin-right:10px;" />
		                WR - รับเข้าจากใบสั่งซื้อ
		              </label>
								</div>
								<div class="col-sm-12">
									<label>
		                <input type="checkbox" class="chk" id="role-wx" name="role[]" value="WX" data-prefix="WX" style="margin-right:10px;" />
		                WX - กระทบยอดสินค้า
		              </label>
								</div>
								<div class="col-sm-12">
									<label>
		                <input type="checkbox" class="chk" id="role-ww" name="role[]" value="WW" data-prefix="WW" style="margin-right:10px;" />
		                WW - โอนสินค้าระหว่างคลัง
		              </label>
								</div>
            	</div>
        		<div class="divider" ></div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-block' data-dismiss='modal'>ตกลง</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="state-modal" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal' style="width:200px;">
        <div class='modal-content'>
            <div class='modal-header' style="border-bottom:solid 1px #e5e5e5;">
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='title'>สถานะเอกสาร</h4>
            </div>
            <div class='modal-body'>
							<div class="row">
								<div class="col-sm-12">
		              <label><input type="checkbox" class="state" name="state[]" value="0" style="margin-right:10px;" />ยังไม่บันทึก</label>
								</div>
								<div class="col-sm-12">
		              <label><input type="checkbox" class="state" name="state[]" value="3" style="margin-right:10px;" />รอรับสินค้า</label>
								</div>
								<div class="col-sm-12">
		              <label><input type="checkbox" class="state" name="state[]" value="1" style="margin-right:10px;" />รับเข้าแล้ว</label>
								</div>
								<div class="col-sm-12">
		              <label><input type="checkbox" class="state" name="state[]" value="2" style="margin-right:10px;" />ยกเลิก</label>
								</div>
							</div>

	        		<div class="divider" ></div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-sm btn-block' data-dismiss='modal'>ตกลง</button>
            </div>
        </div>
    </div>
</div>
<hr>
</form>

<div class="row">
	<div class="col-sm-12 padding-5">
		<table class="table table-striped border-1">
			<thead>
		    <tr class="font-size-12">
		      <th class="width-5 middle text-center">ลำดับ</th>
		      <th class="width-10 middle text-center">วันที่(IX)</th>
		      <th class="width-10 middle text-center">IX</th>
					<th class="width-5 middle text-center">Type(IX)</th>
					<th class="width-15 middle text-center">WMS</th>
					<th class="width-5 middle text-center">Type(WMS)</th>
					<th class="width-10 middle text-center">SAP</th>
					<th class="width-5 middle text-center">Type(SAP)</th>
					<th class="middle text-center">สถานะ(IX)</th>
		    </tr>
			</thead>
			<tbody id="rs"></tbody>
		</table>
  </div>
</div>




<script id="template" type="text/x-handlebars-template">
{{#each this}}
  {{#if nodata}}
    <tr>
      <td colspan="10" align="center"><h4>-----  ไม่พบเอกสารตามเงื่อนไขที่กำหนด  -----</h4></td>
    </tr>
  {{else}}
		<tr class="font-size-12">
			<td class="middle text-center">{{no}}</td>
			<td class="middle text-center">{{ date }}</td>
			<td class="middle text-center">{{ ix_code }}</td>
			<td class="middle text-center">{{ ix_type }}</td>
			<td class="middle text-center">{{ wms_code }}</td>
			<td class="middle text-center">{{ wms_type }}</td>
			<td class="middle text-center">{{ sap_code }}</td>
			<td class="middle text-center">{{ sap_type }}</td>
			<td class="middle text-center">{{ ix_state }}</td>
		</tr>
  {{/if}}
{{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/report/audit/inbound_document_audit.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
