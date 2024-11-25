<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>ลูกค้า/ผู้เบิก</label>
    <input type="text" class="form-control input-sm search" name="customer" value="<?php echo $customer; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="status" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="Y" <?php echo is_selected('Y', $status); ?>>Success</option>
			<option value="D" <?php echo is_selected('D', $status); ?>>Draft</option>
      <option value="N" <?php echo is_selected('N', $status); ?>>Pending</option>
      <option value="E" <?php echo is_selected('E', $status); ?>>Failed</option>
    </select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    <label>การรับสินค้า</label>
    <select class="form-control input-sm" name="is_received" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="Y" <?php echo is_selected('Y', $is_received); ?>>รับแล้ว</option>
      <option value="N" <?php echo is_selected('N', $is_received); ?>>ยังไม่รับ</option>
    </select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
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
    <table class="table table-striped border-1" style="min-width:1320px;">
      <thead>
        <tr>
					<th class="fix-width-40"></th>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-100 text-center">วันที่</th>
          <th class="fix-width-120">เลขที่เอกสาร</th>
					<th class="fix-width-60 text-center">สถานะ</th>
					<th class="fix-width-60 text-center">รับแล้ว</th>
					<th class="fix-width-150">วันที่รับ</th>
          <th class="fix-width-100">รหัสลูกค้า</th>
          <th class="fix-width-250">ชื่อลูกค้า</th>
          <th class="fix-width-150">เข้าถังกลาง</th>
          <th class="fix-width-150">เข้า SAP</th>
					<th class="min-width-100">หมายเหตุ</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($orders as $rs)  : ?>

        <tr class="font-size-12">
					<td class="">
						<?php if($rs->F_Sap != 'Y') : ?>
							<button type="button" class="btn btn-minier btn-danger" onclick="get_delete(<?php echo $rs->DocEntry; ?>, '<?php echo $rs->U_ECOMNO; ?>')">
								<i class="fa fa-trash"></i>
							</button>
						<?php endif; ?>
					</td>
          <td class="middle text-center"><?php echo $no; ?></td>
          <td class="middle text-center"><?php echo thai_date($rs->DocDate); ?></td>
          <td class="middle"><a href="javascript:void(0)" onclick="get_detail(<?php echo $rs->DocEntry; ?>)"><?php echo $rs->U_ECOMNO; ?></a></td>
					<td class="middle text-center">
						<?php if($rs->F_Sap === NULL OR $rs->F_Sap == 'P') : ?>
							<span class="orange">Pending</span>
						<?php elseif($rs->F_Sap === 'D') : ?>
							<span class="blue">Draft</span>
						<?php elseif($rs->F_Sap === 'N') : ?>
							<span class="red">Failed</span>
						<?php elseif($rs->F_Sap === 'Y') : ?>
							<span class="green">Success</span>
						<?php endif; ?>
					</td>
					<td class="middle text-center"><?php echo $rs->F_Receipt === 'Y' ? '<span class="green">Yes</span>' : 'No'; ?></td>
					<td class="middle text-center"><?php echo empty($rs->F_ReceiptDate) ? thai_date($rs->F_ReceiptDate, TRUE) : '' ;?></td>
          <td class="middle"><?php echo $rs->CardCode; ?></td>
          <td class="middle"><?php echo $rs->CardName; ?></td>
					<td class="middle" ><?php echo thai_date($rs->F_E_CommerceDate, TRUE); ?></td>
          <td class="middle"><?php echo empty($rs->F_SapDate) ? '' : thai_date($rs->F_SapDate, TRUE); ?></td>
					<td class="middle"><?php echo $rs->F_Sap == 'N' ? $rs->Message : ''; ?></td>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="11" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<form id="reportForm" method="post" action="<?php echo $this->home; ?>/export_diff">
	<input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>">
</form>

<script src="<?php echo base_url(); ?>scripts/inventory/temp/temp_transfer_draft_list.js?v=<?php echo date('YmdH'); ?>"></script>
<script>
$(document).ready(function(){
	$('[data-toggle="popover"]').popover({
		'container' : 'body',
		'template' : '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
	});
})


function export_diff()
{
  var token = $('#token').val();
  get_download(token);
  $('#reportForm').submit();
}

</script>
<?php $this->load->view('include/footer'); ?>
