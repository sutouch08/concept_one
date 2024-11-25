<?php $this->load->view('include/header'); ?>
<style media="screen">
	.tableFixHead thead th {
		font-size: 11px !important;
	}
</style>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/pos.css"/>
<div class="row hidden-xs">
	<div class="col-lg-4 col-md-4 col-sm-4 padding-5">
		<h4 class="title">บิลขาย POS</h4>
	</div>
  <div class="col-lg-8 col-md-8 col-sm-8 padding-5 text-right top-p">
    <button type="button" class="btn btn-xs btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
		<button type="button" class="btn btn-xs btn-info" onclick="printBill('<?php echo $order->code; ?>')">พิมพ์บิล</button>
		<?php if($order->status == 'O' && empty($order->invoice_code)) : ?>
			<button type="button" class="btn btn-xs btn-success" onclick="showCustomerModal()">ข้อมูลลูกค้า</button>
			<button type="button" class="btn btn-xs btn-purple" onclick="createInvoice()">เปิดใบกำกับ</button>
			<button type="button" class="btn btn-xs btn-primary" onclick="createTaxInvoice()">เปิดใบกำกับภาษี</button>
			<?php if(($order->status == 'O' && (date('Y-m-d') <= date('Y-m-d', strtotime($order->date_add)))) OR $this->_SuperAdmin) : ?>
				<button type="button" class="btn btn-xs btn-danger" onclick="cancelBill('<?php echo $order->code; ?>', <?php echo $order->id; ?>)">
					<i class="fa fa-times"></i> ยกเลิก</button>
			<?php endif; ?>
		<?php endif; ?>
  </div>
</div>
<hr class="hidden-xs"/>
<div class="row hidden-xs">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->code; ?>" disabled />
		<input type="hidden" id="code" value="<?php echo $order->code; ?>" />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
		<label>เล่มเอกสาร</label>
		<select class="form-control input-sm h" id="is-term" disabled>
			<option value="0">ขายสด</option>
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
	</div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
    <label>วันที่</label>
    <input type="text" class="form-control input-sm text-center h" id="data_add" value="<?php echo thai_date($order->date_add); ?>"  disabled/>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
    <label>รหัสลูกค้า</label>
    <input type="text" class="form-control input-sm h" id="customer-code" value="<?php echo $order->customer_code; ?>" disabled />
  </div>
  <div class="col-lg-4 col-md-4-harf col-sm-4-harf padding-5">
    <label>ชื่อลูกค้า</label>
    <input type="text" class="form-control input-sm h" id="customer-name" value="<?php echo $order->customer_name; ?>" disabled />
		<input type="checkbox" id="is-company" class="hide" />
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 padding-5">
		<label>เบอร์โทร</label>
		<input type="text" class="form-control input-sm h" maxlength="32" id="phone" value="<?php echo $order->phone; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-2 padding-5">
		<label>เลขที่ผู้เสียภาษี</label>
		<input type="text" class="form-control input-sm h" maxlength="13" id="tax-id" value="<?php echo $order->tax_id; ?>" disabled/>
	</div>

	<div class="col-lg-1 col-md-1 col-sm-1-harf padding-5">
		<label>รหัสสาขา</label>
		<input type="text" class="form-control input-sm text-center h" maxlength="10" id="branch-code" value="<?php echo $order->branch_code; ?>" disabled/>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-2 padding-5">
		<label>ชื่อสาขา</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="branch-name" value="<?php echo $order->branch_name; ?>" disabled/>
	</div>

	<div class="col-lg-4-harf col-md-6-harf col-sm-4-harf padding-5">
		<label>ที่อยู่</label>
		<input type="text" class="form-control input-sm h" maxlength="254"id="address" value="<?php echo $order->address; ?>" disabled/>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 padding-5">
		<label>ตำบล</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="sub-district" value="<?php echo $order->sub_district; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf padding-5">
		<label>อำเภอ</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="district" value="<?php echo $order->district; ?>" disabled/>
	</div>
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf padding-5">
		<label>จังหวัด</label>
		<input type="text" class="form-control input-sm h" maxlength="100" id="province" value="<?php echo $order->province; ?>" disabled/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-2 padding-5">
		<label>รหัสไปรษณีย์</label>
		<input type="text" class="form-control input-sm text-center h" maxlength="12" id="postcode" value="<?php echo $order->postcode; ?>" disabled/>
	</div>
</div>
<hr class="hidden-xs"/>
<div class="row hidden-xs">
  <div class="col-lg-1-harf col-md-2 col-sm-2 padding-5">
    <label>ใบสั่งขาย</label>
		<div class="input-group">
			<input type="text" class="form-control input-sm text-center" id="so-code" value="<?php echo $order->so_code; ?>" disabled />
			<span class="input-group-btn">
				<button type="button" class="btn btn-xs btn-info"
				onclick="viewSo('<?php echo $order->so_code; ?>')"
				<?php echo (empty($order->so_code) ? 'disabled' :''); ?>>		<i class="fa fa-eye"></i></button>
			</span>
		</div>
  </div>
	<div class="col-lg-1-harf col-md-2 col-sm-2 padding-5">
    <label>ใบกำกับ</label>
		<div class="input-group">
			<input type="text" class="form-control input-sm text-center" value="<?php echo $order->invoice_code; ?>" disabled />
			<span class="input-group-btn">
				<button type="button" class="btn btn-xs btn-info"
				onclick="viewInvoice('<?php echo $order->invoice_code; ?>')"
				<?php echo (empty($order->invoice_code) ? 'disabled' :''); ?>><i class="fa fa-eye"></i></button>
			</span>
		</div>
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 padding-5">
    <label>User</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $order->uname; ?>" disabled/>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1 padding-5">
    <label>สถานะ</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo bill_status_label($order->status); ?>" disabled />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1 padding-5">&nbsp;</div>
	<div class="col-lg-2-harf col-md-1-harf col-sm-2 padding-5">
    <label>จุดขาย</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $pos->shop_name; ?>" disabled/>
  </div>
	<div class="col-lg-2-harf col-md-1-harf col-sm-2 padding-5">
    <label>รหัสเครื่อง</label>
    <input type="text" class="form-control input-sm text-center" value="<?php echo $pos->name; ?>" disabled/>
  </div>

</div><!-- End Row -->
<hr class="padding-5 hidden-xs"/>
<?php if($order->status == 'D') : ?>
	<?php $this->load->view('cancle_watermark'); ?>
<?php endif; ?>
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
				<li>
          <a href="#payment" id="payment-tab" aria-expanded="false" aria-controls="payment" role="tab" data-toggle="tab">การชำระเงิน</a>
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
                  <?php $total_qty += $rs->qty; ?>
                  <?php $total_amount += $rs->total_amount; ?>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

        <div role="tabpanel" class="tab-pane fade" id="down-payment">
          <div class="col-lg-12 col-md-12 col-sm-12 padding-0 border-1 table-responsive"
            style="height:200px; overflow:auto; border-top:solid 1px #ccc;">
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

				<div role="tabpanel" class="tab-pane fade" id="payment">
          <div class="col-lg-12 col-md-12 col-sm-12 padding-0 border-1 table-responsive"
            style="height:200px; overflow:auto; border-top:solid 1px #ccc;">
            <table class="table table-bordered tableFixHead" style="margin-bottom:20px;">
              <thead>
                <tr>
                  <th class="fix-width-40 text-center fix-header">#</th>
                  <th class="fix-width-200 text-center fix-header">วันที่บันทึก</th>
                  <th class="fix-width-200 text-center fix-header">วันที่เกิดรายการ</th>
                  <th class="fix-width-120 text-center fix-header">ช่องทาง</th>
                  <th class="fix-width-120 text-center fix-header">ยอดเงิน</th>
									<th class="min-width-100 text-center fix-header"></th>
                </tr>
              </thead>
              <tbody id="down-payment-table">
            <?php if( ! empty($payments)) : ?>
              <?php $no = 1; ?>							
              <?php foreach($payments as $pm) : ?>
                <tr>
                  <td class="text-center"><?php echo $no; ?></td>
                  <td class="text-center"><?php echo thai_date($pm->date_upd, TRUE); ?></td>
                  <td class="text-center"><?php echo thai_date($pm->payment_date); ?></td>
                  <td class="text-center"><?php echo $pm->role_name; ?></td>
                  <td class="text-center"><?php echo number($pm->amount, 2); ?></td>
                  <td></td>
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
</div>

<div class="row hidden-xs">
	<div class="divider-hidden"></div>
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5">
		<div class="form-horizontal">
			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">พนักงานขาย</label>
				<div class="col-lg-5 col-md-6 col-sm-6">
					<select class="form-control edit" id="sale-id" disabled>
						<?php echo select_saleman($order->sale_id); ?>
					</select>
					<input type="hidden" id="sale_id" value="<?php echo $order->sale_id; ?>" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">Owner</label>
				<div class="col-lg-5 col-md-6 col-sm-6">
					<input type="text" class="form-control input-sm" id="owner" value="<?php echo $order->uname; ?>" disabled>
				</div>
			</div>
		</div>
	</div>

  <div class="col-lg-6 col-md-6 col-sm-6 padding-5">
		<div class="form-horizontal" >
			<div class="form-group" >
				<label class="col-lg-3-harf col-md-3-harf col-sm-3-harf control-label no-padding-right">จำนวน</label>
				<div class="col-lg-2 col-md-2 col-sm-2 padding-5">
					<input type="text" class="form-control input-sm text-center" id="total-qty" value="<?php echo number($total_qty, 2); ?>" disabled>
				</div>
				<label class="col-lg-2-harf col-md-2-harf col-sm-2-harf control-label padding-5">มูลค่ารวม</label>
				<div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="total-amount" value="<?php echo number($total_amount, 2); ?>" disabled>
				</div>
			</div>
			<div class="form-group" >
				<label class="col-lg-6 col-md-6 col-sm-6 control-label no-padding-right">ส่วนลด</label>
				<div class="col-lg-2 col-md-2 col-sm-2 padding-5">
					<span class="input-icon input-icon-right">
						<input type="number" id="bill-disc-percent" class="form-control input-sm" value="<?php echo number($order->discPrcnt, 2); ?>" disabled/>
						<i class="ace-icon fa fa-percent"></i>
					</span>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="bill-disc-amount" value="<?php echo number($order->disc_amount, 2); ?>"disabled >
				</div>
			</div>

			<div class="form-group" id="bill-wht" >
        <label class="col-lg-6 col-md-6 col-sm-6 control-label no-padding-right">หัก ณ ที่จ่าย</label>
        <div class="col-lg-2 col-md-2 col-sm-2 padding-5">
          <span class="input-icon input-icon-right">
          <input type="number" id="whtPrcnt" class="form-control input-sm" onchange="recalTotal()" value="<?php echo number($order->WhtPrcnt, 2); ?>" disabled/>
          <i class="ace-icon fa fa-percent"></i>
          </span>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
          <input type="text" id="wht-amount-label" class="form-control input-sm text-right" value="<?php echo number($order->WhtAmount, 2); ?>" disabled />
        </div>
      </div>

			<div class="form-group" id="bill-vat" >
				<label class="col-lg-6 col-md-6 col-sm-6 control-label no-padding-right">VAT</label>
				<div class="col-lg-2 col-md-2 col-sm-2 padding-5">
					<input type="text" id="vat_type" class="form-control input-sm text-center" value="<?php echo ($order->vat_type == 'E' ? 'Exclude' : 'Include'); ?>" disabled/>
        </div>
				<div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
					<input type="text" id="vat-total" class="form-control input-sm text-right" value="<?php echo number($order->vat_amount, 2); ?>" disabled />
				</div>
			</div>
			<div class="form-group" >
				<label class="col-lg-2 col-md-2 col-sm-2 control-label no-padding-right">มัดจำ</label>
				<div class="col-lg-3-harf col-md-3-harf col-sm-3-harf padding-5">
					<input type="text" id="down-payment-label" class="form-control input-sm text-right" value="<?php echo number($order->down_payment_amount, 2); ?>" disabled />
				</div>
				<label class="col-lg-2-harf col-md-2-harf col-sm-2-harf control-label padding-5">ยอดชำระ</label>
				<div class="col-lg-4 col-md-4 col-sm-4 padding-5 last">
					<input type="text" id="doc-total-label" class="form-control input-sm text-right" value="<?php echo number($order->payAmount, 2); ?>" disabled/>
				</div>
			</div>
			<input type="hidden" id="doc-total" value="<?php echo $order->amount; ?>" />
			<input type="hidden" id="down-payment-amount" value="<?php echo $order->down_payment_amount; ?>" />
			<input type="hidden" id="wht-amount" value="<?php echo $order->WhtAmount; ?>" />
			<input type="hidden" id="pay-amount" value="<?php echo $order->payAmount; ?>" />
		</div>
	</div>
</div>
<div class="divider-hidden"></div>
<div class="divider-hidden"></div>

<?php $this->load->view('order_pos/pos_cancel_modal'); ?>
<?php $this->load->view('order_invoice/customer_modal'); ?>


<input type="hidden" id="payment-role" value="<?php echo $order->payment_role; ?>" />
<input type="hidden" id="bill-amount" value="<?php echo number($order->amount, 2); ?>" />
<input type="hidden" id="pay-amount" value="<?php echo number($order->payAmount, 2); ?>" />
<input type="hidden" id="down-amount" value="<?php echo number($order->down_payment_amount, 2); ?>" />
<input type="hidden" id="shop_id" value="<?php echo $pos->shop_id; ?>" />
<div class="row visible-xs">
	<div class="col-xs-12"><h1 class="text-center">Not supporrt</h1></div>
</div>

<script src="<?php echo base_url(); ?>scripts/order_pos_bill/order_pos_bill.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/order_pos/order_pos_invoice.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
