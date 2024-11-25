<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-sm-6 col-sm-6 hidden-xs padding-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  	<p class="pull-right top-p">
    <?php if($this->pm->can_add) : ?>
      <button type="button" class="btn btn-sm0 btn-success btn-top" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
    <?php endif; ?>
    </p>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>คลังต้นทาง</label>
    <input type="text" class="form-control input-sm search" name="from_warehouse" value="<?php echo $from_warehouse; ?>" />
  </div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>คลังปลายทาง</label>
    <input type="text" class="form-control input-sm search" name="to_warehouse" value="<?php echo $to_warehouse; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>พนักงาน</label>
    <input type="text" class="form-control input-sm search" name="user" value="<?php echo $user; ?>" />
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>การยืนยัน</label>
		<select class="form-control input-sm" name="must_accept" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="1" <?php echo is_selected('1', $must_accept); ?>>ต้องยืนยัน</option>
			<option value="0" <?php echo is_selected('0', $must_accept); ?>>ไม่ต้องยืนยัน</option>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $status); ?>>ยังไม่บันทึก</option>
			<option value="1" <?php echo is_selected('1', $status); ?>>บันทึกแล้ว</option>
			<option value="2" <?php echo is_selected('2', $status); ?>>ยกเลิก</option>
			<option value="4" <?php echo is_selected('4', $status); ?>>รอยืนยัน</option>
			<option value="5" <?php echo is_selected('5', $status); ?>>หมดอายุ</option>
		</select>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>SAP</label>
    <select class="form-control input-sm" name="is_export" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected("0", $is_export); ?>>ยังไม่ส่ง</option>
			<option value="1" <?php echo is_selected('1', $is_export); ?>>ส่งออกแล้ว</option>
		</select>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-group">
      <input type="text" class="form-control input-sm width-50 from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
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
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<p  class="pull-right top-p">
			ว่างๆ = ปกติ, &nbsp;
			<span class="blue">NC</span> = ยังไม่บันทึก, &nbsp;
			<span class="orange">WC</span> = รอการยืนยัน, &nbsp;
			<span class="red">CN</span> = ยกเลิก, &nbsp;
			<span class="dark">EXP</span> = หมดอายุ &nbsp;
		</p>
		<table class="table table-striped table-hover border-1" style="min-width:970px;">
			<thead>
				<tr>
					<th class="fix-width-120 middle"></th>
					<th class="fix-width-40 middle">สถานะ</th>
					<th class="fix-width-40 middle text-center">ลำดับ</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-120 middle">เลขที่เอกสาร</th>
					<th class="min-width-200 middle">ต้นทาง</th>
					<th class="min-width-200 middle">ปลายทาง</th>
					<th class="min-width-100 middle">พนักงาน</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($docs)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($docs as $rs) : ?>
            <tr id="row-<?php echo $rs->code; ?>">
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" onclick="goDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
								<?php if($rs->status == 0 && $this->pm->can_edit) : ?>
									<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
								<?php endif; ?>
								<?php if($rs->status != 2 && empty($rs->inv_code) && $this->pm->can_delete) : ?>
									<button type="button" class="btn btn-minier btn-danger" onclick="goDelete('<?php echo $rs->code; ?>', <?php echo $rs->status; ?>)"><i class="fa fa-trash"></i></button>
								<?php endif; ?>
							</td>
							<td class="middle text-center">
								<?php if($rs->is_expire OR $rs->status == 2) : ?>
									<?php if($rs->status == 2) : ?>
										<span class="red">CN</span>
									<?php else : ?>
										<span class="dark">EXP</span>
									<?php endif; ?>
								<?php else : ?>									
									<?php if($rs->status == 0) : ?>
										<span class="blue">NC</span>
									<?php endif; ?>
									<?php if($rs->status == 4) : ?>
										<span class="orange">WC</span.
									<?php endif; ?>
								<?php endif; ?>
							</td>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
              <td class="middle" style="max-width:250px !important;"><?php echo $rs->from_warehouse_name; ?></td>
              <td class="middle" style="max-width:250px !important;"><?php echo $rs->to_warehouse_name; ?></td>
              <td class="middle"><?php echo $rs->display_name; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
				<?php else : ?>
					<tr><td colspan="8" class="text-center">-- No Data --</td></tr>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/move/move.js?v=<?php echo date('Ymd'); ?>"></script>
<script>
	function export_to_sap(code)
	{
		load_in();
		$.ajax({
			url:HOME + 'export_move/' + code,
			type:'POST',
			cache:false,
			success:function(rs){
				load_out();
				if(rs == 'success'){
					$('#row-'+code).remove();
					swal({
						title:'Success',
						text:'ส่งข้อมูลไป SAP เรียบร้อยแล้ว',
						type:'success',
						timer:1000
					});
				}else{
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			}
		});
	}
</script>

<?php $this->load->view('include/footer'); ?>
