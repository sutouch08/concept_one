<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า/ผู้เบิก</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
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

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
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
    <table class="table table-striped border-1 dataTable" style="min-width:1250px;">
      <thead>
        <tr>
					<th class="fix-width-80"></th>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-100 text-center">วันที่</th>
          <th class="fix-width-120">เลขที่เอกสาร </th>
					<th class="fix-width-60 text-center">สถานะ</th>
          <th class="fix-width-100">รหัสลูกค้า</th>
          <th class="fix-width-350">ชื่อลูกค้า</th>
          <th class="fix-width-150">เข้าถังกลาง</th>
          <th class="fix-width-150">SAP Update</th>
					<th class="min-width-100">หมายเหตุ</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($orders as $rs)  : ?>
        <tr class="font-size-12" id="row-<?php echo $no; ?>">
					<td class="">
						<button type="button"
							class="btn btn-minier btn-info"
							onclick="viewDetail(<?php echo $rs->DocEntry;?>)">
							<i class="fa fa-eye"></i>
						</button>
						<?php if(($rs->F_Sap === 'N' OR $rs->F_Sap == NULL)) : ?>
							<button type="button"
								class="btn btn-minier btn-danger"
								onclick="deleteTemp(<?php echo $rs->DocEntry; ?>, '<?php echo $rs->U_ECOMNO; ?>', <?php echo $no; ?>)">
								<i class="fa fa-trash"></i>
							</button>
						<?php endif; ?>
					</td>
          <td class="text-center no"><?php echo $no; ?></td>
          <td class="text-center"><?php echo thai_date($rs->DocDate); ?></td>
          <td class=""><a href="javascript:void(0)" onclick="viewDpm('<?php echo $rs->U_ECOMNO; ?>')"><?php echo $rs->U_ECOMNO; ?></a></td>
					<td class="text-center">
						<?php if($rs->F_Sap === NULL OR $rs->F_Sap == 'P') : ?>
							<span class="orange">Pending</span>
						<?php elseif($rs->F_Sap === 'N') : ?>
							<span class="red">Failed</span>
						<?php elseif($rs->F_Sap === 'Y') : ?>
							<span class="green">Success</span>
						<?php endif; ?>
					</td>
          <td class=""><?php echo $rs->CardCode; ?></td>
          <td class="hide-text"><?php echo $rs->CardName; ?></td>
          <td class="" ><?php echo thai_date($rs->F_E_CommerceDate, TRUE); ?></td>
          <td class=""><?php echo empty($rs->F_SapDate) ? '' : thai_date($rs->F_SapDate, TRUE); ?></td>
          <td class=""><?php echo $rs->F_Sap == 'N' ? $rs->Message : ''; ?></td>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="10" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<form id="reportForm" method="post" action="<?php echo $this->home; ?>/export_diff">
	<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>">
</form>
<script src="<?php echo base_url(); ?>scripts/inventory/temp/temp_incomming.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
