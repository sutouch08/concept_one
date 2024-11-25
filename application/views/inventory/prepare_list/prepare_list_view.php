<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">

      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>

<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="order_code"  value="<?php echo $order_code; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm search" name="pd_code" value="<?php echo $pd_code; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>โซน</label>
    <input type="text" class="form-control input-sm search" name="zone_code" value="<?php echo $zone_code; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>

  </div>
  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> ค้นหา</button>
  </div>
	<div class="col-sm-1 padding-5 last">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:910px;">
      <tr>
        <th class="fix-width-40 text-center">#</th>
        <th class="fix-width-150 text-center">วันที่</th>
        <th class="fix-width-120 text-center">เลขที่เอกสาร</th>
        <th class="min-width-200 text-center">สินค้า</th>
        <th class="fix-width-100 text-center">จำนวน</th>
        <th class="fix-width-100 text-center">สถานะ</th>
    		<th class="fix-width-200">โซน</th>
      </tr>
      <tbody>
    <?php if( !empty($data)) : ?>
    <?php $no = $this->uri->segment(4) + 1; ?>
		<?php $stateList = []; ?>
    <?php foreach($data as $rs) : ?>
			<?php
			 		if(empty($stateList[$rs->order_code]))
					{
						$state = $this->orders_model->get_state($rs->order_code);
						$stateList[$rs->order_code] = $state;
					}
			?>
      <tr class="font-size-12">
        <td class="text-center no"><?php echo $no; ?></td>
        <td class="text-center"><?php echo thai_date($rs->date_upd, TRUE); ?></td>
        <td class="text-center"><?php echo $rs->order_code; ?></td>
        <td><?php echo $rs->product_code .' : '.$rs->product_name; ?></td>
        <td class="text-center"><?php echo number($rs->qty); ?></td>
    		<td class="text-center"><?php echo state_name($stateList[$rs->order_code]); ?></td>
        <td> <?php echo $rs->zone_name; ?></td>
      </tr>
    <?php  $no++; ?>
    <?php endforeach; ?>
    <?php else : ?>
      <tr>
        <td colspan="8" class="text-center">--- ไม่พบข้อมูล ---</td>
      </tr>
    <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="<?php echo base_url(); ?>scripts/inventory/prepare_list/prepare_list.js"></script>

<?php $this->load->view('include/footer'); ?>
