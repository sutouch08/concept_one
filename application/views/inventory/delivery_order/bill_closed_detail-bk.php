<input type="hidden" id="order_code" value="<?php echo $order->code; ?>" />
<input type="hidden" id="customer_code" value="<?php echo $order->customer_code; ?>" />
<input type="hidden" id="customer_ref" value="<?php echo $order->customer_ref; ?>" />
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <?php if(!empty($order->reference)) : ?>
      <label class="font-size-12 blod">
        <?php echo $order->code.' ['.$order->reference.']';  ?>
      </label>
    <?php else : ?>
    <label class="font-size-14 blod">
      <?php echo $order->code; ?>
    </label>
    <?php endif; ?>
  </div>

  <?php if($order->role == 'C' OR $order->role == 'N') : ?>
  <div class="col-sm-4 padding-5">
    <label class="font-size-12 blod">ลูกค้า : <?php echo empty($order->customer_ref) ? $order->customer_name : $order->customer_ref; ?></label>
  </div>
  <?php else : ?>
    <div class="col-sm-6 padding-5">
      <label class="font-size-14 blod">
        <?php if($order->role == 'L' OR $order->role == 'U' OR $order->role == 'R') : ?>
          ผู้เบิก : <?php echo $order->empName; ?>
          <?php if(!empty($order->user_ref)) : ?>
            &nbsp;&nbsp;[ผู้สั่งงาน : <?php echo $order->user_ref; ?>]
          <?php endif; ?>
        <?php else: ?>
        ลูกค้า : <?php echo empty($order->customer_ref) ? $order->customer_name : $order->customer_ref; ?>
      <?php endif; ?>
      </label>
    </div>
  <?php endif; ?>

  <?php if($order->role == 'C' OR $order->role == 'N') : ?>
    <div class="col-sm-4 padding-5">
      <label class="font-size-2 blod">โซน : <?php echo $order->zone_name; ?></label>
    </div>
    <div class="col-sm-2 padding-5 last text-right">
      <label class="font-size-14 blod">พนักงาน : <?php echo $order->user; ?></label>
    </div>
  <?php else : ?>
  <div class="col-sm-4 padding-5 last text-right">
    <label class="font-size-14 blod">พนักงาน : <?php echo $order->user; ?></label>
  </div>
  <?php endif; ?>

  <?php if( $order->remark != '') : ?>
    <div class="col-sm-12">
      <label class="font-size-14 blod">หมายเหตุ :</label>
      <?php echo $order->remark; ?>
    </div>
  <?php endif; ?>
</div>
<hr/>

<div class="row">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-sm btn-info" onclick="printAddress()"><i class="fa fa-print"></i> ใบนำส่ง</button>
    <button type="button" class="btn btn-sm btn-primary" onclick="printDelivery()"><i class="fa fa-print"></i> ใบส่งของ </button>
    <button type="button" class="btn btn-sm btn-success" onclick="printOrderBarcode()"><i class="fa fa-print"></i> Packing List (barcode)</button>
    <button type="button" class="btn btn-sm btn-warning" onclick="showBoxList()"><i class="fa fa-print"></i> Packing List (ปะหน้ากล่อง)</button>
  </div>
</div>
<hr/>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered" style="min-width:800px;">
      <thead>
        <tr class="font-size-12">
          <th class="width-5 text-center">ลำดับ</th>
          <th class="width-35 text-center">สินค้า</th>
          <th class="width-10 text-center">ราคา</th>
					<th class="width-10 text-center">ส่วนลด</th>
          <th class="width-10 text-center">ออเดอร์</th>
          <th class="width-10 text-center">จัด</th>
          <th class="width-10 text-center">ตรวจ</th>
          <th class="width-10 text-center">มูลค่า</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($details)) : ?>
<?php   $no = 1;
        $totalQty = 0;
        $totalPrepared = 0;
        $totalQc = 0;
        $totalAmount = 0;
        $totalDiscount = 0;
        $totalPrice = 0;
				$billQty = 0;
?>
<?php   foreach($details as $rs) :  ?>
<?php     $color = ($rs->order_qty == $rs->qc OR $rs->is_count == 0) ? '' : 'red'; ?>
        <tr class="font-size-12 <?php echo $color; ?>">
          <td class="text-center">
            <?php echo $no; ?>
          </td>

          <!--- รายการสินค้า ที่มีการสั่งสินค้า --->
          <td>
            <?php echo limitText($rs->product_code.' : '. $rs->product_name, 100); ?>
          </td>

          <!--- ราคาสินค้า  --->
          <td class="text-center">
            <?php echo number($rs->price, 2); ?>
          </td>

					<!--- ส่วนลด  --->
					<td class="text-center">
						<?php echo discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
					</td>

          <!---   จำนวนที่สั่ง  --->
          <td class="text-center">
            <?php echo number($rs->order_qty); ?>
          </td>

          <!--- จำนวนที่จัดได้  --->
          <td class="text-center">
            <?php echo $rs->is_count == 0 ? number($rs->order_qty) : number($rs->prepared); ?>
          </td>

          <!--- จำนวนที่ตรวจได้ --->
          <td class="text-center">
            <?php echo $rs->is_count == 0 ? number($rs->order_qty) : number($rs->qc); ?>
          </td>


          <td class="text-right">
            <?php echo $rs->is_count == 0 ? number($rs->final_price * $rs->order_qty) : number( $rs->final_price * $rs->qc , 2); ?>
          </td>

        </tr>
<?php
      $totalQty += $rs->order_qty;
      $totalPrepared += ($rs->is_count == 0 ? $rs->order_qty : $rs->prepared);
      $totalQc += ($rs->is_count == 0 ? $rs->order_qty : $rs->qc);
			$billQty += $rs->order_qty > $rs->qc ? $rs->qc : $rs->order_qty;
      $totalDiscount += ($rs->is_count == 0 ? $rs->discount_amount * $rs->order_qty : $rs->discount_amount * $rs->qc);
      $totalAmount += ($rs->is_count == 0 ? $rs->final_price * $rs->order_qty : $rs->final_price * $rs->qc);
      $totalPrice += ($rs->is_count == 0 ? $rs->price * $rs->order_qty : $rs->price * $rs->qc);
      $no++;
?>
<?php   endforeach; ?>
      <tr class="font-size-12">
        <td colspan="4" class="text-right font-size-14">
          รวม
        </td>

        <td class="text-center">
          <?php echo number($totalQty); ?>
        </td>

        <td class="text-center">
          <?php echo number($totalPrepared); ?>
        </td>

        <td class="text-center">
          <?php echo number($totalQc); ?>
        </td>

        <td class="text-right">
          <?php echo number($totalAmount, 2); ?>
        </td>
      </tr>
<?php else : ?>
      <tr><td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td></tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="divider-hidden"></div>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">พนักงานขาย</label>
				<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
					<input type="text" class="width-100" value="<?php echo get_sale_name($order->sale_code); ?>" disabled />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-4 col-sm-4 control-label no-padding-right">Owner</label>
				<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12">
					<input type="text" class="form-control input-sm" value="<?php echo $order->user; ?>" disabled>
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label no-padding-right">Remark</label>
				<div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
					<textarea id="remark" maxlength="254" rows="3" class="form-control" onchange="updateRemark()" disabled><?php echo $order->remark; ?></textarea>
				</div>
			</div>

		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
		<div class="form-horizontal">


			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-5-harf col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ส่วนลด</label>
				<div class="col-lg-2-harf col-md-4 col-sm-5 col-xs-6 padding-5">
					<span class="input-icon input-icon-right">
						<input type="number" id="bill-disc-percent" class="form-control input-sm" value="<?php echo round($order->bDiscText, 6); ?>" disabled />
						<i class="ace-icon fa fa-percent"></i>
					</span>
				</div>

				<?php
				$disc = $order->bDiscText > 0 ? $order->bDiscText * 0.01 : 0;
				$bDiscAmount = $totalAmount * $disc;
				$netAmount = $totalAmount - $bDiscAmount;
				?>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="bill-disc-amount" value="<?php echo number($bDiscAmount, 2); ?>" disabled>
				</div>
			</div>
			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">รวมทั้งสิ้น</label>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="doc-total" value="<?php echo number($netAmount, 2); ?>" disabled>
				</div>
			</div>
			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">ชำระแล้ว</label>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="paid-amount" value="<?php echo number($order->paidAmount, 2); ?>" disabled>
				</div>
			</div>
			<div class="form-group" style="margin-bottom:5px;">
				<label class="col-lg-8 col-md-8 col-sm-7 col-xs-6 control-label no-padding-right">คงเหลือ</label>
				<div class="col-lg-4 col-md-4 col-sm-5 col-xs-6 padding-5 last">
					<input type="text" class="form-control input-sm text-right" id="total-balance" value="<?php echo number($order->TotalBalance, 2); ?>" disabled>
				</div>
			</div>
		</div>
	</div>
</div>


<!--************** Address Form Modal ************-->
<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="addressModal" aria-hidden="true">
  <div class="modal-dialog" style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body" id="info_body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-primary" onclick="printSelectAddress()"><i class="fa fa-print"></i> พิมพ์</button>
      </div>
    </div>
  </div>
</div>

<?php $this->load->view('inventory/order_closed/box_list');  ?>

<script src="<?php echo base_url(); ?>scripts/print/print_address.js"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_order.js"></script>
