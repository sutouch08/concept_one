<div class="col-lg-4 col-md-4 col-sm-4 hidden-xs" style="margin-bottom: 5px; padding-left:0px; overflow:auto;" id="right-block">
  <div class="row" style="padding-left:10px;">
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn r-btn" onclick="holdBill()">
        <p style="margin-bottom:0px;">พักบิล</p>
        <p style="font-size:10px; margin-bottom:0px;">(F1)</p>
      </button>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn r-btn" onclick="showHoldBill()">
        <p style="margin-bottom:0px;">
          เรียกบิล
          <?php if( ! empty($holdBillCount)) : ?>
            <span class="badge badge-danger" style="position:absolute; top:0; right:0; color:red; background-color:white;"><?php echo $holdBillCount; ?></span>
          <?php endif; ?>
        </p>
        <p style="font-size:10px; margin-bottom:0px;">(F2)</p>
      </button>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-yellow btn-block pos-btn r-btn" onclick="returnList()">
        <p style="margin-bottom:0px;">รับคืน</p>
        <p style="font-size:10px; margin-bottom:0px;">(F3)</p>
      </button>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-info btn-block pos-btn" onclick="billList()">
        <p style="margin-bottom:0px;">บิลขาย</p>
        <p style="font-size:10px; margin-bottom:0px;">(F4)</p>
      </button>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-danger btn-block pos-btn" onclick="removeItems()">
        <p style="margin-bottom:0px;">ลบ</p>
        <p style="font-size:10px; margin-bottom:0px;">(Ctrl Del)</p>
      </button>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn r-btn" onclick="cashIn()">
        <p style="margin-bottom:0px;">นำเงินเข้า</p>
        <p style="font-size:10px; margin-bottom:0px;">(F6)</p>
      </button>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn r-btn" onclick="cashOut()">
        <p style="margin-bottom:0px;">นำเงินออก</p>
        <p style="font-size:10px; margin-bottom:0px;">(F7)</p>
      </button>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" onclick="changeEmployee()">
        <p style="margin-bottom:0px;">พนักงาน</p>
        <p style="font-size:10px; margin-bottom:0px;">(F8)</p>
      </button>
    </div>

    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" onclick="findItem()">
        <p style="margin-bottom:0px;">ค้นหา</p>
        <p style="font-size:10px; margin-bottom:0px;">(F9)</p>
      </button>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-primary btn-block pos-btn r-btn" onclick="downPaymentList()">
        <p style="margin-bottom:0px;">รับมัดจำ</p>
        <p style="font-size:10px; margin-bottom:0px;">(Ctrl F12)</p>
      </button>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5-5">
      <button type="button" class="btn btn-success btn-block pos-btn" onclick="openDrawer()">
        <p style="margin-bottom:0px;">เปิดลิ้นชัก</p>
        <p style="font-size:10px; margin-bottom:0px;"></p>
      </button>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 padding-5-5">
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

</div>
