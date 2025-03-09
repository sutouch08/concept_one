<?php $this->load->view('include/header'); ?>
<?php $this->load->view('order_invoice/style'); ?>
<div class="row hidden-xs">
  <div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-xs-12 padding-5 visible-xs">
    <h4 class="title-xs"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right top-p">
    <button type="button" class="btn btn-white btn-warning btn-top" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
    <?php if($order->status != 'D') : ?>
      <?php if($this->pm->can_delete) : ?>
        <button type="button" class="btn btn-white btn-danger btn-top" onclick="getCancel(<?php echo $order->id; ?>, '<?php echo $order->code; ?>')"><i class="fa fa-times"></i> ยกเลิก</button>
      <?php endif; ?>
      <div class="btn-group">
        <button data-toggle="dropdown" class="btn btn-success btn-white dropdown-toggle margin-top-5" aria-expanded="false">
          <i class="ace-icon fa fa-send icon-on-left"></i>
          Export
          <i class="ace-icon fa fa-angle-down icon-on-right"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
          <li>
            <a href="javascript:sendToSap('<?php echo $order->code; ?>')"><i class="fa fa-print"></i> Export Invoice</a>
          </li>
          <?php if($order->BaseType == 'POS') : ?>
            <li>
              <a href="javascript:exportIncomming('<?php echo $order->BaseRef; ?>', '<?php echo $order->BaseType; ?>')"><i class="fa fa-print"></i> Export Incomming</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="btn-group">
        <button data-toggle="dropdown" class="btn btn-info btn-white dropdown-toggle margin-top-5" aria-expanded="false">
          <i class="ace-icon fa fa-print icon-on-left"></i>
          พิมพ์
          <i class="ace-icon fa fa-angle-down icon-on-right"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
          <?php if($order->TaxStatus == 'Y') : ?>
            <li>
              <a href="javascript:printInvoice('<?php echo $order->code; ?>', 'DTI')"><i class="fa fa-print"></i> ใบส่งสินค้า/ใบกำกับภาษี</a>
            </li>
            <li>
              <a href="javascript:printInvoice('<?php echo $order->code; ?>', 'DTIN')"><i class="fa fa-print"></i> ใบส่งสินค้า/ใบกำกับภาษี (ไม่แสดงวันที่)</a>
            </li>
            <?php if($order->is_term == 1) : ?>
              <li>
                <a href="javascript:printInvoice('<?php echo $order->code; ?>', 'DITI')"><i class="fa fa-print"></i> ใบส่งสินค้า/ใบแจ้งหนี้/ใบกำกับภาษี</a>
              </li>
              <li>
                <a href="javascript:printInvoice('<?php echo $order->code; ?>', 'DITIN')"><i class="fa fa-print"></i> ใบส่งสินค้า/ใบแจ้งหนี้/ใบกำกับภาษี (ไม่แสดงวันที่)</a>
              </li>
            <?php endif; ?>
            <?php if($order->is_term == 0) : ?>
              <li>
                <a href="javascript:printInvoice('<?php echo $order->code; ?>', 'DRTI')"><i class="fa fa-print"></i> ใบส่งสินค้า/ใบเสร็จรับเงิน/ใบกำกับภาษี</a>
              </li>
              <li>
                <a href="javascript:printInvoice('<?php echo $order->code; ?>', 'DRTIN')"><i class="fa fa-print"></i> ใบส่งสินค้า/ใบเสร็จรับเงิน/ใบกำกับภาษี (ไม่แสดงวันที่)</a>
              </li>
            <?php endif; ?>
          <?php else : ?>
            <li>
              <a href="javascript:printInvoice('<?php echo $order->code; ?>', 'DO')"><i class="fa fa-print"></i> ใบส่งสินค้า</a>
            </li>
            <?php if($order->is_term == 1) : ?>
              <li>
                <a href="javascript:printInvoice('<?php echo $order->code; ?>', 'DIO')"><i class="fa fa-print"></i> ใบส่งสินค้า/ใบแจ้งหนี้</a>
              </li>
            <?php endif; ?>
            <?php if($order->is_term == 0) : ?>
              <li>
                <a href="javascript:printInvoice('<?php echo $order->code; ?>', 'DRO')"><i class="fa fa-print"></i> ใบส่งสินค้า/ใบเสร็จรับเงิน</a>
              </li>
            <?php endif; ?>
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
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
		<label>เล่มเอกสาร</label>
		<select class="form-control input-sm h" id="is-term" disabled>
			<option value="">เลือก</option>
			<option value="0" <?php echo is_selected('0', $order->is_term); ?>>ขายสด</option>
			<option value="1" <?php echo is_selected('1', $order->is_term); ?>>ขายเชื่อ</option>
		</select>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
		<label>ชนิด VAT</label>
		<select class="form-control input-sm h" id="vat-type" onchange="toggleVatType()" disabled>
			<option value="">เลือก</option>
			<option value="E" <?php echo is_selected('E', $order->vat_type); ?>>แยกนอก</option>
			<option value="I" <?php echo is_selected('I', $order->vat_type); ?>>รวมใน</option>
			<option value="N" <?php echo is_selected('N', $order->vat_type); ?>>ไม่ VAT</option>
		</select>
		<input type="hidden" id="tax-status" value="<?php echo $order->TaxStatus; ?>">
	</div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf  padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo thai_date($order->DocDate); ?>"  disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf  padding-5">
    <label>รหัสลูกค้า</label>
    <input type="text" class="form-control input-sm" value="<?php echo $order->CardCode; ?>" disabled />
  </div>
  <div class="col-lg-4-harf col-md-4-harf col-sm-4-harf  padding-5">
    <label>ชื่อลูกค้า</label>
    <input type="text" class="form-control input-sm" value="<?php echo $order->CardName; ?>" disabled />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5">
		<label class="display-block not-show">isCompany</label>
		<label style="margin-top:0;">
			<input type="checkbox" class="ace" id="is-company" value="1" <?php echo is_checked('1', $order->isCompany); ?> disabled />
			<span class="lbl margin-top-5">&nbsp;&nbsp;นิติบุคคล</span>
		</label>
	</div>
  <div class="col-lg-6 col-md-4-harf col-sm-3-harf padding-5">
		<label>ผู้ติดต่อ</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="customer-ref" value="<?php echo $order->NumAtCard; ?>" disabled/>
	</div>
	<div class="col-lg-2 col-md-1-harf col-sm-2  padding-5">
		<label>เบอร์โทร</label>
		<input type="text" class="form-control input-sm h" maxlength="32" id="phone" value="<?php echo $order->phone; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2  padding-5">
		<label>เลขที่ผู้เสียภาษี</label>
		<input type="text" class="form-control input-sm h" maxlength="13" id="tax-id" value="<?php echo $order->tax_id; ?>" disabled/>
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1 padding-5">
		<label>สาขา</label>
		<input type="text" class="form-control input-sm text-center h" maxlength="10" id="branch-code" value="<?php echo $order->branch_code; ?>" disabled/>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2  padding-5">
		<label>ชื่อสาขา</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="branch-name" value="<?php echo $order->branch_name; ?>" disabled/>
	</div>

	<div class="col-lg-4-harf col-md-4-harf col-sm-6-harf padding-5">
		<label>ที่อยู่</label>
		<input type="text" class="form-control input-sm h" maxlength="254"id="address" value="<?php echo $order->address; ?>" disabled/>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-1-harf  padding-5">
		<label>ตำบล</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="sub-district" value="<?php echo $order->sub_district; ?>" disabled/>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-1-harf  padding-5">
		<label>อำเภอ</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="district" value="<?php echo $order->district; ?>" disabled/>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-1-harf  padding-5">
		<label>จังหวัด</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="province" value="<?php echo $order->province; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1  padding-5">
		<label>รหัสไปรษณีย์</label>
		<input type="text" class="form-control input-sm text-center h" maxlength="12" id="postcode" value="<?php echo $order->postcode; ?>" disabled/>
	</div>
  <div class="divider"></div>
  <div class="col-lg-1-harf col-md-3 col-sm-3  padding-5">
    <label>สร้างจาก</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->BaseRef; ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-3 col-sm-3  padding-5">
    <label>ใบสั่งขาย</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->so_code; ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-3 col-sm-3  padding-5">
    <label>บิลขาย</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->bill_code; ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-3 col-sm-3  padding-5">
    <label>ออเดอร์</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->order_code; ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2  padding-5">
    <label>SAP No.</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->DocNum; ?>" readonly />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2  padding-5">
    <label>Incomming No.</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->IncommingNo; ?>" readonly />
  </div>
  <div class="col-lg-1 col-md-2 col-sm-2  padding-5">
    <label>Incomming</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->incomming_exported; ?>" readonly />
  </div>
  <div class="col-lg-1 col-md-2 col-sm-2  padding-5">
    <label>Invoice</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->isExported; ?>" readonly />
  </div>
  <div class="col-lg-1 col-md-2 col-sm-2  padding-5">
    <label>สถานะ</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo bill_status_label($order->status); ?>" readonly />
  </div>

  <input type="hidden" id="code" value="<?php echo $order->code; ?>" />
</div>
<hr class="margin-top-15 margin-bottom-15 hidden-xs">
<?php if($order->status == 'D') : ?>
  <?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>


<div class="row hidden-xs">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 margin-bottom-15">
    <div class="tabable">
      <ul class="nav nav-tabs" role="tablist">
        <li class="active">
          <a href="#items-list" id="item-tab" aria-expanded="true" aria-controls="items-list" role="tab" data-toggle="tab">รายการสินค้า</a>
        </li>
        <li>
          <a href="#down-payment" id="down-tab" aria-expanded="false" aria-controls="down-payment" role="tab" data-toggle="tab">เงินมัดจำ</a>
        </li>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content" style="margin:0px; padding:0px; border:none;">
        <div role="tabpanel" class="tab-pane active" id="items-list">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1 table-responsive"
          style="height:300px; overflow:auto; border-top:solid 1px #ccc;">
          <table class="table table-bordered tableFixHead" style="min-width:1000px; margin-bottom:20px;">
            <thead>
              <tr>
                <th class="fix-width-40 text-center fix-header">#</th>
                <th class="fix-width-200 fix-header">รหัสสินค้า</th>
                <th class="min-width-200 fix-header">รายละเอียด</th>
                <th class="fix-width-100 text-right fix-header">จำนวน</th>
                <th class="fix-width-100 text-right fix-header">ราคา/หน่วย</th>
                <th class="fix-width-100 text-right fix-header">ส่วนลด(%)</th>
                <th class="fix-width-120 text-right fix-header">มูลค่า</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; ?>
              <?php $total_qty = 0; ?>
              <?php $total_amount = 0; ?>
              <?php if( ! empty($details)) : ?>
                <?php foreach($details as $rs) : ?>
                  <?php $price = $rs->VatType == 'E' ? $rs->PriceBefDi : add_vat($rs->PriceBefDi, $rs->VatRate, 'E'); ?>
                  <?php $lineTotal = $rs->VatType == 'E' ? $rs->LineTotal : add_vat($rs->LineTotal, $rs->VatRate, 'E'); ?>
                  <tr>
                    <td class="middle text-center no"><?php echo $no; ?></td>
                    <td class="middle"><?php echo $rs->ItemCode; ?></td>
                    <td class="middle"><?php echo $rs->Dscription; ?></td>
                    <td class="middle text-right"><?php echo number($rs->Qty, 2); ?></td>
                    <td class="middle text-right"><?php echo number($price, 2); ?></td>
                    <td class="middle text-right"><?php echo (empty($rs->DiscPrcnt) ? "" : number($rs->DiscPrcnt, 2)." %"); ?></td>
                    <td class="middle text-right"><?php echo number($lineTotal, 2); ?></td>
                  </tr>
                  <?php $no++; ?>
                  <?php $total_qty += $rs->Qty; ?>
                  <?php $total_amount += $lineTotal; ?>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

        <div role="tabpanel" class="tab-pane fade" id="down-payment">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1 table-responsive"
            style="height:300px; overflow:auto; border-top:solid 1px #ccc;">
            <table class="table table-bordered tableFixHead" style="margin-bottom:20px;">
              <thead>
                <tr>
                  <th class="fix-width-40 text-center fix-header">#</th>
                  <th class="fix-width-200 fix-header">เลขที่</th>
                  <th class="min-width-200 fix-header">ช่องทาง</th>
                  <th class="fix-width-120 text-right fix-header">คงเหลือก่อนตัด</th>
                  <th class="fix-width-120 text-right fix-header">ยอดตัดบิลนี้</th>
                  <th class="fix-width-120 text-right fix-header">คงเหลือหลังตัด</th>
                </tr>
              </thead>
              <tbody id="down-payment-table">
            <?php if( ! empty($down_payment)) : ?>
              <?php $no = 1; ?>
              <?php foreach($down_payment as $dp) : ?>
                <tr>
                  <td class="text-center"><?php echo $no; ?></td>
                  <td><?php echo $dp->down_payment_code; ?></td>
                  <td><?php echo payment_role_name($dp->payment_role); ?></td>
                  <td class="text-right"><?php echo number($dp->amountBfUse, 2); ?></td>
                  <td class="text-right"><?php echo number($dp->amount, 2); ?></td>
                  <td class="text-right"><?php echo number($dp->amountAfUse, 2); ?></td>
                </tr>
                <?php $no++; ?>
              <?php endforeach; ?>
            <?php endif; ?>
              </tbody>
            </table>
          </div><!-- table-responsive -->
        </div> <!-- tab panel -->
      </div><!-- tab-content -->
    </div><!-- tabable -->
  </div><!-- col-lg-12 -->
</div><!-- row -->


<div class="row hidden-xs">
	<div class="divider-hidden"></div>
	<div class="col-lg-6 col-md-6 col-sm-5 padding-5">
		<div class="form-horizontal">
			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">พนักงานขาย</label>
				<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
					<select class="form-control edit" id="sale-id" name="sale_id" disabled>
						<?php echo select_saleman($order->SlpCode); ?>
					</select>
				</div>
			</div>

			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">Owner</label>
				<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
					<input type="text" class="form-control input-sm" id="owner" value="<?php echo $order->user; ?>" disabled>
				</div>
			</div>

			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">Remark</label>
				<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
					<textarea id="remark" maxlength="254" rows="3" class="form-control" disabled><?php echo $order->Comments; ?></textarea>
				</div>
			</div>

		</div>
	</div>

  <div class="col-lg-6 col-md-6 col-sm-7 padding-5">
		<div class="form-horizontal" >
			<div class="form-group" >
				<label class="col-lg-3 col-md-2 col-sm-2 control-label no-padding-right">จำนวน</label>
				<div class="col-lg-2 col-md-2-harf col-sm-3  padding-5">
					<input type="text" class="form-control input-sm text-center" id="total-qty" value="<?php echo number($total_qty, 2); ?>" disabled>
				</div>
				<label class="col-lg-3 col-md-3-harf hidden-sm control-label no-padding-right">มูลค่าก่อนส่วนลด</label>
        <label class="col-sm-3 visible-sm control-label no-padding-right">มูลค่า</label>
				<div class="col-lg-4 col-md-4 col-sm-4  padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="total-amount" value="<?php echo number($total_amount, 2); ?>" disabled>
				</div>
			</div>
			<div class="form-group" >
				<label class="col-lg-5-harf col-md-5-harf col-sm-5-harf  control-label no-padding-right">ส่วนลด</label>
				<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf  padding-5">
					<span class="input-icon input-icon-right">
						<input type="number" id="bill-disc-percent" class="form-control input-sm" value="<?php echo number($order->DiscPrcnt, 2); ?>" disabled/>
						<i class="ace-icon fa fa-percent"></i>
					</span>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="bill-disc-amount" value="<?php echo number($order->DiscSum, 2); ?>"disabled >
				</div>
			</div>

			<div class="form-group <?php echo $order->TaxStatus == 'Y' ? '' : 'hide'; ?>" id="bill-wht" >
        <label class="col-lg-5-harf col-md-5-harf col-sm-5-harf  control-label no-padding-right">หัก ณ ที่จ่าย</label>
        <div class="col-lg-2-harf col-md-2-harf col-sm-2-harf  padding-5">
          <span class="input-icon input-icon-right">
          <input type="number" id="whtPrcnt" class="form-control input-sm" onchange="recalTotal()" value="<?php echo number($order->WhtPrcnt, 2); ?>" disabled/>
          <i class="ace-icon fa fa-percent"></i>
          </span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
          <input type="hidden" id="wht-amount" value="<?php echo $order->WhtAmount; ?>" />
          <input type="text" id="wht-amount-label" class="form-control input-sm text-right" value="<?php echo number($order->WhtAmount, 2); ?>" disabled />
        </div>
      </div>

			<div class="form-group <?php echo $order->TaxStatus == 'Y' ? '' : 'hide'; ?>" id="bill-vat" >
        <label class="col-lg-6 col-md-6 col-sm-6 control-label no-padding-right">VAT</label>
				<div class="col-lg-2 col-md-2 col-sm-2 padding-5">
					<input type="text" id="vat_type" class="form-control input-sm text-center" value="<?php echo ($order->vat_type == 'E' ? 'Exclude' : 'Include'); ?>" disabled/>
        </div>
				<div class="col-lg-4 col-md-4 col-sm-4  padding-5 last">
					<input type="text" id="vat-total" class="form-control input-sm text-right" value="<?php echo number($order->VatSum, 2); ?>" disabled />
				</div>
			</div>

			<div class="form-group" >
        <label class="col-lg-3 col-md-2 col-sm-2 control-label no-padding-right">มัดจำ</label>
				<div class="col-lg-3 col-md-3-harf col-sm-3-harf  padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="down-amount" value="<?php echo number($order->downPaymentAmount, 2); ?>" disabled>
				</div>
				<label class="col-lg-2 col-md-2-harf col-sm-2-harf control-label no-padding-right">รวมทั้งสิ้น</label>
				<div class="col-lg-4 col-md-4 col-sm-4  padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="doc-total" value="<?php echo number($order->DocTotal, 2); ?>" disabled>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if($order->status == 'D') : ?>
  <div class="row hidden-xs">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 red">
      ยกเลิกโดย : <?php echo $order->cancel_user; ?> @<?php echo thai_date($order->cancel_date, TRUE, '/'); ?><br/>
      หมายเหตุ : <?php echo $order->cancel_reason; ?>
    </div>
  </div>
<?php endif; ?>

<?php $this->load->view('cancle_modal'); ?>

<div class="row visible-xs">
	<div class="col-xs-12"><h1 class="text-center">Not supporrt</h1></div>
</div>

<script src="<?php echo base_url(); ?>scripts/order_invoice/order_invoice.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_invoice.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
