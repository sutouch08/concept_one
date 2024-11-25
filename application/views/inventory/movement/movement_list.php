<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
</div>
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm text-center search-box" name="reference" value="<?php echo $reference; ?>" />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm text-center search-box" name="product_code" value="<?php echo $product_code; ?>" />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>รหัสคลัง</label>
    <input type="text" class="form-control input-sm text-center search-box" name="warehouse_code" value="<?php echo $warehouse_code; ?>" />
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>รหัสโซน</label>
    <input type="text" class="form-control input-sm text-center search-box" name="zone_code" value="<?php echo $zone_code; ?>" />
  </div>
  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">search</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">Search</button>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">reset</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
  </div>
</div>
</form>
<hr class="margin-top-15"/>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1">
      <thead>
        <tr>
          <th class="fix-width-150">เลขที่เอกสาร</th>
          <th class="fix-width-200">รหัสสินค้า</th>
          <th class="fix-width-150">รหัสคลัง</th>
          <th class="fix-width-200">รหัสโซน</th>
          <th class="fix-width-100 text-right">เข้า</th>
          <th class="fix-width-100 text-right">ออก</th>
          <th class="fix-width-150">วันที่</th>
        </tr>
      </thead>
      <tbody>
    <?php if( ! empty($data)) : ?>
      <?php foreach($data as $rs) : ?>
        <tr>
          <td><?php echo $rs->reference; ?></td>
          <td><?php echo $rs->product_code; ?></td>
          <td><?php echo $rs->warehouse_code; ?></td>
          <td><?php echo $rs->zone_code; ?></td>
          <td class="text-right"><?php echo number($rs->move_in); ?></td>
          <td class="text-right"><?php echo number($rs->move_out); ?></td>
          <td><?php echo thai_date($rs->date_upd, TRUE); ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else : ?>
      <tr><td colspan="7" class="text-center"> -- ไม่พบข้อมูล --</td></tr>
    <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/movement/movement.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
