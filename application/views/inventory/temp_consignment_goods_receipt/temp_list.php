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
    <label>สถานะ</label>
    <select class="form-control input-sm" name="status" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="Y" <?php echo is_selected('Y', $status); ?>>เข้าแล้ว</option>
      <option value="N" <?php echo is_selected('N', $status); ?>>ยังไม่เข้า</option>
      <option value="E" <?php echo is_selected('E', $status); ?>>Error</option>
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
      สถานะ : <span class="green">สำเร็จ</span> = เข้า SAP แล้ว, &nbsp;
      <span class="red">ERROR</span> = เกิดข้อผิดพลาด, &nbsp;
      <span class="blue">NC</span> = ยังไม่เข้า SAP
    </p>
  </div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1 dataTable" style="min-width:750px;">
      <thead>
				<tr>
					<th class="fix-width-80"></th>
          <th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-100 text-center">วันที่</th>
          <th class="fix-width-150">เลขที่เอกสาร </th>
          <th class="fix-width-150">เข้าถังกลาง</th>
          <th class="fix-width-150">เข้า SAP</th>
          <th class="fix-width-60 text-center">สถานะ</th>
					<th class="min-width-100">หมายเหตุ</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($orders as $rs)  : ?>

        <tr class="font-size-12">
					<td class="text-right">
						<button type="button" class="btn btn-minier btn-info" onclick="get_detail(<?php echo $rs->DocEntry; ?>)">
							<i class="fa fa-eye"></i>
						</button>
					</td>
          <td class="text-center"><?php echo $no; ?></td>

          <td class="text-center"><?php echo thai_date($rs->DocDate); ?></td>

          <td class=""><?php echo $rs->U_ECOMNO; ?></td>

          <td class="" >
						<?php
							if(!empty($rs->F_E_CommerceDate))
							{
								echo thai_date($rs->F_E_CommerceDate, TRUE);
							}
						?>
					</td>

          <td class="">
						<?php
							if(!empty($rs->F_SapDate))
							{
								echo thai_date($rs->F_SapDate, TRUE);
							}
						 ?>
					</td>
					<td class="text-center">
            <?php if($rs->F_Sap === NULL) : ?>
              <span class="blue">NC</span>
            <?php elseif($rs->F_Sap === 'N') : ?>
              <span class="red">ERROR</span>
						<?php elseif($rs->F_Sap == 'Y') : ?>
							<span class="green">สำเร็จ</span>
            <?php endif; ?>
          </td>
          <td class="">
            <?php
            if($rs->F_Sap === 'N')
            {
              echo $rs->Message;
            }
            ?>
          </td>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/temp/temp_consignment_goods_receipt_list.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
