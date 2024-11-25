<?php if($barcode) : ?>

  <div class="row">
  	<div class="col-sm-2 padding-5">
      	<button type="button" class="btn btn-xs btn-default btn-block" onclick="showTransferTable()">แสดงรายการ</button>
      </div>
  	<div class="col-sm-2 padding-5 control-btn">
      	<button type="button" class="btn btn-xs btn-danger btn-block" onclick="getMoveOut()">ย้ายสินค้าออก</button>
      </div>
      <div class="col-sm-2 padding-5 control-btn">
      	<button type="button" class="btn btn-xs btn-info btn-block" onclick="getMoveIn()">ย้ายสินค้าเข้า</button>
      </div>
  </div>

  <hr id="barcode-hr" class="margin-top-15 margin-bottom-15 hide" />

  <div class="row">
  	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 moveOut-zone hide">
      <div class="row">

      </div>
    </div>

    <div class="col-sm-12 padding-5 moveIn-zone hide">
      <div class="row">
        <div class="col-sm-3 padding-5">
          <label>บาร์โค้ดโซน</label>

        </div>
        <div class="col-sm-4 padding-5">
          <label class="display-block not-show">zoneName</label>
          <input type="text" class="form-control input-sm" id="zoneName-label" disabled />
        </div>
        <div class="col-sm-1 padding-5">
          <label class="display-block not-show">newzone</label>
        	<button type="button" class="btn btn-xs btn-info btn-block" id="btn-new-to-zone" onclick="newToZone()" disabled>โซนใหม่</button>
        </div>
        <div class="col-sm-1 padding-5">
          <label>จำนวน</label>
          <input type="number" class="form-control input-sm text-center" id="qty-to" value="1" disabled />
        </div>
        <div class="col-sm-3 padding-5">
          <label>บาร์โค้ดสินค้า</label>
          <input type="text" class="form-control input-sm" id="barcode-item-to" placeholder="ยิงบาร์โค้ดเพื่อย้ายสินค้าออก" disabled />
        </div>
      </div>
    </div>
  </div>

<?php else : ?>

<div class="row">
  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-5">
    <label>ต้นทาง</label>
    <input type="text" class="form-control input-sm" id="from-zone" placeholder="ค้นหาชื่อโซน" autofocus />
  </div>

  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-5">
    <label>ปลายทาง</label>
    <input type="text" class="form-control input-sm" id="to-zone" placeholder="ค้นหาชื่อโซน" />
  </div>

  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-5">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm" id="item-code" placeholder="กรองด้วยรหัสสินค้า" />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">ok</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductInZone()">แสดงสินค้า</button>
  </div>
</div>
<?php endif; ?>

<hr class="margin-top-15 margin-bottom-15" />
