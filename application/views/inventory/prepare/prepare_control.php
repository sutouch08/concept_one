<div class="row">
  <div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>รหัสโซน</label>
    <input type="text" class="form-control input-sm" id="barcode-zone" value="" autofocus/>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="not-show">changeZone</label>
    <button type="button" class="btn btn-xs btn-info btn-block" id="btn-change-zone" onclick="changeZone()">เปลี่ยนโซน</button>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center" id="qty" value="1" />
  </div>
  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
    <label>บาร์โค้ดสินค้า</label>
    <input type="text" class="form-control input-sm" id="barcode-item" />
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3 padding-5">
    <label class="not-show">Submit</label>
    <button type="button" class="btn btn-xs btn-default btn-block" id="btn-submit" onclick="doPrepare()" >ตกลง</button>
  </div>


  <input type="hidden" name="zone_code" id="zone_code" value=""/>

</div>
