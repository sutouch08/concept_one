<?php $this->load->view('include/header'); ?>
<div class="row hidden-print">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
    <h3 class="title">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-lg-4 col-md-4 col-sm-4 padding-5">
		<p class="pull-right top-p">
			<button type="button" class="btn btn-sm btn-primary" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
		</p>
	</div>
</div><!-- End Row -->
<hr class="padding-5 hidden-print"/>
<form class="hidden-print" id="reportForm" method="post" action="<?php echo $this->home; ?>/do_export">
<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-3 padding-5">
    <label>วันที่เอกสาร</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" placeholder="เริ่มต้น" required />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" placeholder="สิ้นสุด" required/>
    </div>
  </div>


	<div class="col-lg-2 col-md-2 col-sm-3 padding-5">
    <label class="display-block">ประเภท</label>
    <div class="btn-group width-100">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-role-all" onclick="toggleAllRole(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-role-range" onclick="toggleAllRole(0)">เลือก</button>
    </div>
  </div>

	<input type="hidden" id="allRole" name="allRole" value="1">
	<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>">
</div>

<div class="modal fade" id="role-modal" tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal' style="width:300px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='title' id='modal_title'>ประเภทเอกสาร</h4>
            </div>
            <div class='modal-body' id='modal_body' style="padding:0px;">
							<div class="col-sm-12">
								<label>
									<label>
										<input type="checkbox" class="ace" onchange="checkAll($(this))" />
										<span class="lbl">    All</span>
									</label>
								</label>
							</div>

            <div class="col-sm-12">
							<label>
								<input type="checkbox" class="ace chk" id="role-s" name="role[WO]" value="WO" data-prefix="WO" style="margin-right:10px;" />
								<span class="lbl">   WO - ขาย</span>
							</label>
						</div>
						<div class="col-sm-12">
							<label>
								<input type="checkbox" class="ace chk" id="role-p" name="role[WS]" value="WS" data-prefix="WS" style="margin-right:10px;" />
								<span class="lbl">   WS - สปอนเซอร์</span>
							</label>
						</div>
						<div class="col-sm-12">
							<label>
								<input type="checkbox" class="ace chk" id="role-u" name="role[WU]" value="WU" data-prefix="WU" style="margin-right:10px;" />
								<span class="lbl">   WU - อภินันท์</span>
							</label>
						</div>
						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-c" name="role[WC]" value="WC" data-prefix="WC" style="margin-right:10px;" />
                <span class="lbl">   WC - ฝากขาย(เทียม)</span>
              </label>
						</div>
						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-n" name="role[WT]" value="WT" data-prefix="WT" style="margin-right:10px;" />
                <span class="lbl">   WT - ฝากขาย(แท้)</span>
              </label>
						</div>
						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-m" name="role[WM]" value="M" data-prefix="WM" style="margin-right:10px;" />
                <span class="lbl">   WM - ตัดยอดฝากขาย(แท้)</span>
              </label>
						</div>
						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-d" name="role[WD]" value="WD" data-prefix="WD" style="margin-right:10px;" />
                <span class="lbl">   WD - ตัดยอดฝากขาย(เทียม)</span>
              </label>
						</div>
						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-l" name="role[WL]" value="WL" data-prefix="WL" style="margin-right:10px;" />
                <span class="lbl">   WL - ยืมสินค้า</span>
              </label>
						</div>
						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-t" name="role[WQ]" value="WQ" data-prefix="WQ" style="margin-right:10px;" />
                <span class="lbl">   WQ - แปรสภาพ(ขาย)</span>
              </label>
						</div>
						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-q" name="role[WV]" value="WV" data-prefix="WV" style="margin-right:10px;" />
                <span class="lbl">   WV - แปรสภาพ(สต็อก)</span>
              </label>
            </div>

						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-wr" name="role[WR]" value="WR" data-prefix="WR" style="margin-right:10px;" />
                <span class="lbl">   WR - รับเข้าจากการซื้อ</span>
              </label>
            </div>

						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-ww" name="role[WW]" value="WW" data-prefix="WW" style="margin-right:10px;" />
                <span class="lbl">   WW - โอนย้ายสินค้า</span>
              </label>
            </div>

						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-mv" name="role[MV]" value="MV" data-prefix="MV" style="margin-right:10px;" />
                <span class="lbl">   MV - ย้ายพื้นที่จัดเก็บสินค้า</span>
              </label>
            </div>

						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-wg" name="role[WG]" value="WG" data-prefix="WG" style="margin-right:10px;" />
                <span class="lbl">   WG - ตัดยอดแปรสภาพ</span>
              </label>
            </div>

						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-rt" name="role[RT]" value="RT" data-prefix="RT" style="margin-right:10px;" />
                <span class="lbl">   RT - รับเข้าจากการแปรสภาพ</span>
              </label>
            </div>

						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-rn" name="role[RN]" value="RN" data-prefix="RN" style="margin-right:10px;" />
              <span class="lbl">     RN - รับคืนจากการยืม</span>
              </label>
            </div>

						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-sm" name="role[SM]" value="SM" data-prefix="SM" style="margin-right:10px;" />
                <span class="lbl">   SM - ลดหนี้ขาย</span>
              </label>
            </div>

						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-cn" name="role[CN]" value="CN" data-prefix="CN" style="margin-right:10px;" />
                <span class="lbl">   CN - ลดหนี้ฝากขาย(เทียม)</span>
              </label>
            </div>

						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-wa" name="role[WA]" value="WA" data-prefix="WA" style="margin-right:10px;" />
                <span class="lbl">   WA - ปรับปรุงสต็อก</span>
              </label>
            </div>

						<div class="col-sm-12">
							<label>
                <input type="checkbox" class="ace chk" id="role-ac" name="role[AC]" value="AC" data-prefix="AC" style="margin-right:10px;" />
                <span class="lbl">   AC - ปรับปรุงสต็อก ฝากขาย(เทียม)</span>
              </label>
            </div>


        		<div class="divider" ></div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default btn-block' data-dismiss='modal'>ตกลง</button>
            </div>
        </div>
    </div>
</div>

<hr>
</form>


<script>
	function checkAll(el) {
		if(el.is(':checked')) {
			$('.chk').prop('checked', true);
		}
		else {
			$('.chk').prop('checked', false);
		}
	}
</script>

<script src="<?php echo base_url(); ?>scripts/report/audit/document_running.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
