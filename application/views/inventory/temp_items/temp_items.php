<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?> </h4>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>Item Code</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="status" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="Y" <?php echo is_selected('Y', $status); ?>>Success</option>
      <option value="N" <?php echo is_selected('N', $status); ?>>Pending</option>
      <option value="E" <?php echo is_selected('E', $status); ?>>Failed</option>
    </select>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<p class="pull-right">
      สถานะ :
			<span class="green">Success</span> = เข้า SAP แล้ว, &nbsp;
      <span class="red">Failed</span> = เกิดข้อผิดพลาด, &nbsp;
      <span class="orange">Pending</span> = ยังไม่เข้า SAP
    </p>
  </div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1050px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">#</th>
					<th class="fix-width-200">Item Code </th>
					<th class="min-width-200">Description</th>
					<th class="fix-width-80 text-center">Uom</th>
					<th class="fix-width-80 text-center">สถานะ</th>
          <th class="fix-width-150 text-center">เข้าถังกลาง</th>
          <th class="fix-width-150 text-center">SAP Update</th>
					<th class="min-width-100">Message</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($items))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($items as $rs)  : ?>
        <tr class="font-size-12" id="row-<?php echo $no; ?>">
          <td class="middle text-center no"><?php echo $no; ?></td>
					<td class="middle"><?php echo $rs->ItemCode; ?></td>
          <td class="middle"><?php echo $rs->ItemName; ?></td>
          <td class="middle text-center"><?php echo $rs->SalUnitMsr; ?></td>
					<td class="middle text-center">
						<?php if($rs->F_Sap === NULL OR $rs->F_Sap == 'P') : ?>
							<span class="orange">Pending</span>
						<?php elseif($rs->F_Sap === 'N') : ?>
							<span class="red">Failed</span>
						<?php elseif($rs->F_Sap === 'Y' OR $rs->F_Sap == 'U') : ?>
							<span class="green">Success</span>
						<?php endif; ?>
					</td>
					<td class="middle"><?php echo thai_date($rs->F_E_CommerceDate, TRUE); ?></td>
					<td class="middle"><?php echo empty($rs->F_SapDate) ? '' : thai_date($rs->F_SapDate, TRUE); ?></td>
					<td class="middle"><?php echo $rs->F_Sap == 'N' ? $rs->Message : ''; ?></td>
					</tr>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/temp/temp_items_list.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
