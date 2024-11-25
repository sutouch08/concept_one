<div class="col-lg-4 col-md-3 col-sm-4 hidden-xs" style="margin-top:-5px; padding-left:0px; overflow:auto;" id="right-block">
  <div class="row" style="padding-left:10px;">
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" onclick="holdBill()">
        <p style="margin-bottom:0px;">พักบิล</p>
        <p style="font-size:10px; margin-bottom:0px;">(F1)</p>
      </button>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" onclick="showHoldBill()">
        <p style="margin-bottom:0px;">
          เรียกบิล
          <?php if( ! empty($holdBillCount)) : ?>
            <span class="badge badge-danger" style="position:absolute; top:0; right:0; color:red; background-color:white;"><?php echo $holdBillCount; ?></span>
          <?php endif; ?>
        </p>
        <p style="font-size:10px; margin-bottom:0px;">(F2)</p>
      </button>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" onclick="returnList()">
        <p style="margin-bottom:0px;">รับคืน</p>
        <p style="font-size:10px; margin-bottom:0px;">(F3)</p>
      </button>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" onclick="billList()">
        <p style="margin-bottom:0px;">บิลขาย</p>
        <p style="font-size:10px; margin-bottom:0px;">(F4)</p>
      </button>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" onclick="removeItems()">
        <p style="margin-bottom:0px;">ลบ</p>
        <p style="font-size:10px; margin-bottom:0px;">(Ctrl Del)</p>
      </button>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" onclick="cashIn()">
        <p style="margin-bottom:0px;">นำเงินเข้า</p>
        <p style="font-size:10px; margin-bottom:0px;">(F6)</p>
      </button>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" onclick="cashOut()">
        <p style="margin-bottom:0px;">นำเงินออก</p>
        <p style="font-size:10px; margin-bottom:0px;">(F7)</p>
      </button>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" onclick="changeEmployee()">
        <p style="margin-bottom:0px;">พนักงาน</p>
        <p style="font-size:10px; margin-bottom:0px;">(F8)</p>
      </button>
    </div>

    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" onclick="findItem()">
        <p style="margin-bottom:0px;">ค้นหา</p>
        <p style="font-size:10px; margin-bottom:0px;">(F9)</p>
      </button>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" onclick="downPaymentList()">
        <p style="margin-bottom:0px;">รับเงินมัดจำ</p>
        <p style="font-size:10px; margin-bottom:0px;">(Ctrl F12)</p>
      </button>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" onclick="openDrawer()">
        <p style="margin-bottom:0px;">เปิดลิ้นชัก</p>
        <p style="font-size:10px; margin-bottom:0px;"></p>
      </button>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 padding-5-5">
<?php if($pos->round_id) : ?>
      <button type="button" class="btn btn-purple btn-block pos-btn" onclick="closeRound()">
        <p style="margin-bottom:0px;">ปิดรอบ</p>
        <p style="font-size:10px; margin-bottom:0px;"></p>
      </button>
<?php else : ?>
    <button type="button" class="btn btn-primary btn-block pos-btn" onclick="openRoundInit()">
      <p style="margin-bottom:0px;">เปิดรอบ</p>
      <p style="font-size:10px; margin-bottom:0px;"></p>
    </button>
<?php endif; ?>
    </div>
  </div>

  <div class="divider-hidden"></div>

  <div class="row" style="padding-left:10px;">
    <div class="col-lg-12 col-md-12 col-sm-12 padding-5" style="padding-left:0;">
      <h4 class="title-xs" style="font-size:14px; padding:8px;">ชำระเงิน</h4>
    </div>
  </div>
  <div class="row" style="padding-left:10px;">
    <div class="col-lg-4 col-md-4 col-sm-4 padding-5-5">
      <?php $active = $order->payment_code == $pos->cash_payment ? 'btn-success' : ''; ?>
      <button type="button" class="btn btn-block pos-btn payment-btn <?php echo $active; ?>"
        id="btn-<?php echo $pos->cash_payment; ?>" onclick="setPayment('<?php echo $pos->cash_payment; ?>', 1)">
        <p style="margin-bottom:0px;">เงินสด</p>
        <p style="font-size:10px; margin-bottom:0px;">(Ctrl 1)</p>
      </button>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 padding-5-5">
      <?php $active = $order->payment_code == $pos->transfer_payment ? 'btn-success' : 'button-default'; ?>
      <button type="button" class="btn btn-block pos-btn payment-btn <?php echo $active; ?>"
        id="btn-<?php echo $pos->transfer_payment; ?>" onclick="setPayment('<?php echo $pos->transfer_payment; ?>', 2)">
        <p style="margin-bottom:0px;">เงินโอน</p>
        <p style="font-size:10px; margin-bottom:0px;">(Ctrl 2)</p>
      </button>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 padding-5-5">
      <?php $active = $order->payment_code == $pos->card_payment ? 'btn-success' : 'button-default'; ?>
      <button type="button" class="btn btn-block pos-btn payment-btn <?php echo $active; ?>"
        id="btn-<?php echo $pos->card_payment; ?>" onclick="setPayment('<?php echo $pos->card_payment; ?>', 3)">
        <p style="margin-bottom:0px;">บัตรเครดิต</p>
        <p style="font-size:10px; margin-bottom:0px;">(Ctrl 3)</p>
      </button>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-5 padding-5-5">
      <?php $active = $order->payment_code == "MULTIPAYMENT" ? 'btn-success' : 'button-default'; ?>
      <button type="button" class="btn btn-block pos-btn payment-btn <?php echo $active; ?>"
        id="btn-MULTIPAYMENT" onclick="setPayment('MULTIPAYMENT', 6)">
        <p style="margin-bottom:0px;">หลายช่องทาง</p>
        <p style="font-size:10px; margin-bottom:0px;">(Ctrl 4)</p>
      </button>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-3-harf padding-5-5">
      <button type="button" class="btn btn-primary btn-block pos-btn" id="recal-btn" onclick="reCalDiscount()">
        <p style="margin-bottom:0px;">สรุปยอด</p>
        <p style="font-size:10px; margin-bottom:0px;">(F10)</p>
      </button>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-3-harf padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" id="pay-btn" onclick="showPayment()">
        <p style="margin-bottom:0px;">รับเงิน</p>
        <p style="font-size:10px; margin-bottom:0px;">(F12)</p>
      </button>
    </div>
  </div>
</div>
