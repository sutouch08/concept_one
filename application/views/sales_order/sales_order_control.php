<!--  Search Product -->
<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-8 padding-5 margin-bottom-10 not-show">
    <label>รุ่นสินค้า</label>
    <input type="text" class="form-control input-sm text-center item-control" id="model-code" placeholder="รุ่นสินค้า" value=""/>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 padding-5 margin-bottom-10 not-show">
    <label class="display-block not-show">M</label>
  	<button type="button" class="btn btn-xs btn-primary btn-block item-control" onclick="getItemGrid()">OK</button>
  </div>

	<div class="divider hidden-lg hidden-md" style="margin-top:5px; margin-bottom:5px;"></div>

  <div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-8 padding-5 margin-bottom-10">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm text-center item-control" id="item-code" value="" placeholder="รหัสสินค้า">
		<input type="hidden" id="item-data" />
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 padding-5 margin-bottom-10">
    <label class="display-block not-show">M</label>
  	<button type="button" class="btn btn-xs btn-primary btn-block item-control" onclick="getItem()">OK</button>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5 margin-bottom-10">
    <label>ราคา</label>
    <input type="number" class="form-control input-sm text-center item-control" id="input-price" placeholder="ราคา" disabled>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5 margin-bottom-10">
    <label>สต็อก</label>
    <input type="number" class="form-control input-sm text-center item-control" id="input-stock" placeholder="สต็อก" disabled>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-8 padding-5 margin-bottom-10">
    <label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center item-control" id="input-qty" placeholder="จำนวน">
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5 margin-bottom-10">
    <label class="display-block not-show">B</label>
    <button type="button" class="btn btn-xs btn-primary btn-block item-control" onclick="addItem()">เพิ่ม</button>
  </div>

	<div class="divider visible-xs"></div>

	<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-4 padding-5 text-right">
    <label class="display-block not-show">B</label>
		<button type="button" class="btn btn-xs btn-danger" onclick="removeChecked()">ลบรายการ</button>
	</div>
</div>
