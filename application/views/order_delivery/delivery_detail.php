<?php $this->load->view('include/header'); ?>
<?php $this->load->view('order_invoice/style'); ?>
<style media="screen">
	.tableFixHead thead th {
		font-size: 11px !important;
	}
</style>
<div class="row hidden-xs">
	<div class="col-lg-4 col-md-4 col-sm-4 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
  <div class="col-lg-8 col-md-8 col-sm-8 padding-5 text-right top-p">
    <button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		<?php if(empty($doc->invoice_code)) : ?>
			<button type="button" class="btn btn-xs btn-success" onclick="showCustomerModal()">ข้อมูลลูกค้า</button>
			<button type="button" class="btn btn-xs btn-purple" onclick="createInvoice()">เปิดใบกำกับ</button>
			<button type="button" class="btn btn-xs btn-primary" onclick="createTaxInvoice()">เปิดใบกำกับภาษี</button>
		<?php endif; ?>
  </div>
</div>
<hr class="hidden-xs"/>
<div class="row hidden-xs">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
		<input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
		<label>เล่มเอกสาร</label>
		<select class="form-control input-sm h" id="is-term" disabled>
			<option value="0" <?php echo is_selected('0', $doc->is_term); ?>>ขายสด</option>
			<option value="1" <?php echo is_selected('1', $doc->is_term); ?>>ขายเชื่อ</option>
		</select>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
		<label>ชนิด VAT</label>
		<select class="form-control input-sm h" id="vat-type" onchange="toggleVatType()" disabled>
			<option value="">เลือก</option>
			<option value="E" <?php echo is_selected('E', $doc->vat_type); ?>>แยกนอก</option>
			<option value="I" <?php echo is_selected('I', $doc->vat_type); ?>>รวมใน</option>
			<option value="N" <?php echo is_selected('N', $doc->vat_type); ?>>ไม่ VAT</option>
		</select>
	</div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center h" id="data_add" value="<?php echo thai_date($doc->date_add); ?>"  disabled/>
		<input type="hidden" id="date" value="<?php echo date('Y-m-d'); ?>" />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
    <label>รหัสลูกค้า</label>
    <input type="text" class="form-control input-sm h" id="customer-code" value="<?php echo $doc->customer_code; ?>" disabled />
  </div>
  <div class="col-lg-4 col-md-4-harf col-sm-4-harf padding-5">
    <label>ชื่อลูกค้า</label>
    <input type="text" class="form-control input-sm h" id="customer-name" value="<?php echo $doc->customer_name; ?>" disabled />
		<input type="checkbox" id="is-company" class="hide" />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 padding-5">
		<label>เบอร์โทร</label>
		<input type="text" class="form-control input-sm h" maxlength="32" id="phone" value="<?php echo $doc->phone; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 padding-5">
		<label>เลขที่ผู้เสียภาษี</label>
		<input type="text" class="form-control input-sm h" maxlength="13" id="tax-id" value="<?php echo $doc->tax_id; ?>" disabled/>
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf padding-5">
		<label>รหัสสาขา</label>
		<input type="text" class="form-control input-sm text-center h" maxlength="10" id="branch-code" value="<?php echo $doc->branch_code; ?>" disabled/>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-2 padding-5">
		<label>ชื่อสาขา</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="branch-name" value="<?php echo $doc->branch_name; ?>" disabled/>
	</div>

	<div class="col-lg-4-harf col-md-6-harf col-sm-4-harf padding-5">
		<label>ที่อยู่</label>
		<input type="text" class="form-control input-sm h" maxlength="254"id="address" value="<?php echo $doc->address; ?>" disabled/>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 padding-5">
		<label>ตำบล</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="sub-district" value="<?php echo $doc->sub_district; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf padding-5">
		<label>อำเภอ</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="district" value="<?php echo $doc->district; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf padding-5">
		<label>จังหวัด</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="province" value="<?php echo $doc->province; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 padding-5">
		<label>รหัสไปรษณีย์</label>
		<input type="text" class="form-control input-sm text-center h" maxlength="12" id="postcode" value="<?php echo $doc->postcode; ?>" disabled/>
	</div>
</div>
<hr class="hidden-xs"/>
<div class="row hidden-xs">
  <div class="col-lg-1-harf col-md-2 col-sm-2 padding-5">
    <label>ใบสั่งขาย</label>
		<div class="input-group">
			<input type="text" class="form-control input-sm text-center" id="so-code" value="<?php echo $doc->so_code; ?>" disabled />
			<span class="input-group-btn">
				<button type="button" class="btn btn-xs btn-info"
				onclick="viewSo('<?php echo $doc->so_code; ?>')"
				<?php echo (empty($doc->so_code) ? 'disabled' :''); ?>>		<i class="fa fa-eye"></i></button>
			</span>
		</div>
  </div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 padding-5">
    <label>ใบกำกับ</label>
		<div class="input-group">
			<input type="text" class="form-control input-sm text-center" value="<?php echo $doc->invoice_code; ?>" disabled />
			<span class="input-group-btn">
				<button type="button" class="btn btn-xs btn-info"
				onclick="viewInvoice('<?php echo $doc->invoice_code; ?>')"
				<?php echo (empty($doc->invoice_code) ? 'disabled' :''); ?>><i class="fa fa-eye"></i></button>
			</span>
		</div>
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 padding-5">
    <label>User</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->user; ?>" disabled/>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1 padding-5">
    <label>สถานะ</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo empty($rs->invoice_code) ? 'Open' : 'Closed'; ?>" disabled />
  </div>

</div><!-- End Row -->
<hr class="padding-5 hidden-xs"/>
<div class="row hidden-xs">
	<div class="col-lg-12 col-md-12 col-sm-12 padding-0 margin-bottom-15">
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
          <div class="col-lg-12 col-md-12 col-sm-12 padding-0 border-1 table-responsive"
          style="height:200px; overflow:auto; border-top:solid 1px #ccc;">
          <table class="table table-bordered tableFixHead" style="min-width:780px; margin-bottom:20px;">
            <thead>
              <tr class="">
                <th class="fix-width-40 text-center fix-header">#</th>
                <th class="fix-width-200 fix-header">รหัสสินค้า</th>
                <th class="min-width-200 fix-header">รายละเอียด</th>
                <th class="fix-width-80 text-right fix-header">จำนวน</th>
                <th class="fix-width-80 text-right font-size-12 fix-header">ราคา/หน่วย</th>
                <th class="fix-width-80 text-right fix-header">ส่วนลด</th>
                <th class="fix-width-100 text-right fix-header">มูลค่า</th>
              </tr>
            </thead>
            <tbody>
              <?php if( ! empty($details)) : ?>
                <?php $no = 1; ?>
                <?php $total_qty = 0; ?>
                <?php $total_amount = 0; ?>
                <?php foreach($details as $rs) : ?>
                  <tr>
                    <td class="middle text-center no"><?php echo $no; ?></td>
                    <td class="middle"><?php echo $rs->product_code; ?></td>
                    <td class="middle"><?php echo $rs->product_name; ?></td>
                    <td class="middle text-right"><?php echo number($rs->qty, 2); ?></td>
                    <td class="middle text-right"><?php echo number($rs->price, 2); ?></td>
                    <td class="middle text-right"><?php echo $rs->discount_label; ?></td>
                    <td class="middle text-right"><?php echo number($rs->total_amount, 2); ?></td>
										<input type="hidden" class="bill-item"
											data-no="<?php echo $no; ?>"
											data-code="<?php echo $rs->product_code; ?>"
											data-name="<?php echo $rs->product_name; ?>"
											data-qty="<?php echo $rs->qty; ?>"
											data-price="<?php echo $rs->price; ?>"
											data-discount="<?php echo $rs->discount_label; ?>"
											data-total="<?php echo $rs->total_amount; ?>" />
                  </tr>
                  <?php $no++; ?>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

        <div role="tabpanel" class="tab-pane fade" id="down-payment">
          <div class="col-lg-12 col-md-12 col-sm-12 padding-0 border-1 table-responsive"
            style="height:200px; overflow:auto; border-top:solid 1px #ccc;">
					<?php if(empty($doc->invoice_code)) : ?>
						<table class="table table-bordered tableFixHead" style="margin-bottom:20px;">
							<thead>
								<tr>
									<th class="fix-width-40 text-center fix-header">#</th>
									<th class="fix-width-200 fix-header">เลขที่</th>
									<th class="min-width-200 fix-header">ช่องทาง</th>
									<th class="fix-width-100 text-right fix-header">ยอดเงิน</th>
									<th class="fix-width-100 text-right fix-header">ตัดแล้ว</th>
									<th class="fix-width-100 text-right fix-header">คงเหลือ</th>
									<th class="fix-width-100 text-right fix-header">ยอดตัดบิลนี้</th>
								</tr>
							</thead>
							<tbody>
					<?php if( ! empty($down_payment_list)) : ?>
						<?php $no = 1; ?>
						<?php foreach($down_payment_list as $dp) : ?>
							<tr id="dp-<?php echo $no; ?>">
								<td class="text-center dp-no"><?php echo $no; ?></td>
								<td class=""><?php echo $dp->code; ?></td>
								<td class=""><?php echo $dp->payment_role_name; ?></td>
								<td class="text-right dp-amount"><?php echo number($dp->amount, 2); ?></td>
								<td class="text-right dp-used"><?php echo number($dp->used, 2); ?></td>
								<td class="text-right dp-available"><?php echo number($dp->available, 2); ?></td>
								<td class="text-right dp-use-amount"><?php echo number($dp->use_amount,2); ?>
									<input type="number" class="form-control input-sm text-right down-amount hide"
										id="down-<?php echo $dp->id; ?>"
										data-id="<?php echo $dp->id; ?>"
										data-code="<?php echo $dp->code; ?>"
										data-reference="<?php echo $dp->reference; ?>"
										data-reftype="<?php echo $dp->ref_type; ?>"
										data-available="<?php echo $dp->available; ?>"
										value="<?php echo $dp->use_amount; ?>"
										disabled />
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
							</tbody>
						</table>
					<?php else : ?>
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
							<?php $downPaymentUse = 0; ?>
              <?php foreach($down_payment as $dp) : ?>
                <tr>
                  <td class="text-center"><?php echo $no; ?></td>
                  <td><?php echo $dp->down_payment_code; ?></td>
                  <td><?php echo payment_role_name($dp->payment_role); ?></td>
                  <td class="text-right"><?php echo number($dp->amountBfUse, 2); ?></td>
                  <td class="text-right"><?php echo number($dp->amount, 2); ?></td>
                  <td class="text-right"><?php echo number($dp->amountAfUse, 2); ?></td>
                </tr>
								<?php $downPaymentUse += $dp->amount; ?>
                <?php $no++; ?>
              <?php endforeach; ?>
            <?php endif; ?>
              </tbody>
            </table>
					<?php endif; ?>
          </div><!-- table-responsive -->
        </div> <!-- tab panel -->
      </div><!-- tab-content -->
    </div><!-- tabable -->
  </div><!-- col-lg-12 -->
</div>

<div class="row hidden-xs">
	<div class="divider-hidden"></div>
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5">
		<div class="form-horizontal">
			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">พนักงานขาย</label>
				<div class="col-lg-5 col-md-6 col-sm-6">
					<select class="form-control edit" id="sale-id" disabled>
						<?php echo select_saleman($doc->sale_code); ?>
					</select>
					<input type="hidden" id="sale_id" value="<?php echo $doc->sale_code; ?>" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">Owner</label>
				<div class="col-lg-5 col-md-6 col-sm-6">
					<input type="text" class="form-control input-sm" id="owner" value="<?php echo $doc->user; ?>" disabled>
				</div>
			</div>

			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">Remark</label>
				<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
					<textarea id="remark" maxlength="254" rows="3" class="form-control" disabled><?php echo $doc->remark; ?></textarea>
				</div>
			</div>
		</div>
	</div>

  <div class="col-lg-6 col-md-6 col-sm-6 padding-5">
		<div class="form-horizontal" >
			<div class="form-group" >
				<label class="col-lg-3-harf col-md-3-harf col-sm-3-harf control-label no-padding-right">จำนวน</label>
				<div class="col-lg-2 col-md-2 col-sm-2 padding-5">
					<input type="text" class="form-control input-sm text-center" id="total-qty" value="<?php echo number($doc->totalQty, 2); ?>" disabled>
				</div>
				<label class="col-lg-2-harf col-md-2-harf col-sm-2-harf control-label padding-5">มูลค่ารวม</label>
				<div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="total-amount" value="<?php echo number($doc->TotalBfDisc, 2); ?>" disabled>
				</div>
			</div>
			<div class="form-group" >
				<label class="col-lg-6 col-md-6 col-sm-6 control-label no-padding-right">ส่วนลด</label>
				<div class="col-lg-2 col-md-2 col-sm-2 padding-5">
					<span class="input-icon input-icon-right">
						<input type="number" id="bill-disc-percent" class="form-control input-sm" value="<?php echo number($doc->bDiscText, 2); ?>" disabled/>
						<i class="ace-icon fa fa-percent"></i>
					</span>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="bill-disc-amount" value="<?php echo number($doc->DiscSum, 2); ?>"disabled >
				</div>
			</div>

			<div class="form-group" id="bill-wht" >
        <label class="col-lg-6 col-md-6 col-sm-6 control-label no-padding-right">หัก ณ ที่จ่าย</label>
        <div class="col-lg-2 col-md-2 col-sm-2 padding-5">
          <span class="input-icon input-icon-right">
          <input type="number" id="whtPrcnt" class="form-control input-sm" onchange="recalTotal()" value="<?php echo number($doc->WhtPrcnt, 2); ?>" disabled/>
          <i class="ace-icon fa fa-percent"></i>
          </span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
          <input type="text" id="wht-amount-label" class="form-control input-sm text-right" value="<?php echo number($doc->WhtAmount, 2); ?>" disabled />
        </div>
      </div>

			<div class="form-group" id="bill-vat" >
				<label class="col-lg-6 col-md-6 col-sm-6 control-label no-padding-right">VAT</label>
				<div class="col-lg-2 col-md-2 col-sm-2 padding-5">
					<input type="text" id="vat_type" class="form-control input-sm text-center" value="<?php echo ($doc->vat_type == 'E' ? 'Exclude' : 'Include'); ?>" disabled/>
        </div>
				<div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
					<input type="text" id="vat-total" class="form-control input-sm text-right" value="<?php echo number($doc->VatSum, 2); ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-8 col-md-4 col-sm-4 control-label no-padding-right">รวมทั้งสิ้น</label>
				<div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
					<input type="text" id="doc-total" class="form-control input-sm text-right" value="<?php echo number($doc->DocTotal, 2); ?>" disabled />
				</div>
			</div>

			<?php $payAmount = $doc->DocTotal - $downPaymentUse; ?>
			<div class="form-group" >
				<label class="col-lg-2 col-md-2 col-sm-2 control-label no-padding-right">มัดจำ</label>
				<div class="col-lg-3-harf col-md-3-harf col-sm-3-harf padding-5">
					<input type="text" id="down-payment-label" class="form-control input-sm text-right" value="<?php echo number($downPaymentUse, 2); ?>" disabled />
				</div>
				<label class="col-lg-2-harf col-md-2-harf col-sm-2-harf control-label padding-5">ยอดชำระ</label>
				<div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
					<input type="text" id="doc-total-label" class="form-control input-sm text-right" value="<?php echo number($payAmount, 2); ?>" disabled/>
				</div>
			</div>
			<input type="hidden" id="doc-total" value="<?php echo $doc->DocTotal; ?>" />
			<input type="hidden" id="down-payment-amount" value="<?php echo $downPaymentUse; ?>" />
			<input type="hidden" id="wht-amount" value="<?php echo $doc->WhtAmount; ?>" />
			<input type="hidden" id="pay-amount" value="<?php echo $payAmount; ?>" />
			<?php $refType = $doc->role == 'S' ? 'WO' : ($doc->role == 'U' ? 'WU' : ($doc->role == 'C' ? 'WC' : 'WS')); ?>

			<input type="hidden" id="ref-type" value="<?php echo $refType; ?>" />
		</div>
	</div>
</div>
<div class="divider-hidden"></div>
<div class="divider-hidden"></div>

<?php $this->load->view('order_pos/pos_cancel_modal'); ?>
<?php $this->load->view('order_invoice/customer_modal'); ?>

<div class="row visible-xs">
	<div class="col-xs-12"><h1 class="text-center">Not supporrt</h1></div>
</div>


<script src="<?php echo base_url(); ?>scripts/order_delivery/order_delivery.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_invoice.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
